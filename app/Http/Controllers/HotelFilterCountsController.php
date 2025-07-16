<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HotelFilterCountsController extends Controller
{
    public function getFilterCounts(Request $request)
    {
        $locationid = $request->get('id');
        $chkin = $request->get('Cin');
        $checout = $request->get('Cout');
        $cacheKey = "filter_counts_{$locationid}_{$chkin}_{$checout}";

        $counts = Cache::remember($cacheKey, 60, function () use ($locationid, $chkin, $checout) {
            // Base query for all hotels matching the location/dates
            $baseQuery = DB::table('TPHotel as h')
                ->join('hotelbookingstemp as hotemp', 'hotemp.hotelid', '=', 'h.hotelid')
                ->whereNotNull('h.slugid');

            // Get counts for each filter category
            $counts = [
                'amenities' => $this->getAmenityCounts($baseQuery),
                'nearbyPlaces' => $this->getNearbyCounts($baseQuery, $locationid)
            ];

            $results = $baseQuery->clone()
                ->join('TPHotel_types as t', 'h.propertyType', '=', 't.hid')
                ->select(
                    'h.stars',
                    't.type as propertyType',
                    'hotemp.agency_name as agency',
                    'h.rating',
                    DB::raw('COUNT(DISTINCT h.hotelid) as count')
                )
                ->groupBy('h.stars', 't.type', 'hotemp.agency_name', 'h.rating')
                ->get();

            $counts['stars'] = $results->groupBy('stars')->map(function ($item) {
                return $item->sum('count');
            });

            $counts['propertyTypes'] = $results->groupBy('propertyType')->map(function ($item) {
                return $item->sum('count');
            });

            $counts['agencies'] = $results->groupBy('agency')->map(function ($item) {
                return $item->sum('count');
            });

            $ratingRanges = [
                '1' => [1, 2],
                '2' => [2, 3],
                '3' => [3, 4],
                '4' => [4, 5],
                '5' => [5, 6],
                '6' => [6, 7],
                '7' => [7, 8],
                '8' => [8, 9],
                '9' => [9, 9.5],
                '9.5' => [9.5, 10],
                '10' => [10, 10],
            ];
            $guestRatings = [];
            foreach ($ratingRanges as $key => $range) {
                $guestRatings[$key] = $results->where('rating', '>=', $range[0])->where('rating', '<', $range[1])->sum('count');
            }
            $counts['guestRatings'] = $guestRatings;

            return $counts;
        });

        return response()->json($counts);
    }

    private function getAmenityCounts($query)
    {
        $commonAmenities = [
            'Wi-Fi in areas', 'breakfast', 'freeWifi', 'Parking', 'Gym', 'Laundry service',
            'Bar', 'Restaurant/cafe', 'A/C', 'Private Bathroom', 'TV', 'Balcony/terrace',
            'Bathtub', 'Handicapped Room', 'Inhouse movies', 'Mini bar', 'Swimming Pool',
            '24h. Reception', 'Smoke-free', 'Wheel chair access', 'refundable', 'cardRequired',
            'Bicycle rental', 'Tours', 'Sauna', 'Water Sports'
        ];

        $amenityIds = DB::table('TPHotel_amenities')
            ->whereIn('shortName', $commonAmenities)
            ->pluck('id', 'shortName');

        $selects = [];
        foreach ($commonAmenities as $amenity) {
            if (in_array($amenity, ['breakfast', 'freeWifi', 'refundable', 'cardRequired'])) {
                $selects[] = DB::raw("COUNT(DISTINCT CASE WHEN hotemp.amenity LIKE '%{$amenity}%' THEN h.hotelid END) as '{$amenity}'");
            } else {
                if (isset($amenityIds[$amenity])) {
                    $id = $amenityIds[$amenity];
                    $selects[] = DB::raw("COUNT(DISTINCT CASE WHEN FIND_IN_SET('{$id}', h.facilities) THEN h.hotelid END) as '{$amenity}'");
                }
            }
        }

        $results = $query->clone()->select($selects)->first();

        $counts = [];
        foreach ($commonAmenities as $amenity) {
            $counts[$amenity] = $results->$amenity ?? 0;
        }

        return $counts;
    }

    private function getNearbyCounts($query, $locationid)
    {     
        $nearbyPlaces = DB::table('Sight')
            ->where('Location_id', $locationid)
        	->whereNotNull('Latitude')
            ->orderBy('Avg_MonthlySearches', 'desc')
            ->take(7)
            ->get(['SightId', 'Title', 'Latitude', 'Longitude', 'LocationId']);
        
        $counts = [];
        
        foreach ($nearbyPlaces as $place) {
            // Skip places with null coordinates
            if (empty($place->Latitude) || empty($place->Longitude)) {
                continue;
            }
            
            $clonedQuery = $query->clone();
            
            try {
                // Use ST_Distance_Sphere if available (more accurate and faster)
                $clonedQuery->whereRaw('
                    ST_Distance_Sphere(
                        point(CAST(h.longnitude AS DECIMAL(10,6)), CAST(h.Latitude AS DECIMAL(10,6))),
                        point(?, ?)
                    ) <= ?',
                    [(float)$place->Longitude, (float)$place->Latitude, 3000] // 3km in meters
                );
            } catch (\Exception $e) {
                // Fallback to simpler distance calculation if ST_Distance_Sphere not available
                $clonedQuery->whereRaw('
                    ROUND(
                        111.045 * SQRT(
                            POWER(ABS(CAST(h.Latitude AS DECIMAL(10,6)) - ?), 2) +
                            POWER(
                                ABS(CAST(h.longnitude AS DECIMAL(10,6)) - ?) * 
                                COS(RADIANS(?)), 
                                2
                            )
                        ), 2
                    ) <= ?', 
                    [
                        (float)$place->Latitude,
                        (float)$place->Longitude,
                        (float)$place->Latitude,
                        3 // 3 km radius
                    ]
                );
            }
            $clonedQuery->whereNotNull('h.Latitude')
            ->whereNotNull('h.longnitude')
            ->whereRaw('TRIM(h.Latitude) != ""')
            ->whereRaw('TRIM(h.longnitude) != ""');
      
            $count = $clonedQuery->count(DB::raw('DISTINCT h.hotelid'));               
            
            $counts[$place->SightId] = $count;
        }
        
        return $counts;
    }
}
