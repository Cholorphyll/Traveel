<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Meilisearch\Client;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;

class ImportDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiliIndex = 'locations';
    protected $primaryKey = 'id'; // Always use 'id' as primary key for all indexes
    protected $timeout = 13600;
    protected $testMode = true; // Added test mode flag

    public function handle()
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index($this->meiliIndex);
        
        // ALWAYS explicitly set the primary key to 'id' to avoid inference issues
        try {
            // For MeiliSearch v0.23.0 and above
            if (method_exists($index, 'updatePrimaryKey')) {
                $index->updatePrimaryKey($this->primaryKey);
            } 
            // For MeiliSearch v0.22.0 and below
            else if (method_exists($client, 'updateIndex')) {
                $client->updateIndex($this->meiliIndex, ['primaryKey' => $this->primaryKey]);
            } 
            // For older versions or alternative method
            else {
                $index->update(['primaryKey' => $this->primaryKey]);
            }
            
            Log::info("Primary key for {$this->meiliIndex} index set to '{$this->primaryKey}'");
        } catch (\Exception $e) {
            Log::warning("Could not update primary key: " . $e->getMessage());
            // Continue anyway as this might be because the index already exists with the correct primary key
        }

        // Preload country names
        $countries = DB::table('Country')->pluck('Name', 'CountryId');
        
        // Preload parent location names
        $parentLocations = DB::table('Location')
            ->whereIn('LocationId', function($query) {
                $query->select('ParentId')
                    ->from('Location')
                    ->whereNotNull('ParentId');
            })
            ->pluck('Name', 'LocationId');

        // Modified query to only get first 10 entries in test mode
        $query = DB::table('Location')
            ->select(
                'LocationId as id', 
                'Name as name', 
                'slug', 
                'Lat as latitude', 
                'Longitude as longitude', 
                'slugid',
                'ParentId',
                'CountryId'
            )
            ->orderBy('LocationId');
            
        if ($this->testMode) {
            $query->limit(10);
            Log::info("Running in TEST MODE - only processing first 10 entries");
        }

        $query->chunk(1000, function ($locations) use ($index, $countries, $parentLocations) {
            $documents = [];

            foreach ($locations as $location) {
                $document = [
                    'id' => $location->id,
                    'name' => $location->name,
                    'slug' => $location->slug,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'slugid' => $location->slugid,
                    'parentName' => $location->ParentId ? ($parentLocations[$location->ParentId] ?? null) : null,
                    'countryName' => $location->CountryId ? ($countries[$location->CountryId] ?? null) : null
                ];

                $documents[] = $document;
                Log::debug("Prepared document: " . json_encode($document));
            }

            try {
                // Reduce chunk size if it's too large
                $batchSize = 1000; // Smaller batch size for better reliability
                $batches = array_chunk($documents, $batchSize);
                
                foreach ($batches as $batch) {
                    $task = $index->updateDocuments($batch);
                    $taskStatus = $index->waitForTask($task['taskUid']);
                    
                    if (($taskStatus['status'] ?? null) !== 'succeeded') {
                        // Log more detailed error information
                        Log::error("Indexing failed for task UID {$task['taskUid']}");
                        Log::error("Task details: " . json_encode($taskStatus));
                        
                        // Try to identify problematic documents
                        foreach ($batch as $doc) {
                            try {
                                // Test each document individually
                                $singleTask = $index->updateDocuments([$doc]);
                                $singleStatus = $index->waitForTask($singleTask['taskUid']);
                                
                                if (($singleStatus['status'] ?? null) !== 'succeeded') {
                                    Log::error("Problem document identified: " . json_encode($doc));
                                }
                            } catch (\Exception $e) {
                                Log::error("Error with document: " . json_encode($doc) . " - " . $e->getMessage());
                            }
                        }
                        
                        throw new \Exception("Indexing failed for batch: " . ($taskStatus['error']['message'] ?? 'Unknown error'));
                    }
                }
                
                Log::info("Successfully updated chunk of " . count($documents) . " documents in " . count($batches) . " batches");
            } catch (\Exception $e) {
                Log::error("MeiliSearch indexing exception: " . $e->getMessage());
                throw $e;
            }
        });

        // Update searchable attributes to include new fields
        $index->updateSearchableAttributes(['name', 'slugid', 'parentName', 'countryName']);
        Log::info("Updated searchableAttributes for {$this->meiliIndex} index");

        Log::info("Completed updating location documents" . ($this->testMode ? " (TEST MODE - 10 entries only)" : ""));
    }
}
