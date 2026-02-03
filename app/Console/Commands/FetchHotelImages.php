<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchHotelImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotels:fetch-images {--location= : Specific location ID to process} {--limit=10 : Number of locations to process per run} {--offset=0 : Start from this location number} {--batch= : Batch identifier (1-8 for parallel processing)} {--debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch hotel images from HotelLook API and save to database';

    /**
     * API credentials
     */
    private $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
    
    /**
     * Rate limiting
     */
    private $requestDelay = 0; // NO DELAY - Maximum speed
    private $timeout = 5; // API timeout in seconds (balanced for reliability)
    private $batchSize = 500; // Large batch inserts for speed
    private $maxRetries = 2; // Retry failed API calls up to 2 times

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting hotel images fetch process...');
        
        $specificLocation = $this->option('location');
        $limit = $this->option('limit');
        $offset = $this->option('offset');
        $batch = $this->option('batch');
        
        try {
            if ($specificLocation) {
                // Process specific location
                $this->processLocation($specificLocation);
            } else {
                // Process multiple locations
                $this->processMultipleLocations($limit, $offset, $batch);
            }
            
            $this->info('Hotel images fetch completed successfully!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('FetchHotelImages Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Process multiple locations
     */
    private function processMultipleLocations($limit, $offset = 0, $batch = null)
    {
        // OPTIMIZED: Get locations from TPLocations table
        // Use NOT EXISTS subquery instead of LEFT JOIN for much better performance
        $query = DB::table('TPLocations as t')
            ->select('t.id', 't.fullName')
            ->whereNotNull('t.id')
            ->where('t.id', '!=', 0)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('TPhotel_images')
                      ->whereColumn('TPhotel_images.location_id', 't.id')
                      ->limit(1);
            })
            ->orderBy('t.id', 'asc'); // Ensure consistent ordering
        
        // Handle batch processing (divide work across multiple devices)
        if ($batch !== null) {
            $batchNumber = (int)$batch;
            $totalBatches = 8; // Default: 8 devices
            
            // Calculate which locations this batch should process
            // Using modulo to distribute evenly
            $query->whereRaw("MOD(t.id, ?) = ?", [$totalBatches, $batchNumber - 1]);
            
            $this->info("Processing batch {$batchNumber} of {$totalBatches} (every {$totalBatches}th location starting from position {$batchNumber})");
        }
        
        // Apply offset if specified
        if ($offset > 0) {
            $query->offset($offset);
        }
        
        // Apply limit if specified
        if ($limit > 0) {
            $query->limit($limit);
        }
        
        // Debug mode: Show SQL query
        if ($this->option('debug')) {
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            $this->info("SQL Query: " . $sql);
            $this->info("Bindings: " . json_encode($bindings));
            
            // Show total locations in database
            $totalLocations = DB::table('TPLocations')->count();
            $this->info("Total locations in TPLocations: {$totalLocations}");
            
            // Show locations matching batch
            if ($batch !== null) {
                $batchNumber = (int)$batch;
                $totalBatches = 8;
                $batchLocations = DB::table('TPLocations')
                    ->whereRaw("MOD(id, ?) = ?", [$totalBatches, $batchNumber - 1])
                    ->count();
                $this->info("Locations matching batch {$batchNumber}: {$batchLocations}");
            }
        }
        
        $locations = $query->get();
        
        // REMOVED: Expensive count query - not needed for processing
        // This was causing massive slowdown with 80 parallel jobs
        
        $this->info("Found {$locations->count()} locations to process");
        
        $totalProcessed = 0;
        $totalImagesFound = 0;
        
        foreach ($locations as $location) {
            try {
                $this->info("Processing location: {$location->fullName} (ID: {$location->id})");
                
                // Skip progress tracking for maximum speed
                // $this->trackProgress($location->id, $location->fullName, 'processing', $batch);
                
                $imagesCount = $this->processLocation($location->id);
                $totalImagesFound += $imagesCount;
                $totalProcessed++;
                
                // Skip progress tracking for maximum speed
                // $this->trackProgress($location->id, $location->fullName, 'completed', $batch, $imagesCount);
                
                // Rate limiting - delay between locations (disabled for speed)
                if ($this->requestDelay > 0) {
                    sleep($this->requestDelay);
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing location {$location->fullName}: " . $e->getMessage());
                
                // Skip progress tracking for maximum speed
                // $this->trackProgress($location->id, $location->fullName, 'failed', $batch, 0, $e->getMessage());
                
                Log::error("Error processing location {$location->id}", [
                    'location_id' => $location->id,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
        
        $this->info("Processed {$totalProcessed} locations, found {$totalImagesFound} total images");
    }

    /**
     * Process a single location
     */
    private function processLocation($locationId)
    {
        $totalImages = 0;
        
        // Step 1: Get hotels for this location from static API
        $hotels = $this->fetchHotelsForLocation($locationId);
        
        if (empty($hotels)) {
            $this->warn("No hotels found for location ID: {$locationId}");
            return 0;
        }
        
        $this->info("Found " . count($hotels) . " hotels for location {$locationId}");
        
        // Step 2: For each hotel, fetch images and save to database
        foreach ($hotels as $hotel) {
            $hotelId = null;
            try {
                // Check if hotel is an array or object
                $hotelId = is_array($hotel) ? $hotel['id'] : $hotel->id;
                
                // Fetch images from HotelLook photos API
                $images = $this->fetchHotelImages($hotelId);
                
                if (!empty($images)) {
                    // Save images to database
                    $savedCount = $this->saveHotelImages($hotelId, $locationId, $images);
                    $totalImages += $savedCount;
                    // Reduced logging for speed
                    // $this->info("  Hotel {$hotelId}: Saved {$savedCount} images");
                } else {
                    // Reduced logging for speed
                    // $this->warn("  Hotel {$hotelId}: No images found");
                }
                
                // Rate limiting (disabled for maximum speed)
                if ($this->requestDelay > 0) {
                    sleep($this->requestDelay);
                }
                
            } catch (\Exception $e) {
                $hotelIdStr = $hotelId ?? 'unknown';
                $this->error("  Error processing hotel {$hotelIdStr}: " . $e->getMessage());
                Log::error("Error processing hotel", [
                    'hotel_id' => $hotelId,
                    'location_id' => $locationId,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
        
        return $totalImages;
    }

    /**
     * Fetch hotels for a location from HotelLook static API
     */
    private function fetchHotelsForLocation($locationId)
    {
        $url = "https://engine.hotellook.com/api/v2/static/hotels.json?locationId={$locationId}&token={$this->TRAVEL_PAYOUT_TOKEN}";
        
        $retries = 0;
        while ($retries <= $this->maxRetries) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout($this->timeout)
                    ->get($url);
                
                // Check rate limiting headers
                $this->handleRateLimiting($response);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['hotels']) && is_array($data['hotels'])) {
                        return $data['hotels'];
                    }
                } else {
                    if ($retries < $this->maxRetries) {
                        $retries++;
                        sleep(1); // Wait 1 second before retry
                        continue;
                    }
                    $this->warn("API request failed for location {$locationId}: " . $response->status());
                }
                
                break; // Exit loop if successful or max retries reached
                
            } catch (\Exception $e) {
                if ($retries < $this->maxRetries) {
                    $retries++;
                    sleep(1); // Wait 1 second before retry
                    continue;
                }
                
                $this->error("Error fetching hotels for location {$locationId}: " . $e->getMessage());
                Log::error("Error fetching hotels", [
                    'location_id' => $locationId,
                    'error' => $e->getMessage()
                ]);
                break;
            }
        }
        
        return [];
    }

    /**
     * Fetch images for a specific hotel from HotelLook photos API
     */
    private function fetchHotelImages($hotelId)
    {
        $url = "https://yasen.hotellook.com/photos/hotel_photos?id={$hotelId}";
        
        $retries = 0;
        while ($retries <= $this->maxRetries) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout($this->timeout)
                    ->get($url);
                
                if ($response->successful()) {
                    $images = $response->json();
                    
                    // The API returns an array of image IDs for the hotel
                    // Format: {"hotelId": [imageId1, imageId2, ...]}
                    if (is_array($images) && isset($images[$hotelId])) {
                        return $images[$hotelId];
                    }
                }
                
                break; // Exit loop if successful
                
            } catch (\Exception $e) {
                if ($retries < $this->maxRetries) {
                    $retries++;
                    sleep(1); // Wait 1 second before retry
                    continue;
                }
                
                // Only log error on final retry
                Log::error("Error fetching hotel images", [
                    'hotel_id' => $hotelId,
                    'error' => $e->getMessage()
                ]);
                break;
            }
        }
        
        return [];
    }

    /**
     * Save hotel images to database
     */
    private function saveHotelImages($hotelId, $locationId, $images)
    {
        $savedCount = 0;
        
        // Check if table exists
        if (!$this->tableExists('TPhotel_images')) {
            $this->error("Table TPhotel_images does not exist!");
            return 0;
        }
        
        // OPTIMIZED: Check if hotel already has images before deleting
        // This reduces unnecessary DELETE operations
        $existingCount = DB::table('TPhotel_images')
            ->where('hotelid', $hotelId)
            ->count();
        
        if ($existingCount > 0) {
            // Only delete if images exist
            DB::table('TPhotel_images')
                ->where('hotelid', $hotelId)
                ->delete();
        }
        
        // Prepare batch insert data
        $insertData = [];
        
        foreach ($images as $imageId) {
            // Construct image URL
            // HotelLook image URL format: https://photo.hotellook.com/image_v2/limit/{imageId}/{width}/{height}.auto
            $imageUrl = "https://photo.hotellook.com/image_v2/limit/{$imageId}/1024/768.auto";
            
            $insertData[] = [
                'hotelid' => $hotelId,
                'location_id' => $locationId,
                'image_id' => $imageId,
                'Image' => $imageUrl,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $savedCount++;
        }
        
        // Batch insert for better performance
        if (!empty($insertData)) {
            // Insert in chunks of 100 for better memory management
            $chunks = array_chunk($insertData, 100);
            foreach ($chunks as $chunk) {
                try {
                    DB::table('TPhotel_images')->insert($chunk);
                } catch (\Exception $e) {
                    // If batch insert fails, try individual inserts
                    foreach ($chunk as $data) {
                        try {
                            DB::table('TPhotel_images')->insert($data);
                        } catch (\Exception $e2) {
                            $this->error("Failed to insert image: " . $e2->getMessage());
                        }
                    }
                }
            }
        }
        
        return $savedCount;
    }

    /**
     * Handle API rate limiting
     */
    private function handleRateLimiting($response)
    {
        $rateLimitRemaining = $response->header('X-RateLimit-Remaining');
        $rateLimitReset = $response->header('X-RateLimit-Reset');
        
        if ($rateLimitRemaining !== null && $rateLimitRemaining == 0) {
            $resetTimestamp = (int)$rateLimitReset;
            $resetSeconds = max($resetTimestamp - time(), 0);
            
            $this->warn("Rate limit reached. Waiting {$resetSeconds} seconds...");
            sleep($resetSeconds);
        }
    }

    /**
     * Check if table exists
     */
    private function tableExists($tableName)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Track progress for resume functionality
     */
    private function trackProgress($locationId, $locationName, $status, $batchNumber = null, $imagesSaved = 0, $errorMessage = null)
    {
        try {
            // Check if progress table exists
            if (!$this->tableExists('hotel_image_fetch_progress')) {
                return; // Skip if table doesn't exist
            }

            $data = [
                'location_id' => $locationId,
                'location_name' => $locationName,
                'status' => $status,
                'batch_number' => $batchNumber,
                'updated_at' => now(),
            ];

            if ($status === 'processing') {
                $data['started_at'] = now();
            }

            if ($status === 'completed') {
                $data['completed_at'] = now();
                $data['images_saved'] = $imagesSaved;
            }

            if ($status === 'failed') {
                $data['error_message'] = $errorMessage;
                $data['completed_at'] = now();
            }

            // Upsert (insert or update)
            DB::table('hotel_image_fetch_progress')->updateOrInsert(
                ['location_id' => $locationId],
                $data
            );
        } catch (\Exception $e) {
            // Silently fail - don't stop the main process
            Log::warning("Failed to track progress for location {$locationId}: " . $e->getMessage());
        }
    }
}
