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

class ImportExperiencesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiliIndex = 'experiences';
    protected $timeout = 0; // Disable timeout
    protected $testMode = false;
    protected $lastProcessedId = 0; // Start from the beginning
    protected $forceFullReindex = false; // Never delete the index
    protected $meilisearchTimeout = 1200; // 20 minutes for operations
    protected $chunkSize = 15000; // Optimized chunk size for better performance
    protected $cacheKey = 'experience_indexing_progress'; // Cache key to store progress
    protected $maxRetries = 5; // Maximum number of retries for failed operations
    protected $checkDuplicates = true; // Check for duplicates before indexing

    public function handle()
    {
        set_time_limit(0); // No time limit

        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

        try {
            // Load the last processed ID from cache if available
            $this->loadProgress();
            
            if ($this->forceFullReindex) {
                $this->initializeIndex($client);
            } else {
                $this->resumeIndexing($client);
            }

            $this->processExperiences($client);
            $this->finalizeIndex($client);
            
            // Clear the cache when job completes successfully
            Cache::forget($this->cacheKey);
            Log::info("Experience indexing completed successfully");

        } catch (\Exception $e) {
            // Save progress to cache so we can resume later
            $this->saveProgress();
            Log::error("Experience indexing failed at ID {$this->lastProcessedId}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Load the last processed ID from cache
     */
    protected function loadProgress()
    {
        $cachedProgress = Cache::get($this->cacheKey);
        if ($cachedProgress && $cachedProgress > $this->lastProcessedId) {
            $this->lastProcessedId = $cachedProgress;
            Log::info("Loaded progress from cache. Last processed ID: {$this->lastProcessedId}");
        } else {
            Log::info("Starting from ID: {$this->lastProcessedId}");
        }
    }
    
    /**
     * Save the current progress to cache
     */
    protected function saveProgress()
    {
        Cache::put($this->cacheKey, $this->lastProcessedId, now()->addDays(7));
        Log::info("Saved progress to cache. Last processed ID: {$this->lastProcessedId}");
    }

    protected function initializeIndex($client)
    {
        // Never delete the index, just check if it exists and create if needed
        try {
            $index = $client->getIndex($this->meiliIndex);
            Log::info("Using existing experience index {$this->meiliIndex}");
        } catch (\Exception $e) {
            $client->createIndex($this->meiliIndex, ['primaryKey' => 'id']);
            Log::info("Created new experience index {$this->meiliIndex}");
        }
        
        // Update index settings immediately
        $this->updateIndexSettings($client);
    }

    protected function resumeIndexing($client)
    {
        try {
            // Check if index exists
            $index = $client->getIndex($this->meiliIndex);
            
            // Update filterable attributes immediately to ensure duplicate checking works
            $this->updateIndexSettings($client);
            
            Log::info("Resuming experience indexing from ID: {$this->lastProcessedId}");
        } catch (\Exception $e) {
            // If index doesn't exist, create it
            $this->initializeIndex($client);
        }
    }

    protected function processExperiences($client)
    {
        // Count total experiences to process
        $totalCount = DB::table('Experience')
            ->where('ExperienceId', '>', $this->lastProcessedId)
            ->count();
            
        Log::info("Processing {$totalCount} experiences starting from ID: {$this->lastProcessedId}");
        
        $processedCount = 0;
        $startTime = microtime(true);
        
        // Process in chunks to avoid memory issues
        while (true) {
            // Get next chunk of experiences with city and country data
            $experiences = DB::table('Experience')
                ->select([
                    'Experience.ExperienceId as id', // Map ExperienceId to id for Meilisearch primary key
                    'Experience.ExperienceId',
                    'Experience.Name',
                    'Experience.Latitude',
                    'Experience.Longitude',
                    'Experience.Slug',
                    'Experience.slugid',
                    'Experience.LocationId',
                    'Location.LocationId as LocationIdRef',
                    'CityLocation.Name as City',
                    'Country.Name as Country'
                ])
                ->leftJoin('Location', 'Experience.LocationId', '=', 'Location.LocationId')
                ->leftJoin('Location as CityLocation', 'Location.ParentId', '=', 'CityLocation.LocationId')
                ->leftJoin('Country', 'CityLocation.CountryId', '=', 'Country.CountryId')
                ->where('Experience.ExperienceId', '>', $this->lastProcessedId)
                ->orderBy('Experience.ExperienceId')
                ->limit($this->chunkSize)
                ->get();
                
            // Break if no more experiences
            if ($experiences->isEmpty()) {
                break;
            }
            
            $count = $experiences->count();
            $processedCount += $count;
            
            // Get the highest ID in this batch
            $maxId = $experiences->last()->ExperienceId;
            
            // Convert to array
            $experiencesArray = $experiences->toArray();
            
            // Get experience IDs for duplicate checking
            $experienceIds = [];
            foreach ($experiencesArray as $experience) {
                $experienceIds[] = $experience->id;
            }
            
            // Check for existing experiences if enabled
            $existingIds = [];
            if ($this->checkDuplicates) {
                $duplicateStartTime = microtime(true);
                $existingIds = $this->checkExistingExperiences($client, $experienceIds);
                $duration = microtime(true) - $duplicateStartTime;
                Log::info("Found " . count($existingIds) . " existing experiences out of {$count}");
            }
            
            // Filter out existing experiences
            $newExperiences = [];
            foreach ($experiencesArray as $experience) {
                if (!in_array($experience->id, $existingIds)) {
                    // Convert to array for Meilisearch
                    $newExperiences[] = (array) $experience;
                }
            }
            
            // Send to Meilisearch in batches
            if (!empty($newExperiences)) {
                $this->sendToMeilisearch($client, $newExperiences);
            }
            
            // Update last processed ID
            $this->lastProcessedId = $maxId;
            $this->saveProgress();
            
            // Log progress
            $progressPercent = $totalCount > 0 ? round(($processedCount / $totalCount) * 100, 2) : 0;
            $duration = microtime(true) - $startTime;
            Log::info("Processed chunk of {$count} experiences in " . number_format($duration, 2) . "s. Progress: {$processedCount}/{$totalCount} ({$progressPercent}%)");
            
            // Break if in test mode
            if ($this->testMode && $processedCount >= $this->chunkSize) {
                break;
            }
        }
    }

    protected function sendToMeilisearch($client, $documents)
    {
        if (empty($documents)) {
            return;
        }
        
        $index = $client->index($this->meiliIndex);
        
        // Process in batches of 5000 to avoid request size limitations
        $batches = array_chunk($documents, 5000);
        $batchIndex = 0;
        
        foreach ($batches as $batch) {
            $retryCount = 0;
            $success = false;
            
            while (!$success && $retryCount <= $this->maxRetries) {
                try {
                    $startTime = microtime(true);
                    
                    // Add documents to index
                    $index->addDocuments($batch);
                    
                    $duration = microtime(true) - $startTime;
                    $docsPerSecond = count($batch) / $duration;
                    
                    Log::info("Successfully indexed batch {$batchIndex} with " . count($batch) . " experiences in " . number_format($duration, 2) . "s (" . number_format($docsPerSecond, 2) . " docs/sec)");
                    
                    $success = true;
                    
                } catch (\Exception $e) {
                    $retryCount++;
                    
                    if ($retryCount > $this->maxRetries) {
                        Log::error("Failed to index batch {$batchIndex} after {$this->maxRetries} retries: " . $e->getMessage());
                        throw $e;
                    }

                    $backoffTime = pow(2, $retryCount);
                    Log::warning("Retry {$retryCount}/{$this->maxRetries} for batch {$batchIndex}. Waiting {$backoffTime}s. Error: " . $e->getMessage());
                    sleep($backoffTime); // Exponential backoff
                }
            }
            
            $batchIndex++;
        }
    }

    /**
     * Check which experience IDs already exist in the index
     * 
     * @param Client $client
     * @param array $experienceIds
     * @return array List of IDs that already exist in the index
     */
    protected function checkExistingExperiences($client, $experienceIds)
    {
        if (empty($experienceIds)) {
            return [];
        }
        
        try {
            $index = $client->index($this->meiliIndex);
            $existingIds = [];
            
            // Make sure filterable attributes are set
            try {
                // Get current settings
                $settings = $index->getSettings();
                
                // Check if 'id' is in the filterable attributes
                if (!isset($settings['filterableAttributes']) || !in_array('id', $settings['filterableAttributes'])) {
                    // Update filterable attributes if needed
                    $this->updateIndexSettings($client);
                    
                    // Wait for the settings to be applied
                    sleep(2);
                }
            } catch (\Exception $e) {
                Log::warning("Error checking index settings: " . $e->getMessage());
                // Try to update settings anyway
                $this->updateIndexSettings($client);
                sleep(2);
            }
            
            // Process in batches to avoid request size limitations
            $idBatches = array_chunk($experienceIds, 100);
            
            foreach ($idBatches as $batch) {
                try {
                    // Build a filter query for this batch of IDs
                    $filterQuery = 'id IN [' . implode(',', $batch) . ']';
                    
                    // Search with this filter and limit to just get IDs
                    $result = $index->search('', [
                        'filter' => $filterQuery,
                        'limit' => count($batch),
                        'attributesToRetrieve' => ['id']
                    ]);
                    
                    // Extract IDs from results - properly handle SearchResult object
                    if (method_exists($result, 'getHits')) {
                        // Handle SearchResult object (newer Meilisearch SDK)
                        $hits = $result->getHits();
                        foreach ($hits as $hit) {
                            if (isset($hit['id'])) {
                                $existingIds[] = $hit['id'];
                            }
                        }
                    } else if (is_array($result) && isset($result['hits'])) {
                        // Handle array response (older Meilisearch SDK)
                        foreach ($result['hits'] as $hit) {
                            if (isset($hit['id'])) {
                                $existingIds[] = $hit['id'];
                            }
                        }
                    } else {
                        Log::warning("Unexpected search result format: " . gettype($result));
                    }
                } catch (\Exception $e) {
                    Log::warning("Error in batch filter search: " . $e->getMessage());
                    // Continue with next batch
                }
            }
            
            return $existingIds;
            
        } catch (\Exception $e) {
            Log::warning("Error checking for existing experiences: " . $e->getMessage());
            return []; // If we can't check, assume none exist to be safe
        }
    }
    
    /**
     * Update index settings
     * 
     * @param Client $client
     */
    protected function updateIndexSettings($client)
    {
        try {
            $index = $client->index($this->meiliIndex);
            
            // Update searchable attributes
            $index->updateSearchableAttributes([
                'Name', 'City', 'Country', 'Slug'
            ]);
            
            // Update filterable attributes - crucial for duplicate checking
            $index->updateFilterableAttributes([
                'LocationId', 'id', 'City', 'Country'
            ]);
            
            Log::info("Updated index settings successfully");
        } catch (\Exception $e) {
            Log::warning("Error updating index settings: " . $e->getMessage());
        }
    }
    
    protected function finalizeIndex($client)
    {
        // Final update of settings
        $this->updateIndexSettings($client);
        
        Log::info("Experience index setup completed");
        $this->forceFullReindex = false;
    }
}
