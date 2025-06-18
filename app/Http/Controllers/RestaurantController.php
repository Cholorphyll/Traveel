<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class RestaurantController extends Controller
{
    public function index()
    {
        return view('restaurant.restaurant');
    }
    public function searchrest(request $request){
        $val =  $request->get('value');
        
            $getlisting = DB::table('Restaurant')              
                ->where(function ($query) use ($val) {
                        $query->where(
                            'RestaurantId', '=', $val)->orWhere(
                                'Title', '=', $val)->orWhere(
                                    'Slug', '=', $val);
                
                        // URL से RestaurantId निकालने की प्रक्रिया
                        if (strpos($val, '-') !== false) {
                            $urlParts = explode('-', $val);
                            $id = isset($urlParts[2]) ? $urlParts[2] : null;
                            
                            error_log('Extracted ID: ' . $id);
                            
                            if ($id) {
                                $query->orWhere('RestaurantId', $id);
                            }
                        }
                })->limit(2)
                ->get();
               
            return view('restaurant.filter_rest',['hotellisting'=>$getlisting]);
    }
    public function edit_restaurant($id){
        $restaurant = restaurant::find($id);
        if(!empty($restaurant)){
            $getcity =  DB::table('Location')->where('LocationId',$restaurant->LocationId)->get();  
        }
       
        $country=[] ;
        if(!$getcity->isEmpty()){

            $country = DB::table('Country')->where('CountryId',$getcity[0]->CountryId)->get();
        }
        $RestaurantFeature =  DB::table('RestaurantFeatureAssociation')
        ->join('RestaurantFeature','RestaurantFeature.RestaurantFeatureId','RestaurantFeatureAssociation.RestaurantFeatureId')
        ->select('RestaurantFeature.*')
        ->where('RestaurantFeatureAssociation.RestaurantId',$id)->get();  

        $RestaurantDeit =  DB::table('RestaurantSpecialDietAssociation')
        ->join('RestaurantSpecialDiet','RestaurantSpecialDiet.RestaurantSpecialDietId','RestaurantSpecialDietAssociation.RestaurantSpecialDietId')
        ->select('RestaurantSpecialDiet.*')
        ->where('RestaurantSpecialDietAssociation.RestaurantId',$id)->get();  
  
        return view('restaurant.edit_restaurant',['restaurant'=>$restaurant,'country'=>$country,'getcity'=>$getcity,'RestaurantFeature'=>$RestaurantFeature,'RestaurantDeit'=>$RestaurantDeit]);
    }

    public function add_restaurant(){
        $country = DB::table('Country')->get();
        return view('restaurant.add_restaurant',['country'=>$country]);
    }

    public function store_rest(request $request){

        //Nearest Station
        $stationNames = $request->input('station_name');
        $times = $request->input('time');
        $durations = $request->input('duration');

        $nearest_station = [];
        for ($i = 0; $i < count($stationNames); $i++) {
            $nearest_station[] = [
                'station_name' => $stationNames[$i],
                'time' => $times[$i],
                'duration' => $durations[$i]
            ];
        }

        $jsonData = json_encode($nearest_station);
        //end nearest Station

        //add menu

        $menu_item = $request->input('menu');

        $menudata = [];

      
        for ($i = 0; $i < count($menu_item); $i++) {
            $menudata[] = [
                'menuitem' => $menu_item[$i],
               
            ];
        }

      $menu = json_encode($menudata);
  
        //end menu
        $ctname = null;
        if ($request->get('ctname') != "") {            
             $getsight = DB::table('Location')->where('Name', $request->get('ctname'))->get();
             if (!$getsight->isEmpty()) {
                 $ctname = $getsight[0]->LocationId;
             }
        }
       

        $Restaurent = new Restaurant;
        $Restaurent->Title = $request->get('rest_name');
        $Restaurent->Slug = Str::slug($request->get('slug'));
        $Restaurent->MetaTagTitle=$request->get('MetaTagTitle');
        $Restaurent->MetaTagDescription=$request->get('MetaTagDescription');
        $Restaurent->About=$request->get('about');
        $Restaurent->Address = $request->get('addressline1') . ($request->get('addressline2') != "" ? ', ' : '') . $request->get('addressline2');


        $Restaurent->neighborhood = $request->get('neighborhood');
        $Restaurent->Pincode = $request->get('pincode');
        $Restaurent->Latitude=$request->get('Latitude');
        $Restaurent->Longitude=$request->get('Longitude');
        $Restaurent->Website=$request->get('website');

        $Restaurent->Phone=$request->get('phone');
        $Restaurent->Email=$request->get('email');
        $Restaurent->PriceRange=$request->get('pricing');  
        $Restaurent->LocationId= $ctname;

        $Restaurent->Menu=$menu;
        if($request->get('mustisee') == 'on'){
          $mustsee = 1;
        }else{
            $mustsee = 0;
        }
        $Restaurent->IsMustSee = $mustsee;
        $Restaurent->IsActive = 1;
       // $Restaurent->IsLocationUpdated = 1;
        $Restaurent->CreatedOn = now();
        $Restaurent->NearestStation = $jsonData;
     //   print_r($jsonData);
        

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
        
    
    
    
        if (count($openingTimes) !== count($closingTimes)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }
        
        
        $dayIdToName = [
            'r1' => 'sun',
            'r2' => 'mon',
            'r3' => 'tue',
            'r4' => 'wed',
            'r5' => 'thu',
            'r6' => 'fri',
            'r7' => 'sat'
        ];
        
    
        $timeData = [];
        
    
        if($open24Hours == 1){
                $sameTime = [
                    'start' => '00:00',
                    'end' => '23:59',
                ];
        
        
                foreach ($selectedDaysIds as $dayId) {
                    $dayName = $dayIdToName[$dayId];
                    $timeData[$dayName] = $sameTime;
                }
        } elseif (count($openingTimes) === 1) {
            $sameTime = [
                'start' => $openingTimes[0],
                'end' => $closingTimes[0]
            ];
        
    
            foreach ($selectedDaysIds as $dayId) {
                $dayName = $dayIdToName[$dayId];
                $timeData[$dayName] = $sameTime;
            }
        } else {
        
            foreach ($selectedDaysIds as $index => $dayId) {
                $dayName = $dayIdToName[$dayId];
                $timeData[$dayName] = [
                    'start' => $openingTimes[$index],
                    'end' => $closingTimes[$index]
                ];
            }
        }
        
        
        $jsonDatatime = [
            'time' => $timeData,
            'open_closed' => [
                'open24' => $open24Hours,
                'closed' => $closed
            ]
        ];
        
            $jsonString = json_encode($jsonDatatime);
        

        /* end timing*/
        $Restaurent->Timings = $jsonString;
        $Restaurent->save();
            

        $lastinsertedId = $Restaurent->RestaurantId;


          //start  feature
        
          $feature_item = $request->input('featureitem');

          $feature_data = [];
  
        
          if (is_array($feature_item)) {
            for ($i = 0; $i < count($feature_item); $i++) {

                $ftdata =  DB::table('RestaurantFeature')->where('Name', $feature_item[$i])->get();
                  $ftdata[0]->RestaurantFeatureId;
                  $fData[] = [
                      'RestaurantFeatureId' => $ftdata[0]->RestaurantFeatureId,
                      'RestaurantId'=>$lastinsertedId,
                     
                  ];
                  DB::table('RestaurantFeatureAssociation')->insert($fData);
            }
          } else {
            // Handle the case when $feature_item is not an array
            // For example, you can initialize it as an empty array
            $feature_item = [];
          }
        
          //end

           //start dietry
        
           $dietry_items = $request->input('dietryitems');

           $feature_data = [];
   
         
           if (is_array($dietry_items)) {
               for ($i = 0; $i < count($dietry_items); $i++) {
                   $dtdata =  DB::table('RestaurantSpecialDiet')->where('Name', $dietry_items[$i])->get();
                   $RestaurantSpecialDietId= $dtdata[0]->RestaurantSpecialDietId;
                   $dietData[] = [
                       'RestaurantSpecialDietId' => $RestaurantSpecialDietId,
                       'RestaurantId'=>$lastinsertedId,
                   ];
                   DB::table('RestaurantSpecialDietAssociation')->insert($dietData);
               }
           }
         
           //end
        return redirect()->route('restaurant')
        ->with('success','Restaurant Added successfully.');   
    }

    public function searchfeature(request $request){
        $search = $request->get('val');

        $result = array();
    
        $query = DB::table('RestaurantFeature')           
            ->where('Name', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();
    
        foreach ($query as $loc) {
            $result[] = [               
                'value' => $loc->Name,               
            ];
        }
    
        return response()->json($result);
    }

    public function update_rest(request $request,$id){
    
        //Nearest Station
        $stationNames = $request->input('station_name');
        $times = $request->input('time');
        $durations = $request->input('duration');

        $nearest_station = [];
        for ($i = 0; $i < count($stationNames); $i++) {
            $nearest_station[] = [
                'station_name' => $stationNames[$i],
                'time' => $times[$i],
                'duration' => $durations[$i]
            ];
        }

        $jsonData = json_encode($nearest_station);
        //end nearest Station

        //add menu

        $menu_item = $request->input('menu');

        $menudata = [];

      
        for ($i = 0; $i < count($menu_item); $i++) {
            $menudata[] = [
                'menuitem' => $menu_item[$i],
               
            ];
        }

      $menu = json_encode($menudata);
  
        //end menu
        $ctname =null;
        if($request->get('ctname') !=""){            
             $getloc =  DB::table('Location')->where('Name',$request->get('ctname'))->get();
             $ctname =$getloc[0]->LocationId  ;
        }
      
        $Restaurent = restaurant::find($id);
        $Restaurent->Title = $request->get('rest_name');
        $Restaurent->Slug = Str::slug($request->get('slug'));
        $Restaurent->MetaTagTitle=$request->get('MetaTagTitle');
        $Restaurent->MetaTagDescription=$request->get('MetaTagDescription');
        $Restaurent->About=$request->get('about');
        $Restaurent->Address = $request->get('addressline1') . ($request->get('addressline2') != "" ? ', ' : '') . $request->get('addressline2');


        $Restaurent->neighborhood = $request->get('neighborhood');
        $Restaurent->Pincode = $request->get('pincode');
        $Restaurent->Latitude=$request->get('Latitude');
        $Restaurent->Longitude=$request->get('Longitude');
        $Restaurent->Website=$request->get('website');

        $Restaurent->Phone=$request->get('phone');
        $Restaurent->Email=$request->get('email');
        $Restaurent->PriceRange=$request->get('pricing');  
        $Restaurent->LocationId= $ctname;

        $Restaurent->Menu=$menu;
        if($request->get('mustisee') == 'on'){
          $mustsee = 1;
        }else{
            $mustsee = 0;
        }
        $Restaurent->IsMustSee = $mustsee;
        $Restaurent->IsActive = 1;
       // $Restaurent->IsLocationUpdated = 1;
        $Restaurent->CreatedOn = now();
        $Restaurent->NearestStation = $jsonData;
     //   print_r($jsonData);
        

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
        
    
    
    
        if (count($openingTimes) !== count($closingTimes)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }
        
        
        $dayIdToName = [
            'r1' => 'sun',
            'r2' => 'mon',
            'r3' => 'tue',
            'r4' => 'wed',
            'r5' => 'thu',
            'r6' => 'fri',
            'r7' => 'sat'
        ];
        
    
        $timeData = [];
        
    
        if($open24Hours == 1){
                $sameTime = [
                    'start' => '00:00',
                    'end' => '23:59',
                ];
        
        
                foreach ($selectedDaysIds as $dayId) {
                    $dayName = $dayIdToName[$dayId];
                    $timeData[$dayName] = $sameTime;
                }
        } elseif (count($openingTimes) === 1) {
            $sameTime = [
                'start' => $openingTimes[0],
                'end' => $closingTimes[0]
            ];
        
    
            foreach ($selectedDaysIds as $dayId) {
                $dayName = $dayIdToName[$dayId];
                $timeData[$dayName] = $sameTime;
            }
        } else {
        
            foreach ($selectedDaysIds as $index => $dayId) {
                $dayName = $dayIdToName[$dayId];
                $timeData[$dayName] = [
                    'start' => $openingTimes[$index],
                    'end' => $closingTimes[$index]
                ];
            }
        }
        
        
        $jsonDatatime = [
            'time' => $timeData,
            'open_closed' => [
                'open24' => $open24Hours,
                'closed' => $closed
            ]
        ];
        
            $jsonString = json_encode($jsonDatatime);
        

        /* end timing*/
        $Restaurent->Timings = $jsonString;
        $Restaurent->save();
            

        $lastinsertedId = $Restaurent->RestaurantId;


          //start feature
          $getrestfeat = DB::table('RestaurantFeatureAssociation')->where('RestaurantId', $id)->get();

          $feature_item = $request->input('featureitem');
          $feature_data = [];
     
          if ($request->input('featureitem') != "") {
            DB::table('RestaurantFeatureAssociation')
            ->where('RestaurantId', $id)
            ->delete();
              for ($i = 0; $i < count($feature_item); $i++) {
                  $ftdata = DB::table('RestaurantFeature')->where('Name', $feature_item[$i])->get();
                  $ftdata[0]->RestaurantFeatureId;
                  $fData = [
                      'RestaurantFeatureId' => $ftdata[0]->RestaurantFeatureId,
                      'RestaurantId' => $lastinsertedId,
                  ];
               
                //   if (!$getrestfeat->isEmpty()) {
                //       // Update each row separately using the where condition for RestaurantFeatureId
                //       DB::table('RestaurantFeatureAssociation')
                //           ->where('RestaurantId', $id)
                //           ->update($fData);
                //   } else {
                      DB::table('RestaurantFeatureAssociation')->insert($fData);
                 // }
              }
          }else{
            if (!$getrestfeat->isEmpty()) {
                if ($request->input('featureitem') == "") {
                   foreach($getrestfeat as $value){
                    DB::table('RestaurantFeatureAssociation')
                    ->where('RestaurantFeatureAssociationId', $value->RestaurantFeatureAssociationId)
                    ->delete();
                   }
                }
            }
          }
          //end

          
          //start 
          $getrestdeit= DB::table('RestaurantSpecialDietAssociation')->where('RestaurantId', $id)->get();

          $dietry_item = $request->input('dietryitems');
          $dietry_data = [];
     
          if ($request->input('dietryitems') != "") {
            DB::table('RestaurantSpecialDietAssociation')
            ->where('RestaurantId', $id)
            ->delete();
              for ($i = 0; $i < count($dietry_item); $i++) {
                  $dtdata = DB::table('RestaurantSpecialDiet')->where('Name', $dietry_item[$i])->get();
              
                  $dtrData = [
                      'RestaurantSpecialDietId' => $dtdata[0]->RestaurantSpecialDietId,
                      'RestaurantId' => $lastinsertedId,
                  ];
               
                //   if (!$getrestfeat->isEmpty()) {
                //       // Update each row separately using the where condition for RestaurantFeatureId
                //       DB::table('RestaurantFeatureAssociation')
                //           ->where('RestaurantId', $id)
                //           ->update($fData);
                //   } else {
                      DB::table('RestaurantSpecialDietAssociation')->insert($dtrData);
                 // }
              }
          }else{
            if (!$getrestdeit->isEmpty()) {
                if ($request->input('dietryitems') == "") {
                   foreach($getrestdeit as $value){
                    DB::table('RestaurantFeatureAssociation')
                    ->where('RestaurantFeatureAssociationId', $value->RestaurantSpecialDietAssociationId)
                    ->delete();
                   }
                }
            }
          }
          //end

        return redirect()->route('restaurant')
        ->with('success','Restaurant Updated successfully.');   
    }

    public function searchDietary(request $request){
        $search = $request->get('val');

        $result = array();
    
        $query = DB::table('RestaurantSpecialDiet')           
            ->where('Name', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();
    
        foreach ($query as $loc) {
            $result[] = [               
                'value' => $loc->Name,               
            ];
        }
    
        return response()->json($result);
    }
	
	// faq
	
  public function restaurant_faq($id){
        $getfaq = DB::table('RestaurantQuestion')
        ->Leftjoin('Restaurant','Restaurant.RestaurantId', '=' ,'RestaurantQuestion.RestaurantId')
        ->select('RestaurantQuestion.*','Restaurant.Title')
        ->where('RestaurantQuestion.RestaurantId',$id)->get();
      
        return view('restaurant.edit_restaurant_faq',['getfaq'=>$getfaq]);
    }

	
	public function add_rest_faq(request $request){
     
       $question = $request->get('checkboxText');
       $RestaurantId = $request->get('restid');
       $data = array(
            'Question'=>$question,
            'Answer'=>null,
            'RestaurantId'=>$RestaurantId,
            'IsActive' => 1,
            'CreatedDate' => now(),
       );
       DB::table('RestaurantQuestion')->insert($data);

       $getfaq = DB::table('RestaurantQuestion')
       ->Leftjoin('Restaurant','Restaurant.RestaurantId', '=' ,'RestaurantQuestion.RestaurantId')
       ->select('RestaurantQuestion.*','Restaurant.Title')
       ->where('RestaurantQuestion.RestaurantId',$RestaurantId)->get();
      
       return view('restaurant.get_faqsucess',['getfaq'=>$getfaq]);
    
    }

    public function update_rest_faq(request $request){
        $restid = $request->get('restid');
        $id =  $request->get('faqId');
       // $currentDate = Carbon::today()->toDateString();
        $data = array(
            'Question' => $request->get('question'),
            'Answer' => $request->get('answer'),
            'IsActive' => 1,
            'CreatedDate' => now(),
        );
        
      DB::table('RestaurantQuestion')->where('RestaurantQuestionId',$id)->update($data);
        
   

       $getfaq = DB::table('RestaurantQuestion')
       ->Leftjoin('Restaurant','Restaurant.RestaurantId', '=' ,'RestaurantQuestion.RestaurantId')
       ->select('RestaurantQuestion.*','Restaurant.Title')
       ->where('RestaurantQuestion.RestaurantId',$restid)->get();
      
       return view('restaurant.get_faqsucess',['getfaq'=>$getfaq]);

    }
    //end edit faq

    public function restaurant_cuisine($id){
        
        $getcus = DB::table('RestaurantCuisineAssociation')
        ->join('RestaurantCuisine','RestaurantCuisine.RestaurantCuisineId', '=' ,'RestaurantCuisineAssociation.RestaurantCuisineId')
        ->select('RestaurantCuisineAssociation.*','RestaurantCuisine.*')
        ->where('RestaurantCuisineAssociation.RestaurantId',$id)->get();

        $getname = DB::table('Restaurant')->where('RestaurantId',$id)->get();
        $getcuisine = DB::table('RestaurantCuisine')->get();

        $getfaqIds = $getcus->pluck('RestaurantCuisineId')->toArray();

        // Retrieve records from RestaurantCuisine while excluding the $getfaqIds
        $restaurantCuisine = DB::table('RestaurantCuisine')
            ->whereNotIn('RestaurantCuisineId', $getfaqIds)
            ->get();

        return view('restaurant.restaurant_cuisine',['getcus'=>$getcus,'getname'=>$getname,'getcuisine'=>$restaurantCuisine]);
    }

    public function addcuisine(request $request){
        $checkedValues = $request->get('checkedValues');
        $id = $request->get('id');
        foreach($checkedValues as $csid){
            $data = array(
                'RestaurantCuisineId' => $csid,
                'RestaurantId'=>$id
            );
            DB::table('RestaurantCuisineAssociation')->insert($data);
        }

        $getcus = DB::table('RestaurantCuisineAssociation')
        ->join('RestaurantCuisine','RestaurantCuisine.RestaurantCuisineId', '=' ,'RestaurantCuisineAssociation.RestaurantCuisineId')
        ->select('RestaurantCuisineAssociation.*','RestaurantCuisine.*')
        ->where('RestaurantCuisineAssociation.RestaurantId',$id)->get();

        $getname = DB::table('Restaurant')->where('RestaurantId',$id)->get();
        $getcuisine = DB::table('RestaurantCuisine')->get();

        $getfaqIds = $getcus->pluck('RestaurantCuisineId')->toArray();

        // Retrieve records from RestaurantCuisine while excluding the $getfaqIds
        $restaurantCuisine = DB::table('RestaurantCuisine')
            ->whereNotIn('RestaurantCuisineId', $getfaqIds)
            ->get();

        return view('restaurant.filter_cuisine',['getcus'=>$getcus,'getname'=>$getname,'getcuisine'=>$restaurantCuisine]);
       
    }

    public function deleterestaurantcus(request $request){
        
      $restval =  $request->get('restid');
      $id = $request->get('id');
      DB::table('RestaurantCuisineAssociation')->where('RestaurantCuisineAssociationId',$id)->delete();

      $getcus = DB::table('RestaurantCuisineAssociation')
      ->join('RestaurantCuisine','RestaurantCuisine.RestaurantCuisineId', '=' ,'RestaurantCuisineAssociation.RestaurantCuisineId')
      ->select('RestaurantCuisineAssociation.*','RestaurantCuisine.*')
      ->where('RestaurantCuisineAssociation.RestaurantId',$restval)->get();

      $getname = DB::table('Restaurant')->where('RestaurantId',$restval)->get();
      $getcuisine = DB::table('RestaurantCuisine')->get();

      $getfaqIds = $getcus->pluck('RestaurantCuisineId')->toArray();
    
      $restaurantCuisine = DB::table('RestaurantCuisine')
          ->whereNotIn('RestaurantCuisineId', $getfaqIds)
          ->get();

      return view('restaurant.filter_cuisine',['getcus'=>$getcus,'getname'=>$getname,'getcuisine'=>$restaurantCuisine]);
    }
	
	//review

    public function editrest_reviews($id){
        
        $getrest = DB::table('Restaurant')->where('RestaurantId',$id)->get();
        $getreview = DB::table('RestaurantReview')->where('RestaurantId',$id)->get();
        return view('restaurant.editrest_reviews',['restreview'=>$getreview,'getrest'=>$getrest]);
    }

    public function update_resteview(request $request){
        $id = $request->get('id');
        $restid = $request->get('restid');

         DB::table('RestaurantReview')->where('RestaurantReviewId',$id)->update(['IsApprove'=>$request->get('value')]);


         $getreviews =  DB::table('RestaurantReview')->where('RestaurantId',$restid)->where('IsApprove',$request->get('value'))->get();     
         $getrest = DB::table('Restaurant')->where('RestaurantId',$restid)->get();

        return view('restaurant.sortRestReview',['restreview'=>$getreviews,'getrest'=>$getrest,'val'=>$request->get('value')]);
    
    }
   
    public function ftrrestrewview(request $request){
        $val1 = $request->get('val');
        $id = $request->get('id');

        if (strpos($val1, ',') !== false) {
            $explodedValues = explode(',', $val1);
             $val1 =  $explodedValues[0];
             $val2 =  $explodedValues[1];
             $val3 =  $explodedValues[2];
             $getreviews =  DB::table('RestaurantReview')->where('RestaurantId',$id)->where('IsApprove',$val1)->orWhere('IsApprove',$val2)->orWhere('IsApprove',$val3)->get();
        } else {

            $getreviews =  DB::table('RestaurantReview')->where('RestaurantId',$id)->where('IsApprove',$val1)->get();
        }
       
        
        return view('restaurant.sortRestReview',['restreview'=>$getreviews,'val'=>$val1]);  
    }

    public function sortRestReview(request $request){
        if($request->get('val') != ""){
            $orderby = $request->get('val');
        }else{
            $orderby = "desc";
        }
        $id = $request->get('id');
        $getrest = DB::table('Restaurant')->where('RestaurantId',$id)->get();

        if(!empty($request->get('filter_option'))){
           
            $filter_option = $request->get('filter_option');
            $getreviews =  DB::table('RestaurantReview')->where('RestaurantId',$id)->where('IsApprove',$filter_option)->orderby('RestaurantReviewId',$orderby)->get();
        }else{
           $filter_option = 0;
            $getreviews =  DB::table('RestaurantReview')->where('RestaurantId',$id)->orderby('RestaurantReviewId',$orderby)->where('IsApprove',$filter_option)->get();
        }
     
       // $hotel_id = $gethid[0]->hotelid;
    
        
        return view('restaurant.sortRestReview',['restreview'=>$getreviews,'getrest'=>$getrest,'val'=>$filter_option]);
    }

       
    public function filterrestbyid(request $request){
        
        $val = $request->get('val');
        $getreviews =  DB::table('RestaurantReview')->where(function($query) use ($val) {
            if (strlen($val) <= 1) {
                $query->where('RestaurantReviewId', 'LIKE', '%' . $val . '%');
            } else {
                $query->where('RestaurantReviewId', $val);
            }
        })
        ->limit(3)->get();
         return view('restaurant.sortRestReview',['restreview'=>$getreviews]);  
        
    }  

    public function delete__rest_review(request $request){

        $id = $request->get('id');
        $restid = $request->get('restid');

   //   return  $records = DB::table('RestaurantReview')->withTrashed()->get();
      DB::table('RestaurantReview')->where('RestaurantReviewId', $id)->update(['deleted_at' => now()]);
      
     //   DB::table('RestaurantReview')->where('RestaurantReviewId',$id)->delete();

         $getreviews =  DB::table('RestaurantReview')->where('RestaurantId',$restid)->where('IsApprove',3)->orWhere('IsApprove',4)->orWhere('IsApprove',5)->get();     
         $getrest = DB::table('Restaurant')->where('RestaurantId',$restid)->get();

        return view('restaurant.sortRestReview',['restreview'=>$getreviews,'getrest'=>$getrest,'val'=>$request->get('value')]);
    }
	
	// Edit Review Images

    public function edit_review_Image($id){

        $getrest = DB::table('Restaurant')->where('RestaurantId',$id)->get();
        $getreview = DB::table('RestaurantImages')->where('RestaurantId',$id)->get();

        return view('restaurant.edit_review_Image',['restreview'=>$getreview,'getrest'=>$getrest]);
  
    }

    public function upload_rest_review_Image(Request $request,$id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
      
        $imagetype = $request->get('image_type');
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('review-images'), $imageName);


            if($imagetype == 1){
                $data = array(
                    'RestaurantId'=>$id,
                    'Image'=>$imageName,
                    'IsPrimary'=>1
                );
            }else{
                $data = array(
                    'RestaurantId'=>$id,
                    'Image'=>$imageName
                );
            }
           
            $getreview = DB::table('RestaurantImages')->insert($data);

            return redirect()->route('edit_review_Image', ['id' => $id])->with('success', 'Image uploaded successfully.');


        }
 
    }
    public function delete_review_image(request $request){
      $id = $request->get('id');
      $restid =  $request->get('restid');

      $image =  DB::table('RestaurantImages')->where('id',$id)->get();
          $image[0]->Image;
     
      if ($image) {

        $imagePath = public_path('review-images/' . $image[0]->Image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
            DB::table('RestaurantImages')->where('id', $id)->delete();

            $getrest = DB::table('Restaurant')->where('RestaurantId',$restid)->get();
              $getreview = DB::table('RestaurantImages')->where('RestaurantId',$restid)->get();
    
            
            return view('restaurant.filter_review_imges',['restreview'=>$getreview,'getrest'=>$getrest]);
        } else {
            return response()->json(['message' => 'Image not found'], 403);
        }

      }
  
      return response()->json(['message' => 'Image not found'], 404);
    }

           
    public function filterimagebyid(request $request){
        $restid = $request->get('restid');
        $val = $request->get('val');
        if($val != ""){
            $getreviews =  DB::table('RestaurantImages')->where(function($query) use ($val) {
                if (strlen($val) <= 1) {
                   // $query->where('Id', 'LIKE', '%' . $val . '%');
                    $query->where('Id', $val);
                } else {
                    $query->where('Id', $val);
                }
            })
            ->limit(2)->get();
        }else{
            $getreviews =  DB::table('RestaurantImages')->where('RestaurantId', $restid)
            ->get();
        }
        $getrest = DB::table('Restaurant')->where('RestaurantId',$restid)->get();

        return view('restaurant.filter_review_imges',['restreview'=>$getreviews,'getrest'=>$getrest]);  
        
    }  
	
}
