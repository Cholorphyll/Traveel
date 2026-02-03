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

class ImportRestaurantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiliIndex = 'restaurants';
    protected $timeout = 0; // Disable timeout
    protected $testMode = false;
    protected $lastProcessedId = 0; // Start from the beginning
    protected $forceFullReindex = false; // Never delete the index
    protected $meilisearchTimeout = 1200; // 20 minutes for operations
    protected $chunkSize = 15000; // Optimized chunk size for better performance
    protected $cacheKey = 'restaurant_indexing_progress'; // Cache key to store progress
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

            $this->processRestaurants($client);
            $this->finalizeIndex($client);
            
            // Clear the cache when job completes successfully
            Cache::forget($this->cacheKey);
            Log::info("Restaurant indexing completed successfully");

        } catch (\Exception $e) {
            // Save progress to cache so we can resume later
            $this->saveProgress();
            Log::error("Restaurant indexing failed at ID {$this->lastProcessedId}: " . $e->getMessage());
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
            Log::info("Using existing restaurant index {$this->meiliIndex}");
        } catch (\Exception $e) {
            $client->createIndex($this->meiliIndex, ['primaryKey' => 'id']);
            Log::info("Created new restaurant index {$this->meiliIndex}");
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
            
            Log::info("Resuming restaurant indexing from ID: {$this->lastProcessedId}");
        } catch (\Exception $e) {
            // If index doesn't exist, create it
            $this->initializeIndex($client);
        }
    }

    protected function processRestaurants($client)
    {
        // Count total restaurants to process
        $totalCount = DB::table('Restaurant')
            ->where('RestaurantId', '>', $this->lastProcessedId)
            ->count();
            
        Log::info("Processing {$totalCount} restaurants starting from ID: {$this->lastProcessedId}");
        
        $processedCount = 0;
        
        // Process in chunks to avoid memory issues
        while (true) {
            // Get next chunk of restaurants
            $restaurants = DB::table('Restaurant')
                ->select([
                    'RestaurantId as id', // Map RestaurantId to id for Meilisearch primary key
                    'RestaurantId',
                    'Title',
                    'Latitude',
                    'Longitude',
                    'City',
                    'Country',
                    'Slug',
                    'slugid',
                    'LocationId'
                ])
                ->where('RestaurantId', '>', $this->lastProcessedId)
                ->orderBy('RestaurantId')
                ->limit($this->chunkSize)
                ->get();
                
            // Break if no more restaurants
            if ($restaurants->isEmpty()) {
                break;
            }
            
            $count = $restaurants->count();
            $processedCount += $count;
            
            // Get the highest ID in this batch
            $maxId = $restaurants->last()->RestaurantId;
            
            // Convert to array
            $restaurantsArray = $restaurants->toArray();
            
            // Get restaurant IDs for duplicate checking
            $restaurantIds = [];
            foreach ($restaurantsArray as $restaurant) {
                $restaurantIds[] = $restaurant->id;
            }
            
            // Check for existing restaurants if enabled
            $existingIds = [];
            if ($this->checkDuplicates) {
                $startTime = microtime(true);
                $existingIds = $this->checkExistingRestaurants($client, $restaurantIds);
                $duration = microtime(true) - $startTime;
                Log::info("Found " . count($existingIds) . " existing restaurants out of {$count}");
            }
            
            // Filter out existing restaurants
            $newRestaurants = [];
            foreach ($restaurantsArray as $restaurant) {
                if (!in_array($restaurant->id, $existingIds)) {
                    // Convert to array for Meilisearch
                    $newRestaurants[] = (array) $restaurant;
                }
            }
            
            // Send to Meilisearch in batches
            if (!empty($newRestaurants)) {
                $this->sendToMeilisearch($client, $newRestaurants);
            }
            
            // Update last processed ID
            $this->lastProcessedId = $maxId;
            $this->saveProgress();
            
            // Log progress
            $progressPercent = $totalCount > 0 ? round(($processedCount / $totalCount) * 100, 2) : 0;
            $duration = microtime(true) - $startTime;
            Log::info("Processed chunk of {$count} restaurants in " . number_format($duration, 2) . "s. Progress: {$processedCount}/{$totalCount} ({$progressPercent}%)");
            
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
                    
                    Log::info("Successfully indexed batch {$batchIndex} with " . count($batch) . " restaurants in " . number_format($duration, 2) . "s (" . number_format($docsPerSecond, 2) . " docs/sec)");
                    
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
     * Check which restaurant IDs already exist in the index
     * 
     * @param Client $client
     * @param array $restaurantIds
     * @return array List of IDs that already exist in the index
     */
    protected function checkExistingRestaurants($client, $restaurantIds)
    {
        if (empty($restaurantIds)) {
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
            $idBatches = array_chunk($restaurantIds, 100);
            
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
            Log::warning("Error checking for existing restaurants: " . $e->getMessage());
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
                'Title', 'City', 'Country', 'Slug'
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
        
        Log::info("Restaurant index setup completed");
        $this->forceFullReindex = false;
    }
}
