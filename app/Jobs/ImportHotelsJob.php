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

class ImportHotelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meiliIndex = 'hotels';
    protected $timeout = 0; // Disable timeout
    protected $testMode = false;
    protected $lastProcessedId = 0; // Start from the beginning
    protected $forceFullReindex = false; // Never delete the index
    protected $meilisearchTimeout = 1200; // 20 minutes for operations
    protected $chunkSize = 15000; // Optimized chunk size for better performance
    protected $cacheKey = 'hotel_indexing_progress'; // Cache key to store progress
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

            $this->processHotels($client);
            $this->finalizeIndex($client);
            
            // Clear the cache when job completes successfully
            Cache::forget($this->cacheKey);
            Log::info("Hotel indexing completed successfully");

        } catch (\Exception $e) {
            // Save progress to cache so we can resume later
            $this->saveProgress();
            Log::error("Hotel indexing failed at ID {$this->lastProcessedId}: " . $e->getMessage());
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
            Log::info("Using existing hotel index {$this->meiliIndex}");
        } catch (\Exception $e) {
            $client->createIndex($this->meiliIndex, ['primaryKey' => 'id']);
            Log::info("Created new hotel index {$this->meiliIndex}");
        }
        
        // Update index settings immediately
        $this->updateIndexSettings($client);
        
        // Start from the beginning
        $this->lastProcessedId = 0;
        Log::info("Setting starting ID to 0 to process all hotels");
    }

    protected function resumeIndexing($client)
    {
        try {
            // Check if index exists
            $index = $client->getIndex($this->meiliIndex);
            
            // Update filterable attributes immediately to ensure duplicate checking works
            $this->updateIndexSettings($client);
            
            // If we have a cached progress ID, use that
            if ($this->lastProcessedId > 0) {
                Log::info("Resuming hotel indexing from ID: {$this->lastProcessedId}");
                return;
            }
            
            // Otherwise start from the beginning
            $this->lastProcessedId = 0;
            Log::info("Starting hotel indexing from the beginning");
            
        } catch (\Exception $e) {
            // Index doesn't exist, create it
            $client->createIndex($this->meiliIndex, ['primaryKey' => 'id']);
            Log::info("Created new hotel index {$this->meiliIndex}, starting from ID: {$this->lastProcessedId}");
            
            // Update settings for the new index
            $this->updateIndexSettings($client);
        }
    }

    protected function processHotels($client)
    {
        $totalHotels = DB::table('TPHotel')
            ->where('TPHotel.id', '>', $this->lastProcessedId)
            ->count();
            
        Log::info("Processing {$totalHotels} hotels starting from ID: {$this->lastProcessedId}");
        
        $query = DB::table('TPHotel')
            ->select(
                'TPHotel.id as id',
                'TPHotel.name',
                'TPHotel.slugid',
                'TPHotel.LocationId',
                'TPHotel.CityName',
                'TPHotel.CountryName',
                'TPHotel.Latitude',
                'TPHotel.longnitude',
                'TPHotel.slug'
            )
            ->where('TPHotel.id', '>', $this->lastProcessedId)
            ->orderBy('TPHotel.id');

        if ($this->testMode) {
            $query->limit(10);
            Log::info("Running in TEST MODE - only processing first 10 hotels");
        }

        $processedCount = 0;
        $query->chunk($this->chunkSize, function ($hotels) use ($client, &$processedCount, $totalHotels) {
            set_time_limit(1000); // Reset timer for each chunk with more time
            
            $chunkStartTime = microtime(true);
            $documents = [];
            $lastIdInChunk = $this->lastProcessedId;
            $hotelIds = [];
            
            // First pass: collect all hotel IDs to check for duplicates
            if ($this->checkDuplicates) {
                foreach ($hotels as $hotel) {
                    $hotelIds[] = $hotel->id;
                }
                
                // Check which IDs already exist in the index
                $existingIds = $this->checkExistingHotels($client, $hotelIds);
                Log::info("Found " . count($existingIds) . " existing hotels out of " . count($hotelIds));
            }
            
            foreach ($hotels as $hotel) {
                // Skip if this hotel already exists in the index
                if ($this->checkDuplicates && in_array($hotel->id, $existingIds ?? [])) {
                    continue;
                }
                
                $documents[] = [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'slugid' => $hotel->slugid,
                    'LocationId' => $hotel->LocationId,
                    'cityName' => $hotel->CityName,
                    'countryName' => $hotel->CountryName,
                    'latitude' => $hotel->Latitude,
                    'longitude' => $hotel->longnitude,
                    'slug' => $hotel->slug
                ];
                $lastIdInChunk = $hotel->id;
            }

            $this->sendToMeilisearch($client, $documents);
            
            // Only update lastProcessedId after successful indexing
            $this->lastProcessedId = $lastIdInChunk;
            
            // Save progress periodically
            $this->saveProgress();
            
            $processedCount += count($documents);
            $percentComplete = $totalHotels > 0 ? round(($processedCount / $totalHotels) * 100, 2) : 0;
            $chunkTime = round(microtime(true) - $chunkStartTime, 2);
            
            Log::info("Processed chunk of " . count($documents) . " hotels in {$chunkTime}s. Progress: {$processedCount}/{$totalHotels} ({$percentComplete}%)");
        });
    }

    protected function sendToMeilisearch($client, $documents)
    {
        if (empty($documents)) {
            Log::info("No documents to index in this chunk");
            return;
        }
        
        $retryCount = 0;
        $batchSize = 5000; // Split into smaller batches for better reliability
        $batches = array_chunk($documents, $batchSize);
        
        foreach ($batches as $batchIndex => $batch) {
            $retryCount = 0;
            
            while ($retryCount < $this->maxRetries) {
                try {
                    $index = $client->index($this->meiliIndex);
                    $startTime = microtime(true);
                    
                    // Add documents with a unique primary key to avoid duplicates
                    $task = $index->addDocuments($batch);
                    $taskStatus = $index->waitForTask($task['taskUid'], $this->meilisearchTimeout * 1000);

                    if (($taskStatus['status'] ?? null) !== 'succeeded') {
                        throw new \Exception("Indexing failed for task UID {$task['taskUid']}");
                    }

                    $duration = round(microtime(true) - $startTime, 2);
                    $docsPerSecond = round(count($batch) / $duration, 2);
                    Log::info("Successfully indexed batch {$batchIndex} with " . count($batch) . " hotels in {$duration}s ({$docsPerSecond} docs/sec)");
                    break; // Success, move to next batch

                } catch (\Exception $e) {
                    $retryCount++;
                    if ($retryCount >= $this->maxRetries) {
                        Log::error("Failed to index batch {$batchIndex} after {$this->maxRetries} attempts.");
                        throw $e;
                    }

                    $backoffTime = pow(2, $retryCount);
                    Log::warning("Retry {$retryCount}/{$this->maxRetries} for batch {$batchIndex}. Waiting {$backoffTime}s. Error: " . $e->getMessage());
                    sleep($backoffTime); // Exponential backoff
                }
            }
        }
    }

    /**
     * Check which hotel IDs already exist in the index
     * 
     * @param Client $client
     * @param array $hotelIds
     * @return array List of IDs that already exist in the index
     */
    protected function checkExistingHotels($client, $hotelIds)
    {
        if (empty($hotelIds)) {
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
            $idBatches = array_chunk($hotelIds, 100);
            
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
            Log::warning("Error checking for existing hotels: " . $e->getMessage());
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
                'name', 'cityName', 'countryName'
            ]);
            
            // Update filterable attributes - crucial for duplicate checking
            $index->updateFilterableAttributes([
                'LocationId', 'id'
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
        
        Log::info("Hotel index setup completed");
        $this->forceFullReindex = false;
    }
}
