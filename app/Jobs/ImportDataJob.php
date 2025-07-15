<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
    protected $primaryKey = 'id';
    protected $timeout = 0; // Disable PHP timeout for the job itself
    protected $testMode = false; // Set to true to process only a small batch
    protected $lastProcessedId = 0; // Start from the beginning or resume
    protected $forceFullReindex = false; // Set to true to clear and re-index everything
    protected $meilisearchTimeout = 120000; // 2 minutes for MeiliSearch operations (in milliseconds for waitForTask)
    protected $chunkSize = 1000; // Number of records to fetch from DB at a time
    protected $batchSize = 500;  // Number of documents to send to MeiliSearch in one batch
    protected $cacheKey = 'location_indexing_progress';
    protected $maxRetries = 3; // Maximum number of retries for failed MeiliSearch operations

    public function handle()
    {
        set_time_limit(0); // No time limit for the job execution

        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

        try {
            $this->loadProgress();
            
            if ($this->forceFullReindex) {
                $this->initializeIndex($client);
            } else {
                $this->resumeIndexing($client);
            }

            $this->processLocations($client);
            $this->finalizeIndex($client);
            
            Cache::forget($this->cacheKey);
            Log::info("Location indexing completed successfully. Last processed ID: {$this->lastProcessedId}");

        } catch (\Exception $e) {
            $this->saveProgress();
            Log::error("Location indexing failed at ID {$this->lastProcessedId}: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            // It's often good to re-throw the exception if you want the job to be marked as failed and potentially retried by Laravel's queue worker
            throw $e;
        }
    }
    
    protected function loadProgress()
    {
        $cachedProgress = Cache::get($this->cacheKey);
        if ($cachedProgress && !$this->forceFullReindex) {
            $this->lastProcessedId = (int)$cachedProgress;
            Log::info("Resuming location import from ID: {$this->lastProcessedId}");
        } else {
            $this->lastProcessedId = 0; // Start from the beginning if no cache or full reindex
            Log::info("Starting location import from beginning (ID: 0)");
        }
    }
    
    protected function saveProgress()
    {
        Cache::put($this->cacheKey, $this->lastProcessedId, now()->addDays(7));
        Log::info("Saved location import progress to cache. Last processed ID: {$this->lastProcessedId}");
    }

    protected function initializeIndex($client)
    {
        try {
            $index = $client->index($this->meiliIndex);
            // Attempt to delete the index if it exists for a full re-index
            $index->delete();
            Log::info("Deleted existing index '{$this->meiliIndex}' for full re-index.");
            // Wait a moment for deletion to complete
            sleep(1);
        } catch (\Meilisearch\Exceptions\ApiException $e) {
            if ($e->getCode() == 404) { // Index not found, which is fine
                Log::info("Index '{$this->meiliIndex}' not found, proceeding with creation.");
            } else {
                throw $e; // Re-throw other API exceptions
            }
        } catch (\Exception $e) {
            // Catch other general exceptions if index interaction fails
            Log::warning("Could not delete index '{$this->meiliIndex}': " . $e->getMessage());
        }

        $client->createIndex($this->meiliIndex, ['primaryKey' => $this->primaryKey]);
        Log::info("Created new index '{$this->meiliIndex}' with primary key '{$this->primaryKey}'.");
        $this->updateIndexSettings($client);
    }

    protected function resumeIndexing($client)
    {
        try {
            $client->getIndex($this->meiliIndex); // Check if index exists
            Log::info("Resuming indexing for '{$this->meiliIndex}' from ID: {$this->lastProcessedId}");
            $this->updateIndexSettings($client); // Ensure settings are up-to-date
        } catch (\Meilisearch\Exceptions\ApiException $e) {
            if ($e->getCode() == 404) { // Index not found
                Log::info("Index '{$this->meiliIndex}' not found while trying to resume. Initializing new index.");
                $this->initializeIndex($client);
            } else {
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error resuming indexing: " . $e->getMessage());
            throw $e;
        }
    }

    protected function processLocations($client)
    {
        $totalCountQuery = DB::table('Location')->where('LocationId', '>', $this->lastProcessedId);
        if ($this->testMode) {
             $totalCountQuery->limit(100); // Limit total in test mode for faster completion
        }
        $totalCount = $totalCountQuery->count();
            
        Log::info("Processing approx. {$totalCount} locations starting from ID: {$this->lastProcessedId}" . ($this->testMode ? " (TEST MODE)" : ""));
        
        $processedInThisRun = 0;
        $overallStartTime = microtime(true);

        // Preload all country names once
        $countries = DB::table('Country')->pluck('Name', 'CountryId')->all();
        // Preload all parent location names once
        $parentLocations = DB::table('Location')->whereNotNull('ParentId')->pluck('Name', 'LocationId')->all();
        
        while (true) {
            $chunkStartTime = microtime(true);
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
                ->where('LocationId', '>', $this->lastProcessedId)
                ->orderBy('LocationId')
                ->limit($this->chunkSize);

            if ($this->testMode && $processedInThisRun >= 100) { // Process a limited number in test mode
                 Log::info("Test mode limit reached for this run.");
                 break;
            }

            $locations = $query->get();
                
            if ($locations->isEmpty()) {
                Log::info("No more locations to process.");
                break;
            }
            
            $documents = [];
            $currentMaxIdInChunk = $this->lastProcessedId;

            foreach ($locations as $location) {
                $documents[] = [
                    'id' => $location->id,
                    'name' => $location->name,
                    'slug' => $location->slug,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'slugid' => $location->slugid,
                    'parentName' => $location->ParentId ? ($parentLocations[$location->ParentId] ?? null) : null,
                    'countryName' => $location->CountryId ? ($countries[$location->CountryId] ?? null) : null,
                ];
                if ($location->id > $currentMaxIdInChunk) {
                    $currentMaxIdInChunk = $location->id;
                }
            }
            
            if (!empty($documents)) {
                $this->sendToMeilisearch($client, $documents);
                $processedInThisRun += count($documents);
            }
            
            $this->lastProcessedId = $currentMaxIdInChunk;
            $this->saveProgress();
            
            $chunkDuration = microtime(true) - $chunkStartTime;
            $logMessage = "Processed chunk of " . count($documents) . " locations in " . number_format($chunkDuration, 2) . "s. ";
            $logMessage .= "Total processed in this run: {$processedInThisRun}. Last ID: {$this->lastProcessedId}.";
            if ($totalCount > 0) {
                $progressPercent = round(($processedInThisRun / $totalCount) * 100, 2);
                $logMessage .= " Progress: {$progressPercent}%";
            }
            Log::info($logMessage);
            
            if ($this->testMode && $processedInThisRun >= 10) { // Ensure test mode processes at least one small batch
                Log::info("Test mode: Processed 10 records, stopping.");
                break;
            }
        }
        $overallDuration = microtime(true) - $overallStartTime;
        Log::info("Finished processing locations in this job run. Total processed: {$processedInThisRun} in " . number_format($overallDuration, 2) . "s.");
    }

    protected function sendToMeilisearch($client, array $documents)
    {
        if (empty($documents)) {
            return;
        }
        
        $index = $client->index($this->meiliIndex);
        $documentBatches = array_chunk($documents, $this->batchSize);
        
        foreach ($documentBatches as $batchIndex => $batch) {
            $retryCount = 0;
            $success = false;
            
            while (!$success && $retryCount <= $this->maxRetries) {
                try {
                    $task = $index->updateDocuments($batch);
                    // Instead of waiting for each task, we'll let MeiliSearch handle it.
                    // If waitForTask is crucial, it should be added with careful timeout management.
                    // For now, we follow ImportExperiencesJob's pattern of not waiting for each sub-batch task.
                    Log::info("Sent batch #{$batchIndex} (" . count($batch) . " docs) to MeiliSearch. Task UID: {$task['taskUid']}");
                    $success = true; 
                } catch (\Meilisearch\Exceptions\TimeOutException $e) {
                    $retryCount++;
                    Log::warning("Timeout sending batch #{$batchIndex} to MeiliSearch. Retry {$retryCount}/{$this->maxRetries}. Error: " . $e->getMessage());
                    if ($retryCount > $this->maxRetries) throw $e;
                    sleep(pow(2, $retryCount)); // Exponential backoff
                } catch (\Exception $e) {
                    $retryCount++;
                    Log::error("Error sending batch #{$batchIndex} to MeiliSearch. Retry {$retryCount}/{$this->maxRetries}. Error: " . $e->getMessage());
                    if ($retryCount > $this->maxRetries) throw $e; // Re-throw if max retries exceeded
                    sleep(pow(2, $retryCount)); // Exponential backoff
                }
            }
        }
    }
    
    protected function updateIndexSettings($client)
    {
        try {
            $index = $client->index($this->meiliIndex);
            
            $settings = [
                'searchableAttributes' => [
                    'name',
                    'slugid',
                    'parentName',
                    'countryName'
                ],
                'filterableAttributes' => [
                    'id',
                    // Add any other attributes you might want to filter by, e.g.:
                    // 'CountryId', 
                    // 'ParentId'
                ],
                'sortableAttributes' => [
                    'name',
                    'id'
                ]
                // Add other settings like rankingRules, distinctAttribute, etc. as needed
            ];

            $task = $index->updateSettings($settings);
            $index->waitForTask($task['taskUid'], $this->meilisearchTimeout); // Wait for settings to apply
            
            Log::info("Updated settings for index '{$this->meiliIndex}'.");
        } catch (\Exception $e) {
            Log::error("Error updating settings for index '{$this->meiliIndex}': " . $e->getMessage());
        }
    }
    
    protected function finalizeIndex($client)
    {
        $this->updateIndexSettings($client); // Ensure settings are applied finally
        $this->forceFullReindex = false; // Reset for subsequent runs
        Log::info("Location index '{$this->meiliIndex}' setup finalized.");
    }
}
