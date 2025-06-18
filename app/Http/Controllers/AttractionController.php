<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sight;
use Illuminate\Auth\Middleware\IsAdmin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
class AttractionController extends Controller
{
    public function index()
    {
        return view('attraction.index'); 
    }
    public function search_attracion(request $request){
        $val =  $request->get('value');
        
        $getatr = DB::table('Sight')
            ->select('Sight.*')
            ->where(function ($query) use ($val) {
                $query->where('Sight.SightId', '=', $val)
                      ->orWhere('Sight.Title', '=', $val)
                      ->orWhere('Sight.Slug', '=', $val);
                
                if (strpos($val, '-') !== false) {
                    $urlParts = explode('-', $val);
                    $id = isset($urlParts[2]) ? $urlParts[2] : null;
                    
                    error_log('Extracted ID: ' . $id);
                    
                    if ($id) {
                        $query->orWhere('Sight.SightId', $id);
                    }
                }
            })->limit(2)
            ->get();
             
        return view('attraction.filterattr',['attraction'=>$getatr]);
    }

    public function create()
    {
        $location = DB::table('Location')->select('Name','LocationId')->get();
        $country = DB::table('Country')->orderby('Name')->get();
        return view('attraction.add_sight',['country'=>$country,]);
    }
    
  public function search_city(Request $request)
    {

        $search = $request->get('val');

        $result = array();
    
        $query = DB::table('Location')
            ->join('Country', 'Location.CountryId', '=', 'Country.CountryId')
            ->select('Location.LocationId','Location.Name as lname','Country.Name as countryName','Country.CountryId')
            ->where('Location.Name', 'LIKE',  $search . '%')
            ->orWhere(DB::raw("LOWER(CONCAT(Location.Name, ' ', Country.Name))"), 'LIKE',  strtolower($search) . '%')
            ->limit(4)
            ->get();
        
        foreach ($query as $loc) {
            $result[] = [
                'id' => $loc->LocationId,
                'value' => $loc->lname,
                'country' => $loc->countryName 
            ];
        }
    
        return response()->json($result);
    }
    public function store(Request $request)
    {
       
        $request->validate([
            'sight_name' => 'required',
            'slug' => 'required',
        	'pincode' => 'nullable|regex:/^[A-Za-z0-9\s]{1,8}$/'
        ]); 

        $formData[]  = $request->get('station_name_');

        $dynamicFields = [];
        foreach ($request->all() as $key => $value) {
        if (strpos($key, 'station_name_') === 0) {
            $fieldCount = str_replace('station_name_', '', $key);
            $dynamicFields[$fieldCount]['station_name'] = $value;
        } elseif (strpos($key, 'time_') === 0) {
            $fieldCount = str_replace('time_', '', $key);
            $dynamicFields[$fieldCount]['time'] = $value;
        } elseif (strpos($key, 'duration_') === 0) {
            $fieldCount = str_replace('duration_', '', $key);
            $dynamicFields[$fieldCount]['duration'] = $value;
        }
        }

        $separator = '  '; // Define the separator to use

        // Combine the fields and store them in a single column
        
        $locId = null;
     if( $request->get('ctname') != ""){
        $locId = null;
        $ctname =  $request->get('ctname');
	//new code
		 
            $lastCommaPos = strrpos($ctname, ',');
        
            if ($lastCommaPos !== false) {
                $countryName = substr($ctname, $lastCommaPos + 1);
                $ctname = substr($ctname, 0, $lastCommaPos);
        
                $getCont  =  DB::table('Country')->where('Name',$countryName)->get();
                $ctid = $getCont[0]->CountryId;

                $locId = null;
               // $ctname =  $request->get('ctname');
              
                if($countryName != ""){
                    $getLoc =  DB::table('Location')->where('Name',$ctname)->where('CountryId',$ctid)->get();
                }else{
                    $getLoc = DB::table('Location')->where('Name',$ctname)->whereNull('CountryId')->get();
                }
                
               
     
                if($getLoc->isEmpty()){
                    return redirect()->route('add_attraction')->with('error','City not found. Please try again.');
                }else{
        
                    $locId =  $getLoc[0]->LocationId;
                }
			}
		 //end new code 
      
    }
    $sap ="";
    if(!empty($request->get('addressline2'))){
         $sap = ', ';
    }
   
        $currentDate = Carbon::today()->toDateString();
        $sight = new Sight;
        $sight->Title = $request->get('sight_name');
        $sight->Slug = Str::slug($request->get('slug'));
        $sight->MetaTagTitle=$request->get('MetaTagTitle');
        $sight->MetaTagDescription=$request->get('MetaTagDescription');
        $sight->About=$request->get('about');
        $sight->short_description=$request->get('short_description');

        $sight->Address = $request->get('addressline1').$sap.$request->get('addressline2');
        $sight->Neighbourhood = $request->get('neighborhood');

        $sight->LocationId = $locId;

        $sight->Pincode = $request->get('pincode');
        $sight->Latitude=$request->get('Latitude');
        $sight->Longitude=$request->get('Longitude');
        $sight->Website=$request->get('website');
		$sight->Likes = $request->get('likes');
        $sight->Dislikes = $request->get('dislikes');
        $sight->Phone=$request->get('phone');
        $sight->Email=$request->get('email');
        $sight->GetIn=$request->get('GetIn');
    
        if($request->get('mustisee') == 'on'){
          $mustsee = 1;
        }else{
            $mustsee = 0;
        }
        $sight->IsMustSee = $mustsee;
        $sight->IsActive = 1;
        $sight->IsLocationUpdated = 1;
        $sight->CreatedOn = $currentDate;
        $sight->save();

        $lastinsertedId = $sight->SightId;
      
        $station_name = $request->input('station_name');
        $time = $request->input('time');
        $duration = $request->input('duration');
    
         //dd($request->input('station_name'));
        // Update the nearest station data
        for ($i = 0; $i < count($station_name); $i++) {

            // DB::table('NearBy')
            //     ->insert([
            //         'SightId' =>$lastinsertedId,
            //         'NearBy' => $station_name[$i],
            //         'Time' => $time[$i], 
            //         'Duration' => $duration[$i],
            //         'CreatedOn' => $currentDate,
            //     ]);
        }
       
        /*timing */

        $selectedDaysIds = $request->input('Group', []);

        $selectedCount = count($selectedDaysIds); 
        $uncheckedCount = 7 - $selectedCount;
        if( $request->input('inlineCheckbox1') == "on"){
            $open24Hours = 1;
        }else{
            $open24Hours = 0;
        }
      
        $closed =$uncheckedCount;
        $openingTimes = $request->input('opentime', []);
        $closingTimes = $request->input('cltime', []);
       

 
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

  // Check if openingTimes length is 1
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
  } elseif (count($openingTimes) === 1) {
      $sameTime = [
          'start' => $openingTimes[0],
          'end' => $closingTimes[0]
      ];

      // Set the same time for all selected days
      foreach ($selectedDaysIds as $dayId) {
          $dayName = $dayIdToName[$dayId];
          $timeData[$dayName] = $sameTime;
      }
  } else {
      // Map the selected days with their corresponding opening and closing times
      foreach ($selectedDaysIds as $index => $dayId) {
          $dayName = $dayIdToName[$dayId];
          $timeData[$dayName] = [
              'start' => $openingTimes[$index],
              'end' => $closingTimes[$index]
          ];
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

  $data = array(
      'SightId' => $lastinsertedId,
      'timings' => $jsonString,
      'dt_added' => now(),
      'dt_modify'=>now(),
  );
   DB::table('SightTiming')->insert($data);


        /* end timing*/
       

        return redirect()->route('search_attraction')
        ->with('success','Attraction Added successfully.');             
    }

       public function edit_attraction($id){
        $country = DB::table('Country')->orderby('Name')->get();
        $getatt = DB::table('Sight')
            ->leftJoin('Location','Location.LocationId','=','Sight.LocationId')
            ->leftJoin('NearBy','NearBy.SightId','=','Sight.SightId')
            ->leftJoin('Country','Country.CountryId','=','Location.CountryId')
            ->select('Sight.*','Sight.SightId as sid','Location.Name','Location.CountryId','Country.Name as ctname','NearBy.*')
            ->where('Sight.SightId',$id)
            ->get();
        $nearestStations = DB::table('NearBy')->where('SightId',$id)->get(); 
        $SightTiming = DB::table('SightTiming')->where('SightId',$id)->get();
        
        // Initialize timingData with default structure
        $timingData = [
            'time' => [],
            'open_closed' => [
                'open24' => 0,
                'closed' => 0
            ]
        ];
        
        // Override with actual data if it exists
        if (!$SightTiming->isEmpty() && !empty($SightTiming[0]->timings)) {
            $decoded = json_decode($SightTiming[0]->timings, true);
            if (is_array($decoded)) {
                if (isset($decoded['time'])) {
                    $timingData['time'] = $decoded['time'];
                }
                if (isset($decoded['open_closed'])) {
                    $timingData['open_closed'] = $decoded['open_closed'];
                }
            }
        }
    
        
        return view('attraction.edit_sight', compact(
            'getatt',
            'country',
            'nearestStations',
            'SightTiming',
            'timingData'
        ));
    }
  public function update_att(Request $request,$id) {  
       

        $request->validate([
            'sight_name' => 'required',
            'slug' => 'required',
          
        ]); 

        $formData[]  = $request->get('station_name_');

        $dynamicFields = [];
        foreach ($request->all() as $key => $value) {
        if (strpos($key, 'station_name_') === 0) {
            $fieldCount = str_replace('station_name_', '', $key);
            $dynamicFields[$fieldCount]['station_name'] = $value;
        } elseif (strpos($key, 'time_') === 0) {
            $fieldCount = str_replace('time_', '', $key);
            $dynamicFields[$fieldCount]['time'] = $value;
        } elseif (strpos($key, 'duration_') === 0) {
            $fieldCount = str_replace('duration_', '', $key);
            $dynamicFields[$fieldCount]['duration'] = $value;
        }
        }
        $separator = '  '; 


        $locId = null;

  
        if ($request->get('ctname') != "") {
      
            $ctname = $request->get('ctname');
            
           
            $lastCommaPos= false;

            if (strrpos($ctname, ',') !== false) {
                $lastCommaPos= true;
                $lastCommaPos = strrpos($ctname, ',');
                if ($lastCommaPos !== false) {
                    $countryName = substr($ctname, $lastCommaPos + 1);
                    $ctname = substr($ctname, 0, $lastCommaPos);
            
                    $getCont  =  DB::table('Country')->where('Name',$countryName)->get();
                    $ctid = $getCont[0]->CountryId;
    
                    $locId = null;    
                    if($countryName != ""){
                        $getLoc =  DB::table('Location')->select('LocationId')->where('Name',$ctname)->where('CountryId',$ctid)->get();
                    }else{
                        $getLoc = DB::table('Location')->select('LocationId')->where('Name',$ctname)->whereNull('CountryId')->get();
                    }
         
                    if($getLoc->isEmpty()){
                        return redirect()->route('edit_attraction',[$id])->with('error','City not found. Please try again.');
                    }else{
            
                        $locId =  $getLoc[0]->LocationId;
                    }
                }
            } else {
                $county = $request->get('county');            
                $getLoc = DB::table('Location')->select('LocationId','Name')->where('Name',$ctname)->where('CountryId',$county)->get();
       
                if($getLoc->isEmpty()){
                    return redirect()->route('edit_attraction',[$id])->with('error','City not found. Please try again.');
                }else{
        
                    $locId =  $getLoc[0]->LocationId;
                }
            }
           
           
              
       

        }

    
        $sap ="";
       if(!empty($request->get('addressline2'))){
            $sap = ', ';
       }
        $currentDate = Carbon::today()->toDateString();
        $sight =  Sight::find($id);;
        $sight->Title = $request->get('sight_name');
        $sight->Slug = Str::slug($request->get('slug'));
        $sight->MetaTagTitle=$request->get('MetaTagTitle');
        $sight->MetaTagDescription=$request->get('MetaTagDescription');
        $sight->About=$request->get('about');
        $sight->short_description=$request->get('short_description');

        $sight->Address = $request->get('addressline1').$sap.$request->get('addressline2');
        $sight->Neighbourhood = $request->get('neighborhood');
   
        $sight->LocationId = $locId;
       
        $sight->Pincode = $request->get('pincode');
        $sight->Latitude=$request->get('Latitude');
        $sight->Longitude=$request->get('Longitude');
        $sight->Website=$request->get('website');
		$sight->Likes = $request->get('likes');
        $sight->Dislikes = $request->get('dislikes');
        $sight->Phone=$request->get('phone');
        $sight->Email=$request->get('email');
		$sight->GetIn=$request->get('GetIn');
        
     
        if($request->get('mustisee') == 'on'){
          $mustsee = 1;
        }else{
            $mustsee = 0;
        }
        $sight->IsMustSee = $mustsee;
        $sight->IsActive = 1;
        $sight->IsLocationUpdated = 1;
        $sight->CreatedOn = $currentDate;
        $sight->save();
    if(!empty($request->input('nearbyid'))){
        $stationIds = $request->input('nearbyid');
        $station_name = $request->input('station_name');
        $time = $request->input('time');
        $duration = $request->input('duration');

        // Assuming you have a location ID to associate with the nearest stations
      //  $locationId = $yourLocationId;
   //dd($request->input('station_name'));
        // Update the nearest station data
        for ($i = 0; $i < count($stationIds); $i++) {
            $stationId = $stationIds[$i];
            
            DB::table('NearBy')
                ->where('NearById', $stationId)
                ->update([
                    'SightId' => $id,
                    'NearBy' => $station_name[$i],
                    'Time' => $time[$i], 
                    'Duration' => $duration[$i],
                    'CreatedOn' => $currentDate,
                ]);
        }
    }elseif(!empty($request->input('station_name') ) && $request->input('nearbyid') == ""){

        $stationIds = $request->input('nearbyid');
        $station_name = $request->input('station_name');
        $time = $request->input('time');
        $duration = $request->input('duration');
		
	
        // Assuming you have a location ID to associate with the nearest stations
      //  $locationId = $yourLocationId;
  // dd($request->input('station_name'));
      //  Update the nearest station data
	//	return count($station_name);
		if(count($station_name) > 1){
			for ($i = 0; $i < count($station_name); $i++) {
        //    $stationId = $stationIds[$i];
            
            DB::table('NearBy')
                ->insert([
                    'SightId' => $id,
                    'NearBy' => $station_name[$i],
                    'Time' => $time[$i], 
                    'Duration' => $duration[$i],
                    'CreatedOn' => $currentDate,
                ]);
        	}
		}elseif(count($station_name) == 1){
			$station_name = $station_name[0];
			$time = $time[0];
			$duration = $duration[0];
			
		//	return $currentDate;
			$dbdata = array(
			    'SightId' => $id,
				'NearBy' => $station_name,
				'Time' => $time, 
				'Duration' => $duration,
				'CreatedOn' => date('Y-m-d'), 
			);
			DB::table('NearBy')->insert($dbdata);
		}        
		
		
    }
    
    //timing start

    $selectedDaysIds = $request->input('Group', []);

    $selectedCount = count($selectedDaysIds); 
    $uncheckedCount = 7 - $selectedCount;
    if( $request->input('inlineCheckbox1') == "on"){
        $open24Hours = 1;
    }else{
        $open24Hours = 0;
    }
  
    $closed =$uncheckedCount;
    $openingTimes = $request->input('opentime', []);
    $closingTimes = $request->input('cltime', []);
   


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

    // Check if openingTimes length is 1
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
    } elseif (count($openingTimes) === 1) {
    $sameTime = [
        'start' => $openingTimes[0],
        'end' => $closingTimes[0]
    ];

    // Set the same time for all selected days
    foreach ($selectedDaysIds as $dayId) {
        $dayName = $dayIdToName[$dayId];
        $timeData[$dayName] = $sameTime;
    }
    } else {
    // Map the selected days with their corresponding opening and closing times
    foreach ($selectedDaysIds as $index => $dayId) {
        $dayName = $dayIdToName[$dayId];
        $timeData[$dayName] = [
            'start' => $openingTimes[$index],
            'end' => $closingTimes[$index]
        ];
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

    $data = array(
    'SightId'=> $id,
    'timings' => $jsonString,
    'dt_added' => now(),
    'dt_modify'=>now(),
    );
   $stim =  DB::table('SightTiming')->where('SightId',$id)->get();
   if(!$stim->isEmpty()){
   
    DB::table('SightTiming')->where('SightId',$id)->update($data);
   }else{
    DB::table('SightTiming')->insert($data);
   
 }

       //timing end
        return redirect()->route('search_attraction')
        ->with('success','Attraction Updated successfully.');             
    }

    // save timing

    public function save_timing(Request $request,$lastinsertedId )
{
    // Retrieve the data from the AJAX request
    $uncheckedIds = $request->input('uncheckedIds');
    $selectedDaysIds = $request->input('selectedDays');
    $open24Hours = $request->input('open24Hours');
    $closed = $request->input('closed');
    $openingTimes = $request->input('openingTimes');
    $closingTimes = $request->input('closingTimes');
    $sightid = $request->input('sightid');
    $uncheckedCount = 0;
   if($closed == 1){
    $uncheckedCount = count($uncheckedIds);
   }
//     // Validate array sizes
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

    // Check if openingTimes length is 1
    if (count($openingTimes) === 1) {
        $sameTime = [
            'start' => $openingTimes[0],
            'end' => $closingTimes[0]
        ];

        // Set the same time for all selected days
        foreach ($selectedDaysIds as $dayId) {
            $dayName = $dayIdToName[$dayId];
            $timeData[$dayName] = $sameTime;
        }
    } else {
        // Map the selected days with their corresponding opening and closing times
        foreach ($selectedDaysIds as $index => $dayId) {
            $dayName = $dayIdToName[$dayId];
            $timeData[$dayName] = [
                'start' => $openingTimes[$index],
                'end' => $closingTimes[$index]
            ];
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

    $data = array(
        'SightId' => $lastinsertedId,
        'timings' => $jsonString,
        'dt_added' => now(),
        'dt_modify'=>now(),
    );
    return DB::table('SightTiming')->insert($data);
}

    
    
    /*---------------- Attraction faq -----------*/
 
      
    public function search_attr(request $request){
        
        $val = $request->get('val');
    
        $result = array();
        $query = DB::table('Sight')
            ->where('Sight.Title', 'LIKE',  $val . '%')->limit(5)
            ->get();
            foreach ($query as $loc) {
            $result[] = ['id' => $loc->SightId, 'value' => $loc->Title];
            }
     
    
        return response()->json($result);
        
    }

    Public function store_sight_faq(request $request){
        $request->validate([
            'sightname' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);
        $sightid ="";
      
            $ctname =  $request->get('sightname');
            $getsight =  DB::table('Sight')->where('Title',$ctname)->get();
           
            if($getsight->isEmpty()){
                return redirect()->route('add_sight_faq')->with('error','Sight not found. Please try again.');
            }else{

                $sightid =  $getsight[0]->SightId;
            }
     
        $currentDate = Carbon::today()->toDateString();
        $data = array(
            'SightId' => $sightid,
            'Question' => $request->get('question'),
            'Answer' => $request->get('answer'),
            'IsActive' => 1,
            'CreatedDate' => $currentDate,
        );
        
        DB::table('SightQuestion')->insert($data);

        return redirect()->route('search_attraction')->with('success','Faq added Successfully.');
    }
   
    public function edit_attfaq($id){
        
        $getfaq = DB::table('SightQuestion')->leftJoin('Sight','Sight.SightId','=','SightQuestion.SightId')
        ->select('SightQuestion.*','Sight.Title')
        ->where('SightQuestion.SightId',$id)->get();
        
        return view('attraction.edit_sight_faq',['getfaq'=>$getfaq]);
    }
    public function update_faq(request $request){
        $id =  $request->get('faqId');
        $answer = $request->get('answer');

        if($answer == ""){
            $answer =" ";
        }


        $currentDate = Carbon::today()->toDateString();
        $data = array(
            'Question' => $request->get('question'),
            'Answer' => $answer,
            'IsActive' => 1,
            'CreatedDate' => now(),
        );
        
        return  DB::table('SightQuestion')->where('SightQuestionId',$id)->update($data);

 
    }
	   public function add_sight_faq(request $request){
            $question = $request->get('checkboxText');
            $sightid = $request->get('sightid');
            $data = array(
                    'Question'=>$question,
                    'SightId'=>$sightid,
                    'IsActive' => 1,
                    'CreatedDate' => now(),
                    'Answer'=>''
            );
            DB::table('SightQuestion')->insert($data);

           
            $hotelfaq = DB::table('SightQuestion')
            ->Leftjoin('Sight','SightQuestion.SightId', '=' ,'Sight.SightId')
            ->select('SightQuestion.*','Sight.Title')
            ->where('SightQuestion.SightId',$sightid)->get();

            return view('attraction.updated_faq',['getfaq'=>$hotelfaq]);
        }

    /*------------- Manage Cotegory  -------------*/

    
    public function add_category(){
        return view('attraction.add_category');
    }
    public function search_attr_category(request $request){
        $val = $request->get('val');
    
        $result = array();
        $query = DB::table('Category')->where('Title', 'LIKE', $val . '%')
            ->orWhere('CategoryId', 'LIKE', $val . '%')->limit(4)
            ->get();
            foreach ($query as $loc) {
            $result[] = ['id' => $loc->CategoryId, 'value' => $loc->Title];
            }
     
    
        return response()->json($result);
    }

    //new code
    public function save_category(request $request){
       
     
      
        $catname =  $request->get('value');
        $id =  $request->get('id');
     
        $geCategoryid =  DB::table('Category')->where('Title',$catname)->value('CategoryId');

        if($geCategoryid !== null){
            $get_sightcat_exists =  DB::table('SightCategory')->where('CategoryId',$geCategoryid)->where('SightId',$id)->exists();

		
            if(!$get_sightcat_exists){
				
                $data = array(
                    'CategoryId'=>$geCategoryid,
                    'SightId'=>$id ,
                );
                DB::table('SightCategory')->insert($data);
            }else{
                return 2;
            }
        }else{
            return 3;
        }

        $getsitecat =  DB::table('SightCategory')->leftJoin('Sight','Sight.SightId','=','SightCategory.SightId')->leftJoin('Category','Category.CategoryId','=','SightCategory.CategoryId')->select('SightCategory.*','Category.Title as ctitle','Sight.Title as stitle')->where('SightCategory.SightId',$id)->get();
        return view('attraction.filter_category',['get_cat'=> $getsitecat,'sightid'=>$id]);
          
             
          
         }
         public function deleteatt_category(request $request){
            $id = $request->get('id');
            $sightid = $request->get('sightid');
           
            DB::table('SightCategory')->where('SightCategoryId',$id)->where('SightId',$sightid)->delete();
         
          
            $getsitecat =  DB::table('SightCategory')->leftJoin('Sight','Sight.SightId','=','SightCategory.SightId')->leftJoin('Category','Category.CategoryId','=','SightCategory.CategoryId')->select('SightCategory.*','Category.Title as ctitle','Sight.Title as stitle')->where('SightCategory.SightId',$sightid)->get();
            
            
            return view('attraction.filter_category',['get_cat'=> $getsitecat,'sightid'=>$sightid]);
         
        }


         public function edit_category($id){
          $getsitecat =  DB::table('SightCategory')->leftJoin('Sight','Sight.SightId','=','SightCategory.SightId')->leftJoin('Category','Category.CategoryId','=','SightCategory.CategoryId')->select('SightCategory.*','Category.Title as ctitle','Sight.Title as stitle')->where('SightCategory.SightId',$id)->get();
            return view('attraction.edit_category',['get_cat'=> $getsitecat]);
        }

//new code


        public function update_category(request $request,$id){
            $request->validate([
                'sightname' => 'required',
                'category' => 'required',
            ]);
            $sightid ="";
          
                $ctname =  $request->get('sightname');
                $getsight =  DB::table('Sight')->where('Title',$ctname)->get();
               
                if($getsight->isEmpty()){
                    return back()->with('error','Attraction not found. Please try again.');
                }else{
    
                    $sightid =  $getsight[0]->SightId;
                }
             $category = "";
             
                $catename = $request->get('category');
                $getcat =  DB::table('Category')->where('Title',$catename)->get();
        
                if($getcat->isEmpty()){
                    return back()->with('error','Category not found. Please try again.');
                }else{
                    $CategoryId =  $getcat[0]->CategoryId;
                }
    
                $data = array(
                    'SightId' => $sightid,
                    'CategoryId' => $CategoryId,
                );
                DB::table('SightCategory')->where('SightCategoryId',$id)->update($data);
                return redirect()->route('search_attraction')->with('success','Category Updated successfully.');
             }

        /*------------- Manage Reviews  -------------*/

        public function manage_att_review(request $request){
        
            $get_attreview =  DB::table('SightReviews')
            ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
            ->select('SightReviews.*','Users.FirstName','Users.LastName')->limit(5)->orderby('SightReviews.id','desc')
            //->where('SightCategory.SightId',$id)->get();
        ->get();
            return view('attraction.manage_reviews',['get_attreview'=> $get_attreview]);
        }
        public function filter_manage_att_review(request $request){
            if($request->get('val') != ""){
                $orderby = $request->get('val');
            }else{
                $orderby = "desc";
            }
        
            $get_attreview =  DB::table('SightReviews')
            ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
            ->select('SightReviews.*','Users.FirstName','Users.LastName')->limit(4)->orderby('SightReviews.id',$orderby)
            ->get();
            return view('attraction.get_filterreview',['get_attreview' => $get_attreview]);
            
        }
        
        public function filter_review_edit(request $request){
            if($request->get('val') != ""){
                $orderby = $request->get('val');
            }else{
                $orderby = "desc";
            }
            $rtype = $request->get('rval');
            $id = $request->get('id');
            if($rtype != ""){
                $get_attreview =  DB::table('SightReviews')
                ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
                ->select('SightReviews.*','Users.FirstName','Users.LastName')->where('SightReviews.SightId',$id)->where('IsApprove',$rtype)->orderby('SightReviews.id',$orderby)
                ->get();
            }else{
                $get_attreview =  DB::table('SightReviews')
                ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
                ->select('SightReviews.*','Users.FirstName','Users.LastName')->where('SightReviews.SightId',$id)->orderby('SightReviews.id',$orderby)
                ->get();
            }
            return view('attraction.get_filterreview',['get_attreview' => $get_attreview]);
            
        }
        public function search_attreviews(request $request){
        
                $val = $request->get('val');
                $sightid = $request->get('id');
                 
                $get_attreview =  DB::table('SightReviews')
                ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
                ->select('SightReviews.*','Users.FirstName','Users.LastName')
                ->where('SightId',$sightid)
                ->where(function($query) use ($val) {
                    if (strlen($val) <= 5) {
                        $query->where('SightReviews.id', 'LIKE', '%' . $val . '%');
                    } else {
                        $query->where('SightReviews.id', $val);
                    }
                })
                ->limit(3)
                ->get();
            
            return view('attraction.get_filterreview',['get_attreview' => $get_attreview]);
            
        }

        public function update_review(request $request){
            $id = $request->get('id');            
            $sightid = $request->get('sightid');
            DB::table('SightReviews')->where('Id',$id)->update(['IsApprove'=>$request->get('value')]);

            $get_attreview =  DB::table('SightReviews')
                ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
                ->select('SightReviews.*','Users.FirstName','Users.LastName')->where('SightReviews.SightId',$sightid)->where('IsApprove',$request->get('value'))
                ->get();
                $val =  $request->get('value');
            return view('attraction.get_filterreview',['get_attreview' => $get_attreview,'val'=>$val]);
        
        }
        public function edit_reviewbyid(request $request,$id){
            $get_attname = DB::table('Sight')->select('Sight.Title')->where('SightId',$id)->get();
            $get_attreview =  DB::table('SightReviews')
            ->leftJoin('users','users.UserId','=','SightReviews.UserId')
            ->select('SightReviews.*','Users.FirstName','Users.LastName')->orderby('SightReviews.id','desc')
            ->where('SightReviews.SightId',$id)
            ->get();
            return view('attraction.edit_reviews',['get_attreview'=> $get_attreview,'get_attname'=>$get_attname]);
        }
        
        public function filterreview(request $request){
            $val = $request->get('val');
            $id = $request->get('id');
            
            $get_attreview =  DB::table('SightReviews')
            ->select('SightReviews.*')
            ->where('SightReviews.SightId',$id)
            ->where('SightReviews.IsApprove',$val)
            ->orderby('SightReviews.Id','desc')
            ->get();

            return view('attraction.get_filterreview',['get_attreview' => $get_attreview,'val'=>$val]);
        
        }
        public function filterreview_manage(request $request){
        $val = $request->get('val');
        $get_attreview =  DB::table('SightReviews')
        ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
        ->select('SightReviews.*','Users.FirstName','Users.LastName')->where('SightReviews.IsApprove',$val)->limit(10)->orderby('SightReviews.Id','desc')
        ->get();
        return view('attraction.get_filterreview',['get_attreview' => $get_attreview,'val'=>$val]);

        }  

        /* -------landing pages ----------*/
        public function att_landingpage(){
            return view('attraction.landing_page');
         }
	
	
       //lading page
        public function add_landing_page(){
            return view('attraction.add_landing_page');
        }
        public function get_category(Request $request){
            $search = $request->get('val');

            $result = array();
        
            $query = DB::table('Category')
                ->where('Title', 'LIKE', '%' . $search . '%')
                ->limit(4)
                ->get();
        
            foreach ($query as $loc) {
                $result[] = [
                    'value' => $loc->Title,                 
                ];
            }
        
            return response()->json($result);
        } 

        public function get_traveler_type(Request $request){
            $val = $request->get('val');
            $hoteltype = [
                'Family Friendly', 'Couples','Kids Friendly' 
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
        
        public function store_landing(request $request){
            $name = $request->name;
            $slug = $request->slug;
            $meta_title = $request->meta_title;
            $meta_desc = $request->meta_desc;
            $about = $request->about; 
            $sightid = $request->sightid;  
            $nearbytype = $request->nearbytype;
            $nearby = $request->nearby;
       
            $category_value = json_encode($request->category_value);
            $star_rating = json_encode($request->star_rating);
            $duration_value = json_encode($request->duration_value);
            $Traveler_Types_value = json_encode($request->Traveler_Types_value);    
            $distance_array = json_encode($request->distance_array);
            $Attraction_Tags_array = json_encode($request->Attraction_Tags_array);

            $data = array(
                'Page_Name' =>$name,
                'Slug' =>$slug,
                'Meta_Title' =>$meta_title,
                'Meta_Description' =>$meta_desc,
                'About' =>$about,
                'sightId' =>$sightid,
                'Near_Type' =>$nearbytype,
                'Nearby' =>$nearby,
                'Category' =>$category_value,
                'Ratings' =>$star_rating,
                'Duration' =>$duration_value,
                'Traveler_Types' =>$Traveler_Types_value,
                'Distance' =>$distance_array,
                'Attraction_Tags' =>$Attraction_Tags_array,


            );
            return     DB::table('SightLanding')->insert($data);
                        
        }

        public function edit_landing($id){
            $get_landing = DB::table('SightLanding')->where('sightid',$id)->get();
            return view('attraction.edit_landing_page',['get_landing'=>$get_landing]);
        }
        

        public function update_landing(request $request){
            $name = $request->name;
            $slug = $request->slug;
            $meta_title = $request->meta_title;
            $meta_desc = $request->meta_desc;
            $about = $request->about; 
            $sightid = $request->sightid;  
            $nearbytype = $request->nearbytype;
            $nearby = $request->nearby;
       
            $category_value = json_encode($request->category_value);
            $star_rating = json_encode($request->star_rating);
            $duration_value = json_encode($request->duration_value);
            $Traveler_Types_value = json_encode($request->Traveler_Types_value);    
            $distance_array = json_encode($request->distance_array);
            $Attraction_Tags_array = json_encode($request->Attraction_Tags_array);
            $id = $request->id;
            $data = array(
                'Page_Name' =>$name,
                'Slug' =>$slug,
                'Meta_Title' =>$meta_title,
                'Meta_Description' =>$meta_desc,
                'About' =>$about,
                'sightId' =>$sightid,
                'Near_Type' =>$nearbytype,
                'Nearby' =>$nearby,
                'Category' =>$category_value,
                'Ratings' =>$star_rating,
                'Duration' =>$duration_value,
                'Traveler_Types' =>$Traveler_Types_value,
                'Distance' =>$distance_array,
                'Attraction_Tags' =>$Attraction_Tags_array,


            );
            return     DB::table('SightLanding')->where('ID',$id)->update($data);
                        
        }

        public function hidepage(request $request){
            $landingid =  $request->get('landing');
                $data = array(
                    'status' => 0,
                );
            
            return DB::table('SightLanding')->where('ID',$landingid)->update($data);
        }

        public function delete_landing(request $request){
            $id =  $request->get('landing');
             return  DB::table('SightLanding')->where('ID',$id)->delete();
       
           }

     public function update_markspan(request $request){
            $id = $request->get('id');
            $sightid = $request->get('sightid');
            
              DB::table('SightReviews')->where('Id',$id)->update(['IsApprove'=>$request->get('value')]);

              $get_attreview =  DB::table('SightReviews')
                  ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
                  ->select('SightReviews.*','Users.FirstName','Users.LastName')->where('SightReviews.SightId',$sightid)->where('IsApprove',$request->get('value'))
                  ->get();

             if($request->get('fval') != ""){
                $val1 =$request->get('fval');
              }else{
                $val1 =$request->get('value');
              }
              return view('attraction.get_filterreview',['get_attreview' => $get_attreview,'val'=>$val1]);
         
        }
	
	   /*---------------Edit Image -----------------*/
	      public function upload_sight_Image(Request $request,$id)
        {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
          
            $imagetype = $request->get('image_type');
        
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('sight-images'), $imageName);
    
                // $filePath = 'sight-images/' . $imageName;
                // Storage::disk('s3')->put($filePath, file_get_contents($file));

                if($imagetype == 1){
                    $data = array(
                        'Sightid'=>$id,
                        'Image'=>$imageName,
                        'IsPrimary'=>1,
                        'IsActive'=>1
                    );
                }else{
                    $data = array(
                        'Sightid'=>$id,
                        'Image'=>$imageName,
                        'IsPrimary'=>0,
                        'IsActive'=>1,
                    );
                }
               
                $getreview = DB::table('Sight_image')->insert($data);
    
                return redirect()->route('edit_sight_img', ['id' => $id])->with('success', 'Image uploaded successfully.');
    
    
            }
     
        }
        
           public function edit_sight_img($id){
            $get_img = DB::table('Sight_image')->where('Sightid',$id)->get();
            $getsight = DB::table('Sight')->where('SightId',$id)->get();
            return view('attraction/edit_sight_img',['get_img' => $get_img,'getsight'=>$getsight]);
        } 
    
        public function delete_sight_image(request $request){
            $id = $request->get('id');
            $sightid =  $request->get('sighid');
      
            $image =  DB::table('Sight_image')->where('id',$id)->get();
                $image[0]->Image;
           
            if ($image) {
      
              $imagePath = public_path('sight-images/' . $image[0]->Image);
              if (File::exists($imagePath)) {
                  File::delete($imagePath);
                  DB::table('Sight_image')->where('id', $id)->delete();
      
               

                  $get_img = DB::table('Sight_image')->where('Sightid',$sightid)->get();
                  $getsight = DB::table('Sight')->where('SightId',$sightid)->get();
                  return view('attraction/filter_images',['get_img' => $get_img,'getsight'=>$getsight]);
          
                  
            
              } else {
                
                  DB::table('Sight_image')->where('id', $id)->delete();
      
                  $get_img = DB::table('Sight_image')->where('Sightid',$sightid)->get();
                  $getsight = DB::table('Sight')->where('SightId',$sightid)->get();
                  return view('attraction/filter_images',['get_img' => $get_img,'getsight'=>$getsight]);
              }
      
            }
        
            return response()->json(['message' => 'Image not found'], 404);
          }

    public function filter_sight_image(request $request){
        $sighid = $request->get('sighid');
        $val = $request->get('val');
        if($val != ""){
            $get_img =  DB::table('Sight_image')->where(function($query) use ($val) {
                if (strlen($val) <= 1) {             
                    $query->where('id', $val);
                } else {
                    $query->where('id', $val);
                }
            })
            ->limit(2)->get();
        }else{

            $get_img = DB::table('Sight_image')->where('Sightid',$sightid)->get();
        }
       
        $getsight = DB::table('Sight')->where('SightId',$sighid)->get();
        return view('attraction/filter_images',['get_img' => $get_img,'getsight'=>$getsight]);

    }  
	
	
	 //start attraction

   
 
    public function att_detail(request $request,$id)
    {
            $pt = $request->input('sloc');
        
        $idSegments = explode('-', $id);
       
        $locationID = 0;
        $sighid ="";
        $location_slug="";
        $sight_slug ="";

        $parts = explode('-', $id);
        if (count($idSegments) > 1) {
           $locationID = $parts[0];
           $sighid = $idSegments[1];
           $sight_slug = implode('-', array_slice($parts, 2, -1));
           $location_slug =  end($parts);
           $slug = $sight_slug.'-'.$location_slug;
        } else {
            $explocname = $id;
        }

        $getparent = DB::table('Sight')->where('SightId',$sighid)->get();
        if(!$getparent->isEmpty()){
          $getparent[0];
        }

  // get parent 
  $getparent = DB::table('Location')->where('LocationId', $locationID)->get();

     $locationPatent = [];
     if (!empty($getparent)){
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
                 break; 
             }
         }
     }
  }
  // end parent

     
      
 
      
        //stored procedure to query start
     $searchresults = DB::table('Sight')
      ->Leftjoin('Location', 'Sight.LocationId', '=', 'Location.LocationId')
      ->Leftjoin('Country', 'Location.CountryId', '=', 'Country.CountryId')
      ->Leftjoin('Category', 'Sight.CategoryId', '=', 'Category.CategoryId')
      ->select('Sight.*', 'Location.Name', 'Location.Slug as Lslug', 'Country.Name as countryName')
      ->where('Sight.SightId', $sighid)
      ->where('Location.LocationId', $locationID)
      ->where('Sight.Slug', $slug)
      ->get()->toArray();
    
		 if(empty($searchresults)){
		   abort(404, 'NOT FOUND');
		  }
		
		       $breadcumb  = DB::table('Location as l')
          ->select('l.CountryId', 'l.Name as LName', 'l.Slug as Lslug', 'co.Name as CountryName','l.LocationId','co.slug as cslug','co.CountryId','cont.Name as ccName','cont.CountryCollaborationId as contid')        
          ->Join('Country as co', 'l.CountryId', '=', 'co.CountryId')
          ->join('CountryCollaboration as cont','cont.CountryCollaborationId','=','co.CountryCollaborationId')
          ->where('l.LocationId', $locationID)	
          ->get()
          ->toArray();
     // $sightimages = DB::table('SightImages')->where('SightId',$sighid)->get();
    
      $sighttimings = DB::table('SightTiming')->where('SightId',$sighid)->get();

      $sightreviews = DB::table('SightReviews')->where('SightId',$sighid)->limit(20)->get();

      $SightNeighbourhood = DB::table('SightNeighbourhood')
      ->join('Sight', function ($join) use ($sighid) {
          $join->on('SightNeighbourhood.NeighborhoodID', '=', 'Sight.SightId')
              ->where('SightNeighbourhood.SightId', '=', $sighid);
      })
      ->join('Location', 'Sight.LocationId', '=', 'Location.LocationId')
      ->join('Country', 'Location.CountryId', '=', 'Country.CountryId')
      ->select('SightNeighbourhood.*', 'Sight.*', 'Country.Name')
      ->get();
     $faq = DB::table('SightListingDetailFaq')->where('SightId',$sighid)->get()->toArray();
 

      //stored procedure to query end
      

      $nearby_sight=collect();
      $getcat =collect();
      $nearbyatt =collect();
      $gettiming =collect();
      $nearby_hotel =collect();
      $within ="";
  
      if(!empty($searchresults)) {
       
       $getcat = DB::table('SightCategory')
              ->join('Category','Category.CategoryId','=','SightCategory.CategoryId')
              ->where('SightCategory.SightId',$searchresults[0]->SightId)
              ->distinct('SightCategory.CategoryId')
              ->get();
        $gettiming = DB::table('SightTiming')->where('SightId',$searchresults[0]->SightId)->get();
      
        $longitude = $searchresults[0]->Longitude;
        $latitude = $searchresults[0]->Latitude;       
        $LocationId = $searchresults[0]->LocationId;
        $sightid = $searchresults[0]->SightId;
        $locationIds = array_column($locationPatent, 'LocationId');
        if( $latitude =="" || $longitude==""){
            //$LocationId
            $getsight = DB::table('Sight')->select('Latitude','Longitude','SightId')->where('LocationId',$LocationId)->whereNotNull('Latitude')->whereNotNull('Longitude')->limit(1)->get();
           
            if(!$getsight->isEmpty()){
                $longitude = $getsight[0]->Longitude;
                $latitude = $getsight[0]->Latitude;   
            }else{
         
                $getsight = DB::table('Location as l')
                ->join('Sight as s', 'l.LocationId', '=', 's.LocationId')
                ->select('s.Latitude','s.Longitude','s.SightId')
                ->whereIn('l.LocationId', $locationIds)
                ->whereNotNull('s.Latitude')
                ->whereNotNull('s.Longitude')
                ->limit(1)
                ->get();
         
         
         
                if(!$getsight->isEmpty()){
                    $longitude = $getsight[0]->Longitude;
                    $latitude = $getsight[0]->Latitude;   
                }
               
            }
           
        }


          if($longitude !="" && $latitude !=""){
           
              $searchradius = 50; 
              $attempts = 0;
              $maxAttempts = 3; 
              
     
          
    
 
                $nearby_sight =  DB::table('Sight_nearby_sights')->where('Sid',$sightid)->orderby('distance','asc')->get();
                $nearbyatt = DB::table('Sight_nbsight')->where('Sid',$sightid)->get();
            	$nearby_hotel =  DB::table('Sight_nearby_hotels')->where('SightId',$sightid)->get();

             }
            //hotel list id
                
                $gethotellistiid = DB::table('Temp_Mapping as tm')
                ->select('tpl.*')
                ->join('TPLocations as tpl','tpl.locationId','=','tm.LocationId')
                ->where('tm.Tid',$locationID)
                ->get();
            
            
            
                // end hotel list id
          }
     
                $Sight_image = DB::table('Sight_image')  
                ->where('Sightid',$sightid)
                ->get();

     return view('sightdetails')->with('searchresult',$searchresults)
     //->with('sightimages',$sightimages)
       ->with('sighttimings',$sighttimings)
     ->with('sightreviews',$sightreviews)
     ->with('sightnearby',$SightNeighbourhood)
     ->with('faq',$faq)
     ->with('sloc',$pt)
     ->with('locationPatent',$locationPatent)
     ->with('nearby_sight',$nearby_sight)
     ->with('nearbyatt',$nearbyatt)
      ->with('getcat',$getcat)
      ->with('gettiming',$gettiming)
      ->with('nearby_hotel',$nearby_hotel)
      ->with('gethotellistiid',$gethotellistiid)
	   ->with('breadcumb',$breadcumb)
		->with('Sight_image',$Sight_image);
       
    }
   
    
    //end attraction
}