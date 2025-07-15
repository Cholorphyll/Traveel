<?php

namespace App\Http\Controllers;
use App\Models\Hotel;
use App\Models\TPHotel;
use App\Models\HotelQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class HotelsController extends Controller
{
    public function index()
    {
        return view('hotels');
    }
    public function filter_hotel(request $request){
        $val =  $request->get('value');

            $getlisting = DB::table('TPHotel')
            ->select('TPHotel.*')
                ->where(function ($query) use ($val) {
                        $query->where('TPHotel.id', '=', $val)
                        ->orWhere('TPHotel.name', '=', $val);

                  if (strpos($val, '-') !== false) {
                      $urlParts = explode('-', $val);
                      $id = isset($urlParts[2]) ? $urlParts[2] : null;

                      error_log('Extracted ID: ' . $id);

                      if ($id) {
                        $query->orWhere('TPHotel.id', $id);
                      }
                  }
              })->limit(2)
              ->get();

            return view('hotels.filter_hotel',['hotellisting'=>$getlisting]);
    }


    public function searchForm()
    {
        return view('hotels'); // Return the main hotels view with the search form
    }

    public function search(Request $request)
    {
		 // Log all data from the request
    Log::info('Request Data:', $request->all());
        // Validate the input
        $val =  $request->get('value');
        $getlisting  = DB::table('TPHotel')
        ->select('TPHotel.*')
        ->where(function ($query) use ($val) {
            $query->where('TPHotel.id', '=', $val)
                  ->orWhere('TPHotel.name', '=', $val);

            if (strpos($val, '-') !== false) {
                $urlParts = explode('-', $val);
                $id = isset($urlParts[2]) ? $urlParts[2] : null;

                error_log('Extracted ID: ' . $id);

                if ($id) {
                    $query->orWhere('TPHotel.id', $id);
                }
            }
        })->limit(2)
        ->get();
        // Return the results to the view
        return view('hotels', compact('hotels')); // Ensure you have a view to display results
    }

 public function edit_hotel($id){
        $getcountry = DB::table('Country')->get();
        $TPHotel_types = DB::table('TPHotel_types')->get();

        $gethotel = DB::table('TPHotel')
        ->Leftjoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
        ->select('TPHotel.*', 'TPLocations.countryName','TPLocations.cityName as Lname')
        ->where('TPHotel.id',$id)
        ->get();
        $location_id =$gethotel[0]->location_id;
        $CtName ="";
        if(!empty($gethotel)){
            $CtName = $gethotel[0]->countryName;
        }
        $getHotelCountry = DB::table('Country')->where('Name',$CtName)->get();
        $gettemp = DB::table('Temp_Mapping')->select('Tid')->where('LocationId',$location_id)->get();
        $Tid ="";
        if(!$gettemp->isEmpty()){
            $Tid = $gettemp[0]->Tid;
        }
        $Neighborhoods = collect();

        if($Tid != ""){
             $Neighborhoods = DB::table('Neighborhood')->where('LocationID',$Tid)->get();
        //  return   print_r($Neighborhoods);
        }else{
            $Neighborhoods = DB::table('Neighborhood')->get();
        }


         $getfaq = DB::table('HotelQuestion')
        ->leftJoin('TPHotel', 'HotelQuestion.HotelId', '=', 'TPHotel.hotelid')

        ->select('HotelQuestion.*', 'TPHotel.name')
        ->where('HotelQuestion.HotelId', $id)
        ->get();


        $gethid = DB::table('TPHotel')->where('hotelid',$id)->get();
      //  $hotel_id = $gethid[0]->hotelid;
       // $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->where('IsApprove',0)->get();

        $getreviews = DB::table('HotelReview')
    ->where('HotelId', $id)
    ->where('ReviewType', 'Regular') // Filter for anonymous messages
    ->get();

        $anonymousMessages = DB::table('HotelReview')
    ->where('HotelId', $id)
    ->where('ReviewType', 'Anonymous') // Filter for anonymous messages
    ->get();


        return view('hotels.edit_hotel',['gethotel'=>$gethotel,  'anonymousMessages' => $anonymousMessages,'id' => $id,'getfaq'=>$getfaq,'getreviews'=>$getreviews,'gethid'=>$gethid, 'country'=>$getcountry,'getHotelCountry'=>$getHotelCountry,'neighborhoodlist'=>$Neighborhoods,'TPHotel_types'=>$TPHotel_types]);
    }
    public function searchCity(request $request){
        $search = $request->get('val');

        $result = array();

        $query = DB::table('TPLocations')
            ->Leftjoin('Country', 'TPLocations.countryName', '=', 'Country.Name')
            ->select('TPLocations.locationId','TPLocations.cityName as lname','TPLocations.countryName','Country.CountryId')
            ->where('TPLocations.cityName', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();

        foreach ($query as $loc) {
            $result[] = [
                'id' => $loc->locationId,
                'value' => $loc->lname,
                'country' => $loc->countryName,
                'countryid' => $loc->CountryId
            ];
        }

        return response()->json($result);
    }

    public function updateHotel(request $request,$id){


        $request->validate([
            'hotel_name' => 'required',
            'slug' => 'required',
			'phone' => ['nullable', 'regex:/^\+?[0-9\s\(\)-]+$/'],
        ]);

         $city = $request->get('ctname');
        $county = $request->get('country');
        $ct =  $city.', '. $county;
		$c = 0;
        if( $city  != ""){

            $getct = DB::table('TPLocations')->where('fullName',$ct)->get();
			if(!$getct->isEmpty()){
			$c = 1;
			   $cityId = $getct[0]->id;
               $iata = $getct[0]->iata;
			}

        }


        $highlightsInput = $request->get('Highlights');

// Check if input is already a valid JSON string
$decodedHighlights = json_decode($highlightsInput, true);
if (json_last_error() === JSON_ERROR_NONE && is_array($decodedHighlights)) {
    $highlightsJson = json_encode($decodedHighlights); // already JSON
} else {
    // Convert comma-separated string to JSON array
    $highlightsArray = array_map('trim', explode(',', $highlightsInput));
    $highlightsJson = json_encode($highlightsArray);
}


        $stationNames = $request->input('station_name');
        $times = $request->input('time');
        $durations = $request->input('duration');

        $nearest_station = [];

        // Loop through the input arrays
        for ($i = 0; $i < count($stationNames); $i++) {
            $nearest_station[] = [
                'station_name' => $stationNames[$i],
                'time' => $times[$i],
                'duration' => $durations[$i]
            ];
        }

        $jsonData = json_encode($nearest_station);

        $addressline1 = $request->get('addressline1', '');
$addressline2 = $request->get('addressline2', '');

$address = '';
if (!empty($addressline1) && !empty($addressline2)) {
    $address = $addressline1 . ', ' . $addressline2;
} elseif (!empty($addressline1)) {
    $address = $addressline1; // Only addressline1 is present
} elseif (!empty($addressline2)) {
    $address = $addressline2; // Only addressline2 is present
}
  $phone = $request->get('phone');
    if ($phone) {
        $phone = preg_replace('/^\+/', '', $phone); // Remove the "+" at the start if it exists
    }
$existingHotel = DB::table('TPHotel')->where('id', $id)->first();

    // Merge new and old amenities
$newAmenities = $request->get('amenities');
$oldAmenities = $existingHotel->facilities;

if (!empty($newAmenities)) {
    $newList = array_map('trim', explode(',', $newAmenities));
    $oldList = !empty($oldAmenities) ? array_map('trim', explode(',', $oldAmenities)) : [];
    $mergedAmenities = implode(',', array_unique(array_merge($oldList, $newList)));
} else {
    $mergedAmenities = $oldAmenities; // retain existing if nothing is passed
}

    $newRoomAminities = $request->get('room_aminities');
$oldRoomAminities = $existingHotel->room_aminities;

if (!empty($newRoomAminities)) {
    $newRoomList = array_map('trim', explode(',', $newRoomAminities));
    $oldRoomList = !empty($oldRoomAminities) ? array_map('trim', explode(',', $oldRoomAminities)) : [];
    $mergedRoomAminities = implode(',', array_unique(array_merge($oldRoomList, $newRoomList)));
} else {
    $mergedRoomAminities = $oldRoomAminities;
}

        $data = array(
            'name'=>$request->get('hotel_name'),
            'slug'=>$request->get('slug'),
            'metaTagTitle'=>$request->get('MetaTagTitle'),
            'MetaTagDescription'=>$request->get('MetaTagDescription'),
            'address'=>$address,
            'Spotlights'=>$request->get('Spotlights'),
            'ThingstoKnow'=>$request->get('ThingstoKnow'),
            'checkIn'=>$request->get('checkIn'),
            'checkOut'=>$request->get('checkOut'),
            'Highlights' => $highlightsJson,
           // 'checkOut'=>$request->get('checkOut'),
            'Pincode'=>$request->get('pincode'),
            'Latitude'=>$request->get('Latitude'),
            'longnitude'=>$request->get('Longitude'),
            'Website'=>$request->get('website'),
            'Phone'=>$phone,
            'Email'=>$request->get('email'),
            'about'=>$request->get('about'),
 			'short_description'=>$request->get('short_description'),
            'stars'=>$request->get('stars'),
            'pricefrom'=>$request->get('pricefrom'),
            'propertyType'=>$request->get('propertyType'),
            'facilities' => $mergedAmenities,
            'shortFacilities'=>$request->get('shortFacilities'),
            'Languages'=>$request->get('Languages'),
            'room_aminities' => $mergedRoomAminities,
            'location_score'=>$request->get('location_score'),


            'NearestStations'=> $jsonData,

            'dt_created'=>now(),

        );
        if ($c == 1 && $request->has('LocationId')) {
    $data['location_id'] = $request->get('LocationId');
    $data['iata'] = $iata;
}

        DB::table('TPHotel')->where('id',$id)->update($data);
     //   return $request->get('neighborhood');

        if ($request->has('faqId')) {
            $faqIds = $request->get('faqId');
            $questions = $request->get('question');
            $answers = $request->get('answer');

            for ($i = 0; $i < count($faqIds); $i++) {
                $faqData = array(
                    'Question' => $questions[$i],
                    'Answer' => $answers[$i],
                    'IsActive' => 1,
                    'CreatedDate' => now(),
                );

                DB::table('HotelQuestion')->where('hotelQuestionId', $faqIds[$i])->update($faqData);
            }
        }

        if ($request->has('faqId')) {
            $faqIds = $request->get('faqId');
            $questions = $request->get('question');
            $answers = $request->get('answer');

            for ($i = 0; $i < count($faqIds); $i++) {
                $faqData = [
                    'Question' => $questions[$i],
                    'Answer' => $answers[$i],
                    'IsActive' => 1,
                    'CreatedDate' => now(),
                ];

                DB::table('HotelQuestion')->where('hotelQuestionId', $faqIds[$i])->update($faqData);
            }
        }

        if ($request->has('new_question') && $request->has('new_answer')) {
            $newQuestions = $request->get('new_question');
            $newAnswers = $request->get('new_answer');

            for ($i = 0; $i < count($newQuestions); $i++) {
                if (!empty($newQuestions[$i]) && !empty($newAnswers[$i])) {
                    $faqData = [
                        'HotelId' => $id, // Ensure HotelId is set
                        'Question' => $newQuestions[$i],
                        'Answer' => $newAnswers[$i],
                        'IsActive' => 1,
                        'CreatedDate' => now(),
                    ];

                    DB::table('HotelQuestion')->insert($faqData);
                }
            }
        }

  // Handle new reviews
  if ($request->has('new_review') && $request->has('new_rating')) {
    $newReviews = $request->get('new_review');
    $newRatings = $request->get('new_rating');

    for ($i = 0; $i < count($newReviews); $i++) {
        if (!empty($newReviews[$i]) && !empty($newRatings[$i])) {
            // Fetch a random user
            $randomUser = DB::table('UsersNames')->inRandomOrder()->first();
            $userName = $randomUser->GivenName . ' ' . $randomUser->Surname;
            $userEmail = $randomUser->EmailAddress;

            $reviewData = [
                'HotelId' => $id, // Add the HotelId field
                'Description' => $newReviews[$i],
                'Rating' => $newRatings[$i],
                'Name' => $userName,
                'Email' => $userEmail,
                'CreatedOn' => now(),
            ];

            DB::table('HotelReview')->insert($reviewData);
        }
    }
}

// Handle updated reviews
if ($request->has('reviewId')) {
    $reviewIds = $request->get('reviewId');
    $reviews = $request->get('review');
    $ratings = $request->get('rating');

    for ($i = 0; $i < count($reviewIds); $i++) {
        $reviewData = [
            'Description' => $reviews[$i],
            'Rating' => $ratings[$i],
            'UpdatedOn' => now(),
        ];

        DB::table('HotelReview')->where('HotelReviewId', $reviewIds[$i])->update($reviewData);
    }
}


        return redirect()->route('hotels')
        ->with('success','Hotel Updated successfully.');



    }

    public function addReview(Request $request)
    {
        $validated = $request->validate([
            'review' => 'required|string|max:5000',
            'hotelId' => 'required|integer',
        ]);

        try {
            $reviewData = [
                'HotelId' => $validated['hotelId'],
                'Description' => $validated['review'],
                'IsActive' => 1,
                'IsApprove' => 1, // Default to approved
                'CreatedOn' => now(),
            ];

            DB::table('HotelReview')->insert($reviewData);

            return response()->json(['success' => true, 'message' => 'Review added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add review. Error: ' . $e->getMessage()]);
        }
    }

  	public function add_hotel(){
        $TPHotel_types = DB::table('TPHotel_types')->get();
        $Neighborhoods = DB::table('Neighborhood')->get();


        return view('hotels.add_hotel',['neighborhoodlist'=>$Neighborhoods,'TPHotel_types'=>$TPHotel_types]);
    }

  public function storeHotel(request $request){
        $request->validate([
            'hotel_name' => 'required',
            'slug' => 'required',
			'phone' => ['nullable', 'regex:/^\+?[0-9\s\(\)-]+$/'],
        ]);

        $city = $request->get('ctname');
        $county = $request->get('country');
        $ct =  $city.', '. $county;
        $c = 0;
        if( $city  != ""){
            $getct = DB::table('TPLocations')->where('fullName',$ct)->get();
            if(!$getct->isEmpty()){
                $c = 1;
                $cityId = $getct[0]->id;
                $iata = $getct[0]->iata;
            }
        }

        $stationNames = $request->input('station_name');
        $times = $request->input('time');
        $durations = $request->input('duration');

        $nearest_station = [];

        // Loop through the input arrays
        if ($stationNames) {
            for ($i = 0; $i < count($stationNames); $i++) {
                $nearest_station[] = [
                    'station_name' => $stationNames[$i],
                    'time' => $times[$i],
                    'duration' => $durations[$i]
                ];
            }
        }

        $jsonData = json_encode($nearest_station);

        $addressline1 = $request->get('addressline1', '');
$addressline2 = $request->get('addressline2', '');

$address = '';
if (!empty($addressline1) && !empty($addressline2)) {
    $address = $addressline1 . ', ' . $addressline2;
} elseif (!empty($addressline1)) {
    $address = $addressline1; // Only addressline1 is present
} elseif (!empty($addressline2)) {
    $address = $addressline2; // Only addressline2 is present
}
	    $phone = $request->get('phone');
    if ($phone) {
        $phone = preg_replace('/^\+/', '', $phone); // Remove the "+" at the start if it exists
    }

        $data = array(
            'name'=>$request->get('hotel_name'),
            'slug'=>$request->get('slug'),
            'metaTagTitle'=>$request->get('MetaTagTitle'),
            'MetaTagDescription'=>$request->get('MetaTagDescription'),
            'address'=>$address,
            'Spotlights'=>$request->get('Spotlights'),
            'ThingstoKnow'=>$request->get('ThingstoKnow'),
            'checkIn'=>$request->get('checkIn'),
            'checkOut'=>$request->get('checkOut'),
            'Highlights'=>$request->get('Highlights'),
            'Pincode'=>$request->get('pincode'),
            'Latitude'=>$request->get('Latitude'),
            'longnitude'=>$request->get('Longitude'),
            'Website'=>$request->get('website'),
            'phone' => $phone,
            'Email'=>$request->get('email'),
            'about'=>$request->get('about'),
            'short_description'=>$request->get('short_description'),
            'stars'=>$request->get('stars'),
            'pricefrom'=>$request->get('pricefrom'),
            'propertyType'=>$request->get('propertyType'),
            'amenities'=>$request->get('amenities'),
            'shortFacilities'=>$request->get('shortFacilities'),
            'Languages'=>$request->get('Languages'),
            'room_aminities'=>$request->get('room_aminities'),
            'location_score'=>$request->get('location_score'),
            'NearestStations'=> $jsonData,
            'dt_created'=>now(),
        );

        if ($c == 1){
            $data['location_id'] = $request->get('LocationId');
            $data['iata'] = $iata;
        }

        // Insert the new hotel and get its ID
        $hotelId = DB::table('TPHotel')->insertGetId($data);

        // Handle neighborhood data if present
        if($request->get('neighborhood') != ""){
            $nid = $request->get('neighborhood');
            $getnb =  DB::table('Neighborhood')->where('NeighborhoodId',$nid)->get();
            if(!$getnb->isEmpty()) {
                $neighborhood = array(
                    'neibourhood_id' => $getnb[0]->NeighborhoodId,
                    'display_name' => $getnb[0]->Name,
                    'hotelid'=> $hotelId,
                    'location_id'=> $getnb[0]->LocationID,
                );
                DB::table('TPhotelNbData')->insert($neighborhood);
            }
        }

        // Handle new FAQs
        if ($request->has('new_question') && $request->has('new_answer')) {
            $newQuestions = $request->get('new_question');
            $newAnswers = $request->get('new_answer');

            for ($i = 0; $i < count($newQuestions); $i++) {
                if (!empty($newQuestions[$i]) && !empty($newAnswers[$i])) {
                    $faqData = [
                        'HotelId' => $hotelId,
                        'Question' => $newQuestions[$i],
                        'Answer' => $newAnswers[$i],
                        'IsActive' => 1,
                        'CreatedDate' => now(),
                    ];
                    DB::table('HotelQuestion')->insert($faqData);
                }
            }
        }

        // Handle new reviews
        if ($request->has('new_review') && $request->has('new_rating')) {
            $newReviews = $request->get('new_review');
            $newRatings = $request->get('new_rating');

            for ($i = 0; $i < count($newReviews); $i++) {
                if (!empty($newReviews[$i]) && !empty($newRatings[$i])) {
                    // Fetch a random user
                    $randomUser = DB::table('UsersNames')->inRandomOrder()->first();

                    $reviewData = [
                        'HotelId' => $hotelId,
                        'Description' => $newReviews[$i],
                        'Rating' => $newRatings[$i],
                        'Name' => $randomUser ? $randomUser->GivenName . ' ' . $randomUser->Surname : 'Anonymous',
                        'Email' => $randomUser ? $randomUser->EmailAddress : null,
                        'CreatedOn' => now(),
                    ];

                    DB::table('HotelReview')->insert($reviewData);
                }
            }
        }

        return redirect()->route('hotels')
            ->with('success', 'Hotel Added successfully.');
    }

    // manage reviews

    public function edit_review($id){
        $gethid = DB::table('TPHotel')->where('hotelid',$id)->get();
      //  $hotel_id = $gethid[0]->hotelid;
        $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->where('IsApprove',0)->get();

        return view('hotels.edit_hotel_reviews',['hotelreview'=>$getreviews,'gethid'=>$gethid]);
    }

    public function sortHotelReview(request $request){
        if($request->get('val') != ""){
            $orderby = $request->get('val');
        }else{
            $orderby = "desc";
        }
        $id = $request->get('id');

        $gethid = DB::table('TPHotel')->where('HotelId',$id)->get();

        if(!empty($request->get('filter_option'))){
            //filter with options like aprove

            $filter_option = $request->get('filter_option');
            $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->where('IsApprove',$filter_option)->orderby('HotelReviewId',$orderby)->get();
        }else{
          $filter_option = 0;
            $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->orderby('HotelReviewId',$orderby)->where('IsApprove',$filter_option)->get();
        }

       // $hotel_id = $gethid[0]->hotelid;


        return view('hotels.sortHotelReview',['hotelreview'=>$getreviews,'gethid'=>$gethid,'val'=>$filter_option]);
    }

    public function filterhotelbyid(request $request){

        $val = $request->get('val');
        $getreviews =  DB::table('HotelReview')->where(function($query) use ($val) {
            if (strlen($val) <= 1) {
                $query->where('HotelReviewId', 'LIKE', '%' . $val . '%');
            } else {
                $query->where('HotelReviewId', $val);
            }
        })
        ->limit(3)->get();
         return view('hotels.sortHotelReview',['hotelreview'=>$getreviews]);

    }
    public function ftrhotelrewview(request $request){
        $val1 = $request->get('val');
        $id = $request->get('id');

        if (strpos($val1, ',') !== false) {
            $explodedValues = explode(',', $val1);
             $val1 =  $explodedValues[0];
             $val2 =  $explodedValues[1];
             $val3 =  $explodedValues[2];
             $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->where('IsApprove',$val1)->orWhere('IsApprove',$val2)->orWhere('IsApprove',$val3)->get();
        } else {
            $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->where('IsApprove',$val1)->get();
        }


        return view('hotels.sortHotelReview',['hotelreview'=>$getreviews,'val'=>$val1]);
    }

      public function update_hotelreview(request $request){
      $id = $request->get('id');

      $hotelid = $request->get('hotelid');
      DB::table('HotelReview')->where('HotelReviewId',$id)->update(['IsApprove'=>$request->get('value')]);

       $filter_option = $request->get('value');
       $gethid = DB::table('TPHotel')->where('HotelId',$hotelid)->get();
       $getreviews =  DB::table('HotelReview')->where('HotelId',$hotelid)->where('IsApprove',$filter_option)->get();

       return view('hotels.sortHotelReview',['hotelreview'=>$getreviews,'gethid'=>$gethid,'val'=>$filter_option]);
    }

    // edit hotle Faq

    public function edit_hotel_faqs($id)
    {
        $hotelfaq = DB::table('HotelQuestion')
            ->leftJoin('TPHotel', 'HotelQuestion.HotelId', '=', 'TPHotel.hotelid')
            ->select('HotelQuestion.*', 'TPHotel.name')
            ->where('HotelQuestion.HotelId', $id)
            ->get();

        // Pass data to edit_hotel.blade
        return view('hotels.edit_hotel', ['getfaq' => $hotelfaq]);
    }


    public function update_hotel_faq(Request $request){

        $id =  $request->get('faqId');
       // $currentDate = Carbon::today()->toDateString();
        $data = array(
            'Question' => $request->get('question'),
            'Answer' => $request->get('answer'),
            'IsActive' => 1,
            'CreatedDate' => now(),
        );

        return  DB::table('HotelQuestion')->where('hotelQuestionId',$id)->update($data);

    }

    public function add_hotel_faq(Request $request){
       $question = $request->get('checkboxText');
       $hotelid = $request->get('hotelid');
       $data = array(
            'Question'=>$question,
            'HotelId'=>$hotelid,
            'IsActive' => 1,
            'CreatedDate' => now(),
       );
       DB::table('HotelQuestion')->insert($data);

       $hotelfaq = DB::table('HotelQuestion')
       ->Leftjoin('TPHotel','HotelQuestion.HotelId', '=' ,'TPHotel.hotelid')
       ->select('HotelQuestion.*','TPHotel.name')
       ->where('HotelQuestion.HotelId',$hotelid)->get();
      return view('hotels.updated_faq',['getfaq'=>$hotelfaq]);
    }

    /*-------------Hotel Category--------------*/

    public function edit_hotel_category($id){

        $categoryIds = DB::table('TPHotel')
        ->where('hotelid', $id)
        ->pluck('CategoryId')
        ->toArray();
        $hname = DB::table('TPHotel')
        ->where('hotelid', $id)->get();


        $hotel_categories = [];

        foreach ($categoryIds as $categoryId) {
            $categoryTypes = DB::table('TPHotel_types')
                ->whereIn('id', explode(',', $categoryId))->get();


            // foreach ($categoryTypes as $type) {
            //     $hotel_categories[] = ['id' => $type->id, 'name' => $type->type];
            // }
        }



        return view('hotels.edit_hotel_category',['hotel_category'=>$categoryTypes,'hname'=>$hname]);
    }

     public function updateHotelCategory(Request $request){
        $categoryid =  $request->get('id');
        $hotelid =  $request->get('hotelid');
        $getcat = DB::table('TPHotel')->where('hotelid',$hotelid)->get();

        $checkcat = $getcat[0]->CategoryId;

        $query = DB::table('TPHotel')
        ->where('hotelid', $hotelid)
        ->whereRaw("FIND_IN_SET($categoryid, TRIM(BOTH ',' FROM CategoryId))")
        ->update([
            'CategoryId' => DB::raw("REPLACE(CategoryId, ',$categoryid', '')")
        ]);


           $categoryIds = DB::table('TPHotel')
           ->where('hotelid', $hotelid)
           ->pluck('CategoryId')
           ->toArray();
           $hname = DB::table('TPHotel')
           ->where('hotelid', $hotelid)->get();


           $hotel_categories = [];

           foreach ($categoryIds as $categoryId) {
               $categoryTypes = DB::table('TPHotel_types')
                   ->whereIn('id', explode(',', $categoryId))->get();


               // foreach ($categoryTypes as $type) {
               //     $hotel_categories[] = ['id' => $type->id, 'name' => $type->type];
               // }
           }



           return view('hotels.filterCategory',['hotel_category'=>$categoryTypes,'hname'=>$hname]);
    }

    public function search_category(Request $request)
    {

        $search = $request->get('val');

        $result = array();

        $query = DB::table('TPHotel_types')
            ->where('TPHotel_types.type', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();

        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->id,
                'value' => $cat->type,
            ];
        }

        return response()->json($result);
    }

	public function addhotelcat(Request $request)
	{
		$cat_type = $request->input('value');

		$getcat = DB::table('TPHotel_types')
			->where('type', $cat_type)
			->get();
	  //return print_r($getcat);
		$CategoryId = "";
		if (!$getcat->isEmpty()) {
			$CategoryId = $getcat[0]->id;
		}else{
			return 'false';
		}

		$hotelId = $request->input('id');

		$hotel = DB::table('TPHotel')->where('hotelid', $hotelId)->first();

		if ($hotel) {
			$existingCategoryIds = explode(',', $hotel->CategoryId);

			// Check if the category ID already exists in the CategoryId column
			if (!in_array($CategoryId, $existingCategoryIds)) {
				// Append the new category ID to the existing IDs
				$newCategoryIds = implode(',', array_merge($existingCategoryIds, [$CategoryId]));

				// Update the CategoryId column with the new value
				DB::table('TPHotel')
					->where('hotelid', $hotelId)
					->update(['CategoryId' => $newCategoryIds]);
			}else{
				return 2;
			}
		}


		$categoryIds = DB::table('TPHotel')
			->where('hotelid', $hotelId)
			->pluck('CategoryId')
			->toArray();

		$hname = DB::table('TPHotel')
			->where('hotelid', $hotelId)
			->get();

		$categoryTypes = [];

		foreach ($categoryIds as $categoryId) {
			$types = DB::table('TPHotel_types')
				->whereIn('id', explode(',', $categoryId))
				->get();

			//$categoryTypes = array_merge($categoryTypes, $types);
		}
		  //  return print_r($hotel);
		return view('hotels.filterCategory', ['hotel_category' => $types, 'hname' => $hname]);
	}

    //edit landing page
    public function edit_hotel_landing($id){
       $getlanding = DB::table('TPHotel_landing')
       ->Leftjoin('TPHotel','TPHotel.hotelid','=','TPHotel_landing.hotelid')
       ->select('TPHotel_landing.*','TPHotel.name','TPHotel.hotelid')
       ->where('TPHotel_landing.hotelid',$id)->get();
       return view('hotels.edit_hotel_landing',['getlanding'=>$getlanding]);
    }

    public function updateLanding(request $request){

        $landingid =  $request->get('landing');
        $value =  $request->get('value');
        $colid =  $request->get('colid');
        if($colid ==1){
            $data = array(
                'Name' => $request->get('value'),
            );
        }elseif($colid==2){
            $data = array(
                'Slug' => $request->get('value'),
            );
        }elseif($colid==3){
            $data = array(
                'MetaTagTitle' => $request->get('value'),
            );
        }elseif($colid==4){
            $data = array(
                'MetaTagDescription' => $request->get('value'),
            );
        }elseif($colid==5){
            $data = array(
                'About' => $request->get('value'),
            );
        }


        return  DB::table('TPHotel_landing')->where('id',$landingid)->update($data);

    }
    public function hidepage(Request $request){
        $landingid =  $request->get('landing');
            $data = array(
                'status' => 0,
            );

        return DB::table('TPHotel_landing')->where('id',$landingid)->update($data);
    }

    public function delete_landing(Request $request){
     $landingid =  $request->get('landing');
      return  DB::table('TPHotel_landing')->where('id',$landingid)->delete();

    }

    public function add_landing_page(){
        return view('hotels.add_landing_page');
    }

    public function search_hotel(Request $request)
    {

        $val = $request->get('val');

        $result = array();

        $query = DB::table('TPHotel')
        ->where(function ($query) use ($val) {
            $query->where(
                'hotelid', '=', '%' . $val . '%')->orWhere(
                    'name', '=', '%' . $val . '%')->orWhere(
                        'slug', '=', '%' . $val . '%');
    })
            ->limit(4)
            ->get();

        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->hotelid,
                'value' => $cat->name,
            ];
        }

        return response()->json($result);
    }
    public function search_restaurent(Request $request)
    {

        $val = $request->get('val');

        $result = array();

        $query = DB::table('Restaurant')
        ->where(function ($query) use ($val) {
            $query->where(
                'RestaurantId', '=', '%' . $val . '%')->orWhere(
                    'Title', '=', '%' . $val . '%');
        })
            ->limit(4)
            ->get();

        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->RestaurantId,
                'value' => $cat->Title,
            ];
        }

        return response()->json($result);
    }
    public function search_neighborhood(Request $request)
    {

        $val = $request->get('val');

        $result = array();

        $query = DB::table('Neighborhood')
        ->where(function ($query) use ($val) {
            $query->where(
                'NeighborhoodId', '=', '%' . $val . '%')->orWhere(
                    'Name', '=', '%' . $val . '%');
        })
            ->limit(4)
            ->get();

        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->NeighborhoodId,
                'value' => $cat->Name,
            ];
        }

        return response()->json($result);
    }


    public function search_category1(Request $request)
    {

        $search = $request->get('val');

        $result = array();

        $query = DB::table('TPHotel_types')
            ->where('TPHotel_types.type', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();

        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->id,
                'value' => $cat->type,
            ];
        }

        return response()->json($result);
    }

    public function search_hotel_amenti(Request $request){
        $val = $request->get('val');
        $hotelAmenities = [
            'Private Parking', 'Free Parking', 'Invoice provided', 'Luggage storage', 'Express check-in/check-out',
            '24-hour front desk', 'Board games/puzzles', 'Daily housekeeping', 'Ironing service', 'Dry cleaning',
            'Laundry', 'Meeting/banquet facilities', 'Fire extinguishers', 'CCTV outside property', 'CCTV in common areas',
            'Smoke alarms', 'Security alarm', 'Key card access', 'Key access', '24-hour security', 'Safety deposit box',
            'Free Wifi', 'Fitness', 'Fitness centre', 'Carbon monoxide detector', 'Shared lounge/TV area',
            'Air conditioning', 'Non-smoking throughout', 'Allergy-free room', 'Heating', 'Soundproofing',
            'Laptop safe', 'Soundproof rooms', 'Lift', 'Family rooms', 'Facilities for disabled guests',
            'Non-smoking rooms', 'Iron',
        ];

        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hotelAmenities as $amenity) {
            // Convert the amenity to lowercase for case-insensitive comparison
            $amenityLowercase = strtolower($amenity);

            // Check if the search term is found in the amenity (case-insensitive search)
            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break; // Limit the result to 4, break out of the loop
                }
            }
        }

        return response()->json($matchingAmenities);
    }


    public function get_Room_type(Request $request){
        $val = $request->get('val');
        $hotelAmenities = [
            'Non-Smoking Rooms', 'test',
        ];

        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hotelAmenities as $amenity) {
            // Convert the amenity to lowercase for case-insensitive comparison
            $amenityLowercase = strtolower($amenity);

            // Check if the search term is found in the amenity (case-insensitive search)
            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break; // Limit the result to 4, break out of the loop
                }
            }
        }

        return response()->json($matchingAmenities);
    }
    public function get_hotel_type(Request $request){
        $val = $request->get('val');
        $hoteltype = [
            'hotel-type 1', 'hotel type 2','test type 3'
        ];
        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hoteltype as $amenity) {

            $amenityLowercase = strtolower($amenity);


            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break;
                }
            }
        }

        return response()->json($matchingAmenities);
    }

    public function get_onsight_restaurant(Request $request){
        $val = $request->get('val');
        $hoteltype = [
            'restaurant value 1', 'restaurant value 2','test value 3'
        ];
        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hoteltype as $amenity) {

            $amenityLowercase = strtolower($amenity);


            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break;
                }
            }
        }

        return response()->json($matchingAmenities);
    }
    public function get_hotel_tags(Request $request){
        $val = $request->get('val');
        $hoteltype = [
            'hotel tag 1', 'hotel tag 2','hotel tag 3'
        ];
        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hoteltype as $amenity) {

            $amenityLowercase = strtolower($amenity);


            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break;
                }
            }
        }

        return response()->json($matchingAmenities);
    }
    public function get_public_transit(Request $request){
        $val = $request->get('val');
        $hoteltype = [
            'public transit 1', 'public transit 2','text transit 3'
        ];
        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hoteltype as $amenity) {

            $amenityLowercase = strtolower($amenity);


            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break;
                }
            }
        }

        return response()->json($matchingAmenities);
    }
    public function get_access(Request $request){
        $val = $request->get('val');
        $hoteltype = [
            'access 1', 'access 2','text transit 3'
        ];
        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hoteltype as $amenity) {

            $amenityLowercase = strtolower($amenity);


            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break;
                }
            }
        }

        return response()->json($matchingAmenities);
    }

/*
public function spotlightSection()
{
    // Fetch hotels where spotlight is enabled or not null (depending on your logic)
    $spotlightHotels = DB::table('TPHotel')
            ->whereNotNull('Spotlights') // Ensure Spotlights column is not null
        ->where('Spotlights', '!=', '') // Ensure Spotlights column is not an empty string
        ->select('id', 'name', 'Spotlights', 'feature1', 'feature2', 'feature3') // Select relevant columns
        ->get(); // Fetch the results

    // Pass the data to the view
    return view('hotels.spotlight', ['spotlightHotels' => $spotlightHotels]);
}
*/



    public function store_landing(request $request){

        $name = $request->name;
        $slug = $request->slug;
        $meta_title = $request->meta_title;
        $meta_desc = $request->meta_desc;
        $about = $request->about;
        $hotelId = $request->hotelId;
        $nearbytype = $request->nearbytype;
        $nearby = $request->nearby;

        // $nearbyid = "";

        // if($nearby != ""){

        //     if($nearbytype == 'Attraction'){
        //         $sight =  DB::table('Sight')->where('Title',$nearby)->get();
        //         $nearbyid = $sight[0]->SightId;
        //     }elseif($nearbytype == 'Hotel'){
        //         $hotel =  DB::table('TPHotel')->where('name',$nearby)->get();
        //         $nearbyid = $hotel[0]->id;
        //     }elseif($nearbytype == 'Restaurent'){
        //         $Restaurant =  DB::table('Restaurant')->where('Title',$nearby)->get();
        //         $nearbyid = $Restaurant[0]->RestaurantId;
        //     }elseif($nearbytype == 'Neighborhood'){
        //         $Neighborhood =  DB::table('Neighborhood')->where('Name',$nearby)->get();
        //         $nearbyid = $Neighborhood[0]->NeighborhoodId;
        //     }

        // }




        $roommntArray = json_encode($request->roommntArray);
        $ratingarray = json_encode($request->ratingarray);
        $hotelmntarray = json_encode($request->hotelmntarray);
        $HotelPricing_array = json_encode($request->HotelPricing_array);

        $room_type_array = json_encode($request->room_type_array);
        $distance_array = json_encode($request->distance_array);
        $hotelstyle_array = json_encode($request->hotelstyle_array);
        $onsiterestaurants_array = json_encode($request->onsiterestaurants_array);

        $Hotel_Tags_array = json_encode($request->Hotel_Tags_array);
        $Public_Transit_array = json_encode($request->Public_Transit_array);
        $Access_value_array = json_encode($request->Access_value_array);


       $data = array(
        'Name' => $name,
        'Slug' => $slug,
        'hotel_tags' => $Hotel_Tags_array,
        'RoomFeatures' => $name,
        'RoomType' => $room_type_array,
        'MetaTagTitle' => $meta_title,
        'MetaTagDescription' => $meta_desc,
        'About' => $about,
        'HotelAmenities' => $hotelmntarray,
        'Rating' => $ratingarray,
        'Room_Amenities' => $roommntArray,
        'Hotel_Pricing' => $HotelPricing_array,
        'Distance' => $distance_array,
        'Hotel_Style' => $hotelstyle_array,
        'OnSiteRestaurants' => $onsiterestaurants_array,
        'PublicTransitAccess' => $Public_Transit_array,
        'hotelid'=> $hotelId,
        'Nearby_Type'=>$nearbytype,
        'NearbyId'=>$nearby,
        'Access'=>$Access_value_array,

       );

       return DB::table('TPHotel_landing')->insert($data);
    }

  public function update_landingfilter(request $request){

        $hotelId = $request->hotelId;
        $id = $request->id;
        $nearbytype = $request->nearbytype;
        $nearby = $request->nearby;

        // $nearbyid = "";

        // if($nearby != ""){

        //     if($nearbytype == 'Attraction'){
        //         $sight =  DB::table('Sight')->where('Title',$nearby)->get();
        //         $nearbyid = $sight[0]->SightId;
        //     }elseif($nearbytype == 'Hotel'){
        //         $hotel =  DB::table('TPHotel')->where('name',$nearby)->get();
        //         $nearbyid = $hotel[0]->id;
        //     }elseif($nearbytype == 'Restaurent'){
        //         $Restaurant =  DB::table('Restaurant')->where('Title',$nearby)->get();
        //         $nearbyid = $Restaurant[0]->RestaurantId;
        //     }elseif($nearbytype == 'Neighborhood'){
        //         $Neighborhood =  DB::table('Neighborhood')->where('Name',$nearby)->get();
        //         $nearbyid = $Neighborhood[0]->NeighborhoodId;
        //     }

        // }

        $ratingArray = $request->ratingarray;

        if(!empty($ratingArray)){
             // Using str_replace to remove " Star" from each element in the array
            $ratingArray = array_map(function ($rating) {
                return str_replace(' Star', '', $rating);
            }, $ratingArray);

            // Now $ratingArray doesn't contain the word "Star"
        }



        $roommntArray = json_encode($request->roommntArray);
        $ratingarray = json_encode($ratingArray);
        $hotelmntarray = json_encode($request->hotelmntarray);
        $HotelPricing_array = json_encode($request->HotelPricing_array);

        $room_type_array = json_encode($request->room_type_array);
        $distance_array = json_encode($request->distance_array);
        $hotelstyle_array = json_encode($request->hotelstyle_array);
        $onsiterestaurants_array = json_encode($request->onsiterestaurants_array);

        $Hotel_Tags_array = json_encode($request->Hotel_Tags_array);
        $Public_Transit_array = json_encode($request->Public_Transit_array);
        $Access_value_array = json_encode($request->Access_value_array);
        $amenities_array = json_encode($request->amenities_array);

       $data = array(

        'hotel_tags' => $Hotel_Tags_array,
        'RoomType' => $room_type_array,
        'HotelAmenities' => $hotelmntarray,
        'Rating' => $ratingarray,
        'Room_Amenities' => $roommntArray,
        'Hotel_Pricing' => $HotelPricing_array,
        'Distance' => $distance_array,
        'Hotel_Style' => $hotelstyle_array,
        'OnSiteRestaurants' => $onsiterestaurants_array,
        'PublicTransitAccess' => $Public_Transit_array,
        'hotelid'=> $hotelId,
        'Nearby_Type'=>$nearbytype,
        'NearbyId'=>$nearby,
        'Access'=>$Access_value_array,
        'Amenities'=>$amenities_array,

       );

       return DB::table('TPHotel_landing')->where('id',$id)->update($data);
    }

	// FUNCTION FOR SPOTLIGHT

	public function spotlightSection()
{
    // Fetch hotels where spotlight is enabled or not null (depending on your logic)
    $spotlightHotels = DB::table('TPHotel')
            ->whereNotNull('Spotlights') // Ensure Spotlights column is not null
        ->where('Spotlights', '!=', '') // Ensure Spotlights column is not an empty string
        ->select('id', 'name', 'Spotlights', 'feature1', 'feature2', 'feature3') // Select relevant columns
        ->get(); // Fetch the results

    // Pass the data to the view
    return view('hotels.spotlight', ['spotlightHotels' => $spotlightHotels]);
}

	//FUNCTION FOR THINGS TO KNOW
public function showHotelDetails($hotelId) {
    // Fetch hotel details including Things to Know from the TPHotel table
    $hotel = DB::table('TPHotel')->where('hotelid', $hotelId)->first();

    // Assume 'things_to_know' is a field in TPHotel table containing the data
    $ThingstoKnow = $hotel ? explode(',', $hotel->ThingstoKnow) : [];

    return view('hotel-details', [
        'hotel' => $hotel,
        'ThingstoKnow' => $ThingstoKnow,
    ]);
}

	 public function addHotledetailFaq(request $request){
            $bhv = 0;
            $expervar = 0;
            $cheapestHOt =0;
                $hotelid = $request->get('hotelid');
                $Latitude = $request->get('Latitude');
                $longnitude = $request->get('Longitude');
                // $getloc = DB::table('TPLocations')->select('cityName')
                // ->where('id',$locationid)
                // ->get();

             //   if(!$getloc->isEmpty()){
                  //  $lname = $getloc[0]->cityName;

                    //quest 1
                    $att_bestH = DB::table('HotelQuestion')
                    ->where('HotelId', $hotelid)
                    ->where('Question', 'What Attractions are nearby?')
                    ->get();

                    if ($att_bestH->isEmpty()) {
                        $bhv = 1;

                        $searchradius = 50;
                        $nearby_sight = DB::table("Sight")
                        ->select('SightId', 'Title', 'LocationId', 'Slug',
                            DB::raw("6371 * acos(cos(radians(" . $Latitude . "))
                                * cos(radians(Sight.Latitude))
                                * cos(radians(Sight.Longitude) - radians(" . $longnitude . "))
                                + sin(radians(" . $Latitude . "))
                                * sin(radians(Sight.Latitude))) AS distance"))
                        ->groupBy("Sight.SightId")
                        ->having('distance', '<=', $searchradius)
                        ->orderBy('distance')
                        ->limit(5)
                        ->where('IsMustSee', 1)
                        ->get();


                     // return  print_r($nearby_sight);

                        $best_hotel = [];
                        if (!$nearby_sight->isEmpty()) {
                            foreach ($nearby_sight as $bh) {
                                $best_hotel[] = [
                                    'name' => $bh->Title,
                                    'url' =>  $bh->LocationId.'-'.$bh->SightId.'-'.$bh->Slug

                                ];
                            }
                        }
                        if(!empty($best_hotel)){
                            $bestharray = array(
                                'HotelId'=>$hotelid,
                                'Question'=>'What Attractions are nearby?',
                                'Answer' =>  'There are several exciting attractions near the hotel that cater to a variety of interests.',
                                'Listing'=>json_encode($best_hotel),
                                'CreatedDate' => now(),
                                'IsActive'=>1,
                            );
                           DB::table('HotelQuestion')->insert($bestharray);
                        }
                    }

                //quest 1 end





                if($bhv = 1 && $expervar = 1 &&  $cheapestHOt = 1){

                    $faq =  DB::table('HotelQuestion')->where('HotelId',$hotelid)->get();
                    $html3 = view('frontend.hotel.hotel_detail_faq',['faq'=>$faq])->render();


                    return response()->json([ 'html' => $html3]);
                }


                //end

           //    }

        }


	  //landing amenities
    public function amenties(Request $request){
        $val = $request->get('val');
        $hotelAmenities = [
            'TV',
                        'Business center',
                        'Shower',
                        'Non-smoking rooms',
                        'Restaurant',
                        'Separate shower and tub',
                        'Air conditioning',
                        'Shops',
                        'Laundry service',
                        'Bar',
                        'Sauna',
                        'Mini bar',
                        'Meeting facilities',
                        'Elevator',
                        'Bathroom',
                        '24hr room service',
                        'Internet Access',
                        'Room Service',
                        'Bathtub',
                        'Pets allowed',
                        'Disabled facilities',
                        'Balcony/terrace',
                        'Garden',
                        'Outdoor pool',
                        'Swimming Pool',
                        'Gym / Fitness Centre',
                        'Conference Facilities',
                        'Massage',
                        'Hotel/airport transfer',
                        'Kitchenette',
                        'Free parking',
                        'Car parking',
                        'Jacuzzi',
                        'Wheelchair accessible',
                        'Microwave',
                        'Inhouse movies',
                        'Babysitting',
                        'Banquet Facilities',
                        'Spa',
                        'Refrigerator',
                        'Crib available',
                        'Indoor pool',
                        'Golf course (on-site)',
                        'Tennis courts',
                        'Water sports (non-motorized)',
                        'Playground',
                        'Wi-Fi Available',
                        'Heated pool',
                        'Kids pool',
                        'Launderette',
                        'Washing machine',
                        'Table tennis',
                        'Casino',
                        'Steam Room',
                        'Rent a car in the hotel',
                        'Barbecue Area',
                        'Games Room',
                        'Video/DVD Player',
                        'Billiards',
                        'Private beach',
                        'Squash courts',
                        'Nightclub',
                        'LGBT friendly',
                        'Valet service',
                        'Horse Riding',
                        'Mini Golf',
                        'Bowling',
                        'Gift Shop',
                        'Eco Friendly',
                        'Wheelchair access',
                        'Security Guard',
                        'Children care/activities',
                        'In-house movie',
                        'Handicapped Room',
                        'Water Sports',
                        'Wi-Fi in public areas',
                        'Smoking room',
                        'Connecting rooms',
                        'English',
                        'French',
                        'Deutsch',
                        'Spanish',
                        'Arabic',
                        'Italian',
                        'Chinese',
                        'Russian',
                        'Deposit',
                        'Private Bathroom',
                        'Adults only',
        ];

        $searchTerm = strtolower($val);

        $matchingAmenities = [];
        $count = 0;
        foreach ($hotelAmenities as $amenity) {
            // Convert the amenity to lowercase for case-insensitive comparison
            $amenityLowercase = strtolower($amenity);

            // Check if the search term is found in the amenity (case-insensitive search)
            if (strpos($amenityLowercase, $searchTerm) !== false) {
                $matchingAmenities[] = ['value' => $amenity];
                $count++;

                if ($count >= 4) {
                    break; // Limit the result to 4, break out of the loop
                }
            }
        }

        return response()->json($matchingAmenities);
    }

    public function storeFaq(Request $request)
{
    $faqData = [
        'HotelId' => $request->HotelId,
        'Question' => $request->Question,
        'Answer' => $request->Answer,
        'IsActive' => 1,
        'CreatedDate' => now(),
    ];

    DB::table('HotelQuestion')->insert($faqData);

    return response()->json(['message' => 'FAQ added successfully!']);
}

}
