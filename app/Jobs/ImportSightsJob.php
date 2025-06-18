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

class ImportSightsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiliIndex = 'sights';
    protected $primaryKey = 'id'; // Always use 'id' as primary key for all indexes
    protected $timeout = 0; // Disable queue worker timeout
    protected $testMode = false; // Disable test mode for production
    protected $lastProcessedId = 0;
    protected $meilisearchTimeout = 600; // 10 minutes for operations
    protected $chunkSize = 10000; // Large chunk size for much faster processing

    public function handle()
    {
        set_time_limit(3600); // 1 hour max execution time

        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

        try {
            $index = $this->initializeIndex($client);
            $this->processSights($index);
            $this->finalizeIndex($index);
        } catch (\Exception $e) {
            Log::error("Sight indexing failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function initializeIndex($client)
    {
        try {
            $index = $client->index($this->meiliIndex);
            Log::info("Index {$this->meiliIndex} already exists.");
            
            // Always explicitly set the primary key to avoid inference issues
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
        } catch (\Exception $e) {
            // Create the index if it doesn't exist
            $index = $client->createIndex($this->meiliIndex, ['primaryKey' => $this->primaryKey]);
            Log::info("Created new index {$this->meiliIndex} with primary key '{$this->primaryKey}'.");
        }

        // Retrieve the last processed ID from a persistent storage or set to 0
        $this->lastProcessedId = $this->getLastProcessedId();

        return $index;
    }

    protected function processSights($index)
    {
        $query = DB::table('Sight')
            ->join('Location', 'Sight.LocationId', '=', 'Location.LocationId')
            ->leftJoin('Location as ParentLocation', 'Location.ParentId', '=', 'ParentLocation.LocationId')
            ->leftJoin('Country', 'Location.CountryId', '=', 'Country.CountryId')
            ->select(
                'Sight.SightId as id',
                'Sight.Title',
                'Sight.LocationId',
                'Sight.Location_id as slugid',
                'Sight.Latitude',
                'Sight.Longitude',
                'Sight.slug',
                'ParentLocation.Name as parentName',
                'Country.Name as countryName'
            )
            ->where('Sight.SightId', '>', $this->lastProcessedId)
            ->orderBy('Sight.SightId');

        if ($this->testMode) {
            $query->limit(10);
            Log::info("Running in TEST MODE - only processing first 10 sights");
        }

        $query->chunk($this->chunkSize, function ($sights) use ($index) {
            set_time_limit(300); // Reset timer for each chunk

            $documents = [];
            foreach ($sights as $sight) {
                $documents[] = [
                    'id' => $sight->id,
                    'Title' => $sight->Title,
                    'LocationId' => $sight->LocationId,
                    'slugid' => $sight->slugid,
                    'latitude' => $sight->Latitude,
                    'longitude' => $sight->Longitude,
                    'slug' => $sight->slug,
                    'parentName' => $sight->parentName,
                    'countryName' => $sight->countryName
                ];
                $this->lastProcessedId = $sight->id;
            }

            $this->processChunk($index, $documents);
            $this->storeLastProcessedId($this->lastProcessedId);
        });
    }

    protected function processChunk($index, $documents)
    {
        $retryCount = 0;
        $maxRetries = 3;
        $batchSize = 1000; // Larger batch size for better performance

        // Break documents into smaller batches
        $batches = array_chunk($documents, $batchSize);
        Log::info("Processing " . count($documents) . " documents in " . count($batches) . " batches");

        foreach ($batches as $batchIndex => $batch) {
            $retryCount = 0;
            
            while ($retryCount < $maxRetries) {
                try {
                    // Use updateDocuments instead of addDocuments
                    $task = $index->updateDocuments($batch);
                    $taskStatus = $index->waitForTask($task['taskUid'], $this->meilisearchTimeout * 1000);

                    if (($taskStatus['status'] ?? null) !== 'succeeded') {
                        throw new \Exception("Indexing failed for task UID {$task['taskUid']}: " . json_encode($taskStatus));
                    }

                    Log::info("Successfully indexed batch " . ($batchIndex + 1) . "/" . count($batches) . " with " . count($batch) . " sights");
                    break; // Success, move to next batch
                } catch (\Exception $e) {
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        Log::error("Failed to process batch after {$maxRetries} attempts. Last ID: {$this->lastProcessedId}");
                        Log::error("Error details: " . $e->getMessage());
                        throw $e;
                    }

                    sleep(10 * $retryCount); // Exponential backoff
                    Log::warning("Retry {$retryCount}/{$maxRetries} for batch " . ($batchIndex + 1) . ". Error: " . $e->getMessage());
                }
            }
        }
        
        Log::info("Completed processing all batches for this chunk");
    }

    protected function finalizeIndex($index)
    {
        $index->updateSearchableAttributes(['slugid', 'slug', 'parentName', 'countryName']);
        Log::info("Sights index setup completed");
    }

    protected function getLastProcessedId()
    {
        // Implement logic to retrieve the last processed ID from persistent storage
        // For example, from a cache or a dedicated database table
        return 0;
    }

    protected function storeLastProcessedId($id)
    {
        // Implement logic to store the last processed ID to persistent storage
        // For example, to a cache or a dedicated database table
    }
}
