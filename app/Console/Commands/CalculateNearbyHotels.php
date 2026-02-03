<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\Hotel;
use App\Models\NearByHotel;

class CalculateNearbyHotels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotels:calculate-nearby {--limit=5 : Limit the number of locations to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store nearby hotels within 25km for each location';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting nearby hotels calculation...');
        
        $limit = $this->option('limit');
        
        // Get locations with coordinates, limiting for testing
        $query = DB::table('Location')
            ->whereNotNull('Lat')
            ->whereNotNull('Longitude')
            ->where('Lat', '!=', '')
            ->where('Longitude', '!=', '')
            ->where('Lat', '!=', '0')
            ->where('Longitude', '!=', '0');
            
        // Apply limit only if it's greater than 0
        if ($limit > 0) {
            $query->limit($limit);
        }
        
        $locations = $query->get(['slugid', 'LocationId', 'Name', 'Lat', 'Longitude']);

        $this->info("Processing {$locations->count()} locations...");

        $totalProcessed = 0;
        $totalHotelsFound = 0;

        foreach ($locations as $location) {
            try {
                $this->info("Processing location: {$location->Name} (ID: {$location->slugid})");
                
                // Clear existing nearby hotels for this location
                DB::table('NearByHotel')->where('slugid', $location->slugid)->delete();
                
                $nearbyHotels = $this->findNearbyHotels(
                    $location->Lat, 
                    $location->Longitude, 
                    $location->slugid
                );
                
                // Batch insert for better performance
                $insertData = [];
                foreach ($nearbyHotels as $hotel) {
                    $insertData[] = [
                        'hotelid' => $hotel->hotelid,
                        'slugid' => $location->slugid
                    ];
                }
                
                if (!empty($insertData)) {
                    // Insert in chunks of 1000 for better memory management
                    $chunks = array_chunk($insertData, 1000);
                    foreach ($chunks as $chunk) {
                        DB::table('NearByHotel')->insert($chunk);
                    }
                }
                
                $hotelCount = count($insertData);
                $this->info("Found {$hotelCount} hotels within 25km of {$location->Name}");
                $totalHotelsFound += $hotelCount;
                $totalProcessed++;
                
            } catch (\Exception $e) {
                $this->error("Error processing location {$location->Name}: " . $e->getMessage());
                continue;
            }
        }

        $this->info("Completed! Processed {$totalProcessed} locations and found {$totalHotelsFound} nearby hotel relationships.");
        
        return Command::SUCCESS;
    }

    /**
     * Find hotels within 25km of given coordinates, excluding hotels already in that location
     */
    private function findNearbyHotels($lat, $lng, $slugid)
    {
        // First get hotels that are already directly associated with this location
        $existingHotelIds = DB::table('TPHotel')
            ->where('slugid', $slugid)
            ->pluck('hotelid')
            ->toArray();

        $this->info("   Excluding {" . count($existingHotelIds) . "} hotels already in this location");

        // Use Haversine formula to calculate distance
        // 25km = 25 kilometers, but exclude hotels already in this location
        $hotels = DB::table('TPHotel')
            ->select('hotelid', 'name', 'Latitude', 'longnitude', 'slugid')
            ->whereNotNull('Latitude')
            ->whereNotNull('longnitude')
            ->where('Latitude', '!=', '')
            ->where('longnitude', '!=', '')
            ->where('Latitude', '!=', '0')
            ->where('longnitude', '!=', '0')
            ->where('slugid', '!=', $slugid) // Exclude hotels from the same location
            ->whereNotIn('hotelid', $existingHotelIds) // Extra safety: exclude by hotelid too
            ->whereRaw('
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(CAST(Latitude AS DECIMAL(10,6)))) * 
                    cos(radians(CAST(longnitude AS DECIMAL(10,6))) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(CAST(Latitude AS DECIMAL(10,6))))
                )) <= 25
            ', [$lat, $lng, $lat])
            ->get();

        return $hotels;
    }
}
