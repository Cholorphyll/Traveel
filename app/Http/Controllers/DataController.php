<?php

namespace App\Http\Controllers;
use App\Models\Sight;
use Illuminate\Http\Request;
use Meilisearch\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Hotel;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Cache;
use App\Models\Tips;
use App\Helpers\OpenAIHelper;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ImportHotelsJob;
class DataController extends Controller
{
	 public function formatDate($date) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        } else {
            $date = str_replace('-', ' ', $date);
            return Carbon::createFromFormat('d M Y', $date)->format('Y-m-d');
        }
    }

       public function homepage(){
        $searchresults =null;
       return view('welcome')->with('searchresults',array());
    }


    public function listLocation(Request $request)
    {
        if (!$request->has('search')) {
            return;
        }

        $originalSearchText = trim($request->input('search'));

        // Early return for very short searches to avoid unnecessary processing
        if (strlen($originalSearchText) < 2) {
            return view('mainpage_result', [
                'searchresults' => [['value' => "Result not found"]],
                'sight' => 0
            ]);
        }

        // Create cache key from original search text
        $cacheKey = 'locations_search_' . md5($originalSearchText);
        $cacheDuration = 3600; // 1 hour cache duration for better performance

        $cachedData = Cache::remember($cacheKey, $cacheDuration, function () use ($originalSearchText) {
            // Initialize Meilisearch client
            $client = new \Meilisearch\Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

            // Process the search text to handle multi-word locations
            $words = preg_split('/\s+/', $originalSearchText);
            $searchTexts = [];

            // Create progressively longer search phrases (from longest to shortest)
            for ($i = count($words); $i > 0; $i--) {
                $phrase = implode(' ', array_slice($words, 0, $i));
                if (strlen($phrase) > 2) {
                    $searchTexts[] = $phrase;
                }
            }

            $sight = 0;
            $allLocations = collect();
            $foundMatch = false;

            // Get Meilisearch indexes
            $locationsIndex = $client->index('locations');
            $sightsIndex = $client->index('sights');
            $restaurantsIndex = $client->index('restaurants');
            $experiencesIndex = $client->index('experiences');

            // Search both locations and sights for each search term
            foreach ($searchTexts as $searchText) {
                if ($foundMatch) {
                    break; // Stop once we've found a match with a longer phrase
                }

                $foundResults = false;
                $combinedResults = collect();
                $exactMatches = collect();

                // Search locations
                $searchParams = [
                    'limit' => 5,
                    'attributesToRetrieve' => ['id', 'name', 'slug', 'slugid', 'parentName', 'countryName']
                ];

                $locationResults = $locationsIndex->search($searchText, $searchParams);
                $locations = collect($locationResults->getHits());

                if (!$locations->isEmpty()) {
                    // Check for exact matches in locations
                    foreach ($locations as $location) {
                        if (strtolower($location['name']) === strtolower($searchText)) {
                            $location['_exact_match'] = true;
                            $exactMatches->push($location);
                        } else {
                            $combinedResults->push($location);
                        }
                    }
                    $foundResults = true;
                }

                // Also search sights for the same term
                $sightParams = [
                    'limit' => 5,
                    'attributesToRetrieve' => ['id', 'Title', 'slugid', 'slug', 'parentName', 'countryName']
                ];

                $sightResults = $sightsIndex->search($searchText, $sightParams);
                $sights = collect($sightResults->getHits());

                if (!$sights->isEmpty()) {
                    // If we found sights, set the sight flag
                    $sight = 1;

                    // Check for exact matches in sights
                    foreach ($sights as $sight_item) {
                        if (isset($sight_item['Title']) && strtolower($sight_item['Title']) === strtolower($searchText)) {
                            $sight_item['_exact_match'] = true;
                            $exactMatches->push($sight_item);
                        } else {
                            $combinedResults->push($sight_item);
                        }
                    }
                    $foundResults = true;
                }

                // If we found any results, save them and break the loop
                if ($foundResults) {
                    // Combine exact matches first, then other results
                    $allLocations = $exactMatches->merge($combinedResults);
                    $foundMatch = true;
                    continue;
                }
            }

            // If we didn't find any matches with progressive search,
            // try individual words for cases like "kullu manali"
            if ($allLocations->isEmpty() && count($words) > 1) {
                $locationMatches = collect();
                $sightMatches = collect();

                foreach ($words as $word) {
                    if (strlen($word) > 2) {
                        // Search locations
                        $locationResults = $locationsIndex->search($word, [
                            'limit' => 5,
                            'attributesToRetrieve' => ['id', 'name', 'slug', 'slugid', 'parentName', 'countryName']
                        ]);
                        $locationMatches = $locationMatches->merge(collect($locationResults->getHits()));

                        // Search sights
                        $sightResults = $sightsIndex->search($word, [
                            'limit' => 5,
                            'attributesToRetrieve' => ['id', 'Title', 'slugid', 'slug', 'parentName', 'countryName']
                        ]);
                        $sightMatches = $sightMatches->merge(collect($sightResults->getHits()));
                    }
                }

                // Combine both location and sight results
                $combinedResults = collect();

                // Add location matches
                if (!$locationMatches->isEmpty()) {
                    $combinedResults = $combinedResults->merge($locationMatches);
                }

                // Add sight matches
                if (!$sightMatches->isEmpty()) {
                    $sight = 1; // Set the sight flag if we found any sights
                    $combinedResults = $combinedResults->merge($sightMatches);
                }

                // Save the combined results
                if (!$combinedResults->isEmpty()) {
                    $allLocations = $combinedResults;
                }

                // Remove duplicates based on id
                if (!$allLocations->isEmpty()) {
                    $uniqueLocations = collect();
                    $seenIds = [];

                    foreach ($allLocations as $item) {
                        $id = $item['id'];
                        if (!in_array($id, $seenIds)) {
                            $seenIds[] = $id;
                            $uniqueLocations->push($item);
                        }
                    }

                    $allLocations = $uniqueLocations->take(10);
                }
            }

            // Transform results for consistent output format
            $transformedLocations = $allLocations->map(function ($item) use ($sight) {
                $result = new \stdClass();

                // Determine if this is a sight or location based on available keys
                $isSight = isset($item['Title']);

                // Check if this is an exact match
                $isExactMatch = isset($item['_exact_match']) && $item['_exact_match'] === true;

                if ($isSight) {
                    // For Sights
                    // Make sure we have both slugid and id for proper URL construction
                    $result->id = $item['slugid'] ?? $item['id']; // Location_id for URL
                    $result->SightId = $item['id']; // SightId for URL
                    $result->Slug = $item['slug'] ?? '';

                    // Use Title if available, otherwise fall back to name
                    $displayName = isset($item['Title']) ? $item['Title'] : ($item['name'] ?? 'Unknown');

                    // Add exact match indicator and type
                    if ($isExactMatch) {
                        $displayName = $displayName ;
                    } else {
                        $displayName = $displayName ;
                    }

                    $result->displayName = $displayName . ', ' .
                                          ($item['parentName'] ?? '') . ', ' .
                                          ($item['countryName'] ?? '');
                    $result->type = 'sight'; // Add type indicator for sights
                } else {
                    // For Locations
                    $result->id = $item['slugid'] ?? $item['id'];
                    $result->Slug = $item['slug'] ?? '';

                    // Add exact match indicator and type
                    $displayName = $item['name'] ?? 'Unknown';
                    if ($isExactMatch) {
                        $displayName = $displayName ;
                    } else {
                        $displayName = $displayName ;
                    }

                    $result->displayName = $displayName .
                                          (isset($item['parentName']) ? ', ' . $item['parentName'] : '') .
                                          (isset($item['countryName']) ? ', ' . $item['countryName'] : '');
                    $result->type = 'location'; // Add type indicator for locations
                }

                return $result;
            });

            return ['locations' => $transformedLocations, 'sight' => $sight];
        });

        $locations = $cachedData['locations'];
        $sight = $cachedData['sight'];
        $result = [];

        if (!$locations->isEmpty()) {
            // Directly map the transformed objects to arrays
            foreach ($locations as $loc) {
                $item = [
                    'id' => $loc->id,
                    'Slug' => $loc->Slug,
                    'value' => $loc->displayName,
                    'type' => $loc->type ?? 'location' // Include type in all results
                ];

                // Add SightId if this is a sight result
                if (isset($loc->SightId) && $loc->type === 'sight') {
                    $item['SightId'] = $loc->SightId;
                }

                $result[] = $item;
            }
        } else {
            $result[] = ['value' => "Result not found"];
        }

       // Only save to search history if we have valid results and it's not a "Result not found" message
        if (!$locations->isEmpty() && isset($result[0]['id']) && $result[0]['value'] !== "Result not found") {
            $searchItem = [
                'id' => $result[0]['id'],
                'key' => $result[0]['Slug'],
                'Name' => $result[0]['value']
            ];

            // Add type and SightId information if this is a sight/attraction
            if (isset($result[0]['type']) && $result[0]['type'] === 'sight') {
                $searchItem['type'] = 'sight';
                if (isset($result[0]['SightId'])) {
                    $searchItem['SightId'] = $result[0]['SightId'];
                }
            } else if ($sight == 1 && isset($result[0]['SightId'])) {
                $searchItem['type'] = 'sight';
                $searchItem['SightId'] = $result[0]['SightId'];
            }

            // Use a try-catch to avoid potential serialization issues
            try {
                if (Session::has('lastsearch')) {
                    $serializedData = Session::get('lastsearch');
                    $searchHistory = unserialize($serializedData);

                    // Check if this location is already in history to avoid duplicates
                    $exists = false;
                    foreach ($searchHistory as $item) {
                        if ($item['id'] == $searchItem['id']) {
                            $exists = true;
                            break;
                        }
                    }

                    // Add to history if not already there
                    if (!$exists) {
                        $searchHistory[] = $searchItem;
                        // Keep only the last 5 searches
                        if (count($searchHistory) > 5) {
                            array_shift($searchHistory);
                        }
                    }
                } else {
                    // First search, create new array
                    $searchHistory = [$searchItem];
                }

                // Save back to session
                Session::put('lastsearch', serialize($searchHistory));
            } catch (\Exception $e) {
                // Error handling removed as requested
            }
        }

        return view('mainpage_result', [
            'searchresults' => $result,
            'sight' => $sight
        ]);
}


    public function recenthistory(Request $request){
        $sight = 0;
        $result = []; // Initialize $result outside the if condition

        if (Session::has('lastsearch')) {
            $serializedData = Session::get('lastsearch');
            $search = unserialize($serializedData);
            $lastFive = array_reverse(array_slice($search, -5));

            foreach ($lastFive as $value) {
                $item = [
                    'id' => $value['id'],
                    'Slug' => $value['key'],
                    'value' => $value['Name'],
                ];

                // Check if this is a sight/attraction and include the necessary information
                if (isset($value['type']) && $value['type'] === 'sight') {
                    $item['type'] = 'sight';
                    $sight = 1; // Set sight flag to ensure proper icon display

                    if (isset($value['SightId'])) {
                        $item['SightId'] = $value['SightId'];
                    }
                }

                $result[] = $item;
            }
        }

        return view('mainpage_result', ['searchresults' => $result,'sight'=>$sight]);

    }


  public function loadMoreAttractions(Request $request)
    {
        $page = $request->input('page');
        $perPage = 10;
        $locationID = $request->input('locid');

        if ($page == 1) {
            return response()->json(['html' => '']);
        }

        $offset = ($page - 1) * $perPage;

        // Main query - get paginated attractions
        $searchresults = DB::table('Sight as s')
            ->join('Location as l', 'l.LocationId', '=', 's.LocationId')
            ->leftJoin('Category', 's.categoryId', '=', 'Category.categoryId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('s.SightId', '=', 'img.Sightid')
                    ->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid = s.SightId LIMIT 1)');
            })
            ->select([
                's.SightId', 's.Title', 's.Latitude', 's.Longitude', 's.IsMustSee',
                's.Averagerating', 's.Address', 's.Slug', 's.IsRestaurant',
                'l.slugid', 'l.Name as LName',
                'img.Image',
                'Category.Title as CategoryTitle'
            ])
            ->where('s.LocationId', $locationID)
            ->orderBy('s.IsMustSee', 'asc')
            ->get();

        $attractions = collect($searchresults)
            ->slice($offset, $perPage)
            ->values();

        if ($attractions->isEmpty()) {
            return response()->json(['html' => '']);
        }

        // Get all sightIds for bulk queries
        $sightIds = $attractions->pluck('SightId')->toArray();

        // Bulk fetch sight images
        $sightImages = DB::table('Sight_image')
            ->whereIn('Sightid', $sightIds)
            ->get();

        // Bulk fetch categories
        $sightCategories = DB::table('SightCategory')
            ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
            ->select('SightCategory.SightId', 'Category.Title')
            ->whereIn('SightCategory.SightId', $sightIds)
            ->get()
            ->groupBy('SightId');

        // Bulk fetch timings
        $timings = DB::table('SightTiming')
            ->select('SightId', 'timings')
            ->whereIn('SightId', $sightIds)
            ->get()
            ->keyBy('SightId');

        // Bulk fetch reviews
        $reviews = DB::table('SightReviews')
            ->select('SightId', 'Name', 'ReviewDescription', 'ReviewRating', 'CreatedDate')
            ->whereIn('SightId', $sightIds)
            ->get()
            ->groupBy('SightId');

        // Attach related data to attractions
        foreach ($attractions as $att) {
            $att->Sightcat = $sightCategories[$att->SightId] ?? collect();
            $att->timing = isset($timings[$att->SightId]) ? [$timings[$att->SightId]] : [];
            $att->reviews = $reviews[$att->SightId] ?? collect();
        }

        // Prepare map data
        $mergedData = [];
        foreach ($attractions as $att) {
            if (!empty($att->Sightcat)) {
                foreach ($att->Sightcat as $category) {
                    if (!empty($att->Latitude) && !empty($att->Longitude)) {
                        $timingInfo = '';
                        if (!empty($att->timing) && isset($att->timing[0]->timings)) {
                            $schedule = json_decode($att->timing[0]->timings, true);
                            if ($schedule && isset($schedule['time'])) {
                                $currentDay = strtolower(date('D'));
                                $currentTime = date('H:i');

                                if (isset($schedule['time'][$currentDay])) {
                                    $openingtime = $schedule['time'][$currentDay]['start'];
                                    $closingTime = $schedule['time'][$currentDay]['end'];

                                    if ($openingtime === '00:00' && $closingTime === '23:59') {
                                        $timingInfo = '12:00 Open Now';
                                    } else {
                                        $timingInfo = ($currentTime >= $openingtime && $currentTime <= $closingTime)
                                            ? 'Open Now'
                                            : 'Closed Today';
                                    }
                                }
                            }
                        }

                        $recomd = '--';
if (!empty($att->Averagerating) && $att->Averagerating != 0) {
    $recomd = $att->Averagerating . '%';
}

                        $imagepath = $att->Image
    ? 'https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/' . $att->Image . '&width=280&height=109'
    : asset('/images/Hotel lobby.svg');

                        $locationData = [
                            'Latitude' => $att->Latitude,
                            'Longitude' => $att->Longitude,
                            'SightId' => $att->SightId,
                            'ismustsee' => $att->IsMustSee,
                            'name' => $att->Title,
                            'recmd' => $recomd,
                            'cat' => $category->Title ?? '',
                            'tm' => $timingInfo,
                            'cityName' => 'City of ' . $att->LName,
                            'imagePath' => $imagepath,
                        ];

                        $mergedData[] = $locationData;
                    }
                }
            } elseif (!empty($att->Latitude) && !empty($att->Longitude)) {
                // Handle attractions without categories
                $timingInfo = '';
                if (!empty($att->timing) && isset($att->timing[0]->timings)) {
                    // Same timing logic as above
                    $schedule = json_decode($att->timing[0]->timings, true);
                    if ($schedule && isset($schedule['time'])) {
                        $currentDay = strtolower(date('D'));
                        $currentTime = date('H:i');
                        if (isset($schedule['time'][$currentDay])) {
                            $openingtime = $schedule['time'][$currentDay]['start'];
                            $closingTime = $schedule['time'][$currentDay]['end'];
                            if ($openingtime === '00:00' && $closingTime === '23:59') {
                                $timingInfo = '12:00 Open Now';
                            } else {
                                $timingInfo = ($currentTime >= $openingtime && $currentTime <= $closingTime)
                                    ? 'Open Now'
                                    : 'Closed Today';
                            }
                        }
                    }
                }

                $recomd = !empty($att->Averagerating) && $att->Averagerating != 0
    ? $att->Averagerating . '%'
    : 'Unavailable';

                    $imagepath = $att->Image
                    ? 'https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/' . $att->Image . '&width=280&height=109'
                    : asset('/images/Hotel lobby.svg');

                $locationData = [
                    'Latitude' => $att->Latitude,
                    'Longitude' => $att->Longitude,
                    'SightId' => $att->SightId,
                    'ismustsee' => $att->IsMustSee,
                    'name' => $att->Title,
                    'recmd' => $recomd,
                    'cat' => '',
                    'tm' => $timingInfo,
                    'cityName' => 'City of ' . $att->LName,
                    'imagePath' => $imagepath,
                ];

                $mergedData[] = $locationData;
            }
        }

        $html = view('getloclistbycatid')
            ->with('searchresults', $attractions)
            ->with('sightImages', $sightImages)
            ->with('type', 'loadmore')
            ->render();

        return response()->json([
            'mapData' => json_encode($mergedData),
            'html' => $html
        ]);
    }

  public function filtersightbycat(request $request){

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
//return $request->session()->all();
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
  //  ->select('Category.Title as CategoryTitle', 'Sight.*','Location.slugid', 'img.Image','Location.Name as LName')
  //   ->orderByRaw("FIELD(Sight.CategoryId, " . implode(',', $categoryIds) . ")")
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

    public function updateSight(Request $request)
    {
        try {

            // Enhanced file processing
            if ($request->hasFile('media')) {
                $files = $request->file('media');

                foreach ($files as $index => $file) {
                    try {
                        // Check if file exists and is readable
                        $realPath = $file->getRealPath();

                        if (!file_exists($realPath)) {
                            continue;
                        }

                        if (!is_readable($realPath)) {
                            continue;
                        }

                        // Get file stats
                        $stats = stat($realPath);
                        // Try to read file contents
                        $fileContents = file_get_contents($realPath);

                        if (empty($fileContents)) {
                            continue;
                        }


                    } catch (\Exception $e) {

                        continue;
                    }
                }
            } else {

            }

            DB::beginTransaction();

            // Get current sight data
            $currentSight = DB::table('Sight')
                ->where('SightId', $request->sightId)
                ->first();

        if (!$currentSight) {
            throw new \Exception('Sight not found');
        }

        // Prepare update data with only provided values
        $updateData = [];

        if ($request->has('about') && !empty(trim($request->about))) {
            $updateData['About'] = $request->about;
        }

        if ($request->has('duration') && !empty(trim($request->duration))) {
            $updateData['duration'] = $request->duration;
        }

        if ($request->has('whatsNearby') && !empty(trim($request->whatsNearby))) {
            $updateData['NearestStation'] = $request->whatsNearby;
        }

        // Only update if there are changes
        if (!empty($updateData)) {
            $updateData['UpdatedOn'] = now();

            $sightUpdated = DB::table('Sight')
                ->where('SightId', $request->sightId)
                ->update($updateData);


        }

        // Handle media uploads (images and videos)
        if ($request->hasFile('media')) {

            $files = $request->file('media');
            foreach ($files as $index => $file) {
                try {

                    // Generate filename based on file type
                    $fileType = $file->getMimeType();
                    $prefix = strpos($fileType, 'image/') === 0 ? 'img' : 'vid';
                    $filename = $prefix . rand(1, 9) . '-' . $request->sightId . '.' . $file->getClientOriginalExtension();

                    try {

                        // Get the AWS S3 client with SSL verification configuration
                        $s3Client = new \Aws\S3\S3Client([
                            'version' => 'latest',
                            'region' => config('filesystems.disks.s3.region'),
                            'credentials' => [
                                'key' => config('filesystems.disks.s3.key'),
                                'secret' => config('filesystems.disks.s3.secret'),
                            ],
                            'http' => [
                                'verify' => false, // Disable SSL verification for testing
                                'timeout' => 300 // Set a reasonable timeout
                            ]
                        ]);

                        // Prepare file contents
                        $fileContents = file_get_contents($file->getRealPath());
                        // Processing file contents

                        // Upload using AWS SDK directly
                        $result = $s3Client->putObject([
                            'Bucket' => config('filesystems.disks.s3.bucket'),
                            'Key' => 'Sight-images/' . $filename,
                            'Body' => $fileContents,
                            'ContentType' => $file->getMimeType(),
                            'ACL' => 'public-read' // Make sure the file is accessible
                        ]);

                        $s3Uploaded = true;

                    } catch (\Exception $e) {

                        $s3Uploaded = false;
                    }

                    if ($s3Uploaded) {
                        // Check for existing primary media
                        $hasPrimaryMedia = DB::table('Sight_image')
                            ->where('Sightid', $request->sightId)
                            ->where('IsPrimary', 1)
                            ->exists();

                        // Insert media record into database
                        $inserted = DB::table('Sight_image')->insert([
                            'Sightid' => $request->sightId,
                            'Image' => $filename,
                            'IsVideo' => strpos($file->getMimeType(), 'video/') === 0 ? 1 : 0,
                            'IsPrimary' => $hasPrimaryMedia ? 0 : 1,
                            'IsActive' => 1,
                            'Title' => $request->knownFor ?? 'Sight Media',
                            'CreatedOn' => now()
                        ]);

                        if ($inserted) {

                        } else {
                            throw new \Exception("Failed to insert media record into DB for sight {$request->sightId}");
                        }
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }

        // Handle timing data
       if ($request->has('timing_data')) {

    try {
        $timingData = json_decode($request->timing_data, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            // Get existing timing data
            $existingTiming = DB::table('SightTiming')
                ->where('SightId', $request->sightId)
                ->first();

            // Initialize with existing timings or empty array
            $existingTimings = $existingTiming ? json_decode($existingTiming->timings, true)['time'] ?? [] : [];
            $formattedTimings = $existingTimings;

            // Get the selected days from the request
            $selectedDays = [];

            // Check for selected days in the request
            if (isset($timingData['selectedDays']) && is_array($timingData['selectedDays'])) {
                $selectedDays = $timingData['selectedDays'];
            }

            // If no selectedDays are explicitly provided, check if any checkboxes were checked
            // This is a fallback for the frontend implementation
            if (empty($selectedDays)) {
                $allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                foreach ($allDays as $day) {
                    if (isset($timingData[$day]) && $timingData[$day]) {
                        $selectedDays[] = $day;
                    }
                }
            }


            // If still no days selected but 24 hours or closed is set, use all days as fallback
            if (empty($selectedDays) && (
                (isset($timingData['twentyFourHours']) && $timingData['twentyFourHours']) ||
                (isset($timingData['closed']) && $timingData['closed'])
            )) {
                $selectedDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            }

            if (!empty($selectedDays)) {
                if (isset($timingData['twentyFourHours']) && $timingData['twentyFourHours']) {
                    // Handle 24 hours case - only for selected days
                    foreach ($selectedDays as $day) {
                        $formattedTimings[$day] = [
                            'start' => '00:00',
                            'end' => '23:59'
                        ];
                    }
                } elseif (isset($timingData['closed']) && $timingData['closed']) {
                    // Handle closed case - only for selected days
                    foreach ($selectedDays as $day) {
                                $formattedTimings[$day] = [
                                    'start' => 'closed',  // Change from empty string to 'closed'
                                    'end' => 'closed'     // Change from empty string to 'closed'
                                ];
                            }
                } else {
                    // Handle specific times - only for selected days
                    foreach ($selectedDays as $day) {
                        if (!empty($timingData['days'][$day])) {
                            $formattedTimings[$day] = [
                                'start' => $timingData['days'][$day][0]['open'],
                                'end' => $timingData['days'][$day][0]['close']
                            ];
                        }
                    }
                }
            }

            // Fix for the datetime issue - use now() for both fields instead of COALESCE
            $now = now();

            // Save to database
            $result = DB::table('SightTiming')->updateOrInsert(
                ['SightId' => $request->sightId],
                [
                    'timings' => json_encode(['time' => $formattedTimings]),
                    'dt_modify' => $now,
                    'dt_added' => $existingTiming ? DB::raw('dt_added') : $now, // Use existing dt_added if it exists
                    'flg_active' => 1,
                    'main_hours' => count($formattedTimings)
                ]
            );

        } else {
            throw new \Exception('Invalid timing data format: ' . json_last_error_msg());
        }
    } catch (\Exception $e) {

    }
}

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Sight updated successfully'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error occurred: ' . $e->getMessage()
        ], 500);
    }
}

public function explore(Request $request, $id)
{
    $pt = $request->input('sloc');
    $sighid = null;
    $locationID = null;
    $slug = "";

    $parts = explode('-', $id);
    $locationID = $parts[0];
    $sighid = $parts[1];
    array_shift($parts);
    array_shift($parts);
    $slug = implode('-', $parts);

    $locid = $locationID;

    // Get parent location
    $getparent1 = DB::table('Location')
        ->select('LocationId')
        ->where('slugid', $locationID)
        ->get();

    if (!$getparent1->isEmpty()) {
        $locationID = $getparent1[0]->LocationId;
    } else {
        if ($locid != null) {
            $checkgetloc = DB::table('Location')
                ->select('slugid')
                ->where('LocationId', $locid)
                ->get();

            if (!$checkgetloc->isEmpty()) {
                $lid = $checkgetloc[0]->slugid;
                return redirect()->route('sight.details', [$lid . '-' . $sighid . '-' . $slug]);
            }
        }
        abort(404, 'NOT FOUND');
    }

    $lname = "";

    // Main query to get sight details
    $searchresults = DB::table('Sight')
        ->leftJoin('Location', 'Sight.LocationId', '=', 'Location.LocationId')
        ->leftJoin('Country', 'Location.CountryId', '=', 'Country.CountryId')
        ->leftJoin('Category', 'Sight.CategoryId', '=', 'Category.CategoryId')
        ->select([
            'Sight.Title', 'Sight.Address', 'Sight.SightId', 'Sight.LocationId',
            'Sight.Longitude', 'Sight.Latitude', 'Sight.Averagerating',
            'Sight.About', 'Sight.Phone', 'Sight.Website', 'Sight.CategoryId',
            'Sight.ReviewCount', 'Sight.MetaTagTitle', 'Sight.MetaTagDescription',
            'Location.Name','Location.show_in_index', 'Location.Slug as Lslug', 'Country.Name as countryName',
            'Location.slugid', 'Sight.IsMustSee', 'Sight.duration',
            'Sight.ReviewSummaryLabel', 'Sight.ReviewSummary', 'Sight.Award',
            'Sight.Award_description', 'Sight.Email', 'Location.Slug as lslug'
        ])
        ->where('Sight.SightId', $sighid)
        ->where('Location.LocationId', $locationID)
        ->where('Sight.Slug', $slug)
        ->get()
        ->toArray();

    if (empty($searchresults)) {
        if ($locid != null) {
            $checkgetloc = DB::table('Location')
                ->select('slugid')
                ->where('LocationId', $locid)
                ->get();

            if (!$checkgetloc->isEmpty()) {
                $lid = $checkgetloc[0]->slugid;
                return redirect()->route('sight.details', [$lid . '-' . $sighid . '-' . $slug]);
            }
        }
        abort(404, 'NOT FOUND');
    }

    $lslug = $searchresults[0]->lslug;
    $lslugid = $searchresults[0]->slugid;
    $lname = $searchresults[0]->Name;

    // Get nearby restaurants
    $get_nearby_rest = DB::table('Sight_nearby_restaurant')
        ->where('SightId', $sighid)
        ->get();

    // Get parent location data
    $getparent = DB::table('Location')
        ->select('LocationLevel', 'ParentId')
        ->where('LocationId', $locationID)
        ->get();

    $locationPatent = [];
    if (!$getparent->isEmpty()) {
        if ($getparent[0]->LocationLevel != 1) {
            $loopcount = $getparent[0]->LocationLevel;
            $lociID = $getparent[0]->ParentId;
            for ($i = 1; $i < $loopcount; $i++) {
                $getparents = DB::table('Location')
                    ->select('LocationId', 'Slug', 'ParentId', 'Name')
                    ->where('LocationId', $lociID)
                    ->get();
                if (!empty($getparents)) {
                    $locationPatent[] = [
                        'LocationId' => $getparents[0]->LocationId,
                        'slug' => $getparents[0]->Slug,
                        'Name' => $getparents[0]->Name,
                    ];
                    if (!empty($getparents) && $getparents[0]->ParentId != "") {
                        $lociID = $getparents[0]->ParentId;
                    }
                } else {
                    break;
                }
            }
        }
    }

    // Get breadcrumb data
    $breadcumb = DB::table('Location as l')
        ->select([
            'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug',
            'co.Name as CountryName', 'l.LocationId', 'co.slug as cslug',
            'co.CountryId', 'cont.Name as ccName',
            'cont.CountryCollaborationId as contid', 'l.slugid'
        ])
        ->join('Country as co', 'l.CountryId', '=', 'co.CountryId')
        ->leftJoin('CountryCollaboration as cont', 'cont.CountryCollaborationId', '=', 'co.CountryCollaborationId')
        ->where('l.LocationId', $locationID)
        ->get()
        ->toArray();

    // Get reviews
    $sightreviews = DB::table('SightReviews')
        ->whereNotNull('Name')
        ->select(['Name', 'ReviewDescription', 'IsRecommend', 'ReviewRating', 'CreatedDate'])
        ->where('SightId', $sighid)
        ->limit(20)
        ->get();

    // Get FAQ
    $faq = DB::table('SightListingDetailFaq')
        ->select(['Faquestion', 'Answer'])
       ->where('SightId', $sighid)
        ->get();
        // ->toArray();

    // Get Questions & Answers
    $sightQuesAns = DB::table('SightQuestion')
        ->select(['Question', 'Answer'])
        ->where('SightId', $sighid)
        ->get();

    // Get Nearby Sight
    $nearby_sight = collect();
    $getcat = collect();
    $nearbyatt = collect();
    $gettiming = collect();
    $nearby_hotel = collect();
    $within = "";

    if (!empty($searchresults)) {
        // Get categories
        $getcat = DB::table('SightCategory')
            ->join('Category', 'Category.CategoryId', '=', 'SightCategory.CategoryId')
            ->select('Category.Title')
            ->where('SightCategory.SightId', $searchresults[0]->SightId)
            ->distinct('SightCategory.CategoryId')
            ->get();

        // Get timing
        $gettiming = DB::table('SightTiming')
            ->select(['main_hours', 'timings'])
            ->where('SightId', $searchresults[0]->SightId)
            ->get();

        $longitude = $searchresults[0]->Longitude;
        $latitude = $searchresults[0]->Latitude;
        $LocationId = $searchresults[0]->LocationId;
        $sightid = $searchresults[0]->SightId;
        $locationIds = array_column($locationPatent, 'LocationId');

        if ($latitude == "" || $longitude == "") {
            $getsight = DB::table('Sight')
                ->select('Latitude', 'Longitude', 'SightId')
                ->where('LocationId', $LocationId)
                ->whereNotNull('Latitude')
                ->whereNotNull('Longitude')
                ->limit(1)
                ->get();

            if (!$getsight->isEmpty()) {
                $longitude = $getsight[0]->Longitude;
                $latitude = $getsight[0]->Latitude;
            } else {
                $getsight = DB::table('Location as l')
                    ->join('Sight as s', 'l.LocationId', '=', 's.LocationId')
                    ->select('s.Latitude', 's.Longitude', 's.SightId')
                    ->whereIn('l.LocationId', $locationIds)
                    ->whereNotNull('s.Latitude')
                    ->whereNotNull('s.Longitude')
                    ->limit(1)
                    ->get();

                if (!$getsight->isEmpty()) {
                    $longitude = $getsight[0]->Longitude;
                    $latitude = $getsight[0]->Latitude;
                }
            }
        }

        if ($longitude != "" && $latitude != "") {
            $nearby_sight = DB::table('Sight_nearby_sights')
                ->where('Sid', $sightid)
                ->orderBy('distance', 'asc')
                ->get();

            $nearbyatt = DB::table('Sight_nbsight')
                ->where('Sid', $sightid)
                ->get();

            $nearby_hotel = DB::table('Sight_nearby_hotels')
                ->where('SightId', $sightid)
                ->get();
        }

           $parts = explode('-', $id);
             $actualSightId = $parts[1] ?? null;

             if (!$actualSightId || !is_numeric($actualSightId)) {
                 return redirect()->route('home')->with('error', 'Invalid SightId provided.');
             }

             // Query the SightTiming table
             $sightTiming = DB::table('SightTiming')->where('SightId', $actualSightId)->first();

             // Query the Sight table to fetch the LocationId
             $locationId = DB::table('Sight')->where('SightId', $actualSightId)->value('LocationId');

             if (!$locationId) {
                 return redirect()->route('home')->with('error', 'Location not found for this SightId.');
             }

             // Query the Location table to fetch the country using LocationId
             $country = DB::table('Location')->where('LocationId', $locationId)->value('country') ?? 'Unknown';

             // Function to determine timezone directly by country name
             function getTimezoneByCountryName($countryName) {
                 $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                 foreach ($timezones as $timezone) {
                     if (stripos($timezone, $countryName) !== false) {
                         return $timezone;
                     }
                 }
                 return 'UTC'; // Default if no match
             }

             // Fetch timezone dynamically using the country name
             $timezone = getTimezoneByCountryName($country);

             // Set the timezone for the script
             date_default_timezone_set($timezone);

             // Decode timings
             $timings = [];
if ($sightTiming && !empty($sightTiming->timings)) {
    $decodedTimings = json_decode($sightTiming->timings, true);
    if (isset($decodedTimings['time'])) {
        // Extract the time data and reformat it
        foreach ($decodedTimings['time'] as $day => $timing) {
            $timings[$day] = [
                'open' => $timing['start'],
                'close' => $timing['end']
            ];
        }
    }
}


             // Fetch current day timings
             $currentDay = strtolower(now()->format('l')); // e.g., 'wednesday'
             $todayTimings = $timings[$currentDay] ?? null;
             $todayStartTime = $todayTimings['open'] ?? null;
             $todayEndTime = $todayTimings['close'] ?? null;

    // Pass data to the view

                $get_experience = DB::table('ExperienceItninerary as e')
    ->join('Experience as exp', 'exp.ExperienceId', '=', 'e.ExperienceId')
    ->leftJoin('ExperienceReview as rr', 'exp.ExperienceId', '=', 'rr.ExperienceId')
    ->select('exp.Img1','exp.Img2','exp.Img3','exp.slugid','exp.ExperienceId','exp.Latitude','exp.Longitude',
             'exp.Slug','exp.Name','exp.viator_url','exp.Duration','exp.Cost','exp.TAAggregationRating',
             DB::raw("COUNT(rr.Id) as review_count"))
    ->where('e.SightId', $sighid)
    ->groupBy('exp.ExperienceId', 'exp.Img1', 'exp.Img2', 'exp.Img3', 'exp.slugid',
              'exp.Latitude', 'exp.Longitude', 'exp.Slug', 'exp.Name', 'exp.viator_url',
              'exp.Duration', 'exp.Cost', 'exp.TAAggregationRating')
    ->orderBy('TAAggregationRating', 'desc')
    ->limit(4)
    ->get();
              //  end experience
          }

                $Sight_image = DB::table('Sight_image')
				->select('Sight_image.Image')
                ->where('Sightid',$sightid)
                ->get();

				$tips_reviews = DB::table('Tips')
    ->select('review', 'username')
    ->where('SightId', $sightid)
    ->get();
                return view('sightdetails')
                ->with('searchresult', $searchresults)
                ->with('get_experience', $get_experience)
                ->with('sightreviews', $sightreviews)
                ->with('faq', $faq)
                ->with('tips_reviews', $tips_reviews)
                ->with('sloc', $pt)
                ->with('locationPatent', $locationPatent)
                ->with('nearby_sight', $nearby_sight)
                ->with('nearbyatt', $nearbyatt)
                ->with('getcat', $getcat)
                ->with('gettiming', $gettiming)
                ->with('nearby_hotel', $nearby_hotel)
                ->with('breadcumb', $breadcumb)
                ->with('Sight_image', $Sight_image)
                ->with('type', 'explore')
                ->with('lname', $lname)
                ->with('get_nearby_rest', $get_nearby_rest)
                ->with('lslug', $lslug)
                ->with('lslugid', $lslugid)
                ->with('timings', $timings)
                ->with('currentDay', $currentDay)
                ->with('todayStartTime', $todayStartTime)
                ->with('todayEndTime', $todayEndTime)
                ->with('country', $country)
                ->with('sightQuesAns', $sightQuesAns)
                ->with('locationId', $locationId);

    }

  public function save_sight_nb_hotel(request $request){

    $latitude = $request->get('Latitude');
    $longitude = $request->get('Longitude');
    $sightId = $request->get('sightId');
    $nbh = 0;
    $nbs = 0;
    $nearatt = 0;
    $nearrest = 0;

    if($latitude != "" && $longitude !=""){
        $get_nearby_hotel = DB::table('Sight_nearby_hotels')->where('SightId',$sightId)->get();

        if (!$get_nearby_hotel->count() >= 4) {
            $nbh = 1;
            $searchradius = 5;

                // Calculate bounding box for initial filtering
            $lat_range = $searchradius / 111.045;
            $lng_range = $searchradius / (111.045 * cos(deg2rad($latitude)));

            $nearby_hotel = DB::table("TPHotel")
                ->join('Temp_Mapping as m', 'm.LocationId', '=', 'TPHotel.location_id')
                ->select([
                    'm.slugid',
                    'TPHotel.id',
                    'TPHotel.name',
                    'TPHotel.location_id',
                    'TPHotel.slug',
                    'TPHotel.address',
                    'TPHotel.pricefrom',
                    'TPHotel.stars',
                    'TPHotel.hotelid',
                    DB::raw("ROUND(
                        6371 * acos(
                            LEAST(1, cos(radians({$latitude}))
                            * cos(radians(TPHotel.Latitude))
                            * cos(radians(TPHotel.longnitude) - radians({$longitude}))
                            + sin(radians({$latitude}))
                            * sin(radians(TPHotel.Latitude)))
                        ), 2
                    ) AS distance")
                ])
                // Add bounding box filter to reduce initial dataset
                ->whereRaw('TPHotel.Latitude BETWEEN ? AND ?', [
                    $latitude - $lat_range,
                    $latitude + $lat_range
                ])
                ->whereRaw('TPHotel.longnitude BETWEEN ? AND ?', [
                    $longitude - $lng_range,
                    $longitude + $lng_range
                ])
                ->having('distance', '<=', $searchradius)
                ->orderBy('distance')
                ->limit(4)
                ->get();

            if(!$nearby_hotel->isEmpty()){
                foreach ($nearby_hotel as $nearby_hotels) {
                    $data3= array(
                        'name'=>$nearby_hotels->name,
                        'slug'=>$nearby_hotels->slug,
                        'hotelid'=>$nearby_hotels->id,
                        'location_id'=>$nearby_hotels->slugid,
                        'distance'=>round($nearby_hotels->distance,2),
                        'radius'=>$searchradius,
                        'address'=>$nearby_hotels->address,
                        'SightId'=>$sightId,
                        'dated'=>now(),
                        'pricefrom'=>$nearby_hotels->pricefrom,
                        'stars'=>$nearby_hotels->stars,
                        'hotel_id'=>$nearby_hotels->hotelid,
                    );
                    $insertdata3 = DB::table('Sight_nearby_hotels')->insert($data3);
                }
            }
        }

        //Nearby Attractions
        $get_nearby_sight = DB::table('Sight_nbsight')->where('Sid',$sightId)->get();

        if (!$get_nearby_sight->count() >= 4) {
            $nearatt = 1;
            $sradius = 5;

                try {
                    // First, create a temporary table with pre-calculated distances
                    DB::statement("
                        CREATE TEMPORARY TABLE temp_nearby_sights AS
                        SELECT
                            s.SightId,
                            s.Title,
                            l.slugid,
                            s.Slug,
                            s.Address,
                            st.timings,
                            s.TAAggregateRating,
                            c.Title as ctitle,
                            ROUND(
                                6371 * acos(
                                    cos(radians({$latitude}))
                                    * cos(radians(s.Latitude))
                                    * cos(radians(s.Longitude) - radians({$longitude}))
                                    + sin(radians({$latitude}))
                                    * sin(radians(s.Latitude))
                                ), 2
                            ) AS distance
                        FROM Sight s
                        LEFT JOIN SightTiming st ON st.SightId = s.SightId
                        LEFT JOIN SightCategory sc ON sc.SightId = s.SightId
                        LEFT JOIN Category c ON c.CategoryId = sc.CategoryId
                        LEFT JOIN Location l ON l.LocationId = s.LocationId
                        WHERE s.SightId != ?
                        AND s.Latitude BETWEEN ? AND ?
                        AND s.Longitude BETWEEN ? AND ?
                        HAVING distance <= ?
                        ORDER BY distance
                        LIMIT 4
                    ", [
                        $sightId,
                        $latitude - ($sradius / 111.045),
                        $latitude + ($sradius / 111.045),
                        $longitude - ($sradius / (111.045 * cos(deg2rad($latitude)))),
                        $longitude + ($sradius / (111.045 * cos(deg2rad($latitude)))),
                        $sradius
                    ]);

                    // Then fetch from temporary table
                    $nearbyatt = DB::table('temp_nearby_sights')->get();

                    // Drop temporary table
                    DB::statement("DROP TEMPORARY TABLE IF EXISTS temp_nearby_sights");

                } catch (\Exception $e) {
                    $nearbyatt = collect();
                }

            if(!$nearbyatt->isEmpty()){
                $recordWithLargestDistance = $nearbyatt->last();
                $largestDistance = round($recordWithLargestDistance->distance, 2);
                $within = $this->getWithinRadius($largestDistance);

                foreach ($nearbyatt as $nearbyatts) {
                    $data1= array(
                        'Title'=>$nearbyatts->Title,
                        'SightId'=>$nearbyatts->SightId,
                        'Slug'=>$nearbyatts->Slug,
                        'LocationId'=>$nearbyatts->slugid,
                        'distance'=>round($nearbyatts->distance,2),
                        'radius'=>$within,
                        'Sid'=>$sightId,
                        'ctitle'=>$nearbyatts->ctitle,
                        'Address'=>$nearbyatts->Address,
                        'timings'=>$nearbyatts->timings,
                        'TAAggregateRating'=>$nearbyatts->TAAggregateRating,
                        'dated'=>now(),
                    );
                    $insertdata3 = DB::table('Sight_nbsight')->insert($data1);
                }
            }
        }

        //nearby restaurant
        $get_nearby_rest = DB::table('Sight_nearby_restaurant')->where('SightId',$sightId)->get();

        if (!$get_nearby_rest->count() >= 4) {
            $nearrest = 1;
            $restradus = 5;
            $nearby_rest = DB::table("Restaurant as r")
                ->leftJoin('RestaurantReview as rr', 'r.RestaurantId', '=', 'rr.RestaurantId')
                ->select('r.Title','r.TATrendingScore','r.slugid','r.RestaurantId','r.Slug',
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(r.Latitude))
                    * cos(radians(r.Longitude) - radians(" . $longitude . "))
                    + sin(radians(" . $latitude . "))
                    * sin(radians(r.Latitude))) AS distance"),
                    DB::raw("COUNT(rr.RestaurantReviewId) as review_count"))
                ->groupBy("r.RestaurantId")
                ->having('distance', '<=', $restradus)
                ->orderBy('distance')
                ->limit(4)
                ->get();

            if(!$nearby_rest->isEmpty()){
                $data_rest = [];
                foreach ($nearby_rest as $restaurant) {
                    $data_rest[] = [
                        'SightId' => $sightId,
                        'RestaurantId' => $restaurant->RestaurantId,
                        'radius' => $restradus,
                        'slugid' => $restaurant->slugid,
                        'Slug' => $restaurant->Slug,
                        'Title' => $restaurant->Title,
                        'distance' => $restaurant->distance,
                        'TATrendingScore' => $restaurant->TATrendingScore,
                        'review_count' => $restaurant->review_count,
                    ];
                }
                DB::table('Sight_nearby_restaurant')->insert($data_rest);
            }
        }
    }

    if($nbh == 1 ||  $nbs == 1 || $nearatt==1 || $nearrest==1){
        $nearby_hotel = DB::table('Sight_nearby_hotels')->where('SightId',$sightId)->get();
        $html3 = view('explore_results.sight_nearby_hotels',['nearby_hotel'=>$nearby_hotel])->render();

        $nearbyatt = DB::table('Sight_nbsight')->where('Sid',$sightId)->get();
        $nearbyattCount = $nearbyatt->count();
        $html4 = view('explore_results.nearby_attraction',['nearbyatt'=>$nearbyatt,'nearbyattCount'=> $nearbyattCount])->render();
        $html4 = (string) $html4;

        $nearby_rest = DB::table('Sight_nearby_restaurant')->where('SightId',$sightId)->get();
        $nearbyattrest = $nearby_rest->count();
        $html5 = view('explore_results.nearby_restaurant',['get_nearby_rest'=>$nearby_rest,'nearbyattrest'=>$nearbyattrest])->render();
        $html5 = (string) $html5;

        return response()->json(['html' => $html3,'html4'=>$html4,'html5'=>$html5]);
    }
}

private function getWithinRadius($distance): int
{
    if ($distance < 1) return 1;
    if ($distance < 5) return 5;
    if ($distance < 10) return 10;
    if ($distance < 20) return 20;
    if ($distance < 50) return 50;
    return 0;
}


    public function landing()
    {
       // $searchresults = DB::select("CALL landingpage($slug)");

        //echo "<pre>";
       // print_r($searchresults);

        print_r(now());


       return view('landingpage');
    }


  public function hotel_list($segment, Request $request)
    {

   	  $segment_parts = explode('-', $segment);
        if (!empty($segment_parts[0]) && is_numeric($segment_parts[0]) && strlen($segment_parts[0]) < 5) {
            $segment_parts[0] = str_pad($segment_parts[0], 5, '0', STR_PAD_LEFT);
            $segment = implode('-', $segment_parts);
            return redirect()->route('hotel.list', [$segment]);
       	 }

         // Initialize variables
        $id = null;
        $slug = null;
        $desiredId = null;
        $lslugid = "";
        $lslug = "";
        $st = "";
        $amenity = "";
        $price = "";
        $reviewscore = "";
        $filtertype = null;
        $amenity_slugs = null;
        $neighborhood_slugs = null;
        $propertytype_slugs = null;
        $redirect_needed = false;
        $sight_slugs = null;

          // Split segment by '-' to separate all parts
        $parts = explode('-', $segment);

        if (!empty($parts)) {
            // Get the ID (first part)
            $id = array_shift($parts);

            // Process remaining parts
            $city_parts = [];
            $filter_parts = [];
            $special_filters = [];
            $processed_amenities = [];
            $processed_neighborhoods = [];
            $processed_propertyTypes = [];
            $processed_sights = [];
            $current_part_type = null;

            foreach ($parts as $part) {

                // Skip if this part is already processed as a name
                if (isset($processed_amenities[$part]) || isset($processed_neighborhoods[$part]) || isset($processed_sights[$part]) || isset($processed_propertyTypes[$part])) {
                    continue;
                }

                // Check for special filters first
                if (preg_match('/^st\d+$/', $part)) {
                    $st = trim(str_replace('st', '', $part));
                    $filtertype = $part;
                    $special_filters[] = $part;
                }
                elseif (preg_match('/^rs\d+$/', $part)) {
                    $reviewscore = trim(str_replace('rs', '', $part));
                    $filtertype = $part;
                    $special_filters[] = $part;
                }
            elseif (in_array($part, ['free_cancellation', 'parking', 'Internet', 'breakfast'])) {
        // Debug logging

        $amenity = $part;
        $filtertype = $part;
        $special_filters[] = $part;

        // Map text-based amenities to their IDs
        switch ($part) {
            case 'free_cancellation':
                $amenity_ids[] = 1; // Replace with actual ID from your database
                break;
            case 'parking':
                $amenity_ids[] = 2; // Replace with actual ID from your database
                break;
            case 'Internet':
                $amenity_ids[] = 3; // Replace with actual ID from your database
                break;
            case 'breakfast':
                $amenity_ids[] = 4; // Replace with actual ID from your database
                break;
        }
    }
                elseif (preg_match('/^price\d+$/', $part)) {
                    $price = trim(str_replace('price', '', $part));
                    $filtertype = $part;
                    $special_filters[] = $part;
                }
                // Check for property type with number (e.g., pt12)
    elseif (preg_match('/^pt(\d+)$/', $part, $matches)) {
        $propertyType_id = $matches[1];

        // Skip if we've already processed this property type
        if (isset($processed_propertyTypes[$part])) {
            continue;
        }

        $propertyType_info = DB::table('TPHotel_types')
            ->select('type')
            ->where('hid', $propertyType_id)
            ->first();

        if ($propertyType_info && $propertyType_info->type) {
            // Generate slug from the type field - use same pattern as amenities and neighborhoods
            $propertyType_slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower($propertyType_info->type));
            $propertyType_slug = trim($propertyType_slug, '-');

            // Check if this property type is already in the URL
            $property_part = $part . '-' . $propertyType_slug;
            if (!strpos($segment, $property_part)) {
                $filter_parts[] = $property_part;
                $redirect_needed = true;
            } else {
                $filter_parts[] = $property_part;
            }

            // Mark this property type as processed to avoid duplicates
            $processed_propertyTypes[$part] = true;

            // Store just the part (pt4) without the slug for later use
            if ($propertytype_slugs === null) {
                $propertytype_slugs = $part;
            } else {
                $propertytype_slugs .= '-' . $part;
            }
        } else {
            $filter_parts[] = $part;
        }
        $current_part_type = 'propertyType';
    }
                // Check for amenity with number (e.g., a33)
                elseif (preg_match('/^a[a-z]+(\d+)$/', $part, $matches)) {
                    $amenity_id = $matches[1];

                    // Skip if we've already processed this amenity
                    if (isset($processed_amenities[$part])) {
                        continue;
                    }

                    $amenity_info = DB::table('TPHotel_amenities')
                        ->select('name', 'slug')
                        ->where('id', $amenity_id)
                        ->first();

                    if ($amenity_info && $amenity_info->name) {
                        $amenity_name = str_replace(' ', '-', strtolower($amenity_info->name));
                        // Only redirect if the name isn't already in the URL
                        if (!strpos($segment, $part . '-' . $amenity_name)) {
                            $filter_parts[] = $part . '-' . $amenity_name;
                            $redirect_needed = true;
                        } else {
                            $filter_parts[] = $part . '-' . $amenity_name;
                        }
                        $processed_amenities[$part] = true;

                        if ($amenity_slugs === null) {
                            $amenity_slugs = $part;
                        } else {
                            $amenity_slugs .= '-' . $part;
                        }
                    } else {
                        $filter_parts[] = $part;
                    }
                    $current_part_type = 'amenity';
                }
                // Check for neighborhood with number (e.g., n5)
                elseif (preg_match('/^n[a-z]+(\d+)$/', $part, $matches)) {
                    $neighborhood_id = $matches[1]; // Extracts only the numeric part


                    // Skip if we've already processed this neighborhood
                    if (isset($processed_neighborhoods[$part])) {
                        continue;
                    }

    $neighborhood_info = DB::table('Neighborhood')
        ->select('NeighborhoodId', 'Name', 'slug', 'LocationID') // Make sure to select LocationId
        ->where('NeighborhoodId', $neighborhood_id)
        ->first();

    if (!$neighborhood_info) {
        // If the neighborhood is not found, show a 404 error
        abort(404);
    }

    // Fetch the LocationId from the Location table using the slugid
    $location_info = DB::table('Location')
        ->select('LocationId')
        ->where('slugid', $id) // Assuming $id is the slugid
        ->first();

    if ($location_info && $neighborhood_info->LocationID == $location_info->LocationId) {
        // Proceed to show the neighborhood page
    } else {
        // Handle the case where the neighborhood does not belong to the location
        abort(404); // Show a 404 error if the neighborhood does not belong to the location
    }
                    if ($neighborhood_info && $neighborhood_info->Name) {
                       $neighborhood_name = preg_replace('/[\/\(\)]/', '-', strtolower($neighborhood_info->Name));

                        $neighborhood_name = str_replace(' ', '-', strtolower($neighborhood_name));
                        // Only redirect if the name isn't already in the URL
                        if (!strpos($segment, $part . '-' . $neighborhood_name)) {
                            $filter_parts[] = $part . '-' . $neighborhood_name;
                            $redirect_needed = true;
                        } else {
                            $filter_parts[] = $part . '-' . $neighborhood_name;
                        }
                        $processed_neighborhoods[$part] = true;

                        if ($neighborhood_slugs === null) {
                            $neighborhood_slugs = $part;
                        } else {
                            $neighborhood_slugs .= '-' . $part;
                        }
                    } else {
                        $filter_parts[] = $part;
                    }
                    $current_part_type = 'neighborhood';
                }


                elseif (preg_match('/^sqx(\d+)$/', $part, $matches)) {
                    $sight_id = $matches[1]; // Extracts only the numeric part

                    // Skip if we've already processed this sight
                    if (isset($processed_sights[$part])) {
                        continue;
                    }

                    $sight = DB::table('Sight')
                    ->select('Title', 'SightId','Location_id', 'LocationId','Latitude','Longitude')
                    ->where('SightId', $sight_id)
                    ->first();


                if (!$sight) {
                    // If the sight is not found, show a 404 error
                    abort(404);
                }

                // Now check if this sight belongs to the correct location
                if ($sight->Location_id != $id) {
                    // If the sight doesn't belong to this location, show 404
                    abort(404);
                }

                    if ($sight && $sight->Title) {
                        $sight_name = preg_replace('/[^a-z0-9-]+/', '-', strtolower($sight->Title));
                        $sight_name = str_replace(' ', '-', strtolower($sight_name));

                        // Only redirect if the name isn't already in the URL
                        if (!strpos($segment, $part . '-' . $sight_name)) {
                            $filter_parts[] = $part . '-' . $sight_name;
                            $redirect_needed = true;
                        } else {
                            $filter_parts[] = $part . '-' . $sight_name;
                        }
                        $processed_sights[$part] = true;

                        if ($sight_slugs === null) {
                            $sight_slugs = $part;
                        } else {
                            $sight_slugs .= '-' . $part;
                        }
                    } else {
                        $filter_parts[] = $part;
                    }
                    $current_part_type = 'sight';
                }

                // If it's a text part following an amenity, neighborhood, property type, or sight ID, skip it
                elseif (preg_match('/^[a-zA-Z\-]+$/', $part) &&
                       ($current_part_type == 'amenity' || $current_part_type == 'neighborhood' || $current_part_type == 'sight' || $current_part_type == 'propertyType')) {
                    continue;
                }
                // If none of the above, it's part of the city name
                else {
                    $city_parts[] = $part;
                    $current_part_type = 'city';
                }
            }

            // Combine city parts to form the slug
            if (!empty($city_parts)) {
                $slug = implode('-', $city_parts);
            }

            // If we need to redirect to include names
            if ($redirect_needed) {
                // Build the new URL maintaining the correct order
                $new_url_parts = ['ho'];

                // Add ID first
                if ($id) {
                    $new_url_parts[] = $id;
                }

                // Add city parts
                if (!empty($city_parts)) {
                    $new_url_parts[] = implode('-', $city_parts);
                }

                // Add special filters
                if (!empty($special_filters)) {
                    $new_url_parts = array_merge($new_url_parts, $special_filters);
                }

                // Add amenities and neighborhoods with their names
                if (!empty($filter_parts)) {
                    $new_url_parts = array_merge($new_url_parts, array_unique($filter_parts));
                }

                $new_url = implode('-', $new_url_parts);

                // Only redirect if the URL is actually different
                if ($new_url !== 'ho-' . $segment) {

                    return redirect($new_url);
                }
            }
        }

        // Check if location exists in Temp_Mapping by slugid
        $locationExists = DB::table('Temp_Mapping')->where('slugid', $id)->first();

        if ($locationExists) {
            // Fix: Set the slug variable to match what's in the database
            // This ensures we're using the correct slug throughout the function
            if (empty($slug) || $slug != $locationExists->slug) {
                $slug = $locationExists->slug;
            }
    // Get the correct slug from the database
    $correctSlug = $locationExists->slug;

    // Construct what the URL should be with just the location part
    $baseCorrectUrl = 'ho-' . $id . '-' . $correctSlug;

    // If the URL doesn't match exactly, redirect to the correct one but preserve filters
    $currentUrl = 'ho-' . $segment;
    if (strpos($currentUrl, $baseCorrectUrl) === false) {
        // We need to redirect, but preserve any filters

        // Extract all parts after the location ID
        $allParts = explode('-', $segment);
        array_shift($allParts); // Remove the ID part

        // Extract the location slug parts and filter parts
        $filterParts = [];
        $locationSlugParts = [];

        // Process all parts to separate location slug from filters
        foreach ($allParts as $part) {
            // Check if this part is a filter
            if (preg_match('/^(st\d+|rs\d+|price\d+|pt\d+|a[a-z]*\d+|n[a-z]*\d+|sqx\d+)/', $part) ||
                in_array($part, ['free_cancellation', 'parking', 'Internet', 'breakfast']) ||
                (isset($processed_amenities[$part]) || isset($processed_neighborhoods[$part]) || isset($processed_sights[$part]) || isset($processed_propertyTypes[$part]))) {
                $filterParts[] = $part;
            }
            // If it's a descriptive part following a filter ID, add it to filters
            elseif (preg_match('/^[a-zA-Z\-]+$/', $part) &&
                   ($current_part_type == 'amenity' || $current_part_type == 'neighborhood' || $current_part_type == 'sight' || $current_part_type == 'propertyType')) {
                $filterParts[] = $part;
            }
            // Otherwise it's part of the location slug
            else {
                $locationSlugParts[] = $part;
            }
        }

        // Build the new URL with correct location slug and preserved filters
        $correctUrl = $baseCorrectUrl;

        // Add filters if they exist
        if (!empty($filterParts)) {
            $correctUrl .= '-' . implode('-', $filterParts);
        }

        return redirect($correctUrl);
    }
}

       // Parse amenity IDs
    $amenity_ids = [];
    if ($amenity_slugs) {
        $amenity_parts = explode('-', $amenity_slugs);

        // Create a cache key based on the amenity slugs
        $amenityCacheKey = "amenity_ids_" . md5(implode('_', $amenity_parts));

        // Cache amenity IDs for 24 hours (86400 seconds)
        $amenity_ids = Cache::remember($amenityCacheKey, 86400, function() use ($amenity_parts) {
            $ids = [];
            foreach ($amenity_parts as $part) {
                if (preg_match('/^a[a-z]+(\d+)$/', $part, $matches)) {
                    $ids[] = $matches[1]; // Store the ID value
                }
            }
            return $ids;
        });
    }

    // Parse property type IDs
    $propertyType_ids = [];
    $propertyType_info = collect();
    if ($propertytype_slugs) {
        $propertyType_parts = explode('-', $propertytype_slugs);

        // Create a cache key based on the property type slugs
        $propertyTypeCacheKey = "propertyType_data_" . md5(implode('_', $propertyType_parts));


        // Cache property type data for 24 hours (86400 seconds)
        $propertyTypeData = Cache::remember($propertyTypeCacheKey, 86400, function() use ($propertyType_parts) {
            $ids = [];
            $propertyTypes = collect();

            foreach ($propertyType_parts as $part) {
                if (preg_match('/^pt(\d+)$/', $part, $matches)) {
                    $propertyType_id = $matches[1];

                    $propertyType = DB::table('TPHotel_types')
                        ->select('hid', 'type', 'id')
                        ->where('hid', $propertyType_id)
                        ->first();

                    if ($propertyType) {
                        $ids[] = $propertyType_id;
                        $propertyTypes->push($propertyType);
                    }
                }
            }

            return [
                'ids' => $ids,
                'propertyTypes' => $propertyTypes
            ];
        });

        $propertyType_ids = $propertyTypeData['ids'];
        $propertyType_info = $propertyTypeData['propertyTypes'];

    }

    // Get hotel property types for the sidebar
    $hotelPropertyTypes = $this->getHotelPropertyTypes($id, $lslug);

    $neighborhood_ids = [];
    $neighborhoods = collect();
    if ($neighborhood_slugs) {
        $neighborhood_parts = explode('-', $neighborhood_slugs);

        // Create a cache key based on the neighborhood slugs
        $neighborhoodCacheKey = "neighborhood_data_" . md5(implode('_', $neighborhood_parts));

        // Cache neighborhood data for 24 hours (86400 seconds)
        $neighborhoodData = Cache::remember($neighborhoodCacheKey, 86400, function() use ($neighborhood_parts) {
            $ids = [];
            $neighborhoods = collect();

            foreach ($neighborhood_parts as $part) {
                if (preg_match('/^n[a-z]+(\d+)$/', $part, $matches)) {
                    $neighborhood_id = $matches[1];

                    $neighborhood = DB::table('Neighborhood')
                        ->select('NeighborhoodId', 'Name', 'LocationID', 'Latitude', 'Longitude')
                        ->where('NeighborhoodId', $neighborhood_id)
                        ->first();

                    if ($neighborhood) {
                        $ids[] = $neighborhood_id;
                        $neighborhoods->push($neighborhood);
                    }
                }
            }

            return [
                'ids' => $ids,
                'neighborhoods' => $neighborhoods
            ];
        });

        $neighborhood_ids = $neighborhoodData['ids'];
        $neighborhoods = $neighborhoodData['neighborhoods'];
    }

    $sight_hotel_ids = [];
    $sights = collect();
    if ($sight_slugs) {
        $sight_parts = explode('-', $sight_slugs);

        // Create a cache key based on the sight slugs
        $sightCacheKey = "sight_data_" . md5(implode('_', $sight_parts));

        // Cache sight data for 24 hours (86400 seconds)
        $sightData = Cache::remember($sightCacheKey, 86400, function() use ($sight_parts) {
            $ids = [];
            $sights = collect();

            foreach ($sight_parts as $part) {
                if (preg_match('/^sqx(\d+)$/', $part, $matches)) {
                    $sight_id = $matches[1];

                    $sight = DB::table('Sight')
                        ->select('SightId', 'Title', 'LocationId', 'Latitude', 'Longitude')
                        ->where('SightId', $sight_id)
                        ->first();

                    if ($sight) {
                        $ids[] = $sight_id;
                        $sights->push($sight);
                    }
                }
            }

            return [
                'ids' => $ids,
                'sights' => $sights
            ];
        });

        $sight_ids = $sightData['ids'];
        $sights = $sightData['sights'];
    }

        // Set the variables for the rest of the function
        $explocname = $slug;
        $slgid = $id;
        $desiredId = $id;

       $metadata =collect();
        $filterPattern = '/(fl[a-z]+[0-9a-zA-Z_]+)/';


            preg_match_all($filterPattern, $slug, $matches);

            if (!empty($matches[0])) {
                foreach ($matches[0] as $filter) {

                    $filterType = substr($filter, 0, 3);
                    $filterValue= substr($filter, 3);


                    $slug = str_replace($filter, '', $slug);
                }


                $slug = trim($slug, '-');
            }

            $explocname = $slug;
            $slgid = $id;
            $desiredId = $id;
            $agencyData =[];

            $searchresultscount=0;
            $guest =null;
            $Tid =null;
            $hotel = null;
            $gethoteltype = collect();
            $getlocationexp = collect();
            $searchresults =collect();
            $countryname ="";
            $lname ="";
            $hlid="";
            $pagetype="";

            $chkin = $request->get('checkin');
            $checout = $request->get('checkout');

            $count_result = null;

            // Consolidate location information queries into a single cached query
            $locationInfo = Cache::remember("location_info_{$id}", 3600, function() use ($id) {
                return DB::table('Location')
                    ->select('LocationId', 'LocationLevel', 'ParentId', 'Slug', 'slugid',
                            'heading', 'headingcontent', 'show_in_index', 'Name',
                            'HotelTitleTag', 'HotelMetaDescription', 'MetaTagTitle', 'MetaTagDescription')
                    ->where('slugid', $id)
                    ->first();
            });

            // Set variables based on the consolidated location query
            if ($locationInfo) {
                $lslug = $locationInfo->Slug;
                $lslugid = $locationInfo->slugid;
                $metadata = collect([$locationInfo]);
                $location_info = (object)[
                    'heading' => $locationInfo->heading,
                    'headingcontent' => $locationInfo->headingcontent
                ];
            }

            // Optimize nearby places query with better caching
            $nearbyPlaces = Cache::remember("nearby_places_{$id}", 86400, function() use ($id) {
                return DB::table('Sight')
                    ->select('SightId', 'Title', 'LocationId', 'Latitude', 'Longitude','popularity_score')
                	->whereNotNull('Latitude')
                    ->where('Location_id', $id)
                    ->orderBy('Avg_MonthlySearches', 'desc')
                    ->get();
            });

            if( $chkin !="" && $checout !=""){
                   session()->forget('filterd');
                $pagetype="withdate";
                $chkin = $this->formatDate($chkin);
                $checout = $this->formatDate($checout);
                $getval =  $chkin .'_'. $checout;
                $rooms = $request->get('rooms');
                $guest = $request->get('guest')  ?: 1;
                $slug = $segment;
                $locationid =  $request->get('locationid')?: $request->get('lid');
                session([
                    'checkin' => $getval,
                    'rooms' => $rooms,
                    'guest' => $guest,
                    'slug' => $slug,
                    'slugid'=>$locationid,
                ]);
                $fullname = "";
                $tplocationid =$locationid;
                $getloclink = Cache::remember("location_mapping_{$locationid}", 3600, function() use ($locationid) {
                    return DB::table('Temp_Mapping')
                        ->select('LocationId', 'Tid','cityName','countryName','fullName')
                        ->where('slugid', $locationid)
                        ->first();
                });

                // return   print_r($getloclink);
                if ($getloclink) {
                    $locationid = $getloclink->LocationId;
                    $tplocationid = $locationid;
                    $Tid = $getloclink->Tid;
                }


                $locationPatent =[];

                $metadata = Cache::remember("metadata_{$slgid}", 86400, function() use ($slgid) {
                    return DB::table('Location')
                        ->select('HotelTitleTag','HotelMetaDescription','MetaTagTitle','MetaTagDescription')
                        ->where('slugid', $slgid)
                        ->get();
                });
                $gethoteltype = Cache::remember('hotel_types', 604800, function() {
                    return DB::table('TPHotel_types')
                        ->select('hid', 'name', 'slug')
                        ->orderBy('hid', 'desc')
                        ->get();
                });

              //  $getloc = DB::table('TPLocations')->select('fullName','cityName','countryName')->where('id',$locationid)->first();

                if (!$getloclink) {
                    abort(404, 'Not FOUND');
                }
                if($fullname ==""){
                    $fullname = $getloclink->fullName;
                }
                $lname = $getloclink->cityName;
                $countryName = $getloclink->countryName;

                $countryname  = $getloclink->countryName;

                $getloclink =collect();
                $getcontlink =collect();

                //start session
                $searchEntry = [
                    'checkin' => $chkin,
                    'checkout' => $checout,
                    'rooms' => $rooms,
                    'guest' => $guest,
                    'slug' => $segment,
                    'locationid' => $desiredId,
                    'fullname' => $fullname,
                ];
                $recentSearches = session('recent_searches', []);
                $exists = false;
                foreach ($recentSearches as $entry) {
                    if ($entry['locationid'] == $searchEntry['locationid']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $recentSearches[] = $searchEntry;
                    if (count($recentSearches) > 4) {
                        $recentSearches = array_slice($recentSearches, -4);
                    }
                    session(['recent_searches' => $recentSearches]);
                }
                //  $end =  date("H:i:s");
                //  return $start.'=='.$end;
                //end session

                //api code
  $checkinDate =  $chkin;
                $checkoutDate = $checout;
                $adultsCount = $guest;
                $customerIP = '49.156.89.145';
                $childrenCount = '1';
                $chid_age = '10';
                $lang = 'en';
                $currency ='USD';
                $waitForResult ='0';
                $iata= $tplocationid;
                $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
                $TRAVEL_PAYOUT_MARKER = "299178";
                $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
                    $checkinDate.":".
                    $checkoutDate.":".
                    $chid_age.":".
                    $childrenCount.":".
                    $iata.":".
                    $currency.":".
                    $customerIP.":".
                    $lang.":".
                    $waitForResult;
                $signature = md5($SignatureString);

                $url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;


                $response = Http::withoutVerifying()->get($url);


                if ($response->successful()) {
                    $data = json_decode($response);
                    if(!empty($data)){
                        $searchId = $data->searchId;
                        $limit = 40;
                        $offset=0;
                        $roomsCount=0;
                        $sortAsc=0;
                        $sortBy='stars';
                        $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
                        $sig2 =  md5($SignatureString2);
                        $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=40&sortBy=stars&sortAsc=0&roomsCount=0&offset=0&marker=299178&signature='.$sig2;


                        //new code

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

                              $hotel = $response2->object();

                              // Save raw hotel data to TPRoomtype for debugging
                              if (isset($hotel->result) && is_array($hotel->result)) {
                                  $hotelIdsFromApi = array_column($hotel->result, 'id');

                                  // First, clear any old raw data for these hotels to avoid duplicates
                                  if (!empty($hotelIdsFromApi)) {
                                      DB::table('TPRoomtype')->whereIn('hotelid', $hotelIdsFromApi)->delete();
                                  }

                                  $rawDataToInsert = [];
                                  foreach ($hotel->result as $hotelData) {
                                      $rawDataToInsert[] = [
                                          'hotelid' => $hotelData->id,
                                          'Roomdesc' => json_encode($hotelData),
                                          'flag' => 0,
                                          'created_at' => now(),
                                      ];
                                  }

                                  if (!empty($rawDataToInsert)) {
                                      DB::table('TPRoomtype')->insert($rawDataToInsert);

                                  }

                                  // Now, process the staged data
                                  $stagedHotels = DB::table('TPRoomtype')->whereIn('hotelid', $hotelIdsFromApi)->where('flag', 0)->get();


                                  foreach ($stagedHotels as $stagedHotel) {
                                      try {
                                          $hotelData = json_decode($stagedHotel->Roomdesc);

                                          if (json_last_error() !== JSON_ERROR_NONE) {
                                              continue; // Skip to next hotel
                                          }

                                          if (is_null($hotelData)) {
                                              continue;
                                          }


                                          $roomsToInsert = [];

                                          if (isset($hotelData->rooms) && is_array($hotelData->rooms) && count($hotelData->rooms) > 0) {
                                              foreach ($hotelData->rooms as $room) {
                                                  $roomsToInsert[] = [
                                                      'hotelid' => $hotelData->id,
                                                      'roomType' => $room->roomName ?? $room->desc ?? $room->type ?? 'N/A',
                                                      'price' => $room->price ?? 0,
                                                      'refundable' => $room->options->refundable ?? 0,
                                                      'halfBoard' => $room->options->halfBoard ?? 0,
                                                      'ultraAllInclusive' => $room->options->ultraAllInclusive ?? 0,
                                                      'allInclusive' => $room->options->allInclusive ?? 0,
                                                      'freeWifi' => $room->options->freeWifi ?? 0,
                                                      'fullBoard' => $room->options->fullBoard ?? 0,
                                                      'deposit' => $room->options->deposit ?? 0,
                                                      'cardRequired' => $room->options->cardRequired ?? 0,
                                                      'breakfast' => $room->options->breakfast ?? 0,
                                                      'smoking' => $room->options->smoking ?? 0,
                                                      'viewSentence' => $room->options->viewSentence ?? '',
                                                      'beds' => isset($room->options->beds) ? (is_object($room->options->beds) || is_array($room->options->beds) ? array_sum((array)$room->options->beds) : (int)$room->options->beds) : 0,
                                                      'doublebed' => $room->options->doublebed ?? 0,
                                                      'twin' => $room->options->twin ?? 0,
                                                      'available' => $room->options->available ?? 0,
                                                      'agencyId' => $room->agencyId ?? 0,
                                                      'agencyName' => $room->agencyName ?? 'N/A',
                                                      'booking_url' => isset($room->fullBookingURL) ? $room->fullBookingURL : '',
                                                      'balcony' => 0, // Assuming not available from API
                                                      'privateBathroom' => 1, // Assuming available
                                                      'dt_created' => now(),
                                                      'amenities' => 0, // Default value
                                                      'view' => '',
                                                      'roomtypeid' => 0
                                                 ];
                                           	 $roomPrices = [];
												foreach ($rooms as $room) {
   												$roomPrices[] = [
        										'hotelid' => $hotelData->id,
        										'roomtype' => $room->roomName ?? $room->desc ?? $room->type ?? 'N/A',
       											'price' => $room->price ?? 0,
        										'search_hash' => $search_hash,
        										'checkin' => $chkin ?? null,
        										'checkout' => $checout ?? null,
        										'created_at' => now(),
        										'updated_at' => now(),
  											  ];
											}
											DB::table('RoomPrices')->insert($roomPrices);
                                              }
                                          } else {
                                          }

                                          if (!empty($roomsToInsert)) {

                                               // Filter out any 'total' fields from the data to prevent SQL errors
                                               $roomsToInsert = array_map(function($room) {
                                                   if (isset($room['total'])) {
                                                       unset($room['total']);
                                                   }
                                                   return $room;
                                               }, $roomsToInsert);

                                               DB::table('TPRoomtype_tmp')->insert($roomsToInsert);
                                              DB::table('TPRoomtype')->where('id', $stagedHotel->id)->update(['flag' => 1]);
                                          } else {
                                          }
                                      } catch (\Exception $e) {
                                      }
                                  }

                                  // Check if there are any unprocessed records before starting the second process
                                  $unprocessedRecordsCount = DB::table('TPRoomtype')
                                      ->whereIn('hotelid', $hotelIdsFromApi)
                                      ->where('flag', 0)
                                      ->count();

                                  // Only process if there are unprocessed records
                                  if ($unprocessedRecordsCount > 0) {
                                      // Now, process the data from TPRoomtype and insert into TPRoomtype_tmp

                                      $rawDataRecords = DB::table('TPRoomtype')
                                          ->whereIn('hotelid', $hotelIdsFromApi)
                                          ->where('flag', 0)
                                          ->get();

                                      // Only delete and reinsert if we actually have unprocessed records
                                      if (!empty($rawDataRecords) && $rawDataRecords->count() > 0) {
                                          // Get the specific hotel IDs that have unprocessed records
                                          $unprocessedHotelIds = $rawDataRecords->pluck('hotelid')->unique()->toArray();

                                          if (!empty($unprocessedHotelIds)) {
                                              DB::table('TPRoomtype_tmp')->whereIn('hotelid', $unprocessedHotelIds)->delete();
                                          }
                                      } else {
                                          $rawDataRecords = collect(); // Empty collection
                                      }
                                  } else {
                                      $rawDataRecords = collect(); // Empty collection
                                  }

                                  $roomsToInsert = [];
                                  foreach ($rawDataRecords as $record) {
                                       try {
                                           $hotelData = json_decode($record->Roomdesc);
                                           $hotelId = $record->hotelid;

                                           if (isset($hotelData->rooms) && is_array($hotelData->rooms)) {
                                               foreach ($hotelData->rooms as $room) {
                                                   // Process beds data to ensure it's an integer
                                                   $bedsValue = 0;
                                                   $doubleBedValue = 0;
                                                   $twinValue = 0;

                                                   if (isset($room->options->beds)) {
                                                       if (is_object($room->options->beds)) {
                                                           // Handle object format
                                                           if (isset($room->options->beds->double)) {
                                                               $doubleBedValue = (int)$room->options->beds->double;
                                                           }
                                                           if (isset($room->options->beds->twin)) {
                                                               $twinValue = (int)$room->options->beds->twin;
                                                           }
                                                           $bedsValue = $doubleBedValue + $twinValue;
                                                       } elseif (is_array($room->options->beds)) {
                                                           // Handle array format
                                                           $bedsValue = array_sum((array)$room->options->beds);
                                                       } else {
                                                           // Handle scalar value
                                                           $bedsValue = (int)$room->options->beds;
                                                       }
                                                   }

                                                   $roomsToInsert[] = [
                                                       'hotelid' => $hotelId,
                                                       'roomType' => $room->roomName ?? $room->desc ?? 'N/A',
                                                       'roomtypeid' => $room->internalTypeId ?? 0,
                                                       'breakfast' => isset($room->options->breakfast) ? (int)$room->options->breakfast : 0,
                                                       'available' => $room->options->available ?? 1,
                                                       'halfBoard' => isset($room->options->halfBoard) ? (int)$room->options->halfBoard : 0,
                                                       'ultraAllInclusive' => isset($room->options->ultraAllInclusive) ? (int)$room->options->ultraAllInclusive : 0,
                                                       'allInclusive' => isset($room->options->allInclusive) ? (int)$room->options->allInclusive : 0,
                                                       'refundable' => isset($room->options->refundable) ? (int)$room->options->refundable : 0,
                                                       'freeWifi' => isset($room->options->freeWifi) ? (int)$room->options->freeWifi : 1,
                                                       'fullBoard' => isset($room->options->fullBoard) ? (int)$room->options->fullBoard : 0,
                                                       'deposit' => isset($room->options->deposit) ? (int)$room->options->deposit : 0,
                                                       'smoking' => isset($room->options->smoking) ? (int)$room->options->smoking : 0,
                                                       'view' => $room->options->view ?? '',
                                                       'viewSentence' => $room->options->viewSentence ?? '',
                                                       'cardRequired' => isset($room->options->cardRequired) ? (int)$room->options->cardRequired : 0,
                                                       'beds' => $bedsValue,
                                                       'doublebed' => $doubleBedValue,
                                                       'twin' => $twinValue,
                                                       'balcony' => isset($room->options->balcony) ? (int)$room->options->balcony : 0,
                                                       'privateBathroom' => isset($room->options->privateBathroom) ? (int)$room->options->privateBathroom : 1,
                                                       'dt_created' => now(),
                                                       'images' => $room->images ?? '',
                                                       'price' => $room->price ?? 0,
                                                       'agencyId' => $room->agencyId ?? 0,
                                                       'agencyName' => $room->agencyName ?? '',
                                                       'amenities' => $room->amenities ?? 0,
                                                       'booking_url' => $room->bookingURL ?? '',
                                                   ];
                                               }
                                           }
                                       } catch (\Exception $e) {
                                           continue;
                                       }
                                   }
                                  if (!empty($roomsToInsert)) {
                                       try {
                                           // Filter out any 'total' fields from the data to prevent SQL errors
                                            $roomsToInsert = array_map(function($room) {
                                                if (isset($room['total'])) {
                                                    unset($room['total']);
                                                }
                                                return $room;
                                            }, $roomsToInsert);

                                            // Insert rooms in smaller batches to avoid potential issues
                                           $chunks = array_chunk($roomsToInsert, 50);
                                           foreach ($chunks as $chunk) {
                                               DB::table('TPRoomtype_tmp')->insert($chunk);
                                           }



                                           // Update the flag for processed records
                                           $processedIds = array_column($rawDataRecords->all(), 'id');
                                           DB::table('TPRoomtype')->whereIn('id', $processedIds)->update(['flag' => 1]);
                                       } catch (\Exception $e) {
                                       }
                                   } else {
                                  }
                              } //start agency code
                              $agencyData = [];
                              if (isset($hotel->result) && is_array($hotel->result)) {
                                  foreach ($hotel->result as $hotelData) {
                                      if (isset($hotelData->rooms) && is_array($hotelData->rooms)) {
                                          foreach ($hotelData->rooms as $room) {
                                              $agencyName = $room->agencyName;

                                              if (!in_array($agencyName, $agencyData)) {
                                                  $agencyData[] = $agencyName;
                                              }
                                          }
                                      }
                                  }
                              }
                               //end agency code

                              $idArray = array_column($hotel->result, 'id');
                              $idArray = array_filter($idArray, function ($ids) {
                                  return isset($ids);
                              });
                              $idArray = array_unique($idArray);

                              $cacheKey = "hotel_search_" . md5(implode('_', $idArray));

                              $searchresults = Cache::remember($cacheKey, 300, function() use ($idArray) {
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

                              // Transform the results if needed
                              $searchresults = $searchresults->map(function($hotel) {
                                  $hotel->amenity_info = !empty($hotel->amenity_info)
                                      ? $hotel->amenity_info
                                      : '';
                                  return $hotel;
                              });


                              $count_result = count($searchresults);
                           }

                      } catch (\Exception $e) {

                              $searchresults =collect();
                      }




                       // end new code

                    }

                } else {

                    //  return 2;
                }

                    /*breadcrumb*/
                $breadcrumbCacheKey = "breadcrumb_data_{$desiredId}";
                $breadcrumbData = Cache::remember($breadcrumbCacheKey, 86400, function() use ($desiredId) {
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


                // Set variables from cached data
                $getloclink = $breadcrumbData['getloclink'];
                $getcontlink = $breadcrumbData['getcontlink'];
                $locationPatent = $breadcrumbData['locationPatent'];
                $getlocationexp = $breadcrumbData['getlocationexp'];
                $lslug = $breadcrumbData['lslug'];
                $lslugid = $breadcrumbData['lslugid'];

                $neabyhotelwithswimingpool = Cache::remember("nearby_hotels_{$desiredId}", 86400, function() use ($desiredId) {
                    $swimmingPoolId = DB::table('TPHotel_amenities')->where('name', 'Swimming pool')->value('id');
                    return DB::table('TPHotel as h')
                        ->select('h.name', 'h.location_id', 'h.id', 'h.hotelid', 'h.slugid', 'h.slug',
                                 'h.OverviewShortDesc', 'h.rating', 'h.pricefrom', 'l.Name as Lname')
                        ->leftJoin('Location as l', 'l.slugid', '=', 'h.slugid')
                        ->whereRaw('FIND_IN_SET(?, h.facilities)', [$swimmingPoolId])
                        ->where('h.slugid', $desiredId)
                        ->whereNotNull('h.OverviewShortDesc')
                        ->orderBy('h.stars', 'desc')
                        ->limit(4)
                        ->get();
                });
                /*breadcrumb*/

            }else{
                $pagetype="withoutdate";
                $getloc = DB::table('Temp_Mapping as tm')
                    // ->leftjoin('TPLocations as l', 'l.id', '=', 'tm.LocationId')
                    // ,'l.location'
                    ->select('tm.LocationId','tm.cityName','tm.countryName')
                    ->where('tm.slugid', $desiredId)
                    ->where('tm.slug', $slug)
                    ->limit(1)
                    ->get();

                if($getloc->isEmpty()){
                    if($id){
                        $checkgetloc = DB::table('Temp_Mapping as tm')
                            ->select('tm.slugid','tm.slug')
                            ->where('tm.Tid', $id)
                            ->limit(1)
                            ->get();
                        if(!$checkgetloc->isEmpty()){
                            $id =  $checkgetloc[0]->slugid;
                            $slug = $checkgetloc[0]->slug;
                            return redirect()->route('hotel.list', [$id.'-'.$slug]);
                        }
                        // Redirect to the new URL
                        $checkgetloc2 = DB::table('Temp_Mapping as tm')
                            ->select('tm.slugid','tm.slug')
                            ->where('tm.LocationId', $id)
                            ->get();
                        if(!$checkgetloc2->isEmpty()){
                            $id =  $checkgetloc2[0]->slugid;
                            $slug = $checkgetloc[0]->slug;
                            return redirect()->route('hotel.list', [$id.'-'.$slug]);
                        }

                    }
                    abort(404,'Not found');
                }
                if(!$getloc->isEmpty()){
                    $desiredId =  $getloc[0]->LocationId;
                    $lname = $getloc[0]->cityName;
                    $countryname = $getloc[0]->countryName;
                }

                $locationid =  $desiredId;


                // header searchbar link
                $hlid =$desiredId;

                $locationgeo ="";



                $gethoteltype = Cache::remember('hotel_types', 604800, function() {
                    return DB::table('TPHotel_types')
                        ->orderBy('hid', 'desc')
                        ->get();
                });


                $getloclink =collect();


                $getloclink = DB::table('Temp_Mapping as tm')
                    ->join('Location as l', 'l.LocationId', '=', 'tm.Tid')
                    ->select('l.LocationLevel','l.ParentId','l.LocationId','l.Slug','tm.Tid')
                    ->where('tm.LocationId', $desiredId)
                    ->limit(1)
                    ->get();


                $getcontlink =collect();

                $getcontlink = DB::table('Country as co')
                    ->join('Location as l', 'l.CountryId', '=', 'co.CountryId')
                    ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
                    ->select('co.CountryId','co.Name','co.slug','cont.Name as cName','cont.CountryCollaborationId as contid')
                    ->where('l.LocationId', $getloclink[0]->LocationId)
                    ->get();




                $locationPatent = [];
                if(!$getloclink->isEmpty()){
                    $Tid = $getloclink[0]->Tid;
                    $locationid = $getloclink[0]->LocationId;
                    $getlocationexp = DB::table('Location')->select('slugid','LocationId','Name','Slug')->where('LocationId', $locationid)->get();

                    if (!$getloclink->isEmpty() &&  $getloclink[0]->LocationLevel != 1) {
                        $loopcount =  $getloclink[0]->LocationLevel;

                        $lociID = $getloclink[0]->ParentId;
                        for ($i = 1; $i < $loopcount; $i++) {
                            $getparents = DB::table('Location')->select('slugid','LocationId','Name','Slug','ParentId')->where('LocationId', $lociID)->get();
                            if (!empty($getparents)) {
                                $locationPatent[] = [
                                    'LocationId' => $getparents[0]->slugid,
                                    'slug' => $getparents[0]->Slug,
                                    'Name' => $getparents[0]->Name,
                                ];
                                if (!empty($getparents) && $getparents[0]->ParentId != "") {
                                    $lociID = $getparents[0]->ParentId;
                                }
                            } else {
                                break;
                            }
                        }
                    }
                }


             $metadata = DB::table('Location')->select('HotelTitleTag','HotelMetaDescription','MetaTagTitle','MetaTagDescription')->where('slugid', $slgid)->get();

             // Add this at the start of your function to initialize
                $searchresults = collect();

                // Then in your code where the search results query is:
                try {
                    // Create a more specific cache key that includes all filter parameters
                    $cacheKey = "search_results_" . md5(
                        $slgid . '_' .
                        $st . '_' .
                        $amenity . '_' .
                        $price . '_' .
                        $reviewscore . '_' .
                        implode('_', $amenity_ids ?? []) . '_' .
                        implode('_', $neighborhood_ids ?? []) . '_' .
                        implode('_', $propertyType_ids ?? [])
                    );

                    // Extend cache duration from 300 to 1800 seconds (30 minutes) for better performance
                    $searchresults = Cache::remember($cacheKey, 1800, function() use ($slgid, $st, $amenity, $price, $reviewscore, $amenity_ids, $neighborhood_ids, $propertyType_ids) {
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

                            if (!empty($propertyType_ids)) {
                                $query->whereIn('h.propertyType', $propertyType_ids);
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

                    $count_result = $searchresults->count();

                } catch (\Exception $e) {

                }
          //nearby hotels with swimming pool

               $neabyhotelwithswimingpool = Cache::remember("nearby_hotels_{$desiredId}", 2592000, function() use ($desiredId) {
                    $swimmingPoolId = DB::table('TPHotel_amenities')->where('name', 'Swimming pool')->value('id');
                    return DB::table('TPHotel as h')
                        ->select('h.name', 'h.location_id', 'h.id', 'h.hotelid', 'h.slugid', 'h.slug',
                                 'h.OverviewShortDesc','h.rating','h.pricefrom','l.Name as Lname')
                        ->leftJoin('Location as l', 'l.slugid', '=', 'h.slugid')
                        ->whereRaw('FIND_IN_SET(?, h.facilities)', [$swimmingPoolId])
                        ->where('h.slugid', $desiredId)
                        ->whereNotNull('h.OverviewShortDesc')
                        ->orderBy('h.stars', 'desc')
                        ->limit(4)
                        ->get();
                });

            //end nearby hotels with swimming pool


            }

            if (!empty($amenity_ids)) {
                $amenity_info = $this->getAmenityInfo($amenity_ids);

            }

            $sight_ids = [];
    if ($sight_slugs) {
        $sight_parts = explode('-', $sight_slugs);
        foreach ($sight_parts as $part) {
            if (preg_match('/^sqx(\d+)$/', $part, $matches)) {
                $sight_ids[] = $matches[1];
            }
        }
    }
            // Optimize neighborhood information query with caching
            $neighborhood_info = empty($neighborhood_ids) ? collect() :
                Cache::remember("neighborhood_info_" . md5(implode('_', $neighborhood_ids)), 86400, function() use ($neighborhood_ids) {
                    return DB::table('Neighborhood')
                        ->select('NeighborhoodId', 'Name', 'slug', 'Latitude', 'Longitude', 'LocationID')
                        ->whereIn('NeighborhoodId', $neighborhood_ids)
                        ->get();
                });

            // Optimize sight information query with caching
            $sight_info = empty($sight_ids) ? collect() :
                Cache::remember("sight_info_" . md5(implode('_', $sight_ids)), 86400, function() use ($sight_ids) {
                    return DB::table('Sight')
                        ->select('SightId', 'Title', 'LocationId', 'Latitude', 'Longitude')
                        ->whereIn('SightId', $sight_ids)
                        ->get();
                });

            $type = "hotel";

           $locationId = DB::table('TPHotel')
        ->where('slugid', $slgid)
        ->value('LocationId'); // Fetch single value

    // Fetch show_in_index from Location table using LocationId
    $showInIndex = null; // Default value

    if ($locationId) {
        $showInIndex = DB::table('Location')
            ->where('LocationId', $locationId)
            ->value('show_in_index'); // Fetch single value
    }

	$location_info = DB::table('Location')
    ->select('heading', 'headingcontent' , 'Name')
    ->where('slugid', $id)
    ->first();

            $pricingStats = $this->_calculatePricingStatistics($id);

            $popularNeighborhoods = collect($searchresults)->pluck('CityName')->filter()->unique()->take(4)->implode(', ');

            $hotelAmenities = $this->getHotelAmenities($id);
            $hotelNeighborhoods = $this->getHotelNeighborhoods($id);
            $popularSections = $this->getPopularNeighborhoods($id);
            $nearbySights = $this->getNearbySights($id);

            // Get hotels by star rating using caching for better performance
            $fiveStarHotels = Cache::remember("five_star_hotels_{$id}", 60 * 60 * 24, function () use ($id) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $id)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $id)
                    ->where('stars', 5)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $id . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $fourStarHotels = Cache::remember("four_star_hotels_{$id}", 60 * 60 * 24, function () use ($id) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $id)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $id)
                    ->where('stars', 4)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $id . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $threeStarHotels = Cache::remember("three_star_hotels_{$id}", 60 * 60 * 24, function () use ($id) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $id)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $id)
                    ->where('stars', 3)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $id . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $twoStarHotels = Cache::remember("two_star_hotels_{$id}", 60 * 60 * 24, function () use ($id) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $id)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $id)
                    ->where('stars', 2)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $id . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $oneStarHotels = Cache::remember("one_star_hotels_{$id}", 60 * 60 * 24, function () use ($id) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $id)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $id)
                    ->where('stars', 1)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $id . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            return view('hotel_listing')
                ->with('pricingStats', $pricingStats)
                ->with('popularNeighborhoods', $popularNeighborhoods)
                ->with('searchresults', $searchresults)
                ->with('hotels', $hotel)
                ->with('hotelAmenities', $hotelAmenities)
                ->with('hotelNeighborhoods', $hotelNeighborhoods)
                ->with('popularSections', $popularSections)
                ->with('nearbySights', $nearbySights)
                ->with('fiveStarHotels', $fiveStarHotels)
                ->with('fourStarHotels', $fourStarHotels)
                ->with('threeStarHotels', $threeStarHotels)
                ->with('twoStarHotels', $twoStarHotels)
                ->with('oneStarHotels', $oneStarHotels)
                ->with('neabyhotelwithswimingpool',$neabyhotelwithswimingpool)
                ->with('metadata',$metadata)
                ->with('agencyData',$agencyData)
                ->with('showInIndex',$showInIndex)
				->with('location_info',$location_info)
                ->with('st',$st)
                ->with('amenity',$amenity)
                ->with('amenity_info', $amenity_info ?? null)
                ->with('price',$price)
                ->with('reviewscore',$reviewscore)
                ->with('pagetype',$pagetype)
                ->with('gethoteltype',$gethoteltype)
                ->with('lname',$lname)
                ->with('countryname',$countryname)
                // ->with('locationgeo',$locationgeo)
                ->with('amenity_ids', $amenity_ids)
                ->with('neighborhood_ids', $neighborhood_ids)
                ->with('propertyType_ids', $propertyType_ids)
                ->with('propertyType_info', $propertyType_info)
                ->with('sight_info', $sight_info)
                ->with('neighborhood_info', $neighborhood_info)
                ->with('id',$id)
                ->with('locationPatent',$locationPatent)
                ->with('getlocationexp',$getlocationexp)
                ->with('getcontlink',$getcontlink)
                ->with('Tid',$Tid)
                ->with('type',$type)
                ->with('hlid',$hlid)
                ->with('slgid',$slgid )
                ->with('locationid',$locationid )
                ->with('count_result',$count_result)
                ->with('slugdata',$explocname)
                ->with('lslug',$lslug)
                ->with('nearbyPlaces', $nearbyPlaces)
                ->with('lslugid',$lslugid)
                ->with('hotelPropertyTypes', $hotelPropertyTypes);
        }

		  private function getHotelNeighborhoods($locationId) {
        // Create a cache key for this location's neighborhoods
        $cacheKey = "hotel_neighborhoods_{$locationId}";

        // Try to get from cache first (cache for 24 hours)
        return Cache::remember($cacheKey, 86400, function() use ($locationId) {
            // Get location info first to ensure it exists
            $location = DB::table('Location')
                ->select('LocationId', 'slugid', 'Slug')
                ->where('slugid', $locationId)
                ->first();

            if (!$location) {
                return collect(); // Return empty collection if location not found
            }

            // Get neighborhoods for this location
            $neighborhoods = DB::table('Neighborhood')
                ->select('NeighborhoodId', 'Name', 'slug', 'Latitude', 'Longitude')
                ->where('LocationID', $location->LocationId)
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->orderBy('Name')
                ->limit(7) // Limit to 7 neighborhoods for display
                ->get();

            // Format neighborhoods with URLs
            $formattedNeighborhoods = collect();

            foreach ($neighborhoods as $neighborhood) {
                // Check if neighborhood exists and has valid slug
                if (!empty($neighborhood->slug)) {
                    // Build the URL: ho-{locationId}-{locationSlug}-{neighborhoodSlug}-{neighborhoodName}
                    $neighborhoodName = str_replace(' ', '-', strtolower($neighborhood->Name));
                    $url = 'ho-' . $locationId . '-' . $location->Slug . '-' . $neighborhood->slug . '-' . $neighborhoodName;

                    $formattedNeighborhoods->push([
                        'name' => $neighborhood->Name,
                        'url' => $url,
                        'code' => $neighborhood->slug
                    ]);
                }
            }

            return $formattedNeighborhoods;
        });
    }

	    private function getPopularNeighborhoods($locationId) {
        // Create a cache key for this location's popular neighborhoods
        $cacheKey = "popular_neighborhoods_{$locationId}";

        // Try to get from cache first (cache for 24 hours)
        return Cache::remember($cacheKey, 86400, function() use ($locationId) {
            // Get location info first to ensure it exists
            $location = DB::table('Location')
                ->select('LocationId', 'slugid', 'Slug')
                ->where('slugid', $locationId)
                ->first();

            if (!$location) {
                return collect(); // Return empty collection if location not found
            }

            // Get neighborhoods for this location with hotel counts
            $neighborhoods = DB::table('Neighborhood as n')
                ->select('n.NeighborhoodId', 'n.Name', 'n.slug', DB::raw('COUNT(h.HotelId) as hotel_count'))
                ->leftJoin('TPHotel as h', function($join) {
                    $join->on('h.NeighborhoodId', '=', 'n.NeighborhoodId')
                         ->whereNotNull('h.slugid')
                         ->where('h.slugid', '!=', '');
                })
                ->where('n.LocationID', $location->LocationId)
                ->whereNotNull('n.slug')
                ->where('n.slug', '!=', '')
                ->groupBy('n.NeighborhoodId', 'n.Name', 'n.slug')
                ->orderBy('hotel_count', 'desc') // Order by hotel count in descending order
                ->limit(7) // Limit to 7 neighborhoods for display
                ->get();

            // Format neighborhoods with URLs
            $formattedNeighborhoods = collect();

            foreach ($neighborhoods as $neighborhood) {
                // Check if neighborhood exists and has valid slug
                if (!empty($neighborhood->slug)) {
                    // Build the URL: ho-{locationId}-{locationSlug}-{neighborhoodSlug}-{neighborhoodName}
                    $neighborhoodName = str_replace(' ', '-', strtolower($neighborhood->Name));
                    $url = 'ho-' . $locationId . '-' . $location->Slug . '-' . $neighborhood->slug . '-' . $neighborhoodName;

                    $formattedNeighborhoods->push([
                        'name' => $neighborhood->Name,
                        'url' => $url,
                        'code' => $neighborhood->slug,
                        'hotel_count' => $neighborhood->hotel_count
                    ]);
                }
            }

            return $formattedNeighborhoods;
        });
    }

		    private function getHotelPropertyTypes($locationId, $locationSlug)
    {
        // Create a cache key for this location's property types
        $cacheKey = "hotel_property_types_{$locationId}";

        // Cache property types for 24 hours (86400 seconds)
        return Cache::remember($cacheKey, 86400, function() use ($locationId, $locationSlug) {
            // Get location info first to ensure it exists
            $location = DB::table('Location')
                ->select('LocationId', 'slugid', 'Slug')
                ->where('slugid', $locationId)
                ->first();

            if (!$location) {
                return collect(); // Return empty collection if location not found
            }

            // Get property types from TPHotel_types table
            $propertyTypes = DB::table('TPHotel_types')
                ->select('hid', 'type', 'id')
                ->orderBy('type')
                ->get();

            // Format property types with URLs
            $formattedPropertyTypes = collect();

            foreach ($propertyTypes as $propertyType) {
                // Skip if missing required data
                if (empty($propertyType->type) || empty($propertyType->hid)) {
                    continue;
                }

                // Generate slug from the type field - use same pattern as in URL parsing
                $slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower($propertyType->type));
                $slug = trim($slug, '-');

                // Format URL: ho-{locationId}-{locationSlug}-pt{propertyTypeId}-{propertyTypeSlug}
                $url = "ho-{$locationId}-{$locationSlug}-pt{$propertyType->hid}-{$slug}";

                // Add to collection
                $formattedPropertyTypes->push([
                    'name' => $propertyType->type,
                    'url' => $url,
                    'code' => "pt{$propertyType->hid}"
                ]);
            }

            return $formattedPropertyTypes;
        });
    }

	    private function getHotelAmenities($locationId) {
        // Create a cache key for this location's amenities
        $cacheKey = "hotel_amenities_{$locationId}";

        // Try to get from cache first (cache for 24 hours)
        return Cache::remember($cacheKey, 86400, function() use ($locationId) {
            // Get location info first to ensure it exists
            $location = DB::table('Location')
                ->select('LocationId', 'slugid', 'Slug')
                ->where('slugid', $locationId)
                ->first();

            if (!$location) {
                return collect(); // Return empty collection if location not found
            }

            // First check for amenities that match between Tripadvisor_amenities_url and TPHotel_amenities
            // This follows the same pattern as in SitemapXmlController
            $amenities = DB::table('Tripadvisor_amenities_url as ta')
                ->join('TPHotel_amenities as tpha', 'tpha.name', '=', 'ta.Keyword')
                ->where('ta.LocationId', $location->LocationId)
                ->select('tpha.id', 'tpha.name', 'tpha.slug', 'tpha.groupName')
                ->whereNotNull('tpha.slug')
                ->where('tpha.slug', '!=', '')
                ->where(function($query) {
                    // Ensure we have valid slugs
                    $query->whereRaw("LENGTH(tpha.slug) > 0");
                })
                ->orderBy('tpha.groupName')
                ->orderBy('tpha.id')
                ->limit(7) // Limit to 7 amenities for display
                ->get();

            // If no amenities found in Tripadvisor table, fall back to all amenities
            if ($amenities->isEmpty()) {
                $amenities = DB::table('TPHotel_amenities')
                    ->select('id', 'name', 'slug', 'groupName')
                    ->whereNotNull('slug')
                    ->where('slug', '!=', '')
                    ->where(function($query) {
                        // Ensure we have valid slugs
                        $query->whereRaw("LENGTH(slug) > 0");
                    })
                    // Group by groupName to get a variety of amenity types
                    ->orderBy('groupName')
                    ->orderBy('id')
                    ->limit(7) // Limit to 7 amenities for display
                    ->get();
            }

            // Format amenities with URLs
            $formattedAmenities = collect();

            foreach ($amenities as $amenity) {
                // Check if amenity exists and has valid slug
                if (!empty($amenity->slug)) {
                    // Build the URL: ho-{locationId}-{locationSlug}-{amenityCode}-{amenityName}
                    $amenityName = str_replace(' ', '-', strtolower($amenity->name));
                    $url = 'ho-' . $locationId . '-' . $location->Slug . '-' . $amenity->slug . '-' . $amenityName;

                    $formattedAmenities->push([
                        'name' => $amenity->name,
                        'url' => $url,
                        'code' => $amenity->slug
                    ]);
                }
            }

            return $formattedAmenities;
        });
    }

    private function getAmenityInfo($amenity_ids) {
        return DB::table('TPHotel_amenities')
            ->select('id', 'name', 'shortName', 'slug')
            ->whereIn('id', $amenity_ids)
            ->get();
    }

    /**
     * Get nearby sights/attractions for display in the hotel listing page
     *
     * @param string $locationId The location ID to check for nearby sights
     * @return \Illuminate\Support\Collection Collection of nearby sights
     */
    private function getNearbySights($locationId) {
        // Create a cache key for this location's sights
        $cacheKey = "nearby_sights_{$locationId}";

        // Return cached data if available
        $result = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($locationId) {
            // Get location info for the slug
            $location = DB::table('Location')
                ->select('Slug')
                ->where('slugid', $locationId)
                ->first();

            $locationSlug = $location ? $location->Slug : '';

            // Get nearby sights for this location
            $sights = DB::table('Sight')
                ->select('SightId', 'Title')
                ->where('Location_id', $locationId)
                ->whereNotNull('Title')
                ->where('Title', '!=', '')
                ->limit(7)
                ->get();

            // Format the sights with URLs
            $formattedSights = collect();

            foreach ($sights as $sight) {
                // Create SEO-friendly URL
                $url = url()->current() . '-sqx' . $sight->SightId;

                $formattedSights->push([
                    'name' => $sight->Title,
                    'url' => $url,
                    'code' => $sight->SightId
                ]);
            }

            return $formattedSights;
        });

        return $result;
    }

    private function getNeighborhoodInfo($neighborhood_info) {
        if (!is_array($neighborhood_info)) {
            return collect();
        }

        $neighborhood_ids = array_column($neighborhood_info, 'NeighborhoodId');

        if (empty($neighborhood_ids)) {
            return collect();
        }

        return DB::table('Neighborhood')
            ->select('NeighborhoodId', 'Name', 'slug', 'Latitude', 'Longitude', 'LocationID')
            ->whereIn('NeighborhoodId', $neighborhood_ids)
            ->get();
    }

    public function searchNearbyPlaces(Request $request)
    {
        $search = $request->input('search');
        $locationId = $request->input('location_id');

        $query = DB::table('Sight')
            ->select('SightId', 'Title', 'LocationId','Latitude','Longitude')
            ->where('Location_id', $locationId);

        if ($search) {
            $query->where('Title', 'LIKE', '%' . $search . '%');
        }

        $places = $query->get();

        return response()->json([
            'success' => true,
            'places' => $places
        ]);
    }

    public function getwithoutdatedata(Request $request) {

        $amenity_ids = $request->input('amenity_ids');
        if (!is_array($amenity_ids)) {
            $amenity_ids = is_string($amenity_ids) ? explode(',', $amenity_ids) : [];
        }

        $neghborhood_info = $request->input('neighborhood_info');

        if (!is_array($neghborhood_info)) {
            $neghborhood_info = is_string($neghborhood_info) ? json_decode($neghborhood_info, true) : [];
        }
        $neghborhood_info = array_filter($neghborhood_info);

        $sight_info = $request->input('sight_info');
        if (!is_array($sight_info)) {
            $sight_info = is_string($sight_info) ? json_decode($sight_info, true) : [];
        }
        $sight_info = array_filter($sight_info);

        $propertyType_ids = $request->input('property_type_ids');
        if (!is_array($propertyType_ids)) {
            $propertyType_ids = is_string($propertyType_ids) ? explode(',', $propertyType_ids) : [];
        }

        $pagetype = "withoutdate";
        $desiredId = $request->get('locationid');
        $lname = $request->get('lname');
        $st = $request->get('starrating') ? trim($request->get('starrating')) : "";
        $amenity = $request->get('amenity') ? trim(str_replace('_',' ',$request->get('amenity'))) : "";
        $reviewscore = $request->get('rs') ? trim(str_replace('_',' ',$request->get('rs'))) : "";
        $price = $request->get('price') ? trim(str_replace('_',' ',$request->get('price'))) : "";

        if($amenity == "") {
            $query = DB::table('TPHotel as h')
                ->leftJoin('TPHotel_amenities as a', DB::raw('FIND_IN_SET(a.id, h.shortFacilities)'), '>', DB::raw('0'))
                ->select([
                    'h.hotelid', 'h.id', 'h.name', 'h.slug', 'h.stars', 'h.pricefrom',
                    'h.rating', 'h.amenities', 'h.distance', 'h.image', 'h.about',
                    'h.facilities', 'h.room_aminities', 'h.shortFacilities', 'h.slugid',
                    'h.CityName', 'h.short_description', 'h.ReviewSummary',
                    'h.OverviewShortDesc','h.NeighborhoodId','h.Neighborhood','h.propertyType',
                            DB::raw('GROUP_CONCAT(CONCAT(a.shortName, "|", a.image) ORDER BY a.name SEPARATOR ", ") as amenity_info')
    ]);

if (!empty($sight_info) && isset($sight_info[0]['Latitude']) && isset($sight_info[0]['Longitude'])) {
    $query->addSelect(DB::raw('ROUND(
        111.045 * SQRT(
            POWER(ABS(CAST(h.Latitude AS DECIMAL(10,6)) - ' . (float)$sight_info[0]['Latitude'] . '), 2) +
            POWER(
                ABS(CAST(h.longnitude AS DECIMAL(10,6)) - ' . (float)$sight_info[0]['Longitude'] . ') *
                COS(RADIANS(' . (float)$sight_info[0]['Latitude'] . ')),
                2
            )
        ), 2
    ) AS calculated_distance'));
}

if (!empty($neghborhood_info)) {
    foreach ($neghborhood_info as $index => $neighborhood) {
        if (isset($neighborhood['Latitude']) && isset($neighborhood['Longitude'])) {
            $alias = "neighborhood_distance_" . $index;
            $query->addSelect(DB::raw('ROUND(
                111.045 * SQRT(
                    POWER(ABS(CAST(h.Latitude AS DECIMAL(10,6)) - ' . (float)$neighborhood['Latitude'] . '), 2) +
                    POWER(
                        ABS(CAST(h.longnitude AS DECIMAL(10,6)) - ' . (float)$neighborhood['Longitude'] . ') *
                        COS(RADIANS(' . (float)$neighborhood['Latitude'] . ')),
                        2
                    )
                ), 2
            ) AS neighborhood_distance'));
        }
    }
}

$query->where('h.slugid', $desiredId)
    ->whereNotNull('h.slugid');

            if ($st != "") {
                $query->where('h.stars', $st);
            }
            if ($reviewscore != "") {
                $query->whereNotNull('h.rating')->where('h.rating', '>=', $reviewscore);
            }
            if (!empty($price)) {
                $query->where('h.pricefrom', '<=', (int)trim($price));
            }

            if (!empty($amenity_ids)) {
                $query->where(function($query) use ($amenity_ids) {
                    foreach ($amenity_ids as $index => $amenity_id) {
                        if ($index === 0) {
                            $query->whereRaw('FIND_IN_SET(?, h.facilities)', [$amenity_id]);
                        } else {
                            $query->orWhereRaw('FIND_IN_SET(?, h.facilities)', [$amenity_id]);
                        }
                    }
                });
            }


            // Apply sight-based distance filter
            if (!empty($sight_info)) {
                $query->where(function($query) use ($sight_info) {
                    foreach ($sight_info as $sight) {
                        $query->orWhere(function($q) use ($sight) {
                            // First, create the distance calculation expression
                            $distanceCalculation = "ROUND(
                                111.045 * SQRT(
                                    POWER(ABS(CAST(h.Latitude AS DECIMAL(10,6)) - ?), 2) +
                                    POWER(
                                        ABS(CAST(h.longnitude AS DECIMAL(10,6)) - ?) *
                                        COS(RADIANS(?)),
                                        2
                                    )
                                ), 2
                            )";

                            // Add it to the select clause to return the calculated distance
                            $q->addSelect(DB::raw("$distanceCalculation AS calculated_distance"))
                              ->whereRaw("$distanceCalculation <= ?", [
                                    (float)$sight['Latitude'],
                                    (float)$sight['Longitude'],
                                    (float)$sight['Latitude'],
                                    2 // 2 km radius
                                ])
                              ->where('h.LocationId', $sight['LocationId'])
                              ->whereNotNull('h.Latitude')
                              ->whereNotNull('h.longnitude')
                              ->whereRaw('TRIM(h.Latitude) != ""')
                              ->whereRaw('TRIM(h.longnitude) != ""');
                        });
                    }
                });
            }

            if (!empty($propertyType_ids)) {
                $query->where(function($query) use ($propertyType_ids) {
                    foreach ($propertyType_ids as $index => $propertyType_id) {
                        if ($index === 0) {
                            $query->where('h.propertyType', $propertyType_id);
                        } else {
                            $query->orWhere('h.propertyType', $propertyType_id);
                        }
                    }
                });
            }

        if (!empty($neghborhood_info)) {

            $query->where(function($query) use ($neghborhood_info) {
                foreach ($neghborhood_info as $neighborhood) {

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
                                (float)$neighborhood['Latitude'],
                                (float)$neighborhood['Longitude'],
                                (float)$neighborhood['Latitude'],
                                2 // 2 km radius
                            ]
                        )
                        ->where('h.LocationId', $neighborhood['LocationID'])
                        ->whereNotNull('h.Latitude')
                        ->whereNotNull('h.longnitude')
                        ->whereRaw('TRIM(h.Latitude) != ""')
                        ->whereRaw('TRIM(h.longnitude) != ""');
                    });
                }
            });
        }

            $query->orderBy(DB::raw('h.short_description IS NULL'), 'asc')
                ->orderBy('h.stars', 'desc')
                ->groupBy('h.id');
            $searchresults = $query->paginate(30);
        }

        if($amenity !=""){
    $query = DB::table('TPHotel as h')
        ->leftJoin('TPHotel_amenities as a', DB::raw('FIND_IN_SET(a.id, h.facilities)'), '>', DB::raw('0'));

    // Add the TPRoomtype_tmp join right away if needed
    if ($amenity == 'free_cancellation' || $amenity == 'breakfast') {
        $query->leftJoin('TPRoomtype_tmp as rt', 'h.hotelid', '=', 'rt.hotelid');
    }

    $query->select(
        'h.hotelid',
        'h.id',
        'h.name',
        'h.slug',
        'h.stars',
        'h.pricefrom',
        'h.facilities',
        'h.rating',
        'h.distance',
        'h.image',
        'h.about',
        'h.slugid',
        'h.CityName',
        'h.short_description',
        'h.ReviewSummary',
        'h.NeighborhoodId',
        'h.OverviewShortDesc',
        'h.Neighborhood',
        DB::raw('GROUP_CONCAT(CONCAT(a.shortName, "|", a.image) ORDER BY a.name SEPARATOR ", ") as amenity_info')
    )
    ->where('h.slugid', $desiredId)
    ->whereNotNull('h.slugid');

    if (!empty($st)) {
        $query->where('h.stars', $st);
    }

    if (!empty($amenity)) {
        if ($amenity == 'parking' || $amenity == 'Internet') {
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
        } elseif ($amenity == 'free_cancellation') {
            $query->where('rt.refundable', 1);
        } elseif ($amenity == 'breakfast') {
            $query->where('rt.breakfast', 1);
        }
    }

    if ($reviewscore !="") {
        $query->whereNotNull('h.rating')
            ->where('h.rating', '>=', $reviewscore);
    }

    if (!empty($price)) {
        $query->where('h.pricefrom', '<=', (int)trim($price));
    }

    $query->orderBy(DB::raw('h.short_description IS NULL'), 'asc')
        ->orderBy('h.stars', 'desc')
        ->groupBy('h.id');

    $searchresults = $query->paginate(30);
}

        $paginationLinks = $searchresults->appends($request->except(['_token']))->links('hotellist_pagg.default');

        $amenity_info = $this->getAmenityInfo($amenity_ids);
        $neighborhood_info = $this->getNeighborhoodInfo($neghborhood_info);
		$sight_info = $this->getSightHotelInfo($sight_info);
        $count_result = $searchresults->total();

        $location_info = DB::table('Location')
    ->select('heading', 'headingcontent','Slug')
    ->where('slugid', $desiredId)
    ->first();

        $propertyType_info = [];
        if (!empty($propertyType_ids)) {
            $propertyType_info = DB::table('TPHotel_types')
                ->select('hid', 'type', 'id')
                ->whereIn('hid', $propertyType_ids)
                ->get();
        }

        return view('frontend.hotel.hoteldata_withoutdate', [
            'count_result' => $count_result,
            'searchresults' => $searchresults,
            'lname' => $lname,
            'st' => $st,
            'location_info' => $location_info,
            'amenity' => $amenity,
            'amenity_ids' => $amenity_ids,
            'amenity_info' => $amenity_info,
            'neighborhood_info' => $neighborhood_info,
            'price' => $price,
            'reviewscore' => $reviewscore,
            'sight_info' => $sight_info,
            'pagetype' => $pagetype,
            'propertyType_ids' => $propertyType_ids,
            'propertyType_info' => $propertyType_info,
        ]);
}

private function getSightHotelInfo($sight_info) {
    if (is_array($sight_info) && isset($sight_info[0]) && isset($sight_info[0]['SightId'])) {
        $sight_info = $sight_info[0];
    }

    if (empty($sight_info) || !isset($sight_info['SightId'])) {
        return collect();
    }

    // Get sight details
    $sightDetails = DB::table('Sight')
        ->where('SightId', $sight_info['SightId'])
        ->select('Title','slug', 'Location_id')
        ->first();

    return [
        'sight_name' => $sight_info['Title'],
        'sight_id' => $sight_info['SightId'],
        'sight_slug' => $sightDetails ? $sightDetails->slug : null,
        'sight_Location_id' => $sightDetails ? $sightDetails->Location_id : null,
    ];
}

    public function gethotellist_withoutdate_test(request $request){
            $id = $request->get('id');
            $desiredId = $request->get('locationid');
            $lname = $request->get('lname');
            $countryname = $request->get('countryname');
               $Tid = $request->get('Tid');

		$LocationId = $Tid;
		   $start  =  date("H:i:s");
      //  return $desiredId;

		   $searchresults = Hotel::leftJoin('TPHotel_types as ty', 'ty.hid', '=', 'TPHotel.propertyType')
    ->select('TPHotel.hotelid', 'TPHotel.id', 'TPHotel.name', 'TPHotel.address', 'TPHotel.slug', 'TPHotel.cityId', 'TPHotel.iata', 'TPHotel.location_id as loc_id', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.rating', 'TPHotel.popularity', 'TPHotel.amenities', 'TPHotel.distance', 'TPHotel.image', 'ty.type as propertyType')
    ->where('TPHotel.location_id', $desiredId)
	->orderBy('TPHotel.hotelid','asc')
     ->paginate(10);
		   $end  =  date("H:i:s");
     //     return   $start.'--'.$end;
              $url = 'ho-'.$id;
              $searchresults->appends(request()->except(['_token', 'locationid', 'lname', 'countryname', 'id']));

              $searchresults->setPath($url);
              $paginationLinks = $searchresults->links('hotellist_pagg.default');

         return view('frontend.hotel.get_hotel_listing_result_withoutdate')->with('searchresults',$searchresults)->with('lname',$lname)->with('countryname',$countryname)->with('LocationId',$LocationId);

    }

  public function getSignature(request $request) {
    $cityId = $request->get('lid');
    $hotelId = $request->get('hid');
    $cityName = $request->get('cityName');

    $guests = $request->get('guest');
    $rooms = $request->get('rooms');
    if ($guests == 0) {
        $guests = Session()->get('guest');
    }
    if ($rooms == 0) {
        $rooms = Session()->get('rooms');
    }

    $stchin = $request->get('checkin');
    $checkout = $request->get('checkout');

    $cmbdate = $request->get('checkin') . '_' . $request->get('checkout');

    $checkin = Session()->get('checkin');

    if ($cmbdate === $checkin || empty($checkout) && !empty($checkin)) {
        $expdate = explode('_', $checkin);

        $checkin_date = trim($expdate[0]);
        $checkout_date = trim($expdate[1]);

        $date_stchin = strtotime($checkin_date);
        $chkin = date("Y-m-d", $date_stchin);

        $date_chout = strtotime($checkout_date);
        $checout = date("Y-m-d", $date_chout);

    } else {
        if (!empty($stchin) && !empty($checkout)) {

            $date_stchin = strtotime($stchin);
            $chkin = date("Y-m-d", $date_stchin);

            $date_chout = strtotime($checkout);
            $checout = date("Y-m-d", $date_chout);

            $cmbdate = $chkin . '_' . $checout;

            session()->put('checkin', $cmbdate);

        } else {
            $checkinTimestamp = strtotime("+1 day");
            $chkin = date("Y-m-d", $checkinTimestamp);

            // Get the checkout date by adding 4 days
            $checkoutTimestamp = strtotime("+5 days", $checkinTimestamp);
            $checout = date("Y-m-d", $checkoutTimestamp);
        }
    }
    if (empty($chkin) && empty($checout)) {
        return 0;
    }

    // New code start
    $checkinDate = $chkin;
    $checkoutDate = $checout;
    $adultsCount = 2; // $guests;
    $customerIP = '49.156.89.145';
    $childrenCount = '1';
    $chid_age = '10';
    $lang = 'en';
    $currency = 'USD';
    $waitForResult = '0';
    $iata = $hotelId;

    $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
    $TRAVEL_PAYOUT_MARKER = "299178";

    $SignatureString = "" . $TRAVEL_PAYOUT_TOKEN . ":" . $TRAVEL_PAYOUT_MARKER . ":" . $adultsCount . ":" .
        $checkinDate . ":" .
        $checkoutDate . ":" .
        $chid_age . ":" .
        $childrenCount . ":" .
        $currency . ":" .
        $customerIP . ":" .
        $iata . ":" .
        $lang . ":" .
        $waitForResult;

    $signature = md5($SignatureString);

    $url = 'http://engine.hotellook.com/api/v2/search/start.json?hotelId=' . $iata . '&checkIn=' . $checkinDate . '&checkOut=' . $checkoutDate . '&adultsCount=' . $adultsCount . '&customerIP=' . $customerIP . '&childrenCount=' . $childrenCount . '&childAge1=' . $chid_age . '&lang=' . $lang . '&currency=' . $currency . '&waitForResult=' . $waitForResult . '&marker=299178&signature=' . $signature;

    $response = Http::withoutVerifying()->get($url);

    if ($response->successful()) {
        $data = json_decode($response);
        if (!empty($data)) {
            $searchId = $data->searchId;

            $limit = 10;
            $offset = 0;
            $roomsCount = 10;
            $sortAsc = 1;
            $sortBy = 'price';

            $SignatureString2 = "" . $TRAVEL_PAYOUT_TOKEN . ":" . $TRAVEL_PAYOUT_MARKER . ":" . $limit . ":" . $offset . ":" . $roomsCount . ":" . $searchId . ":" . $sortAsc . ":" . $sortBy;
            $sig2 = md5($SignatureString2);

            $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId=' . $searchId . '&limit=10&sortBy=price&sortAsc=1&roomsCount=10&offset=0&marker=299178&signature=' . $sig2;

            // Polling for search completion
            $searchComplete = false;
            $maxAttempts = 10;
            $attempt = 0;
            $retryInterval = 2; // seconds

            while (!$searchComplete && $attempt < $maxAttempts) {
                $response2 = Http::withoutVerifying()
                    ->timeout(0)
                    ->get($url2);

                $jsonResponse = json_decode($response2, true);

                if (isset($jsonResponse['errorCode']) && $jsonResponse['errorCode'] === 4) {
                    // Search not finished, wait and retry
                    sleep($retryInterval);
                    $attempt++;
                } else {
                    $searchComplete = true;
                }
            }

            if (!$searchComplete) {
                // Handle the case where the search did not complete
                return response()->json(['error' => 'Search did not complete in time.'], 408);
            }

            if ($searchComplete && !empty($jsonResponse)) {
                return view('hotel_result', ['hotels' => $jsonResponse]);
            } else {
                return 'search id not found';
            }

        } else {
            return 'search id not found';
        }

    } else {
        return 2;
    }
}


   //HOTEL DETAIL Page
		//start hotel detail
     public function hotel_detail($id,Request $request) {

            $currentTime  = now();
            $checkin = $request->get('checkin');
            $checkout = $request->get('checkout');
            $lslugid="";
            $lslug="";

            $TPRoomtype = collect();
            $gethotel = collect();

            $rooms = [];
            $uniqueAmenities =[];
            $locid = 0;
            $hotelid = null;
            $slug = "";
            $locname = "";
            $ctname = "";
            $LocationId =null;
            $location_slugid=null;
            // Extract only the first two parts (location ID and hotel ID) from the URL
            $parts = explode('-', $id, 3);
            if (count($parts) >= 2) {
                $LocationId = str_pad($parts[0], 5, '0', STR_PAD_LEFT);
                $location_slugid = $LocationId;
                $hotelid = $parts[1];

                // We don't care about the slug in the URL anymore
                // Just check if the hotel exists and get its data
                $hotelExists = DB::table('TPHotel')->where('id', $hotelid)->exists();
                if ($hotelExists) {
                    // Get the actual hotel data to see what slugid and slug it has
                    $actualHotel = DB::table('TPHotel')->select('slugid', 'slug')->where('id', $hotelid)->first();

                    // If the hotel exists but the location ID doesn't match, redirect to the correct URL
                    // Or if we need to ensure the URL has the correct slug format
                    $correctSlug = strtolower(str_replace(' ', '_', str_replace('#', '!', $actualHotel->slug)));

                    // Check if the current URL already has the correct format
                    $currentUrl = 'hd-' . $location_slugid . '-' . $hotelid;
                    $correctUrl = 'hd-' . $actualHotel->slugid . '-' . $hotelid . '-' . $correctSlug;

                    // Only redirect if the URL needs correction
                    if ($currentUrl !== $correctUrl && $request->path() !== $correctUrl) {
                        return redirect($correctUrl);
                    }
                }
            }

           $getloclink = DB::table('Temp_Mapping as m')
           ->select('m.LocationId','m.cityName')
           ->where('m.slugid', $LocationId)
           ->get();
           if(!$getloclink->isEmpty()){
            $locid = $getloclink[0]->LocationId;
           }
           $hlid =$locid;


           $searchresults = DB::table('TPHotel as h')
           ->select('h.name','h.location_id','h.id','h.hotelid','h.metaTagTitle','h.MetaTagDescription','h.Latitude','h.longnitude','h.stars','h.address','h.pricefrom','h.about','h.location_score','h.room_aminities',
            'h.amenities','h.shortFacilities','h.Phone','h.Also_known_as','h.knownfor','h.CityName','h.AmenitiesRest','h.PlaceKnownFor','h.PriceRange','h.Moreinfo','h.PopularRoomTypes','h.OverviewShortDesc','h.People_also_search','h.CheckIn_Policy','h.Damage_Policy','h.Children_Policy','h.Beds_Policy','h.Age_Policy','h.Payment_Policy','h.Curfew_Policy','h.Parties_Policy','h.QuiteHours_Policy','h.Groups_Policy','h.Email','h.Website','h.Smoking_Policy', 'h.photoCount','h.Highlights','h.cntRooms','h.Languages','h.maxprice','h.minprice','h.checkIn','h.checkOut','h.CityName','l.cityName'   ,'h.photosByRoomType','h.propertyTypeId','h.GreatForScore','h.GreatFor','h.facilities','h.rating','h.ratingcount','h.photoCount','h.ReviewSummary','h.ReviewSummaryLabel','h.Spotlights','h.ThingstoKnow','h.slugid','h.slug')
            ->leftJoin('TPLocations as l', 'l.id', '=', 'h.location_id')
            ->where('h.id', $hotelid)
            ->get()->toArray();

         //   return  print_r( $searchresults);

            if (empty($searchresults)) {
                    if ($LocationId) {
                    $redirect = DB::table('Temp_Mapping as m')
                    ->select('m.slugid')
                    ->where('m.Tid', $LocationId)
                    ->get();
                    if(!$redirect->isEmpty()){
                        $locid = $redirect[0]->slugid;
                        // Get the correct slug from the hotel
                        $hotelData = DB::table('TPHotel')->select('slug')->where('id', $hotelid)->first();
                        if ($hotelData) {
                            $correctSlug = strtolower(str_replace(' ', '_', str_replace('#', '!', $hotelData->slug)));
                            return redirect('hd-' .$locid.'-'.$hotelid . '-' . $correctSlug);
                        } else {
                            // If we can't find the hotel data, just redirect with the location ID and hotel ID
                            return redirect('hd-' .$locid.'-'.$hotelid);
                        }
                    }
                    // Redirect to the new URL
                    $checkgetloc2 = DB::table('Temp_Mapping as tm')
                        ->select('tm.slugid')
                        ->where('tm.LocationId', $LocationId)
                        ->get();


                    if(!$checkgetloc2->isEmpty()){
                        $locid =  $checkgetloc2[0]->slugid;
                        // Get the correct slug from the hotel
                        $hotelData = DB::table('TPHotel')->select('slug')->where('id', $hotelid)->first();
                        if ($hotelData) {
                            $correctSlug = strtolower(str_replace(' ', '_', str_replace('#', '!', $hotelData->slug)));
                            return redirect('hd-' .$locid.'-'.$hotelid . '-' . $correctSlug);
                        } else {
                            // If we can't find the hotel data, just redirect with the location ID and hotel ID
                            return redirect('hd-' .$locid.'-'.$hotelid);
                        }
                    }
                    // Redirect to the new URL

                    }
                abort(404, 'Hotel not found');
            }
             $shortfacilityIds = explode(',', $searchresults[0]->shortFacilities);
                $shortFacilities = DB::table('TPHotel_amenities')
                ->whereIn('id', $shortfacilityIds)
                ->get();

     			$highlightWords = [];

            if (!empty($searchresults[0]->Highlights)) {
                $highlightsRaw = $searchresults[0]->Highlights;

                // Convert invalid PHP-style array string to proper JSON format
                $highlightsRaw = str_replace(["'", "[", "]"], ['"', "[", "]"], $highlightsRaw);

                // Attempt to decode again
                $highlightsArray = json_decode($highlightsRaw, true);

                if (is_array($highlightsArray) && count($highlightsArray) > 0) {
                    shuffle($highlightsArray); // Randomize order
                    $highlightWords = array_slice($highlightsArray, 0, 40); // Pick first 4 words
                }
            }

         $facilityIds = explode(',', $searchresults[0]->facilities); // Convert comma-separated IDs to array

                $facilityNames = DB::table('TPHotel_amenities')
                    ->whereIn('id', $facilityIds)
                    ->get();

                // Group facilities by groupName and pick the first amenity from each group
                $groupedFacilities = [];
                $amenitiesArray = []; // This will hold one amenity per group for mobile view

                $groupedFacilities = []; // Grouped amenities with their headings
foreach ($facilityNames as $facility) {
    $groupedFacilities[$facility->groupName][] = $facility->name;
}

// Prepare a mobile-specific array with one amenity per group
$amenitiesArray = [];
foreach ($groupedFacilities as $groupName => $facilities) {
    if (!empty($facilities)) {
        $amenitiesArray[$groupName] = $facilities[0]; // Key: groupName, Value: first amenity
    }
}


           $hotid= 0;
           $getroomtype =collect();
           $getreview = collect();
           $getquest =collect();
        // Get all reviews for the hotel
        $getreview = DB::table('HotelReview')
            ->where('HotelId', $hotelid)
            ->where('IsActive', 1) // Only get active reviews
            ->get();

        // Calculate review statistics
        $totalReviews = $getreview->count();
        $avgCleanliness = $totalReviews > 0 ? $getreview->avg('cleanrating') : 0;
        $avgLocation = $totalReviews > 0 ? $getreview->avg('locationrating') : 0;
        $avgService = $totalReviews > 0 ? $getreview->avg('servicerating') : 0;
        $avgValue = $totalReviews > 0 ? $getreview->avg('valuerating') : 0;

        // Calculate overall rating (average of all categories)
        $overallRating = $totalReviews > 0 ?
            ($avgCleanliness + $avgLocation + $avgService + $avgValue) / 4 : 0;

        // Calculate rating percentage (scale 1-5 to 0-100)
        $ratingPercentage = ($overallRating / 5) * 100;

        // Determine rating text based on overall rating
        $ratingText = '';
        if ($overallRating >= 4.5) {
            $ratingText = 'Excellent';
        } elseif ($overallRating >= 4.0) {
            $ratingText = 'Very Good';
        } elseif ($overallRating >= 3.0) {
            $ratingText = 'Good';
        } elseif ($overallRating >= 2.0) {
            $ratingText = 'Fair';
        } else {
            $ratingText = 'Poor';
        }

        // Count mentions of each category
        $mentions = [
            'Cleanliness' => $getreview->where('cleanrating', '>=', 4)->count(),
            'Location' => $getreview->where('locationrating', '>=', 4)->count(),
            'Service' => $getreview->where('servicerating', '>=', 4)->count(),
            'Value' => $getreview->where('valuerating', '>=', 4)->count(),
        ];

        // Sort mentions by count in descending order and take top 5
        arsort($mentions);
        $topMentions = array_slice($mentions, 0, 5, true);

        if(!empty($searchresults)){
               $hotid = $searchresults[0]->hotelid;
               $hoid = $searchresults[0]->id;
               $TPRoomtype = DB::table('TPRoomtype')->select('Roomdesc')->where('hotelid',$hotid)->get();
               $photosByRoomType = json_decode($searchresults[0]->photosByRoomType, true);
               if (!empty($photosByRoomType)) {
                    foreach ($photosByRoomType as $key => $value) {
                        $roomtyids[] = $key;
                    }
                    $getroomtype = DB::table('TPRoom_types')->select('rid','type')->whereIn('rid', $roomtyids)->get();
               }

           }


           $url = 'https://yasen.hotellook.com/photos/hotel_photos?id='.$hotid ;
          $response = Http::withoutVerifying()->get($url);
          $images = $response->json();

           //nearby attraction

         $within = null;
         $nearby_hotel = collect();
         $nearby_sight = collect();
         $near_sight = collect();
         $nearby_rest =collect();
         $get_experiences =collect();
         $sighid = null;
         if(!empty($searchresults)){
           $latitude = $searchresults[0]->Latitude;
           $longitude = $searchresults[0]->longnitude ;
           $location_id = $searchresults[0]->location_id ;


           $restradus= 1;
           $Tid =null;
         }


        $getloclink =collect();
        $getcontlink =collect();
        $getlocationexp =collect();
   //  return $locid;
        $locationPatent = [];
        $getloclink = DB::table('Temp_Mapping as tm')
        ->join('Location as l', 'l.LocationId', '=', 'tm.Tid')
        ->select('l.LocationId','l.LocationLevel','l.ParentId','l.CountryId','l.Slug','l.slugid')
        ->where('tm.LocationId', $locid)
        ->get();
        if(!$getloclink->isEmpty()){
            $lslug = $getloclink[0]->Slug;
            $lslugid = $getloclink[0]->slugid;
          //  $lname =$getloclink[0]->Name;
        }
         if(!$getloclink->isEmpty() ){
             $getcontlink = DB::table('Country as co')
             ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
             ->select('co.CountryId','co.slug','co.Name','cont.Name as cName','cont.CountryCollaborationId as contid')
             ->where('co.CountryId', $getloclink[0]->CountryId)
             ->get();

             if(!$getloclink->isEmpty()){

                  $locationid = $getloclink[0]->LocationId;
                     $getlocationexp = DB::table('Location')->select('slugid','LocationId','Name','Slug')->where('LocationId', $locationid)->get();

                 if (!$getloclink->isEmpty() &&  $getloclink[0]->LocationLevel != 1) {

                     $loopcount =  $getloclink[0]->LocationLevel;
                     $lociID = $getloclink[0]->ParentId;
                     for ($i = 1; $i < $loopcount; $i++) {
                         $getparents = DB::table('Location')->select('slugid','Name','Slug','ParentId')->where('LocationId', $lociID)->get();
                         if (!empty($getparents)) {
                             $locationPatent[] = [
                                 'LocationId' => $getparents[0]->slugid,
                                 'slug' => $getparents[0]->Slug,
                                 'Name' => $getparents[0]->Name,
                             ];
                             if (!empty($getparents) && $getparents[0]->ParentId != "") {
                             $lociID = $getparents[0]->ParentId;
                         }
                         } else {
                             break;
                         }
                     }

                 }
             }


        }

            $amenity_desc = collect();

            $matching_amenities =[];
            $amenity_desc =[];


          //end


        $rooms =[];
        $pgtype = '';
        $Roomdesc =[];
        $filteredAmenitiesData =[];
        // $TPRoomtype = DB::table('TPRoomtype')->select('Roomdesc')->where('hotelid',$hotid)->get();
        $roomtypeAmenities = [];
$allowedColumns = [
    'breakfast', 'available', 'halfBoard', 'ultraAllInclusive', 'allInclusive',
    'refundable', 'freeWifi', 'fullBoard', 'deposit', 'smoking',
    'view', 'viewSentence', 'cardRequired', 'beds', 'doublebed',
    'twin', 'balcony', 'privateBathroom'
];

          $TPRoomtype1 = DB::table('TPRoomtype_tmp')->select('*')
          ->where('hotelid',$hotid)->get()->toArray();

          // Get minimum price from TPRoomtype_tmp table
          $minPrice = DB::table('TPRoomtype_tmp')
              ->where('hotelid', $hotid)
              ->where('price', '>', 0)  // Only consider prices greater than 0
              ->min('price');

          // Default to 0 if no valid prices found
          $minPrice = $minPrice ?? 0;

          foreach ($TPRoomtype1 as $room) {
            $amenities = [];
            foreach ($allowedColumns as $col) {
                if (isset($room->$col) && $room->$col == 1) {
                    $amenities[] = $col;
                }
            }
            $roomtypeAmenities[] = [
                'roomtype' => $room->roomtype ?? 'Room',
                'price' => $room->price ?? null, // Adjust according to your schema
                'amenities' => $amenities,
            ];
        }

          if(!empty($TPRoomtype1))
          {
              foreach ($TPRoomtype1 as $key => $value)
              {
                  foreach ($value as $key1 => $value1)
                  {
                      if($value1==1)
                      {
                          $amenitiesListNew[] = $key1;
                      }
                  }
              }
          }
          if(!empty($amenitiesListNew)){
              $valuesToRemove = [0,1,2,3];
              $filteredAmenitiesData = array_diff(array_unique($amenitiesListNew), $valuesToRemove);
          }
          $pgtype = '';
          if($checkin !=""  &&   $checkout !=""){
              $pgtype = 'withdate';
          }else{
              $pgtype = 'withoutdate';
          }
          $type = "hotel";
          $lname = $searchresults[0]->cityName;

		// Get location_id from TPHotel for the selected hotel
$hotelData = DB::table('TPHotel')
    ->select('LocationId')
    ->where('id', $hotelid)
    ->first();

$show_in_index = null; // Default value

if ($hotelData) {
    // Fetch show_in_index value from Location table using the location_id
    $locationData = DB::table('Location')
        ->select('show_in_index')
        ->where('LocationId', $hotelData->LocationId)
        ->first();

    if ($locationData) {
        $show_in_index = intval($locationData->show_in_index); // Convert to integer for strict comparison
    }
}

			$tips_reviews = DB::table('Tips')
  			  ->select('review', 'username')
    		  ->where('hotelid', $hotelid)
   			  ->get();
		   //start price code
			$getprice =collect();
			$hotelid = $searchresults[0]->hotelid;

     		  // Get nearby transportation data
            $transportationRequest = new \Illuminate\Http\Request();
            $transportationRequest->merge(['hotelid' => $searchresults[0]->id]);
            $nearbyTransportation = $this->getNearbyTransportation($transportationRequest);
     $popularNeighborhoods = collect($searchresults)->pluck('CityName')->filter()->unique()->take(4)->implode(', ');

           $hotelAmenities = $this->getHotelAmenities($location_slugid);
           $hotelNeighborhoods = $this->getHotelNeighborhoods($location_slugid);
           $popularSections = $this->getPopularNeighborhoods($location_slugid);
           $nearbyattractions = $this->getNearbySights($location_slugid);

            // Get hotels by star rating using caching for better performance
            $fiveStarHotels = Cache::remember("five_star_hotels_{$location_slugid}", 60 * 60 * 24, function () use ($location_slugid) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $location_slugid)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $location_slugid)
                    ->where('stars', 5)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $location_slugid . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $fourStarHotels = Cache::remember("four_star_hotels_{$location_slugid}", 60 * 60 * 24, function () use ($location_slugid) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $location_slugid)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $location_slugid)
                    ->where('stars', 4)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $location_slugid . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $threeStarHotels = Cache::remember("three_star_hotels_{$location_slugid}", 60 * 60 * 24, function () use ($location_slugid) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $location_slugid)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $location_slugid)
                    ->where('stars', 3)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $location_slugid . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $twoStarHotels = Cache::remember("two_star_hotels_{$location_slugid}", 60 * 60 * 24, function () use ($location_slugid) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $location_slugid)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $location_slugid)
                    ->where('stars', 2)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $location_slugid . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

            $oneStarHotels = Cache::remember("one_star_hotels_{$location_slugid}", 60 * 60 * 24, function () use ($location_slugid) {
                $location = DB::table('Location')->select('Slug')->where('slugid', $location_slugid)->first();
                $locationSlug = $location ? $location->Slug : '';

                $hotels = DB::table('TPHotel')
                    ->select('id', 'name', 'stars')
                    ->where('slugid', $location_slugid)
                    ->where('stars', 1)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->limit(5)
                    ->get();

                $formattedHotels = collect();
                foreach ($hotels as $hotel) {
                    $url = url('/') . '/hd-' . $location_slugid . '-' . $hotel->id;
                    $formattedHotels->push(['name' => $hotel->name, 'url' => $url, 'id' => $hotel->id]);
                }
                return $formattedHotels;
            });

			$getprice =DB::table('hotelbookingstemp')->where('hotelid',$hotelid )->orderby('price','asc')->get();
			//end price code
           return view('hotel_detail')
           ->with('popularNeighborhoods', $popularNeighborhoods)
           ->with('hotelAmenities', $hotelAmenities)
           ->with('hotelNeighborhoods', $hotelNeighborhoods)
           ->with('popularSections', $popularSections)
           ->with('nearbyattractions', $nearbyattractions)
           ->with('fiveStarHotels', $fiveStarHotels)
           ->with('fourStarHotels', $fourStarHotels)
           ->with('threeStarHotels', $threeStarHotels)
           ->with('twoStarHotels', $twoStarHotels)
           ->with('oneStarHotels', $oneStarHotels)
           ->with('checkin', $checkin)  // Add this
  		   ->with('checkout', $checkout)
           ->with('searchresult',$searchresults)
           ->with('images',$images)
           ->with('review',$getreview)
           ->with('faq',$getquest)
           ->with('roomtypeAmenities', $roomtypeAmenities)
           ->with('nearbyTransportation', $nearbyTransportation)
           ->with('tips_reviews', $tips_reviews)
           ->with('show_in_index', $show_in_index)
           ->with('highlightWords', $highlightWords)
           ->with('RoomsData',$TPRoomtype1)
           ->with('nearby_sight',$nearby_sight)
           ->with('nearby_hotel',$nearby_hotel)
           ->with('getroomtype',$getroomtype)
           ->with('getloclink',$getloclink)
           ->with('locationPatent',$locationPatent)
           ->with('within',$within)
           ->with('amenity_desc',$amenity_desc)
           ->with('TPRoomtype',$TPRoomtype)
           ->with('near_sight',$near_sight)
           ->with('getlocationexp',$getlocationexp)
           ->with('getcontlink',$getcontlink)
           ->with('hlid',$hlid)
           ->with('type',$type)
           ->with('amenitiesListroom',$filteredAmenitiesData)
           ->with('rooms',$rooms)
           ->with('pgtype', $pgtype)
           ->with('roomdt',$rooms)
           ->with('nearby_rest', $nearby_rest)
           ->with('restradus', $restradus)
           ->with('get_experience', $get_experiences)
           ->with('tid', $Tid)
           ->with('currentTime', $currentTime)
           ->with('location_slugid', $location_slugid)
           ->with('lname', $lname)
           ->with('facilityNames', $facilityNames)
           ->with('shortFacilities', $shortFacilities)
           ->with('getprice', $getprice)
           ->with('lslug', $lslug)
           ->with('lslugid', $lslugid)
           ->with('amenitiesArray', $amenitiesArray)
           ->with('groupedFacilities', $groupedFacilities)
           ->with('minprice', $minPrice)
           ->with('reviewStats', [
               'totalReviews' => $totalReviews,
               'overallRating' => $overallRating,
               'ratingPercentage' => $ratingPercentage,
               'ratingText' => $ratingText,
               'avgCleanliness' => $avgCleanliness,
               'avgLocation' => $avgLocation,
               'avgService' => $avgService,
               'avgValue' => $avgValue,
               'topMentions' => $topMentions,
           ]);
        }

	 public function getNearbyTransportation(Request $request)
  {
      $hotelid = $request->input('hotelid');

      if (!$hotelid) {
          return response()->json(['error' => 'Hotel ID is required'], 400);
      }

      try {
          // First try to find the hotel by hotelid to get internal ID
          $hotel = DB::table('TPHotel')
              ->where('hotelid', $hotelid)
              ->select('id')
              ->first();

          if ($hotel) {
              // Found by hotelid, use the internal id
              $internalId = $hotel->id;
          } else {
              // If not found by hotelid, try using the ID directly
              $internalId = $hotelid;
          }

          // Database connection details
          $connection = config('database.default');
          $database = config("database.connections.$connection.database");
          $table = 'LIQNearby';

          // Check if table exists
          $tableExists = \DB::select("SHOW TABLES LIKE '$table'");
          if (empty($tableExists)) {
              return response()->json([
                  'error' => 'Transportation data not available',
                  'debug' => [
                      'table_exists' => false,
                      'table' => $table,
                      'database' => $database
                  ]
              ], 404);
          }

          // First, check if there are any records for this hotel
          $count = DB::table($table)->where('TPHotelId', $internalId)->count();

          // Get the distinct types of records for this hotel
          $types = DB::table($table)
              ->where('TPHotelId', $internalId)
              ->select('Type', DB::raw('count(*) as count'))
              ->groupBy('Type')
            ->get();

          if ($count === 0) {
              // Try to find similar hotel IDs in case of mismatch
              $similarHotels = DB::table($table)
                  ->where('TPHotelId', 'like', substr($hotelid, 0, -2) . '%')
                  ->orWhere('TPHotelId', 'like', '%' . substr($hotelid, -3))
                  ->distinct('TPHotelId')
                  ->pluck('TPHotelId')
                  ->take(5);
          }

          // Fetch nearby transportation points from LIQNearby table
          $transportation = DB::table($table)
              ->select('Name', 'DistanceMtr', 'EstimatedTravelTimeMin', 'Type')
              ->where('TPHotelId', $internalId)
              ->whereIn('Type', ['airport', 'train_station', 'subway', 'bus_station', 'bus_stop'])
              ->orderBy('DistanceMtr')
              ->limit(4)
              ->get();

          $groupedTransportation = $transportation->groupBy('Type');

          $transportationData = [
            'airports' => $groupedTransportation->get('airport', collect())->all(),
            'trains' => $groupedTransportation->get('train_station', collect())->all(),
            'subways' => $groupedTransportation->get('subway', collect())->all(),
            'buses' => $groupedTransportation->get('bus_stop', $groupedTransportation->get('bus_station', collect()))->all(),
            '_debug' => [
                'hotel_id' => $hotelid,
                'query_params' => $request->all(),
                'found_types' => $groupedTransportation->keys()->toArray(),
                'table' => $table,
                'database' => $database,
                'record_count' => $count
            ]
        ];

          if ($transportation->isEmpty()) {
              return response()->json([
                  'error' => 'No transportation data available for this location',
                  'debug' => [
                      'hotel_id' => $hotelid,
                      'table' => $table,
                      'database' => $database,
                      'similar_hotels' => $similarHotels ?? null
                  ]
              ], 404);
          }

          return response()->json($transportationData);

      } catch (\Exception $e) {
          return response()->json([
              'error' => 'Failed to fetch transportation data',
              'message' => $e->getMessage(),
              'debug' => [
                  'hotel_id' => $hotelid,
                  'exception' => get_class($e)
              ]
          ], 500);
      }
  }

    public function hotel_detailfaqs(request $request){

        $hotelid = $request->get('hotelid');

        $hname = $request->get('hname');
        $hid = $request->get('hid');

        $nearby_sight =  DB::table('TPhotel_neaby_sight')->select('radius','distance','LocationId','SightId','slug','name','TAAggregateRating','Latitude','Longitude','category')->where('hotelid',$hid)->get();

        $get_experiences =collect();
        if(!$nearby_sight->isEmpty() ){

            $sighid = $nearby_sight[0]->SightId;
            $location_id = $nearby_sight[0]->LocationId;
            $mapping =  DB::table('Temp_Mapping')->select('Tid')->where('slugid',$location_id)->get();


            if(!$mapping->isEmpty()){
                $Tid = $mapping[0]->Tid;
                $get_experiences = DB::table('ExperienceItninerary as e')
                ->join('Experience as exp', 'exp.ExperienceId', '=', 'e.ExperienceId')
                ->leftJoin('ExperienceReview as rr', 'exp.ExperienceId', '=', 'rr.ExperienceId')
                    ->select('exp.Img1','exp.Img2','exp.Img3','exp.slugid','exp.ExperienceId',
                                'exp.Slug','exp.Name','exp.viator_url','exp.Duration','exp.Cost','exp.TAAggregationRating' ,DB::raw("COUNT(rr.Id) as review_count"))
                ->where('exp.LocationId', $Tid)
                ->groupBy('exp.ExperienceId', 'exp.Img1', 'exp.Img2', 'exp.Img3', 'exp.slugid',
                          'exp.Slug', 'exp.Name', 'exp.viator_url', 'exp.Duration', 'exp.Cost', 'exp.TAAggregationRating')
                ->orderBy('exp.TAAggregationRating', 'desc')
                ->limit(4)
                ->get();;
            }
       }

         $getquest =  DB::table('HotelQuestion')->select('User_Name','CreatedDate','Question','updatedOn','Listing','Answer','faq_image')->where('HotelId',$hid)->get();

         $getreview =  DB::table('HotelReview')->where('HotelId',$hid)->get();
      // return print_r($getreview);
         $html1 = view('hotel_detail_result.Near_by_Attractions',['nearby_sight'=>$nearby_sight])->render();

       //  $html2 = view('frontend.hotel.hotel_detail_nearby_hotel',['nearby_hotel'=>$nearby_hotel,'hname'=>$hname])->render();
         $html3 = view('frontend.hotel.hotel_detail_faq',['faq'=>$getquest])->render();
         $html4 = view('frontend.hotel.hotel_review_result',['review'=>$getreview])->render();

         $html5 = view('frontend.hotel.hotel_detail_nearby_exp',['get_experience'=>$get_experiences,'hname'=>$hname])->render();

         $html1 = (string) $html1;
     //    $html2 = (string) $html2;
         $html3 = (string) $html3;
         $html4 = (string) $html4;
         $html5 = (string) $html5;
         return response()->json([ 'html1' => $html1,'html3'=>$html3,'html4'=>$html4,'html5'=>$html5]);
    }


    public function hotel_detail_perfect_sight(request $request){

        $hname = $request->get('hname');
        $hotelid = $request->get('hotelid');
        $hid = $request->get('hid');
        $near_sight =  DB::table('TPHotelPerfectLocSights')->select('distance','name')->where('hotelid',$hid)->get();

        $html1 = view('hotel_detail_result.perfect_location',['near_sight'=>$near_sight])->render();
        $html1 = (string) $html1;
        return response()->json(['html1' => $html1]);

    }
    public function hoteldetailnearbyrest(request $request){

        $latitude = $request->get('latitude');
        $longitude = $request->get('longnitude');
        $tid = $request->get('tid');
        $hid = $request->get('hid');
        $hname = $request->get('hname');
        $hotelid = $request->get('hotelid');
    	$checkin = $request->get('checkin'); // Add this line
        $checkout = $request->get('checkout');

        $nearby_rest =  DB::table('TPhotel_nearby_restaurant')->where('hotelid',$hid)->get();
        $nearby_hotel =  DB::table('TPNearby_hotel')->where('hid',$hid)->get();


         $html1 = view('frontend.hotel.hotel_detail_nearbyrest',['nearby_rest'=>$nearby_rest])->render();

         $html2 = view('frontend.hotel.hotel_detail_nearby_hotel',['nearby_hotel'=>$nearby_hotel,'hname'=>$hname, 'checkin' => $checkin, 'checkout' => $checkout])->render();


         $html1 = (string) $html1;
         $html2 = (string) $html2;

         return response()->json(['html1' => $html1, 'html2' => $html2]);
    }

    public function add_hoteldetail_nearbyrest(request $request){

        $latitude = $request->get('latitude');
        $longitude = $request->get('longnitude');
        $hid = $request->get('hid');
        $hname = $request->get('hname');

        $nearby_restcheck =  DB::table('TPhotel_nearby_restaurant')->where('hotelid',$hid)->get();
        $restradus= 1;

        if($nearby_restcheck->isEmpty() &&  $latitude !="" && $longitude !="" ){

            $nearby_rest = DB::table("Restaurant as r")
            ->leftJoin('RestaurantReview as rr', 'r.RestaurantId', '=', 'rr.RestaurantId')
            ->select('r.Title','r.TATrendingScore','r.slugid','r.RestaurantId','r.Slug',
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(r.Latitude))
                    * cos(radians(r.Longitude) - radians(" . $longitude . "))
                    + sin(radians(" . $latitude . "))
                    * sin(radians(r.Latitude))) AS distance"),
                    DB::raw("COUNT(rr.RestaurantReviewId) as review_count"))
            ->groupBy("r.RestaurantId")
            ->having('distance', '<=', $restradus)
            ->orderBy('distance')
            ->limit(4)
            ->get();
            if (!$nearby_rest->isEmpty()) {
               $data = [];

               foreach ($nearby_rest as $restaurant) {
                   $data[] = [
                       'hotelid' => $hid,
                       'RestaurantId' => $restaurant->RestaurantId,
                       'radius' => $restradus,
                       'slugid' => $restaurant->slugid,
                       'Slug' => $restaurant->Slug,
                       'Title' => $restaurant->Title,
                       'distance' => $restaurant->distance,
                       'TATrendingScore' => $restaurant->TATrendingScore,
                       'review_count' => $restaurant->review_count,
                   ];
               }

               DB::table('TPhotel_nearby_restaurant')->insert($data);
           }



            $nearby_restdata =  DB::table('TPhotel_nearby_restaurant')->where('hotelid',$hid)->get();

            $html1 = view('frontend.hotel.hotel_detail_nearbyrest',['nearby_rest'=>$nearby_restdata,'restradus'=>$restradus])->render();



            $html1 = (string) $html1;


            return response()->json([ 'html1' => $html1]);
        }


    }

  // add hotel detail description


 public function insert_hotel_desction(request $request){

    $timeout = PHP_INT_MAX;
 //   $checkinDate = date('Y-m-d');

    $checkin = $request->get('checkin');
    $checkout = $request->get('checkout');
    $hid = $request->get('hid');
    $chkin = $checkin;
    $checout = $checkout;
    $checkin = $this->formatDate($checkin);
    $checkout = $this->formatDate($checkout);
       $guests = 2;
       $rooms = 1;

       $stchin = $checkin;
       $checkout = $checkout;

       $cmbdate = $checkin.'_'.$checkout;
       $checkin =  $checkin;


       //new code start
       $checkinDate = $checkin;
       $checkoutDate = $checkout;
       $adultsCount = 2; //$guests;
       $customerIP = '49.156.89.145';
       $childrenCount = '1';
       $chid_age = '10';
       $lang = 'en';
       $currency ='USD';
       $waitForResult ='0';
       $iata=$hid;

       $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
       $TRAVEL_PAYOUT_MARKER = "299178";

       $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
       $checkinDate.":".
       $checkoutDate.":".
       $chid_age.":".
       $childrenCount.":".
       $currency.":".
       $customerIP.":".
       $iata.":".
       $lang.":".
       $waitForResult;


       $signature = md5($SignatureString);


       $url ='http://engine.hotellook.com/api/v2/search/start.json?hotelId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;



       $response = Http::withoutVerifying()->get($url);

       if ($response->successful()) {


       $data = json_decode($response);
           if(!empty($data)){
           $searchId = $data->searchId;


           $limit =10;
           $offset=0;
           $roomsCount=10;
           $sortAsc=1;
           $sortBy='price';

           $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
           $sig2 =  md5($SignatureString2);

           $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=10&sortBy=price&sortAsc=1&roomsCount=10&offset=0&marker=299178&signature='.$sig2;

           $maxAttempts = 4;
           $retryInterval = 1;
           $response2 = Http::withoutVerifying()
           ->timeout(0)
           ->retry($maxAttempts, $retryInterval)
           ->get($url2);

           $jsonResponse = json_decode($response2, true);

           if ($jsonResponse['status'] == 'ok') {
               $hotels = $jsonResponse['result'];
               $rooms = [];
               $roomsData = [];

               foreach ($hotels as $hotel) {
                   if (isset($hotel['rooms']) && is_array($hotel['rooms'])) {

                       foreach ($hotel['rooms'] as $room) {
                           $rooms[] = [
                               'options' => $room['options'],
                               'desc' => $room['desc'],
                           	   'price' => $room['price'] ?? 0,
                           		'booking_url' => $room['fullBookingURL'] ?? 0,
    							'agencyId' => $room['agencyId'] ?? null,
   								'agencyName' => $room['agencyName'] ?? '',
                           ];
                           $roomName = $room['desc'];
                           $amenities = $room['options'];
                           $roomsData[$roomName] = $amenities;
                       }
                   }
               }

               $getdata = DB::table('TPRoomtype_tmp')->where('hotelid', $hid)->get();
if (!$getdata->isEmpty()) {
    $roomData = [];
    foreach ($rooms as $room) {
        $desc = $room['desc'];
        $options = $room['options'];

        // Update or insert into TPRoomtype_tmp
        DB::table('TPRoomtype_tmp')->updateOrInsert(
            ['hotelid' => $hid, 'roomType' => $desc],
            [
                'roomType' => $desc,
                'roomtypeid' => 0, // Default value
                'breakfast' => $options['breakfast'] ?? 0,
                'available' => $options['available'] ?? 1,
                'halfBoard' => $options['halfBoard'] ?? 0,
                'ultraAllInclusive' => $options['ultraAllInclusive'] ?? 0,
                'allInclusive' => $options['allInclusive'] ?? 0,
                'refundable' => $options['refundable'] ?? 0,
                'freeWifi' => $options['freeWifi'] ?? 0,
                'fullBoard' => $options['fullBoard'] ?? 0,
                'deposit' => $options['deposit'] ?? 0,
                'smoking' => $options['smoking'] ?? 0,
                'view' => $options['view'] ?? '',
                'viewSentence' => $options['viewSentence'] ?? '',
                'cardRequired' => $options['cardRequired'] ?? 0,
                'beds' => $options['beds']['double'] ?? 0,
                'doublebed' => $options['beds']['double'] ?? 0,
                'twin' => $options['beds']['twin'] ?? 0,
                'balcony' => $options['balcony'] ?? 0,
                'privateBathroom' => $options['privateBathroom'] ?? 0,
                'images' => $options['images'] ?? '',
            	'price' => $room['price'] ?? 0,
            	'booking_url' => $room['booking_url'] ?? 0,
                'agencyId' => $room['agencyId'] ?? null,
                'agencyName' => $room['agencyName'] ?? '',
                'dt_created' => now()
            ]
        );

        // Update or insert into TPRoomtype (store data as JSON in Roomdesc)
        DB::table('TPRoomtype')->updateOrInsert(
            ['hotelid' => $hid, 'Roomdesc' => $desc],
            [
                'Roomdesc' => json_encode([
                    'roomType' => $desc,
                    'options' => $options
                ]),
                'flag' => 1, // Default value
                'created_at' => now()
            ]
        );
    }
    return "Data updated in TPRoomtype_tmp and TPRoomtype";
} else {
    $roomData = [];
    foreach ($rooms as $room) {
        $desc = $room['desc'];
        $options = $room['options'];

        // Insert into TPRoomtype_tmp
        DB::table('TPRoomtype_tmp')->insert([
            'hotelid' => $hid,
            'roomType' => $desc,
            'roomtypeid' => 0, // Default value
            'breakfast' => $options['breakfast'] ?? 0,
            'available' => $options['available'] ?? 1,
            'halfBoard' => $options['halfBoard'] ?? 0,
            'ultraAllInclusive' => $options['ultraAllInclusive'] ?? 0,
            'allInclusive' => $options['allInclusive'] ?? 0,
            'refundable' => $options['refundable'] ?? 0,
            'freeWifi' => $options['freeWifi'] ?? 0,
            'fullBoard' => $options['fullBoard'] ?? 0,
            'deposit' => $options['deposit'] ?? 0,
            'smoking' => $options['smoking'] ?? 0,
            'view' => $options['view'] ?? '',
            'viewSentence' => $options['viewSentence'] ?? '',
            'cardRequired' => $options['cardRequired'] ?? 0,
            'beds' => $options['beds']['double'] ?? 0,
            'doublebed' => $options['beds']['double'] ?? 0,
            'twin' => $options['beds']['twin'] ?? 0,
            'balcony' => $options['balcony'] ?? 0,
            'privateBathroom' => $options['privateBathroom'] ?? 0,
            'images' => $options['images'] ?? '',
            'price' => $room['price'] ?? 0,
        	'booking_url' => $room['booking_url'] ?? 0,
            'agencyId' => $room['agencyId'] ?? null,
            'agencyName' => $room['agencyName'] ?? '',
            'dt_created' => now()
        ]);

        // Insert into TPRoomtype (store data as JSON in Roomdesc)
        DB::table('TPRoomtype')->insert([
            'hotelid' => $hid,
            'Roomdesc' => json_encode([
                'roomType' => $desc,
                'options' => $options
            ]),
            'flag' => 1, // Default value
            'created_at' => now()
        ]);
    }
    return "Data inserted into TPRoomtype_tmp and TPRoomtype";
}
            }
           }
       }
    }

	  public function saveTphotel_nearby(request $request){

        $latitude = $request->get('Latitude');
        $longitude = $request->get('longitude');
        $hotelid = $request->get('hotelid');
        $locationid = $request->get('locationid');
        $stars = $request->get('stars');

        $nbs =0;
        $nbh = 0;
        $ns =0;

        $nb_sighttable = DB::table('TPhotel_neaby_sight')->where('hotelid',$hotelid)->get();

           if($latitude != "" && $longitude !=""){
               if (!$nb_sighttable->count() >= 4) {
                   $sredius= 5;
                    $nearby_sights = DB::table("Sight")
                            ->join('Location as l','l.LocationId','=','Sight.LocationId')
                            ->leftjoin('Category as c','c.CategoryId','=','Sight.CategoryId')
                            ->select('Sight.SightId', 'l.slugid','Sight.Title','Sight.LocationId','Sight.Slug',
                            'c.Title as catname','Sight.TAAggregateRating',
                            'Sight.Latitude','Sight.Longitude',
                                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                                * cos(radians(Sight.Latitude))
                                * cos(radians(Sight.Longitude) - radians(" . $longitude . "))
                                + sin(radians(" . $latitude . "))
                                * sin(radians(Sight.Latitude))) AS distance"))
                            ->groupBy("Sight.SightId")
                            ->having('distance', '<=', $sredius)
                            ->orderBy('distance')
                            ->limit(4)
                            ->where('Sight.IsMustSee',1)
                            ->get();


               if (!$nearby_sights->isEmpty()) {
                   $nbs =1;
                   foreach ($nearby_sights as $nearby_sight) {
                       $sightId = $nearby_sight->SightId;
                       $slug = $nearby_sight->Slug;
                       $Title = $nearby_sight->Title;
                       $catname = $nearby_sight->catname;
                       $TAAggregateRating = $nearby_sight->TAAggregateRating;
                       $LocationId = $nearby_sight->slugid;
                       $distance = round($nearby_sight->distance,2);

                       $data= array(
                           'SightId'=>  $sightId,
                           'name'=>$Title,
                           'slug'=>$slug,
                           'LocationId'=>$LocationId,
                           'hotelid'=>$hotelid,
                           'distance'=>$distance,
                           'radius'=>5,
                           'dated'=>now(),
                           'category'=>$catname,
                           'TAAggregateRating'=>$TAAggregateRating
                       );

                       $insertdata = DB::table('TPhotel_neaby_sight')->insert($data);
                   }
               }




           }







            $get_nearby_hotel = DB::table('TPNearby_hotel')->where('hid',$hotelid)->get();

         if (!$get_nearby_hotel->count() >= 5) {
           //  return print_r($get_nearby_hotel);
                $searchradius = 8;
            $lat_range = $searchradius / 111.045;
           $lng_range = $searchradius / (111.045 * cos(deg2rad($latitude)));

        $nearby_hotel = DB::table("TPHotel as h")
            ->join('Temp_Mapping as m', 'm.LocationId', '=', 'h.location_id')
            ->select([
                'h.id',
                'm.slugid',
                'h.name',
                'h.location_id',
                'h.slug',
                'h.address',
                'h.pricefrom',
                'h.stars',
                'h.hotelid as hotid',
                DB::raw("6371 * acos(
                    LEAST(1, cos(radians({$latitude}))
                    * cos(radians(h.Latitude))
                    * cos(radians(h.longnitude) - radians({$longitude}))
                    + sin(radians({$latitude}))
                    * sin(radians(h.Latitude)))
                ) AS distance")
            ])
            // Add bounding box filter
            ->whereRaw('h.Latitude BETWEEN ? AND ?', [
                $latitude - $lat_range,
                $latitude + $lat_range
            ])
            ->whereRaw('h.longnitude BETWEEN ? AND ?', [
                $longitude - $lng_range,
                $longitude + $lng_range
            ])
            // Group the conditions properly
            ->where(function($query) use ($locationid, $hotelid) {
               $query->where('h.location_id', $locationid)
                ->where('h.id', '!=', $hotelid);
            })

            ->having('distance', '<=', $searchradius)
            ->orderBy('distance')
            ->limit(6)
            ->get();


                $savedCount = 0;
           // return print_r($nearby_hotel);
            if(!$nearby_hotel->isEmpty()){

                $nbh =1;


               foreach ($nearby_hotel as $nearby_hotels) {
                        $id = $nearby_hotels->id;
                   if($id != $hotelid){
                       $hot_id = $nearby_hotels->hotid;
                       $slug = $nearby_hotels->slug;
                       $Title = $nearby_hotels->name;
                       $LocationId = $nearby_hotels->slugid;
                       $distance = round($nearby_hotels->distance,2);
                       $address = $nearby_hotels->address;
                       $stars = $nearby_hotels->stars;
                       $pricefrom = $nearby_hotels->pricefrom;


                       $data3= array(
                           'name'=>$Title,
                           'slug'=>$slug,
                           'hotelid'=>$id,
                           'hotel_id'=> $hot_id,
                           'LocationId'=>$LocationId,
                           'distance'=>$distance,
                           'radius'=>$searchradius,
                           'address'=>$address,
                           'stars'=>$stars,
                           'pricefrom'=>$pricefrom,
                           'hid'=>$hotelid,
                           'dated'=>now(),
                       );


                       $insertdata3 = DB::table('TPNearby_hotel')->insert($data3);
                       $savedCount++;
                       if ($savedCount >= 5) {
                           break;
                       }
                   }
               }


           }
         }


        }

     if( $nbs ==1 || $nbh == 1){

           $updated_data = DB::table('TPhotel_neaby_sight')->where('hotelid',$hotelid)->get();
           $html_view = view('hotel_detail_result.Near_by_Attractions', ['nearby_sight' => $updated_data])->render();

           $nearby_hotel =  DB::table('TPNearby_hotel')->where('hid',$hotelid)->get();
           $html3 = view('hotel_detail_result.nearby_hotels',['nearby_hotel'=>$nearby_hotel])->render();
           $html3 = (string) $html3;
               // Return the updated data and HTML as a JSON response
           return response()->json([ 'html' => $html_view,'html3'=>$html3]);
       }



      }

     public function similarhotel(request $request){
           $locid =  $request->get('lid');

        //new code start
        $chkin = date("Y-m-d");
        $checkinDate = date("Y-m-d",strtotime($chkin . ' +2 days'));
        $checkoutDate = date("Y-m-d", strtotime($chkin . ' +8 days'));
        $adultsCount = 2; //$guests;
        $customerIP = '49.156.89.145';
        $childrenCount = '1';
        $chid_age = '10';
        $lang = 'en';
        $currency ='USD';
        $waitForResult ='0';
        $iata=$locid;

        $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
        $TRAVEL_PAYOUT_MARKER = "299178";



       $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
        $checkinDate.":".
        $checkoutDate.":".
        $chid_age.":".
        $childrenCount.":".
        $iata.":".
        $currency.":".
        $customerIP.":".
        $lang.":".
        $waitForResult;

    //  return $SignatureString;
      $signature = md5($SignatureString);
   //  $signature = '3193e161e98200459185e43dd7802c2c'; iata=HKT

     $url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;



          $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {


          $data = json_decode($response);
            if(!empty($data)){
              $searchId = $data->searchId;


            $limit =5;
            $offset=0;
            $roomsCount=0;
            $sortAsc=1;
            $sortBy='price';

              $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
                 $sig2 =  md5($SignatureString2);

                 $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=5&sortBy=price&sortAsc=1&roomsCount=0&offset=0&marker=299178&signature='.$sig2;

                    $response2 = Http::withoutVerifying()->get($url2);
                         $hotel = json_decode($response2, true);

            }else{
                return 'search id not found';
            }

        } else {

            return 2;
        }


        return view('filter_similar_hotel',['gethotel'=>$hotel,'locid'=>$locid]);
    }



	public function filter_hotel_list(request $request)
	{
		$locationid = $request->get('locationid');

		$minPrice = $request->get('priceFrom');
		$priceTo = $request->get('priceTo');
		$typeHotel = $request->get('hoteltype');
		$starRating = $request->get('starRating');
		$mnt = $request->get('mnt');

		$amenities = [];
		if(is_string($mnt)){
			$amenities = explode(',', $mnt);
		}

		$typeHotels = [];
		if (is_string($typeHotel)) {
			$typeHotels = explode(',', $typeHotel);
		}

		$userrating = $request->get('userrating');
		$user_rating = [];
		if (is_string($userrating)) {
			$user_rating = explode(',', $userrating);
		}


		$address = $request->get('address');
		$distance = $request->get('distance');




		$st = "";
		if($starRating ==1){
			$st = 0;
		}
		// [$searchresults] = DB::selectResultSets(
		//     "CALL getHotelsListFiltered('$id','$minPrice','$priceTo','$typeHotel','$userrating','$starRating','$distance','$neibourhood')"
		//     );
		$searchresults =collect();

		if (!empty($minPrice) && !empty($priceTo) && !empty($amenities) && !empty($user_rating) && !empty($starRating) && !empty($typeHotels) && $address ) {

			$searchresults  = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where('h.stars', $starRating)
				->where(function ($query) use ($typeHotels) {
					$query->whereIn('h.propertyType', $typeHotels);
				})
				->whereBetween('h.pricefrom', [$minPrice, $priceTo])
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->where(function ($query) use ($user_rating) {
					$query->whereIn('h.amenities', $user_rating);
				})
				->where('h.distance', '>=',$distance)
				->where('h.address', 'LIKE', $address . '%')
				->where('h.Pincode', $address)
				->limit(10)
				->get();
		} elseif (!empty($starRating) && !empty($amenities) && !empty($starRating)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where('h.stars', $starRating)
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->limit(10)
				->get();
		} elseif (!empty($starRating) && !empty($amenities)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where('h.stars', $starRating)
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->limit(10)
				->get();
		} elseif (!empty($amenities) && !empty($user_rating) ) {

			$searchresults= DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->orWhere(function ($query) use ($user_rating) {
					$query->whereIn('h.amenities', $user_rating);
				})
				->limit(10)
				->get();

		}elseif (!empty($starRating) ) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where('h.stars', $starRating)
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->limit(10)
				->get();

		}elseif (!empty($minPrice) && !empty($priceTo) && !empty($amenities)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->whereBetween('h.pricefrom', [$minPrice, $priceTo])
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->whereBetween('h.pricefrom', [$minPrice, $priceTo])
				->limit(10)
				->get();



		}elseif (!empty($minPrice) && !empty($priceTo) ) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)

				->limit(10)
				->get();


		} elseif (!empty($amenities)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where(function ($query) use ($amenities) {
					foreach ($amenities as $amenity) {
						$query->where('h.amenities', 'LIKE', $amenity . '%');
					}
				})
				->limit(10)
				->get();
		} elseif (!empty($user_rating)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where(function ($query) use ($user_rating) {
					$query->whereIn('h.amenities', $user_rating);
				})
				->limit(10)
				->get();
		} elseif (!empty($starRating)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where('h.stars', $starRating)
				->limit(10)
				->get();
		} elseif (!empty($address)) {

			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->where('h.address', 'LIKE', $address . '%')
				->where('h.Pincode', $address)
				->limit(10)
				->get();
		}else{
			$searchresults = DB::table('TPHotel as h')
				->select('h.hotelid', 'h.id', 'h.location_id as loc_id', 'h.name', 'h.address', 'h.slug', 'h.distance', 'h.stars', 'h.pricefrom', 'h.rating',
						 'h.photos', 'h.facilities', 'h.amenities', 'h.shortFacilities',
						 'l.fullName', 'l.countryName', 'l.cityName', 'ty.type as propertyType')
				->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
				->leftjoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
				->where('h.location_id', $locationid)
				->where('h.distance', '<=', $distance)
				->limit(10)
				->get();
		}





		$lname ="";
		$countryName ="";
		if(!$searchresults->isEmpty()){
			$lname = $searchresults[0]->cityName;
			$countryName = $searchresults[0]->countryName;
		}


		return view('filter_hotel_list')->with('searchresults',$searchresults)->with('lname',$lname)->with('countryname',$countryName);
	}

	public function getfilteredhotellist(request $request){

		$gethoteltype =collect();
		$locationid = $request->get('locationid');
		$rooms = $request->get('rooms');
		if( $rooms ==""){
			$rooms = 1;
		}
		$guest = $request->get('guest');

		$Tid = $request->get('Tid');
		session(['rooms' => $rooms]);
		session(['guest' => $guest]);
		if(session()->has('checkin')){
			$getval = session('checkin');
			$value=  explode('_',$getval);
			$chkin = $value[0];
			$checout = $value[1];

		}else{
			$chkin = $request->get('checkin');
			$checout = $request->get('checkout');

		}


		$searchresults = collect();

		$datavalues = [];
		$checkinDate = $chkin;
		$checkoutDate = $checout;
		$adults = (int) $request->get('guest');

		// Initialize session variables to prevent undefined variable notices
		$session_adult_count = 0;
		$sessionCheckin = null;
		$sessionCheckout = null;
		//return session()->all();
		// Check if 'data_save' exists in the session
		if (session()->has('data_save')) {
			$sessionCheckin = session('checkinDate');
			$sessionCheckout = session('checkoutDate');
			$session_adult_count = session('table_guestcount');
			$lo_id = session('lo_id');
		}
		//return session()->all();
		if ($checkinDate != $sessionCheckin || $checkoutDate != $sessionCheckout || !session()->has('data_save') || intval($lo_id) != intval($Tid) ) {
	      DB::table('hotelbookingstemp')->truncate();

			//new code start
			$checkinDate =  $chkin;
			$checkoutDate = $checout;
			$adultsCount = $guest;
			$customerIP = '49.156.89.145';
			$childrenCount = '1';
			$chid_age = '10';
			$lang = 'en';
			$currency ='USD';
			$waitForResult ='0';
			$iata= $locationid ;//24072

			$TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
			$TRAVEL_PAYOUT_MARKER = "299178";
			$SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
				$checkinDate.":".
				$checkoutDate.":".
				$chid_age.":".
				$childrenCount.":".
				$iata.":".
				$currency.":".
				$customerIP.":".
				$lang.":".
				$waitForResult;

			$signature = md5($SignatureString);

			$url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;

			$response = Http::withoutVerifying()->get($url);

			if ($response->successful()) {
				$data = json_decode($response);
				if(!empty($data)){
					$searchId = $data->searchId;
					$limit = 0;
					$offset=0;
					$roomsCount=0;
					$sortAsc=0;
					$sortBy='stars';
					$SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
					$sig2 =  md5($SignatureString2);
					$url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=0&sortBy=stars&sortAsc=0&roomsCount=0&offset=0&marker=299178&signature='.$sig2;
					$maxAttempts = 3;
					$retryInterval = 1;
					$response2 = Http::withoutVerifying()->timeout(0)->retry($maxAttempts, $retryInterval)->get($url2);

					$responseData = $response2->json();

					// $end =  date("H:i:s");

					//     return $start.'=='.$end;
					if ($responseData['status'] === 'error' && $responseData['errorCode'] === 409) {
						$status = 4;
						return 'Search is not finished.';
					}else{
						$status = 1;
					}


					if ($response2->successful()) {
						$hotel = json_decode($response2);
						$idArray = array_column($hotel->result, 'id');
						$idArray = array_filter($idArray, function ($id) {
							return isset($id);
						});
						$idArray = array_unique($idArray);

						//  $limitedIdArray = array_slice($idArray, 0, 100);
					  $searchresults = DB::table('TPHotel as h')
                                    ->select('h.hotelid', 'h.id', 'h.name', 'h.slug', 'h.stars', 'h.rating',
                                      'h.amenities', 'h.distance', 'h.slugid', 'h.room_aminities','h.Latitude','h.longnitude','h.CountryName','h.CityName','h.short_description',
                                              DB::raw('GROUP_CONCAT(CONCAT(a.shortName, "|", a.image) ORDER BY a.name SEPARATOR ", ") as amenity_info'))
                                    ->leftJoin('TPHotel_amenities as a', DB::raw('FIND_IN_SET(a.id, h.shortFacilities)'), '>', DB::raw('0'))
                                    ->whereIn('h.hotelid', $idArray)
                                    ->whereNotNull('h.slugid')
                                    ->groupBy('h.id')
						            ->orderby('h.stars','desc')
                                    ->paginate(30)
                                    ->withQueryString();

						$url = 'filter_availble_hotel.html';
						$searchresults->appends(request()->except(['_token']));

						$searchresults->setPath($url);
						$paginationLinks = $searchresults->links('hotellist_pagg.default');

						$hotelpage = 'hotelpage';

						$count_result = $searchresults->total();
						// Start code



						//	|| $adults != $session_adult_count


						// Clear specific session values
						Session::forget('checkinDate');
						Session::forget('checkoutDate');
						Session::forget('table_guestcount');
						Session::forget('data_save');

						// Store the new values in session
						Session::put('checkinDate', $checkinDate);
						Session::put('checkoutDate', $checkoutDate);
						Session::put('table_guestcount', $adults);
						Session::put('data_save', '1');
						Session::put('lo_id', $Tid);



						// If session values are different, proceed with inserting data
						if (!empty($hotel->result)) {
							foreach ($hotel->result as $hotel_results) {
								$id = $hotel_results->id;
								foreach ($hotel_results->rooms as $room) {
									$price = $room->total;
									$agencyId = $room->agencyId;
									$fullurl = $room->fullBookingURL;
									$options = $room->options;
									$desc = $room->desc;
									$agency_name = $room->agencyName;
									$agencyId = $room->agencyId;
									$beds = isset($options->beds) ? (array) $options->beds : [];
									$saveValue = in_array(1, $beds) ? 1 : null;

									// Convert the object to an array for filtering
									$trueOptions = array_keys(array_filter((array) $options, function ($value, $key) {
										return $value === true && $key != 'beds';
									}, ARRAY_FILTER_USE_BOTH));

									$optionsString = implode(',', $trueOptions);

									$datavalues[] = [
										'booking_checkin_date' => $checkinDate,
										'booking_checkout_date' => $checkoutDate,
										'hotelid' => $id,
										'rooms' => $saveValue,
										'amenity' => $optionsString,
										'room_type' => $desc,
										'agency_name' => $agency_name,
										'booking_link' => $fullurl,
										'guest' => $adults, // Use $adults here to match the session variable
										'price' => $price,
										'agency_id' => $agencyId,

									];
								}
							}

							// Insert all data at once

							if (!empty($datavalues)) {
								DB::table('hotelbookingstemp')->insert($datavalues);
								DB::table('historic_cost')->insert($datavalues);
							}
						}
						//}


						// end insert data
						// Retrieve and return the updated session values
						$updated_adult_count = session('table_guestcount');
						$updated_checkinDate = session('checkinDate');
						$updated_checkoutDate = session('checkoutDate');

						// End code
						$hotelIds = array_unique($searchresults->pluck('hotelid')->toArray()) ;
						$hotelpricedata = DB::table('hotelbookingstemp')
							->whereIn('hotelid', $hotelIds)
							->get();
						  $uniqueAgencies =null;
                        if(!$hotelpricedata->isEmpty()){
                            $uniqueAgencies = $hotelpricedata->pluck('agency_name')->unique();
                        }
						$html = view('frontend.hotel.get_filtered_hotels', [
							'hotels' => $hotelpricedata,
							'TRAVEL_PAYOUT_TOKEN' => $TRAVEL_PAYOUT_TOKEN,
							'locid' => $locationid,
							'searchresults' => $searchresults,
							'LocationId' => $Tid,
							'checkinDate' => $checkinDate,
							'checkoutDate' => $checkoutDate,
							'count_result' => $count_result,
						])->render();

						//return print_r(	session()->all());
 					if (!session()->has('filterd')) {
						// Return JSON response

						return response()->json([
							'html' => $html,
							'count_result' => $count_result,
							 'uniqueAgencies' => $uniqueAgencies,
						]);
					}

					}


				}else{
					return 'search id not found';
				}

			} else {

				return 2;
			}


		}else{

			$hotelpricedata = DB::table('hotelbookingstemp as h')
				->select('h.hotelid','h.amenity', 'h.room_type', 'h.booking_link','h.price','h.agency_id')
				->get();
			$hotelIds = array_unique($hotelpricedata->pluck('hotelid')->toArray()) ;
          //  $uniqueAgencies = $hotelpricedata->pluck('agency_name')->unique();


  		  $getagenc = DB::table('hotelbookingstemp as h')
			->leftJoin('TPHotel as t', 't.hotelid','=','h.hotelid')
			->select('agency_name')
			->get();

			$uniqueAgencies =null;
			if(!$getagenc->isEmpty()){
				$uniqueAgencies = $getagenc->pluck('agency_name')->unique();
			}

			$searchresults = DB::table('TPHotel as h')
			->select(
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
				'h.CityName','h.short_description','h.Latitude','h.longnitude','h.CountryName',
			  DB::raw('GROUP_CONCAT(CONCAT(a.shortName, "|", a.image) ORDER BY a.name SEPARATOR ", ") as amenity_info')
			)
			->leftJoin('TPHotel_amenities as a', DB::raw('FIND_IN_SET(a.id, h.shortFacilities)'), '>', DB::raw('0'))
			->whereNotNull('h.slugid')
			->whereIn('h.hotelid', $hotelIds)
			->groupBy('h.id') // Group by hotel ID to use GROUP_CONCAT
			->orderby('h.stars','desc')
			->paginate(30)
			->withQueryString();

			$url = 'filter_availble_hotel.html';
			$searchresults->appends(request()->except(['_token']));
			$searchresults->setPath($url);
			$paginationLinks = $searchresults->links('hotellist_pagg.default');
			$hotelpage = 'hotelpage';

			$count_result =  $searchresults->total();

			$html = view('frontend.hotel.get_filtered_hotels', [
				'hotels' => $hotelpricedata,
				'locid' => $locationid,
				'searchresults' => $searchresults,
				'LocationId' => $Tid,
				'checkinDate' => $checkinDate,
				'checkoutDate' => $checkoutDate,
				'count_result' => $count_result,
			])->render();
	//return print_r(	session()->all());

			if (!session()->has('filterd')) {
					return response()->json([
						'html' => $html,
						'count_result' => $count_result,
						 'uniqueAgencies' => $uniqueAgencies,
					]);
			}
		}





	}
	// dowload logo and update min max price


	public function donload_agencyimg(request  $request){


    $chkin = $request->get('checkin');
    $checout = $request->get('checkout');

     $rooms = $request->get('rooms');
     $guest = $request->get('guest');

     $locationid =  $request->get('locationid');

     $searchresults =collect();


     //new code start
     $checkinDate =  $chkin;
     $checkoutDate = $checout;
     $adultsCount = $guest;
     $customerIP = '49.156.89.145';
     $childrenCount = '1';
     $chid_age = '10';
     $lang = 'en';
     $currency ='USD';
     $waitForResult ='0';
     $iata= $locationid ;//24072

     $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
     $TRAVEL_PAYOUT_MARKER = "299178";



    $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
     $checkinDate.":".
     $checkoutDate.":".
     $chid_age.":".
     $childrenCount.":".
     $iata.":".
     $currency.":".
     $customerIP.":".
     $lang.":".
     $waitForResult;


   $signature = md5($SignatureString);
//  $signature = '3193e161e98200459185e43dd7802c2c'; iata=HKT

  $url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;



       $response = Http::withoutVerifying()->get($url);

     if ($response->successful()) {


       $data = json_decode($response);
         if(!empty($data)){
           $searchId = $data->searchId;


         $limit =0;
         $offset=0;
         $roomsCount=0;
         $sortAsc=1;
         $sortBy='price';

           $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
              $sig2 =  md5($SignatureString2);

              $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=0&sortBy=price&sortAsc=1&roomsCount=0&offset=0&marker=299178&signature='.$sig2;

                     $gethoteltype =collect();
                 $response2 = Http::withoutVerifying()->timeout(30)->get($url2);
            //    $response2 = Http::timeout(30)->retry(3, 100)->get($url2);
            sleep(5);

             $responseData = $response2->json();
                 if ($responseData['status'] === 'error' && $responseData['errorCode'] === 4) {
                    $status = 4;
                    return 'Search is not finished.';
                    //  $response2 = Http::withoutVerifying()->get($url2);

                }else{
                    $status = 1;
                }

             $maxRetries = 10; // Set a maximum number of retries
            //	$retryInterval = 5; // Set the interval between retries in seconds

                //for ($i = 0; $i < $maxRetries; $i++) {

                //}
                 if ($response2->successful()) {

                     $hotel = json_decode($response2);

                     $idArray = array();

                     foreach ($hotel->result as $hotelInfo) {
                         if (isset($hotelInfo->id)) {
                             $idArray[] = $hotelInfo->id;
                         }
                     }

                     $getpricenull = DB::table('TPHotel as h')
                     ->select('h.hotelid','h.minprice','h.maxprice')
                     ->whereIn('h.hotelid', $idArray)
                     ->where(function($query) {
                         $query->whereNull('h.minprice')
                               ->orWhereNull('h.maxprice');
                     })
                     ->get();

                        foreach ($hotel->result as $searchresult) {

                        $hotelid = $searchresult->id;
                     //   $getprice =  DB::table('TPHotel')->select('minprice','maxprice')->where('hotelid',$hotelid)->get();
                        // $minprice =  $getprice[0]->minprice;
                        // $maxprice =  $getprice[0]->maxprice;

                     //   if($maxprice == "" || $minprice=""){
                        if($getpricenull->isEmpty()){
                            $minPriceTotal = $searchresult->minPriceTotal;
                            $maxPrice = $searchresult->maxPrice;

                            $price=array(
                                'minprice'=>$minPriceTotal,
                                'maxprice'=> $maxPrice,
                            );

                           DB::table('TPHotel')->where('hotelid',$hotelid)->update($price);
                        }
                      }

                     foreach ($hotel->result as $searchresult) {

                        foreach ($searchresult->rooms as $room) {


                            $agencyId =  $room->agencyId;

                            $getagency = DB::table('agencies')->where('agencyId', $agencyId)->get();

                            if ($getagency->isEmpty()) {
                                $imagePath = 'public/agency-image/' . $agencyId . '.png';

                              if (!File::exists($imagePath)) {
                                $imageUrl = 'http://pics.avs.io/hl_gates/100/100/' . $agencyId . '.png';
                                $storagePath = 'public/agency-image/' . $agencyId . '.png';

                                $response = Http::withoutVerifying()->get($imageUrl);

                                if ($response->successful()) {

                                    File::put($storagePath, $response->body());

                                    DB::table('agencies')->insert([
                                        'agencyId' => $agencyId,
                                        'agencyName' => $room->agencyName,
                                        'imageUrl' => $imageUrl,
                                        'imagepath' => 'agency/' . $agencyId . '.png',
                                    ]);


                                    continue;
                                } else {

                                    return "Failed to download the image for agency ID $agencyId.";
                                }
                            } else {
                                return "Image for agency ID $agencyId already exists.";
                            }


                            }
                        }



                    }
                    //end


                     // end download logo



                 }


         }else{
             return 'search id not found';
         }

     } else {

         return 2;
     }


}

//end save logo


   public function filter_availble_hotel_old(request $request){
       $getval = $request->get('checkin') .'-'.$request->get('checkout');
       $chkin = $request->get('checkin');
       $checout = $request->get('checkout');

        // $value=  explode('-',$getval);
        // $checkin = $value[0];
        // $checkout = $value[1];
        $rooms = $request->get('rooms');
        $guest = $request->get('guest');
        $lid =  $request->get('lid');
      //  $rooms = $request->get('child1');
      //  $guest = $request->get('child2');
            // if (session()->has('checkin')) {
            //     session()->forget('checkin');
            // }


        session(['checkin' => $getval]);
        session(['rooms' => $rooms]);
        session(['guest' => $guest]);


      //  $id='675';
        // if (!empty($getval)) {
        //    $chkin =  date('Y-m-d',strtotime( $checkin));
        //    $checout =  date('Y-m-d',strtotime( $checkout));

        // } else {
        //     return 0;
        // }


        //new code start
        $checkinDate =  $chkin;
        $checkoutDate = $checout;
        $adultsCount = 2; //$guests;
        $customerIP = '49.156.89.145';
        $childrenCount = '1';
        $chid_age = '10';
        $lang = 'en';
        $currency ='USD';
        $waitForResult ='0';
        $iata= 24072;// $lid ;

        $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
        $TRAVEL_PAYOUT_MARKER = "299178";



       $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
        $checkinDate.":".
        $checkoutDate.":".
        $chid_age.":".
        $childrenCount.":".
        $iata.":".
        $currency.":".
        $customerIP.":".
        $lang.":".
        $waitForResult;

    //  return $SignatureString;
      $signature = md5($SignatureString);
   //  $signature = '3193e161e98200459185e43dd7802c2c'; iata=HKT
//locationId
     $url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;



          $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {


          $data = json_decode($response);
            if(!empty($data)){
              $searchId = $data->searchId;


            $limit =10;
            $offset=0;
            $roomsCount=0;
            $sortAsc=1;
            $sortBy='price';

              $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
                 $sig2 =  md5($SignatureString2);

                 $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=10&sortBy=price&sortAsc=1&roomsCount=0&offset=0&marker=299178&signature='.$sig2;

                    $response2 = Http::withoutVerifying()->get($url2);

                    if ($response2->successful() ) {

                        $hotel = json_decode($response2);


    				 //     $responseContent = $response2->body();
					//  if (strpos($responseContent, 'errorCode: 4') !== false) {
							// Handle the case when the response contains "errorCode: 4"
						//	$hotel['data_status'] = 4;

						//}


                        return view('hotel_list_api_result',['hotels'=> $hotel,'TRAVEL_PAYOUT_TOKEN'=>$TRAVEL_PAYOUT_TOKEN,'locid'=>$locationid]);
                    }


            }else{
                return 'search id not found';
            }

        } else {

            return 2;
        }

        // [$searchresults] = DB::selectResultSets(
        //     "CALL getHotelsListFiltered('$id','$minPrice','$priceTo','$typeHotel','$userrating','$starRating','$distance','$neibourhood')"
        //     );


        // return view('filter_hotel_list')->with('searchresults',$searchresults);
    }


public function add_hoteldata()
{
    $url = 'http://engine.hotellook.com/api/v2/static/hotels.json?locationId=895&token=27bde6e1d4b86710997b1fd75be0d869';
    $response = Http::get($url);

    if ($response->successful()) {
        $hotelsData = $response->json();


        if (isset($hotelsData['pois'])) {

            $hotels = $hotelsData['pois'];

            foreach ($hotels as $hotel) {
                // Store the hotel data in the 'hotels' table
        //      echo   $hotel['location']['lon'];
        //    echo  $hotel['name'];

                // DB::table('PriyankaT')->insert([
                //     'id' => $hotel['id'],
                //         'name' => $hotel['name'],
                //     'rating' => $hotel['rating'],
                //     'category' => $hotel['category'],
                //     'lat' => $hotel['location']['lat'],
                //     'lon' => $hotel['location']['lon'],
                //     'type' => "Point",
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ]);
            }


        } else {
            $this->error('No hotels data found in the API response.');
        }
    } else {
        $this->error('Failed to fetch hotels data from the API.');
    }
}

public function addLocationfaqfont(request $request){

    $locationid = $request->get('locationIdValue');




    $getloc = DB::table('Location')->where('LocationId',$locationid)->get();
      $lname = $getloc[0]->Name;

          //kids outdoor activity


$titlesToMatch = [
  'Disney Parks & Activities',
  'Theme Parks',
  'Water Parks',
  'Aquariums',
  'Other Zoos & Aquariums',
  'Zoos',
  'Bowling Alleys',
  'Game & Entertainment Centers',
  'Mini Golf',
  'Other Fun & Games',
  'Playgrounds',
  'Rides & Activities',
  'Room Escape Games',
  "Children's Museums",
  'Parks',
  'Playgrounds',
];

$categoryIdsks = DB::table('Category')
  ->whereIn('Title', $titlesToMatch)
  ->pluck('CategoryId','Title')
  ->toArray();


$getkidsoutdoor = DB::table('Sight as s')
    ->join('Location as l','l.LocationId','=','s.LocationId')
    ->where('s.LocationId', $locationid)
    ->where('s.ReviewCount', '>', 20)
    ->whereIn('s.CategoryId', $categoryIdsks)
    ->select('s.Title','s.SightId','s.Slug','l.slugid')
    ->limit(5)
    ->get();


$outkids_site = [];

foreach ($getkidsoutdoor as $ks) {
    $outkids_site[] = [
        'name' => $ks->Title,
        'url' => $ks->SightId.'-'.$ks->Slug,
    ];
}

//   /* delete faq if exist*/


//     DB::table('LocationQuestion')
//    ->where('LocationId', $locationid)
//    ->where('Question', 'What are the best outdoor activities in '. $lname )
//    ->delete();

// Now $outdoorloc contains the matched outdoor locations
$existingEntry_outdoor = DB::table('LocationQuestion')
->where('LocationId', $locationid)
->where('Question', 'What the best outdoor activities for Kids in '. $lname )
->exists();

if (!$existingEntry_outdoor) {
        if(!empty($outkids_site)){
            $outdoorkds = array(
                'LocationId'=>$locationid,
                'slugid'=>$getkidsoutdoor[0]->slugid,
                'Question'=>'What the best outdoor activities for Kids in '. $lname,
                'Answer' => 'The best outdoor activities for Kids in ' . $lname.' are:',
                'listing'=>json_encode($outkids_site),
                'CreatedDate' => now(),
            );
              DB::table('LocationQuestion')->insert($outdoorkds);
        }
    }else{
         'record already exist';
      //    if(!empty($outkids_site)){
      //       $outdoor = array(
      //           'LocationId'=>$locationid,
      //           'Question'=>'What are the best outdoor activities in '. $lname,
      //           'Answer' => 'The best outdoor activities in ' . $lname.' are:',
      //           'listing'=>json_encode($outdoorloc),
      //           'CreatedDate' => now(),
      //       );
      //       DB::table('LocationQuestion')->where('LocationId',$locationid)->where('Question', 'What are the best outdoor activities in '. $lname )->update($outdoor);
      //   }
    }






  //end kids outdoor activity
             DB::table('LocationQuestion')
   ->where('LocationId', $locationid)
   ->where('Question', 'What are the top attractions to visit in ' . $lname)
   ->delete();
    $existingEntry = DB::table('LocationQuestion')
          ->where('LocationId', $locationid)
          ->where('Question', 'What are the top attractions to visit in ' . $lname)
          ->exists();



      //dt
      $gettopatt = DB::table('Sight as s')
      ->join('Location as l','l.LocationId','=','s.LocationId')
      ->select('s.Title','s.SightId','s.Slug','l.slugid')
      ->where('s.LocationId',$locationid)
      ->where('s.ReviewCount', '>', 20)
      ->orderby('s.TAAggregateRating','desc')
      ->limit(5)
      ->get();

          $sightIds = [];

          foreach ($gettopatt as $gettopatts) {
              $sights[] = [
                  'name' => $gettopatts->Title,
                  'url' => $gettopatts->SightId.'-'.$gettopatts->Slug,
              ];
          }



  if (!$existingEntry) {

      if(!empty($sights)){
      $data = array(
          'LocationId'=>$locationid,
          'slugid'=>$gettopatt[0]->slugid,
          'Question'=>'What are the top attractions to visit in '. $lname,
          'Answer' => 'The top attractions to visit in ' . $lname.' are:',
          'listing'=>json_encode($sights),
          'CreatedDate' => now(),
      );
      DB::table('LocationQuestion')->insert($data);
    }
  }else{
      'record already exist';
      if(!empty($sights)){

      $data = array(
          'LocationId'=>$locationid,
          'slugid'=>$gettopatt[0]->slugid,
          'Question'=>'What are the top attractions to visit in '. $lname,
          'Answer' => 'The top attractions to visit in ' . $lname.' are:',
          'listing'=>json_encode($sights),
          'CreatedDate' => now(),
      );

       DB::table('LocationQuestion')->where('LocationId',$locationid)->where('Question', 'What are the top attractions to visit in ' . $lname)->update($data);
    }
  }

  // outdoor activity

  $get_out_cat = DB::table('Category')->where('ParentId', 1238)->get();

  $ctids = [];

  foreach ($get_out_cat as $cat) {
      $ctids[] = $cat->CategoryId;
  }

  $getoutdoor = DB::table('Sight as s')
      ->join('Location as l','l.LocationId','=','s.LocationId')
      ->select('s.Title','s.SightId','s.Slug','l.slugid',)
      ->where('s.LocationId', $locationid)
      ->whereIn('s.CategoryId', $ctids)
      ->where('s.ReviewCount', '>', 20)
      ->orderBy('s.TAAggregateRating', 'desc')
      ->limit(5)
      ->get();


  $outdoorloc = [];

  foreach ($getoutdoor as $sight) {
      $outdoorloc[] = [
          'name' => $sight->Title,
          'url' => $sight->SightId.'-'.$sight->Slug,
      ];
  }

  /* delete faq if exist*/


    DB::table('LocationQuestion')
   ->where('LocationId', $locationid)
   ->where('Question', 'What are the best outdoor activities in '. $lname )
   ->delete();

  // Now $outdoorloc contains the matched outdoor locations
  $existingEntry_outdoor = DB::table('LocationQuestion')
  ->where('LocationId', $locationid)
  ->where('Question', 'What are the best outdoor activities in '. $lname )
  ->exists();
  if (!$existingEntry_outdoor) {
          if(!empty($outdoorloc)){
              $outdoor = array(
                  'LocationId'=>$locationid,
                  'slugid'=>$getoutdoor[0]->slugid,
                  'Question'=>'What are the best outdoor activities in '. $lname,
                  'Answer' => 'The best outdoor activities in ' . $lname.' are:',
                  'listing'=>json_encode($outdoorloc),
                  'CreatedDate' => now(),
              );
                DB::table('LocationQuestion')->insert($outdoor);
          }
      }else{
           'record already exist';
           if(!empty($outdoorloc)){
              $outdoor = array(
                  'LocationId'=>$locationid,
                  'Question'=>'What are the best outdoor activities in '. $lname,
                  'Answer' => 'The best outdoor activities in ' . $lname.' are:',
                  'listing'=>json_encode($outdoorloc),
                  'CreatedDate' => now(),
              );
              DB::table('LocationQuestion')->where('LocationId',$locationid)->where('Question', 'What are the best outdoor activities in '. $lname )->update($outdoor);
          }
      }

      // childern
      $get_child_loc = DB::table('Category')->where('ParentId', 1349)->orWhere('ParentId', 1353)->get();

      $catid = [];

      foreach ($get_child_loc as $get_child_loc) {
          $catid[] = $get_child_loc->CategoryId;
      }

      $getsights_child = DB::table('Sight as s')
      ->join('Location as l','l.LocationId','=','s.LocationId')
      ->select('s.Title','s.SightId','s.Slug','l.slugid',)
      ->where('s.LocationId', $locationid)
      ->whereIn('s.CategoryId', $catid)
          ->where('s.ReviewCount', '>', 20)
          ->orderBy('s.TAAggregateRating', 'desc')
          ->limit(5)
          ->get();


      $child_loc = [];

      foreach ($getsights_child as $getsights_childs) {
          $child_loc[] = [
              'name' => $getsights_childs->Title,
              'url' => $getsights_childs->SightId.'-'.$getsights_childs->Slug,
          ];
      }


      $alredyexist = DB::table('LocationQuestion')
      ->where('LocationId', $locationid)
      ->where('Question', 'What are the most popular things to do in '. $lname. ' with children')
      ->exists();
      if (!$alredyexist) {
              if(!empty($child_loc)){
                  $childlocdata = array(
                      'LocationId'=>$locationid,
                      'slugid'=>$getsights_child[0]->slugid,
                      'Question'=>'What are the most popular things to do in '. $lname. ' with children',
                      'Answer' => 'The most popular things to do in ' . $lname.' with children are:',
                      'listing'=>json_encode($child_loc),
                      'CreatedDate' => now(),
                  );
                  DB::table('LocationQuestion')->insert($childlocdata);
              }
      }else{
           'record already exist';
           if(!empty($child_loc)){
              $childlocdata = array(
                  'LocationId'=>$locationid,
                  'slugid'=>$getsights_child[0]->slugid,
                  'Question'=>'What are the most popular things to do in '. $lname. ' with children',
                  'Answer' => 'The most popular things to do in ' . $lname.' with children are:',
                  'listing'=>json_encode($child_loc),
                  'CreatedDate' => now(),
              );
              DB::table('LocationQuestion')->where('LocationId',$locationid)->where('Question', 'What are the most popular things to do in '. $lname. ' with children')->update($childlocdata);
          }
      }
      $faq =  DB::table('LocationQuestion')->where('locationid',$locationid)->get();
      return view('get_faq_data',['faq'=>$faq,'lname'=>$lname,'locid'=>$locationid]);
  }



  //end  sight faq


//start sight faq

     public function addsightfaqfront(request $request){
        $locationid = $request->get('locationIdValue');
        $sightid = $request->get('sightId');

        $getsightname = DB::table('Sight')->where('SightId',$sightid)->limit(1)->get();
           $Sname = $getsightname[0]->Title;

        $gettiming = DB::table('SightTiming')->where('SightId',$sightid)->get();

        $existingEntry = DB::table('SightListingDetailFaq')
            ->where('SightId', $sightid)
            ->where('Faquestion', 'When is ' . $Sname . ' open')
            ->exists();

        if(!$gettiming->isEmpty()){

            if (!$existingEntry) {
            $data = array(
                'SightId'=>$sightid,
                'Faquestion'=>'When is ' . $Sname . ' open',
                'Answer' => 'The ' . $Sname.' is open:',
                'timing' =>$gettiming[0]->timings,
                'CreatedOn' => now(),
                'OrderFaq' => 1,
            );
                DB::table('SightListingDetailFaq')->insert($data);

            }else{
                'record already exist';
            }
        }
            $faq =  DB::table('SightListingDetailFaq')->where('SightId',$sightid)->get();
                return view('add_explorefaq',['faq'=>$faq,'Sname'=>$Sname]);
        }

     //    aad reviews

        public function add_sightreview(request $request){

        //     if (Auth::check()) {
        //         $userId = Auth::id();
        //        $getuser = DB::table('user')->where('userid',$userId)->get();
        //        return  $email = $getuser[0]->EmailAddress;
        //          $Name = $getuser[0]->GivenName.' '.$getuser[0]->Surname;
        //        $data = array(
        //         'Name' => $Name,
        //         'Email' => $email,
        //         'ReviewDescription' => $request->get('review'),
        //         'ReviewRating' => $request->get('rating'),
        //         'SightId' =>$request->get('sightId'),
        //     );

        //     } else {

        //         $data = array(
        //             'Name' => $request->get('name'),
        //             'Email' => $request->get('email'),
        //             'ReviewDescription' => $request->get('review'),
        //             'ReviewRating' => $request->get('rating'),
        //             'SightId' =>$request->get('sightId'),
        //         );
        //     }
        //    $result = DB::table('SightReviews')->insert($data);
        //    if($result){
        //     return 'Review added successfully.';
        //    }

              $uploadedFiles = $request->file('files');

           $data = array(
            'Name' => $request->get('name'),
            'Email' => $request->get('email'),
            'ReviewDescription' => $request->get('review'),
            'IsRecommend' => $request->get('rating'),
            'SightId' =>$request->get('sightId'),
            'CreatedDate' => now(),
           );

            $result = DB::table('SightReviews')->insertGetId($data);

            if(!empty($uploadedFiles)){
                foreach ($uploadedFiles as $image) {
                    if ($image->isValid()) {
                        $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('review-images'), $imageName);


                        $data = [
                            'SightReviewId'=>$result,
                            'Image' => $imageName,
                            'created_at'=>now(),
                        ];

                        $getreview = DB::table('sight_review_image')->insert($data);
                    }
                }
             }
            if($result){
              $getrv = DB::table('SightReviews')->where('SightId',$request->get('sightId'))->get();
             if(!$getrv->isEmpty()){
                $totalReviews = $getrv->count();
                $recommendedCount = $getrv->where('IsRecommend', 1)->count();
                $notRecommendedCount = $totalReviews - $recommendedCount;
                $positiveReviews = $recommendedCount;
                $negativeReviews = $notRecommendedCount;
                $averageRating = ($positiveReviews * 5 + $negativeReviews * 1) / $totalReviews;
               $averageRatingPercentage = round(($averageRating / 5) * 100, 2);
               $averageRatingPercentage = floor($averageRatingPercentage);
             }


               $reviewhtml =  view('updated_sight_review',[ 'sightreviews'=>$getrv])->render();
               return response()->json(['reviewhtml'=>$reviewhtml,'positiveReviews'=>$positiveReviews,'negativeReviews'=>$negativeReviews,'averageRatingPercentage'=>$averageRatingPercentage]);
            }
        }
        public function add_hotelreview(request $request){

            // if (Auth::check()) {
            //     $userId = Auth::id();
            //    $getuser = DB::table('user')->where('userid',$userId)->get();
            //    return  $email = $getuser[0]->EmailAddress;
            //      $Name = $getuser[0]->GivenName.' '.$getuser[0]->Surname;
            //    $data = array(
            //     'Name' => $Name,
            //     'Email' => $email,
            //     'Description' => $request->get('review'),
            //     'Rating' => $request->get('rating'),
            //     'HotelId' =>$request->get('hotelid'),
            //     'UserId' => 0,
            //     'IsActive'=>0,
            // );

            // } else {

            //     $data = array(
            //         'Name' => $request->get('name'),
            //         'Email' => $request->get('email'),
            //         'Description' => $request->get('review'),
            //         'Rating' => $request->get('rating'),
            //         'HotelId' =>$request->get('hotelid'),
            //         'UserId' => 0,
            //         'IsActive'=>0,
            //     );

            // }
            // $result = DB::table('HotelReview')->insert($data);
            // if($result){
            //  return 'Review added successfully.';
            // }


            $data = array(
                'Name' => $request->get('name'),
                'Email' => $request->get('email'),
                'Description' => $request->get('review'),
                'Rating' => $request->get('rating'),
                'HotelId' =>$request->get('hotelid'),
                'UserId' => 0,
                'IsActive'=>0,
                'CreatedOn'=>now(),
            );
            $result = DB::table('HotelReview')->insert($data);
            if($result){
             //return 'Review added successfully.';
            }


			//send email to hotel

			  $hotel = DB::table('TPHotel')->where('id', $request->get('hotelid'))->first();

            // Customize the email subject and body
			if(!$hotel->isEmpty()){
			return	$subject = 'New Review for ' . $hotel->name;
				$body = 'A new review has been added for ' . $hotel->name . '. Rating: ' . $request->get('rating') . '. Review: ' . $request->get('review');



				$to = 'priyathakur141997@gmail.com';
			   // $to = $request->get('hotel_email');

				try {
					// Attempt to send the email
					Mail::raw($body, function ($message) use ($to, $subject) {
						$message->to($to)
								->subject($subject);
					});

					// Email sent successfully
					return response()->json(['message' => 'Email sent successfully'], 200);
				} catch (\Exception $e) {
					// Email sending failed
					return response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
				}

			}

			//end send email

        }

       // aad reviews end


 // add reviews end

        /*--------------------Restaurant page------------------*/

       public function restaurant($id){


            $rest_id =null;
            $locationID=null;
            $slug ="";



            $parts = explode('-', $id);
            $locationID = $parts[0];
            $rest_id = $parts[1];
            array_shift($parts);
            array_shift($parts);
            $slug = implode('-', $parts);

            $rest =  DB::table('Restaurant as r')
             ->select('r.*','l.Slug as LSlug','l.Name as Lname','c.Name as Cname','l.slugid')
             ->leftjoin('Location as l','l.LocationId','=','r.LocationId')
             ->leftjoin('Country as c','c.CountryId','=','l.CountryId')
             ->where('r.RestaurantId',$rest_id)
             ->where('r.slug',$slug)
             ->where('l.Slugid',$locationID)
             ->get();

             if($rest->isEmpty()){
                  $checkgetloc =  DB::table('Location as lo')
                  ->select('lo.slugid')
                 ->where('lo.LocationId',$locationID)
                 ->get();

                      if(!$checkgetloc->isEmpty()){
                         $id =  $checkgetloc[0]->slugid;

                         return redirect()->route('restaurant_detail', [$id.'-'.$rest_id.'-'.$slug]);
                      }
                 abort('404','url not found');
             }
             if(!$rest->isEmpty()){
                $locationID = $rest[0]->LocationId;
             }


		       $breadcumb  = DB::table('Location as l')
               ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid','l.slugid')
               ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
               ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
               ->where('l.LocationId', $locationID)
               ->get()
               ->toArray();

                $getcuisine = DB::table('RestaurantCuisineAssociation')
              ->join('RestaurantCuisine','RestaurantCuisineAssociation.RestaurantCuisineId','=','RestaurantCuisine.RestaurantCuisineId')->where('RestaurantCuisineAssociation.RestaurantId',$rest_id)->get();

              $getfetures=collect();
              $restreview=collect();
              $getfetures = DB::table('RestaurantFeatureAssociation')
              ->join('RestaurantFeature','RestaurantFeature.RestaurantFeatureId','=','RestaurantFeatureAssociation.RestaurantFeatureId')->where('RestaurantFeatureAssociation.RestaurantId',$rest_id)->get();

              $getspecialdt = DB::table('RestaurantSpecialDietAssociation')
              ->join('RestaurantSpecialDiet','RestaurantSpecialDiet.RestaurantSpecialDietId','=','RestaurantSpecialDietAssociation.RestaurantSpecialDietId')->where('RestaurantSpecialDietAssociation.RestaurantId',$rest_id)->get();

              $near_restaurant = [];
              if (!$rest->isEmpty()) {
                  if (!empty($rest[0]->Longitude)) {
                      $longitude = $rest[0]->Longitude;
                      $latitude = $rest[0]->Latitude;

                      $searchradius = 5;
                      $near_restaurant = DB::table('Restaurant as r')
                            ->leftjoin('Location as l','l.LocationId','=','r.LocationId')
                          ->select('r.Title','r.RestaurantId','r.LocationId','r.Slug','l.slugid')
                          ->selectRaw('(6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(r.Latitude)) * COS(RADIANS(r.Longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(r.Latitude)))) as distance', [$latitude, $longitude, $latitude])
                          ->whereNotNull('r.Longitude') // Filter out restaurants with NULL longitude
                          ->whereNotNull('r.Latitude')  // Filter out restaurants with NULL latitude
                          ->orderBy('distance')
                          ->limit(4)
                          ->get();
                  }
              }
               $restreview =  DB::table('RestaurantReview')->where('RestaurantId',$rest_id    )->limit(8)->get();


                 $gethoteltype = DB::table('TPHotel_types')->orderby('hid','asc')->get();


                //get tplocation
                 $gethotellistiid =collect();
                 $gethotellistiid = DB::table('Temp_Mapping as tm')
                 ->select('tpl.*')
                 ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
                 ->where('tm.Tid',$locationID)
                 ->get();
                 $CountryId ="";

                   if($gethotellistiid->isEmpty()){

                   $lid = DB::table('Location')->where('LocationId',$locationID)->get();

                   if(!$lid->isEmpty()){
                     $CountryId = $lid->CountryId;
                   }
                   $countryLocations = DB::table('Location as l')
                   ->select('l.LocationId')
                   ->where('l.CountryId', $CountryId)
                   ->get();

                     foreach ($countryLocations as $location) {
                         $gethotellistiid = DB::table('Temp_Mapping as tm')
                             ->select('tpl.*')
                             ->join('TPLocations as tpl', 'tpl.locationId', '=', 'tm.LocationId')
                             ->join('Location as l', 'l.locationId', '=', 'tm.Tid')
                             ->where('l.LocationId', $location->LocationId)
                             ->get();

                         // If records are found, break the loop
                         if (!$gethotellistiid->isEmpty()) {
                             // Do something with $gethotellistiid
                             break;
                         }
                     }
                 }

          //end get tplocation
         //get location parent
          $getparent = DB::table('Location')->where('LocationId', $locationID)->get();

          $locationPatent = [];
         if (!$getparent->isEmpty()){
          if ( $getparent[0]->LocationLevel != 1) {
              $loopcount = $getparent[0]->LocationLevel;
              $lociID = $getparent[0]->ParentId;
              for ($i = 1; $i < $loopcount; $i++) {
                  $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                  if (!empty($getparents)) {
                       $locationPatent[] = [
                           'LocationId' => $getparents[0]->slugid,
                           'slug' => $getparents[0]->Slug,
                           'Name' => $getparents[0]->Name,
                       ];
                      if (!empty($getparents) && $getparents[0]->ParentId != "") {
                      $lociID = $getparents[0]->ParentId;
                   }
                  } else {
                      break;
                  }
              }
          }
         }
          //end get location Parent

                return view('restaurant',['rest'=> $rest,'getcuisine'=> $getcuisine,'getfetures'=>$getfetures,'near_restaurant'=>$near_restaurant,'restreview'=>$restreview,'getspecialdiet'=>$getspecialdt,'gethotellistiid'=>$gethotellistiid,'locationPatent'=>$locationPatent,'breadcumb'=>$breadcumb]);
             }

        public function add_rest_review(Request $request) {
            $desc = $request->input('desc');
            $uploadedFiles = $request->file('files');
            $name = $request->input('name');
            $email = $request->input('email');
            $rating = $request->input('rating');
            $restid = $request->input('restid');

            $data1 = [
                'RestaurantId' => $restid,
                'Rating' => $rating,
                'Description'=> $desc,
                'Email' =>$email,
                'Name'  =>$name,
                'CreatedOn'=>now(),
            ];

             $add = DB::table('RestaurantReview')->insertGetId($data1);

         if(!empty($uploadedFiles)){
            foreach ($uploadedFiles as $image) {
                if ($image->isValid()) {
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('rest-img'), $imageName);


                    $data = [
                       'RestaurantReviewId'=>$add,
                        'Image' => $imageName,
                        'created_at'=>now(),
                    ];

                    $getreview = DB::table('Restaurant_review_image')->insert($data);
                }
            }
         }


            $restreview =  DB::table('RestaurantReview')->where('RestaurantId',$restid)->limit(8)->get();

            return view('updated_rest_review',['restreview'=>$restreview]);
        }
  //restaurant
        public function restaurant_listing($id){

            $getrest = DB::table('Restaurant')->where('LocationId',$id)->get();
            $getsight = DB::table('Sight')->where('LocationId',$id)->where('IsMustSee',1)->limit(1)->get();

            $specialdiet = collect(); // Initialize a new Laravel Collection

            if (!$getrest->isEmpty()) {
                foreach ($getrest as $value) {
                    // Fetch special diets and append them to the $specialdiet collection
                    $specialdietsForRestaurant = DB::table('RestaurantSpecialDiet')
                        ->join('RestaurantSpecialDietAssociation', 'RestaurantSpecialDietAssociation.RestaurantSpecialDietId', '=', 'RestaurantSpecialDiet.RestaurantSpecialDietId')
                        ->select('RestaurantSpecialDiet.*')
                        ->where('RestaurantSpecialDietAssociation.RestaurantId', $value->RestaurantId)
                        ->get();

                    $specialdiet = $specialdiet->concat($specialdietsForRestaurant);
                }
            }

            // Now you can convert the Laravel Collection to an array and print it


            $experience = DB::table('Experience')->where('LocationId',$id)->limit(1)->get();
            return view('restaurant_listing',['getrest'=>$getrest,'specialdiet'=>$specialdiet,'getsight'=>$getsight,'experience'=>$experience]);
        }



        public function search_rest(request $request)
        {

            if(request('search')){

                $searchText = request('search');
                $lastSpaceIndex = strrpos($searchText, ' ');

                if($lastSpaceIndex != ""){
                    $locationText = trim(substr($searchText, 0, $lastSpaceIndex));
                     $countryText = trim(substr($searchText, $lastSpaceIndex + 1));
                }


                $query = DB::table('Location AS l')
                    ->select('l.LocationId AS id', DB::raw("CONCAT(l.Name, ', ', c.Name) AS displayName"),DB::raw("CONCAT(l.Slug, '-', c.Name) AS Slug"))
                    ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
                    ->where('l.Slug', 'LIKE', $searchText . '%') // First match against location names
                    ->orWhere('l.Slug', 'LIKE', ','. $searchText. '%')
                    ->limit(5);

                    $locations = $query->get();



                if (empty($locations)) {


                    $query = DB::table('Location AS l')
                    ->select('l.LocationId AS id', DB::raw("CONCAT(l.Name, ', ', c.Name) AS displayName"),DB::raw("CONCAT(l.Slug, '-', c.Name) AS Slug"))
                    ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
                    ->where('l.Name', 'LIKE', '%' . $locationText . '%')
                    ->where('c.Name', 'LIKE', '%' . $countryText . '%')
                    ->limit(5);
                    $locations = $query->get();

                }

                $result = [];
                if(!empty($locations)){
                    foreach ($locations as $loc) {
                        $result[] = ['id'=>$loc->id, 'Slug' => $loc->Slug,'value' => $loc->displayName,];
                        }
                }else{

                     $result[]  = [ 'value' => "Result not founds"];
                }


                return view('restaurant_search_result',['searchresults' => $result]);


             }


        }

        public function recenthistory_restaurant(Request $request){

            // if (Session::has('lastsearch')) {
            //     $serializedData = Session::get('lastsearch');
            //     $search = unserialize($serializedData);
            //     $lastFive = array_slice($search, -5);
            // $result = [];

            //     foreach ($lastFive as $value) {
            //         $result[] = [
            //             'id' => $value['id'],
            //             'Slug' => $value['key'],
            //             'value' => $value['Name'],
            //         ];
            //     }

            // }else{
                $searcht = array(
                    array(
                        'id' => 742277,
                        'key' => 'goa_india-india',
                        'Name' => 'Goa,india'
                    ),
                    array(
                        'id' => 687665,
                        'key' => 'london_ontario-Canada',
                        'Name' => 'London,Canada'
                    ),
                    array(
                        'id' => 822509,
                        'key' => 'Dubai_emirate_of_Dubai-United_Arab_Emirates',
                        'Name' => 'Dubai,United Arab Emirates'
                    )
                );

                $result = array();

                foreach ($searcht as $item) {
                    $result[] = array(
                        'id' => $item['id'],
                        'Slug' => $item['key'],
                        'value' => $item['Name']
                    );
                }


           // }

            return view('restaurant_search_result', ['searchresults' => $result]);

        }

         public function filterrestbycat(request $request){
            $locId = $request->input('locationId');
            $catname = $request->input('catid');


            $getids = DB::table('RestaurantSpecialDiet')
                ->where('Name', $catname)
                ->pluck('RestaurantSpecialDietId');

            if (!empty($getids)) {

                $getrest = DB::table('Restaurant')
                    ->join('RestaurantSpecialDietAssociation', 'RestaurantSpecialDietAssociation.RestaurantId', '=', 'Restaurant.RestaurantId')
                    ->whereIn('RestaurantSpecialDietAssociation.RestaurantSpecialDietId', $getids)
                    ->where('Restaurant.LocationId', $locId)
                    ->select('Restaurant.*')
                    ->get();
            } else {

                $getrest = [];
            }

            if ($catname == "Must See") {
                $getrest = DB::table('Restaurant')
                    ->where('LocationId', $locId)
                    ->where('IsMustSee', 1)
                    ->select('Restaurant.*')
                    ->get() ;
            }


            $experience = DB::table('Experience')->where('LocationId',$locId)->limit(1)->get();

            // return view('filter_restby_cat',['getrest'=>$getrest,'experience'=>$experience]);

              // Extract latitude and longitude into a separate array
        $locationData = [];
        if(!empty($getrest)){
            foreach ($getrest as $restaurant) {
                if (!empty($restaurant->Latitude) && !empty($restaurant->Longitude)) {
                    $locationData[] = [
                        'Latitude' => $restaurant->Latitude,
                        'Longitude' => $restaurant->Longitude,
                        'RestaurantId' => $restaurant->RestaurantId,
                    ];
                }
            }
        }


        // Encode data as JSON
        $locationDataJson = json_encode($locationData);
            return response()->json(['mapData' => $locationDataJson, 'htmlView' => view('filter_restby_cat', ['getrest'=>$getrest,'experience'=>$experience])->render()]);


        }

	/*timing */
      public function editSighttiming(Request $request){

            $selectedDaysIds = $request->input('selectedDays', []);
            $selectedCount = count($selectedDaysIds);
            $uncheckedCount = 7 - $selectedCount;
            $mainhours = $request->input('mainhours');
            // Determine if open 24 hours or closed
            $open24Hours = $request->input('open24Hours') == 1 ? 1 : 0;
            $closed = $request->input('closed') == 1 ? 7 : $uncheckedCount;

            // Get opening and closing times from the request
            $openingTimes = $request->input('openingTimes', []);
            $closingTimes = $request->input('closingTimes', []);

            // Validate array sizes
            if (count($openingTimes) !== count($closingTimes)) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // Mapping array for day IDs to day names
            $dayIdToName = [
                'r1' => 'sun',
                'r2' => 'mon',
                'r3' => 'tue',
                'r4' => 'wed',
                'r5' => 'thu',
                'r6' => 'fri',
                'r7' => 'sat'
            ];

            // Create an array to store the time data
            $timeData = [];

            // Loop through all days and set their timing data
           if($open24Hours == 1){
                $sameTime = [
                    'start' => '00:00',
                    'end' => '23:59',
                ];

                // Set the same time for all selected days
                foreach ($selectedDaysIds as $dayId) {
                    $dayName = $dayIdToName[$dayId];
                    $timeData[$dayName] = $sameTime;
                }

            }else{
    foreach ($dayIdToName as $dayId => $dayName) {
                $index = array_search($dayId, $selectedDaysIds);
                if ($index !== false) {
                    // Day is selected, use provided opening and closing times
                    $timeData[$dayName] = [
                        'start' => $openingTimes[$index],
                        'end' => $closingTimes[$index]
                    ];

                }elseif($open24Hours == 1){

                } else {
                    // Day is not selected, set default start and end times
                    $timeData[$dayName] = [
                        'start' => '00:00',
                        'end' => '00:00'
                    ];
                }
            }

            }

            // Create the final JSON object
            $jsonData = [
                'time' => $timeData,
                'open_closed' => [
                    'open24' => $open24Hours,
                    'closed' => $closed
                ]
            ];

            $jsonString = json_encode($jsonData);

            // Check if the record exists and update or insert accordingly
            $gettiming = DB::table('SightTiming')->where('SightId', $request->input('sightid'))->first();
            if($gettiming === null){
                $data = [
                    'SightId' => $request->input('sightid'),
                    'timings' => $jsonString,
                    'dt_added' => now(),
                    'dt_modify' => now(),
                    'main_hours' =>$mainhours,
                ];
                DB::table('SightTiming')->insert($data);
            } else {
                $data = [
                    'timings' => $jsonString,
                    'dt_modify' => now(),
                    'main_hours' => $mainhours,
                ];
                DB::table('SightTiming')->where('SightId', $request->input('sightid'))->update($data);
            }

            // Fetch the updated timing data
            $gettiming = DB::table('SightTiming')->where('SightId', $request->input('sightid'))->get();

            return view('updated_timing', ['gettiming' => $gettiming]);
        }

	//end timing


	public function add_sight_images(Request $request)
	{
	  $sightid = $request->input('sight_id');

            foreach ($request->file('files') as $key => $file) {
                $title = $request->input('title')[$key];
                $originalName = $file->getClientOriginalName();
              //  $extension = $file->getClientOriginalExtension(); // Get the file extension

                $randomNumber = rand(1000, 9999);
                $filename = time() . '_' . $randomNumber . '_' . $originalName; // Use the original name for display purposes
                $file->move('public/sight-images', $filename);

                $data = [
                    'title' => $title,
                    'Image' => $filename,
                    'Sightid' => $sightid,
                  // Save the extension to the database
                ];

                DB::table('Sight_image')->insert($data);
            }
            $Sight_image = DB::table('Sight_image')
            ->where('Sightid',$sightid)
            ->get();


            return view('sight_image',['Sight_image'=>$Sight_image]);
	}
	public function about_us(){
            return view('about_us');
    }
      public function term_condition(){
            return view('term_condition');
	  }

	public function trust_and_safety(){
		return view('trust_and_safety');
	}
	public function career(){
		return view('career');
	}

	public function accessibility_statement(){
		return view('accessibility_statement');
	}
	public function contact_us(){
        return view('contact_us');
    }
	public function privacy_policy(){
		return view('privacy_policy');
	}

	public function hotel_homepage(){
        return view('hotel_homepage');
     }


      public function list_hotelsloc(Request $request)
     {
         if (!$request->has('search')) {
             return response()->json([]);
         }

         $searchText = trim($request->input('search'));
         $city = $request->input('city');

         if (strlen($searchText) < 2) {
             return response()->json([]);
         }

         $cacheKey = 'hotel_search_' . md5($searchText . ($city ?? ''));
         $cacheDuration = 3600;

         return Cache::remember($cacheKey, $cacheDuration, function() use ($searchText, $city) {
             // Initialize Meilisearch client
             $client = new \Meilisearch\Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

             // Get Meilisearch indexes
             $locationsIndex = $client->index('locations');
             $hotelsIndex = $client->index('hotels');

             $allResults = collect();
             $foundExactLocation = false;

             // First check for exact location match - use direct search instead of filters
             $exactLocationParams = [
                 'limit' => 5,
                 'attributesToRetrieve' => ['id', 'name', 'slugid', 'slug', 'parentName', 'countryName']
             ];

             // Use exact search term with quotes to prioritize exact matches
             $exactLocationResults = $locationsIndex->search('"' . $searchText . '"', $exactLocationParams);
             $locationMatches = collect($exactLocationResults->getHits());

             if (!$locationMatches->isEmpty()) {
                 // Transform location results to match expected format
                 $transformedLocations = $locationMatches->map(function($item) {
                     return (object) [
                         'id' => $item['slugid'] ?? $item['id'],
                         'displayName' => $item['name'] .
                                        ($item['parentName'] ? ', ' . $item['parentName'] : '') .
                                        ($item['countryName'] ? ', ' . $item['countryName'] : ''),
                         'Slug' => $item['slug'],
                         'is_hotel' => 0,
                         'hotelid' => null,
                         'sort_priority' => 2 // Highest priority for exact location matches
                     ];
                 });

                 $allResults = $transformedLocations;
                 $foundExactLocation = true;
             }

             // If no exact location match or if we want to include hotels
             if (!$foundExactLocation) {
                 // Search for partial location matches
                 $partialLocationParams = [
                     'limit' => 5,
                     'attributesToRetrieve' => ['id', 'name', 'slugid', 'slug', 'parentName', 'countryName']
                 ];

                 $partialLocationResults = $locationsIndex->search($searchText, $partialLocationParams);
                 $locationMatches = collect($partialLocationResults->getHits());

                 if (!$locationMatches->isEmpty()) {
                     // Transform location results
                     $transformedLocations = $locationMatches->map(function($item) {
                         return (object) [
                             'id' => $item['slugid'] ?? $item['id'],
                             'displayName' => $item['name'] .
                                            ($item['parentName'] ? ', ' . $item['parentName'] : '') .
                                            ($item['countryName'] ? ', ' . $item['countryName'] : ''),
                             'Slug' => $item['slug'],
                             'is_hotel' => 0,
                             'hotelid' => null,
                             'sort_priority' => 1 // Second priority for partial location matches
                         ];
                     });

                     $allResults = $allResults->merge($transformedLocations);
                 }

                 // Search for hotels
                 $hotelParams = [
                     'limit' => 5,
                     'attributesToRetrieve' => ['id', 'name', 'slugid', 'slug', 'cityName', 'countryName']
                 ];

                 // Add city filter if provided
                 if ($city) {
                     $hotelParams['filter'] = 'cityName LIKE "' . addslashes($city) . '%"';
                 }

                 $hotelResults = $hotelsIndex->search($searchText, $hotelParams);
                 $hotelMatches = collect($hotelResults->getHits());

                 if (!$hotelMatches->isEmpty()) {
                     // Transform hotel results
                     $transformedHotels = $hotelMatches->map(function($item) {
                         return (object) [
                             'id' => $item['slugid'] ?? $item['id'],
                             'displayName' => $item['name'] . ', ' . ($item['cityName'] ?? ''),
                             'Slug' => $item['slug'],
                             'is_hotel' => 1,
                             'hotelid' => $item['id'],
                             'sort_priority' => 0 // Lowest priority for hotels
                         ];
                     });

                     $allResults = $allResults->merge($transformedHotels);
                 }
             }

             // If no matches found with exact search, try progressive search
             if ($allResults->isEmpty()) {
                 $words = preg_split('/\s+/', $searchText);
                 $searchTexts = [];

                 for ($i = count($words); $i > 0; $i--) {
                     $phrase = implode(' ', array_slice($words, 0, $i));
                     if (strlen($phrase) > 2) {
                         $searchTexts[] = $phrase;
                     }
                 }

                 foreach ($searchTexts as $searchTerm) {
                     // Search locations first
                     $locationResults = $locationsIndex->search($searchTerm, [
                         'limit' => 5,
                         'attributesToRetrieve' => ['id', 'name', 'slugid', 'slug', 'parentName', 'countryName']
                     ]);

                     $locationMatches = collect($locationResults->getHits());

                     if (!$locationMatches->isEmpty()) {
                         // Transform location results
                         $transformedLocations = $locationMatches->map(function($item) {
                             return (object) [
                                 'id' => $item['slugid'] ?? $item['id'],
                                 'displayName' => $item['name'] .
                                                ($item['parentName'] ? ', ' . $item['parentName'] : '') .
                                                ($item['countryName'] ? ', ' . $item['countryName'] : ''),
                                 'Slug' => $item['slug'],
                                 'is_hotel' => 0,
                                 'hotelid' => null,
                                 'sort_priority' => 1
                             ];
                         });

                         $allResults = $transformedLocations;
                         continue;
                     }

                     // Search hotels
                     $hotelParams = [
                         'limit' => 5,
                         'attributesToRetrieve' => ['id', 'name', 'slugid', 'slug', 'cityName', 'countryName']
                     ];

                     // Add city to search query if provided instead of using filters
                     $combinedSearchTerm = $searchTerm;
                     if ($city) {
                         // Combine the search term with city for better results
                         $combinedSearchTerm = $searchTerm . ' ' . $city;
                     }

                     $hotelResults = $hotelsIndex->search($combinedSearchTerm, $hotelParams);
                     $hotelMatches = collect($hotelResults->getHits());

                     if (!$hotelMatches->isEmpty()) {
                         // Transform hotel results
                         $transformedHotels = $hotelMatches->map(function($item) {
                             return (object) [
                                 'id' => $item['slugid'] ?? $item['id'],
                                 'displayName' => $item['name'] . ', ' . ($item['cityName'] ?? ''),
                                 'Slug' => $item['slug'],
                                 'is_hotel' => 1,
                                 'hotelid' => $item['id'],
                                 'sort_priority' => 0
                             ];
                         });

                         $allResults = $allResults->merge($transformedHotels);
                     }
                 }
             }

             // Sort results - exact locations first, then partial locations, then hotels
             $sortedResults = $allResults->sortByDesc('sort_priority');

             // Transform results
             $result = $sortedResults->map(function($item) {
                 $base = [
                     'id' => $item->id,
                     'value' => $item->displayName,
                     'Slug' => $item->Slug,
                     'hotel' => $item->is_hotel
                 ];

                 if ($item->is_hotel) {
                     $base['hotelid'] = $item->hotelid;
                 }

                 return $base;
             })->take(5)->all();

             // Update search history
             if (!empty($result)) {
                 try {
                     $searchItem = [
                         'id' => $result[0]['id'],
                         'key' => $result[0]['Slug'],
                         'Name' => $result[0]['value'],
                         'hotel' => $result[0]['hotel'], // Save the hotel type (0 for location, 1 for hotel)
                         'hotelid' => $result[0]['hotelid'] ?? null // Save the hotel ID if it exists
                     ];

                     $searchHistory = [];
                     if (Session::has('lasthotelsearch')) {
                         $searchHistory = unserialize(Session::get('lasthotelsearch'));
                         // Check if this ID already exists in history
                         $existingIndex = array_search($searchItem['id'], array_column($searchHistory, 'id'));

                         if ($existingIndex === false) {
                             // Add new item if it doesn't exist
                             array_push($searchHistory, $searchItem);
                             if (count($searchHistory) > 5) {
                                 array_shift($searchHistory);
                             }
                         } else {
                             // Update existing item to ensure it has the correct hotel flag and hotel ID
                             $searchHistory[$existingIndex] = $searchItem;
                         }
                     } else {
                         $searchHistory = [$searchItem];
                     }

                     Session::put('lasthotelsearch', serialize($searchHistory));
                 } catch (\Exception $e) {
                 }
             }

             return $result;
         });
     }


public function recenthotels(Request $request){
    $result = []; // Initialize result array

    if (Session::has('lasthotelsearch')) {
        $serializedData = Session::get('lasthotelsearch');
        $search = unserialize($serializedData);

        if (!empty($search)) {
            // Get the last 5 items without reversing the order
            $lastFive = array_reverse(array_slice($search, -5));

            foreach ($lastFive as $value) {
                $item = [
                    'id' => $value['id'],
                    'Slug' => $value['key'],
                    'value' => $value['Name'],
                    'hotel' => isset($value['hotel']) ? $value['hotel'] : 0,
                    'hotelid' => isset($value['hotelid']) ? $value['hotelid'] : null
                ];

                $result[] = $item;
            }
        }
    }

    return response()->json($result);
}

	public function insert_data(){

    $timeout = PHP_INT_MAX;

    $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
    $TRAVEL_PAYOUT_MARKER = "299178";

    $batchSize = 1;
    $delaySeconds = 130;
    $delayafterloop = 60;
    $offset = 0;

    $getamenties = "http://engine.hotellook.com/api/v2/static/amenities/en.json?token=".$TRAVEL_PAYOUT_TOKEN;
    $allmnt = Http::withoutVerifying()->timeout($timeout)->get($getamenties);
    $mnt = json_decode($allmnt);
       $rateLimitLimit = $allmnt->header('X-RateLimit-Limit');
        $rateLimitRemaining = $allmnt->header('X-RateLimit-Remaining');
        $rateLimitReset = $allmnt->header('X-RateLimit-Reset');

        // If rate limit headers are present, you can use them to avoid exceeding the limit
        if ($rateLimitRemaining == 0) {
            // You've hit the rate limit, so wait until it resets
            $resetTimestamp = (int)$rateLimitReset;
            $resetSeconds = max($resetTimestamp - time(), 0); // Time in seconds until the limit resets
            sleep($resetSeconds);
        }

    do {
        // Select a batch of records with a limit and offset
        $data = DB::table('TPHotel')
            ->select('location_id')
        //    ->orderBy('location_id', 'desc')
			->where('amenities',null)
			->where('location_id','!=',1677251)
			 ->distinct()
            ->limit($batchSize)
            ->get();
        // Select a batch of records with a limit and offset

        if ($data->isEmpty()) {
            // No more records to process, exit the loop
			dd('empty data');
            break;

        }

    $englishNames = [];


      if(empty($data)){
	  	dd('data is empty');
	  }

     foreach ($data as $cont) {
         $cid = $cont->location_id;

			 $getval = "http://engine.hotellook.com/api/v2/static/hotels.json?locationId=$cid&token=".$TRAVEL_PAYOUT_TOKEN;
			 $gethot = Http::withoutVerifying()->timeout($timeout)->get($getval);
			 $dt2 = json_decode($gethot);

                $rLimit = $gethot->header('X-RateLimit-Limit');
				$rateLRemaining = $gethot->header('X-RateLimit-Remaining');
				$rateLReset = $gethot->header('X-RateLimit-Reset');
			 if ($rateLRemaining == 0) {

				 $resetTimest = (int)$rateLReset;
				 $restSeconds = max($resetTimest - time(), 0); // Time in seconds until the limit resets
				 sleep($restSeconds);
			  }


               if (is_object($dt2) && property_exists($dt2, 'hotels')) {
                   $hotels = $dt2->hotels;
           $j =1;
                   // Insert the fetched hotel data into your database here
			  $b =1;
         foreach ($hotels as $hotel) {

			 $b++;


					      $hid = $hotel->id;
				 $check_amenti = DB::table('TPHotel')
					->select('location_id')
					->where('hotelid',$hid)
					->whereNotNull('amenities')
					->get();

          if ($check_amenti->isEmpty()) {


                 //start mnt
                       $facilities = $hotel->facilities;
                       $amenitiesNames = [];


			           $languageString = [];
                       $roomsaminityString = [];
                       $propertymntString = [];
			  			$languages =[];
						 $roommnt = [];

                        foreach ($facilities as $facilityId) {
                           $matchingAmenity = array_filter($mnt, function ($amenity) use ($facilityId) {
                               return $amenity->id == $facilityId;
                           });



                           if (!empty($matchingAmenity)) {
                               $amenityName = reset($matchingAmenity)->name;
                               $groupName = reset($matchingAmenity)->groupName;

                               $amenitiesNames[] = $amenityName;

							 $roomsaminity[$groupName] = $amenityName;
							$roommnt = json_encode($roomsaminity);

                               if($groupName  == "Staff languages"){
                                $languages [] = $amenityName;
                               }





                           }
                       }
                       $languageString = implode(', ', $languages);
                       $amenitiesString = implode(', ', $amenitiesNames);


                      //end mnt



                    date_default_timezone_set('Asia/Kolkata');
                    //start ct
                    $ctid =$hotel->cityId;





                       $star =$hotel->stars;
                       $pricefrom = $hotel->pricefrom;
                       $rating = $hotel->rating;
                       $name = $hotel->name;
                       $hname =  $name->en;
                       $address = $hotel->address;

                       $hadd = $address->en;
                       $popularity = $hotel->popularity;

                       $propertyType = $hotel->propertyType;
                       $checkIn = $hotel->checkIn;
                       $checkOut = $hotel->checkOut;

                       $distance = $hotel->distance;
                       $photoCount = $hotel->photoCount;

                       $photos = $hotel->photos;
                       $photosJSON = json_encode($photos);
                       $photosByRoomType = $hotel->photosByRoomType;
                       $yearOpened = $hotel->yearOpened;
                       $yearRenovated = $hotel->yearRenovated;
                       $cntRooms = $hotel->cntRooms;
                       $cntSuites = $hotel->cntSuites;


                       $shortFacilities = $hotel->shortFacilities;
                       $loc = $hotel->location;
                       $lon = $loc->lon;
                       $lat = $loc->lat;
                       $link = $hotel->link;


                        $dt = array(

                            'pricefrom' =>$pricefrom,
                            'rating' => $rating,
                            'popularity' => $popularity,
                            'propertyTypeId' =>  $propertyType,
                            'propertyType' => $propertyType,
                            'checkIn' => $checkIn,
                            'checkOut' => $checkOut,
                            'distance' => $distance,
                            'photos' => $photosJSON,
                            'photosByRoomType' => json_encode($photosByRoomType),
                            'photosByRoomTypeNames' => json_encode($photosByRoomType),
                            'yearOpened' =>  $yearOpened,
                            'yearRenovated' => $yearRenovated,
                            'cntRooms' => $cntRooms,
                            'cntSuites' => $cntSuites,
                            'cntFloors' => null,
                            'facilities' => null,
                            'amenities' => $amenitiesString ,
                            'shortFacilities' => json_encode($shortFacilities),
                            'Latitude' => $lon,
                            'longnitude' => $lat,
                            'address' =>  $hadd,
                            'link' => $link,
                            'metaTagTitle' => '',
                            'MetaTagDescription' => '',
                            'poi_distance' => '',
                            'dt_created' => now(),
                            'address_flg' => 1,
                            'IsActive' => 1,
                            'hotelAPi' => null,
                            'Location_Api' => null,
                            'hotelAddFlg' =>null,
                            'hotelslugFlag' => null,
                            'Pincode' =>null,
                            'Phone' => null,
                            'Email' => '',
                            'Website' => '',
                            'NearestStations' => null,
                            'about' => null,
                            'CategoryId' => null,
  							'Languages' => $languageString,
							'room_aminities' => $roommnt,

                        );

                        DB::table('TPHotel')->where('hotelid',$hid)->update($dt);

	 sleep($delayafterloop);
				}

               $j++;
                   }


               } else {
                   echo "No 'hotels' found in the response for name: $name\n";
               }


           }
 sleep($delaySeconds);
           $offset += $batchSize;
        } while (true);

        }



	    public function update_location_geo(){


            $chunkSize = 2;
            $delaySeconds =2;
            $offset =0;

             //start session code
             $maxRequestsPerDay = 5000;
            //  session_start();
            //     $sessions = session()->all();
            //    //	return print_r($sessions);
            //  if (!session()->has('requestsMade')) {
            //     $_SESSION['requestsMade'] = 0;

            //     }

            //     if (!session()->has('lastResetDate')) {
            //     $_SESSION['lastResetDate'] = date('Y-m-d');
            // }
           //session end


            $skipOuterLoop = false;

            while (true) {
                 //session start
                // if (date('Y-m-d') !== $_SESSION['lastResetDate']) {
                //     $_SESSION['requestsMade'] = 0;
                //     $_SESSION['lastResetDate'] = date('Y-m-d');
                // }

                //session end

                    $locations = DB::table('Location as l')
                    ->select('l.LocationId','l.LocationLevel','l.ParentId','l.Name as Place','c.Name as country',DB::raw('(SELECT IFNULL(l1.Name,"") FROM Location as l1 where l1.LocationId=l.ParentId) as Address') )
                    ->join('Country as c','c.CountryId','=','l.CountryId')
                    ->where('l.lat',null)
                    ->offset($offset)
                    ->limit($chunkSize)
                    ->orderby('LocationId','desc')
                    ->get();


    //   return print_r($locations);

                if ($locations->isEmpty()) {

                    break;
                }

                foreach ($locations as $location) {
                    $place = $location->Place;
                    $Address = $location->Address;
                    $country = $location->country;



                    $add = "";
                    if ($place !== "") {
                        $add .= $place;
                    }
                    if (!empty($Address)) {
                        $add .= ',' . $Address;
                    }

                 $add .= ',' . $country;

                 //pk.5afe8ffa47e9ad018968fd02a9c3e0ec
                 //pk.58953ccedd458eeabd120ef183e78efb
                $api = "https://us1.locationiq.com/v1/search?key=pk.5afe8ffa47e9ad018968fd02a9c3e0ec&q=$add&format=json&limit=1";
               //   $api = "https://us1.locationiq.com/v1/autocomplete?q=$add&tag=place%3Acity&key=pk.5afe8ffa47e9ad018968fd02a9c3e0ec&limit=1";
                          //session
                        //        session(['requestsMade' => session('requestsMade', 0) + 1]);
                         //session

                   $getdata = Http::withoutVerifying()->get($api);
                    $decode = json_decode($getdata, true);
                   if (isset($decode['error']) && $decode['error'] == 'Unable to geocode') {
                                        // Set the flag to true and break out of the inner loop
                                        $skipOuterLoop = true;
                                        break;
                  }

                //    print_r($location);
                //      return print_r($decode);

                    sleep($delaySeconds);
                     if (isset($decode['error'])) {
                        // Set the flag to true and break out of the inner loop
                      return $decode['error'];
                    }


                    //session start
                   // if ($locations->isEmpty() || $_SESSION['requestsMade'] >= $maxRequestsPerDay) {
                   //     break;
                   // }
                    //sesstion end


                   if ($skipOuterLoop) {

                    $skipOuterLoop = false;
                    continue;

                }

                    if (is_array($decode)) {
                        foreach ($decode as $value) {

                          $lat = null;
                          $long =  null;

                          if (is_array($value) && isset($value['lat'])) {
                            $lat = $value['lat'];
                           }
                           if (is_array($value) && isset($value['lon'])) {
                            $long = $value['lon'];
                           }

                          $countpt = $location->LocationLevel;
                          $par = $countpt +1;


                        if (is_array($value) && isset($value['display_name'])) {
                              $parts =  explode(',',$value['display_name']);
                         }else{
                             $parts =[];
                        }


                                $countpt = count($parts);
                                $countpt = count($parts);


            //	    echo '-------'. $long;

                   if($lat != null && $long !=null){
                           $Locname ="";
                                $variableName="";
                                $country_name="";
                        //    if ($countpt >= $par) {
                                $Locname = isset($parts[0]) ? $parts[0] : null;
                                    $country_name = isset($parts[$countpt - 1]) ? $parts[$countpt - 1] : null;
                            //       $country_name =  $value['country'];
                                    $parents = [];
                                    for ($i = 1; $i <= $par - 2; $i++) {
                                        $variableName = 'parent' . $i;
                                        $parents[$i - 1] = isset($parts[$i]) ? $parts[$i] : null;
                                    }
                                    $parentsString = implode(', ', $parents);

                                //    return  $country_name .'--'.$Locname.'---'.$parentsString;

                            //    }



                                    //      echo $Locname.'--';
                                    //      echo  $country_name .'--';
                                    //    echo $parentsString;

                                     date_default_timezone_set('Asia/Kolkata');
                                       $parentid = $location->ParentId;

                                       $valuearray = array(
                                        'Lat'=>$lat,
                                        'Longitude'=>$long,
                                        'updated_at'=>now(),
                                       );
                                          $Locname = trim($Locname);
                                          $country_name = trim($country_name);


                                        if(empty($parentid)){

                                            $update = DB::table('Location as l')
                                                ->join('Country as c', 'l.CountryId', '=', 'c.CountryId')
                                                ->where('c.Name', $country_name)
                                                ->where('l.Name', $Locname)
                                                ->update($valuearray);
                                        }else{

                                            $parentsString = trim(str_replace(['ë', 'ç'], ['e', 'c'], $parentsString));



                                            $update = DB::table('Location as l')
                                            ->select('l.LocationId')
                                            ->join('Country as c', 'l.CountryId', '=', 'c.CountryId')
                                            ->Join('Location as pt', 'l.ParentId', '=', 'pt.LocationId')
                                            ->where('c.Name', $country_name)
                                            ->where('l.Name', $Locname)
                                            ->where('pt.Name', 'LIKE', $parentsString);

                                            $getid = $update->get();
                                          //  print_r($getid);
                                            $locId = 0;
                                            if (!$getid->isEmpty()) {
                                                $firstRecord = $getid->first();
                                                $locId = $firstRecord->LocationId;
                                            }

                                             DB::table('Location')->where('LocationId',$locId)->update($valuearray);
                                          // if($locId !=0){
                                          //          return '---'.$locId;
                                         //       }


                                        }

                                }

                        }
                    } else {

                        echo "Error decoding JSON data";
                    }


                }

                sleep($delaySeconds);

                $offset += $chunkSize;
            }
        sleep($delaySeconds);
        }


	  //experience

   public function experince($id){
    $idSegments = explode('-', $id);

    $locationID = 0;
    $sighid ="";
    $location_slug="";
    $sight_slug ="";

    $parts = explode('-', $id);
    if (count($idSegments) > 1) {
           $locationID = $parts[0];
           $exp_id = $idSegments[1];
       $sight_slug = implode('-', array_slice($parts, 2, -1));
       $location_slug =  end($parts);
     $slug = $sight_slug.'-'.$location_slug;
    } else {
        $explocname = $id;
    }
      $getexprv =collect();

    $getexprv = DB::table('ExperienceReview')->where('ExperienceId',$exp_id)->get();

    $getexp = DB::table('Experience')
    ->leftJoin('ExperienceContactDetail','ExperienceContactDetail.ExperienceId','=','Experience.ExperienceId')
    ->select('Experience.*','ExperienceContactDetail.Address','ExperienceContactDetail.Email','ExperienceContactDetail.Phone','c.Name as Cname','l.Slug as LSlug','l.Name as Lname','l.slugid')
    ->Join('Location as l','l.LocationId','=','Experience.LocationId')
    ->leftJoin('Country as c','c.CountryId','=','c.CountryId')
    ->where('Experience.ExperienceId',$exp_id)
    ->where('Experience.Slug',$slug)
    ->where('l.slugid',$locationID)
    ->limit(1)
    ->get();
    //  ->paginate(1);
//return  print_r($getexp);

    if(!$getexp->isEmpty()){
        $locationID =$getexp[0]->LocationId;
    }

    $languageData = collect();
    if(!$getexp->isEmpty()){
  //  foreach ($getexp as $val) {
        $languageData = DB::table('ExperienceLanguageAssociation')
            ->leftJoin('ExperienceLanguage', 'ExperienceLanguage.ExperienceLanguageId', '=', 'ExperienceLanguageAssociation.ExperienceLanguageId')
            ->where('ExperienceLanguageAssociation.ExperienceId', $getexp[0]->ExperienceId)
            ->get();

        // Store language data for each experience in the array

    //}

   }
   $breadcumb  = DB::table('Location as l')
   ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid','l.slugid')
   ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
   ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
   ->where('l.LocationId', $locationID)
   ->get()
   ->toArray();

   $iteneryday = [];

    if(!$getexp->isEmpty()){
        foreach($getexp as $val){
          $iten =  DB::table('ExperienceItninerary as e')
          ->join('Sight as s','s.SightId','=','e.SightId')
          ->join('Location as l','l.LocationId','=','s.LocationId')
          ->select('e.*','l.slugid','s.Slug')
          ->where('e.ExperienceId',$val->ExperienceId)->orderby('e.ItninerarySequence','asc')->get();
            $iteneryday[$val->ExperienceId] = $iten;
        }
    }

  // return print_r( $iteneryday );
    $itenerytime = [];
    if(!$getexp->isEmpty()){
        foreach($getexp as $exp){
           $getiten = DB::table('ExperienceItnineraryStartEnd')->where('ExperienceId',$exp->ExperienceId)->get();
		   $itenerytime[$exp->ExperienceId] = $getiten;
        }

    }

    $reviews = [];
    $nearby_exp =collect();
    $getreview =collect();
    if(!$getexp->isEmpty()){

        foreach($getexp as $exp){
           $getreviews = DB::table('ExperienceItnineraryStartEnd')->where('ExperienceId',$exp->ExperienceId)->get();
		   $reviews[$exp->ExperienceId] = $getreviews;
        }
        $latitude = $getexp[0]->Latitude;
        $longitude = $getexp[0]->Longitude;
        if($latitude !="" &&  $longitude !=""){
            //similar experience
            $searchradius = 50;
            $nearby_exp= DB::table("Experience as exp")
            ->select('ExperienceId', 'Name','Slug','LocationId','slugid','Img1','adult_price',
                        DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(exp.Latitude))
                * cos(radians(exp.Longitude) - radians(" . $longitude . "))
                + sin(radians(" . $latitude . "))
                * sin(radians(exp.Latitude))) AS distance"))
            ->having('distance', '<=', $searchradius)
            ->where('LocationId',$locationID)
            ->whereNotIn('ExperienceId', [$exp_id])
            //   ->orWhere('stars',$star)
            ->orderBy('distance')
            ->limit(5)
            ->get();
            //end similar experience

        }else{
            $nearby_exp= DB::table("Experience as exp")

            ->select('ExperienceId', 'Name','Slug','LocationId','adult_price','slugid','Img1')
            ->where('LocationId',$locationID)
            ->where('ExperienceId','!=', $exp_id)
            ->limit(5)
            ->get();
        }

            $getreview = DB::table('ExperienceReview')->where('ExperienceId',$exp_id)->get();


    }
$getparent = DB::table('Location')->select('LocationLevel','ParentId')->where('LocationId', $locationID)->get();

$locationPatent = [];

if (!$getparent->isEmpty() && $getparent[0]->LocationLevel != 1) {
   $loopcount = $getparent[0]->LocationLevel;
   $lociID = $getparent[0]->ParentId;
   for ($i = 1; $i < $loopcount; $i++) {
       $getparents = DB::table('Location')->select('LocationId','Slug','Name','ParentId','slugid')->where('LocationId', $lociID)->get();
       if (!empty($getparents)) {
            $locationPatent[] = [
                'LocationId' => $getparents[0]->slugid,
                'slug' => $getparents[0]->Slug,
                'Name' => $getparents[0]->Name,
            ];
           if (!$getparents->isEmpty() && $getparents[0]->ParentId != "") {
           $lociID = $getparents[0]->ParentId;
        }
       } else {
           break; // Exit the loop if no more parent locations are found
       }
   }
}




      //get tplocation
      $gethotellistiid =collect();
      $gethotellistiid = DB::table('Temp_Mapping as tm')
      ->select('tpl.*')
      ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
      ->where('tm.Tid',$locationID)
      ->get();
      $CountryId ="";

        if($gethotellistiid->isEmpty()){

        $lid = DB::table('Location')->where('LocationId',$locationID)->get();

        if(!$lid->isEmpty()){
          $CountryId = $lid[0]->CountryId;
        }
        $countryLocations = DB::table('Location as l')
        ->select('l.LocationId')
        ->where('l.CountryId', $CountryId)
        ->get();

          foreach ($countryLocations as $location) {
              $gethotellistiid = DB::table('Temp_Mapping as tm')
                  ->select('tpl.*')
                  ->join('TPLocations as tpl', 'tpl.locationId', '=', 'tm.LocationId')
                  ->join('Location as l', 'l.locationId', '=', 'tm.Tid')
                  ->where('l.LocationId', $location->LocationId)
                  ->get();

              // If records are found, break the loop
              if (!$gethotellistiid->isEmpty()) {
                  // Do something with $gethotellistiid
                  break;
              }
          }
      }



//end get tplocation   Latitude varchar(50)  Longitude



    return view('experience',['getexp'=>$getexp,'languageData' => $languageData,'iteneryday'=>$iteneryday,'itenerytime'=>$itenerytime,'reviews'=>$reviews,'gethotellistiid'=>$gethotellistiid,'nearby_exp'=>$nearby_exp,'locationPatent'=>$locationPatent,'getexprv'=>$getexprv,'breadcumb'=>$breadcumb]);
}
	//weather
	    public function weather(){
            return view('weather');
        }
      //filter hotel list
           public function hotel_all_filters(request $request)
        {

			  session(['filterd' => 1]);
           $locationid = $request->get('locationid');
           $chkin = $request->get('Cin');
           $checout = $request->get('Cout');
           $rooms = $request->get('rooms');
           $guest = $request->get('guest');
           $minPrice = $request->get('priceFrom');
           $priceTo = $request->get('priceTo');
           $typeHotel = $request->get('hoteltype');
           $starRating = $request->get('starRating');
           $mnt = $request->get('mnt');
           $Smnt = $request->get('Smnt');
           $agencydt = $request->get('agency');
           $guestRating = $request->get('guest_rating');
		   $nearby = $request->get('nearby');
           $sort_field = '';
           $sort_direction = '';
           $sort_by = $request->get('sort_by');
           if($sort_by !=""){
                if ($sort_by == 'Recommended') {
                    $sort_field = 'h.rating';
                    $sort_direction = 'desc';
                }
                $sort_direction = trim($sort_direction);
           }



            $stars = [];
            if (is_string($starRating)) {
                $stars = explode(',', $starRating);
            }
            $amenities = [];
            if(is_string($mnt)){
                $amenities = explode(',', $mnt);
            }

            $Specamenities = [];
            if(is_string($Smnt)){
                $Specamenities = explode(',', $Smnt);
            }
            $typeHotels = [];
            if (is_string($typeHotel)) {
                $typeHotels = explode(',', $typeHotel);
            }
            //agency
            $agency =[];
            if(is_string($agencydt)){
                $agency = explode(',', $agencydt);
            }
           //guest
            $guestRatings = [];
			if (is_string($guestRating)) {
   				$guestRatings = explode(',', $guestRating);
			}
          //  $address = $request->get('address');
            $distance = $request->get('distance');
            if($distance ==0){
                $distance = "";
            }

            $searchresults = DB::table('TPHotel as h')
           ->join('hotelbookingstemp as hotemp','hotemp.hotelid','=','h.hotelid')
           ->leftJoin('TPHotel_amenities as a', DB::raw('FIND_IN_SET(a.id, h.facilities)'), '>', DB::raw('0'))
           ->select(
               'h.hotelid', 'h.id', 'h.name', 'h.slug', 'h.stars', 'h.rating','h.Latitude','h.longnitude',
               'h.amenities', 'h.distance', 'h.slugid', 'h.room_aminities', 'h.CityName','h.short_description','h.CountryName',
                DB::raw('GROUP_CONCAT(CONCAT(a.shortName, "|", a.image) ORDER BY a.name SEPARATOR ", ") as amenity_info')
           )
		->distinct()
           ->whereNotNull('h.slugid')
           ->groupBy('h.hotelid');
			//->distinct('h.id');

            if (!empty($stars)) {
                $searchresults->whereIn('h.stars', $stars);
            }
            if (!empty($typeHotels)) {
                $searchresults->whereIn('h.propertyType',$typeHotels);
            }
           if (!empty($amenities)) {
    // Get amenity IDs for the names
    $amenityIds = DB::table('TPHotel_amenities')
        ->whereIn('shortName', $amenities)
        ->pluck('id')
        ->toArray();

    // Filter using the IDs in shortFacilities
    foreach ($amenityIds as $amenityId) {
        $searchresults->whereRaw("FIND_IN_SET(?, h.facilities) > 0", [$amenityId]);
    }
}
            //new code
            if (!empty($agency)) {
                $searchresults->whereIn('hotemp.agency_name', $agency);
            }
           if (!empty($Specamenities)) {
                $searchresults->where(function ($q) use ($Specamenities) {
                    foreach ($Specamenities as $amenity) {
                        $q->where('hotemp.amenity', 'LIKE', '%' . $amenity . '%');
                    }
                });
            }

            if (!empty($guestRatings)) {
                $searchresults->where(function($query) use ($guestRatings) {
                    foreach ($guestRatings as $rating) {
                        // Handle 9 rating specially (9 to 9.5)
                        if ($rating == 9) {
                            $query->orWhereBetween('h.rating', [9, 9.5]);
                        }
                        // Handle 9.5 rating specially (9.5 to 10)
                        elseif ($rating == 9.5) {
                            $query->orWhereBetween('h.rating', [9.5, 10]);
                        }
                        // Handle 10 rating specially (exactly 10)
                        elseif ($rating == 10) {
                            $query->orWhere('h.rating', 10);
                        }
                        // Standard handling for other ratings
                        else {
                            $query->orWhereBetween('h.rating', [$rating, $rating + 1]);
                        }
                    }
                });
            }

            if ($minPrice !="" && $priceTo !="") {
                $searchresults->where('hotemp.price', '>=', $minPrice)
                            ->where('hotemp.price', '<=', $priceTo);
            }

			 if (!empty($nearby)) {
                $nearbyArray = explode(',', $nearby);

                // Get sight details with their coordinates
                $sights = DB::table('Sight')
                    ->select('SightId', 'Latitude', 'Longitude', 'LocationId')
                    ->whereIn('SightId', $nearbyArray)
                    ->get();


                if ($sights->isNotEmpty()) {
                    $searchresults->where(function($query) use ($sights) {
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

                    // Add groupBy to ensure uniqueness
                    $searchresults->groupBy('h.hotelid');
                }
            }


            if (!empty($sort_by)) {

				if($sort_by == 'Top-rated'){
					$searchresults->where('h.rating', '>=', 8.0)
						->orderBy('h.rating', 'desc');
                }elseif ($sort_by == 'Price: High to Low') {

                    $searchresults->orderBy(DB::raw('(SELECT MIN(hotemp.price) FROM hotelbookingstemp hotemp WHERE hotemp.hotelid = h.hotelid)'), 'desc');
                }elseif ($sort_by == 'Price: Low to High') {

                    $searchresults->orderBy(DB::raw('(SELECT MIN(hotemp.price) FROM hotelbookingstemp hotemp WHERE hotemp.hotelid = h.hotelid)'), 'asc');
                }else{
                    $searchresults->orderBy($sort_field, $sort_direction);
                }


            }else{
			 $searchresults->orderBy('h.stars', 'desc');
			}
      		   //   $searchresults = $searchresults->distinct('h.id')

            $searchresults = $searchresults->groupBy('h.id', 'hotemp.price', 'h.hotelid', 'h.name', 'h.slug', 'h.stars', 'h.rating', 'h.amenities', 'h.distance', 'h.slugid', 'h.room_aminities', 'h.CityName');   //groupBy('h.id');
				//	  $searchresults = $searchresults->distinct('h.hotelid');

            $hotelIds = $searchresults->pluck('h.hotelid')->unique();
            $uniqueHotelsCount = $hotelIds->count();

     	    $searchresults = $searchresults->groupBy('h.hotelid')
       								  ->paginate(30)->withQueryString();

			$sthotelIds = $searchresults->pluck('hotelid')->unique()->values()->toArray();

			$hotelpricedata = DB::table('hotelbookingstemp')
 			   ->whereIn('hotelid', $sthotelIds)
  			  ->get();

           $result = view('all_hotel_filter')
               ->with('searchresults',$searchresults)
               ->with('hotels',$hotelpricedata)
			    ->with('chkin',$chkin)
                ->with('checout',$checout)
			    ->with('resultcount',$uniqueHotelsCount)
			   ->render();
           return response()->json(['result'=>$result,'resultcount'=>$uniqueHotelsCount]);
        }

//neighbourhood
	    public function hotel_neighbourhood(request $request){
       $getval = $request->get('checkin') .'_'.$request->get('checkout');
        $chkin = $request->get('checkin');
        $checout = $request->get('checkout');

         $rooms = $request->get('rooms');
         $guest = $request->get('guest');
         $slug = $request->get('slug');
         $locationid =  $request->get('locationid');
	     $neighbourhood="";
         $neighbourhood =  $request->get('neighborhood');

         if( $locationid == ""){
            $locationid =  $request->get('lid');
         }else{
            $locationid = $locationid;
         }

          $fullname = $request->get('locname');
          $countryname ="";
       //   $lname ="";
       $Neighborhood =[];
       $Neighborhood = DB::table('Neighborhood as n')
       ->select('n.NeighborhoodId','n.LocationID','n.Name','Hn.hotelid')
       ->Leftjoin('TPHotelNeighbourhood as Hn','n.NeighborhoodId','=','Hn.NeighborhoodID')
       ->where('n.NeighborhoodId',$neighbourhood)->where('n.LocationID',$locationid)->get()->toArray();
         if(empty($Neighborhood)){
            abort('404','Neibourhood not found');
         }
       $getloc = DB::table('Temp_Mapping as tm')
         ->join('Location as l','l.LocationId','=','tm.Tid')
         ->select('tm.locationId')
         ->where('tm.Tid',$locationid)
         ->get();

         if(!$getloc->isEmpty()){
            $locationid = $getloc[0]->locationId;
         }

       if(!empty($Neighborhood)){
           if($fullname ==""){
          // $fullname = $Neighborhood[0]->Name;
           }
           $neibhood_name = $Neighborhood[0]->Name;

       }
            $getloc = DB::table('TPLocations')->select('fullName','cityName','countryName')->where('id',$locationid)->get();

            if(!$getloc->isEmpty()){
                if($fullname ==""){
                    $fullname = $getloc[0]->fullName;
            }
                $lname2 = $getloc[0]->cityName;
                $countryName = $getloc[0]->countryName;
            }
            $getloclink =collect();

            $getloclink = DB::table('Temp_Mapping as tm')
            ->join('Location as l','l.LocationId','=','tm.Tid')
            ->select('l.*')
            ->where('tm.LocationId',$locationid)
            ->get();

            $locationPatent = [];
            if(!$getloclink->isEmpty()){


                if (!$getloclink->isEmpty() &&  $getloclink[0]->LocationLevel != 1) {
                    $loopcount =  $getloclink[0]->LocationLevel;

                    $lociID = $getloclink[0]->ParentId;
                    for ($i = 1; $i < $loopcount; $i++) {
                        $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                        if (!empty($getparents)) {
                            $locationPatent[] = [
                                'LocationId' => $getparents[0]->LocationId,
                                'slug' => $getparents[0]->Slug,
                                'Name' => $getparents[0]->Name,
                            ];
                            if (!empty($getparents) && $getparents[0]->ParentId != "") {
                            $lociID = $getparents[0]->ParentId;
                        }
                        } else {
                            break; // Exit the loop if no more parent locations are found
                        }
                    }
                }
            }

            $hotelpage ="hotelpage";
            $gethoteltype = DB::table('TPHotel_types')->orderby('hid','asc')->get();
            return view('hotel_Neighbourhood',['locid'=>$locationid,'gethoteltype'=>$gethoteltype,'fullname'=>$fullname,'lname2'=>$lname2,'countryname'=>$countryName,'getloclink'=>$getloclink,'locationPatent'=>$locationPatent,'neibhood_name'=>$neibhood_name,'locationid'=>$locationid,'hotelpage'=>$hotelpage]);


     }
    public function fetch_neighb_listing_with_api(request $request){
        $searchresults =collect();
        $getval = $request->get('checkin') .'_'.$request->get('checkout');

        $chkin = $request->get('checkin');
        $checout = $request->get('checkout');
        $rooms = $request->get('rooms');
        $guest = $request->get('guest');
        $slug = $request->get('slug');
        $fullname = $request->get('locname');
        $id =   $request->get('neighborhood');
        $locationid =  $request->get('locationid');

        $Neighborhood = [];

        $Neighborhood = DB::table('TPHotelNeighbourhood')
        ->select('hotelid')
        ->where('NeighborhoodID',$id)->get()->toArray();


        $getloc = DB::table('TPLocations')->select('fullName','cityName','countryName')->where('id',$locationid)->get();
        $cityName ="";
        $countryName ="";
        if(!$getloc->isEmpty()){
            $countryName = $getloc[0]->countryName;
            $cityName =$getloc[0]->cityName;
        }

         session(['checkin' => $getval]);
         session(['rooms' => $rooms]);
         session(['guest' => $guest]);

         //new code start
         $checkinDate =  $chkin;
         $checkoutDate = $checout;
         $adultsCount = $guest;
         $customerIP = '49.156.89.145';
         $childrenCount = '1';
         $chid_age = '10';
         $lang = 'en';
         $currency ='USD';
         $waitForResult ='0';
         $iata= $locationid ;//24072

         $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
         $TRAVEL_PAYOUT_MARKER = "299178";



        $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
         $checkinDate.":".
         $checkoutDate.":".
         $chid_age.":".
         $childrenCount.":".
         $iata.":".
         $currency.":".
         $customerIP.":".
         $lang.":".
         $waitForResult;


       $signature = md5($SignatureString);

      $url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;

           $response = Http::withoutVerifying()->get($url);

         if ($response->successful()) {


           $data = json_decode($response);
             if(!empty($data)){
               $searchId = $data->searchId;


             $limit =0;
             $offset=0;
             $roomsCount=0;
             $sortAsc=1;
             $sortBy='price';

               $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
                  $sig2 =  md5($SignatureString2);

                  $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=0&sortBy=price&sortAsc=1&roomsCount=0&offset=0&marker=299178&signature='.$sig2;

                         $gethoteltype =collect();
				     $response2 = Http::withoutVerifying()->timeout(30)->get($url2);
                //    $response2 = Http::timeout(30)->retry(3, 100)->get($url2);


				 $responseData = $response2->json();
				 	if ($responseData['status'] === 'error' && $responseData['errorCode'] === 4) {
						$status = 4;
						return 'Search is not finished.';
						//  $response2 = Http::withoutVerifying()->get($url2);

					}else{
						$status = 1;
					}

				 $maxRetries = 10;
                     if ($response2->successful()) {

                         $hotel = json_decode($response2);


                        $idArray = array();

                        // Iterate through the $hotel array
                        foreach ($hotel->result as $hotelInfo) {
                            // Check if the 'id' key exists in the inner array
                            if (isset($hotelInfo->id)) {
                                // Add the 'id' value to the $idArray
                                $idArray[] = $hotelInfo->id;
                            }
                        }
                        //return  print_r($idArray);
                        $neighborhoodHotelIds = array_map(function ($item) {
                            return $item->hotelid;
                        }, $Neighborhood);

                        // Find common hotel IDs
                        $commonHotelIds = array_intersect($neighborhoodHotelIds, $idArray);


                         // end download logo
                            $searchresults = DB::table('TPHotel as h')
                         ->select('h.hotelid','h.id', 'h.name', 'h.address', 'h.slug', 'h.cityId', 'h.iata', 'h.location_id as loc_id','h.stars', 'h.pricefrom', 'h.rating', 'h.popularity', 'h.amenities', 'h.distance', 'h.propertyType')

                         ->whereIn('h.hotelid',$commonHotelIds)->paginate(5)  ;

                        $searchresults->appends($request->except('_token'));
                         $searchresults->setPath('hotel_neighborhood.html');

                         return view('frontend.hotel.get_hotel_landing_result',['hotels'=> $hotel,'locid'=>$locationid,'searchresults'=>$searchresults,'fullname'=>$fullname,'cityName'=>$cityName,'countryname'=>$countryName]);
                     }


             }else{
                 return 'search id not found';
             }

         } else {

             return 2;
         }
     }
     public function hotel_neiborhood_listing($id,$nid,$slug)
     {
        $gethoteltype = [];
         $searchresults=[];
         $neib = DB::table('Neighborhood')->select('Name')->where('NeighborhoodID', $nid)->where('slug',$slug)->where('LocationID',$id)->get();
         if($neib->isEmpty()){
            abort('404','url not found');
         }
         $lname = "";
          $breadcumb  = DB::table('Location as l')
             ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid')
             ->leftJoin('Country as co', 'l.CountryId', '=', 'co.CountryId')
             ->leftJoin('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
             ->where('l.LocationId', $id)
             ->get()
             ->toArray();
         if(!$neib->isEmpty()){
          $neibhood_name=$neib[0]->Name;
         }
         $neib_ids = DB::table('TPHotelNeighbourhood')->where('NeighborhoodID', $nid)->pluck('hotelid')->toArray();
             $searchresults = DB::table('TPHotel as tph')
             ->join('Temp_Mapping as t','t.LocationId','=','tph.location_id')
             ->select('tph.hotelid', 'tph.id', 'tph.name', 'tph.address', 'tph.slug', 'tph.cityId', 'tph.iata', 'tph.location_id as loc_id', 'tph.stars', 'tph.pricefrom', 'tph.rating', 'tph.popularity', 'tph.amenities', 'tph.distance', 'tph.propertyType','t.slugid')

             ->whereIn('hotelid', $neib_ids)
             ->paginate(10);

             $locmap = DB::table('Temp_Mapping')
             ->where('Tid',$id)
             ->get();
             if(!$locmap->isEmpty()){
               $id = $locmap[0]->LocationId;
                $loc = DB::table('TPLocations')
                ->where('id',$id)
                ->get();
                if(!$loc->isEmpty()){
                    $lname2 =$loc[0]->cityName;
                    $countryname = $loc[0]->countryName;
                }
             }


     //   $end  =  date("H:i:s");

      //  echo  $start .'---'.$st2 .'---'. $end ;

        $gethoteltype = DB::table('TPHotel_types')->orderby('hid','asc')->get();


        $getloclink =collect();

        $getloclink = DB::table('Temp_Mapping as tm')
        ->join('Location as l','l.LocationId','=','tm.Tid')
        ->select('l.*')
        ->where('l.LocationId',$id)
        ->get();

	    $locationPatent = [];
        if(!$getloclink->isEmpty()){


            if (!$getloclink->isEmpty() &&  $getloclink[0]->LocationLevel != 1) {
                $loopcount =  $getloclink[0]->LocationLevel;

                $lociID = $getloclink[0]->ParentId;
                for ($i = 1; $i < $loopcount; $i++) {
                    $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                    if (!empty($getparents)) {
                        $locationPatent[] = [
                            'LocationId' => $getparents[0]->slugid,
                            'slug' => $getparents[0]->Slug,
                            'Name' => $getparents[0]->Name,
                        ];
                        if (!empty($getparents) && $getparents[0]->ParentId != "") {
                        $lociID = $getparents[0]->ParentId;
                    }
                    } else {
                        break; // Exit the loop if no more parent locations are found
                    }
                }
            }
        }
        $hotelpage ='hotelpage';
         return view('hotel_neiborhood_listing')->with('searchresults',$searchresults)->with('gethoteltype',$gethoteltype)->with('neibhood_name',$neibhood_name)->with('countryname',$countryname)->with('getloclink',$getloclink)->with('locationPatent',$locationPatent)->with('lname2',$lname2)->with('locationid',$id)->with('hotelpage',$hotelpage)->with('breadcumb',$breadcumb);
     }
  public function hotel_landing($lid,$landid,$slug)
     {

        $id = str_replace('ld',' ',$landid);
        $locid =$lid;
        $gethoteltype = [];
        $searchresults=[];

        $getloc = DB::table('Temp_Mapping as m')
        ->select('m.LocationId')
        ->where('m.slugid',$lid)
        ->get();
       // return print_r($getloc);
        if(!$getloc->isEmpty()){
            $lid = $getloc[0]->LocationId;
        }

        $getlanding = DB::table('TPHotel_landing')
        ->select('Amenities','Rating','location_id')
        ->where('id', $id)
        ->where('slug', $slug)
        ->where('location_id',$lid)
        ->get();

		if($getlanding->isEmpty()){
            abort('404','url not found');
         }
        $amenities='';
        $Rating=[];
        $locationid = "";
        if(!$getlanding->isEmpty()){
            $locationid = $getlanding[0]->location_id;
            if($getlanding[0]->Amenities !=""){

                // if(is_string($getlanding[0]->Amenities)){
                //     $amenities = explode(',', $getlanding[0]->Amenities);
                // }

                if(!empty($getlanding[0]->Amenities)){
                    $amenities = json_decode($getlanding[0]->Amenities);
                }
            }
            $Rating=[];
            if($getlanding[0]->Rating !=""){

                 $rating[] = $getlanding[0]->Rating;
                if(!empty($rating)){
                    $Rating[] = json_decode($getlanding[0]->Rating);
                }


                // if (strpos($rating, ',') !== false) {
                //     $explodedRating = explode(',', $rating);
                //     $Rating = array();

                //     foreach ($explodedRating as $value) {
                //         $Rating[] = trim($value);
                //     }


                // } else {
                //     $Rating[] = $getlanding[0]->Rating;
                // }
           }
         //  return print_r( $Rating);

        }
       $start =  date("H:i:s");


     $searchresults = DB::table('TPHotel as tph')
     //->join('Temp_Mapping as m','m.LocationId','=','tph.location_id')
    ->select('tph.hotelid', 'tph.id', 'tph.name', 'tph.slug','tph.location_id as loc_id', 'tph.stars', 'tph.pricefrom', 'tph.rating', 'tph.amenities','tph.distance','tph.room_aminities','tph.Languages','tph.slugid')
    ->where('tph.location_id', $locationid);
	if (!empty($amenities) || !empty($Rating)) {
		$searchresults->where(function ($query) use ($amenities, $Rating) {

			if (!empty($amenities)) {
				foreach ($amenities as $amenity) {
					$query->orWhere('tph.amenities', 'LIKE', '%' . $amenity . '%');
				}
			}
            if (!empty($amenities)) {
				foreach ($amenities as $amenity) {
					$query->orWhere('tph.room_aminities', 'LIKE', '%' . $amenity . '%');
				}
			}
            if (!empty($amenities)) {
				foreach ($amenities as $amenity) {
					$query->orWhere('tph.Languages', 'LIKE', '%' . $amenity . '%');
				}
			}
			if (!empty($Rating)) {
				$query->orWhereIn('tph.stars',$Rating);
			}
		});
	}


	$searchresults = $searchresults->limit(7)->get();

	     $st2 =  date("H:i:s");




        $countryname ="";
        $lname2="";
        $lname ="";


        $getloc = DB::table('TPLocations')->select('cityName','countryName')->where('id',$locationid)->get();
            if(!$getloc->isEmpty()){
            $lname2 =$getloc[0]->cityName;
            $countryname = $getloc[0]->countryName;
            $lname =$getloc[0]->cityName;
            }
  $end  =  date("H:i:s");
            $neibhood_name =str_replace('-',' ',$slug);




        $gethoteltype = DB::table('TPHotel_types')->orderby('hid','asc')->get();


        $getloclink =collect();

        $getloclink = DB::table('Temp_Mapping as tm')
        ->join('Location as l','l.LocationId','=','tm.Tid')
        ->select('l.*')
        ->where('tm.LocationId',$locid)
        ->get();
	  $breadcumb=[];
        if(!$getloclink->isEmpty()){
        $locationid = $getloclink[0]->LocationId;


            $breadcumb  = DB::table('Location as l')
            ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid','l.slugid')
            ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
            ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
            ->where('l.LocationId', $locationid)
            ->get()
            ->toArray();
        }
//   return print_r($getloclink);
	    $getlocationexp = collect();
        $locationPatent = [];
        if(!$getloclink->isEmpty()){
            $locationid = $getloclink[0]->LocationId;
            $getlocationexp = DB::table('Location')->select('LocationId','Name','Slug','slugid')->where('LocationId', $locationid)->get();

            if (!$getloclink->isEmpty() &&  $getloclink[0]->LocationLevel != 1) {

                $loopcount =  $getloclink[0]->LocationLevel;

                $lociID = $getloclink[0]->ParentId;
                for ($i = 1; $i < $loopcount; $i++) {
                    $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                    if (!empty($getparents)) {
                        $locationPatent[] = [
                            'LocationId' => $getparents[0]->slugid,
                            'slug' => $getparents[0]->Slug,
                            'Name' => $getparents[0]->Name,
                        ];
                        if (!empty($getparents) && $getparents[0]->ParentId != "") {
                        $lociID = $getparents[0]->ParentId;
                    }
                    } else {
                        break; // Exit the loop if no more parent locations are found
                    }
                }
            }
        }
	$hotelpage ='hotelpage';
         return view('frontend.hotel.hotel_landing')->with('searchresults',$searchresults)->with('gethoteltype',$gethoteltype)->with('sname',$neibhood_name)->with('countryname',$countryname)->with('getloclink',$getloclink)->with('locationPatent',$locationPatent)->with('lname2',$lname2)->with('locationid',$id)->with('locid',$locid)->with('lname',$lname)->with('amenities',$amenities)->with('Rating',$Rating)->with('getloc',$getloc)->with('hotelpage',$hotelpage)->with('getlocationexp',$getlocationexp)->with('breadcumb',$breadcumb);
     }
   //hotel landing with date
  //hotel landing with date
   public function hotel_landing_with_date(request $request){
      $start  =  date("H:i:s");
         $requiredParameters = ['checkin', 'checkout','guest','id'];
        foreach ($requiredParameters as $param) {
            if (!$request->filled($param)) {
                abort(404, "The '$param' parameter is required.");
            }
        }
        if (!$request->filled('locationid') && !$request->filled('lid')) {
            abort(404, 'Either locationid or lid is required.');
        }

          $getval = $request->get('checkin') .'_'.$request->get('checkout');

          $chkin = $request->get('checkin');
          $checout = $request->get('checkout');
          $rooms = $request->get('rooms');
          $guest = $request->get('guest');
          $slug = $request->get('slug');
          $landid =  $request->get('id');
          $id = str_replace('ld',' ',$landid);
          $locationid =  $request->get('locationid');

          $fullname = $request->get('locname');
          $countryname ="";
          $lname ="";
          $lname2 ="";
          $neibhood_name =str_replace('-',' ',$slug);

          $getloc = DB::table('TPLocations')->select('fullName','cityName','countryName')->where('id',$locationid)->get();
            if($getloc->isEmpty()){
                abort(404,'Not FOUND');
            }
          if(!$getloc->isEmpty()){
              if($fullname ==""){
                  $fullname = $getloc[0]->fullName;
          }
              $lname2 = $getloc[0]->cityName;
              $lname = $getloc[0]->cityName;
              $countryName = $getloc[0]->countryName;

          }

          $gethoteltype = DB::table('TPHotel_types')->orderby('hid','asc')->get();
          $searchresults =collect();
	     $hotelpage = 'hotelpage';
	       $getloclink =collect();

          $getloclink = DB::table('Temp_Mapping as tm')
          ->join('Location as l','l.LocationId','=','tm.Tid')
          ->select('l.*')
          ->where('tm.LocationId',$locationid)
          ->get();

	      $breadcumb =[];
          if(!$getloclink->isEmpty()){
            $locationid = $getloclink[0]->LocationId;
            $breadcumb  = DB::table('Location as l')
            ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid')
            ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
            ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
            ->where('l.LocationId', $locationid)
            ->get()
            ->toArray();
        }
  //   return print_r($getloclink);
          $getlocationexp = collect();
          $locationPatent = [];
          if(!$getloclink->isEmpty()){
              $locationid = $getloclink[0]->LocationId;
              $getlocationexp = DB::table('Location')->select('LocationId','Name','Slug')->where('LocationId', $locationid)->get();

              if (!$getloclink->isEmpty() &&  $getloclink[0]->LocationLevel != 1) {

                  $loopcount =  $getloclink[0]->LocationLevel;

                  $lociID = $getloclink[0]->ParentId;
                  for ($i = 1; $i < $loopcount; $i++) {
                      $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                      if (!empty($getparents)) {
                          $locationPatent[] = [
                              'LocationId' => $getparents[0]->LocationId,
                              'slug' => $getparents[0]->Slug,
                              'Name' => $getparents[0]->Name,
                          ];
                          if (!empty($getparents) && $getparents[0]->ParentId != "") {
                          $lociID = $getparents[0]->ParentId;
                      }
                      } else {
                          break; // Exit the loop if no more parent locations are found
                      }
                  }
              }
          }

          return view('frontend.hotel.hotel_landing_with_date',['searchresults'=>$searchresults,'locid'=>$locationid,'fullname'=>$fullname,'lname2'=>$lname2,'countryname'=>$countryName,'neibhood_name'=>$neibhood_name,'locationid'=>$locationid,'lname'=>$lname,'gethoteltype'=>$gethoteltype,'hotelpage'=>$hotelpage,'getlocationexp'=>$getlocationexp,'locationPatent'=>$locationPatent,'breadcumb'=>$breadcumb]);

       }

       public function get_hotel_landing_result(request $request){

        $start  =  date("H:i:s");
        $searchresults =collect();
        $getval = $request->get('checkin') .'_'.$request->get('checkout');
        $chkin = $request->get('checkin');
        $checout = $request->get('checkout');

        $rooms = $request->get('rooms');
        $guest = $request->get('guest');
        $slug = $request->get('slug');
        $landid =  $request->get('id');
         $id = str_replace('ld',' ',$landid);
        $locationid =  $request->get('locationid');
        //new code
        $md  =  date("H:i:s");
        $getlanding = DB::table('TPHotel_landing')->select('Amenities','Rating','location_id')->where('id', $id)->where('slug', $slug)->get();


        $amenities=[];
        $Rating=[];
        $locationid = "";
        if(!$getlanding->isEmpty()){
            $locationid = $getlanding[0]->location_id;

           $amenities="";
			$Rating="";
			$locationid = "";
			if(!$getlanding->isEmpty()){
				$locationid = $getlanding[0]->location_id;
				if($getlanding[0]->Amenities !=""){

					// if(is_string($getlanding[0]->Amenities)){
					//     $amenities = explode(',', $getlanding[0]->Amenities);
					// }

					if(!empty($getlanding[0]->Amenities)){
						$amenities = json_decode($getlanding[0]->Amenities);
					}
				}
				$Rating='';
				if($getlanding[0]->Rating !=""){

					 $rating = $getlanding[0]->Rating;
					if(!empty($rating)){
						$Rating = json_decode($getlanding[0]->Rating);
					}
				}
        }
        $end  =  date("H:i:s");

       //end new code


         session(['checkin' => $getval]);
         session(['rooms' => $rooms]);
         session(['guest' => $guest]);

         //new code start
         $checkinDate =  $chkin;
         $checkoutDate = $checout;
         $adultsCount = $guest;
         $customerIP = '49.156.89.145';
         $childrenCount = '1';
         $chid_age = '10';
         $lang = 'en';
         $currency ='USD';
         $waitForResult ='0';
         $iata= $locationid ;//24072

         $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
         $TRAVEL_PAYOUT_MARKER = "299178";

        $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
         $checkinDate.":".
         $checkoutDate.":".
         $chid_age.":".
         $childrenCount.":".
         $iata.":".
         $currency.":".
         $customerIP.":".
         $lang.":".
         $waitForResult;

          $signature = md5($SignatureString);
          $url ='http://engine.hotellook.com/api/v2/search/start.json?cityId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;

             $response = Http::withoutVerifying()->get($url);

         if ($response->successful()) {
             $data = json_decode($response);
             if(!empty($data)){
               $searchId = $data->searchId;
             $limit =0;
             $offset=0;
             $roomsCount=0;
             $sortAsc=1;
             $sortBy='price';

            $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
             $sig2 =  md5($SignatureString2);

            $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=0&sortBy=price&sortAsc=1&roomsCount=0&offset=0&marker=299178&signature='.$sig2;
            $gethoteltype =collect();
            $response2 = Http::withoutVerifying()->timeout(30)->get($url2);
            $responseData = $response2->json();
                if ($responseData['status'] === 'error' && $responseData['errorCode'] === 4) {
                    $status = 4;
                    return 'Search is not finished.';
                }else{
                    $status = 1;
                }
                 $maxRetries = 10;
                     if ($response2->successful()) {
                        $hotel = json_decode($response2);
                        $idArray = array();
                        foreach ($hotel->result as $hotelInfo) {
                            if (isset($hotelInfo->id)) {
                                $idArray[] = $hotelInfo->id;
                            }
                        }
                        $st2  =  date("H:i:s");


                               $searchresults = DB::table('TPHotel as tph')
                          ->select('tph.hotelid', 'tph.id', 'tph.name', 'tph.slug','tph.location_id as loc_id', 'tph.stars', 'tph.pricefrom', 'tph.rating', 'tph.amenities','tph.distance','tph.room_aminities','tph.Languages')
                            ->whereIn('tph.hotelid',$idArray) ;
                          if (!empty($amenities) || !empty($Rating)) {
                              $searchresults->where(function ($query) use ($amenities, $Rating) {

                                  if (!empty($amenities)) {
                                      foreach ($amenities as $amenity) {
                                          $query->orWhere('tph.amenities', 'LIKE', '%' . $amenity . '%');
                                      }
                                  }
                                  if (!empty($amenities)) {
                                      foreach ($amenities as $amenity) {
                                          $query->orWhere('tph.room_aminities', 'LIKE', '%' . $amenity . '%');
                                      }
                                  }
                                  if (!empty($amenities)) {
                                      foreach ($amenities as $amenity) {
                                          $query->orWhere('tph.Languages', 'LIKE', '%' . $amenity . '%');
                                      }
                                  }
                              if (!empty($Rating)) {
                                  $query->orWhereIn('tph.stars', $Rating);
                              }
                          });
                      }

                      $searchresults = $searchresults->paginate(5);

                      // Manually append existing query parameters to pagination links
                      $searchresults->appends($request->except('_token'));


                    $searchresults->setPath('hotel_landing.html');


                    $getloc = DB::table('TPLocations')->select('cityName','countryName')->where('id',$locationid)->limit(1)->get();
						 $cityName ="";
					     $countryname ="";
							if(!$getloc->isEmpty()){
								$lname2 =$getloc[0]->cityName;
								$countryname = $getloc[0]->countryName;
								$cityName =$getloc[0]->cityName;
							}

                      // end save id if not found in TPhotel table


              return view('frontend.hotel.get_hotel_landing_result_withdate', ['hotels' => $hotel, 'locid' => $locationid, 'searchresults' => $searchresults, 'amenities' => $amenities, 'Rating' => $Rating, 'cityName' => $cityName, 'countryname' => $countryname])->render();


                     }


             }else{
                 return 'search id not found';
             }

         } else {

             return 2;
         }

       }
	   }
     public function saveNearbyhotel_hotellist(request $request){

        $locationJson = $request->get('Latitude');

        // Decode JSON string
        $locationData = json_decode($locationJson, true);

        // Extract latitude and longitude values
        $latitude = $locationData['lat'];
        $longitude = $locationData['lon'];


        $locationid = $request->get('locationid');


        $nbs =0;
        $nbh = 0;
        $ns =0;

           if($latitude != "" && $longitude !=""){
            $get_nearby_hotel = DB::table('TPhotel_listing_NBhotel')->where('LocationId',$locationid)->get();


        if (!$get_nearby_hotel->count() >= 5) {
            $nbh = 1;
             $searchradius = 10;
            $nearby_hotel = DB::table("TPHotel")
            ->select('id', 'name','location_id','slug','address','pricefrom','stars',
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(TPHotel.Latitude))
                * cos(radians(TPHotel.longnitude) - radians(" . $longitude . "))
                + sin(radians(" . $latitude . "))
                * sin(radians(TPHotel.Latitude))) AS distance"))
            ->having('distance', '<=', $searchradius)
            ->where('location_id',$locationid)

            ->orderBy('distance')
            ->limit(5)

            ->get();
       //   return print_r($nearby_hotel);
         if(!$nearby_hotel->isEmpty()){




            foreach ($nearby_hotel as $nearby_hotels) {
                $slug = $nearby_hotels->slug;
                $Title = $nearby_hotels->name;
                $LocationId = $nearby_hotels->location_id;
                $distance = round($nearby_hotels->distance,2);
                $address = $nearby_hotels->address;
                $stars = $nearby_hotels->stars;
                $pricefrom = $nearby_hotels->pricefrom;
                $id = $nearby_hotels->id;

                $data3= array(
                    'name'=>$Title,
                    'slug'=>$slug,
                    'hotelid'=>$id,
                    'LocationId'=>$LocationId,
                    'distance'=>$distance,
                    'radius'=>$searchradius,
                    'address'=>$address,
                    'stars'=>$stars,
                    'pricefrom'=>$pricefrom,

                    'dated'=>now(),
                );


                $insertdata3 = DB::table('TPhotel_listing_NBhotel')->insert($data3);
            //   return print_r($data2);
            }


         }
      }


        }
        if($nbh = 1){

           $nearby_hotel =  DB::table('TPhotel_listing_NBhotel')->where('LocationId',$locationid)->get();
           $html3 = view('hotel_detail_result.nearby_hotels',['nearby_hotel'=>$nearby_hotel])->render();


           return response()->json([ 'html' => $html3]);
       }
    }
	 public function sightlist_saveNearbyhotel(request $request){


              $locid = $request->get('locationid');
        $getloc = DB::table('Location')->where('LocationId',$locid)->get();


        $nbh = 0;

        $nearby_hotel =collect();
        if(!empty($getloc)){
            $latitude = $getloc[0]->Lat;
            $longitude = $getloc[0]->Longitude ;
            $LocationId = $getloc[0]->LocationId ;


       if($latitude != "" && $longitude !=""){
            $get_nearby_hotel = DB::table('SightListingNBhotels')->where('loc_id',$locid)->get();


        if (!$get_nearby_hotel->count() >= 1) {
            $nbh = 1;
             $searchradius = 10;
             $nearby_hotel = DB::table("TPHotel")
                 ->select('id', 'name','location_id','slug','address','stars','pricefrom',
                     DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                     * cos(radians(TPHotel.Latitude))
                     * cos(radians(TPHotel.longnitude) - radians(" . $longitude . "))
                     + sin(radians(" . $latitude . "))
                     * sin(radians(TPHotel.Latitude))) AS distance"))
               //  ->groupBy("TPHotel.SightId")
                 ->having('distance', '<=', $searchradius)
                // ->where('TPHotel.hotelid', '!=', $sightid)
                 ->orderBy('distance')
                 ->limit(4)

                 ->get();


         if(!$nearby_hotel->isEmpty()){




            foreach ($nearby_hotel as $nearby_hotels) {
                $slug = $nearby_hotels->slug;
                $Title = $nearby_hotels->name;
                $LocationId = $nearby_hotels->location_id;
                $distance = round($nearby_hotels->distance,2);
                $address = $nearby_hotels->address;
                $stars = $nearby_hotels->stars;
                $pricefrom = $nearby_hotels->pricefrom;
                $id = $nearby_hotels->id;

                $data3= array(
                    'name'=>$Title,
                    'slug'=>$slug,
                    'hotelid'=>$id,
                    'LocationId'=>$LocationId,
                    'distance'=>$distance,
                    'radius'=>$searchradius,
                    'address'=>$address,
                    'stars'=>$stars,
                    'pricefrom'=>$pricefrom,
                    'dated'=>now(),
                    'loc_id'=>$locid,
                );


                $insertdata3 = DB::table('SightListingNBhotels')->insert($data3);
            //   return print_r($data2);
            }


         }
      }


        }
    }
        if($nbh = 1){

           $nearby_hotel =  DB::table('SightListingNBhotels')->where('loc_id',$locid)->get();

           $html3 = view('frontend.explore.loc_nearby_hotels_result',['nearby_hotel'=>$nearby_hotel])->render();


           return response()->json([ 'html' => $html3]);
       }
    }


	 public function hotel_all_filters_without_date(request $request)
    {

         $locationid = $request->get('locationid');

         $chkin = $request->get('Cin');
         $checout = $request->get('Cout');

        $rooms = $request->get('rooms');
        $guest = $request->get('guest');


        $minPrice = $request->get('priceFrom');
        $priceTo = $request->get('priceTo');
        $typeHotel = $request->get('hoteltype');
        $starRating = $request->get('starRating');
        $mnt = $request->get('mnt');
        $Smnt = $request->get('Smnt');

        $stars = [];
        if (is_string($starRating)) {
            $stars = explode(',', $starRating);
        }

        $amenities = [];
        if(is_string($mnt)){
            $amenities = explode(',', $mnt);
        }

        $Specamenities = [];
        if(is_string($Smnt)){
            $Specamenities = explode(',', $Smnt);
        }
         $Specamenities;


        $typeHotels = [];
        if (is_string($typeHotel)) {
            $typeHotels = explode(',', $typeHotel);
        }

        $userrating = $request->get('userrating');
        $user_rating = [];
        if (is_string($userrating)) {
            $user_rating = explode(',', $userrating);
        }


        $address = $request->get('address');
        $distance = $request->get('distance');
      if($distance ==0){
        $distance = "";
      }


                    $searchresults = DB::table('TPHotel as h')
                    ->select([
                        'h.hotelid',
                        'h.id',
                        'h.location_id as loc_id',
                        'h.name',
                        'h.address',
                        'h.slug',
                        'h.distance',
                        'h.stars',
                        'h.pricefrom',
                        'h.rating',
                        'h.photos',
                        'h.facilities',
                        'h.amenities',
                        'h.shortFacilities',
                        'l.fullName',
                        'l.countryName',
                        'l.cityName',
                        'ty.type as propertyType',
                        'h.image',
                    ])
                    ->join('TPLocations as l', 'l.locationId', '=', 'h.location_id')
                    ->leftJoin('TPHotel_types as ty', 'ty.hid', '=', 'h.propertyType')
                    ->when(!empty($stars), function ($query) use ($stars) {
                        $query->whereIn('h.stars', $stars);
                    })
                    // ->when(!empty($distance), function ($query) use ($distance) {
                    //     $query->orWhere('h.distance', '<=', $distance);
                    // })
                    ->when(!empty($address), function ($query) use ($address) {
                        $query->where('h.address', 'like','%'. $address . '%');
                    })
                    ->when(!empty($typeHotels), function ($query) use ($typeHotels) {
                        $query->whereIn('h.propertyType', $typeHotels);
                    })
                    ->when(!empty($user_rating), function ($query) use ($user_rating) {
                        $query->whereIn('h.rating', $user_rating);
                    })
					 ->when(!empty($user_rating), function ($query) use ($user_rating) {
                        $query->whereIn('h.rating', $user_rating);
                    })
					->when(!empty($minPrice) && !empty($priceTo), function ($query) use ($minPrice, $priceTo) {
						$query->whereBetween('h.pricefrom', [$minPrice, $priceTo]);
					})
                    ->when(!empty($amenities), function ($query) use ($amenities) {
                        foreach ($amenities as $amenity) {
                            $query->where('h.amenities', 'LIKE', '%' . $amenity . '%');
                        }
                    })
                 //   ->limit(30)
                   ->paginate(10);

                    $url = 'ho-'.$locationid;
                    $searchresults->appends(request()->except(['_token']));

                    $searchresults->setPath($url);
                    $paginationLinks = $searchresults->links('hotellist_pagg.default');








//return   print_r($searchresults);
//    echo '---';
//    print_r($hotel);
//    die();

        $lname ="";
        $countryName ="";
        if(!$searchresults->isEmpty()){
            $lname = $searchresults[0]->cityName;
            $countryName = $searchresults[0]->countryName;
        }


        return view('frontend.hotel.get_hotel_listing_result_withoutdate')->with('searchresults',$searchresults)->with('lname',$lname)->with('countryname',$countryName);
    }




 public function explore_country_list($id,$slug)
 {
        $is_rest ="";
        $ismustsee ="";
        $is_rest ="";
        $rest_avail ="";
        $getSightCat ="";

        $start =  date("H:i:s");


        $cont = DB::table('Country')
        ->select('Country_Content','Name','CountryId')
        ->where('CountryId', $id)
        ->where('slug', $slug)
        ->get()
        ->toArray();

         if(empty($cont)){
              abort(404, 'NOT FOUND');
         }

         $countryname = $cont[0]->Name;


           //end

        $faq= collect();

        $getloc = DB::table('Location')
        ->select('LocationId')
        ->where('CountryId', $id)
        ->get()
        ->toArray();
        $locationIds = array_column($getloc, 'LocationId');

        $searchresults = DB::table('Sight as s')
        ->select('s.*', 'c.Title as CategoryTitle', 'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc', 'l.tp_location_mapping_id', 'co.Name as CountryName','l.About')
        ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
        ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
        ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
      //  ->where('s.LocationId', $locationIds)
      ->whereIn('s.LocationId', $locationIds)
      //  ->where('co.slug', $slug)
        //->where('l.LocationLevel',1)
      //  ->where('co.CountryId', $id)
        ->where('s.IsMustSee',1)
        ->orderBy('s.TATrendingScore', 'desc')
        ->limit(10)
        ->get()
        ->toArray();





        $getSightCat = DB::table('Sight')
            ->select('Category.CategoryId', 'Category.Title')
            ->distinct()
            ->join('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->whereIn('Sight.LocationId', $locationIds) // Use location IDs obtained from the first query
            ->get();



            $breadcumb=[];

            $breadcumb  = DB::table('Country as c')
            ->select('co.*','c.Name as cname')
            ->join('CountryCollaboration as co', 'c.CountryCollaborationId', '=', 'co.CountryCollaborationId')
            ->where('c.CountryId', $id)
            ->get()
            ->toArray();


         $locn ="";
         $tplocname=array();
         $locationPatent = [];

         $getrest=collect();
         $experience =collect();
         $nearby_hotel =collect();
         $gethotellistiid =collect();

         if(!empty($searchresults)){
          $locationID  = $searchresults[0]->LocationId;
          $lociID = $searchresults[0]->LocationId;
          $locn =  $searchresults[0]->LocationId;

          if(empty($searchresults)){


            $loc = DB::table('Location')
            ->select('LocationId', 'parentid','LocationLevel')
            ->where('LocationId', $locationID)
            ->first();

            if(!empty($loc)){
                    $parentId =$loc->parentid;
                $LocationLevel =$loc->LocationLevel;


                while ($parentId !== null && $LocationLevel !=1) {
                    $parent = DB::table('Location')
                        ->select('LocationId', 'ParentId')
                        ->where('LocationId', $parentId)
                        ->first();


                    if ($parent) {
                        $isParentInSight = DB::table('Sight')
                            ->where('LocationId', $parent->LocationId)
                            ->exists();

                        if ($isParentInSight) {
                            $parentId = $parent->LocationId;
                            break;
                        } else {
                            $parentId = $parent->ParentId;
                        }
                    } else {
                        $parentId = null;
                    }
                }
            }


                $searchresults = DB::table('Sight as s')
                ->select('s.*', 'c.Title as CategoryTitle', 'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc', 'l.tp_location_mapping_id', 'co.Name as CountryName')
                ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
                ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
                ->leftJoin('Country as co', 'l.CountryId', '=', 'co.CountryId')
               // ->where('l.Slug', $explocname)
                ->where('l.LocationId', $parentId)
                ->orderBy('s.TATrendingScore', 'desc')
                ->limit(10)
                ->get()
                ->toArray();
            }



          $start3 =  date("H:i:s");


           if(!empty($searchresults[0]->tp_location_mapping_id)){
             $tplocname =  DB::table('TPLocations')->select('cityName','countryName','LocationId')->where('LocationId',$searchresults[0]->tp_location_mapping_id)->get();
           }

           $getparent = DB::table('Location')->where('LocationId', $lociID)->get();


           if (!$getparent->isEmpty()){
           if (!empty($getparent) && $getparent[0]->LocationLevel != 1) {
               $loopcount = $getparent[0]->LocationLevel;
               $lociID = $getparent[0]->ParentId;
               for ($i = 1; $i < $loopcount; $i++) {
                   $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                   if (!empty($getparents)) {
                        $locationPatent[] = [
                            'LocationId' => $getparents[0]->LocationId,
                            'slug' => $getparents[0]->Slug,
                            'Name' => $getparents[0]->Name,
                        ];
                       if (!empty($getparents) && $getparents[0]->ParentId != "") {
                       $lociID = $getparents[0]->ParentId;
                    }
                   } else {
                       break; // Exit the loop if no more parent locations are found
                   }
               }
           }
        }

              if(!empty($searchresults[0]->LocationId)){
               $getrest = DB::table('Restaurant')->select('Title','RestaurantId','LocationId','Slug','Address','PriceRange')->whereIn('LocationId',$locationIds)->limit(10)->get();
              }


             if(!empty($searchresults[0]->LocationId)){
                $experience =  DB::table('Experience')->whereIn('LocationId',$locationIds)->limit(10)->get();
            }

             //new code
            $percentageRecommended = 0;
            if(!empty($searchresults)){
         //   if (!$searchresults->isEmpty()) {

                foreach ($searchresults as $results) {
                    $sightId = $results->SightId;

                    $Sightcat = DB::table('SightCategory')
                        ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
                        ->select('Category.Title')
                        ->where('SightCategory.SightId', '=', $sightId)
                        ->get();

                    $results->Sightcat = $Sightcat;

                    $timing = DB::select("SELECT * FROM SightTiming WHERE SightId = ?", [$sightId]);
                    $results->timing = $timing;

                }
                //end code



            }
           //nearby hotel


              $nearby_hotel =  DB::table('SightListingNBhotels')->where('loc_id',$locationID)->get();
              /*end nearby hotels */


              $getloc = DB::table('Location')->where('LocationId',$locationID)->get();


              if(!$getloc->isEmpty()){
                $latitude = $getloc[0]->Lat;
                $longitude = $getloc[0]->Longitude ;

               //hotel list id

              $gethotellistiid = DB::table('Temp_Mapping as tm')
              ->select('tpl.*')
              ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
              //  ->where('tm.Tid',$locationID)
               ->whereIn('tm.Tid',$locationIds)
              ->get();


              $CountryId ="";
             $formattedDateTime = date("H:i:s");
             if($gethotellistiid->isEmpty()){
                $getlocid = DB::table('Location')->select('ParentId','CountryId')->where('LocationId',$locationID)->get();
                if(!$getlocid->isEmpty()){
                 $CountryId =$getlocid[0]->CountryId;
                }
             }


            if($gethotellistiid->isEmpty()){
                    $getlocid = DB::table('Location')->select('ParentId','CountryId')->where('LocationId',$locationID)->get();
                    if(!$getlocid->isEmpty()){
                        $locationID = $getlocid[0]->ParentId;
                        $CountryId =$getlocid[0]->CountryId;


                    $gethotellistiid = DB::table('Temp_Mapping as tm')
                    ->select('tpl.*')
                    ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
                // ->where('tm.Tid',$locationID)
                    ->whereIn('tm.Tid',$locationIds)
                    ->get();
                    }
            }

        }


        //end record nit available

            if ($gethotellistiid->isEmpty()) {
                $gethotellistiid = DB::table('Temp_Mapping as tm')
                    ->select('tpl.locationId')
                    ->join('TPLocations as tpl', 'tpl.locationId', '=', 'tm.LocationId')
                    ->join('Location as l', 'l.locationId', '=', 'tm.Tid')
                    ->where('l.CountryId', $CountryId)
                    ->limit(1) // limit the result to 1 row
                    ->get();
            }


              // end hotel list id
              }



                //end nearby hotel

            return view('country_sight_listing')->with('searchresults',$searchresults)->with('searchlocation',$locn)->with('faq',$faq)->with('getSightCat',$getSightCat)->with('rest_avail',$rest_avail)->with('ismustsee',$ismustsee)->with('tplocname',$tplocname)->with('locationPatent',$locationPatent)->with('getrest',$getrest)->with('experience',$experience)->with('nearby_hotel',$nearby_hotel)->with('gethotellistiid',$gethotellistiid)->with('breadcumb',$breadcumb)->with('countryname',$countryname)->with('cont',$cont);




        }



    public function loadMoresightbycontry(Request $request)
    {
        $page = $request->input('page');
        $perPage = 10;
        $contid = $request->input('locid');

        if ($page == 1) {
            return response()->json(['html' => '']);
        }

        $offset = ($page - 1) * $perPage;

        $searchresults = DB::table('Sight as s')
        ->select('s.*', 'c.Title as CategoryTitle', 'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc', 'l.tp_location_mapping_id', 'co.Name as CountryName','l.About')
        ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
        ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
        ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
        //  ->where('co.slug', $slug)
        ->where('l.LocationLevel',1)
        ->where('co.CountryId', $contid)
        ->where('s.IsMustSee',1)
        ->orderBy('s.TATrendingScore', 'desc')
        ->get();

        $attractions = collect($searchresults)
            ->slice($offset, $perPage)
            ->values();

            if (!empty($attractions)) {

                foreach ($attractions as $results) {
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
            if (!empty($attractions)) {
                foreach ($attractions as $att) {
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
                                if($att->TAAggregateRating != ""){
                                    $recomd = rtrim($att->TAAggregateRating, '.0') * 20;
                            }else{
                                $recomd ='Unavailable';
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
                                ];

                                $mergedData[] = $locationData; // Add the locationData directly to mergedData
                            }
                        }
                    } else {
                        // If there are no categories, create a default "uncategorized" category
                        if (!empty($att->Latitude) && !empty($att->Longitude)) {
                            // Check if $att->timing is set and contains the required properties
                            if (isset($att->timing->timings)) {
                                // Calculate the opening and closing time (same as above)
                                // ...
                                // ...
                                if($att->TAAggregateRating != ""){
                                    $recomd = rtrim($att->TAAggregateRating, '.0') * 20;
                                }else{
                                    $recomd ='Unavailable';
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
                                ];

                                $mergedData[] = $locationData;
                            }
                        }
                    }
                }
            }

            // Encode data as JSON
            $locationDataJson = json_encode($mergedData);



            if ($attractions->isEmpty()) {
                return response()->json(['html' => '']);
            }
            $html = view('getloclistbycatid')->with('searchresults', $attractions)->render();

            return response()->json(['mapData' => $locationDataJson, 'html' => $html]);

    }
	public function explore_continent_list($id,$slug){

        $is_rest ="";
        $ismustsee ="";
        $is_rest ="";
        $rest_avail ="";
        $getSightCat ="";

        $start =  date("H:i:s");

        $slug = str_replace('-',' ',$slug);
        $cont = DB::table('CountryCollaboration')
        ->select('CountryCollaborationId','Name')
        ->where('CountryCollaborationId', $id)
        ->where('Name', $slug)
        ->get()
        ->toArray();

         if(empty($cont)){
              abort(404, 'NOT FOUND');
         }

         $countryname = $cont[0]->Name;


        $faq= collect();

        $getcount = DB::table('Country as c')
        ->join('CountryCollaboration as clb', 'clb.CountryCollaborationId', '=', 'c.CountryCollaborationId')
        ->select('c.CountryId', 'c.Name')
        ->where('clb.CountryCollaborationId', $id)
       // ->limit(20)

        ->get()
        ->toArray();
        //return print_r($getcount);
        $countryId = array_column($getcount, 'CountryId');
        $getloc = DB::table('Location as l')
        ->select('l.LocationId', 'l.Name')
        ->whereIn('l.CountryId', $countryId)
        ->limit(50)
        ->get()
        ->toArray();


     //   return  $getloc;
        $locationIds = array_column($getloc, 'LocationId');

        $searchresults = DB::table('Sight as s')
        ->select('s.*', 'c.Title as CategoryTitle', 'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc', 'l.tp_location_mapping_id', 'co.Name as CountryName','l.About')
        ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
        ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
        ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
      //  ->where('s.LocationId', $locationIds)
      ->whereIn('s.LocationId', $locationIds)
      //  ->where('co.slug', $slug)
        //->where('l.LocationLevel',1)
      //  ->where('co.CountryId', $id)
        ->where('s.IsMustSee',1)
        // ->orderBy('s.TATrendingScore', 'desc')
        ->limit(10)
        ->get()
        ->toArray();





        $getSightCat = DB::table('Sight')
            ->select('Category.CategoryId', 'Category.Title')
            ->distinct()
            ->join('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->whereIn('Sight.LocationId', $locationIds) // Use location IDs obtained from the first query
            ->get();




         $locn ="";
         $tplocname=array();
         $locationPatent = [];

         $getrest=collect();
         $experience =collect();
         $nearby_hotel =collect();
         $gethotellistiid =collect();

         if(!empty($searchresults)){
          $locationID  = $searchresults[0]->LocationId;
          $lociID = $searchresults[0]->LocationId;
          $locn =  $searchresults[0]->LocationId;

          if(empty($searchresults)){


            $loc = DB::table('Location')
            ->select('LocationId', 'parentid','LocationLevel')
            ->where('LocationId', $locationID)
            ->first();

            if(!empty($loc)){
                    $parentId =$loc->parentid;
                    $LocationLevel =$loc->LocationLevel;


                while ($parentId !== null && $LocationLevel !=1) {
                    $parent = DB::table('Location')
                        ->select('LocationId', 'ParentId')
                        ->where('LocationId', $parentId)
                        ->first();


                    if ($parent) {
                        $isParentInSight = DB::table('Sight')
                            ->where('LocationId', $parent->LocationId)
                            ->exists();

                        if ($isParentInSight) {
                            $parentId = $parent->LocationId;
                            break;
                        } else {
                            $parentId = $parent->ParentId;
                        }
                    } else {
                        $parentId = null;
                    }
                }
            }


                $searchresults = DB::table('Sight as s')
                ->select('s.*', 'c.Title as CategoryTitle', 'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc', 'l.tp_location_mapping_id', 'co.Name as CountryName')
                ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
                ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
                ->leftJoin('Country as co', 'l.CountryId', '=', 'co.CountryId')
               // ->where('l.Slug', $explocname)
                ->where('l.LocationId', $parentId)
                ->orderBy('s.TATrendingScore', 'desc')
                ->limit(10)
                ->get()
                ->toArray();




            }


            $locids = []; // Initialize an empty array to store LocationIds

            foreach ($searchresults as $result) {
                // Assuming 'LocationId' is a property of the result object
                if (isset($result->LocationId)) {
                    $locids[] = $result->LocationId;
                }
            }
       //     return print_r($locids);

          $start3 =  date("H:i:s");


           if(!empty($searchresults[0]->tp_location_mapping_id)){
             $tplocname =  DB::table('TPLocations')->select('cityName','countryName','LocationId')->where('LocationId',$searchresults[0]->tp_location_mapping_id)->get();
           }

           $getparent = DB::table('Location')->where('LocationId', $lociID)->get();


           if (!$getparent->isEmpty()){
           if (!empty($getparent) && $getparent[0]->LocationLevel != 1) {
               $loopcount = $getparent[0]->LocationLevel;
               $lociID = $getparent[0]->ParentId;
               for ($i = 1; $i < $loopcount; $i++) {
                   $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
                   if (!empty($getparents)) {
                        $locationPatent[] = [
                            'LocationId' => $getparents[0]->LocationId,
                            'slug' => $getparents[0]->Slug,
                            'Name' => $getparents[0]->Name,
                        ];
                       if (!empty($getparents) && $getparents[0]->ParentId != "") {
                       $lociID = $getparents[0]->ParentId;
                    }
                   } else {
                       break; // Exit the loop if no more parent locations are found
                   }
               }
           }
        }



             //new code
            $percentageRecommended = 0;
            if(!empty($searchresults)){
         //   if (!$searchresults->isEmpty()) {

                foreach ($searchresults as $results) {
                    $sightId = $results->SightId;

                    $Sightcat = DB::table('SightCategory')
                        ->join('Category', 'SightCategory.CategoryId', '=', 'Category.CategoryId')
                        ->select('Category.Title')
                        ->where('SightCategory.SightId', '=', $sightId)
                        ->get();

                    $results->Sightcat = $Sightcat;

                    $timing = DB::select("SELECT * FROM SightTiming WHERE SightId = ?", [$sightId]);
                    $results->timing = $timing;

                }
                //end code



            }



       if(!empty($locids)){
	   $getrest = DB::table('Restaurant')->select('Title','RestaurantId','LocationId','Slug','Address','PriceRange')->whereIn('LocationId',$locids)->get();
        $nearby_hotel =  DB::table('SightListingNBhotels')->whereIn('loc_id',$locids)->get();
        $experience =  DB::table('Experience')->whereIn('LocationId',$locids)->get();
       }
           //nearby hotel


              /*end nearby hotels */
     //     return print_r( $nearby_hotel);

              $getloc = DB::table('Location')->where('LocationId',$locationID)->get();


              if(!$getloc->isEmpty()){
                $latitude = $getloc[0]->Lat;
                $longitude = $getloc[0]->Longitude ;

               //hotel list id

              $gethotellistiid = DB::table('Temp_Mapping as tm')
              ->select('tpl.*')
              ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
              //  ->where('tm.Tid',$locationID)
               ->whereIn('tm.Tid',$locationIds)
              ->get();


              $CountryId ="";
             $formattedDateTime = date("H:i:s");
             if($gethotellistiid->isEmpty()){
                $getlocid = DB::table('Location')->select('ParentId','CountryId')->where('LocationId',$locationID)->get();
                if(!$getlocid->isEmpty()){
                 $CountryId =$getlocid[0]->CountryId;
                }
             }


            if($gethotellistiid->isEmpty()){
                    $getlocid = DB::table('Location')->select('ParentId','CountryId')->where('LocationId',$locationID)->get();
                    if(!$getlocid->isEmpty()){
                        $locationID = $getlocid[0]->ParentId;
                        $CountryId =$getlocid[0]->CountryId;


                    $gethotellistiid = DB::table('Temp_Mapping as tm')
                    ->select('tpl.*')
                    ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
                // ->where('tm.Tid',$locationID)
                    ->whereIn('tm.Tid',$locationIds)
                    ->get();
                    }
            }

        }


        //end record nit available

            if ($gethotellistiid->isEmpty()) {
                $gethotellistiid = DB::table('Temp_Mapping as tm')
                    ->select('tpl.locationId')
                    ->join('TPLocations as tpl', 'tpl.locationId', '=', 'tm.LocationId')
                    ->join('Location as l', 'l.locationId', '=', 'tm.Tid')
                    ->where('l.CountryId', $CountryId)
                    ->limit(1) // limit the result to 1 row
                    ->get();
            }


              // end hotel list id
              }



                //end nearby hotel

            return view('continent_sight_listing')->with('searchresults',$searchresults)->with('searchlocation',$locn)->with('faq',$faq)->with('getSightCat',$getSightCat)->with('rest_avail',$rest_avail)->with('ismustsee',$ismustsee)->with('tplocname',$tplocname)->with('locationPatent',$locationPatent)->with('getrest',$getrest)->with('experience',$experience)->with('nearby_hotel',$nearby_hotel)->with('gethotellistiid',$gethotellistiid)->with('countryname',$countryname)->with('cont',$cont);




        }


        public function loadMoresightbycontinent(Request $request)
        {
            $page = $request->input('page');
            $perPage = 50;
            $contlim = 50;
            $contid = $request->input('locid');

            if ($page == 1) {
                return response()->json(['html' => '']);
            }

            $offset = ($page - 1) * $perPage;
            $offset2 = $page  * $contlim;


            $getcount = DB::table('Country as c')
            ->join('CountryCollaboration as clb', 'clb.CountryCollaborationId', '=', 'c.CountryCollaborationId')
            ->select('c.CountryId', 'c.Name')
            ->where('clb.CountryCollaborationId', $contid)
           // ->limit(20)

            ->get()
            ->toArray();
            //return print_r($getcount);
            $countryId = array_column($getcount, 'CountryId');
            $getloc = DB::table('Location as l')
     //   ->join('Country as c', 'c.CountryId', '=', 'l.CountryId')
       // ->join('CountryCollaboration as clb', 'clb.CountryCollaborationId', '=', 'c.CountryCollaborationId')
        ->select('l.LocationId')
        ->whereIn('l.CountryId', $countryId)
       ->limit($offset2)
        ->get()
        ->toArray();


         //   return  $getloc;
            $locationIds = array_column($getloc, 'LocationId');


            $attraId = collect($getloc)
            ->slice($offset, $perPage)
            ->values()
            ->toArray();

            $attraIdValues = array_column($attraId, 'LocationId');
         //   print_r($attraIdValues);

                   $attractions = DB::table('Sight as s')

            ->select('s.*', 'c.Title as CategoryTitle', 'l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'l.MetaTagTitle as mTitle', 'l.MetaTagDescription as mDesc', 'l.tp_location_mapping_id', 'co.Name as CountryName','l.About')
            ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
            ->join('Location as l', 's.LocationId', '=', 'l.LocationId')
            ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
          //  ->where('s.LocationId', $locationIds)
          ->whereIn('s.LocationId', $attraIdValues)
          //  ->where('co.slug', $slug)
            //->where('l.LocationLevel',1)
          //  ->where('co.CountryId', $id)
            ->orWhere('s.IsMustSee',1)
            // ->orderBy('s.TATrendingScore', 'desc')
            ->limit(10)
            ->get();







                if (!empty($attractions)) {

                    foreach ($attractions as $results) {
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
                if (!empty($attractions)) {
                    foreach ($attractions as $att) {
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
                                  //  if($att->TAAggregateRating != ""){
                                    if (isset($att->TAAggregateRating) && $att->TAAggregateRating !== null && $att->TAAggregateRating > 0) {
                                        // ... code inside the condition ...
                                        $recomd = rtrim($att->TAAggregateRating, '.0') * 20;
                                }else{
                                    $recomd ='Unavailable';
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
                                    ];

                                    $mergedData[] = $locationData; // Add the locationData directly to mergedData
                                }
                            }
                        } else {
                            // If there are no categories, create a default "uncategorized" category
                            if (!empty($att->Latitude) && !empty($att->Longitude)) {
                                // Check if $att->timing is set and contains the required properties
                                if (isset($att->timing->timings)) {
                                    // Calculate the opening and closing time (same as above)
                                    // ...
                                    // ...
                                    if($att->TAAggregateRating != ""){
                                        $recomd = rtrim($att->TAAggregateRating, '.0') * 20;
                                    }else{
                                        $recomd ='Unavailable';
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
                                    ];

                                    $mergedData[] = $locationData;
                                }
                            }
                        }
                    }
                }

                // Encode data as JSON
                $locationDataJson = json_encode($mergedData);



                if ($attractions->isEmpty()) {
                    return response()->json(['html' => '']);
                }
                $html = view('getloclistbycatid')->with('searchresults', $attractions)->render();

                return response()->json(['mapData' => $locationDataJson, 'html' => $html]);

        }

    public function search_sights(request $request){

        $locId = $request->input('locationId');
        $val = $request->input('val');
        $res_type = $request->input('res_type');
     //   session()->flush();
        $result_rest =[];

        $rest =0;
        $exp =0;
       $recentSearches = session()->get('recent_sightlist_searches', []);

        if (!empty($val)) {
            $currentSearch = [
                'locationId' => $locId,
                'val' => $val,
                'type' => $res_type
            ];

            // Check if the current search already exists in recent searches
            $isDuplicate = false;
            foreach ($recentSearches as $search) {
                if ($search['locationId'] === $currentSearch['locationId'] &&
                    $search['val'] === $currentSearch['val'] &&
                    $search['type'] === $currentSearch['type']) {
                    $isDuplicate = true;
                    break;
                }
            }

            // Add the current search only if it is not a duplicate
            if (!$isDuplicate) {
                array_unshift($recentSearches, $currentSearch);
                $recentSearches = array_slice($recentSearches, 0, 4); // Limit to 4 recent searches
                session(['recent_sightlist_searches' => $recentSearches]);
            }
        }


        if(empty($val)){
            $result = DB::table('Sight')
            ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->join('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('Sight.SightId', '=', 'img.Sightid');
                $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
               })
            ->where('Sight.LocationId', $locId)
            ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.Averagerating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.ReviewCount','Sight.ticket')
           // ->select('Category.Title as CategoryTitle', 'Sight.*')
			->where('Sight.IsMustSee', 1)
            ->limit(10)
            ->get()
            ->toArray();
        }

        if($val == "Must See" || $val == "must see" || $val == "mustsee"){
            $result = DB::table('Sight')
            ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->join('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('Sight.SightId', '=', 'img.Sightid');
                $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
            })
            ->where('Sight.LocationId', $locId)
            ->where('Sight.IsMustSee', 1)
            ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.Averagerating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.ReviewCount','Sight.ticket')
            ->limit(5)
            ->get()
            ->toArray();
        }
        if(empty($result) && $val == "Restaurant" || $val == "restaurant"){
            $rest =1;
            $result_rest = DB::table('Restaurant as r')
            ->join('Location','Location.slugid','=','r.slugid')
            ->where('r.LocationId', $locId)
            ->where(function ($query) use ($val) {
                $query->where('r.Title', 'LIKE','%'. $val . '%') ;
            })
            ->select('r.LocationId','r.RestaurantId','r.Title','r.Timings','r.Averagerating','r.category','r.features','r.slugid','r.PriceRange','Location.Name as Lname')
            ->limit(5)
            ->get()
            ->toArray();
         //   return print_r(result_rest);
        }
        if(empty($result) && $val == "experience" || $val == "Experience"){
            $exp =1;
            $result_rest = DB::table('Experience as e')
            ->join('Location','Location.slugid','=','e.slugid')
            ->where('e.LocationId', $locId)
            ->select('e.slugid','e.ExperienceId','e.Slug','e.Name','e.adult_price','Location.Name as Lname','e.Img1','e.Img2','e.Img3')
            ->limit(5)
            ->get()
         ->toArray();
        //  return print_r($result_rest);
        }

        if(empty($result) &&  empty($result_rest)){
            $result = DB::table('Sight')
            ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->join('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('Sight.SightId', '=', 'img.Sightid');
                $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
            })
            ->where('Sight.LocationId', $locId)
            ->where(function ($query) use ($val) {
                $query->where('Sight.Title', 'LIKE', $val . '%');
            })
            ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.Averagerating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.ReviewCount','Sight.ticket')
        // ->select('Category.Title as CategoryTitle', 'Sight.*')
            ->limit(5)
            ->get()
            ->toArray();
       }


        if(empty($result) && empty($result_rest)){
            $result = DB::table('Sight')
            ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->join('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('Sight.SightId', '=', 'img.Sightid');
                $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
            })
            ->where('Sight.LocationId', $locId)
            ->where(function ($query) use ($val) {
                $query->where('Category.Title', 'LIKE', $val . '%');
            })
            ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.Averagerating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.ReviewCount','Sight.ticket')
            ->limit(5)
            ->get()
            ->toArray();
        }
        if(empty($result) && empty($result_rest)){
            $result = DB::table('Sight')
            ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->join('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('Sight.SightId', '=', 'img.Sightid');
                $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
            })
             ->where('Sight.LocationId', $locId)
            ->where(function ($query) use ($val) {
                $query->where('Sight.About', 'LIKE',  '%'. $val . '%');
            })
            ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.Averagerating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.ReviewCount','Sight.ticket')
            ->limit(5)
            ->get()
            ->toArray();

          // return print_r($result);
        }
        if(empty($result) && empty($result_rest)){
            $result = DB::table('Sight')
                ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
            ->join('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('Sight_image as img', function ($join) {
                $join->on('Sight.SightId', '=', 'img.Sightid');
                $join->whereRaw('img.Image = (SELECT Image FROM Sight_image WHERE Sightid =Sight.SightId LIMIT 1)');
            })
            ->leftJoin('SightReviews', 'Sight.SightId', '=', 'SightReviews.SightId')
            ->where('Sight.LocationId', $locId)
            ->where(function ($query) use ($val) {
                $query->where('SightReviews.ReviewDescription', 'LIKE','%'. $val . '%');
            })
            ->select('Sight.SightId', 'Sight.IsMustSee', 'Sight.Title', 'Sight.Averagerating', 'Sight.LocationId', 'Sight.Slug', 'IsRestaurant', 'Address', 'Sight.Latitude', 'Sight.Longitude', 'Sight.CategoryId', 'Category.Title as CategoryTitle', 'Location.Name as LName', 'Location.slugid',  'img.Image', 'Sight.ReviewCount','Sight.ticket')
            ->limit(5)
            ->get()
            ->toArray();


        }
//image code
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
}

//image code


		//return print_r($result);

        //restaurant
      //  $rest =0;
     //   $exp =0;
        if(empty($result)){

            $rest =1;
            if(empty($result_rest)){
                $result_rest = DB::table('Restaurant as r')
                ->join('Location','Location.slugid','=','r.slugid')
                ->where('r.LocationId', $locId)
                ->where(function ($query) use ($val) {
                    $query->where('r.Title', 'LIKE','%'. $val . '%') ;
                })
                ->select('r.LocationId','r.RestaurantId','r.Title','r.Timings','r.Averagerating','r.category','r.features','r.slugid','r.PriceRange','Location.Name as Lname')
                ->limit(5)
                ->get()
                ->toArray();
            }

            if(empty($result_rest)){
                $result_rest = DB::table('Restaurant as r')
                ->join('Location','Location.slugid','=','r.slugid')
                ->where('r.LocationId', $locId)
                ->where(function ($query) use ($val) {
                    $query->where('r.About', 'LIKE','%'. $val . '%');
                })
                ->select('r.LocationId','r.RestaurantId','r.Title','r.Address','r.Timings','r.Averagerating','r.category','r.features','r.slugid','r.PriceRange','Location.Name as Lname')
                ->limit(5)
                ->get()
                ->toArray();
            }
            if(empty($result_rest)){
                $result_rest = DB::table('Restaurant as r')
                ->join('Location','Location.slugid','=','r.slugid')
                ->join('RestaurantCuisineAssociation as ra','ra.RestaurantId','=','r.RestaurantId')
                ->join('RestaurantCuisine as rc','ra.RestaurantCuisineId','=','rc.RestaurantCuisineId')
                ->where('r.LocationId', $locId)
                ->where(function ($query) use ($val) {
                    $query->where('rc.Name', 'LIKE','%'. $val . '%');
                })
                ->select('r.LocationId','r.RestaurantId','r.Title','r.Address','r.Timings','r.Averagerating','r.category','r.features','r.slugid','r.PriceRange','Location.Name as Lname')
                ->limit(5)
                ->get()
                ->toArray();
            }
             if(empty($result_rest)){
                $result_rest = DB::table('Restaurant as r')
                ->join('Location','Location.slugid','=','r.slugid')
                ->join('RestaurantReview as rv','rv.RestaurantId','=','r.RestaurantId')
                ->where('r.LocationId', $locId)
                ->where(function ($query) use ($val) {
                    $query->where('rv.Description', 'LIKE','%'. $val . '%');
                })
                ->select('r.LocationId','r.RestaurantId','r.Title','r.Address','r.Timings','r.Averagerating','r.category','r.features','r.slugid','r.PriceRange','Location.Name as Lname')
                ->limit(5)
                ->get()
                ->toArray();
            }

            if(empty($result_rest)){
               $exp =1;
                $result_rest = DB::table('Experience as e')
                ->join('Location','Location.slugid','=','e.slugid')
                ->where('e.LocationId', $locId)
                ->where(function ($query) use ($val) {
                    $query->where('e.Name', 'LIKE','%'. $val . '%');
                })
                ->select('e.slugid','e.ExperienceId','e.Slug','e.Name','e.adult_price','Location.Name as Lname','e.Img1','e.Img2','e.Img3')
                ->limit(5)
                ->get()
                ->toArray();
            }
            if(empty($result_rest)){
                $exp =1;
                 $result_rest = DB::table('Experience as e')
                 ->join('Location','Location.slugid','=','e.slugid')
                 ->where('e.LocationId', $locId)
                 ->where(function ($query) use ($val) {
                     $query->where('e.Inclusive', 'LIKE','%'. $val . '%');
                 })
                 ->select('e.slugid','e.ExperienceId','e.Slug','e.Name','e.adult_price','Location.Name as Lname','e.Img1','e.Img2','e.Img3')
                 ->limit(5)
                 ->get()
                 ->toArray();
             }

             if(empty($result_rest)){
                $exp =1;
                 $result_rest = DB::table('Experience as e')
                 ->join('Location','Location.slugid','=','e.slugid')
                 ->join('ExperienceItninerary as  et','et.ExperienceId','=','e.ExperienceId')
                 ->where('e.LocationId', $locId)
                 ->where(function ($query) use ($val) {
                     $query->where('et.Name', 'LIKE','%'. $val . '%');
                 })
                 ->select('e.slugid','e.ExperienceId','e.Slug','e.Name','e.adult_price','Location.Name as Lname','e.Img1','e.Img2','e.Img3')
                 ->limit(5)
                 ->get()
                 ->toArray();
             }
         //  return print_r($result_rest);
            if(empty($result_rest)){
                $exp =1;
                $result_rest = DB::table('Experience as e')
				->join('Location','Location.slugid','=','e.slugid')
                ->join('ExperienceReview as  rv','rv.ExperienceId','=','e.ExperienceId')
                ->where('e.LocationId', $locId)
                ->where(function ($query) use ($val) {
                    $query->where('rv.Description', 'LIKE','%'. $val . '%');
                })
                ->select('e.slugid','e.ExperienceId','e.Slug','e.Name','e.adult_price','Location.Name as Lname','e.Img1','e.Img2','e.Img3')
                ->limit(5)
                ->get()
                ->toArray();
            }

        }


        //end restaurant


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
 					if($att->Averagerating != ""  && $att->Averagerating != 0){
                        $recomd = rtrim($att->Averagerating, '.0') * 20;
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
                        'tm' => $timingInfo,
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
                    // Calculate the opening and closing time (same as above)
                    // ...
                    // ...
				   if($att->Averagerating != ""  && $att->Averagerating != 0){
                        $recomd = rtrim($att->Averagerating, '.0') * 20;
					   $recomd = $recomd . '%';
                   }else{
                       $recomd ='--';
                   }
                   $imagepath ="";
                   if($att->Image !=""){
                          $imagepath = asset('sight-images/'. $att->Image) ;
                   }else{
                          $imagepath = asset('images/Hotel lobby.svg');
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
  // return $mergedData;
	// Encode data as JSON
	    $locationDataJson = json_encode($mergedData);

        if($exp == 1){
            $html = view('get_location_filtered_experience')->with('result', $result_rest)->with('type','search')->render();
        }elseif($rest == 1){
            $html = view('get_location_filtered_Restaurant')->with('result', $result_rest)->with('type','search')->render();
        }else{
            $html = view('getloclistbycatid')->with('sightImages',$sightImages)->with('searchresults', $result)->with('type','search')->render();
        }


        return response()->json(['mapData' => $locationDataJson, 'html' => $html]);

    }


	//end filter


       public function update_sight_desc(request $request){
        $desc = $request->get('desc');
        $sightid = $request->get('id');
        if($sightid != ''){
            $update = DB::table('Sight')
            ->where('SightId', $sightid)
            ->update(['About' => $desc]);
        }


        $get_about = DB::table('Sight')->select('About')->where('SightId',$sightid)->get();
        return  view('get_about',['searchresult'=>$get_about]);

    }

    public function restaurant_landing(request $request){
   //echo $locationID =828104;  // 840749;
        $category ="";
        $category = $request->get('q');
        $locationID = $request->get('location');

        if( $locationID !=""){
             $getloc = DB::table('Location')->select('LocationId')->where('slugid', $locationID)->get();

            if(!$getloc->isEmpty()){
                $locationID =  $getloc[0]->LocationId;
            }
        }


        $breadcumb=[];
        $locationPatent = [];
       if( $locationID != ""){

        $breadcumb  = DB::table('Location as l')
        ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid')
        ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
        ->leftJoin('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
        ->where('l.LocationId', $locationID)
        ->get()
        ->toArray();

     //  return print_r( $breadcumb);

        $getparent = DB::table('Location')->select('LocationId','slug','Name','LocationLevel','ParentId')->where('LocationId', $locationID)->get();




      if (!empty($getparent) && $getparent[0]->LocationLevel != 1) {
          $loopcount = $getparent[0]->LocationLevel;
          $lociID = $getparent[0]->ParentId;
          for ($i = 1; $i < $loopcount; $i++) {
              $getparents = DB::table('Location')->where('LocationId', $lociID)->get();
              if (!empty($getparents)) {
                   $locationPatent[] = [
                       'LocationId' => $getparents[0]->slugid,
                       'slug' => $getparents[0]->Slug,
                       'Name' => $getparents[0]->Name,
                   ];
                  if (!empty($getparents) && $getparents[0]->ParentId != "") {
                  $lociID = $getparents[0]->ParentId;
               }
              } else {
                  break; // Exit the loop if no more parent locations are found
              }
          }
      }

  }

      $searchresults = DB::table('Restaurant as r')
      ->leftjoin('Location as l', 'l.LocationId', '=', 'r.LocationId')
      ->select('r.*', 'l.Longitude as loc_longitude', 'l.Lat as loc_latitude','l.Name as lname','l.slugid')
      ->whereRaw("CONCAT(',', r.category, ',') LIKE '%,{$category},%'");

        if ($locationID !="") {
            $searchresults->where('r.LocationId', $locationID);
        }

          $searchresults = $searchresults->limit(15)->get();
          $lname ="";
          if(!$searchresults->isEmpty()){
            $lname =  $searchresults[0]->lname;
          }


        return view('restaurant_landing',['breadcumb'=>$breadcumb,'locationPatent'=>$locationPatent,'searchresults'=>$searchresults,'category'=>$category,'lname'=>$lname]);
    }

  public function hotel_room_desc(Request $request) {
    $checkin = $request->get('checkin');
    $checkout = $request->get('checkout');
    $cityId = $request->get('lid');
    $hotelId = $request->get('hid');

	$checkin = $this->formatDate($checkin);
    $checkout = $this->formatDate($checkout);

    // Other variables and initial setup...

    // Fetch room data
    $roomsData = [];

    if($checkin !=""  && $checkout !="") {
        $pgtype = 'withdate';

        //   $start  =  date("H:i:s");
        $pgtype = 'withdate';

       // $cityName = $request->get('cityName') ;

       $guests = 2; //Session()->get('guest');
       $rooms = 1;//Session()->get('rooms');

       $stchin = $checkin;
       $checkout = $checkout;

       $cmbdate = $checkin.'_'.$checkout;
       $checkin =  $checkin;


       //new code start
       $checkinDate = $checkin;
       $checkoutDate = $checkout;
       $adultsCount = 2; //$guests;
       $customerIP = '49.156.89.145';
       $childrenCount = '1';
       $chid_age = '10';
       $lang = 'en';
       $currency ='USD';
       $waitForResult ='0';
       $iata=$hotelId;

       $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
       $TRAVEL_PAYOUT_MARKER = "299178";



       $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
       $checkinDate.":".
       $checkoutDate.":".
       $chid_age.":".
       $childrenCount.":".
       $currency.":".
       $customerIP.":".
       $iata.":".
       $lang.":".
       $waitForResult;


       $signature = md5($SignatureString);
       //  $signature = '3193e161e98200459185e43dd7802c2c';

       $url ='http://engine.hotellook.com/api/v2/search/start.json?hotelId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;



      $response = Http::withoutVerifying()
        ->timeout(30)  // 30 seconds timeout
        ->retry(4, 1000) // retry 4 times with 1 second delay
        ->get($url);
       if ($response->successful()) {


       $data = json_decode($response);
           if(!empty($data)){
           $searchId = $data->searchId;


           $limit =10;
           $offset=0;
           $roomsCount=10;
           $sortAsc=1;
           $sortBy='price';

           $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
               $sig2 =  md5($SignatureString2);

           $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=10&sortBy=price&sortAsc=1&roomsCount=10&offset=0&marker=299178&signature='.$sig2;
       // $response2 = Http::withoutVerifying()->get($url2);

           $maxAttempts = 4;
           $retryInterval = 1; // seconds
           $response2 = Http::withoutVerifying()
           ->timeout(0) // Maximum time for an individual request
           ->retry($maxAttempts, $retryInterval)
           ->get($url2);
           $jsonResponse = json_decode($response2, true);

           //new code 1
           if ($jsonResponse['status'] == 'ok') {


               $hotels = $jsonResponse['result'];
               $rooms = [];
               $roomsData = [];

                if ($jsonResponse['status'] == 'ok') {
                    $hotels = $jsonResponse['result'];

                    foreach ($hotels as $hotel) {
                        if (isset($hotel['rooms']) && is_array($hotel['rooms'])) {
                            foreach ($hotel['rooms'] as $room) {
                                $roomsData[$room['desc']] = [
                                    'price' => $room['total'],
                                    'agencyId' => $room['agencyId'],
                                    'fullBookingURL' => $room['fullBookingURL']
                                ];
                            }
                        }

                   //     return $roomsData;
                    }
                }


            }
        }
    }
}

    $TPRoomtype = DB::table('TPRoomtype')->select('Roomdesc')->where('hotelid', $hotelId)->get();
	$tpdesc =[];
    return response()->json([
        'roomsData' => $roomsData,
        'TPRoomtype' => $TPRoomtype,
		 'tpdesc' => $tpdesc
    ]);
}
//filter hotel room detail page


   public function filter_hotel_room(Request $request)
    {
        $value = $request->get('value'); // Example: ['breakfast', 'deposit']
        $hotelid = $request->get('hotelid');

        // Retrieve the rooms for the specified hotel
        $rooms = DB::table('TPRoomtype')
            ->where('hotelid', $hotelid)
            ->get();

        // Retrieve the hotel data
        $hotel = DB::table('TPHotel')
            ->select('photosByRoomType', 'hotelid', 'photoCount')
            ->where('hotelid', $hotelid)
            ->get();

        // Decode room descriptions
        $roomDesc = json_decode($rooms[0]->Roomdesc, true);

        $updatedRoomDescJson = []; // Initialize the array to store filtered room data

        if (!empty($value)) {
            foreach ($roomDesc as $key => $desc) {
                $includeRoom = true;

                foreach ($value as $filterValue) {
                    // Check if each filter value is set and true in the description
                    if (isset($desc[$filterValue]) && $desc[$filterValue] == true) {
                        $updatedRoomDescJson[$key] = $desc;
                    }
                }

            }

            $updatedRoomDescJson = json_encode($updatedRoomDescJson);
        } else {
            // If no filters provided, return all room descriptions
            $updatedRoomDescJson = json_encode($roomDesc);
        }

        // Retrieve room types
        $getroomtype = collect();
        $photosByRoomType = json_decode($hotel[0]->photosByRoomType, true);

        if (!empty($photosByRoomType)) {
            $roomtyids = array_keys($photosByRoomType);

            $getroomtype = DB::table('TPRoom_types')
                ->select('rid', 'type')
                ->whereIn('rid', $roomtyids)
                ->get();
        }

        // Return the view with the filtered room data
        return view('frontend.hotel.filter_room_result', [
            'TPRoomtype' => $rooms,
            'updatedRoomDescJson' => $updatedRoomDescJson,
            'searchresult' => $hotel,
            'getroomtype' => $getroomtype
        ]);
    }




 public function filter_hotel_room_with_date(request $request){
         $value = $request->get('value');

         $hotelid = $request->get('hotelid');

         $checkout = $request->get('checkout');
         $checkin = $request->get('checkin');

         $hoteldata = DB::table('TPHotel')
         ->select('photosByRoomType','hotelid','photoCount')
         ->where('hotelid',$hotelid)
         ->get();

         $roomsprice = [];

         $roomsData = [];
            $rooms =[];
            $pgtype = '';

            //start room
            if($checkin !=""  &&   $checkout !=""){

            $pgtype = 'withdate';



            $guests = 2; //Session()->get('guest');
            $rooms = 1;//Session()->get('rooms');

            $stchin = $checkin;
            $checkout = $checkout;

            $cmbdate = $checkin.'_'.$checkout;


            $checkin =  $checkin;

            //new code start
            $checkinDate = $checkin;
            $checkoutDate = $checkout;
            $adultsCount = 2; //$guests;
            $customerIP = '49.156.89.145';
            $childrenCount = '1';
            $chid_age = '10';
            $lang = 'en';
            $currency ='USD';
            $waitForResult ='0';
            $iata=$hotelid;

            $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869";
            $TRAVEL_PAYOUT_MARKER = "299178";



            $SignatureString = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$adultsCount.":".
            $checkinDate.":".
            $checkoutDate.":".
            $chid_age.":".
            $childrenCount.":".
            $currency.":".
            $customerIP.":".
            $iata.":".
            $lang.":".
            $waitForResult;


            $signature = md5($SignatureString);
            //  $signature = '3193e161e98200459185e43dd7802c2c';

            $url ='http://engine.hotellook.com/api/v2/search/start.json?hotelId='.$iata.'&checkIn='. $checkinDate.'&checkOut='.$checkoutDate.'&adultsCount='.$adultsCount.'&customerIP='.$customerIP.'&childrenCount='.$childrenCount.'&childAge1='.$chid_age.'&lang='.$lang.'&currency='.$currency.'&waitForResult='.$waitForResult.'&marker=299178&signature='.$signature;



            $response = Http::withoutVerifying()->get($url);

            if ($response->successful()) {
               $data = json_decode($response);
                if(!empty($data)){
                $searchId = $data->searchId;


                $limit =10;
                $offset=0;
                $roomsCount=10;
                $sortAsc=1;
                $sortBy='price';

                $SignatureString2 = "". $TRAVEL_PAYOUT_TOKEN .":".$TRAVEL_PAYOUT_MARKER.":".$limit.":".$offset.":".$roomsCount.":".$searchId.":".$sortAsc.":".$sortBy;
                $sig2 =  md5($SignatureString2);

                $url2 = 'http://engine.hotellook.com/api/v2/search/getResult.json?searchId='.$searchId.'&limit=10&sortBy=price&sortAsc=1&roomsCount=10&offset=0&marker=299178&signature='.$sig2;
            // $response2 = Http::withoutVerifying()->get($url2);

                $maxAttempts = 4;
                $retryInterval = 1; // seconds
                $response2 = Http::withoutVerifying()
                ->timeout(0) // Maximum time for an individual request
                ->retry($maxAttempts, $retryInterval)
                ->get($url2);
                $jsonResponse = json_decode($response2, true);

                //new code 1

                if ($jsonResponse['status'] == 'ok') {
                    $hotels = $jsonResponse['result'];
                    $roomsdsc = [];


                    //new code
                    foreach ($hotels as $hotel) {
                        if (isset($hotel['rooms']) && is_array($hotel['rooms'])) {
                            foreach ($hotel['rooms'] as $room) {
                                $includeRoom = false;

                                foreach ($value as $values) {
                                    if (isset($room['options'][$values]) && $room['options'][$values] === true) {
                                        $includeRoom = true;
                                        break; // Exit the innermost foreach loop once a match is found
                                    }
                                }

                                if ($includeRoom) {
                                    $roomsdsc[] = [
                                        'options' => $room['options'],
                                        'desc' => $room['desc'],
                                        'price' => $room['price'],
                                        'bookingURL' => $room['bookingURL'],
                                        'fullBookingURL' => $room['fullBookingURL'],
                                        'agencyId' => $room['agencyId'],
                                        'agencyName' => $room['agencyName'],
                                    ];

                                    $roomName = $room['desc'];
                                    $amenities = $room['options'];
                                    $roomsData[$roomName] = $amenities;
                                    $roomsprice[$roomName] = [
                                        'agencyId' => $room['agencyId'],
                                        'price' => $room['price'],
                                        'fullBookingURL' => $room['fullBookingURL']
                                    ];
                                }
                            }
                        }
                    }


                    //end new code


        //end dd

        }

     //end new code

     if (isset($jsonResponse['errorCode']) && $jsonResponse['errorCode'] === 4) {
         $jsonResponse['data_status'] = 4;
          return   $jsonResponse;
     }

        //return val

     }else{
         return 'search id not found';
     }

 }

}


        //end

//start code
       $rooms = DB::table('TPRoomtype')
           ->where('hotelid',$hotelid)
           ->get();

        $Roomdesc =  $rooms[0]->Roomdesc;

        $roomsData = [];
        $roomDesc = json_decode($rooms[0]->Roomdesc, true);
        if(!empty($value)){
          //  foreach ($rooms as $room) {


                foreach ($roomDesc as $key => $desc) {
                    $includeRoom = true;

                    foreach ($value as $values) {
                        // Check if each value is set and true in the description
                        if (isset($desc[$values]) && $desc[$values] == true) {
                            $roomsData[$key] = $desc;
                        }else{
                            $includeRoom = false;
                            break;
                        }
                    }

                    if ($includeRoom) {
                        $roomsData[$key] = $desc;
                    }
                }
                // return $roomsData;

        }else{
            $roomsData =  $roomDesc;
        }







       //end
       $getroomtype = collect();
       $photosByRoomType = json_decode($hoteldata[0]->photosByRoomType, true);

          if (!empty($photosByRoomType)) {
              foreach ($photosByRoomType as $key => $value) {
                  $roomtyids[] = $key;
              }

              $getroomtype = DB::table('TPRoom_types')->select('rid','type')->whereIn('rid', $roomtyids)->get();


           }

        //   return print_r($roomsprice);
       //end code

      return  view('frontend.hotel.filter_room_with_date',['TPRoomtype'=> $rooms,'searchresult'=>$hoteldata,'getroomtype'=>$getroomtype,'roomsdsc'=>$roomsData,'roomsprice'=>$roomsprice]);

    }

//end filter hotel room
   public function view_hotel_all_images(request $request){
        $hotid = $request->get('hotelid');
        $url = 'https://yasen.hotellook.com/photos/hotel_photos?id='.$hotid ;
        $response = Http::withoutVerifying()->get($url);
        $images = $response->json();
        return  view('hotel_detail_data.hoteldetail_view_all_images')->with('images',$images)->with('hotelid',$hotid);
    }
    public function gethotel_galary_image(request $request){
        $hotid = $request->get('hotelid');
        $url = 'https://yasen.hotellook.com/photos/hotel_photos?id='.$hotid ;
        $response = Http::withoutVerifying()->get($url);
        $images = $response->json();
        return  view('hotel_detail_data.hotel_galary_image')->with('images',$images)->with('hotelid',$hotid);
    }


    public function hotel_detail_nearby_city_and_sights(request $request){

        $location_id = $request->get('locationid');
        $location_slugid = $request->get('location_slugid');
        $hotellatitude = $request->get('Latitude');
        $hotellongitude = $request->get('longitude');
        $hotelid = $request->get('hotelid');
        $hname = $request->get('hname');
        $hid = $request->get('hid');
        $getlocation =  DB::table('Location')->select('Lat','Longitude')->where('slugid',$location_slugid)->get();
        $lat ="";



        $searchradius = 100;
        $nearby_city = collect();
		$nearby_Sight  = collect();
	    $city_hotel_count = [];
		$sight_hotel_count = [];
		$sight_hotel_count_grouped = [];
		$sight_hotelcount  = null;
        if(!$getlocation->isEmpty() ){
            $latitude = $getlocation[0]->Lat;
            $longitude = $getlocation[0]->Longitude;
            if ($latitude != "" && $longitude != "") {
                $nearby_city = DB::table("Location")
                    ->join('Temp_Mapping as t', 'Location.slugid', '=', 't.slugid')
                    ->select(
                        'Location.LocationId',
                        'Location.Name',
                        't.LocationId as locid','t.slugid','t.slug',
                        DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                            * cos(radians(Location.Lat))
                            * cos(radians(Location.Longitude) - radians(" . $longitude . "))
                            + sin(radians(" . $latitude . "))
                            * sin(radians(Location.Lat))) AS distance")
                    )
                    ->having('distance', '<=', $searchradius)
                    ->where('Location.slugid', '!=', $location_slugid)
                    ->orderBy('distance')
                    ->limit(10)
                    ->get();


            if (!$nearby_city->isEmpty()) {

                foreach ($nearby_city as $value) {
                    $hotel_count = DB::table('TPHotel')->where('location_id', $value->locid)->count();
                    $city_hotel_count[] = [
                        'city_name' => $value->Name,
                        'hotel_count' => $hotel_count,
                        'slug'=>'ho-'.$value->slugid.'-'.$value->slug,

                    ];
                }
            }

            //get location
            $searchradius =50;
            $nearby_Sight = DB::table("Sight")
                ->join('Temp_Mapping as t', 'Sight.LocationId', '=', 't.tid')
                ->join('Category as c', 'c.CategoryId', '=', 'Sight.CategoryId')
                ->select(
                    'Sight.LocationId',
                    'Sight.Title','t.slugid','t.slug',
                    't.LocationId as locid', 'c.Title as ctitle','Sight.Latitude','Sight.Longitude',
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                        * cos(radians(Sight.Latitude))
                        * cos(radians(Sight.Longitude) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . "))
                        * sin(radians(Sight.Latitude))) AS distance")
                )
                ->having('distance', '<=', $searchradius)
                ->orWhere('Sight.IsMustSee', '=', 1)

                ->orderBy('distance')
                ->limit(10)
                ->get();

		  }


        if (!$nearby_Sight->isEmpty()) {

            foreach ($nearby_Sight as $val) {
                $slat = $val->Latitude;
                $slongitude = $val->Longitude;

                $searchradius =10;
                $sight_hotelcount = DB::table("TPHotel as h")
                ->select(DB::raw("6371 * acos(cos(radians(" . $slat . "))
                    * cos(radians(h.Latitude))
                    * cos(radians(h.longnitude) - radians(" . $slongitude . "))
                    + sin(radians(" . $slat . "))
                    * sin(radians(h.Latitude))) AS distance"))
                ->having('distance', '<=', $searchradius)
                ->count();

            $sight_hotel_count[] = [
                'category' => $val->ctitle,
                'Title' => $val->Title,
                'hotelcount' => $sight_hotelcount,
                'slug'=>'ho-'.$val->slugid.'-'.$val->slug,
            ];
            }


            foreach ($sight_hotel_count as $sightval) {
                $sight_hotel_count_grouped[$sightval['category']][] = $sightval;
            }


        }


        }


       return view('frontend.hotel.where_to_stay',['sight_hotel_count'=>$sight_hotel_count,'city_hotel_count'=>$city_hotel_count,'sight_hotel_count_grouped'=>$sight_hotel_count_grouped,'hname'=>$hname]);


    }

    public function stays()
    {

          $searchresults = DB::table('UniqueHotel as TPHotel')
          ->leftJoin('TPHotel_types as ty', 'ty.hid', '=', 'TPHotel.propertyType')
          ->select('TPHotel.hotelid', 'TPHotel.id', 'TPHotel.name', 'TPHotel.slug', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.rating', 'TPHotel.amenities', 'TPHotel.distance', 'TPHotel.image', 'ty.type as propertyType','TPHotel.slugid','TPHotel.CityName as cityName','TPHotel.room_aminities')
		 ->whereNotNull('TPHotel.slugid')
		  ->inRandomOrder()
           ->get();

         $locationIds = "";
         $hotels ="";
         $citiesWithHotels="";



          // Inspect the data



        $type = "hotel";
        return view('stays')->with('searchresults',$searchresults)->with('citiesWithHotels',$citiesWithHotels)->with('locationIds',$locationIds);
    }
    public function stayslocdata(request $request)
    {


        $locationIds = [ 786371, 740257, 750174, 682061,  837452, 697010, 820094,
          665939, 840749, 797023, 683796, 763119, 845571,837009,668729
       ];


          $cities = DB::table(DB::raw('(SELECT DISTINCT l.Name, l.slugid, l.Slug, l.LocationId
            FROM Location as l
            JOIN TPHotel as h ON h.slugid = l.slugid) AS subquery'))
            ->select('subquery.Name', 'subquery.slugid', 'subquery.Slug')
              ->whereIn('subquery.LocationId', $locationIds)
            ->get();

            $locationIds = $cities->pluck('slugid');
		     $hotels = DB::table('TPHotel')
           ->select('name', 'slug', 'pricefrom', 'slugid', 'id')
            ->whereIn('slugid', $locationIds)
            ->get();


        $citiesWithHotels = $cities->map(function($city) use ($hotels) {
            $city->hotels = $hotels->filter(function($hotel) use ($city) {
                return $hotel->slugid == $city->slugid;
            })->take(10);

            return $city;
        });

        return view('stays_location_data')->with('citiesWithHotels',$citiesWithHotels);
    }


public function storeHighlightReview(Request $request)
{
    try {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'highlight_review' => 'required|string|max:65535', // TEXT field max length

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
		$randomUser = DB::table('UsersNames')->inRandomOrder()->first();
        // Store the review
        $tip = new Tips();
        $tip->username = $randomUser->GivenName . ' ' . $randomUser->Surname;
        $tip->review = $request->highlight_review;
        $tip->SightId = $request->sight_id;
		$tip->hotelid = $request->hotel_id;
        $tip->save();

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $tip
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error submitting review',
            'error' => $e->getMessage()
        ], 500);
    }
}
    //sight listing search

    public function searchsightlisting(Request $request)
    {
        if ($request->has('val')) {
            $searchText = $request->input('val');
            $locId = $request->input('locId');


            $type = 'attraction';
            $result = DB::table('Sight')
                ->where('Sight.LocationId', $locId)
                ->where(function ($query) use ($searchText) {
                    $query->where('Sight.Title', 'LIKE', $searchText . '%');
                })
                ->select('Sight.SightId as id', 'Sight.Title as displayname')
                ->limit(5)
                ->get();
            if(empty($result) && $searchText =="must see"){
                $result[] = [
                    'id' => '1',
                    'displayname' =>"Must See",
                    'type' => "Category"
                ];
            }
            if ($result->isEmpty()) {
                $type = 'Category';
                $result = DB::table('Sight')
                    ->leftJoin('Category', 'Sight.categoryId', '=', 'Category.categoryId')
                    ->join('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                    ->where('Sight.LocationId', $locId)
                    ->where(function ($query) use ($searchText) {
                        $query->where('Category.Title', 'LIKE', $searchText . '%');
                    })
				    ->distinct()
                    ->select('Category.categoryId as id', 'Category.Title as displayname')
                    ->limit(5)
                    ->get();
            }
    //  return print_r($result);
            if ($result->isEmpty()) {
                $type = 'Restaurant';
                $result = DB::table('Restaurant as r')
                ->join('Location','Location.slugid','=','r.slugid')
                ->where('Location.LocationId', $locId)
               ->where(function ($query) use ($searchText) {
                     $query->where('r.Title', 'LIKE','%'. $searchText . '%') ;
                })
			    ->distinct()
                ->select('r.RestaurantId  as id','r.Title as displayname')
                ->limit(5)
                ->get();

            }
            if ($result->isEmpty()) {
                $type = 'Restaurant';
                $result = DB::table('Restaurant as r')
                ->join('Location','Location.slugid','=','r.slugid')
                ->where('Location.LocationId', $locId)
               ->where(function ($query) use ($searchText) {
                     $query->where('r.About', 'LIKE','%'. $searchText . '%') ;
                })
                ->select('r.RestaurantId  as id','r.Title as displayname')
                ->limit(5)
                ->get();

            }
            if ($result->isEmpty()) {
                $type = 'Restaurant';
                $result = DB::table('Restaurant as r')
                    ->join('Location', 'Location.slugid', '=', 'r.slugid')
                    ->join('RestaurantCuisineAssociation as ra', 'ra.RestaurantId', '=', 'r.RestaurantId')
                    ->join('RestaurantCuisine as rc', 'ra.RestaurantCuisineId', '=', 'rc.RestaurantCuisineId')
                    ->where('Location.LocationId', $locId)
                    ->where(function ($query) use ($searchText) {
                        $query->where('rc.Name', 'LIKE', '%' . $searchText . '%');
                    })
                    ->select('r.RestaurantId as id', 'r.Title as displayname')
                    ->limit(5)
                    ->get();
            }



            $response = [];

            if (!$result->isEmpty()) {
                foreach ($result as $loc) {
                    $response[] = [
                        'id' => $loc->id,
                        'value' => $loc->displayname,
                        'type' => $type
                    ];
                }
            } elseif ($searchText == "must see") {

                $response[] = [
                    'id' => '1',
                    'value' => "Must See",
                    'type' => "Category"
                ];
            } else {
                $response[] = ['value' => "Result not found"];
            }

            return response()->json($response);
        }

        // Optionally, return an empty response if no 'val' is provided
       // return response()->json([]);
    }

    public function sightlistinghistory(Request $request)
    {
        $recentSearches = session()->get('recent_sightlist_searches', []);



            $recentSearches = array_unique($recentSearches, SORT_REGULAR);
            $recentSearches = array_slice($recentSearches, 0, 4);
            session(['recent_sightlist_searches' => $recentSearches]);
            $response = [];

        if (Session::has('recent_sightlist_searches')) {
            $recentSearches = session('recent_sightlist_searches');
            foreach ($recentSearches as $value) {
                $response[] = [
                    'id' => $value['locationId'],
                    'value' => $value['val'],
                    'type' => $value['type'],
                ];
            }
        }else{

                $response[] = [
                    'id' => '',
                    'value' => 'Restaurant',
                    'type' => '',
                    'id' => '',
                    'value' => 'Experience',
                    'type' => '',
                ];

        }

        return response()->json($response);
    }

        private function _calculatePricingStatistics($locationSlugId)
    {
        $cacheKey = 'pricing_stats_' . $locationSlugId;
        $cacheDuration = 3600; // Cache for 1 hour

        return Cache::remember($cacheKey, $cacheDuration, function () use ($locationSlugId) {
            $baseQuery = DB::table('TPRoomtype_tmp as r')
                ->join('TPHotel as h', 'r.hotelid', '=', 'h.hotelid')
                ->where('h.slugid', $locationSlugId)
                ->whereNotNull('r.price')
                ->where('r.price', '>', 0);

            $stats = [];

            // Total Hotels
            $stats['totalHotels'] = DB::table('TPHotel')->where('slugid', $locationSlugId)->count();

            // Overall stats
            $overallStats = (clone $baseQuery)->select(
                DB::raw('AVG(r.price) as avgPrice'),
                DB::raw('MIN(r.price) as minPrice'),
                DB::raw('MAX(r.price) as maxPrice')
            )->first();

            $stats['overall'] = [
                'avgPrice' => $overallStats->avgPrice ?? 0,
                'minPrice' => $overallStats->minPrice ?? 0,
                'maxPrice' => $overallStats->maxPrice ?? 0,
            ];

            if ($overallStats && $overallStats->avgPrice) {
                $stats['averageNightlyRate'] = [
                    'min' => round($overallStats->avgPrice * 0.8),
                    'max' => round($overallStats->avgPrice * 1.2)
                ];
                $stats['priceRange'] = [
                    'min' => $overallStats->minPrice,
                    'max' => $overallStats->maxPrice
                ];
            } else {
                $stats['averageNightlyRate'] = ['min' => 0, 'max' => 0];
                $stats['priceRange'] = ['min' => 0, 'max' => 0];
            }

            // Stats by star rating
            $statsByRating = [];
            for ($stars = 5; $stars >= 1; $stars--) {
                $ratingStats = (clone $baseQuery)
                    ->where('h.stars', $stars)
                    ->select(
                        DB::raw('AVG(r.price) as avgPrice')
                    )->first();

                if ($ratingStats && $ratingStats->avgPrice) {
                    $statsByRating[$stars] = [
                        'averageRate' => [
                            'min' => round($ratingStats->avgPrice * 0.9),
                            'max' => round($ratingStats->avgPrice * 1.1)
                        ],
                    ];
                } else {
                     $statsByRating[$stars] = [
                        'averageRate' => ['min' => 0, 'max' => 0],
                    ];
                }
            }
            $stats['byStarRating'] = $statsByRating;

            return $stats;
        });
    }

}
