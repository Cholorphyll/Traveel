<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Sight;
use App\Models\Restaurant;
use App\Models\Experience;

class ItineraryGenerator {
    private $distanceCache = []; // Cache for distance calculations
    private $maxResults = 1000; // Maximum number of results to return
    private $proximityThreshold = 1000; // Distance in meters to consider attractions nearby (1km)

    public function generateItinerary(array $params = []): array
    {
        try {
            // Extract locationId from params if provided
            $locationId = $params['locationId'] ?? null;
            
            // Debug: Log the locationId
            Log::info('ItineraryGenerator - locationId: ' . $locationId);
            
            if (!$locationId) {
                // If no locationId is provided, return the items passed in (limited to maxResults)
                if (isset($params[0]) && is_array($params[0])) {
                    return array_slice($params, 0, $this->maxResults);
                }
                return [];
            }
            
            // Get tier 1 items from all categories
            $tier1Sights = $this->getSights($locationId, null, 1);
            $tier1Restaurants = $this->getRestaurants($locationId, null, 1);
            $tier1Experiences = $this->getExperiences($locationId, null, 1);
            
            // Combine all tier 1 items
            $allTier1 = collect([]);
            
            // Add sights to tier 1 collection
            foreach ($tier1Sights as $sight) {
                $allTier1->push([
                    'type' => 'sight',
                    'id' => $sight->SightId,
                    'lat' => (float)$sight->Latitude,
                    'lng' => (float)$sight->Longitude,
                    'tier' => 1,
                    'data' => $sight
                ]);
            }
            
            // Add restaurants to tier 1 collection
            foreach ($tier1Restaurants as $restaurant) {
                $allTier1->push([
                    'type' => 'restaurant',
                    'id' => $restaurant->SightId,
                    'lat' => (float)$restaurant->Latitude,
                    'lng' => (float)$restaurant->Longitude,
                    'tier' => 1,
                    'data' => $restaurant
                ]);
            }
            
            // Add experiences to tier 1 collection
            foreach ($tier1Experiences as $experience) {
                $allTier1->push([
                    'type' => 'experience',
                    'id' => $experience->SightId,
                    'lat' => (float)$experience->Latitude,
                    'lng' => (float)$experience->Longitude,
                    'tier' => 1,
                    'data' => $experience
                ]);
            }
            
            // If no tier 1 items found, return empty array
            if ($allTier1->isEmpty()) {
                return [];
            }
            
            // Sort Tier 1 items by proximity
            $sortedItems = collect([]);
            if ($allTier1->isNotEmpty()) {
                $currentItem = $allTier1->shift();
                $sortedItems->push($currentItem);

                while ($allTier1->isNotEmpty()) {
                    $nearest = null;
                    $minDistance = PHP_INT_MAX;

                    foreach ($allTier1 as $index => $item) {
                        $distance = $this->calculateDistance(
                            $item['lat'], $item['lng'],
                            $currentItem['lat'], $currentItem['lng']
                        );
                        if ($distance < $minDistance) {
                            $minDistance = $distance;
                            $nearest = $item;
                            $nearestIndex = $index;
                        }
                    }

                    if ($nearest) {
                        $sortedItems->push($nearest);
                        $allTier1->splice($nearestIndex, 1);
                        $currentItem = $nearest;
                    }
                }
            }
            
            // Get Tier 2/3 items
            $tier2Sights = $this->getSights($locationId, null, [2, 3]);
            $tier2Restaurants = $this->getRestaurants($locationId, null, [2, 3]);
            $tier2Experiences = $this->getExperiences($locationId, null, [2, 3]);
            
            $allLowerTier = collect([]);
            
            // Add sights to lower tier collection
            foreach ($tier2Sights as $sight) {
                $allLowerTier->push([
                    'type' => 'sight',
                    'id' => $sight->SightId,
                    'lat' => (float)$sight->Latitude,
                    'lng' => (float)$sight->Longitude,
                    'tier' => $sight->tier,
                    'data' => $sight
                ]);
            }
            
            // Add restaurants to lower tier collection
            foreach ($tier2Restaurants as $restaurant) {
                $allLowerTier->push([
                    'type' => 'restaurant',
                    'id' => $restaurant->SightId,
                    'lat' => (float)$restaurant->Latitude,
                    'lng' => (float)$restaurant->Longitude,
                    'tier' => $restaurant->tier,
                    'data' => $restaurant
                ]);
            }
            
            // Add experiences to lower tier collection
            foreach ($tier2Experiences as $experience) {
                $allLowerTier->push([
                    'type' => 'experience',
                    'id' => $experience->SightId,
                    'lat' => (float)$experience->Latitude,
                    'lng' => (float)$experience->Longitude,
                    'tier' => $experience->tier,
                    'data' => $experience
                ]);
            }
            
            // Inject nearby Tier 2/3 items between Tier 1 items
            $finalFeed = collect([]);
            foreach ($sortedItems as $index => $tier1Item) {
                // Add Tier 1 item
                $finalFeed->push($tier1Item);

                // Find nearby Tier 2/3 items (within 1km)
                $nearby = $allLowerTier->filter(function ($item) use ($tier1Item) {
                    $distance = $this->calculateDistance(
                        $tier1Item['lat'], $tier1Item['lng'],
                        $item['lat'], $item['lng']
                    );
                    return $distance <= 1; // 1km radius
                })->sortByDesc('tier'); // Prefer Tier 2 over Tier 3

                // Add nearby items to feed (limited to 3 per tier 1 item)
                $count = 0;
                $maxNearby = 3;
                
                foreach ($nearby as $nearbyItem) {
                    if ($count < $maxNearby) {
                        $finalFeed->push($nearbyItem);
                        $count++;
                    } else {
                        break;
                    }
                }
            }
            
            // Format the itinerary to match the expected structure
            $result = $this->formatItinerary($finalFeed);
            
            // Return all results without limiting
            return $result;
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('ItineraryGenerator error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Return empty array on error
            return [];
        }
    }
    
    /**
     * Format the itinerary to match the expected structure in the application
     */
    private function formatItinerary(Collection $itinerary): array
    {
        $result = [];
        
        foreach ($itinerary as $item) {
            $data = $item['data'];
            
            if ($item['type'] === 'sight') {
                // Format sight data
                $sight = (array)$data;
                $sight['visited'] = true;
                
                // Ensure MustSee and IsMustSee are explicitly set to integers (1 or 0)
                if (isset($sight['MustSee']) && $sight['MustSee'] == 1) {
                    $sight['MustSee'] = 1;
                    $sight['IsMustSee'] = 1;
                } else {
                    $sight['MustSee'] = 0;
                    $sight['IsMustSee'] = 0;
                }
                
                $result[] = $sight;
                
            } elseif ($item['type'] === 'experience') {
                // Format experience data
                $experience = (array)$data;
                $experience['visited'] = true;
                $experience['MustSee'] = 0;
                $experience['IsMustSee'] = 0;
                $experience['SightId'] = 'exp_' . $experience['SightId'];
                $result[] = $experience;
                
            } elseif ($item['type'] === 'restaurant') {
                // Format restaurant data
                $restaurant = (array)$data;
                $restaurant['visited'] = true;
                $restaurant['MustSee'] = 0;
                $restaurant['IsMustSee'] = 0;
                $restaurant['SightId'] = 'rest_' . $restaurant['SightId'];
                $result[] = $restaurant;
            }
        }
        
        return $result;
    }
    
    /**
     * Get sights from the database with limit and tier filter
     */
    public function getSights($locationId = null, $limit = 30, $tier = null): array
    {
        $query = DB::table('Sight as s')
            ->select(
                's.SightId', 's.Title', 's.Latitude', 's.Longitude', 
                's.ReviewCount', 's.Averagerating', 's.tier',
                DB::raw('CASE WHEN s.MustSee = 1 THEN 1 ELSE 0 END as MustSee'),
                DB::raw('CASE WHEN s.MustSee = 1 THEN 1 ELSE 0 END as IsMustSee'),
                's.LocationId', 's.Slug', 'IsRestaurant', 'Address', 's.CategoryId', 
                'c.Title as CategoryTitle', 'l.Name as LName', 'l.Slug as Lslug',
                'l.slugid', 'l.tp_location_mapping_id', 's.ticket', 's.MicroSummary',
                DB::raw("'attraction' as type")
            )
            ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
            ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
            ->whereNotNull('s.Latitude')
            ->whereNotNull('s.Longitude');
            
        if ($locationId) {
            $query->where('s.LocationId', $locationId);
        }
        
        if ($tier !== null) {
            if (is_array($tier)) {
                $query->whereIn('s.tier', $tier);
            } else {
                $query->where('s.tier', $tier);
            }
        }
        
        $query->orderBy('s.tier', 'asc')
              ->orderBy('s.MustSee', 'desc')
              ->orderBy('s.ReviewCount', 'desc')
              ->orderBy('s.Averagerating', 'desc');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get()->toArray();
    }
    
    /**
     * Get restaurants from the database with limit and tier filter
     */
    public function getRestaurants($locationId = null, $limit = 15, $tier = null): array
    {
        $query = DB::table('Restaurant as r')
            ->select(
                'r.RestaurantId as SightId', 'r.Title', 'r.Latitude', 'r.Longitude',
                'r.ReviewCount', 'r.Averagerating', 'r.tier', 'r.LocationId', 'r.slugid', 
                'r.Slug', 'r.Timings', 'r.PriceRange', 'r.category', 'r.features',
                'r.Address', 'l.Name as LName',
                DB::raw("'restaurant' as type")
            )
            ->join('Location as l', 'r.LocationId', '=', 'l.LocationId')
            ->whereNotNull('r.Latitude')
            ->whereNotNull('r.Longitude');
            
        if ($locationId) {
            $query->where('r.LocationId', $locationId);
        }
        
        if ($tier !== null) {
            if (is_array($tier)) {
                $query->whereIn('r.tier', $tier);
            } else {
                $query->where('r.tier', $tier);
            }
        }
        
        $query->orderBy('r.tier', 'asc')
              ->orderBy('r.Averagerating', 'desc')
              ->orderBy('r.ReviewCount', 'desc');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get()->toArray();
    }
    
    /**
     * Get experiences from the database with limit and tier filter
     */
    public function getExperiences($locationId = null, $limit = 15, $tier = null): array
    {
        $query = DB::table('Experience as e')
            ->select(
                'e.ExperienceId as SightId', 'e.Name as Title', 'e.Latitude', 'e.Longitude',
                'e.ViatorReviewCount as ReviewCount', 'e.ViatorAggregationRating as Averagerating',
                'e.tier', 'e.LocationId', 'e.slugid', 'e.Slug', 'e.viator_url', 'e.adult_price',
                'e.Img1', 'e.Img2', 'e.Img3', 'l.Name as LName',
                DB::raw("'experience' as type")
            )
            ->join('Location as l', 'e.LocationId', '=', 'l.LocationId')
            ->whereNotNull('e.Latitude')
            ->whereNotNull('e.Longitude');
            
        if ($locationId) {
            $query->where('e.LocationId', $locationId);
        }
        
        if ($tier !== null) {
            if (is_array($tier)) {
                $query->whereIn('e.tier', $tier);
            } else {
                $query->where('e.tier', $tier);
            }
        }
        
        $query->orderBy('e.tier', 'asc')
              ->orderBy('e.ViatorAggregationRating', 'desc')
              ->orderBy('e.ViatorReviewCount', 'desc');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get()->toArray();
    }
    
    /**
     * Convert coordinates to float
     */
    private function parseCoordinates(array $items): array
    {
        return array_map(function ($item) {
            $item = (array)$item;
            $item['Latitude'] = (float)$item['Latitude'];
            $item['Longitude'] = (float)$item['Longitude'];
            return $item;
        }, $items);
    }
    
    /**
     * Calculate distance between two points in meters
     */
    private function calculateDistanceInMeters(array $a, array $b): float
    {
        return $this->calculateDistance($a[0], $a[1], $b[0], $b[1]) * 1000; // Convert km to meters
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        // Create a cache key for this distance calculation
        $cacheKey = json_encode([$lat1, $lng1, $lat2, $lng2]);
        
        // Check if we've already calculated this distance
        if (isset($this->distanceCache[$cacheKey])) {
            return $this->distanceCache[$cacheKey];
        }
        
        // Haversine formula
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        // Cache the result
        $this->distanceCache[$cacheKey] = $distance;
        
        return $distance;
    }
}
