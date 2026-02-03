<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HotelService
{
    /**
     * Cache duration constants
     */
    const CACHE_SHORT = 300; // 5 minutes
    const CACHE_MEDIUM = 3600; // 1 hour
    const CACHE_LONG = 86400; // 1 day
    const CACHE_VERY_LONG = 604800; // 1 week

    /**
     * Get location information with caching
     *
     * @param string $id Location slug ID
     * @return object|null Location information
     */
    public function getLocationInfo($id)
    {
        return Cache::remember("location_info_{$id}", self::CACHE_LONG, function() use ($id) {
            return DB::table('Location')
                ->select('LocationId', 'LocationLevel', 'ParentId', 'Slug', 'slugid', 
                        'heading', 'headingcontent', 'show_in_index', 'Name', 
                        'HotelTitleTag', 'HotelMetaDescription', 'MetaTagTitle', 'MetaTagDescription')
                ->where('slugid', $id)
                ->first();
        });
    }

    /**
     * Get nearby places with caching
     *
     * @param string $id Location ID
     * @return \Illuminate\Support\Collection Nearby places
     */
    public function getNearbyPlaces($id)
    {
        return Cache::remember("nearby_places_{$id}", self::CACHE_LONG, function() use ($id) {
            return DB::table('Sight')
                ->select('SightId', 'Title', 'LocationId', 'Latitude', 'Longitude')
                ->where('Location_id', $id)
                ->orderBy('Title')
                ->get();
        });
    }

    /**
     * Get hotel types with caching
     *
     * @return \Illuminate\Support\Collection Hotel types
     */
    public function getHotelTypes()
    {
        return Cache::remember('hotel_types', self::CACHE_VERY_LONG, function() {
            return DB::table('TPHotel_types')
                ->select('hid', 'name', 'slug')
                ->orderBy('hid', 'desc')
                ->get();
        });
    }

    /**
     * Get breadcrumb data with caching
     *
     * @param string $desiredId Location slug ID
     * @return array Breadcrumb data
     */
    public function getBreadcrumbData($desiredId)
    {
        $breadcrumbCacheKey = "breadcrumb_data_{$desiredId}";
        return Cache::remember($breadcrumbCacheKey, self::CACHE_LONG, function() use ($desiredId) {
            $result = [
                'getloclink' => collect(),
                'getcontlink' => collect(),
                'locationPatent' => [],
                'getlocationexp' => collect(),
                'lslug' => '',
                'lslugid' => ''
            ];
            
            // Get location info
            $locInfo = DB::table('Location as l')         
                ->select('l.LocationLevel', 'l.ParentId', 'l.LocationId', 'l.Slug', 'l.slugid', 'l.heading', 'l.Name', 'l.CountryId')
                ->where('l.slugid', $desiredId)
                ->first();
            
            if ($locInfo) {
                $result['getloclink'] = collect([$locInfo]);
                $result['lslug'] = $locInfo->Slug;
                $result['lslugid'] = $locInfo->slugid;
                
                // Get country info
                $result['getcontlink'] = DB::table('Country as co')
                    ->join('Location as l', 'l.CountryId', '=', 'co.CountryId')
                    ->join('CountryCollaboration as cont', 'cont.CountryCollaborationId', '=', 'co.CountryCollaborationId')
                    ->select('co.CountryId', 'co.Name', 'co.slug', 'cont.Name as cName', 'cont.CountryCollaborationId as contid')
                    ->where('l.LocationId', $locInfo->LocationId)
                    ->get();
                
                // Get location details
                $result['getlocationexp'] = DB::table('Location')
                    ->select('slugid', 'LocationId', 'Name', 'Slug')
                    ->where('LocationId', $locInfo->LocationId)
                    ->get();
                
                // Build location parent hierarchy
                if ($locInfo->LocationLevel != 1) {
                    $loopcount = $locInfo->LocationLevel;
                    $lociID = $locInfo->ParentId;
                    
                    for ($i = 1; $i < $loopcount; $i++) {
                        $getparents = DB::table('Location')
                            ->select('slugid', 'LocationId', 'Name', 'Slug', 'ParentId')
                            ->where('LocationId', $lociID)
                            ->first();
                            
                        if ($getparents) {
                            $result['locationPatent'][] = [
                                'LocationId' => $getparents->slugid,
                                'slug' => $getparents->Slug,
                                'Name' => $getparents->Name,
                            ];
                            
                            if ($getparents->ParentId != "") {
                                $lociID = $getparents->ParentId;
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
            
            return $result;
        });
    }

    /**
     * Get nearby hotels with swimming pool
     *
     * @param string $slgid Location slug ID
     * @return \Illuminate\Support\Collection Nearby hotels with swimming pool
     */
    public function getNearbyHotelsWithSwimmingPool($slgid)
    {
        return Cache::remember("nearby_hotels_{$slgid}", self::CACHE_LONG, function() use ($slgid) {
            return DB::table('TPHotel as h')
                ->select('h.name', 'h.location_id', 'h.id', 'h.hotelid', 'h.slugid', 'h.slug', 
                         'h.OverviewShortDesc', 'h.rating', 'h.pricefrom', 'l.Name as Lname')
                ->leftJoin('Location as l', 'l.slugid', '=', 'h.slugid')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('TPHotel_amenities as ha')
                        ->whereRaw('FIND_IN_SET(ha.id, h.facilities) > 0')
                        ->where('ha.name', 'Swimming pool');
                })
                ->where('h.slugid', $slgid)
                ->whereNotNull('h.OverviewShortDesc')
                ->orderBy('h.stars', 'desc')
                ->limit(4)
                ->get();
        });
    }

    /**
     * Get hotel search results with filters
     *
     * @param string $slgid Location slug ID
     * @param string $st Star rating filter
     * @param string $amenity Amenity filter
     * @param string $price Price filter
     * @param string $reviewscore Review score filter
     * @param array $amenity_ids Amenity IDs filter
     * @param array $neighborhood_ids Neighborhood IDs filter
     * @param array $sights Sights filter
     * @param array $neighborhoods Neighborhoods filter
     * @return \Illuminate\Support\Collection Hotel search results
     */
    public function getHotelSearchResults($slgid, $st, $amenity, $price, $reviewscore, $amenity_ids, $neighborhood_ids, $sights, $neighborhoods)
    {
        // Create a more specific cache key that includes all filter parameters
        $cacheKey = "search_results_" . md5(
            $slgid . '_' . 
            $st . '_' . 
            $amenity . '_' . 
            $price . '_' . 
            $reviewscore . '_' . 
            implode('_', $amenity_ids ?? []) . '_' . 
            implode('_', $neighborhood_ids ?? [])
        );
        
        // Extend cache duration from 300 to 1800 seconds (30 minutes) for better performance
        return Cache::remember($cacheKey, 1800, function() use ($slgid, $st, $amenity, $price, $reviewscore, $amenity_ids, $neighborhood_ids, $sights, $neighborhoods) {
            $query = DB::table('TPHotel as h')
                ->select([
                    'h.hotelid', 
                    'h.id', 
                    'h.name', 
                    'h.slug', 
                    'h.stars', 
                    'h.pricefrom', 
                    'h.rating', 
                    'h.amenities', 
                    'h.distance', 
                    'h.image',         
                    'h.about', 
                    'h.room_aminities', 
                    'h.shortFacilities', 
                    'h.slugid', 
                    'h.CityName',
                    'h.short_description',
                    'h.ReviewSummary',
                    'h.Latitude',
                    'h.longnitude',
                    'h.OverviewShortDesc',
                    DB::raw('GROUP_CONCAT(
                        DISTINCT CONCAT(a.shortName, "|", a.image) 
                        ORDER BY a.name 
                        SEPARATOR ", "
                    ) as amenity_info')
                ])
                ->leftJoin('TPHotel_amenities as a', function($join) {
                    $join->whereRaw('FIND_IN_SET(a.id, h.facilities)');
                });

                // Apply base conditions only once
                $query->where('h.slugid', $slgid)
                    ->whereNotNull('h.slugid');

                // Add conditional join for specific amenities - moved up to avoid multiple joins
                if ($amenity == 'free cancellation' || $amenity == 'breakfast') {
                    $query->leftJoin('TPRoomtype_tmp as rt', 'h.hotelid', '=', 'rt.hotelid');
                }

                if (!empty($sights)) {
                    $query->where(function($query) use ($sights) {
                        foreach ($sights as $sight) {
                            $query->orWhere(function($q) use ($sight) {
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
                                        (float)$sight->Latitude,
                                        (float)$sight->Longitude,
                                        (float)$sight->Latitude,
                                        3 // 3 km radius
                                    ]
                                )
                                ->where('h.LocationId', $sight->LocationId)
                                ->whereNotNull('h.Latitude')
                                ->whereNotNull('h.longnitude')
                                ->whereRaw('TRIM(h.Latitude) != ""')
                                ->whereRaw('TRIM(h.longnitude) != ""');
                            });
                        }
                    });
                }

                if (!empty($amenity_ids)) {
                    $query->where(function($query) use ($amenity_ids) {
                        foreach ($amenity_ids as $amenity_id) {
                            $query->whereRaw("FIND_IN_SET(?, h.facilities)", [$amenity_id]);
                        }
                    });
                }

                if (!empty($neighborhoods)) {
                    $query->where(function($query) use ($neighborhoods) {
                        foreach ($neighborhoods as $neighborhood) {
                            $query->orWhere(function($q) use ($neighborhood) {
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
                                        (float)$neighborhood->Latitude,
                                        (float)$neighborhood->Longitude,
                                        (float)$neighborhood->Latitude,
                                        2 // 2 km radius
                                    ]
                                )
                                ->where('h.LocationId', $neighborhood->LocationId)
                                ->whereNotNull('h.Latitude')
                                ->whereNotNull('h.longnitude')
                                ->whereRaw('TRIM(h.Latitude) != ""')
                                ->whereRaw('TRIM(h.longnitude) != ""');
                            });
                        }
                    });
                }

                // Apply star rating filter
                if (!empty($st)) {
                    $query->where('h.stars', $st);
                }

                // Apply amenity filters
                if (!empty($amenity)) {
                    if ($amenity == 'parking' || $amenity == 'wifi') {
                        $query->whereExists(function($subquery) use ($amenity) {
                            $subquery->select(DB::raw(1))
                                ->from('TPHotel_amenities as a2')
                                ->whereRaw('FIND_IN_SET(a2.id, h.facilities)')
                                ->where(function($q) use ($amenity) {
                                    $q->where('a2.name', 'LIKE', "%{$amenity}%")
                                    ->orWhere('a2.name', 'LIKE', "% {$amenity}%")
                                    ->orWhere('a2.name', 'LIKE', "%{$amenity} %");
                                });
                        });
                    } elseif ($amenity == 'free cancellation') {
                        $query->where('rt.refundable', 1);
                    } elseif ($amenity == 'breakfast') {
                        $query->where('rt.breakfast', 1);
                    }
                }

                // Apply review score filter
                if (!empty($reviewscore)) {
                    $query->whereNotNull('h.rating')
                        ->where('h.rating', '>=', $reviewscore);
                }

                // Apply price filter
                if (!empty($price)) {
                    $query->where('h.pricefrom', '<=', (int)trim($price));
                }

                return $query->orderBy('h.stars', 'desc')  // Add this line for default star sorting
                ->orderBy(DB::raw('h.short_description IS NULL'), 'asc')
                ->groupBy([
                    'h.hotelid', 
                    'h.id', 
                    'h.name', 
                    'h.slug',
                    'h.stars',
                    'h.pricefrom', 
                    'h.rating', 
                    'h.amenities', 
                    'h.distance',
                    'h.image', 
                    'h.about', 
                    'h.room_aminities',
                    'h.shortFacilities',
                    'h.slugid', 
                    'h.CityName',
                    'h.short_description',
                    'h.ReviewSummary',
                    'h.Latitude',
                    'h.longnitude',
                    'h.OverviewShortDesc'
                ])
                ->limit(20)
                ->get();
        });
    }

    /**
     * Get hotel search results with date filters
     *
     * @param string $searchId Search ID
     * @param array $idArray Hotel IDs
     * @return \Illuminate\Support\Collection Hotel search results
     */
    public function getHotelSearchResultsWithDates($idArray)
    {
        $cacheKey = "hotel_search_" . md5(implode('_', $idArray));

        return Cache::remember($cacheKey, 300, function() use ($idArray) {
            return DB::table('TPHotel as h')
                ->select([
                    'h.hotelid',
                    'h.id', 
                    'h.name', 
                    'h.slug',
                    'h.stars',
                    'h.rating', 
                    'h.amenities', 
                    'h.distance',
                    'h.slugid',
                    'h.room_aminities',
                    'h.CityName',
                    'h.short_description',
                    'h.Latitude',
                    'h.longnitude',
                    'h.MicroSummary',
                    DB::raw('GROUP_CONCAT(
                        DISTINCT CONCAT(a.shortName, "|", COALESCE(a.image, "")) 
                        ORDER BY a.name 
                        SEPARATOR ", "
                    ) as amenity_info')
                ])
                ->leftJoin('TPHotel_amenities as a', function($join) {
                    $join->whereRaw('FIND_IN_SET(a.id, h.shortFacilities) > 0');
                })
                ->whereIn('h.hotelid', $idArray)
                ->whereNotNull('h.slugid')
                ->groupBy([
                    'h.hotelid',
                    'h.id', 
                    'h.name', 
                    'h.slug',
                    'h.stars',
                    'h.rating', 
                    'h.amenities', 
                    'h.distance',
                    'h.slugid',
                    'h.room_aminities',
                    'h.CityName',
                    'h.short_description',
                    'h.Latitude',
                    'h.longnitude',
                    'h.MicroSummary'
                ])
                ->orderBy('h.stars', 'desc')
                ->get();
        });
    }

    /**
     * Get amenity information
     *
     * @param array $amenity_ids Amenity IDs
     * @return \Illuminate\Support\Collection Amenity information
     */
    public function getAmenityInfo($amenity_ids)
    {
        $cacheKey = "amenity_info_" . md5(implode('_', $amenity_ids));
        
        return Cache::remember($cacheKey, self::CACHE_LONG, function() use ($amenity_ids) {
            return DB::table('TPHotel_amenities')
                ->select('id', 'name', 'slug', 'image')
                ->whereIn('id', $amenity_ids)
                ->get();
        });
    }

    /**
     * Get neighborhood information
     *
     * @param array $neighborhood_ids Neighborhood IDs
     * @return \Illuminate\Support\Collection Neighborhood information
     */
    public function getNeighborhoodInfo($neighborhood_ids)
    {
        if (empty($neighborhood_ids)) {
            return collect();
        }
        
        $cacheKey = "neighborhood_info_" . md5(implode('_', $neighborhood_ids));
        
        return Cache::remember($cacheKey, self::CACHE_LONG, function() use ($neighborhood_ids) {
            return DB::table('Neighborhood')
                ->select('NeighborhoodId', 'Name', 'slug', 'Latitude', 'Longitude', 'LocationID')
                ->whereIn('NeighborhoodId', $neighborhood_ids)
                ->get();
        });
    }

    /**
     * Get sight information
     *
     * @param array $sight_ids Sight IDs
     * @return \Illuminate\Support\Collection Sight information
     */
    public function getSightInfo($sight_ids)
    {
        if (empty($sight_ids)) {
            return collect();
        }
        
        $cacheKey = "sight_info_" . md5(implode('_', $sight_ids));
        
        return Cache::remember($cacheKey, self::CACHE_LONG, function() use ($sight_ids) {
            return DB::table('Sight')
                ->select('SightId', 'Title', 'LocationId', 'Latitude', 'Longitude')
                ->whereIn('SightId', $sight_ids)
                ->get();
        });
    }

    /**
     * Make API call to hotellook.com
     *
     * @param array $params API parameters
     * @return object|null API response
     */
    public function makeHotellookApiCall($params)
    {
        $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869"; 
        $TRAVEL_PAYOUT_MARKER = "299178";
        
        $SignatureString = "" . $TRAVEL_PAYOUT_TOKEN . ":" . $TRAVEL_PAYOUT_MARKER . ":" . $params['adultsCount'] . ":" . 
            $params['checkinDate'] . ":" . 
            $params['checkoutDate'] . ":" .
            $params['chid_age'] . ":" . 
            $params['childrenCount'] . ":" . 
            $params['iata'] . ":" .  
            $params['currency'] . ":" . 
            $params['customerIP'] . ":" .             
            $params['lang'] . ":" . 
            $params['waitForResult']; 
            
        $signature = md5($SignatureString);

        $url = 'http://engine.hotellook.com/api/v2/search/start.json?cityId=' . $params['iata'] . 
            '&checkIn=' . $params['checkinDate'] . 
            '&checkOut=' . $params['checkoutDate'] . 
            '&adultsCount=' . $params['adultsCount'] . 
            '&customerIP=' . $params['customerIP'] . 
            '&childrenCount=' . $params['childrenCount'] . 
            '&childAge1=' . $params['chid_age'] . 
            '&lang=' . $params['lang'] . 
            '&currency=' . $params['currency'] . 
            '&waitForResult=' . $params['waitForResult'] . 
            '&marker=299178&signature=' . $signature;       

        try {
            $response = Http::withoutVerifying()->timeout(10)->get($url);
            
            if ($response->successful()) {
                return json_decode($response);
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error('Hotellook API error: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get hotel search results from hotellook.com
     *
     * @param string $searchId Search ID
     * @return object|null API response
     */
    public function getHotellookSearchResults($searchId)
    {
        $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869"; 
        $TRAVEL_PAYOUT_MARKER = "299178";
        
        $limit = 40;
        $offset = 0;
        $roomsCount = 0;
        $sortAsc = 0;
        $sortBy = 'stars';
        
        $SignatureString2 = "" . $TRAVEL_PAYOUT_TOKEN . ":" . $TRAVEL_PAYOUT_MARKER . ":" . $limit . ":" . $offset . ":" . $roomsCount . ":" . $searchId . ":" . $sortAsc . ":" . $sortBy;
        $sig2 = md5($SignatureString2); 
        
        $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId=' . $searchId . '&limit=40&sortBy=stars&sortAsc=0&roomsCount=0&offset=0&marker=299178&signature=' . $sig2;
        
        $maxAttempts = 6; 
        $retryInterval = 2;
        $status = 0; // Default status

        try {
            // Make the HTTP request with retries
            $response2 = Http::withoutVerifying()
                ->timeout(0)
                ->retry($maxAttempts, $retryInterval)
                ->get($url2);
        
            $responseData = $response2->json();
        
            if (isset($responseData['errorCode']) && $responseData['errorCode'] === 4) {
                $status = 4; 
            } else {
                $status = 1; 
            }
        
            // If the status indicates a successful search
            if ($status == 1 && $response2->successful()) {
                return json_decode($response2);
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error('Hotellook search results API error: ' . $e->getMessage());
        }
        
        return null;
    }
}
