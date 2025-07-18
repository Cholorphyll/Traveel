<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Http;



class HOSegmentController extends Controller
{

	public function formatDate($date) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        } else {
            $date = str_replace('-', ' ', $date);
            return Carbon::createFromFormat('d M Y', $date)->format('Y-m-d');
        }
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
            
                $neabyhotelwithswimingpool = Cache::remember("nearby_hotels_{$slgid}", 86400, function() use ($slgid) {
                    return DB::table('TPHotel as h')
                        ->select('h.name', 'h.location_id', 'h.id', 'h.hotelid', 'h.slugid', 'h.slug', 
                                 'h.OverviewShortDesc','h.rating','h.pricefrom','l.Name as Lname')
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

    // Calculate pricing statistics for a location
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

    // Get hotel amenities for a location
    private function getHotelAmenities($locationId) 
    {
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

    // Get hotel neighborhoods for a location
    private function getHotelNeighborhoods($locationId) 
    {
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
    // Get popular neighborhoods for a location
    private function getPopularNeighborhoods($locationId) 
    {
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
    // Get nearby sights for a location
    private function getNearbySights($locationId) 
    {
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

    private function getAmenityInfo($amenity_ids)
    {
        return DB::table('TPHotel_amenities')
            ->select('id', 'name', 'shortName', 'slug')
            ->whereIn('id', $amenity_ids)
            ->get();
    }

public function insert_hotel_desction(request $request)
 {
    
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
    
}
