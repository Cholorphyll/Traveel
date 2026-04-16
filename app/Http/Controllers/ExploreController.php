<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Hotel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\ItineraryGenerator;
use App\Services\TouristFeedEngine;
use App\Services\AlgoFeed\AlgoFeedEngine;
use Illuminate\Support\Facades\Log;

class ExploreController extends Controller
{

    private $itineraryGenerator;

    public function __construct(ItineraryGenerator $itineraryGenerator)
    {
        $this->itineraryGenerator = $itineraryGenerator;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; 

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; 
    }

    public function createLocationObjects($data, $type)
    {
        $locations = [];
        foreach ($data as $item) {
            $popularityIndex = $item->PopularityIndex ?? null;
            $isMustSee = $item->MustSee ?? null;
            $title = $item->Title ?? null;
            $latitude = $item->Latitude ?? null;
            $longitude = $item->Longitude ?? null;
            $reviewCount = $item->ReviewCount ?? 0;
            $averageRating = $item->Averagerating ?? 0;

            $slugid = $item->slugid ?? null;
            $Slug = $item->Slug ?? null;
            $Averagerating = $item->Averagerating ?? null;
            $cuisines = $item->cuisines ?? null;
            $Cost = $item->Cost ?? null;
            $Img1 = $item->Img1 ?? null;

            if (!$title || !$latitude || !$longitude) {
                continue; 
            }

            $locations[] = [
                'id' => $item->SightId ?? $item->Id ?? null,
                'SightId' => $item->SightId ?? $item->Id ?? null,
                'Title' => $title,
                'Latitude' => (float)$latitude,
                'Longitude' => (float)$longitude,
                'type' => $type,
                'MustSee' => $isMustSee == 1 ? 1 : 0,
                'ReviewCount' => (int)$reviewCount,
                'Averagerating' => (float)$averageRating,
                'popularity_score' => (float)$popularityIndex,
                'slugid' => $slugid,
                'Slug' => $Slug,
                'cuisines' => $cuisines,
                'Cost' => $Cost,
                'Img1' => $Img1,
                'LName' => $item->LName ?? '',
                'LocationId' => $item->LocationId ?? null,
                'CategoryTitle' => $item->CategoryTitle ?? null,
                'CategoryId' => $item->CategoryId ?? null,
                'Address' => $item->Address ?? null,
                'IsRestaurant' => $item->IsRestaurant ?? null,
                'TAAggregateRating' => $item->TAAggregateRating ?? null,
                'TATotalReviews' => $item->TATotalReviews ?? null,
                'ticket' => $item->ticket ?? null,
                'MicroSummary' => $item->MicroSummary ?? null
            ];
        }
        return $locations;
    }

    public function createMainAttractions($data)
    {
        if (empty($data)) {
            return [];
        }

        $mainAttractions = [];

        $data = $data instanceof \Illuminate\Support\Collection ? $data->toArray() : $data;

        foreach ($data as $item) {
            $item = is_object($item) ? (array)$item : $item;

            $attraction = [
                'SightId' => $item['SightId'] ?? $item->SightId ?? null,
                'id' => $item['SightId'] ?? $item->SightId ?? null,
                'MustSee' => $item['MustSee'] ?? $item->MustSee ?? 0,
                'Title' => $item['Title'] ?? $item->Title ?? '',
                'Latitude' => (float)($item['Latitude'] ?? $item->Latitude ?? 0),
                'Longitude' => (float)($item['Longitude'] ?? $item->Longitude ?? 0),
                'type' => 'attraction',
                'ReviewCount' => (int)($item['ReviewCount'] ?? $item->ReviewCount ?? 0),
                'Averagerating' => (float)($item['Averagerating'] ?? $item->Averagerating ?? 0),
                'LocationId' => (int)($item['LocationId'] ?? $item->LocationId ?? 0),
                'Slug' => $item['Slug'] ?? $item->Slug ?? '',
                'About' => $item['About'] ?? $item->About ?? '',
                'IsRestaurant' => $item['IsRestaurant'] ?? $item->IsRestaurant ?? 0,
                'Address' => $item['Address'] ?? $item->Address ?? '',
                'CategoryId' => $item['CategoryId'] ?? $item->CategoryId ?? 0,
                'CategoryTitle' => $item['CategoryTitle'] ?? $item->CategoryTitle ?? '',
                'CountryId' => $item['CountryId'] ?? $item->CountryId ?? 0,
                'LName' => $item['LName'] ?? $item->LName ?? '',
                'mTitle' => $item['mTitle'] ?? $item->mTitle ?? '',
                'Lslug' => $item['Lslug'] ?? $item->Lslug ?? '',
                'slugid' => $item['slugid'] ?? $item->slugid ?? null,
                'mDesc' => $item['mDesc'] ?? $item->mDesc ?? '',
                'CountryName' => $item['CountryName'] ?? $item->CountryName ?? '',
                'ticket' => $item['ticket'] ?? $item->ticket ?? null,
                'MicroSummary' => $item['MicroSummary'] ?? $item->MicroSummary ?? '',
                'TAAggregateRating' => $item['TAAggregateRating'] ?? $item->TAAggregateRating ?? null,
                'TATotalReviews' => $item['TATotalReviews'] ?? $item->TATotalReviews ?? null,
                'short_description' => $item['short_description'] ?? $item->short_description ?? ''
            ];

            $mainAttractions[] = $attraction;
        }

        return $mainAttractions;
    }

    private function processAttractionsForView($attractions, $sightImages = null)
    {
        if ($sightImages === null) {
            $sightImages = collect();
        }

        $restaurantItems = [];
        $experienceItems = [];
        $restaurantIds = [];
        $experienceIds = [];
        
        foreach ($attractions as $item) {
            $type = 'attraction';
            $originalId = $item->SightId;

            if (isset($item->SightId)) {
                if (strpos($item->SightId, 'rest_') === 0) {
                    $type = 'restaurant';
                    $originalId = str_replace('rest_', '', $item->SightId);
                    $restaurantItems[$originalId] = $item;
                    $restaurantIds[] = $originalId;
                } elseif (strpos($item->SightId, 'exp_') === 0) {
                    $type = 'experience';
                    $originalId = str_replace('exp_', '', $item->SightId);
                    $experienceItems[$originalId] = $item;
                    $experienceIds[] = $originalId;
                }
            }
            
            $item->type = $type;
            
            $item->Sightcat = collect();
            $item->timing = [];
            $item->images = []; 
        }
        
        if (!empty($restaurantIds)) {
            try {
                $allRestaurantCategories = DB::table('RestaurantCategory')
                    ->join('Category', 'RestaurantCategory.CategoryId', '=', 'Category.CategoryId')
                    ->select('RestaurantCategory.RestaurantId', 'Category.Title')
                    ->whereIn('RestaurantCategory.RestaurantId', $restaurantIds)
                    ->get()
                    ->groupBy('RestaurantId');
                    
                $allRestaurantImages = DB::table('Restaurant_image')
                    ->whereIn('RestaurantId', $restaurantIds)
                    ->get()
                    ->groupBy('RestaurantId');
                    
                foreach ($restaurantIds as $restaurantId) {
                    $item = $restaurantItems[$restaurantId];
                    
                    if (isset($allRestaurantCategories[$restaurantId])) {
                        $item->Sightcat = $allRestaurantCategories[$restaurantId];
                    }
                    
                    if (isset($allRestaurantImages[$restaurantId])) {
                        $item->images = []; 
                        foreach ($allRestaurantImages[$restaurantId] as $image) {
                            $sightImageObj = (object)[
                                'Sightid' => $item->SightId,
                                'Image' => $image->Image
                            ];
                            $sightImages->push($sightImageObj);
                            
                            if (!empty($image->Image)) {
                                $item->images[] = $image->Image;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }
        
        foreach ($experienceItems as $experienceId => $item) {
            $item->images = []; 
            
            if (!empty($item->Img1)) {
                $sightImages->push((object)[
                    'Sightid' => $item->SightId,
                    'Image' => $item->Img1
                ]);
                $item->images[] = $item->Img1;
            }
            if (!empty($item->Img2)) {
                $sightImages->push((object)[
                    'Sightid' => $item->SightId,
                    'Image' => $item->Img2
                ]);
                $item->images[] = $item->Img2;
            }
            if (!empty($item->Img3)) {
                $sightImages->push((object)[
                    'Sightid' => $item->SightId,
                    'Image' => $item->Img3
                ]);
                $item->images[] = $item->Img3;
            }
        }
        
        if (!$sightImages->isEmpty()) {
            $groupedImages = $sightImages->groupBy('Sightid');
            
            foreach ($attractions as $item) {
                if (isset($item->SightId) && $item->type === 'attraction' && isset($groupedImages[$item->SightId])) {
                    $item->images = [];
                    foreach ($groupedImages[$item->SightId] as $image) {
                        if (!empty($image->Image)) {
                            $item->images[] = $image->Image;
                        }
                    }
                }
                
                if (empty($item->images) && $item->type === 'attraction') {
                    if (!empty($item->Image)) {
                        $item->images[] = $item->Image;
                    }
                }
            }
        }

        return $attractions;
    }

    public function singleLocation(Request $request, $segment, $category = null) 
    {
        $mustSeeLimit = 10;

        $id = null;
        $slug = null;
        $category_slugs = null;
        $focusSightId = null;
        $nearbyAttractionLandingTitle = null;
        $redirect_needed = false;
        
        $parts = explode('-', $segment);
        
        if (!empty($parts)) {
            $id = array_shift($parts);
            $city_parts = [];
            $filter_parts = [];
            $processed_categories = [];
            $current_part_type = null;

            foreach ($parts as $part) {

                if (isset($processed_categories[$part])) {
                    continue;
                }

                if ($part === 'xpat') {
                    $filter_parts[] = $part;
                    $processed_categories[$part] = true;
                    if ($category_slugs === null) {
                        $category_slugs = $part;
                    } else {
                        $category_slugs .= '-' . $part;
                    }
                    $current_part_type = 'attractions_filter';
                }
                elseif (preg_match('/^xp(\d+)$/', $part, $matches)) {
                    $category_id = $matches[1]; 
                    
                    if (isset($processed_categories[$part])) {
                        continue;
                    }

                    $category_info = DB::table('Category')
                        ->select('CategoryId', 'Title', 'Slug')
                        ->where('CategoryId', $category_id)
                        ->first();

                    if (!$category_info) {
                        abort(404);
                    }

                    $filter_parts[] = $part;
                    $processed_categories[$part] = true;
                    if ($category_slugs === null) {
                        $category_slugs = $part;
                    } else {
                        $category_slugs .= '-' . $part;
                    }
                    $current_part_type = 'category';
                }
                elseif (preg_match('/^sqx(\d+)$/', $part, $matches)) {
                    $focusSightId = $matches[1];
                    $filter_parts[] = $part;
                    $processed_categories[$part] = true;
                    $current_part_type = 'sight';
                }
                elseif (preg_match('/^[a-zA-Z\-]+$/', $part) && ($current_part_type == 'category' || $current_part_type == 'sight')) {
                    continue;
                }
                else {
                    $city_parts[] = $part;
                    $current_part_type = 'city';
                }
            }

            if (!empty($city_parts)) {
                $slug = implode('-', $city_parts);
            }
            if ($redirect_needed) {
                $new_url_parts = ['lo'];
                
                if ($id) {
                    $new_url_parts[] = $id;
                }
                
                if (!empty($city_parts)) {
                if (!empty($city_parts)) {
                    $new_url_parts[] = implode('-', $city_parts);
                }
                
                if (!empty($filter_parts)) {
                    $new_url_parts = array_merge($new_url_parts, array_unique($filter_parts));
                }
                
                $new_url = implode('-', $new_url_parts);
                
                if ($new_url !== 'lo-' . $segment) {
                    return redirect($new_url);
                }
            }
        }

        $location_name = "";

        $location = DB::table('Location')
            ->select('Name', 'LocationId', 'About', 'MetaTagTitle as mTitle',
                    'MetaTagDescription as mDesc', 'tp_location_mapping_id',
                    'Longitude as loc_longitude', 'Lat as loc_latitude', 'Slug', 'slugid')
            ->where('slugid', $id)
            ->first();

        if ($location) {
            $correctSlug = $location->Slug;
            
            $baseCorrectUrl = 'lo-' . $id . '-' . $correctSlug;
            
            $currentUrl = 'lo-' . $segment;
            if (strpos($currentUrl, $baseCorrectUrl) === false) {
                
                
                $allParts = explode('-', $segment);
                array_shift($allParts); 
                
                $filterParts = [];
                $locationSlugParts = [];
                
                foreach ($allParts as $part) {
                    if (preg_match('/^(xp\d+|sqx\d+)/', $part)) {
                        $filterParts[] = $part;
                    } else {
                        $locationSlugParts[] = $part;
                    }
                }
                
                $correctUrl = $baseCorrectUrl;
                
                if (!empty($filterParts)) {
                    $correctUrl .= '-' . implode('-', $filterParts);
                }
                
                return redirect($correctUrl);
            }
        }
        $getloccheck = DB::table('Location')
            ->select('Name', 'LocationId', 'About', 'MetaTagTitle as mTitle',
                    'MetaTagDescription as mDesc', 'tp_location_mapping_id',
                    'Longitude as loc_longitude', 'Lat as loc_latitude')
            ->where('Slug', $slug)
            ->where('slugid', $id)
            ->get();

        if($getloccheck->isEmpty()) {
            if ($id != null) {
                $checkgetloc = DB::table('Location')
                    ->select('slugid')
                    ->where('LocationId', $id)
                    ->get();
                if(!$checkgetloc->isEmpty()) {
                    $id = $checkgetloc[0]->slugid;
                    return redirect()->route('search.results', [$id.'-'.$slug]);
                }
            }
            abort(404, 'NOT FOUND');
        }

        if($request->get('category') != "") {
            $oldcat = str_replace('ct', '', $request->get('category'));
            $redirecturl = DB::table('Category')
                ->select('Title')
                ->where('CategoryId', $oldcat)
                ->get();
            if(!$redirecturl->isEmpty()) {
                $cattitle = str_replace(' ', '-', $redirecturl[0]->Title);
                return redirect()->route('search.results', [
                    'id' => $id . '-' . $slug,
                    'category' => $cattitle,
                ]);
            }
        }

        $location_name = $getloccheck[0]->Name;
        $locationID = $getloccheck[0]->LocationId;
        $lociID = $locationID;
        $locn = $getloccheck[0]->Name;

        $focusSight = null;
        $nearbyAttractionLanding = false;
        if (!empty($focusSightId)) {
            $focusSight = DB::table('Sight')
                ->select('SightId', 'Title', 'Latitude', 'Longitude', 'LocationId', 'tier', 'IsRestaurant')
                ->where('SightId', $focusSightId)
                ->first();

            if (!$focusSight) {
                abort(404);
            }

            if ((int)($focusSight->IsRestaurant ?? 0) !== 0) {
                abort(404);
            }

            if ((int)$focusSight->LocationId !== (int)$locationID) {
                abort(404);
            }

            if (!is_numeric($focusSight->Latitude) || !is_numeric($focusSight->Longitude)) {
                abort(404);
            }

            $nearbyAttractionLanding = true;
            $nearbyAttractionLandingTitle = 'Attractions near ' . ($focusSight->Title ?? '');
        }

        $category_ids = [];
        $category_info = collect();
        
        if ($category_slugs) {
            $category_parts = explode('-', $category_slugs);
            
            $categoryCacheKey = "category_data_" . md5(implode('_', $category_parts));
            
            $categoryData = Cache::remember($categoryCacheKey, 86400, function() use ($category_parts) {
                $ids = [];
                $categories = collect();
                
                foreach ($category_parts as $part) {
                    if (preg_match('/^xp(\d+)$/', $part, $matches)) {
                        $category_id = $matches[1];
                        
                        $category = DB::table('Category')
                            ->select('CategoryId', 'Title', 'Slug')
                            ->where('CategoryId', $category_id)
                            ->first();
                            
                        if ($category) {
                            $ids[] = $category_id;
                            $categories->push($category);
                        }
                    }
                }
                
                return [
                    'ids' => $ids,
                    'categories' => $categories
                ];
            });
            
            $category_ids = $categoryData['ids'];
            $category_info = $categoryData['categories'];
            
        }

        $catheading = "";
        $catid = null;
        $category_name = null;

        if ($nearbyAttractionLanding && !empty($nearbyAttractionLandingTitle)) {
            $catheading = $nearbyAttractionLandingTitle;
        }

        if (!$nearbyAttractionLanding && $category != "") {
            $category = str_replace('-', ' ', $category);
            $catheading = $category;
            $getcatid = DB::table('Category')
                ->select('CategoryId')
                ->where('Title', $category)
                ->get();
            if(!$getcatid->isEmpty()) {
                $catid = $getcatid[0]->CategoryId;
            }
        }

        if (!empty($category_info) && $category_info->count() > 0) {
            $firstCategory = $category_info->first();
            $category_name = $firstCategory->Title;
            $catheading = $firstCategory->Title;

        }

        $catid = str_replace('ct', '', $catid);
        $lid = $request->session()->get('locId');

        if($lid != $locationID) {
            foreach (request()->session()->all() as $key => $value) {
                if (str_starts_with($key, 'cat_') || str_starts_with($key, 'catid_')) {
                    request()->session()->forget($key);
                }
            }
            $request->session()->forget('locId');
            $request->session()->forget('mustSee');
            $request->session()->forget('isrestaurant');
        }

        $top_attractions = 0;
        if($catid == 'mustsee') {
            $top_attractions = 1;
            $request->session()->put('locId', $locationID);
            $request->session()->put('mustSee', 1);

            if (!$request->session()->has('catid_' . $catid)) {
                $request->session()->put('catid_' . $catid, $catid);
            }
            if (!$request->session()->has('cat_' . $catid)) {
                $request->session()->put('cat_' . $catid, $catid);
            }
        } else {
            $request->session()->forget('catid_mustsee');
            $request->session()->forget('cat_mustsee');
            $request->session()->forget('locId');
            $request->session()->forget('mustSee');
        }

        $attractionsOnly = false;
        if ($category_slugs && strpos($category_slugs, 'xpat') !== false) {
            $attractionsOnly = true;
        }

        
        ini_set('max_execution_time', 600);

        $debugGeo = false;
        $debugGeoId = null;
        try {
            $debugGeo = ($request->query('debug_geo') == '1' || $request->boolean('debug_geo'));
            if ($debugGeo) {
                try {
                    $debugGeoId = bin2hex(random_bytes(16));
                } catch (\Exception $e) {
                    $debugGeoId = uniqid('geo_', true);
                }
                Log::info('GEO_DEBUG request_start', [
                    'geo_debug_id' => $debugGeoId,
                    'path' => $request->path(),
                    'full_url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'ua' => (string) $request->userAgent(),
                    'segment' => $segment,
                    'category' => $category,
                ]);
            }
        } catch (\Exception $e) {
            $debugGeo = false;
            $debugGeoId = null;
        }

        $userLat = null;
        $userLon = null;
        try {
            $cookieLat = $request->cookie('tr_user_lat');
            $cookieLng = $request->cookie('tr_user_lng');
            if ($cookieLat !== null && $cookieLng !== null) {
                $userLat = is_numeric($cookieLat) ? (float)$cookieLat : null;
                $userLon = is_numeric($cookieLng) ? (float)$cookieLng : null;
            }

            if ($debugGeo) {
                Log::info('GEO_DEBUG cookies', [
                    'geo_debug_id' => $debugGeoId,
                    'cookie_lat_raw' => $cookieLat,
                    'cookie_lng_raw' => $cookieLng,
                    'user_lat' => $userLat,
                    'user_lon' => $userLon,
                ]);
            }
        } catch (\Exception $e) {
            $userLat = null;
            $userLon = null;
            if ($debugGeo) {
                Log::warning('GEO_DEBUG cookie_parse_failed', [
                    'geo_debug_id' => $debugGeoId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $currentWeather = null;
        $feedContext = [];
        try {
            $now = Carbon::now();
            $hour = (int)$now->format('G');

            $timeBucket = 'midday';
            if ($hour >= 5 && $hour < 11) {
                $timeBucket = 'morning';
            } elseif ($hour >= 11 && $hour < 16) {
                $timeBucket = 'midday';
            } elseif ($hour >= 16 && $hour < 20) {
                $timeBucket = 'evening';
            } else {
                $timeBucket = 'night';
            }

            $isMealTime = (($hour >= 12 && $hour <= 14) || ($hour >= 19 && $hour <= 21));

            // Debug override for force_meal
            if ($debugGeo && ($request->query('force_meal') == '1' || $request->boolean('force_meal'))) {
                $isMealTime = true;
            }

            $mode = 'out_city';
            if ($userLat !== null && $userLon !== null && !empty($getloccheck) && isset($getloccheck[0]->loc_latitude) && isset($getloccheck[0]->loc_longitude)) {
                $locLat = is_numeric($getloccheck[0]->loc_latitude) ? (float)$getloccheck[0]->loc_latitude : null;
                $locLon = is_numeric($getloccheck[0]->loc_longitude) ? (float)$getloccheck[0]->loc_longitude : null;
                if ($locLat !== null && $locLon !== null) {
                    $distToCity = $this->calculateDistance($userLat, $userLon, $locLat, $locLon);
                    if (is_numeric($distToCity) && $distToCity <= 60) {
                        $mode = 'in_city';
                    }

                    if ($debugGeo) {
                        Log::info('GEO_DEBUG mode_distance', [
                            'geo_debug_id' => $debugGeoId,
                            'location_lat' => $locLat,
                            'location_lon' => $locLon,
                            'distance_km' => $distToCity,
                            'mode' => $mode,
                        ]);
                    }
                }
            }

            // Debug override for force_mode
            if ($debugGeo && ($request->query('force_mode') === 'in_city' || $request->query('force_mode') === '1')) {
                $mode = 'in_city';
            }

            // Live current weather via Open-Meteo (no API key)
            if ($userLat !== null && $userLon !== null) {
                $weatherCacheKey = 'open_meteo_current_' . md5(sprintf('%.4f,%.4f', $userLat, $userLon));
                $weatherCacheHit = Cache::has($weatherCacheKey);
                $weatherStartedAt = microtime(true);
                if ($debugGeo) {
                    Log::info('GEO_DEBUG weather_cache_check', [
                        'geo_debug_id' => $debugGeoId,
                        'cache_key' => $weatherCacheKey,
                        'cache_hit' => $weatherCacheHit,
                        'user_lat' => $userLat,
                        'user_lon' => $userLon,
                    ]);
                }

                $currentWeather = Cache::remember($weatherCacheKey, 900, function () use ($userLat, $userLon, $debugGeo, $debugGeoId, $weatherCacheKey) {
                    try {
                        $url = 'https://api.open-meteo.com/v1/forecast';
                        $reqStartedAt = microtime(true);
                        $res = Http::timeout(6)->get($url, [
                            'latitude' => $userLat,
                            'longitude' => $userLon,
                            'current' => 'temperature_2m,precipitation,weather_code,wind_speed_10m',
                            'timezone' => 'auto'
                        ]);

                        if ($debugGeo) {
                            Log::info('GEO_DEBUG weather_api_response', [
                                'geo_debug_id' => $debugGeoId,
                                'cache_key' => $weatherCacheKey,
                                'ok' => $res->ok(),
                                'status' => $res->status(),
                                'duration_ms' => (int) round((microtime(true) - $reqStartedAt) * 1000),
                            ]);
                        }

                        if (!$res->ok()) {
                            return null;
                        }
                        $json = $res->json();
                        if (!is_array($json) || !isset($json['current'])) {
                            return null;
                        }

                        $c = $json['current'];
                        $tempC = isset($c['temperature_2m']) && is_numeric($c['temperature_2m']) ? (float)$c['temperature_2m'] : null;
                        $tempF = $tempC !== null ? round(($tempC * 9/5) + 32) : null;
                        $precip = isset($c['precipitation']) && is_numeric($c['precipitation']) ? (float)$c['precipitation'] : null;
                        $code = isset($c['weather_code']) && is_numeric($c['weather_code']) ? (int)$c['weather_code'] : null;
                        $wind = isset($c['wind_speed_10m']) && is_numeric($c['wind_speed_10m']) ? (float)$c['wind_speed_10m'] : null;

                        $payload = [
                            'temp_c' => $tempC,
                            'temp_f' => $tempF,
                            'precip_mm' => $precip,
                            'weather_code' => $code,
                            'wind_kmh' => $wind,
                            'time' => $c['time'] ?? null,
                        ];

                        if ($debugGeo) {
                            Log::info('GEO_DEBUG weather_parsed', [
                                'geo_debug_id' => $debugGeoId,
                                'cache_key' => $weatherCacheKey,
                                'weather' => $payload,
                            ]);
                        }

                        return $payload;
                    } catch (\Exception $e) {
                        if ($debugGeo) {
                            Log::warning('GEO_DEBUG weather_api_exception', [
                                'geo_debug_id' => $debugGeoId,
                                'cache_key' => $weatherCacheKey,
                                'error' => $e->getMessage(),
                            ]);
                        }
                        return null;
                    }
                });

                if ($debugGeo) {
                    Log::info('GEO_DEBUG weather_done', [
                        'geo_debug_id' => $debugGeoId,
                        'cache_key' => $weatherCacheKey,
                        'duration_ms' => (int) round((microtime(true) - $weatherStartedAt) * 1000),
                        'weather_null' => $currentWeather === null,
                        'weather_time' => is_array($currentWeather) ? ($currentWeather['time'] ?? null) : null,
                        'temp_c' => is_array($currentWeather) ? ($currentWeather['temp_c'] ?? null) : null,
                        'precip_mm' => is_array($currentWeather) ? ($currentWeather['precip_mm'] ?? null) : null,
                    ]);
                }
            } else {
                if ($debugGeo) {
                    Log::info('GEO_DEBUG weather_skipped', [
                        'geo_debug_id' => $debugGeoId,
                        'reason' => 'missing_user_geo',
                        'user_lat' => $userLat,
                        'user_lon' => $userLon,
                    ]);
                }
            }

            $weatherLabel = 'clear';
            if (is_array($currentWeather)) {
                $tempC = $currentWeather['temp_c'] ?? null;
                $precip = $currentWeather['precip_mm'] ?? null;
                if ($precip !== null && $precip > 0.1) {
                    $weatherLabel = 'rain';
                } elseif ($tempC !== null && $tempC >= 35) {
                    $weatherLabel = 'hot';
                } else {
                    $weatherLabel = 'clear';
                }
            }

            $feedContext = [
                'mode' => $mode,
                'weather' => $weatherLabel,
                'time_bucket' => $timeBucket,
                'is_meal_time' => $isMealTime,
                'user_lat' => $userLat,
                'user_lon' => $userLon,
            ];

            if ($debugGeo) {
                Log::info('GEO_DEBUG feed_context', [
                    'geo_debug_id' => $debugGeoId,
                    'feed_context' => $feedContext,
                ]);
            }

        } catch (\Exception $e) {
            $currentWeather = null;
            $feedContext = [];
            if ($debugGeo) {
                Log::warning('GEO_DEBUG context_exception', [
                    'geo_debug_id' => $debugGeoId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($nearbyAttractionLanding) {

            $focusLat = (float)$focusSight->Latitude;
            $focusLon = (float)$focusSight->Longitude;

            $cacheKey = 'nearby_attractions_sqx_' . $locationID . '_' . $focusSight->SightId;
            $cachePayload = Cache::remember($cacheKey, 3600, function() use ($locationID, $focusSight, $focusLat, $focusLon) {
                $baseCandidates = DB::table('Sight as s')
                    ->where('s.LocationId', $locationID)
                    ->where('s.IsRestaurant', 0)
                    ->whereNotNull('s.Latitude')
                    ->whereNotNull('s.Longitude')
                    ->whereIn('s.tier', [1, 2])
                    ->where('s.SightId', '<>', $focusSight->SightId);

                $totalCandidates = (int) $baseCandidates->count();
                if ($totalCandidates < 1) {
                    return [
                        'optimizedItinerary' => [],
                        'structuredFeed' => []
                    ];
                }

                $takeCount = (int) ceil($totalCandidates * 0.5);
                $takeCount = max(15, $takeCount);
                $takeCount = min($takeCount, $totalCandidates);
                $takeCount = max(1, min($takeCount, 120));

                $distanceSql = '(6371 * acos(LEAST(1, cos(radians(?)) * cos(radians(s.Latitude)) * cos(radians(s.Longitude) - radians(?)) + sin(radians(?)) * sin(radians(s.Latitude)))))';

                // Bounding box optimization - filter rows BEFORE distance calculation
                $searchRadius = 50; // Max reasonable distance for nearby attractions
                $lat_range = $searchRadius / 111.045;
                $lng_range = $searchRadius / (111.045 * cos(deg2rad($focusLat)));

                $attractionsQuery = DB::table('Sight as s')
                    ->select(
                        's.SightId', 's.MustSee', 's.Title', 's.Averagerating',
                        's.LocationId', 's.Slug', 's.IsRestaurant', 's.Address', 's.Latitude',
                        's.Longitude', 's.CategoryId', 'c.Title as CategoryTitle',
                        'l.Name as LName', 'l.Slug as Lslug', 'l.slugid',
                        's.popularity_score', 's.ReviewCount',
                        's.About', 'co.Name as CountryName',
                        'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc',
                        's.ticket', 's.MicroSummary', 's.TAAggregateRating', 's.short_description'
                    )
                    ->selectRaw($distanceSql . ' as distance_km', [$focusLat, $focusLon, $focusLat])
                    ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
                    ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
                    ->leftJoin('Country as co', 'l.CountryId', '=', 'co.CountryId')
                    ->where('s.LocationId', $locationID)
                    ->where('s.IsRestaurant', 0)
                    ->whereNotNull('s.Latitude')
                    ->whereNotNull('s.Longitude')
                    ->whereIn('s.tier', [1, 2])
                    ->where('s.SightId', '<>', $focusSight->SightId)
                    ->whereRaw('s.Latitude BETWEEN ? AND ?', [
                        $focusLat - $lat_range,
                        $focusLat + $lat_range
                    ])
                    ->whereRaw('s.Longitude BETWEEN ? AND ?', [
                        $focusLon - $lng_range,
                        $focusLon + $lng_range
                    ])
                    ->orderBy('distance_km', 'asc')
                    ->limit($takeCount)
                    ->get();

                return [
                    'optimizedItinerary' => $attractionsQuery->toArray(),
                    'structuredFeed' => []
                ];
            });

            $optimizedItinerary = $cachePayload['optimizedItinerary'] ?? [];
            $structuredFeed = $cachePayload['structuredFeed'] ?? [];

        } elseif ($attractionsOnly) {
            
            $attractionsQuery = DB::table('Sight as s')
                ->select(
                    's.SightId', 's.MustSee', 's.Title', 's.Averagerating',
                    's.LocationId', 's.Slug', 's.IsRestaurant', 's.Address', 's.Latitude',
                    's.Longitude', 's.CategoryId', 'c.Title as CategoryTitle',
                    'l.Name as LName', 'l.Slug as Lslug', 'l.slugid',
                    's.popularity_score', 's.ReviewCount',
                    's.About', 'co.Name as CountryName',
                    'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc',
                    's.ticket', 's.MicroSummary', 's.TAAggregateRating'
                )
                ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
                ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
                ->leftJoin('Country as co', 'l.CountryId', '=', 'co.CountryId')
                ->where('s.LocationId', $locationID)
                ->where('s.IsRestaurant', 0) 
                ->whereNotNull('s.Latitude')
                ->whereNotNull('s.Longitude')
                ->orderBy('s.MustSee', 'asc')
                ->orderBy('s.ReviewCount', 'desc')
                ->limit(30)
                ->get();
                
            $optimizedItinerary = $attractionsQuery->toArray();
            
            $optimizedItinerary = $attractionsQuery->toArray();

        } else {
            // Use new TouristFeedEngine for optimized routing
            try {
                // Calculate city tourism score
                $tourismScore = $this->calculateCityTourismScore($locationID);
                
                Log::info('Using TouristFeedEngine', [
                    'locationID' => $locationID,
                    'tourismScore' => $tourismScore
                ]);
                
                // Fetch data for the engine
                $sightsQuery = DB::table('Sight as s')
                    ->select(
                        's.SightId', 's.Title', 's.Latitude', 's.Longitude', 
                        's.ReviewCount', 's.tier', 's.KnownFor', 's.Related_Tags',
                        's.Averagerating', 's.LocationId', 's.Slug', 's.IsRestaurant',
                        's.Address', 's.CategoryId', 's.popularity_score', 's.About',
                        's.ticket', 's.MicroSummary', 's.short_description', 's.TAAggregateRating', 's.MustSee',
                        'c.Title as CategoryTitle', 'l.Name as LName', 'l.Slug as Lslug', 
                        'l.slugid', 'co.Name as CountryName', 'l.MetaTagTitle as mTitle', 
                        'l.MetaTagDescription as mDesc'
                    )
                    ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
                    ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
                    ->leftJoin('Country as co', 'l.CountryId', '=', 'co.CountryId')
                    ->where('s.LocationId', $locationID)
                    ->where('s.IsRestaurant', 0)
                    ->whereNotNull('s.Latitude')
                    ->whereNotNull('s.Longitude')
                    ->whereIn('s.tier', [1, 2, 3]);
                
                if (!empty($category_ids)) {
                    $sightsQuery->whereIn('s.CategoryId', $category_ids);
                }
                
                $sights = $sightsQuery->get();
                
                // Fetch experiences
                $experiencesQuery = DB::table('Experience as e')
                    ->select(
                        'e.ExperienceId as SightId', 'e.Name as Title', 'e.Latitude', 
                        'e.Longitude', 'e.ReviewCount', 'e.tier', 'e.Averagerating',
                        'e.LocationId', 'e.Slug', 'e.Cost', 'e.Duration', 'e.about',
                        'e.Img1', 'e.Img2', 'e.Img3', 'e.popularity_score',
                        'l.Name as LName', 'l.Slug as Lslug', 'l.slugid'
                    )
                    ->join('Location as l', 'e.LocationId', '=', 'l.LocationId')
                    ->where('e.LocationId', $locationID)
                    ->whereNotNull('e.Latitude')
                    ->whereNotNull('e.Longitude')
                    ->whereIn('e.tier', [1, 2, 3])
                    ->get();
                
                // Fetch restaurants
                $restaurants = DB::table('Restaurant as r')
                    ->select(
                        'r.RestaurantId', 'r.Title', 'r.Latitude', 'r.Longitude',
                        'r.ReviewCount', 'r.Averagerating', 'r.tier', 'r.cuisines',
                        'r.LocationId', 'r.Slug', 'r.Address', 'r.category',
                        'r.PriceRange', 'r.About', 'l.Name as LName', 'l.Slug as Lslug',
                        'l.slugid'
                    )
                    ->join('Location as l', 'r.LocationId', '=', 'l.LocationId')
                    ->where('r.LocationId', $locationID)
                    ->whereNotNull('r.Latitude')
                    ->whereNotNull('r.Longitude')
                    ->where('r.Averagerating', '>=', 4)
                    ->get();
                
                // ── Build context for AlgoFeedEngine ─────────────────────────
                $algoContext = [
                    'user_lat'             => $feedContext['user_lat']  ?? null,
                    'user_lon'             => $feedContext['user_lon']  ?? null,
                    'weather_type'         => $feedContext['weather']   ?? 'clear',
                    'local_time'           => now()->format('H:i:s'),
                    'local_date'           => now()->format('Y-m-d'),
                    'temperature_c'        => is_array($currentWeather) ? ($currentWeather['temp_c']    ?? 22)  : 22,
                    'feels_like_c'         => is_array($currentWeather) ? ($currentWeather['feelslike_c'] ?? 22) : 22,
                    'humidity'             => is_array($currentWeather) ? ($currentWeather['humidity']   ?? 50)  : 50,
                    'wind_speed_kmh'       => is_array($currentWeather) ? ($currentWeather['wind_kph']   ?? 10)  : 10,
                    'precipitation_mm'     => is_array($currentWeather) ? ($currentWeather['precip_mm']  ?? 0)   : 0,
                    'is_meal_time'         => $feedContext['is_meal_time'] ?? false,
                    'user_fatigue_state'   => 'fresh',
                    'user_hunger_state'    => ($feedContext['is_meal_time'] ?? false) ? 'hungry' : 'normal',
                    'session_energy_state' => 'active',
                    'user_id'              => Auth::id() ?? null,
                    'trip_id'              => null,
                ];

                if ($debugGeo) {
                    Log::info('GEO_DEBUG feed_inputs', [
                        'geo_debug_id'       => $debugGeoId,
                        'locationID'         => $locationID,
                        'tourismScore'       => $tourismScore,
                        'algo_context'       => $algoContext,
                    ]);
                }

                // ── Run AlgoFeedEngine (14-module pipeline) ───────────────────
                $feedStartedAt = microtime(true);
                $algoEngine    = new AlgoFeedEngine($locationID, $algoContext);
                $algoResult    = $algoEngine->generate();
                $feedData      = $algoResult['feed'] ?? [];

                if ($debugGeo) {
                    Log::info('GEO_DEBUG feed_generated', [
                        'geo_debug_id' => $debugGeoId,
                        'duration_ms'  => (int) round((microtime(true) - $feedStartedAt) * 1000),
                        'feed_count'   => count($feedData),
                        'session_id'   => $algoResult['session_id'] ?? null,
                        'meta'         => $algoResult['meta'] ?? [],
                    ]);
                }

                // ── Store structured feed for view ────────────────────────────
                $structuredFeed = $feedData;

                // ── Convert to flat list for view backward-compatibility ───────
                $optimizedItinerary = [];
                foreach ($feedData as $card) {
                    if (($card['type'] ?? '') === 'transit') continue;
                    // Map new card shape to legacy object shape expected by view
                    $optimizedItinerary[] = (object)[
                        'SightId'        => $card['entity_id']    ?? null,
                        'Title'          => $card['title']        ?? '',
                        'Latitude'       => $card['lat']          ?? null,
                        'Longitude'      => $card['lng']          ?? null,
                        'Averagerating'  => $card['rating']       ?? null,
                        'ReviewCount'    => $card['review_count'] ?? 0,
                        'tier'           => $card['tier']         ?? 4,
                        'Img1'           => $card['image']        ?? null,
                        'Slug'           => $card['slug']         ?? null,
                        'slugid'         => $card['slugid']       ?? null,
                        'CategoryTitle'  => $card['category']     ?? null,
                        'MicroSummary'   => $card['short_description'] ?? null,
                        'entity_type'    => $card['entity_type']  ?? $card['type'] ?? 'sight',
                        'type'           => $card['type']         ?? 'sight',
                        'moment_label'   => $card['moment_label_short'] ?? null,
                        'primary_role'   => $card['primary_role'] ?? null,
                    ];
                }

                Log::info('AlgoFeedEngine generated feed', [
                    'feedCount' => count($feedData),
                    'flatCount' => count($optimizedItinerary),
                    'session_id'=> $algoResult['session_id'] ?? null,
                ]);
                
            } catch (\Exception $e) {
                Log::error('AlgoFeedEngine failed, falling back to ItineraryGenerator: ' . $e->getMessage());
                
                // Fallback to old method
                $itineraryParams = [
                    'locationId' => $locationID,
                    'limit' => 30  
                ];
                
                if (!empty($category_ids)) {
                    $itineraryParams['categoryIds'] = $category_ids;
                }
                
                $optimizedItinerary = $this->itineraryGenerator->generateItinerary($itineraryParams);
                $structuredFeed = []; // No structured feed in fallback mode
            }
        }
        
        // Initialize structuredFeed if not set
        if (!isset($structuredFeed)) {
            $structuredFeed = [];
        }

        if ($request->boolean('dd_structured')) {
            dd([
                'structuredFeed_count' => is_array($structuredFeed) ? count($structuredFeed) : null,
                'structuredFeed_first' => is_array($structuredFeed) ? ($structuredFeed[0] ?? null) : null,
                'structuredFeed_first_data' => is_array($structuredFeed) ? (($structuredFeed[0]['data'] ?? null)) : null,
            ]);
        }

        $allResults = json_decode(json_encode($optimizedItinerary));

        if (empty($allResults)) {
            $fallbackQuery = DB::table('Sight as s')
                ->select(
                    's.SightId', 's.MustSee', 's.Title', 's.Averagerating',
                    's.LocationId', 's.Slug', 'IsRestaurant', 'Address', 's.Latitude',
                    's.Longitude', 's.CategoryId', 'c.Title as CategoryTitle',
                    'l.Name as LName', 'l.Slug as Lslug', 'l.slugid'
                )
                ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
                ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
                ->where('s.LocationId', $locationID)
                ->whereNotNull('s.Latitude')
                ->whereNotNull('s.Longitude');
                
            if (!empty($category_ids)) {
                $fallbackQuery->whereIn('s.CategoryId', $category_ids);
            } 
            
            $fallbackResults = $fallbackQuery
                ->orderBy('s.MustSee', 'desc')
                ->orderBy('s.ReviewCount', 'desc')
                ->limit(30)  
                ->get();

            $allResults = $fallbackResults;
        }

        $usedIds = [];
        $filteredResults = [];
        $duplicateCount = 0;

        foreach ($allResults as $item) {
            if (isset($item->SightId)) {
                $id = $item->SightId;

                if (in_array($id, $usedIds)) {
                    $duplicateCount++;
                    continue;
                }

                $usedIds[] = $id;
                $filteredResults[] = $item;
            }
        }
        
        if ($duplicateCount > 0) {
        }

        $perPage = 30;  
        $searchresults = $filteredResults;  
        $totalCountResults = count($filteredResults);        
        $getSightCat = DB::table('Sight')
            ->select('Category.CategoryId', 'Category.Title')
            ->distinct()
            ->join('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->where('Sight.LocationId', $locationID)
            ->get();

        // Get FAQs
        $faq = DB::table('LocationQuestion')
            ->where('LocationId', $locationID)
            ->get();

        // Get breadcrumb data
        $breadcumb = DB::table('Location as l')
            ->select(
                'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug',
                'co.Name as CountryName', 'l.LocationId', 'co.slug as cslug',
                'co.CountryId', 'cont.Name as ccName',
                'cont.CountryCollaborationId as contid'
            )
            ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
            ->leftJoin('CountryCollaboration as cont', 'cont.CountryCollaborationId', '=', 'co.CountryCollaborationId')
            ->where('l.LocationId', $locationID)
            ->get();

        // Get location parent data
        $locationPatent = [];
        $location_parent_name = null;
        $getparent = DB::table('Location')->where('LocationId', $lociID)->get();

        if (!empty($getparent) && $getparent[0]->LocationLevel != 1) {
            $loopcount = $getparent[0]->LocationLevel;
            $lociID = $getparent[0]->ParentId;
            for ($i = 1; $i < $loopcount; $i++) {
                $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                if (!empty($getparents)) {
                    if($i == 1) {
                        $location_parent_name = $getparents[0]->Name;
                    }
                    $locationPatent[] = [
                        'LocationId' => $getparents[0]->slugid,
                        'slug' => $getparents[0]->Slug,
                        'Name' => $getparents[0]->Name,
                    ];
                    if (!empty($getparents) && $getparents[0]->ParentId != "") {
                        $lociID = $getparents[0]->ParentId;
                    }
                }
            }
        }

        // Get sight images
        $sightIds = [];
        $sightImages = collect();
        
        // Collect SightIds from searchresults
        if (!empty($searchresults)) {
            foreach ($searchresults as $sight) {
                if (isset($sight->SightId) && !is_null($sight->SightId) &&
                    strpos($sight->SightId, 'rest_') === false &&
                    strpos($sight->SightId, 'exp_') === false) {
                    $sightIds[] = $sight->SightId;
                }
            }
        }
        
        // Also collect SightIds from structured feed
        if (!empty($structuredFeed)) {
            foreach ($structuredFeed as $feedItem) {
                // Main attraction card in the feed
                if (isset($feedItem['type']) && $feedItem['type'] === 'attraction' && isset($feedItem['data']['id'])) {
                    $sightIds[] = $feedItem['data']['id'];
                }

                // "Also at" sights/attractions
                if (!empty($feedItem['also_at'])) {
                    foreach ($feedItem['also_at'] as $alsoAtItem) {
                        $entityType = $alsoAtItem['entity_type'] ?? null;
                        $alsoAtId = $alsoAtItem['id'] ?? ($alsoAtItem['SightId'] ?? null);
                        if (($entityType === 'sight' || $entityType === 'attraction') && !empty($alsoAtId) && (is_string($alsoAtId) || is_int($alsoAtId))) {
                            $sightIds[] = $alsoAtId;
                        }
                    }
                }

                // Nearby sights
                if (!empty($feedItem['nearby']['sights'])) {
                    foreach ($feedItem['nearby']['sights'] as $nearbySight) {
                        if (isset($nearbySight['id'])) {
                            $sightIds[] = $nearbySight['id'];
                        }
                    }
                }

                // Nearby restaurants (they use Sight_image table too)
                if (!empty($feedItem['nearby']['restaurants'])) {
                    foreach ($feedItem['nearby']['restaurants'] as $nearbyRest) {
                        if (isset($nearbyRest['id'])) {
                            $sightIds[] = $nearbyRest['id'];
                        }
                    }
                }
            }
        }
        
        // Remove duplicates and fetch images
        $sightIds = array_unique($sightIds);
        if (!empty($sightIds)) {
            $sightImages = DB::table('Sight_image')
                ->whereIn('Sightid', $sightIds)
                ->get();
            
            // Create optimized image lookup array for performance
            $sightImageLookup = [];
            foreach ($sightImages as $img) {
                if (!isset($sightImageLookup[$img->Sightid])) {
                    $sightImageLookup[$img->Sightid] = [];
                }
                $sightImageLookup[$img->Sightid][] = $img->Image;
            }
        } else {
            $sightImageLookup = [];
        }

        if ($request->boolean('dd_also_at_images')) {
            $firstAlsoAt = null;
            if (!empty($structuredFeed)) {
                foreach ($structuredFeed as $feedItem) {
                    if (!empty($feedItem['also_at'])) {
                        $firstAlsoAt = $feedItem['also_at'];
                        break;
                    }
                }
            }

            $alsoAtIds = [];
            if (!empty($firstAlsoAt)) {
                foreach ($firstAlsoAt as $item) {
                    $entityType = $item['entity_type'] ?? null;
                    $id = $item['id'] ?? ($item['SightId'] ?? null);
                    if (($entityType === 'sight' || $entityType === 'attraction') && !empty($id)) {
                        $alsoAtIds[] = $id;
                    }
                }
            }

            $alsoAtSightImageRows = [];
            if (!empty($alsoAtIds)) {
                $alsoAtSightImageRows = DB::table('Sight_image')
                    ->whereIn('Sightid', array_unique($alsoAtIds))
                    ->limit(50)
                    ->get();
            }

            dd([
                'structuredFeed_count' => is_array($structuredFeed) ? count($structuredFeed) : null,
                'firstAlsoAt' => $firstAlsoAt,
                'alsoAtIds' => $alsoAtIds,
                'collected_sightIds_count' => count($sightIds),
                'collected_sightIds_sample' => array_slice($sightIds, 0, 25),
                'sightImages_count' => $sightImages->count(),
                'alsoAtSightImageRows_count' => is_object($alsoAtSightImageRows) ? $alsoAtSightImageRows->count() : null,
                'alsoAtSightImageRows_sample' => is_object($alsoAtSightImageRows) ? $alsoAtSightImageRows->take(10) : null,
            ]);
        }

        // Process sight categories for each result
        if (!empty($searchresults)) {
            // Collect all SightIds
            $sightIds = array_map(fn($s) => $s->SightId, $searchresults);

            // Fetch all categories for these SightIds
            $allCategories = DB::table('SightCategory')
                ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
                ->select('SightCategory.SightId', 'Category.Title')
                ->whereIn('SightCategory.SightId', $sightIds)
                ->get()
                ->groupBy('SightId');

            $allTimings = DB::table('SightTiming')
                ->whereIn('SightId', $sightIds)
                ->get()
                ->groupBy('SightId');

            foreach ($searchresults as $results) {
                $results->Sightcat = $allCategories[$results->SightId] ?? collect();
                $results->timing = $allTimings[$results->SightId] ?? [];
            }
        }

        // Get TripPlanner location data
        $tplocname = [];
        if(!empty($searchresults) && !empty($searchresults[0]->tp_location_mapping_id)) {
            $tplocname = DB::table('TPLocations')
                ->select('cityName', 'countryName', 'LocationId')
                ->where('LocationI d', $searchresults[0]->tp_location_mapping_id)
                ->get();
        }

        // Get location SEO data
        $location_seo = DB::table('Location')
            ->where('LocationId', $locationID)  
            ->first();

        // Set type for view
        $type = "h";

        // Get slug data
        $lslug = null;
        $lslugid = null;
        if (!empty($searchresults) && count($searchresults) > 0) {
            $firstResult = $searchresults[0];
            $lslug = $firstResult->Lslug ?? null;
            $lslugid = $firstResult->slugid ?? null;
        }

        // Get total count - filter by category if category page
        if (!$nearbyAttractionLanding) {
            $totalCountQuery = DB::table('Sight as s')
                ->where('s.LocationId', $locationID)
                ->whereNotNull('s.Latitude')
                ->whereNotNull('s.Longitude');
                
            if (!empty($category_ids)) {
                $totalCountQuery->whereIn('s.CategoryId', $category_ids);
            }
            
            $totalCountResults = $totalCountQuery->count();
        } else {
            $totalCountResults = is_array($searchresults) ? count($searchresults) : (is_countable($searchresults) ? count($searchresults) : 0);
        }
        $ismustsee = "";
        $rest_avail = "";
        $processedExperiences = ['separate_listings' => []];
        $restaurantdata = [];
        $getexp = [];

        $searchresults = $this->processAttractionsForView($searchresults, $sightImages);

        $neighborhoods = collect();
        if (isset($location) && $location && !empty($location->LocationId)) {
            try {
                $lat = $location->loc_latitude;
                $lng = $location->loc_longitude;
                $radius = 10;
                $earthRadius = 6371; 
                
                $maxLat = $lat + rad2deg($radius / $earthRadius);
                $minLat = $lat - rad2deg($radius / $earthRadius);
                $maxLng = $lng + rad2deg(asin($radius / $earthRadius) / cos(deg2rad($lat)));
                $minLng = $lng - rad2deg(asin($radius / $earthRadius) / cos(deg2rad($lat)));

                // First try to find neighborhoods by location ID and within bounding box
                $neighborhoods = DB::table('Neighborhood')
                    ->select('NeighborhoodId', 'Name', 'slug', 'Latitude', 'Longitude', 'LocationId')
                    ->where('LocationId', $location->LocationId)
                    ->whereBetween('Latitude', [$minLat, $maxLat])
                    ->whereBetween('Longitude', [$minLng, $maxLng])
                    ->get()
                    ->map(function($neighborhood) use ($location) {
                        if (!empty($location->loc_latitude) && !empty($location->loc_longitude) && 
                            !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                            $distance = $this->calculateDistance(
                                (float)$location->loc_latitude,
                                (float)$location->loc_longitude,
                                (float)$neighborhood->Latitude,
                                (float)$neighborhood->Longitude
                            );
                            $neighborhood->distance = round($distance, 2); // Add distance in km
                            $neighborhood->display_text = round($distance, 1) . ' km from ' . $neighborhood->Name;
                        } else {
                            $neighborhood->distance = null;
                            $neighborhood->display_text = $neighborhood->Name;
                        }
                        return $neighborhood;
                    })
                    ->sortBy('distance')
                    ->values();
                
                // If no neighborhoods found in bounding box, try without bounding box
                if ($neighborhoods->isEmpty()) {
                    $neighborhoods = DB::table('Neighborhood')
                        ->select('NeighborhoodId', 'Name', 'slug', 'Latitude', 'Longitude', 'LocationId')
                        ->where('LocationId', $location->LocationId)
                        ->get()
                        ->map(function($neighborhood) use ($location) {
                            if (!empty($location->loc_latitude) && !empty($location->loc_longitude) && 
                                !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                $distance = $this->calculateDistance(
                                    (float)$location->loc_latitude,
                                    (float)$location->loc_longitude,
                                    (float)$neighborhood->Latitude,
                                    (float)$neighborhood->Longitude
                                );
                                $neighborhood->distance = round($distance, 2);
                                $neighborhood->display_text = round($distance, 1) . ' km from ' . $neighborhood->Name;
                            } else {
                                $neighborhood->distance = null;
                                $neighborhood->display_text = $neighborhood->Name;
                            }
                            return $neighborhood;
                        })
                        ->sortBy('distance')
                        ->values();
                }

            } catch (\Exception $e) {
            }
        }

        $sightIds = collect($searchresults)->pluck('SightId')->unique()->toArray();
        $shortDescriptions = [];
        
        if (!empty($sightIds)) {
            try {
                $sights = DB::table('Sight')
                    ->whereIn('SightId', $sightIds)
                    ->select('SightId', 'short_description')
                    ->whereNotNull('short_description')
                    ->where('short_description', '<>', '')
                    ->get()
                    ->keyBy('SightId');
                
                // Create a mapping of SightId to short_description
                foreach ($sights as $sightId => $sight) {
                    if (!empty(trim($sight->short_description ?? ''))) {
                        $shortDescriptions[$sightId] = trim($sight->short_description);
                    }
                }
            } catch (\Exception $e) {
            }
        }
        
        foreach ($searchresults as &$result) {
            if (isset($result->SightId) && isset($shortDescriptions[$result->SightId])) {
                $result->short_description = $shortDescriptions[$result->SightId];
            }
        }

        // Fetch weather data from database
        $weatherData = [];
        $lname = $location_name; // Use location name for weather section header
        
        try {
            // Get current year for weather data
            $currentYear = Carbon::now()->year;
            
            // Fetch weather data from Weather_Month table
            $weatherResults = DB::table('Weather_Month')
                ->where('location_id', $locationID)
                ->where('year', $currentYear)
                ->orderBy('month')
                ->get();
            
            // If no data for current year, try previous year
            if ($weatherResults->isEmpty()) {
                $weatherResults = DB::table('Weather_Month')
                    ->where('location_id', $locationID)
                    ->where('year', $currentYear - 1)
                    ->orderBy('month')
                    ->get();
            }
            
             // Process weather data to match the expected format in the view
            if (!$weatherResults->isEmpty()) {
                $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                          'July', 'August', 'September', 'October', 'November', 'December'];
                
                foreach ($weatherResults as $weather) {
                    // Convert Celsius to Fahrenheit
                    $tempHighF = round(($weather->avg_temperature_max_c * 9/5) + 32);
                    $tempLowF = round(($weather->avg_temperature_min_c * 9/5) + 32);
                    
                    // Get month name
                    $monthName = $months[$weather->month - 1] ?? 'Unknown';
                    
                    // Get condition text from WeatherCondition_Text table
                    $conditionText = $this->getWeatherConditionFromDatabase($weather);
                    
                    $weatherData[] = [
                        'month' => $monthName,
                        'avg_temp_high' => $tempHighF,
                        'avg_temp_low' => $tempLowF,
                        'num_rainy_days' => $weather->num_rainy_days ?? 0,
                        'condition_text' => $conditionText
                    ];
                }
            }
        } catch (\Exception $e) {
            $weatherData = [];
        }

        return view('listing', compact(
            'searchresults', 'locn','location', 'faq', 'getSightCat', 'rest_avail',
            'ismustsee', 'tplocname', 'locationPatent', 'breadcumb',
            'restaurantdata', 'getexp', 'location_name', 'type', 'locn',
            'totalCountResults', 'sightImages', 'top_attractions', 'lslug',
            'lslugid', 'location_seo', 'catheading', 'location_parent_name',
            'processedExperiences', 'neighborhoods', 'category_ids', 'category_info',
            'category_name', 'weatherData', 'lname', 'structuredFeed', 'sightImageLookup',
            'currentWeather', 'userLat', 'userLon'
        ));
    }
    }
    /**
     * Get weather condition text from database based on weather parameters
     */
    private function getWeatherConditionFromDatabase($weather)
    {
        try {
            // Get weather condition from WeatherCondition_Text table based on parameters
            $conditionResult = DB::table('WeatherCondition_Text')
                ->where('temp_min_c', '<=', $weather->avg_temperature_min_c)
                ->where('temp_max_c', '>=', $weather->avg_temperature_max_c)
                ->where('wind_min_ms', '<=', $weather->avg_wind_speed_max_ms ?? 0)
                ->where('wind_max_ms', '>=', $weather->avg_wind_speed_max_ms ?? 0)
                ->where('humidity_min_percent', '<=', $weather->avg_relative_humidity ?? 0)
                ->where('humidity_max_percent', '>=', $weather->avg_relative_humidity ?? 0)
                ->first();

            if ($conditionResult) {
                return $conditionResult->condition_text;
            }

            // Fallback: try to find closest match based on temperature only
            $fallbackResult = DB::table('WeatherCondition_Text')
                ->where('temp_min_c', '<=', $weather->avg_temperature_mean_c ?? $weather->avg_temperature_min_c)
                ->where('temp_max_c', '>=', $weather->avg_temperature_mean_c ?? $weather->avg_temperature_max_c)
                ->first();

            if ($fallbackResult) {
                return $fallbackResult->condition_text;
            }

            // Final fallback
            return 'Pleasant weather';

        } catch (\Exception $e) {
            return 'Pleasant weather';
        }
    }

    public function getrestaurents($searchresults, $locationId) {
        // This method is no longer needed as the ItineraryGenerator service
        // now handles restaurant and experience retrieval
        return [
            'restaurant' => [],
            'getexp' => []
        ];
    }

    public function loadMoreAttractions(Request $request)
    {
        $page = $request->input('page', 1);
        $locationID = $request->input('locid');
        $slug = $request->input('slug');
        $perPage = 30;
        $skip = ($page - 1) * $perPage;

        // Get already shown IDs from request
        $shownIds = [];
        if ($request->has('shownIds')) {
            $shownIdsParam = $request->input('shownIds');
            if (!empty($shownIdsParam)) {
                $shownIds = explode(',', $shownIdsParam);
            }
        }

        // Page: $page, locationID: $locationID
        // Process shown IDs

        // Get attractions with pagination and exclusion of already shown IDs
        $items = $this->getAttractions($locationID, $perPage, $skip, $shownIds);

        // Get the total count of attractions for this location
        $totalCount = $this->getAttractionsCount($locationID, $shownIds);

        // Process attractions for view
        $processedAttractions = $this->processAttractionsForView($items['attractions']);
        $processedRestaurants = $this->processAttractionsForView($items['restaurants']);
        $processedExperiences = $this->processAttractionsForView($items['experiences']);

        // Get sight images for the attractions
        $sightIds = $processedAttractions->pluck('SightId')->toArray();
        $sightImages = DB::table('Sight_image')
            ->whereIn('Sightid', $sightIds)
            ->get();

        // Found sight images

        // Collect new IDs to return to the client
        $newIds = [];
        foreach ($processedAttractions as $attraction) {
            $newIds[] = $attraction->SightId;
        }
        foreach ($processedRestaurants as $restaurant) {
            $newIds[] = $restaurant->SightId;
        }
        foreach ($processedExperiences as $experience) {
            $newIds[] = $experience->SightId;
        }

        // Create a mixed list of attractions, restaurants, and experiences
        $mixedResults = $this->createMixedItinerary(
            $processedAttractions->toArray(),
            $processedRestaurants->toArray(),
            $processedExperiences->toArray()
        );

        // Prepare map data for markers
        $mapData = collect();

        // Add all items to map data
        foreach ($mixedResults as $item) {
            $type = 'attraction';
            if (isset($item->SightId)) {
                if (strpos($item->SightId, 'rest_') === 0) {
                    $type = 'restaurant';
                } elseif (strpos($item->SightId, 'exp_') === 0) {
                    $type = 'experience';
                }
            }

            $mapData->push([
                'Latitude' => $item->Latitude ?? null,
                'Longitude' => $item->Longitude ?? null,
                'SightId' => $item->SightId ?? null,
                'name' => $item->Title ?? ($item->Name ?? ''),
                'type' => $type
            ]);
        }

        // Render the view
        $html = view('getloclistbycatid', [
            'searchresults' => collect($mixedResults),
            'sightImages' => $sightImages,
            'type' => 'loadmore'
        ])->render();

        // Determine if there are more items to load
        $shownCount = count($shownIds) + count($newIds);
        $hasMore = $totalCount > $shownCount;

        // Response prepared with pagination data

        // Return the response
        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore,
            'newIds' => $newIds,
            'totalCount' => $totalCount,
            'page' => $page,
            'mapData' => json_encode($mapData)
        ]);
    }

    /**
     * Calculate city tourism score based on attraction count and quality
     * Returns a score from 0-100
     */
    private function calculateCityTourismScore($locationId)
    {
        try {
            // Count tier 1 and tier 2 attractions
            $tier1Count = DB::table('Sight')
                ->where('LocationId', $locationId)
                ->where('tier', 1)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->count();
            
            $tier2Count = DB::table('Sight')
                ->where('LocationId', $locationId)
                ->where('tier', 2)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->count();
            
            $totalCount = DB::table('Sight')
                ->where('LocationId', $locationId)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->count();
            
            // Calculate score based on tier distribution
            // Cities with many tier 1 attractions get higher scores
            $score = 0;
            
            if ($tier1Count >= 20) {
                $score = 90;
            } elseif ($tier1Count >= 10) {
                $score = 70;
            } elseif ($tier1Count >= 5) {
                $score = 50;
            } elseif ($totalCount >= 30) {
                $score = 45;
            } elseif ($totalCount >= 15) {
                $score = 30;
            } else {
                $score = 20;
            }
            
            return $score;
            
        } catch (\Exception $e) {
            Log::error('Error calculating city tourism score: ' . $e->getMessage());
            return 40; // Default medium score
        }
    }

    /**
     * Create a mixed itinerary of attractions, restaurants, and experiences
     * similar to how the ItineraryGenerator service does it
     */
    private function createMixedItinerary($attractions, $restaurants, $experiences)
    {
        $result = [];
        $attractionCount = count($attractions);
        $restaurantCount = count($restaurants);
        $experienceCount = count($experiences);

        // Calculate the distribution pattern
        $totalItems = $attractionCount + $restaurantCount + $experienceCount;

        // If we have all three types, create a mixed itinerary
        if ($attractionCount > 0 && $restaurantCount > 0 && $experienceCount > 0) {
            $attractionIndex = 0;
            $restaurantIndex = 0;
            $experienceIndex = 0;

            // Pattern: 2 attractions, 1 restaurant, 1 attraction, 1 experience, repeat
            while (count($result) < $totalItems) {
                // Add 2 attractions if available
                for ($i = 0; $i < 2; $i++) {
                    if ($attractionIndex < $attractionCount) {
                        $result[] = $attractions[$attractionIndex++];
                    }
                }

                // Add 1 restaurant if available
                if ($restaurantIndex < $restaurantCount) {
                    $result[] = $restaurants[$restaurantIndex++];
                }

                // Add 1 more attraction if available
                if ($attractionIndex < $attractionCount) {
                    $result[] = $attractions[$attractionIndex++];
                }

                // Add 1 experience if available
                if ($experienceIndex < $experienceCount) {
                    $result[] = $experiences[$experienceIndex++];
                }
            }
        }
        // If we only have attractions and restaurants
        else if ($attractionCount > 0 && $restaurantCount > 0) {
            $attractionIndex = 0;
            $restaurantIndex = 0;

            // Pattern: 3 attractions, 1 restaurant, repeat
            while (count($result) < $totalItems) {
                // Add 3 attractions if available
                for ($i = 0; $i < 3; $i++) {
                    if ($attractionIndex < $attractionCount) {
                        $result[] = $attractions[$attractionIndex++];
                    }
                }

                // Add 1 restaurant if available
                if ($restaurantIndex < $restaurantCount) {
                    $result[] = $restaurants[$restaurantIndex++];
                }
            }
        }
        // If we only have attractions and experiences
        else if ($attractionCount > 0 && $experienceCount > 0) {
            $attractionIndex = 0;
            $experienceIndex = 0;

            // Pattern: 3 attractions, 1 experience, repeat
            while (count($result) < $totalItems) {
                // Add 3 attractions if available
                for ($i = 0; $i < 3; $i++) {
                    if ($attractionIndex < $attractionCount) {
                        $result[] = $attractions[$attractionIndex++];
                    }
                }

                // Add 1 experience if available
                if ($experienceIndex < $experienceCount) {
                    $result[] = $experiences[$experienceIndex++];
                }
            }
        }
        // If we only have restaurants and experiences
        else if ($restaurantCount > 0 && $experienceCount > 0) {
            $restaurantIndex = 0;
            $experienceIndex = 0;

            // Pattern: 1 restaurant, 1 experience, repeat
            while (count($result) < $totalItems) {
                // Add 1 restaurant if available
                if ($restaurantIndex < $restaurantCount) {
                    $result[] = $restaurants[$restaurantIndex++];
                }

                // Add 1 experience if available
                if ($experienceIndex < $experienceCount) {
                    $result[] = $experiences[$experienceIndex++];
                }
            }
        }
        // If we only have one type, just add all of them
        else {
            if ($attractionCount > 0) {
                $result = $attractions;
            } else if ($restaurantCount > 0) {
                $result = $restaurants;
            } else if ($experienceCount > 0) {
                $result = $experiences;
            }
        }

        return $result;
    }

    /**
     * Get attractions for a location with pagination and exclusion of already shown IDs
     */
    private function getAttractions($locationId, $limit = 30, $skip = 0, $excludeIds = [])
    {
        // Extract regular IDs, restaurant IDs, and experience IDs from excludeIds
        $regularExcludeIds = [];
        $restaurantExcludeIds = [];
        $experienceExcludeIds = [];

        foreach ($excludeIds as $id) {
            if (strpos($id, 'rest_') === 0) {
                $restaurantExcludeIds[] = str_replace('rest_', '', $id);
            } else if (strpos($id, 'exp_') === 0) {
                $experienceExcludeIds[] = str_replace('exp_', '', $id);
            } else {
                $regularExcludeIds[] = $id;
            }
        }

        // Calculate how many of each type to get
        $attractionLimit = 20;
        $restaurantLimit = 5;
        $experienceLimit = 5;

        // First, get all sights for this location
        $sights = DB::table('Sight as s')
            ->select(
                's.SightId', 's.Title', 's.Latitude', 's.Longitude',
                's.ReviewCount', 's.Averagerating', 's.tier',
                DB::raw('CASE WHEN s.MustSee = 1 THEN 1 ELSE 0 END as MustSee'),
                DB::raw('CASE WHEN s.MustSee = 1 THEN 1 ELSE 0 END as IsMustSee'),
                's.LocationId', 's.Slug', 'IsRestaurant', 'Address', 's.CategoryId',
                'c.Title as CategoryTitle', 'l.Name as LName', 'l.Slug as Lslug',
                'l.slugid', 'l.tp_location_mapping_id', 's.ticket', 's.MicroSummary', 's.short_description',
                DB::raw("'attraction' as type")
            )
            ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
            ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
            ->whereNotNull('s.Latitude')
            ->whereNotNull('s.Longitude')
            ->where('s.LocationId', $locationId);

        // Exclude already shown regular IDs
        if (!empty($regularExcludeIds)) {
            $sights->whereNotIn('s.SightId', $regularExcludeIds);
        }

        $sights = $sights->orderBy('s.MustSee', 'desc')
            ->orderBy('s.ReviewCount', 'desc')
            ->limit($attractionLimit)
            ->get();

        // Get restaurants for this location
        $restaurants = DB::table('Restaurant as r')
            ->select(
                'r.RestaurantId as SightId', 'r.Title', 'r.Latitude', 'r.Longitude',
                'r.ReviewCount', 'r.Averagerating', 'r.tier', 'r.LocationId', 'r.slugid',
                'r.Slug', 'r.Timings', 'r.PriceRange', 'r.category', 'r.features',
                'r.Address', 'l.Name as LName',
                DB::raw("'restaurant' as type")
            )
            ->join('Location as l', 'r.LocationId', '=', 'l.LocationId')
            ->whereNotNull('r.Latitude')
            ->whereNotNull('r.Longitude')
            ->where('r.LocationId', $locationId);

        // Exclude already shown restaurant IDs
        if (!empty($restaurantExcludeIds)) {
            $restaurants->whereNotIn('r.RestaurantId', $restaurantExcludeIds);
        }

        $restaurants = $restaurants->orderBy('r.ReviewCount', 'desc')
            ->limit($restaurantLimit)
            ->get();

        // Get experiences for this location
        $experiences = DB::table('Experience as e')
            ->select(
                'e.ExperienceId as SightId', 'e.Name as Title', 'e.Latitude', 'e.Longitude',
                'e.ViatorReviewCount as ReviewCount', 'e.ViatorAggregationRating as Averagerating',
                'e.tier', 'e.LocationId', 'e.slugid', 'e.Slug', 'e.viator_url', 'e.adult_price',
                'e.Img1', 'e.Img2', 'e.Img3', 'l.Name as LName',
                DB::raw("'experience' as type")
            )
            ->join('Location as l', 'e.LocationId', '=', 'l.LocationId')
            ->whereNotNull('e.Latitude')
            ->whereNotNull('e.Longitude')
            ->where('e.LocationId', $locationId);

        // Exclude already shown experience IDs
        if (!empty($experienceExcludeIds)) {
            $experiences->whereNotIn('e.ExperienceId', $experienceExcludeIds);
        }

        $experiences = $experiences->orderBy('e.ViatorReviewCount', 'desc')
            ->limit($experienceLimit)
            ->get();

        // Process restaurants - add prefix to SightId to avoid conflicts
        foreach ($restaurants as $restaurant) {
            $restaurant->SightId = 'rest_' . $restaurant->SightId;
            $restaurant->MustSee = 0;
            $restaurant->IsMustSee = 0;
        }

        // Process experiences - add prefix to SightId to avoid conflicts
        foreach ($experiences as $experience) {
            $experience->SightId = 'exp_' . $experience->SightId;
            $experience->MustSee = 0;
            $experience->IsMustSee = 0;
        }

        // Return separate collections for each type
        return [
            'attractions' => $sights,
            'restaurants' => $restaurants,
            'experiences' => $experiences
        ];
    }

    /**
     * Get total count of attractions for a location excluding already shown IDs
     */
    private function getAttractionsCount($locationId, $excludeIds = [])
    {
        // Extract regular IDs, restaurant IDs, and experience IDs from excludeIds
        $regularExcludeIds = [];
        $restaurantExcludeIds = [];
        $experienceExcludeIds = [];

        foreach ($excludeIds as $id) {
            if (strpos($id, 'rest_') === 0) {
                $restaurantExcludeIds[] = str_replace('rest_', '', $id);
            } else if (strpos($id, 'exp_') === 0) {
                $experienceExcludeIds[] = str_replace('exp_', '', $id);
            } else {
                $regularExcludeIds[] = $id;
            }
        }

        // Count sights
        $sightCount = DB::table('Sight')
            ->where('LocationId', $locationId)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude');

        // Exclude already shown regular IDs
        if (!empty($regularExcludeIds)) {
            $sightCount->whereNotIn('SightId', $regularExcludeIds);
        }

        $sightCount = $sightCount->count();

        // Count restaurants
        $restaurantCount = DB::table('Restaurant')
            ->where('LocationId', $locationId)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude');

        // Exclude already shown restaurant IDs
        if (!empty($restaurantExcludeIds)) {
            $restaurantCount->whereNotIn('RestaurantId', $restaurantExcludeIds);
        }

        $restaurantCount = $restaurantCount->count();

        // Count experiences
        $experienceCount = DB::table('Experience')
            ->where('LocationId', $locationId)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude');

        // Exclude already shown experience IDs
        if (!empty($experienceExcludeIds)) {
            $experienceCount->whereNotIn('ExperienceId', $experienceExcludeIds);
        }

        $experienceCount = $experienceCount->count();

        // Total count is the sum of all three counts
        $totalCount = $sightCount + $restaurantCount + $experienceCount;

        // Count summary: Sights, Restaurants, Experiences

        return $totalCount + count($excludeIds);
    }

    /**
     * Get saved images for a specific location
     *
     * @param int $locationId The ID of the location
     * @return array Array of saved images with their details
     */
    private function getSavedImagesForLocation($locationId)
    {
        // Query to fetch saved images for the location
        // This assumes there's a table called 'SavedImages' or similar
        // Adjust the table name and fields according to your database schema
        $images = DB::table('Sight')
            ->where('LocationId', $locationId)
            ->where('IsActive', 1)
            ->where('Image', '!=', '')
            ->select('SightId', 'Title', 'Image')
            ->limit(6) // Limit to 6 images for display
            ->get();
        
        $savedImages = [];
        
        foreach ($images as $image) {
            $imagePath = !empty($image->Image) 
                ? asset('public/sight-images/' . $image->Image)
                : asset('explore/images/city-of-london-1.png'); // Fallback image
            
            $savedImages[] = [
                'id' => $image->SightId,
                'title' => $image->Title,
                'path' => $imagePath,
            ];
        }
        
        // If no images found, use default placeholders
        if (empty($savedImages)) {
            $defaultImages = [
                'city-of-london-1.png',
                'city-of-london-2.png',
                'city-of-london-3.png',
            ];
            
            foreach ($defaultImages as $index => $image) {
                $savedImages[] = [
                    'id' => $index,
                    'title' => 'Default Image',
                    'path' => asset('explore/images/' . $image),
                ];
            }
        }
        
        return $savedImages;
    }

    public function filtersightbycat(Request $request){

        $locId = $request->input('locationId');
        $catid = $request->input('catid');
        $names = $request->input('names');
        $delcatid = $request->input('delcatid');

        $clearfilter = $request->input('clearfilter');
        if($clearfilter == 1){
            foreach (request()->session()->all() as $key => $value) {
                if (str_starts_with($key, 'cat_') || str_starts_with($key, 'catid_')) {
                    request()->session()->forget($key);
                }
            }
        }

        $lid = $request->session()->get('locId');
        if($lid != $locId){
            foreach ($request->session()->all() as $key => $value) {
                if (str_starts_with($key, 'catid_')) {
                    $request->session()->forget($key);
                }
            }

          $request->session()->forget('locId');
          $request->session()->forget('mustSee');
          $request->session()->forget('isrestaurant');
        }
        if( $delcatid != ""){
            foreach ($request->session()->all() as $key => $value) {
                if (str_starts_with($key, 'catid_') && $value == $delcatid) {
                    $request->session()->forget($key);
                }
            }
            foreach ($request->session()->all() as $key => $value) {
                if (str_starts_with($key, 'cat_')) {
                    $catId = explode('_', $value)[1];

                    if ($catId == $delcatid) {
                        $request->session()->forget($key);
                    }
                }
            }

        }
        if($delcatid = "mustsee"){
          $request->session()->forget('mustSee');
        }
        if($delcatid = "isrestaurant"){
          $request->session()->forget('isrestaurant');
        }

        $request->session()->put('locId', $locId);

        if (!$request->session()->has('catid_' . $catid)) {
            $sessionVariableName = 'catid_' . $catid;
            $request->session()->put($sessionVariableName, $catid);

        }


        if (!$request->session()->has('cat_' . $catid)) {

            $catNameAndId = $names . '_' . $catid;

            $sessionVariableName = 'cat_' . $catid;
            $request->session()->put($sessionVariableName, $catNameAndId);
        }


        $categoryIds = [];
        $mustSee = 0;
        $isRestaurant = 0;
     foreach ($request->session()->all() as $key => $value) {
            if (str_starts_with($key, 'catid_')) {
                 if ($value != 'mustsee' && $value != 'isrestaurant' && $value != null) {

                        $categoryIds[] = $value;

                 }
                if ($value === 'mustsee') {
                    $mustSee = 1;
                    $request->session()->put('mustSee', 1);
                } elseif ($value === 'isrestaurant') {
                    $isRestaurant = 1;
                    $request->session()->put('isrestaurant', 1);
                }
            }
        }

        $getSight = [];
        $getSight2 = [];
        $getSight3 = [];


      $allResults = [];
    $result=[];
    // Fetch data based on 'mustSee' flag
    //return $categoryIds
    // Fetch data based on category IDs
    if (!empty($categoryIds) || (isset($categoryIds[0]) && $categoryIds[0] == null)) {

    $getSightCategory = DB::table('Sight')
         ->join('Location','Location.LocationId','=','Sight.LocationId')
        ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')

        ->leftJoin('Sight_image as img', function ($join) {
            $join->on('Sight.SightId', '=', 'img.Sightid');
            $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
           })

        ->where('Sight.LocationId', $locId)
        ->whereIn('Sight.CategoryId', $categoryIds)
        ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.TAAggregateRating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid', 'img.Image', 'Sight.TATotalReviews', 'Sight.ticket', 'Sight.MicroSummary', 'Sight.short_description')
      //  ->select('Category.Title  as CategoryTitle', 'Sight.*','Location.slugid', 'img.Image','Location.Name as LName')
         ->orderByRaw("FIELD(Sight.CategoryId, " . implode(',', $categoryIds) . ")")
          ->orderBy('Sight.IsMustSee', 'asc')
        ->get()
        ->toArray();



    $result = array_merge($result, $getSightCategory);
    $result = array_reverse($result);

    }

    if ($mustSee == 1) {
    $getSightMustSee = DB::table('Sight')
        ->join('Location','Location.LocationId','=','Sight.LocationId')
        ->leftJoin('Sight_image as img', function ($join) {
            $join->on('Sight.SightId', '=', 'img.Sightid');
            $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid = Sight.SightId LIMIT 1)');
           })
        ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
        ->where('Sight.LocationId', $locId)
        ->where('Sight.IsMustSee', 1)
        ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.TAAggregateRating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid', 'img.Image', 'Sight.TATotalReviews', 'Sight.ticket', 'Sight.MicroSummary', 'Sight.short_description')
        ->orderBy('Sight.IsMustSee', 'asc')
        //->select('Category.Title as CategoryTitle', 'Sight.*','Location.slugid', 'img.Image','Location.Name as LName')
        ->get()
        ->toArray();

    $result = array_merge($result, $getSightMustSee);
    if( $catid == 'mustsee'){
         $result = array_reverse($result);
    }

    }




    $result = array_unique($result, SORT_REGULAR);
    //	return $request->session()->all() ;


    if (!$request->session()->has('mustSee') && !$request->session()->has('isrestaurant') && (empty($categoryIds) || $categoryIds[0] == null)) {
        $result =[];
        $result = DB::table('Sight')
        ->join('Location','Location.LocationId','=','Sight.LocationId')
        ->leftJoin('Sight_image as img', function ($join) {
            $join->on('Sight.SightId', '=', 'img.Sightid');
            $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid = Sight.SightId LIMIT 1)');
           })
        ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
        ->where('Sight.LocationId', $locId)
       // ->select('Category.Title  as CategoryTitle', 'Sight.*','Location.slugid', 'img.Image','Location.Name as LName')
         ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.TAAggregateRating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid', 'img.Image', 'Sight.TATotalReviews', 'Sight.ticket', 'Sight.MicroSummary', 'Sight.short_description')
            ->orderBy('Sight.IsMustSee', 'asc')
        //->orderBy('Sight.TATotalReviews','desc')
        ->limit(10)
        ->get()->toArray();

    }
    // return $result;
    $sightImages = collect();
    $sightIds = []; // Initialize the array to hold SightId values

    if (!empty($result)) {
        // Check if $result is an array of stdClass objects
        if (is_array($result)) {
            // Use foreach to collect SightId from each stdClass object
            foreach ($result as $sights) {
                // Ensure $sights is an object and then access the SightId
                if (is_object($sights) && isset($sights->SightId)) {
                    $sightIds[] = $sights->SightId; // Collect SightId from object
                }
            }
        }

        // After collecting SightId, check if $sightIds is not empty
        if (!empty($sightIds)) {
            // Fetch sight images if $sightIds is not empty
            $sightImages = DB::table('Sight_image')
                ->whereIn('Sightid', $sightIds)
                ->get();
        }
    } else {
        $result = []; // If no results, set result to empty array
    }




    // Final result as an array
    $result = array_values($result);
    //	$result = $result->toArray();
        //new code
    if (!empty($result)) {

    // Collect all SightIds
    $sightIds = array_map(fn($s) => $s->SightId, $result);

    // Batch fetch categories, timings, reviews, images
    $allCategories = DB::table('SightCategory')
        ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
        ->select('SightCategory.SightId', 'Category.Title')
        ->whereIn('SightCategory.SightId', $sightIds)
        ->get()
        ->groupBy('SightId');

    $allTimings = DB::table('SightTiming')
        ->whereIn('SightId', $sightIds)
        ->get()
        ->groupBy('SightId');

    $allReviews = DB::table('SightReviews')
        ->whereIn('SightId', $sightIds)
        ->get()
        ->groupBy('SightId');

    $sightImages = DB::table('Sight_image')
        ->whereIn('Sightid', $sightIds)
        ->get()
        ->groupBy('Sightid');

    // Assign to each result
    foreach ($result as $results) {
        $results->Sightcat = $allCategories[$results->SightId] ?? collect();
        $results->timing = $allTimings[$results->SightId] ?? [];
        $results->reviews = $allReviews[$results->SightId] ?? collect();
        $results->images = $sightImages[$results->SightId] ?? collect();
    }
    }


    //end set timing cat val
    $mergedData = [];

    // Loop through attractions and associate them with categories
    if (!empty($result)) {
    foreach ($result as $att) {
        if (!empty($att->Sightcat)) {
            // Loop through categories and create an associative array
            foreach ($att->Sightcat as $category) {
                if ($category->Title != "") {
                    $categoryTitle = $category->Title;
                } else {
                    $categoryTitle = '';
                };

                if (!empty($att->Latitude) && !empty($att->Longitude)) {
                    // Check if $att->timing is set and contains the required properties
                    if (isset($att->timing->timings)) {
                        // Calculate the opening and closing time
                        $schedule = json_decode($att->timing->timings, true);
                        $currentDay = strtolower(date('D'));
                        $currentTime = date('H:i');
                        $openingtime = $schedule['time'][$currentDay]['start'];
                        $closingTime = $schedule['time'][$currentDay]['end'];
                        $isOpen = false;
                        $formatetime = '';

                        if ($openingtime === '00:00' && $closingTime === '23:59') {
                            $formatetime = '12:00';
                            $closingTime = '11:59';
                        }

                        if ($currentTime >= $openingtime && $currentTime <= $closingTime) {
                            $isOpen = true;
                        }

                        $timingInfo = $isOpen ? $formatetime . ' Open Now' : 'Closed Today';
                    } else {
                        $timingInfo = '';
                    }
                     if($att->TAAggregateRating != ""  && $att->TAAggregateRating != 0){
                        $recomd = rtrim($att->TAAggregateRating, '.0') * 20;
                        $recomd = $recomd . '%';
                   }else{
                       $recomd ='--';
                   }

                   $imagepath ="";
                   if($att->Image !=""){
                          $imagepath = asset('public/sight-images/'. $att->Image) ;
                   }else{
                          $imagepath = asset('public/images/Hotel lobby.svg');
                   }
                    $locationData = [
                        'Latitude' => $att->Latitude,
                        'Longitude' => $att->Longitude,
                        'SightId' => $att->SightId,
                        'ismustsee' => $att->IsMustSee,
                        'name' => $att->Title,
                        'recmd' => $recomd,
                        'cat' => $categoryTitle,
                        'tm' => $timingInfo, // Include the timing in the locationData array
                        'cityName'=>'City of '.$att->LName,
                        'imagePath'=>$imagepath,
                    ];

                    $mergedData[] = $locationData; // Add the locationData directly to mergedData
                }
            }
        } else {
            // If there are no categories, create a default "uncategorized" category
            if (!empty($att->Latitude) && !empty($att->Longitude)) {
                // Check if $att->timing is set and contains the required properties
                if (isset($att->timing->timings)) {

                   if($att->TAAggregateRating != ""  && $att->TAAggregateRating != 0){
                        $recomd = rtrim($att->TAAggregateRating, '.0') * 20;
                       $recomd = $recomd . '%';
                   }else{
                       $recomd ='--';
                   }
                   $imagepath ="";
                   if($att->Image !=""){
                          $imagepath = asset('public/sight-images/'. $att->Image) ;
                   }else{
                          $imagepath = asset('public/images/Hotel lobby.svg');
                   }
                    $locationData = [
                        'Latitude' => $att->Latitude,
                        'Longitude' => $att->Longitude,
                        'SightId' => $att->SightId,
                        'ismustsee' => $att->IsMustSee,
                        'name' => $att->Title,
                        'recmd' => $recomd,
                        'cat' => ' ',
                        'tm' => $timingInfo,
                        'cityName'=>'City of '.$att->LName,
                        'imagePath'=>$imagepath,
                    ];

                    $mergedData[] = $locationData;
                }
            }
        }
    }
    }

            $result = array_reverse($result);
    //return print_r($result);
    // Encode data as JSON
    $locationDataJson = json_encode($mergedData);

        $html = view('getloclistbycatid')->with('searchresults', $result)->with('sightImages',$sightImages)->with('type','filter')->render();

    return response()->json(['mapData' => $locationDataJson, 'html' => $html]);

    }

    public function showListing($city)
    {
    // Fetch city data from the database
    $location = DB::table('Location')
        ->where('Slug', $city)
        ->orWhere('slugid', $city)
        ->first();

    if (!$location) {
        abort(404, 'City not found');
    }

    // Fetch saved images for this location
    $savedImages = $this->getSavedImagesForLocation($location->LocationId);
    $savedImagesCount = count($savedImages);
    
    // Fetch attractions for this location
    $attractions = $this->getAttractions($location->LocationId, 50); // Get first 50 attractions
    
    // Process attractions for view compatibility
    $searchresults = $this->processAttractionsForView($attractions);
    
    // Return the view with the dynamic data
    return view('listing', [
        'cityName' => $location->Name,
        'locn' => $location->Name,
        // 'shortDescription' => $shortDescription,
        'location' => $locationContent ?: (object)[
            'About' => null,
            'BestTimeToVisit' => null,
            'TopReasonsToVisit' => null,
            'GettingAround' => null,
            'InsiderTips' => null
        ],
        'catheading' => '',
        'totalCountResults' => count($searchresults),
        'location_name' => $location->Name,
        'location_parent_name' => '',
        'locationPatent' => [],
        'breadcumb' => [],
        'savedImages' => $savedImages,
        'savedImagesCount' => $savedImagesCount,
        'searchresults' => $searchresults
    ]);
}
}
