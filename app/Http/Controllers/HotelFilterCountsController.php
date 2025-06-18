<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelFilterCountsController extends Controller
{
    public function getFilterCounts(Request $request)
    {
        $locationid = $request->get('id');
        $chkin = $request->get('Cin');
        $checout = $request->get('Cout');
        
        // Base query for all hotels matching the location/dates
        $baseQuery = DB::table('TPHotel as h')
            ->join('hotelbookingstemp as hotemp', 'hotemp.hotelid', '=', 'h.hotelid')
            ->whereNotNull('h.slugid');
            
        // Get counts for each filter category
        $counts = [
            'stars' => $this->getStarRatingCounts($baseQuery),
            'amenities' => $this->getAmenityCounts($baseQuery),
            'propertyTypes' => $this->getPropertyTypeCounts($baseQuery),
            'agencies' => $this->getAgencyCounts($baseQuery),
            'guestRatings' => $this->getGuestRatingCounts($baseQuery),
            'nearbyPlaces' => $this->getNearbyCounts($baseQuery, $locationid)
        ];
        
        return response()->json($counts);
    }
    
    private function getStarRatingCounts($query)
    {
        $starRatings = [1, 2, 3, 4, 5];
        $counts = [];
        
        foreach ($starRatings as $rating) {
            $count = $query->clone()
                ->where('h.stars', $rating)
                ->count(DB::raw('DISTINCT h.hotelid'));
            $counts[$rating] = $count;
        }
        
        return $counts;
    }
    
    private function getAmenityCounts($query)
    {
        $commonAmenities = [
            'Wi-Fi in areas', // Matches blade template value exactly
            'breakfast',      // Matches blade template value exactly  
            'freeWifi',       // Matches blade template value exactly
            'Parking',
            'Gym',
            'Laundry service',
            'Bar',
            'Restaurant/cafe',
            'A/C',
            'Private Bathroom',
            'TV',
            'Balcony/terrace',
            'Bathtub',
            'Handicapped Room',
            'Inhouse movies',
            'Mini bar',
			'Swimming Pool',
        	'24h. Reception',
        	'Smoke-free',
        	'Wheel chair access',
			'refundable',
            'cardRequired',
            'breakfast',
			'Bicycle rental',
			'Tours',
			'Sauna',
			'Water Sports'
        ];
        
        $counts = [];
        
        foreach ($commonAmenities as $amenity) {
            $countQuery = clone $query;
            
            if (in_array($amenity, ['breakfast', 'freeWifi', 'refundable', 'cardRequired', 'breakfast'])) {
                // Check hotelbookingstemp.amenity for these
                $countQuery->where('hotemp.amenity', 'LIKE', '%'.$amenity.'%');
            } else {
                // Check h.amenities column for others using FIND_IN_SET
                $amenityId = DB::table('TPHotel_amenities')
                    ->where('shortName', $amenity)
                    ->value('id');
                
                if ($amenityId) {
                    $countQuery->whereRaw("FIND_IN_SET(?, h.facilities) > 0", [$amenityId]);
                }
            }
            
            $count = $countQuery->count(DB::raw('DISTINCT h.hotelid'));
            $counts[$amenity] = $count;
        }
        
        return $counts;
    }
    
    private function getPropertyTypeCounts($query)
    {
        $commonPropertyTypes = [
            'Room','Lodge', 'Vacation Rental','Farm Stay','Aparment Hotel','Hotel', 'Resort', 'Apartment', 'Villa', 'Hostel',
            'Guest House', 'Motel', 'Bed and Breakfast'
        ];
        
        $counts = [];
        foreach ($commonPropertyTypes as $type) {
            $count = $query->clone()
                ->join('TPHotel_types as t', 'h.propertyType', '=', 't.hid')
                ->where('t.type', $type)
                ->count(DB::raw('DISTINCT h.hotelid'));
            $counts[$type] = $count;
        }
        
        return $counts;
    }
    
    private function getAgencyCounts($query)
    {
        return $query->clone()
            ->select('hotemp.agency_name', DB::raw('COUNT(DISTINCT h.hotelid) as count'))
            ->whereNotNull('hotemp.agency_name')
            ->groupBy('hotemp.agency_name')
            ->get()
            ->pluck('count', 'agency_name');
    }
  private function getGuestRatingCounts($query)
{
    $guestRatings = [1, 2, 3, 4, 5, 6, 7, 8, 9, 9.5, 10];
    $counts = [];
    
    foreach ($guestRatings as $rating) {
        if ($rating == 9) {
            $count = $query->clone()
                ->whereBetween('h.rating', [9, 9.5])
                ->count(DB::raw('DISTINCT h.hotelid'));
        }
        elseif ($rating == 9.5) {
            $count = $query->clone()
                ->whereBetween('h.rating', [9.5, 10])
                ->count(DB::raw('DISTINCT h.hotelid'));
        }
        elseif ($rating == 10) {
            $count = $query->clone()
                ->where('h.rating', 10)
                ->count(DB::raw('DISTINCT h.hotelid'));
        }
        else {
            $count = $query->clone()
                ->whereBetween('h.rating', [$rating, $rating + 1])
                ->count(DB::raw('DISTINCT h.hotelid'));
        }
        
        $counts[(string)$rating] = $count;
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
            
            $clonedQuery->where(function($q) use ($place) {
                // Fallback to simpler distance calculation if ST_Distance_Sphere not available
                $q->whereRaw('
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
                )
                ->whereNotNull('h.Latitude')
                ->whereNotNull('h.longnitude')
                ->whereRaw('TRIM(h.Latitude) != ""')
                ->whereRaw('TRIM(h.longnitude) != ""');
            });       
      
            $count = $clonedQuery->count(DB::raw('DISTINCT h.hotelid'));               
            
            $counts[$place->SightId] = $count;
        }
        
        return $counts;
    }
}
