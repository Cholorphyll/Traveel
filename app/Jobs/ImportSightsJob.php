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

class ImportSightsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiliIndex = 'sights';
    protected $primaryKey = 'id';
    protected $timeout = 0; // Disable PHP timeout for the job itself
    protected $testMode = false; // Set to true to process only a small batch
    protected $lastProcessedId = 0; // Start from the beginning or resume
    protected $forceFullReindex = false; // Set to true to clear and re-index everything
    protected $meilisearchTimeout = 120000; // 2 minutes for MeiliSearch operations (in milliseconds for waitForTask)
    protected $chunkSize = 5000;  // Number of records to fetch from DB at a time (adjust as needed)
    protected $batchSize = 500;   // Number of documents to send to MeiliSearch in one batch
    protected $cacheKey = 'sight_indexing_progress';
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

            $this->processSightsData($client);
            $this->finalizeIndex($client);
            
            Cache::forget($this->cacheKey); // Clear progress cache on successful completion
            Log::info("Sight indexing completed successfully. Last processed ID: {$this->lastProcessedId}");

        } catch (\Exception $e) {
            $this->saveProgress(); // Save progress on failure
            Log::error("Sight indexing failed at ID {$this->lastProcessedId}: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            throw $e; // Re-throw for queue worker to handle (retry/fail)
        }
    }
    
    protected function loadProgress()
    {
        $cachedProgress = Cache::get($this->cacheKey);
        if ($cachedProgress && !$this->forceFullReindex) {
            $this->lastProcessedId = (int)$cachedProgress;
            Log::info("Resuming sight import from ID: {$this->lastProcessedId}");
        } else {
            $this->lastProcessedId = 0; // Start from the beginning if no cache or full reindex
            Log::info("Starting sight import from beginning (ID: 0)");
        }
    }
    
    protected function saveProgress()
    {
        Cache::put($this->cacheKey, $this->lastProcessedId, now()->addDays(7));
        Log::info("Saved sight import progress to cache. Last processed ID: {$this->lastProcessedId}");
    }

    protected function initializeIndex($client)
    {
        try {
            $index = $client->index($this->meiliIndex);
            $index->delete();
            Log::info("Deleted existing index '{$this->meiliIndex}' for full re-index.");
            sleep(1); // Give MeiliSearch a moment
        } catch (\Meilisearch\Exceptions\ApiException $e) {
            if ($e->getCode() == 404) { // Index not found, which is fine
                Log::info("Index '{$this->meiliIndex}' not found, proceeding with creation.");
            } else {
                throw $e; 
            }
        } catch (\Exception $e) {
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
            Log::error("Error resuming indexing for '{$this->meiliIndex}': " . $e->getMessage());
            throw $e;
        }
    }

    protected function processSightsData($client)
    {
        $totalCountQuery = DB::table('Sight')->where('SightId', '>', $this->lastProcessedId);
        if ($this->testMode) {
             $totalCountQuery->limit(100); // Limit total in test mode for faster completion
        }
        $totalCount = $totalCountQuery->count();
            
        Log::info("Processing approx. {$totalCount} sights starting from ID: {$this->lastProcessedId}" . ($this->testMode ? " (TEST MODE)" : ""));
        
        $processedInThisRun = 0;
        $overallStartTime = microtime(true);
        
        while (true) {
            $chunkStartTime = microtime(true);
            $query = DB::table('Sight')
                ->join('Location', 'Sight.LocationId', '=', 'Location.LocationId')
                ->leftJoin('Location as ParentLocation', 'Location.ParentId', '=', 'ParentLocation.LocationId')
                ->leftJoin('Country', 'ParentLocation.CountryId', '=', 'Country.CountryId') // Corrected join for Country
                ->select(
                    'Sight.SightId as id',
                    'Sight.Title',
                    'Sight.LocationId',
                    'Sight.Location_id as slugid', // Assuming Location_id in Sight table is the slugid
                    'Sight.Latitude',
                    'Sight.Longitude',
                    'Sight.slug',
                    'ParentLocation.Name as parentName',
                    'Country.Name as countryName'
                )
                ->where('Sight.SightId', '>', $this->lastProcessedId)
                ->orderBy('Sight.SightId')
                ->limit($this->chunkSize);

            if ($this->testMode && $processedInThisRun >= 100) { 
                 Log::info("Test mode limit reached for this run.");
                 break;
            }

            $sights = $query->get();
                
            if ($sights->isEmpty()) {
                Log::info("No more sights to process.");
                break;
            }
            
            $documents = [];
            $currentMaxIdInChunk = $this->lastProcessedId;

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
                    'countryName' => $sight->countryName,
                ];
                if ($sight->id > $currentMaxIdInChunk) {
                    $currentMaxIdInChunk = $sight->id;
                }
            }
            
            if (!empty($documents)) {
                $this->sendToMeilisearch($client, $documents);
                $processedInThisRun += count($documents);
            }
            
            $this->lastProcessedId = $currentMaxIdInChunk;
            $this->saveProgress();
            
            $chunkDuration = microtime(true) - $chunkStartTime;
            $logMessage = "Processed chunk of " . count($documents) . " sights in " . number_format($chunkDuration, 2) . "s. ";
            $logMessage .= "Total processed in this run: {$processedInThisRun}. Last ID: {$this->lastProcessedId}.";
            if ($totalCount > 0 && $processedInThisRun > 0) {
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
        Log::info("Finished processing sights in this job run. Total processed: {$processedInThisRun} in " . number_format($overallDuration, 2) . "s.");
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
                    // Not waiting for each task to complete here for speed, similar to other jobs.
                    // MeiliSearch handles tasks asynchronously.
                    Log::info("Sent batch #{$batchIndex} (" . count($batch) . " docs) to MeiliSearch for '{$this->meiliIndex}'. Task UID: {$task['taskUid']}");
                    $success = true; 
                } catch (\Meilisearch\Exceptions\TimeOutException $e) {
                    $retryCount++;
                    Log::warning("Timeout sending batch #{$batchIndex} to MeiliSearch for '{$this->meiliIndex}'. Retry {$retryCount}/{$this->maxRetries}. Error: " . $e->getMessage());
                    if ($retryCount > $this->maxRetries) throw $e;
                    sleep(pow(2, $retryCount)); // Exponential backoff
                } catch (\Exception $e) {
                    $retryCount++;
                    Log::error("Error sending batch #{$batchIndex} to MeiliSearch for '{$this->meiliIndex}'. Retry {$retryCount}/{$this->maxRetries}. Error: " . $e->getMessage());
                    if ($retryCount > $this->maxRetries) throw $e; 
                    sleep(pow(2, $retryCount)); 
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
                    'Title',
                    'slug',
                    'slugid',
                    'parentName',
                    'countryName'
                ],
                'filterableAttributes' => [
                    'id',
                    'LocationId',
                    // Add other attributes you might want to filter by
                ],
                'sortableAttributes' => [
                    'Title',
                    'id'
                ]
            ];

            $task = $index->updateSettings($settings);
            $index->waitForTask($task['taskUid'], $this->meilisearchTimeout); 
            
            Log::info("Updated settings for index '{$this->meiliIndex}'.");
        } catch (\Exception $e) {
            Log::error("Error updating settings for index '{$this->meiliIndex}': " . $e->getMessage());
        }
    }
    
    protected function finalizeIndex($client)
    {
        $this->updateIndexSettings($client); 
        $this->forceFullReindex = false; 
        Log::info("Sight index '{$this->meiliIndex}' setup finalized.");
    }
}
