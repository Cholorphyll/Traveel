<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Http;


class HotelDetailController extends Controller
{
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
      // cache data store start
		
    // cache data store end
       $searchresults = Cache::remember("hotel_details_" . $hotelid, 60, function () use ($hotelid) {
            return DB::table('TPHotel as h')
                ->select('h.name','h.location_id','h.id','h.hotelid','h.metaTagTitle','h.MetaTagDescription','h.Latitude','h.longnitude','h.stars','h.address','h.pricefrom','h.about','h.location_score','h.room_aminities',
                 'h.amenities','h.shortFacilities','h.Phone','h.Also_known_as','h.knownfor','h.CityName','h.AmenitiesRest','h.PlaceKnownFor','h.PriceRange','h.Moreinfo','h.PopularRoomTypes','h.OverviewShortDesc','h.People_also_search','h.CheckIn_Policy','h.Damage_Policy','h.Children_Policy','h.Beds_Policy','h.Age_Policy','h.Payment_Policy','h.Curfew_Policy','h.Parties_Policy','h.QuiteHours_Policy','h.Groups_Policy','h.Email','h.Website','h.Smoking_Policy', 'h.photoCount','h.Highlights','h.cntRooms','h.Languages','h.maxprice','h.minprice','h.checkIn','h.checkOut','h.CityName','l.cityName'   ,'h.photosByRoomType','h.propertyTypeId','h.GreatForScore','h.GreatFor','h.facilities','h.rating','h.ratingcount','h.photoCount','h.ReviewSummary','h.ReviewSummaryLabel','h.Spotlights','h.ThingstoKnow','h.slugid','h.slug')
                ->leftJoin('TPLocations as l', 'l.id', '=', 'h.location_id')
                ->where('h.id', $hotelid)
                ->get()->toArray();
        });
		
    	
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
        // Optimize and cache amenities and facilities queries
        $shortFacilities = collect();
        $facilityNames = collect();
        $groupedFacilities = [];
        $amenitiesArray = [];
        $highlightWords = [];
        if (!empty($searchresults) && isset($searchresults[0]->shortFacilities)) {
            $shortfacilityIds = array_filter(explode(',', $searchresults[0]->shortFacilities));
            $shortFacilities = Cache::remember("short_facilities_" . $searchresults[0]->id, 3600, function() use ($shortfacilityIds) {
                return DB::table('TPHotel_amenities')->whereIn('id', $shortfacilityIds)->get();
            });
        }

        if (!empty($searchresults) && isset($searchresults[0]->Highlights) && !empty($searchresults[0]->Highlights)) {
            $highlightsRaw = $searchresults[0]->Highlights;
            $highlightsRaw = str_replace(["'", "[", "]"], ['"', "[", "]"], $highlightsRaw);
            $highlightsArray = json_decode($highlightsRaw, true);
            if (is_array($highlightsArray) && count($highlightsArray) > 0) {
                shuffle($highlightsArray);
                $highlightWords = array_slice($highlightsArray, 0, 40);
            }
        }

        if (!empty($searchresults) && isset($searchresults[0]->facilities)) {
            $facilityIds = array_filter(explode(',', $searchresults[0]->facilities));
            $facilityNames = Cache::remember("facility_names_" . $searchresults[0]->id, 3600, function() use ($facilityIds) {
                return DB::table('TPHotel_amenities')->whereIn('id', $facilityIds)->get();
            });
            foreach ($facilityNames as $facility) {
                $groupedFacilities[$facility->groupName][] = $facility->name;
            }
            foreach ($groupedFacilities as $groupName => $facilities) {
                if (!empty($facilities)) {
                    $amenitiesArray[$groupName] = $facilities[0];
                }
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

      $TPRoomtype1 = Cache::remember("room_types_" . $hotid, 60, function () use ($hotid) {
          return DB::table('TPRoomtype_tmp')->select('*')
              ->where('hotelid',$hotid)->get()->toArray();
      });

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
    
    // Get nearby transportation
    public function getNearbyTransportation(Request $request)
    {
      $hotelid = $request->input('hotelid');
      
      if (!$hotelid) {
          return response()->json(['error' => 'Hotel ID is required'], 400);
      }
      
      try {
          // First try to find the hotel by hotelid to get internal ID
        //   $hotel = DB::table('TPHotel')
        //       ->where('hotelid', $hotelid)
        //       ->select('id')
        //       ->first();
          
        //   if ($hotel) {
        //       // Found by hotelid, use the internal id
        //       $internalId = $hotel->id;
        //   } else {
        //       // If not found by hotelid, try using the ID directly
        //       $internalId = $hotelid;
        //   }
          
        //   // Database connection details
        //   $connection = config('database.default');
        //   $database = config("database.connections.$connection.database");
        //   $table = 'LIQNearby';
          
        //   // Check if table exists
        //   $tableExists = \DB::select("SHOW TABLES LIKE '$table'");
        //   if (empty($tableExists)) {
        //       return response()->json([
        //           'error' => 'Transportation data not available',
        //           'debug' => [
        //               'table_exists' => false,
        //               'table' => $table,
        //               'database' => $database
        //           ]
        //       ], 404);
        //   }
          
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
    
    // Get hotel amenities
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

    // Get hotel neighborhoods
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
    // Get popular neighborhoods
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

    // Get nearby sights
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
    
}
