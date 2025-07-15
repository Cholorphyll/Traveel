<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\Hotel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\ItineraryGenerator;

class ExploreController extends Controller
{

    private $itineraryGenerator;

    public function __construct(ItineraryGenerator $itineraryGenerator)
    {
        $this->itineraryGenerator = $itineraryGenerator;
    }

    // Helper function to create location objects from raw data
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
                continue;  // Skip if crucial data is missing
            }

            $locations[] = [
                'id' => $item->SightId ?? $item->Id ?? null,  // Support both SightId and Id
                'SightId' => $item->SightId ?? $item->Id ?? null,  // Ensure SightId is always set
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

    // Create main attractions from the fetched data
    public function createMainAttractions($data)
    {
        if (empty($data)) {
            return [];
        }

        $mainAttractions = [];

        // Convert to array if it's a Collection
        $data = $data instanceof \Illuminate\Support\Collection ? $data->toArray() : $data;

        foreach ($data as $item) {
            // Convert stdClass to array if needed
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
                'TATotalReviews' => $item['TATotalReviews'] ?? $item->TATotalReviews ?? null
            ];

            $mainAttractions[] = $attraction;
        }

        return $mainAttractions;
    }

    // Process attractions for view compatibility
    private function processAttractionsForView($attractions, $sightImages = null)
    {
        if ($sightImages === null) {
            $sightImages = collect();
        }

        $processedItems = [];

        foreach ($attractions as $item) {
            // Determine item type
            $type = 'attraction';
            $originalId = $item->SightId;

            if (isset($item->SightId)) {
                if (strpos($item->SightId, 'rest_') === 0) {
                    $type = 'restaurant';
                    $originalId = str_replace('rest_', '', $item->SightId);
                } elseif (strpos($item->SightId, 'exp_') === 0) {
                    $type = 'experience';
                    $originalId = str_replace('exp_', '', $item->SightId);
                }
            }

            // Add type to the item
            $item->type = $type;

            // For restaurants, get additional data
            if ($type === 'restaurant') {
                // Get restaurant categories - using a try-catch to handle missing tables
                try {
                    $restaurantCategories = DB::table('RestaurantCategory')
                        ->join('Category', 'RestaurantCategory.CategoryId', '=', 'Category.CategoryId')
                        ->select('Category.Title')
                        ->where('RestaurantCategory.RestaurantId', '=', $originalId)
                        ->get();

                    $item->Sightcat = $restaurantCategories;
                } catch (\Exception $e) {
                    // If table doesn't exist, create an empty collection
                    Log::warning("Error getting restaurant categories: " . $e->getMessage());
                    $item->Sightcat = collect();
                }

                $item->timing = [];

                // Get restaurant images
                try {
                    $restaurantImages = DB::table('Restaurant_image')
                        ->where('RestaurantId', $originalId)
                        ->get();

                    // Add restaurant images to sight images collection
                    foreach ($restaurantImages as $image) {
                        $sightImageObj = (object)[
                            'Sightid' => $item->SightId,
                            'Image' => $image->Image
                        ];
                        $sightImages->push($sightImageObj);
                    }
                } catch (\Exception $e) {
                    Log::warning("Error getting restaurant images: " . $e->getMessage());
                }
            }
            // For experiences, get additional data
            else if ($type === 'experience') {
                // Experiences don't have categories, so create an empty collection
                $item->Sightcat = collect();
                $item->timing = [];

                // Create image objects from experience image fields
                if (!empty($item->Img1)) {
                    $sightImageObj = (object)[
                        'Sightid' => $item->SightId,
                        'Image' => $item->Img1
                    ];
                    $sightImages->push($sightImageObj);
                }
                if (!empty($item->Img2)) {
                    $sightImageObj = (object)[
                        'Sightid' => $item->SightId,
                        'Image' => $item->Img2
                    ];
                    $sightImages->push($sightImageObj);
                }
                if (!empty($item->Img3)) {
                    $sightImageObj = (object)[
                        'Sightid' => $item->SightId,
                        'Image' => $item->Img3
                    ];
                    $sightImages->push($sightImageObj);
                }
            }

            $processedItems[] = $item;
        }

        return collect($processedItems);
    }

    public function singleLocation(Request $request, $segment, $category = null){

        $mustSeeLimit = 10;

        // Parse segment to get id and slug
        $parts = explode('-', $segment);
        $id = null;
        $slug = null;
        if (count($parts) > 1) {
            $id = array_shift($parts);
            $slug = implode('-', $parts);
        }

        $location_name = "";

        $location = DB::table('Location')
            ->select('Name', 'LocationId', 'About', 'MetaTagTitle as mTitle',
                    'MetaTagDescription as mDesc', 'tp_location_mapping_id',
                    'Longitude as loc_longitude', 'Lat as loc_latitude', 'Slug', 'slugid')
            ->where('slugid', $id)
            ->first();

        if ($location) {
            // Get the correct slug from the database
            $correctSlug = $location->Slug;

            // Construct what the URL should be
            $correctUrl = 'lo-' . $id . '-' . $correctSlug;

            // If the URL doesn't match exactly, redirect to the correct one
            $currentUrl = 'lo-' . $segment;
            if ($currentUrl != $correctUrl) {
                return redirect($correctUrl);
            }
        }
        // Validate location exists
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

        // Handle category redirect
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

        // Category processing
        $catheading = "";
        $catid = null;

        if($category != "") {
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

        $catid = str_replace('ct', '', $catid);
        $lid = $request->session()->get('locId');

        // Session management
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

        // Must see handling
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

        // Set a higher timeout for this operation
        ini_set('max_execution_time', 600);

        // Generate optimized itinerary using the new ItineraryGenerator service
        // This will directly fetch all data from the database and create the optimized itinerary
        $optimizedItinerary = $this->itineraryGenerator->generateItinerary([
            'locationId' => $locationID
        ]);

        // Convert to stdClass objects
        $allResults = json_decode(json_encode($optimizedItinerary));

        // Ensure we have results
        if (empty($allResults)) {
            // Fallback to basic query if the itinerary generator returns empty results
            $fallbackResults = DB::table('Sight as s')
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
                ->whereNotNull('s.Longitude')
                ->limit(20)
                ->get();

            $allResults = $fallbackResults;
        }

        // Track IDs to prevent duplicates
        $usedIds = [];
        $filteredResults = [];

        foreach ($allResults as $item) {
            if (isset($item->SightId)) {
                $id = $item->SightId;

                // Skip if this ID has already been used
                if (in_array($id, $usedIds)) {
                    continue;
                }

                $usedIds[] = $id;
                $filteredResults[] = $item;
            }
        }

        // Apply pagination - show only first 30 results initially
        $perPage = 30;
        $searchresults = array_slice($filteredResults, 0, $perPage);

        // Calculate total count for all attractions
        $totalCountResults = count($filteredResults);

        // Get sight categories
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
        if (!empty($searchresults)) {
            foreach ($searchresults as $sight) {
                if (isset($sight->SightId) && !is_null($sight->SightId) &&
                    strpos($sight->SightId, 'rest_') === false &&
                    strpos($sight->SightId, 'exp_') === false) {
                    $sightIds[] = $sight->SightId;
                }
            }

            if (!empty($sightIds)) {
                $sightImages = DB::table('Sight_image')
                    ->whereIn('Sightid', $sightIds)
                    ->get();
            }
        }

        // Process sight categories for each result
        if (!empty($searchresults)) {
            $sightIds = array_column($searchresults, 'SightId');
            $sightCats = DB::table('SightCategory')
                ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
                ->select('SightCategory.SightId', 'Category.Title')
                ->whereIn('SightCategory.SightId', $sightIds)
                ->get()
                ->groupBy('SightId');

            $timings = DB::table('SightTiming')
                ->whereIn('SightId', $sightIds)
                ->get()
                ->keyBy('SightId');

            foreach ($searchresults as $results) {
                if (isset($results->SightId) && (
                    strpos($results->SightId, 'rest_') === 0 ||
                    strpos($results->SightId, 'exp_') === 0)) {
                    // For restaurants and experiences, create an empty Sightcat collection
                    $results->Sightcat = collect();
                    $results->timing = [];
                } else if (isset($results->SightId)) {
                    // For attractions, get categories and timing
                    $results->Sightcat = $sightCats->get($results->SightId, collect());
                    $results->timing = $timings->get($results->SightId);
                }
            }
        }

        // Get TripPlanner location data
        $tplocname = [];
        if(!empty($searchresults) && !empty($searchresults[0]->tp_location_mapping_id)) {
            $tplocname = DB::table('TPLocations')
                ->select('cityName', 'countryName', 'LocationId')
                ->where('LocationId', $searchresults[0]->tp_location_mapping_id)
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

        // Get total count
        $totalCountResults = DB::table('Sight as s')
            ->where('s.LocationId', $locationID)
            ->whereNotNull('s.Latitude')
            ->whereNotNull('s.Longitude')
            ->count();

        // Define variables for compatibility with the view
        $ismustsee = "";
        $rest_avail = "";
        $processedExperiences = ['separate_listings' => []];
        $restaurantdata = [];
        $getexp = [];

        // Process attractions for view
        $searchresults = $this->processAttractionsForView($searchresults, $sightImages);

        // Return view with all necessary data
        return view('listing', compact(
            'searchresults', 'locn', 'faq', 'getSightCat', 'rest_avail',
            'ismustsee', 'tplocname', 'locationPatent', 'breadcumb',
            'restaurantdata', 'getexp', 'location_name', 'type', 'locn',
            'totalCountResults', 'sightImages', 'top_attractions', 'lslug',
            'lslugid', 'location_seo', 'catheading', 'location_parent_name',
            'processedExperiences'
        ));
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

        Log::info("loadMoreAttractions called with page: $page, locationID: $locationID");
        Log::info("Already shown IDs: " . implode(', ', $shownIds));

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

        Log::info("Found " . count($sightImages) . " sight images");

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

        Log::info("Response prepared: hasMore=$hasMore, totalCount=$totalCount, shownCount=$shownCount");

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
                'l.slugid', 'l.tp_location_mapping_id', 's.ticket', 's.MicroSummary',
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

        $sights = $sights->orderBy('s.tier', 'asc')
            ->orderBy('s.MustSee', 'desc')
            ->orderBy('s.ReviewCount', 'desc')
            ->orderBy('s.Averagerating', 'desc')
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

        $restaurants = $restaurants->orderBy('r.tier', 'asc')
            ->orderBy('r.Averagerating', 'desc')
            ->orderBy('r.ReviewCount', 'desc')
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

        $experiences = $experiences->orderBy('e.tier', 'asc')
            ->orderBy('e.ViatorAggregationRating', 'desc')
            ->orderBy('e.ViatorReviewCount', 'desc')
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

        // Log the counts
        Log::info("Counts - Sights: $sightCount, Restaurants: $restaurantCount, Experiences: $experienceCount, Total: $totalCount");

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
    if (!empty($categoryIds) || isset($categoryIds[0])  && $categoryIds[0] == null) {

    $getSightCategory = DB::table('Sight')
         ->join('Location','Location.LocationId','=','Sight.LocationId')
        ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')

        ->leftJoin('Sight_image as img', function ($join) {
            $join->on('Sight.SightId', '=', 'img.Sightid');
            $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
           })

        ->where('Sight.LocationId', $locId)
        ->whereIn('Sight.CategoryId', $categoryIds)
        ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.TAAggregateRating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.TATotalReviews','Sight.ticket','Sight.MicroSummary')
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
        ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.TAAggregateRating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.TATotalReviews','Sight.ticket','Sight.MicroSummary')
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
         ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.TAAggregateRating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.TATotalReviews','Sight.ticket','Sight.MicroSummary')
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

    foreach ($result as $results) {
        $sightId = $results->SightId;

        $Sightcat = DB::table('SightCategory')
            ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
            ->select('Category.Title')
            ->where('SightCategory.SightId', '=', $sightId)
            ->get();

        $results->Sightcat = $Sightcat;

        $timing = DB::select("SELECT * FROM SightTiming WHERE SightId = ?", [$sightId]);
        $results->timing = $timing;

        // Retrieve reviews for the sight using a raw SQL query
        $reviews = DB::select("SELECT * FROM SightReviews WHERE SightId = ?", [$sightId]);

        // Merge the reviews into the result directly
        $results->reviews = $reviews;
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

    // Fetch city description from the database or use a default
    $description = DB::table('CityContent')
        ->where('location_id', $location->LocationId)
        ->value('description');

    // If no description exists, use a default one
    $cityDescription = $description
        ? [$description]
        : ["Discover the amazing sights and experiences in {$location->Name}."];

    // Fetch location content from CityContent table
    $locationContent = DB::table('CityContent')
        ->where('location_id', $location->LocationId)
        ->first();

    // Fetch saved images for this location
    $savedImages = $this->getSavedImagesForLocation($location->LocationId);
    $savedImagesCount = count($savedImages);
    
    // Return the view with the dynamic data
    return view('listing', [
        'cityName' => $location->Name,
        'locn' => $location->Name,
        'cityDescription' => $cityDescription,
        'location' => $locationContent ?: (object)[
            'About' => null,
            'BestTimeToVisit' => null,
            'TopReasonsToVisit' => null,
            'GettingAround' => null,
            'InsiderTips' => null
        ],
        'catheading' => '',
        'totalCountResults' => 0,
        'location_name' => $location->Name,
        'location_parent_name' => '',
        'locationPatent' => [],
        'breadcumb' => [],
        'savedImages' => $savedImages,
        'savedImagesCount' => $savedImagesCount
    ]);
}
}
