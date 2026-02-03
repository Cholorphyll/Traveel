<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
class HotelController extends Controller
{
 
  public function addHotleListingFaq(request $request){
        $bhv = 0;
        $expervar = 0;
        $cheapestHOt =0;
        $topcheap = 0;
        $pool = 0;
		$bthotel = 0;
            $locationid = $request->get('locationid');
	        $slugid = $request->get('slugid');
            $getloc = DB::table('TPLocations')->select('cityName')
            ->where('id',$locationid)
            ->get();

	  		
            if(!$getloc->isEmpty()){
                $lname = $getloc[0]->cityName;  

				  //quest 6 start
                $quest = "Best Hotel in $lname?";
                $exist_besth = DB::table('TPLocationFaq')
                ->where('location_id', $locationid)
                ->where('Question', $quest)
                ->get();
				
            
  //return print_r($exist_besth);
   if ($exist_besth->isEmpty()) {
       $topbest = 1;
      
       $bthotel =1;
        $getbesthotel = DB::table('TPHotel as h')
        ->where('h.location_id', $locationid) 
      ->join('TPLocations as t','t.id','=','h.location_id') 
      //->where('h.id', 2585489) 
       ->select('h.id','h.location_id','h.slug','h.name','h.about','n.Name as nbname','n.NeighborhoodId','n.slug as nbslug','n.LocationID','hn.*','t.cityName','h.rating','h.distance','h.cntRooms','h.amenities','h.shortFacilities','h.pricefrom','h.Latitude','h.longnitude','h.hotelid','h.rating','h.location_score') 
     //  ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
     //  ->join('Location as l','l.LocationId','=','t.Tid')  
       ->leftJoin('TPHotelNeighbourhood as hn','hn.hotelid','=','h.hotelid') 
       ->leftJoin('Neighborhood as n','n.NeighborhoodId','=','hn.NeighborhoodId')       
       ->where('h.pricefrom','!=',0)
       ->where('h.distance','!=',null)
       ->where('h.location_score','!=',null)
       
       ->where(function($query) {
          $query->where('h.amenities','!=', "")
              ->where('h.shortFacilities','!=', "")
              ->orWhere('h.amenities','like', '%Swimming pool%')
              ->orWhere('h.amenities','like', '%gym%')
              ->orWhere('h.amenities','like', '%Babysitting%');
              
       })
       ->orderByDesc('h.rating') 
       ->limit(1)
       ->get();
       

    //  return print_r($getbesthotel);
   
       $bthotellink = [];
       if (!$getbesthotel->isEmpty()) {
        
        $hotelid =$getbesthotel[0]->hotelid;
           foreach ($getbesthotel as $val) {
               $bthotellink[] = [
                   'name' => $val->name,
                   'url' => $slugid.'-'.$val->id.'-'.$val->slug,
                   'nb_name'=>$val->nbname,
                   'nburl'=> $val->LocationID.'-'.$val->NeighborhoodId.'-'.$val->nbslug,
               ];
           }

           $currentAnswer ="";

       $cityname = $getbesthotel[0]->cityName;
    
       $nbname =  $getbesthotel[0]->nbname;
       $rating =  $getbesthotel[0]->rating;
       $distance =  $getbesthotel[0]->distance;
       $cntRooms =  $getbesthotel[0]->cntRooms;
       $amenities =  $getbesthotel[0]->amenities;
       $location_score =  $getbesthotel[0]->location_score;
       
       if($nbname != ""){
        $currentAnswer .= "Located in $nbname, $cityname ,";
       }else{
        $currentAnswer .= "Located in $cityname, ";
       }             
       $currentAnswer .= $getbesthotel[0]->name;
       $ct = 0;
       if($distance != ""){
            
        if($distance < 1){
            $currentAnswer .= ' right in the middle of the city,';
            $ct = 1;
          }else if($distance < 3){
          // $currentAnswer .=  ' is conveniently located close to the city center';
           $currentAnswer .= " is $distance km away from the city center,";
           $ct = 1;
          }else{
            $currentAnswer .= " is $distance km away from the city center,";
            $ct = 1;
          } 
       }
     


            $Latitude = $getbesthotel[0]->Latitude; 
            $longnitude = $getbesthotel[0]->longnitude; 
            if($Latitude !="" && $longnitude != "" ){
            $searchradius = 10;
            $popular_landmark = DB::table("Sight")
            ->join('Location as l','l.LocationId','=','Sight.LocationId') 
                ->select('Sight.SightId', 'Sight.Title', 'Sight.LocationId', 'Sight.Slug','l.slugid',
                    DB::raw("6371 * acos(cos(radians(" . $Latitude . "))
                        * cos(radians(Sight.Latitude))
                        * cos(radians(Sight.Longitude) - radians(" . $longnitude . "))
                        + sin(radians(" . $Latitude . "))
                        * sin(radians(Sight.Latitude))) AS distance"))
                ->groupBy("Sight.SightId")
                ->having('distance', '<=', $searchradius)
                
                ->orderBy('distance')
                ->limit(1)
                
                ->get();
    
            if (!$popular_landmark->isEmpty()) {
                $bthotellink[] = [        
                    'sight_name'=>$popular_landmark[0]->Title,
                    'sight_url'=>  $popular_landmark[0]->slugid.'-'.$popular_landmark[0]->SightId.'-'. $popular_landmark[0]->Slug,
                ];
                $distanceval = round($popular_landmark[0]->distance, 2);
                if($ct = 0){
                // $currentAnswer .=" and ";
            
                    if($distanceval <= 1){
                        $currentAnswer .=   "is at walking distance from ".$popular_landmark[0]->Title;
                    }elseif($distanceval > 3){
                        $currentAnswer .=   "is ".$popular_landmark[0]->distance." km from ".$popular_landmark[0]->Title;
                    }
                }else{
                    if($distanceval <= 1){
                        $currentAnswer .=   "and walking distance from ".$popular_landmark[0]->Title;
                    }elseif($distanceval > 3){
                        $currentAnswer .=   "and ".$popular_landmark[0]->distance." km from ".$popular_landmark[0]->Title;
                    }
                }
             }
          }
          //return  $currentAnswer;

            if($location_score != ""){
                if($location_score >= 8.5){
                $currentAnswer .=  " has an incredible rating of $location_score.";
                }else{
                    $currentAnswer .= " has rating of $location_score.";
                }
            }
        
                //    The hotel has (number) rooms with balconies (if any), family rooms/ suites
            if($cntRooms != null && $cntRooms != 0 ){
                $currentAnswer .= " The hotel has $cntRooms rooms"; 
            }
            $amenitiesArray = explode(', ', $getbesthotel[0]->amenities);


            $getroomdesc = DB::table('TPRoomtype')->where('hotelid',$hotelid)->get();
            if(!$getroomdesc->isEmpty()){
                    $roomdescArray = json_decode($getroomdesc[0]->Roomdesc, true);
                $bal =0;
                    if ($roomdescArray && isset($roomdescArray['balcony']) && $roomdescArray['balcony'] === true) {
                        $currentAnswer .=  "with balconies";
                        $bal =1;
                    }

                    if(in_array("attached washroom", $amenitiesArray)){ 
                        $currentAnswer .=  "with attached washroom";
                        $bal =1;
                    }
        
                
                    if ($roomdescArray && isset($roomdescArray['Standard Double room (full double bed)']) ) {
                        if($bal == 1){                    
                            $currentAnswer .=  ", ";
                        }
                        $currentAnswer .=  "family rooms";
                        $bal =1;
                    }

                
                }

                $shortFacilities = $getbesthotel[0]->shortFacilities;
                $shortFacilitiesdt = [];
                if(!empty($shortFacilities)){
            
                    $shortFacilitiesArray = json_decode($shortFacilities);
            
                    // Concatenate the values into a comma-separated list
                    $shortFacilitiesdt = implode(", ", $shortFacilitiesArray);
                }

        

            $shortFacilities = $getbesthotel[0]->shortFacilities;
            $shortFacilitiesdtv = [];
        if(!empty($shortFacilities)){

            $shortFacilitiesdtv = json_decode($shortFacilities);
        }
            

            $searchAmenities = ["Jacuzzi", "Private pool","Bathtub","Swimming pool","pool"];              
            $amenityFound = false;        
            $pool ="";
            foreach ($searchAmenities as $amenity) {
                
                if (in_array($amenity, $amenitiesArray)) {
                    $pool = $amenity;
                    $amenityFound = true; 
                    break; 
                }
            }
            
            }
            if(!empty($shortFacilitiesArray)){
                foreach ($shortFacilitiesdtv as $val) {
                
                    if (in_array($val, $amenitiesArray)) {
                        $pool = $val;
                        $amenityFound = true; 
                        break; 
                    }
            }
            $pl = 0;
            if($pool !=""){
                $currentAnswer .= "with the facilities of $pool."; 
                $pl =1;
            }
            if($pl == 0){
                $currentAnswer .= ".";
            }
              $shortFacilities = $getbesthotel[0]->shortFacilities;
				$shortFacilitiesdt = ""; 
				if (!empty($shortFacilities)) {
					$shortFacilitiesArray = json_decode($shortFacilities);           

					$wifiEncountered = false;  
					foreach ($shortFacilitiesArray as $key => $facility) {
						if (stripos($facility, 'wi-fi') !== false) {
							$wifiEncountered = true;

							$shortFacilitiesArray[$key] = "wi-fi";
						}
					}

					if ($wifiEncountered) {          
						$shortFacilitiesArray = array_unique($shortFacilitiesArray);
						$shortFacilitiesdt = implode(", ", $shortFacilitiesArray);
					} else {

						$shortFacilitiesdt = implode(", ", $shortFacilitiesArray);
					}
				}
    
                    
            // Show the concatenated list
                    $facil = 0;
                    if(!empty($shortFacilitiesdt)){
                        $currentAnswer .=" It offers facilities, such as  $shortFacilitiesdt ";
                        $facil = 1;
                    }
                    
                    $smoking = ["smoking", "non-smoking rooms"];              
                    $s = false;        
                    $smokingval ="";
                    foreach ($searchAmenities as $amenity) {         
                        if (in_array($amenity, $smoking)) {
                            $smokingval = $amenity;
                            $s = true; 
                            break; 
                        }
                    }

                    if($s == false){
                        if(!empty($shortFacilitiesArray)){
                            foreach ($shortFacilitiesArray as $sf) {
                        
                                if (in_array($sf, $smoking)) {
                                    $smokingval = $sf;
                                    $s = true; 
                                    break; 
                                }
                            }
                        }
                        
                    }
                    
                    $pricefrom = $getbesthotel[0]->pricefrom;
                    if($pricefrom !=""){
                        $currentAnswer .="for $".$pricefrom.',';
                    }
                    if($smokingval !=""){
                    $currentAnswer .=" and $smokingval,  ";
                    }

                    if($facil == 1){
                        $currentAnswer .=" to guarantee guests a pleasant and comfortable stay. ";
                    }
                
                    $barfac = ["Restaurant/cafe", "Bar",'lounge','rooftop bars'];              
                        
                    $bar = [];
                    foreach ($searchAmenities as $amenity) {         
                        if (in_array($amenity, $barfac)) {
                            $bar[] = $amenity;         
                        
                        }
                    }
                    
                    $barsval = 0;
                
                    if(!empty($bar)){
                    
                        $barval = implode(", ", $bar);
                        $currentAnswer .= "The hotel has $barval";
                        $barsval =1;
                    }

                    if(in_array("gym", $amenitiesArray)){                
                        $currentAnswer .=  "and fitness facilities like gym.";
                    }
                    if($barsval ==1){                
                        $currentAnswer .=  ".";
                    }
                    $babys=0;
                    $dis=0;
                    if(in_array("Babysitting", $amenitiesArray)){                
                            $currentAnswer .=  " Families traveling with young children can take advantage of babysitting services";
                            $babys=1;
                    }
                
                    if(in_array("Disabled facilities", $amenitiesArray)){    
                        if($babys == 1){
                            $currentAnswer .=  ",";
                        }        
                        $currentAnswer .=  "while accessible rooms cater to guests with disabilities,";
                        $dis=1;
                    }
                    if($babys== 1 || $dis== 1){
                        $currentAnswer .=  "ensuring that everyone can enjoy a comfortable and memorable stay.";
                    }
                
                
                
            //  $getroomdesc = DB::table('TPRoomtype')->where('hotelid',$hotelid)->get();
                
                if(!$getroomdesc->isEmpty()){
                    $roomdescArray = json_decode($getroomdesc[0]->Roomdesc, true);
                
                if ($roomdescArray && isset($roomdescArray['refundable']) && $roomdescArray['refundable'] === true) {
                    $currentAnswer .=  " Guests can savor adaptable amenities like free cancellation.";
                }

            //  return $currentAnswer;
        }


            
        
                
        //    return $currentAnswer ;

            
        
            
           
            if($currentAnswer !=""){
                $besth = array(
                    'location_id'=>$locationid,
                    'Question'=>'Best Hotel in '.$lname.'?',
                    'Answer' => $currentAnswer,
                    'Listing'=>json_encode($bthotellink),
                    'CreatedDate' => now(),
                    'IsActive'=>1,
                );
            DB::table('TPLocationFaq')->insert($besth);
            }
            

        //     return  $answer;

        
        }
        }
            //quest 6 end
                //quest 1
                $exist_bestH = DB::table('TPLocationFaq')
                ->where('location_id', $locationid)
                ->where('Question', 'What are the best hotels in '. $lname.' ?')
                ->get();
              
                if ($exist_bestH->isEmpty()) {
                    $bhv = 1;
                
                    $getbesthotels= DB::table('TPHotel as h')
                   // ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
                   // ->join('Location as l','l.LocationId','=','t.Tid') 
                    ->where('h.location_id', $locationid)
                    ->select('h.id','h.location_id','h.slug','h.name','h.about')  
                    ->where('h.location_score', '>', 8) 
                    ->orderBy(DB::raw('location_score * stars'), 'desc') 
                    ->limit(5)
                    ->get();
                
                    $best_hotel = [];
                    if (!$getbesthotels->isEmpty()) {
                        foreach ($getbesthotels as $bh) {
                            $best_hotel[] = [
                                'name' => $bh->name,
                                'url' => $slugid.'-'.$bh->id.'-'.$bh->slug,
                                'about' =>  $bh->about,
                                
                            ];
                        }
                    }
                    if(!empty($best_hotel)){
                        $bestharray = array(
                            'location_id'=>$locationid,
                            'Question'=>'What are the best hotels in '. $lname.' ?',
                            'Answer' =>  $lname.' has a number of great hotels, here are some of the best hotels in based on reviews by members of the Travell community',
                            'Listing'=>json_encode($best_hotel),
                            'CreatedDate' => now(),
                            'IsActive'=>1,
                        );
                       DB::table('TPLocationFaq')->insert($bestharray);
                    }
                }

            //quest 1 end

             //quest 2
              
        
          
                $exist_exp_hotels = DB::table('TPLocationFaq')
                ->where('location_id', $locationid)
                ->where('Question', 'What are the most expensive hotels in '. $lname.' ?')
                ->get();
                if ($exist_exp_hotels->isEmpty()) {
                    $expervar = 1;
                     $getmostexpensive = DB::table('TPHotel as h')
                     //->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
                    // ->join('Location as l','l.LocationId','=','t.Tid') 
                     ->where('h.location_id', $locationid)
                     ->select('h.id','h.location_id','h.slug','h.name','h.about')  
                    ->orderby('h.pricefrom','desc') 
                    ->limit(5)
                    ->get();

                
                    $expen_hotel = [];
                    if (!$getmostexpensive->isEmpty()) {
                        foreach ($getmostexpensive as $exp) {
                            $expen_hotel[] = [
                                'name' => $exp->name,
                                'url' =>$slugid.'-'.$exp->id.'-'.$exp->slug,
                                'about' =>$exp->about,
                            ];
                        }
                    }
                    if(!empty($expen_hotel)){
                        $exphotels = array(
                            'location_id'=>$locationid,
                            'Question'=>'What are the most expensive hotels in '. $lname.' ?',
                            'Answer' => 'Here are some of the most expensive hotels in '. $lname,
                            'Listing'=>json_encode($expen_hotel),
                            'CreatedDate' => now(),
                            'IsActive'=>1,
                        );
                       DB::table('TPLocationFaq')->insert($exphotels);
                    }
                }

            //quest 2 end
              //quest 3
              
        
          
              $averpriceexist = DB::table('TPLocationFaq')
              ->where('location_id', $locationid)
              ->where('Question', 'What is the average price of a hotel room in '. $lname.' ?')
              ->get();

              if ($averpriceexist->isEmpty()) {
                  $expervar = 1;
                  $result = DB::table('TPHotel as h')
                    // ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
                   // ->join('Location as l','l.LocationId','=','t.Tid') 
                    ->where('h.location_id', $locationid)
                    ->select('h.id', 'h.location_id', 'h.slug', 'h.name', 'h.about', 'h.pricefrom', 'h.stars')
                    ->orderBy('h.pricefrom', 'desc')
                    ->where('h.pricefrom', '>', 0)
                    ->get();
                    $hotelpriceurl =[];
                  
                if (!$result->isEmpty()) {
                    $mostExpensive = $result->first();
                    $leastExpensive = $result->last();
                      
                    $mostExpensiveName = $mostExpensive->name;
                    $mostExpensivePrice = $mostExpensive->pricefrom;

                    $hotelpriceurl[] = [
                        $mostExpensiveName =>  $mostExpensive->location_id.'-'.$mostExpensive->id.'-'.$mostExpensive->slug,
                        
                    ];

                     $leastExpensiveName    = $leastExpensive->name;
                    $leastExpensivePrice = $leastExpensive->pricefrom;
                    $hotelpriceurl[] = [
                        $leastExpensiveName =>  $leastExpensive->location_id.'-'.$leastExpensive->id.'-'.$leastExpensive->slug,
                        
                    ];
                   

                    $averagePrices = DB::table('TPHotel as h')
                  //  ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
                 //   ->join('Location as l','l.LocationId','=','t.Tid') 
                    ->select('h.id', 'h.location_id', 'h.slug', 'h.name','h.stars', 'h.pricefrom')
                    ->where('h.location_id', $locationid)
                    ->where('h.pricefrom', '>', 0)
                    ->whereIn('h.stars', [3, 4, 5])
                    ->get();

                 
           
                    $threeStarHotel = null;
                    $fourStarHotel = null;
                    $fiveStarHotel = null;

                    $fourStarprice = "";
                    $threeStarHotel = "";
                    $fiveStarprice = "";

                    $allCategoriesFound = false;

                  
                    foreach ($averagePrices as $averagePrice) {
                        $star = $averagePrice->stars;
                       $price = $averagePrice->pricefrom;
                        if ($star == 3 && !$threeStarHotel ) {
                            $threeStarName = $averagePrice->name;
                            $threeStarprice = $averagePrice->pricefrom;

                            $hotelpriceurl[] = [
                                '3 star' =>$slugid.'-'.$averagePrice->id.'-'.$averagePrice->slug,
                                
                            ];
                            $threeStarHotel =true;

                        } elseif ($star == 4 && !$fourStarHotel ) {
                            $fourStarName = $averagePrice->name;
                            $fourStarprice = $averagePrice->pricefrom;

                            $hotelpriceurl[] = [
                                '4 star' =>  $slugid.'-'.$averagePrice->id.'-'.$averagePrice->slug,
                                
                            ];
                            $fourStarHotel = true;

                        } elseif ($star == 5 && !$fiveStarHotel ) {
                            $fiveStarName = $averagePrice->name;
                            $fiveStarprice = $averagePrice->pricefrom;
                            
                            $hotelpriceurl[] = [
                                '5 star' =>  $slugid.'-'.$averagePrice->id.'-'.$averagePrice->slug,
                                
                            ];
                            $fiveStarHotel = true;
                        }
                    
                      
                        if ($threeStarHotel && $fourStarHotel && $fiveStarHotel) {
                            $allCategoriesFound = true;
                            break;
                        }
                    }
                            
               
          

                   


         if(!isset($leastExpensiveName)){   
                
           if($leastExpensiveName !="" && $leastExpensivePrice !="" && $mostExpensivePrice !="" && $mostExpensiveName !=""  && $fourStarprice != "" &&  $threeStarprice != "" &&  $fiveStarprice != ""){

                $answer ="From Travell's 3500+ hotel recomdendations, the cheapest stay you'll find at ". $leastExpensiveName." for $". $leastExpensivePrice." a night, while the most expensive costs $".$mostExpensivePrice." a night at ".$mostExpensiveName ;

                $answer.= ". On average, its costs $".$fourStarprice." a night at a 3 star hotel and $".$threeStarprice." a night for a 4 star hotel.A stay at a 5 star property would cost $".$fiveStarprice." a night on average.";

                // if ($threeStarHotel != "") {
                //     $answer.= $threeStarprice." a night for a 4 star hotel.";
                // }
                // if ($fiveStarprice != "") {
                //     $answer.="A stay at a 5 star property would cost ".$fiveStarprice." a night on average.";
                // }
                //    return $hotelpriceurl;
              // 
                    $exphotels = array(
                        'location_id'=>$locationid,
                        'Question'=>'What is the average price of a hotel room in '. $lname.' ?',
                        'Answer' => $answer,
                        'Listing'=>json_encode($hotelpriceurl),
                        'CreatedDate' => now(),
                        'IsActive'=>1,
                    );
                   DB::table('TPLocationFaq')->insert($exphotels);
              
       }
    }
              
            
    }
          

             
              }

          //quest 3 end

           //quest 4 start

           $exist_cheap_hotels = DB::table('TPLocationFaq')
           ->where('location_id', $locationid)
           ->where('Question', "What are the top cheap hotels in $lname?")
           ->get();

           if ($exist_cheap_hotels->isEmpty()) {
               $topcheap = 1;
                $getcheapesthotel = DB::table('TPHotel as h')
              //  ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
               // ->join('Location as l','l.LocationId','=','t.Tid')
                ->join('TPHotelNeighbourhood as hn','hn.hotelid','=','h.hotelid') 
                ->leftjoin('Neighborhood as n','n.NeighborhoodId','=','hn.NeighborhoodId') 
                ->where('location_id', $locationid)
                ->select('h.id','h.location_id','h.slug','h.name','h.about','n.Name as nbname','n.NeighborhoodId','n.slug as nbslug','n.LocationID','hn.*') 
             
               ->orderby('h.pricefrom','asc') 
               ->limit(9)
               ->get();

               $getlocation = DB::table('TPLocations')
               ->select('cityName') 
               ->where('id', $locationid)
               ->get();

          //   return  print_r($getcheapesthotel);
           
               $cheaphotel = [];
               if (!$getcheapesthotel->isEmpty()) {
                   foreach ($getcheapesthotel as $val) {
                       $cheaphotel[] = [
                           'name' => $val->name,
                           'url' =>$slugid.'-'.$val->id.'-'.$val->slug,
                           'nb_name'=>$val->nbname,
                           'nburl'=> $val->LocationID.'-'.$val->NeighborhoodId.'-'.$val->nbslug,
                       ];
                   }

                   $hotelNames = implode(', ', array_map(function ($hotel) {
                    return $hotel['name'] . ' (' . $hotel['nb_name'] . ')';
                }, $cheaphotel));

               $cityname = $getlocation[0]->cityName;
                $answer = "As per travell, $cityname offers a wide variety of hotels within a reasonable price range. Some of the best cheap hotels are $hotelNames.";
               }

          //     return  $answer;

               if(!empty($cheaphotel)){
                   $cphotels = array(
                       'location_id'=>$locationid,
                       'Question'=>"What are the top cheap hotels in $lname?",
                       'Answer' => $answer,
                       'Listing'=>json_encode($cheaphotel),
                       'CreatedDate' => now(),
                       'IsActive'=>1,
                   );
                  DB::table('TPLocationFaq')->insert($cphotels);
               }
           }
            //quest 4 end
              //quest 5 start

           $exist_pool_hotels = DB::table('TPLocationFaq')
           ->where('location_id', $locationid)
           ->where('Question', "What are the best hotels in $lname with outdoor pools?")
           ->get();
      

           if ($exist_pool_hotels->isEmpty()) {
               $pool = 1;
                $getpoolhotel = DB::table('TPHotel as h')
              //  ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
               // ->join('Location as l','l.LocationId','=','t.Tid')           
                ->select('h.id','h.location_id','h.slug','h.name','h.about','n.Name as nbname','n.NeighborhoodId','n.slug as nbslug','n.LocationID','hn.*','h.pricefrom','h.stars','h.amenities','h.location_score') 
                ->leftjoin('TPHotelNeighbourhood as hn','hn.hotelid','=','h.hotelid') 
                ->leftjoin('Neighborhood as n','n.NeighborhoodId','=','hn.NeighborhoodId') 
                ->where('location_id', $locationid)
                ->whereRaw("h.amenities LIKE '%Swimming Pool%'")
                ->whereNotNull('h.pricefrom') 
                ->whereNotNull('h.amenities') 
                ->whereNotNull('h.location_score') 
                ->orderby('h.stars','desc') 
                ->limit(3)
                ->get();
             
               $getlocation = DB::table('TPLocations')
               ->select('cityName') 
               ->where('id', $locationid)
               ->get();
       
               $currentAnswer = "";

           if (!$getpoolhotel->isEmpty()) {
                //start
                if (count($getpoolhotel) == 3) {
                    $hotelwithpool = [];
                    if (!$getpoolhotel->isEmpty()) {
                        foreach ($getpoolhotel as $vals) {
                            $hotelwithpool[] = [
                                'name' => $vals->name,
                                'url' => $slugid.'-'.$vals->id.'-'.$vals->slug,
                                'nb_name'=>$vals->nbname,
                                'nburl'=> $vals->LocationID.'-'.$vals->NeighborhoodId.'-'.$vals->nbslug,
                            ];
                        }          
                    }
              


                    $nb =$getpoolhotel[0]->nbname;
                    if($getpoolhotel[0]->nbname ==""){
                        $nb = $getlocation[0]->cityName;
                    }
                    $cityname = $getlocation[0]->cityName;

                    $amenitiesString = $getpoolhotel[0]->amenities;
                    $amenitiesArray = explode(',', $amenitiesString);
                    
                
                    if (count($amenitiesArray) >= 3) {
                        list($amenity1, $amenity2, $amenity3) = array_slice($amenitiesArray, 0, 3);
                        
                    }

                    $amenitiesString2 = $getpoolhotel[1]->amenities;
                    $amenitiesArray2 = explode(',', $amenitiesString2);
                    if (count($amenitiesArray2) >= 3) {
                        list($amen1, $amen2, $amen3) = array_slice($amenitiesArray2, 0, 3);
                    
                    }
                    $amenitiesString3 = $getpoolhotel[2]->amenities;
                    $amenitiesArray3= explode(',', $amenitiesString3);

                    if (count($amenitiesArray3) >= 3) {
                    list($mnt1, $mnt2, $mnt3) = array_slice($amenitiesArray3, 0, 3);
                    
                    }

                    $currentAnswer = "There are over 15 hotels in $lname that have outdoor pools. It is advisable to check for the availability of outdoor pools with the hotels directly for the most up-to-date information.<br<br>";

                    $currentAnswer .= "Here are a few options that you might want to consider.  <br> <br>";

                    $hname  =$getpoolhotel[0]->name;
                    $currentAnswer .= "* $hname Located at $nb offers ";
                    $currentAnswer .= "$amenity1, $amenity2 and $amenity3. The price for the " .$getpoolhotel[0]->stars." Star Rating Property starts at " .$getpoolhotel[0]->pricefrom.".<br><br>";
                    
                    $currentAnswer .= "* ".$getpoolhotel[1]->name." offers $amen1, $amen2 and $amen3. The price for the ".$getpoolhotel[1]->stars." Star Rating Property starts at ".$getpoolhotel[1]->pricefrom.".<br><br>";
                    
                    $currentAnswer .= "* ".$getpoolhotel[2]->name." Features $mnt1, $mnt2 and $mnt3 and has a user Rating of ".$getpoolhotel[2]->location_score.". <br><br>";

                    $anser1 = $currentAnswer;
                   }
                //   return $currentAnswer;
                //   die();

            


                    $getpoolhotel4 = DB::table('TPHotel as h')    
                   // ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
                   // ->join('Location as l','l.LocationId','=','t.Tid')                  
                    ->select('h.id','h.location_id','h.slug','h.name','h.about','n.Name as nbname','n.NeighborhoodId','n.slug as nbslug','n.LocationID','hn.*','h.pricefrom','h.stars','h.amenities','h.location_score') 
                    ->leftjoin('TPHotelNeighbourhood as hn','hn.hotelid','=','h.hotelid') 
                    ->leftjoin('Neighborhood as n','n.NeighborhoodId','=','hn.NeighborhoodId') 
                    ->where('h.location_id', $locationid)
                    ->whereRaw("h.amenities LIKE '%Swimming Pool%'")
                    ->whereNotNull('n.Name')              
                    ->orderby('h.hlocation_score','desc') 
                    ->limit(1)
                    ->get();

                    if(!$getpoolhotel4->isEmpty()){
                        $currentAnswer .= "* ".$getpoolhotel4[0]->name." Located at ".$getpoolhotel4[0]->nbname." has the highest location score amongst all hotels with outdoor pools. Hotel Name is within walking distance from several popular attractions in ".$cityname.". <br><br>";
                   
                        foreach ($getpoolhotel4 as $val2) {
                            $hotelwithpool[] = [
                                'name' => $val2->name,
                                'url' => $slugid.'-'.$val2->id.'-'.$val2->slug,
                                'nb_name'=>$val2->nbname,
                                'nburl'=> $val2->LocationID.'-'.$val2->NeighborhoodId.'-'.$val2->nbslug,
                            ];
                        }       
                        
                     
                    }
                  
                    $getpoolhotel5 = DB::table('TPHotel as h')   
                  //  ->join('Temp_Mapping as t','t.LocationId','=','h.location_id') 
                  //  ->join('Location as l','l.LocationId','=','t.Tid')          
                    ->select('h.id','h.location_id','h.slug','h.name','h.about','n.Name as nbname','n.NeighborhoodId','n.slug as nbslug','n.LocationID','hn.*','h.pricefrom','h.stars','h.amenities','h.location_score') 
                    ->leftjoin('TPHotelNeighbourhood as hn','hn.hotelid','=','h.hotelid') 
                    ->leftjoin('Neighborhood as n','n.NeighborhoodId','=','hn.NeighborhoodId') 
                    ->where('h.location_id', $locationid)
                    ->whereNotNull('h.pricefrom') 
                    ->whereNotNull('h.location_score') 
                    ->whereRaw("h.amenities LIKE '%Swimming Pool%'")
                    ->orderby('h.location_score','desc') 
                    ->limit(1)
                    ->get();
      
               
                    if (!$getpoolhotel5->isEmpty()) {
                        foreach ($getpoolhotel5 as $val3) {
                            $hotelwithpool[] = [
                                'name' => $val3->name,
                                'url' => $slugid.'-'.$val3->id.'-'.$val3->slug,
                                'nb_name'=>$val3->nbname,
                                'nburl'=> $val3->LocationID.'-'.$val3->NeighborhoodId.'-'.$val3->nbslug,
                            ];
                        }          
                    
                        $currentAnswer .= "* ".$getpoolhotel5[0]->name." with a Rating of ".$getpoolhotel5[0]->location_score.", is the cheapest hotel in San Francisco with an outdoor pool The prices start from ".$getpoolhotel5[0]->pricefrom." a night at the hotel. <br><br>";

                        
                    }
             
              

                  if($anser1 != ""){
                        $poolarray = array(
                            'location_id'=>$locationid,
                            'Question'=>"What are the best hotels in $lname with outdoor pools?",
                            'Answer' => $currentAnswer,
                            'Listing'=>json_encode($hotelwithpool),
                            'CreatedDate' => now(),
                            'IsActive'=>1,
                        );
                       DB::table('TPLocationFaq')->insert($poolarray);
                 }

               
        
        }
           //end
    
    

           
        }
            //quest 5 end

           
            if($bhv == 1 || $expervar == 1 ||  $cheapestHOt == 1 || $topcheap == 1 || $pool == 1 || $bthotel =1){
                     
                $faq =  DB::table('TPLocationFaq')->where('location_id',$locationid)->get();
                $html3 = view('frontend.hotel.hotel_listing_faq',['faq'=>$faq,'lname'=>$lname])->render();
             
       
                return response()->json([ 'html' => $html3]);
            }

    
            //end

           }
         
    }
	
 //hotel detail faq 1
  public function addHotledetailFaq(request $request){
    $q1 = 0;
    $q2 = 0;
    $q3 =0;
    $q4 =0;
        $hotelid = $request->get('hotelid');
        $Latitude = $request->get('Latitude');
        $longnitude = $request->get('longnitude');
        $hname = $request->get('hname');
        $hid = $request->get('hid');
        
        
        // $getloc = DB::table('TPLocations')->select('cityName')
        // ->where('id',$locationid)
        // ->get();

        //   if(!$getloc->isEmpty()){
            //  $lname = $getloc[0]->cityName;  

            //quest 1
         //   $att_bestH = DB::table('HotelQuestion')
          //  ->where('hotelid', $hid)
         //   ->where('Question', 'What Attractions are nearby?')
         //   ->get();
        
       //     if ($att_bestH->isEmpty()) {
        //        $q1  = 1;               
                
        //        $searchradius = 5; 
        //        $nearby_sight = DB::table("Sight")
       //         ->join('Location as l','l.LocationId','=','Sight.LocationId')
     //           ->select('Sight.SightId', 'Sight.Title', 'Sight.LocationId', 'Sight.Slug','l.slugid',
     //               DB::raw("6371 * acos(cos(radians(" . $Latitude . "))
     //                   * cos(radians(Sight.Latitude))
    //                    * cos(radians(Sight.Longitude) - radians(" . $longnitude . "))
     //                   + sin(radians(" . $Latitude . "))
     //                   * sin(radians(Sight.Latitude))) AS distance"))
     //           ->groupBy("Sight.SightId")
     //           ->having('distance', '<=', $searchradius)                      
      //          ->orderBy('distance')
     //           ->limit(5)
     //           ->where('Sight.IsMustSee', 1)
     //           ->get();


                // return  print_r($nearby_sight);
            
        //        $best_hotel = [];
        //        if (!$nearby_sight->isEmpty()) {
        //            foreach ($nearby_sight as $bh) {
        //                $best_hotel[] = [
         //                   'name' => $bh->Title,
        //                    'url' => $bh->slugid.'-'.$bh->SightId.'-'.$bh->Slug,                              
                            
          //              ];
        //            }
       //         }
       //         if(!empty($best_hotel)){
                    
        //            $bestharray = array(
       //                 'HotelId'=>$hid,
        //                'Question'=>'What Attractions are nearby?',
      //                  'Answer' =>  'There are several exciting attractions near the hotel that cater to a variety of interests.',
       //                 'Listing'=>json_encode($best_hotel),
      //                  'CreatedDate' => now(),
      //                  'IsActive'=>1,
     //               );
      //              DB::table('HotelQuestion')->insert($bestharray);
      //          }
     //       }

        //quest 1 end
  

     //quest 2
        $get_parkinghotel = DB::table('HotelQuestion')
        ->where('HotelId', $hid)
        ->where('Question', "Does the hotel have parking facilities?")
        ->get();

      

        if ($get_parkinghotel->isEmpty()) {
            $q2  = 1; 
            $getparking =  DB::table('TPHotel')
            ->select('amenities')
            ->where('id',$hid)
            ->get();
            $ans ="";
     
          if(!$getparking->isEmpty()){          
            $amenities = $getparking[0]->amenities;

           if (preg_match('/\bparking\b/i', $amenities)) {
               
                $ans .= "Yes, the hotel offers on-site parking facilities for guests. Whether you're arriving by car or renting one during your stay, you'll find convenient parking available at the hotel.";
               
            } else {
                $ans .= "Unfortunately, the hotel does not provide parking facilities. We recommend checking for public parking options in the surrounding area before your arrival.";
            }

            
          }
       //   return $ans;
         
            if( $ans!="" ){
                $parkarray = array(
                    'HotelId'=>$hid,
                    'Question'=>"Does the hotel have parking facilities?",
                    'Answer' => $ans,                   
                    'CreatedDate' => now(),
                    'IsActive'=>1,
                );
              //  return $parkarray;
                DB::table('HotelQuestion')->insert($parkarray);
            }
        }

    //quest 2 end
    
       //quest 3
    $ques3 = DB::table('HotelQuestion')
    ->where('HotelId', $hid)
    ->where('Question', "How far is the $hname from the city center?")
    ->get();

  

    if ($ques3->isEmpty()) {
       
        $gethotel3 =  DB::table('TPHotel')
        ->select('name','stars','distance')
        ->where('id',$hid)
        ->get();
     
        $ans ="";
   //   return  print_r($getparking);

      if(!$gethotel3->isEmpty()){
        $stars = $gethotel3[0]->stars;
   
         $name = $gethotel3[0]->name;
         $distance = $gethotel3[0]->distance ;
        
     
      $q3  = 1; 
        if($distance !=""){
        if ($stars == 4 || $stars == 5 &&  $distance !="" && $distance < 0.5 ) {
            $ans.= "The";
            if($stars !="" && $stars !=0){
                $ans .= " $stars star property,";
            }else{
                $ans .= " hotel,";
            }               
            $ans .=" $name is nestled right in the heart of the city.";

        }elseif($stars == 4 || $stars == 5 && $distance >= 0.5 && $distance <= 1){
            $ans.= "The ";
            if($stars !="" && $stars !=0){
                $ans .= " $stars star property,";
            }else{
                $ans .= " hotel,";
            }               
            $ans .=" $name is conveniently located within walking distance from the city center.";
        }elseif($stars == 4 || $stars == 5 && $distance <= 2){
            $ans.= "The prestigious ";
            if($stars !="" && $stars !=0){
                $ans .= " $stars star property,";
            }else{
                $ans .= " hotel,";
            }               
            $ans .=" $name is just a short stroll away from the city center";
          }elseif($stars == 4 || $stars == 5 && $distance >= 2 && $distance <= 5){

            $ans.= "The";
            if($stars !="" && $stars !=0){
                $ans .= " $stars star property,";
            }else{
                $ans .= " hotel,";
            }               
            $ans .=" $name is situated at a short distance in approximately 20-25 minutes, from the city center.";
          }elseif($stars == 1 || $stars == 2 || $stars == 3   && $distance >= 0 && $distance < 0.5){

            $ans.= "The";
            if($stars !="" && $stars !=0){
                $ans .= " $stars star property,";
            }else{
                $ans .= " hotel,";
            }               
            $ans .=" $name is located right in the center of the city.";
           }
          }elseif($stars == 1 || $stars == 2 || $stars == 3  && $distance >= 0 && $distance <= 1){

            $ans.= "The";
            if($stars !="" && $stars !=0){
                $ans .= " $stars star property,";
            }else{
                $ans .= " hotel,";
            }               
            $ans .=" $name is located within walking distance/ 10-15 minutes walk on foot from the city center.";
            
        
         }

     
      }

     
        if( $ans !="" ){
            $q3_array = array(
                'HotelId'=>$hid,
                'Question'=>"How far is the $hname from the city center?",
                'Answer' => $ans,                   
                'CreatedDate' => now(),
                'IsActive'=>1,
            );
      
            DB::table('HotelQuestion')->insert($q3_array);
        }
    }

    // end quest 3 

// //quest 4
$check_checkin = DB::table('HotelQuestion')
->where('HotelId', $hid)
->where('Question', "What are the check-in and check-out times?")
->get();


// return print_r($check_checkin);
if ($check_checkin->isEmpty()) {
    $q4 = 1;
    $gethotel =  DB::table('TPHotel')
    ->select('name','stars','checkOut','checkIn')
    ->where('id',$hid)
    ->get();

//   return  print_r($getparking);
if(!$gethotel->isEmpty()){
   // $stars = $gethotel[0]->stars;
 //   $name = $gethotel[0]->name;
    $checkIn = $gethotel[0]->checkIn;
    $checkOut = $gethotel[0]->checkOut;
    $answer ="";
    if ($checkIn != "" && $checkOut != "") {

        if ($checkIn == "14:00" && $checkOut == "10:00") {         
            $answer .= "Check-in at the hotel starts at 2:00 PM, and check-out is required by 10:00 AM. While this early check-out ensures rooms are ready for incoming guests, it may feel rushed for those who prefer a more leisurely departure.";
        }elseif($checkIn == "15:00" && $checkOut == "10:00") {          
            $answer .= "Check-in begins at 3:00 PM, and check-out is required by 10:00 AM. The early check-out helps the hotel maintain high standards of cleanliness, but it might slightly impact the comfort of those who prefer a later start.";
        }elseif($checkIn == "12:00" && $checkOut == "10:00") {         
            $answer .= "Check-in at the hotel starts at 12:00 PM, with check-out by 10:00 AM. Early check-in allows you to start your stay sooner, but the early check-out might feel rushed for those who enjoy lingering in the morning.";
        }elseif($checkIn == "11:00" && $checkOut == "10:00") {
            $answer .= "Check-in begins at 11:00 AM, with check-out required by 10:00 AM. While early check-in gives you more time to settle in, the early check-out may disrupt a leisurely departure.";
        }elseif($checkIn == "16:00" && $checkOut == "10:00") {
            $answer .= "Check-in at the hotel starts at 4:00 PM, with check-out required by 10:00 AM. The late check-in might limit your time to unwind, and the early check-out could feel rushed for those who prefer a relaxed morning.";
        }elseif($checkIn == "13:00" && $checkOut == "10:00") {
            $answer .= "Check-in at the hotel starts at 1:00 PM, with check-out required by 10:00 AM. While the early check-in lets you begin your stay sooner, the early check-out may reduce your time to relax in the morning.";
        }elseif($checkIn == "15:00" && $checkOut == "10:00") {
            $answer .= "Check-in begins at 3:00 PM, and check-out is required by 10:00 AM. The early check-out ensures rooms are promptly available for the next guest, but it might impact the comfort of those who prefer a slower start to their day.";
        }elseif($checkIn == "14:00" && $checkOut == "09:00") {
            $answer .= "Check-in starts at 2:00 PM, with an early check-out at 9:00 AM. This early check-out might be ideal for early risers or those catching a morning flight, but it could feel hurried for guests wanting to enjoy a more relaxed departure.";
        }
        
        if( $answer!="" ){
            $checkin_array = array(
                'HotelId'=>$hid,
                'Question'=>"What are the check-in and check-out times?",
                'Answer' => $answer,            
                'CreatedDate' => now(),
                'IsActive'=>1,
            );
            DB::table('HotelQuestion')->insert($checkin_array);
        }
  
    }
    
    }
 

}
// //quest 4 end

        
        if($q1 == 1 || $q2 ==1 || $q3 == 1 &&  $q4 == 1){
                                            
            $faq =  DB::table('HotelQuestion')->where('HotelId',$hid)->get();
            $html3 = view('frontend.hotel.hotel_detail_faq',['faq'=>$faq])->render();
            
    
            return response()->json([ 'html' => $html3]);
        }


        //end

    //    }
        
}
 
    public function add_Hotelreview(request $request){
           $uploadedFiles = $request->file('files');
           
           // Calculate the reverse rating (5 becomes 1, 4 becomes 2, etc.)
           $rating = $request->get('rating');
           $reversedRating = 6 - $rating; // This will convert 1->5, 2->4, 3->3, 4->2, 5->1
           
           $data = array(
            'Name' => $request->get('name'),
            'Email' => $request->get('email'),
            'Description' => $request->get('review'), 
            'Rating' => $reversedRating, // Use reversed rating
            'HotelId' => $request->get('hotelid'),
            'CreatedOn' => now(),
            'UpdatedOn' => now(),
            'IsActive' => 1,
            'cleanrating' => 6 - $request->get('cleanrating', 0), // Reverse rating for cleanliness
            'locationrating' => 6 - $request->get('starlocation', 0), // Reverse rating for location
            'servicerating' => 6 - $request->get('starservice', 0), // Reverse rating for service
            'valuerating' => 6 - $request->get('starvalue', 0), // Reverse rating for value
            'go_with' => $request->get('gowith', ''),
            'IsRecommend' => 1, // Default value
            'IsApprove' => 0, // Default value
            'ReviewType' => 'Regular' // Default value
           );
    
           $imageUrls = $request->input('imageUrls');
            $result = DB::table('HotelReview')->insertGetId($data);
 // return    print_r($uploadedFiles);
            if(!empty($imageUrls)){
               foreach ($uploadedFiles as $image) {
                    if ($image->isValid()) {
                        $imageName = 'hr'.time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('review-images'), $imageName);
            
            
                        $data = [ 
                            'hotelreviewid'=>$result,
                            'Image' => $imageName,    
                            'created_at'=>now(),              
                        ];
            
                        $getreview = DB::table('TPhotel_review_image')->insert($data);
                    }
                }
             }
            if($result){ 
                    
             $review = DB::table('HotelReview')->where('HotelId',$request->get('hotelid'))->get();
				
				//send email to hotel
			
			  $hotel = DB::table('TPHotel')->where('id', $request->get('hotelid'))->first();

             if ($hotel != null) {
			
				$subject = 'New Review for ' . $hotel->name;
				//$body = 'A new review has been added for ' . $hotel->name . '. Rating: ' . $request->get('rating') . '. Review: ' . $request->get('review');
$body = 'A new review has been added for ' . $hotel->name . '.' . PHP_EOL . 
        'Rating: ' . $request->get('rating') . '.' . PHP_EOL . 
        'Review: ' . $request->get('review'). '.';
			
				
				$to = 'priyathakur141997@gmail.com';
			   // $to = $request->get('hotel_email'); 

				try {
					// Attempt to send the email
					Mail::raw($body, function ($message) use ($to, $subject) {
						$message->to($to)
								->subject($subject);
					});

					// Email sent successfully
					 response()->json(['message' => 'Email sent successfully'], 200);
				} catch (\Exception $e) {
					// Email sending failed
					\Log::error('Failed to send email: ' . $e->getMessage());
					 response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
				}

			 }
			
			//end send email
              return  view('frontend.hotel.hotel_review_result',[ 'review'=>$review]);
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
        ->where('s.TATotalReviews', '>', 20)
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
      ->where('s.TATotalReviews', '>', 20)
      ->orderby('s.TAAggregateRating','desc')
      ->limit(5)
      ->get(); 
          
          $sightIds = [];

          foreach ($gettopatt as $gettopatts) {
              $sights[] = [
                  'name' => $gettopatts->Title,
                  'url' =>$gettopatts->SightId.'-'.$gettopatts->Slug,
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
      ->where('s.TATotalReviews', '>', 20)
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
          ->where('s.TATotalReviews', '>', 20)
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
	
	
	
	
	//update hotel overview 
	
	 public function update_hotel_overview(){
   
  
        $delaySeconds = 100;
        $batchSize = 5; 
    
        do {
                 
          //  $hotel_id = 1000038;
            $data = DB::table('TPHotel as h')
            ->select('h.id','h.location_id','h.slug','h.name','hn.id as NeighborhoodId','hn.name as nbslug','hn.location_id as LocationID','hn.name as nbname','t.Tid','t.cityName','h.rating','h.distance','h.cntRooms','h.amenities','h.shortFacilities','h.pricefrom','h.Latitude','h.longnitude','h.hotelid','h.location_score','h.stars','h.room_aminities') 
            ->Leftjoin('Temp_Mapping as t','t.LocationId','=','h.location_id')      
            ->Join('TPhotelNbData as hn','hn.hotelid','=','h.id')    
            ->where('h.about',null) 
           // ->where('h.id',$hotel_id)
            ->limit($batchSize)
            ->get();
       
            if ($data->isEmpty()) {
                
                dd('empty data');
                break;
                
            }   
           // 1000005
            foreach ($data as $cont) {
                 $hotelid =$cont->hotelid;
                 $currentAnswer ="";            
                 $cityname = $cont->cityName;
              
                 $nbname = $cont->nbname;
                 $rating = $cont->rating;
                 $distance = $cont->distance;
                 $cntRooms = $cont->cntRooms;
                 $amenities = $cont->amenities;
                 $stars = $cont->stars;
                 $location_score = $cont->location_score;
                 $pricefrom = $cont->pricefrom;
                 $hname = $cont->name;
                 $id = $cont->id;
            
                 $Latitude = $cont->Latitude; 
                 $longnitude = $cont->longnitude; 
            
                //  if($stars != "" &&  $nbname != "" &&  $cityname != "" && $hname !=""){
                //   $currentAnswer .= "A $stars star property ,$hname, is situated in $nbname, $cityname.";
                //  }elseif($stars != "" &&  $nbname != "" &&  $cityname != "" && $hname !="" && $distance <= 0.5){
                //     $currentAnswer .= "Situated in $nbname, $cityname, a $stars star property, $hname, is a short stroll away from the city center.";
                //  }elseif($stars != "" &&  $cityname != "" && $hname !="" && $distance < 1){
                //      $currentAnswer .= "Located in $cityname, a $stars star property, $hname,";
            
                //      if($distance <= 0.5){             
                //        $currentAnswer .= 'is just a stroll away from the city center.';   
                //      }elseif($distance < 1){ 
                //       $currentAnswer .= "is within walking distance from the city center."; 
                //      }
                // }elseif($stars != "" &&  $cityname != "" && $hname !="" && $distance > 1){
                //     $currentAnswer .= "Located in $cityname, a $stars star property, $hname, is a mere minute drive away $distance km from the city center.";
                  
                //  }elseif($stars != "" && $nbname !="" && $cityname != "" && $hname !="" && $distance <= 4 &&   $location_score != ""){
                //     $currentAnswer .= "Situated in $nbname, $cityname, a $stars star property, $hname, is a short stroll away from the city center. With a prime location score of $location_score, $hname allows you to spend less time commuting and more time experiencing the city.";
                //   }
                  
            
            
            
            
               
                 if($Latitude !="" && $longnitude != "" ){
                 $searchradius = 50;
                 $popular_landmark = DB::table("Sight")
                     ->select('SightId', 'Title', 'LocationId', 'Slug',
                         DB::raw("6371 * acos(cos(radians(" . $Latitude . "))
                             * cos(radians(Sight.Latitude))
                             * cos(radians(Sight.Longitude) - radians(" . $longnitude . "))
                             + sin(radians(" . $Latitude . "))
                             * sin(radians(Sight.Latitude))) AS distance"))
                     ->where('Sight.IsMustSee')
                     ->groupBy("Sight.SightId")
                     ->having('distance', '<=', $searchradius)
                     
                     ->orderBy('distance', 'asc')
                     ->limit(1)
                     
                     ->get();
                 }
            
                 $currentAnswer .= "";
                 $title ="";
                 $landmark_distance ="";
                if(!$popular_landmark->isEmpty()){
                    $title = $popular_landmark[0]->Title;
                    $landmark_distance = $popular_landmark[0]->distance;
                }
            
                if($stars != "" &&  $cityname != "" && $hname !="" && $title !="" &&  $landmark_distance != "" && $distance !="" && $distance <= 1 ){
                    $currentAnswer .= "Situated in ";
                    if($nbname != ""){
                        $currentAnswer .= "$nbname,";
                    }
                    $currentAnswer .= " $cityname,";
                    if($stars != 0){
                        $currentAnswer .= ",a $stars star property, ";
                    }else{
                        $currentAnswer .= ", The hotel ";
                    }
                    $currentAnswer .= "$hname,";
        
                    if($distance <= 1){             
                        $currentAnswer .= ' is few steps away from the city center.';   
                    }elseif($distance > 1 && $distance <= 3){ 
                        $currentAnswer .= " a mere minute drive away."; 
                    }elseif($distance > 3){ 
                        $currentAnswer .= " is $distance away from city center."; 
                    }
        
                    if($landmark_distance <= 0.5){
                      $currentAnswer .= "The hotel is built conveniently nearby the $title on foot";
                    }elseif($landmark_distance >= 0.5 &&  $landmark_distance >= 1){
                     $currentAnswer .= "The hotel is conveniently positioned just within walking distance from the $title.";
                    }elseif($landmark_distance >= 1 &&  $landmark_distance <= 3){
                     $currentAnswer .= "The hotel is a quick drive away from the $title.";
                    }elseif($landmark_distance >= 3 &&  $landmark_distance <= 5){
                     $currentAnswer .= "The hotel is a mere minute’s drive away from the $title.";
                    }elseif($landmark_distance >= 5 &&  $landmark_distance <= 10){
                      $currentAnswer .= "The Hotel is slightly far away from the $title.";
                    }elseif($landmark_distance >= 10 ){
                     $currentAnswer .= "is $landmark_distance km away from the $title.";
                    }
                
               }elseif($stars != "" &&  $cityname != "" && $hname !="" && $title !="" &&  $landmark_distance != ""){
                    $currentAnswer .= "Located in ";
        
                    if($nbname != ""){
                        $currentAnswer .= "$nbname,";
                    }
                    $currentAnswer .= " $cityname,";
                    if($stars != 0){
                        $currentAnswer .= ",a $stars star property, ";
                    }else{
                        $currentAnswer .= ", The hotel ";
                    }
                    $currentAnswer .= "$hname,";
                    if($landmark_distance <= 0.5){
                    $currentAnswer .= " is built conveniently nearby the $title on foot";
                    }elseif($landmark_distance >= 0.5 &&  $landmark_distance >= 1){
                    $currentAnswer .= " is conveniently positioned just within walking distance from the $title.";
                    }elseif($landmark_distance >= 1 &&  $landmark_distance <= 3){
                    $currentAnswer .= " is a quick drive away from the $title.";
                    }elseif($landmark_distance >= 3 &&  $landmark_distance <= 5){
                    $currentAnswer .= " is a mere minute’s drive away from the $title.";
                    }elseif($landmark_distance >= 5 &&  $landmark_distance <= 10){
                    $currentAnswer .= " is slightly far away from the $title.";
                    }elseif($landmark_distance >= 10 ){
                    $currentAnswer .= "is $landmark_distance km away from the $title.";
                    }
                }elseif($stars != "" && $cityname != "" && $hname !=""  &&  $title  !=""){
                    $currentAnswer .= "Nestled in "; 
                    if($nbname != ""){
                        $currentAnswer .= "$nbname,";
                    }
                    $currentAnswer .= " $cityname,";       
                    if($stars != 0){
                        $currentAnswer .= ",a $stars star property, ";
                    }else{
                        $currentAnswer .= ", The hotel ";
                    }
                    $currentAnswer .= " $hname, is located nearby the $title.";

                }elseif($stars != "" && $cityname != "" && $hname !="" && $distance <= 4 &&   $location_score != ""){
                    $currentAnswer .= "Located in ";
                    if($nbname != ""){
                        $currentAnswer .= "$nbname,";
                    }
                    $currentAnswer .= " $cityname,";    
                    if($stars != 0){
                        $currentAnswer .= ",a $stars star property, ";
                    }else{
                        $currentAnswer .= ". The hotel ";
                    }
                  
                    $currentAnswer .= "$hname, is a short stroll away from the city center. With a prime location score of $location_score, $hname allows you to spend less time commuting and more time experiencing the city.";    
                }elseif($stars != "" &&  $nbname != "" &&  $cityname != "" && $hname !="" && $distance <= 0.5){
                      $currentAnswer .= "Situated in ";
                        if($nbname != ""){
                             $currentAnswer .= "$nbname,";
                        }
                        $currentAnswer .= " $cityname,";      
                        if($stars != 0){
                             $currentAnswer .= ",a $stars star property, ";
                        }else{
                            $currentAnswer .= ", The hotel ";
                        }
                        $currentAnswer .= "$hname, is a short stroll away from the city center.";
                }elseif($stars != "" &&  $cityname != "" && $hname !="" && $distance < 1){
                       $currentAnswer .= "Located in $cityname";
                       if($stars != 0){
                        $currentAnswer .= ",a $stars star property, ";
                        }else{
                            $currentAnswer .= ", The hotel ";
                        }
        
                       if($distance <= 0.5){             
                         $currentAnswer .= "$hname, is within walking distance from the city center.";   
                        }elseif($distance >= 1 && $distance <= 3){ 
                            $currentAnswer .= " a mere minute drive away."; 
                        }elseif($distance > 3){ 
                        $currentAnswer .= "$hname,  is $distance away from city center."; 
                       }

                     
                }elseif($stars != "" &&  $cityname != "" && $hname !="" && $distance > 1){
                      $currentAnswer .= "Placed in $cityname";
                        if($stars != 0){
                            $currentAnswer .= ",a $stars star property, ";
                        }else{
                            $currentAnswer .= ". The hotel ";
                        }
                      $currentAnswer .= "$hname, is $distance km from the city center.";
                    
               
                }elseif($stars != ""  &&  $cityname != "" && $hname !=""){
                    if($stars != 0){
                        $currentAnswer .= "A $stars star property, ";
                    }else{
                        $currentAnswer .= "The hotel ";
                    }
                    $currentAnswer .= "$hname, is situated in ";
                    if($nbname != ""){
                        $currentAnswer .= "$nbname,";
                    }
                    $currentAnswer .= " $cityname.";    
                }
            
             
            
            
            
            
            
                $amenitiesWithTexts = [];
                $amenitiesString="";
              if($stars == 5){
            
              $shortFacilities = $cont->shortFacilities;
                if(!empty($shortFacilities)){
                
                    $shortFacilitiesArray = json_decode($shortFacilities, true);
                  
                    $desiredAmenities = ['pool', 'bar', 'lounge', 'restaurants', 'pets', 'Business services', 'kitchenette', '24/7', 'water park', 'indoor pool', 'spa','Hairdryer'];
                    
                    $availableAmenities = [];
                    
                    foreach ($desiredAmenities as $amenity) {
                        if (in_array($amenity, $shortFacilitiesArray)) {
                            $availableAmenities[] = $amenity; 
                        }
                    }
                }
                if($amenities !=""){
                    $amenitiesArray = explode(',', $amenities);
                    
                    $availableAmenities = [];  
                    foreach ($amenitiesArray as $amenity) {     
                        $trimmedAmenity = trim($amenity);
                
                        if (in_array($trimmedAmenity, $desiredAmenities)) {           
                            $availableAmenities[] = $trimmedAmenity;
                        }
                    }
            
                }
                //   $room_amenities = $cont->room_aminities;
                //   $room_amenitie_array = json_decode($room_amenities, true); 
                //   foreach ($desiredAmenities as $amenity) {
                //       if (in_array($amenity, $room_amenitie_array)) {
                //           $availableAmenities[] = $amenity; 
                //       }
                //   }
            
            
                $amenitiesTexts = [ 
                    'pets' => "$hname's pet policy welcomes its guest's furry travel buddy."
                   
                  ];
                  
                  $amenitiesWithTexts = [];
                  
                  if(!empty($availableAmenities)){
                       foreach ($availableAmenities as $amenity) {
                  
                            if (isset($amenitiesTexts[$amenity])) {
                            
                                $amenitiesWithTexts[] = $amenitiesTexts[$amenity];
                            } else {
                                // If no text is assigned for the amenity, include the amenity itself
                                $amenitiesWithTexts[] = $amenity;
                            }
                        }
                      
                   }else{
                    if(!empty($shortFacilitiesArray)){
                        $amenitiesWithTexts = $shortFacilitiesArray;
                     }elseif($amenities !=""){
                        $amenitiesWithTexts = $amenities;
                     }
                    
                    }
                   $amenitiesString = implode(', ', $amenitiesWithTexts);
            
             
                    
                    if($amenitiesString !=""){
                        $currentAnswer .=  " The Hotel provides amenities like $amenitiesString.";
                    }
                  
            
                }elseif($stars >= 3 && $stars <= 4){
            
                  $shortFacilities = $cont->shortFacilities;
                  if(!empty($shortFacilities)){
                  
                      $shortFacilitiesArray = json_decode($shortFacilities, true);
              
                      $desiredAmenities = ['swimming pool','pool', 'bar', 'lounge', 'restaurants', 'pets', 'Business services', 'kitchenette', '24/7', 'room services', 'jacuzzi', 'spa'];
                      
                      $availableAmenities = [];
                      
                      foreach ($desiredAmenities as $amenity) {
                          if (in_array($amenity, $shortFacilitiesArray)) {
                              $availableAmenities[] = $amenity; 
                          }
                      }
                  }
                  if($amenities !=""){
                      $amenitiesArray = explode(',', $amenities);
                      
                      $availableAmenities = [];  
                      foreach ($amenitiesArray as $amenity) {     
                          $trimmedAmenity = trim($amenity);
                  
                          if (in_array($trimmedAmenity, $desiredAmenities)) {           
                              $availableAmenities[] = $trimmedAmenity;
                          }
                      }
              
                  }
                  //   $room_amenities = $cont->room_aminities;
                  //   $room_amenitie_array = json_decode($room_amenities, true); 
                  //   foreach ($desiredAmenities as $amenity) {
                  //       if (in_array($amenity, $room_amenitie_array)) {
                  //           $availableAmenities[] = $amenity; 
                  //       }
                  //   }
              
              
                  $amenitiesTexts = [ 
                      'pets' => "$hname has a pet-friendly policy which provides pet-friendly rooms."
                     
                    ];
                    
                    $amenitiesWithTexts = [];
                    
                    if(!empty($availableAmenities)){
                         foreach ($availableAmenities as $amenity) {
                    
                              if (isset($amenitiesTexts[$amenity])) {
                              
                                  $amenitiesWithTexts[] = $amenitiesTexts[$amenity];
                              } else {
                                  // If no text is assigned for the amenity, include the amenity itself
                                  $amenitiesWithTexts[] = $amenity;
                              }
                          }
                          $amenitiesString = implode(', ', $amenitiesWithTexts);
                     }elseif(!empty($shortFacilitiesArray)){
                          $amenitiesWithTexts = $shortFacilitiesArray;
                          $amenitiesString = implode(', ', $amenitiesWithTexts);
                     }elseif(!empty($amenities)){
                          $amenitiesString = $amenities;
                     }
                   
              
                     if($amenitiesString !=""){
                        $currentAnswer .=  " The Hotel provides amenities like $amenitiesString.";
                      }
              
                  }elseif($stars >= 1 && $stars <= 2){
            
                    $shortFacilities = $cont->shortFacilities;
                      if(!empty($shortFacilities)){
                      
                          $shortFacilitiesArray = json_decode($shortFacilities, true);
                  
                          $desiredAmenities = ['local transportation'];
                          
                          $availableAmenities = [];
                          
                          foreach ($desiredAmenities as $amenity) {
                              if (in_array($amenity, $shortFacilitiesArray)) {
                                  $availableAmenities[] = $amenity; 
                              }
                          }
                      }
                      if($amenities !=""){
                          $amenitiesArray = explode(',', $amenities);
                          
                          $availableAmenities = [];  
                          foreach ($amenitiesArray as $amenity) {     
                              $trimmedAmenity = trim($amenity);
                      
                              if (in_array($trimmedAmenity, $desiredAmenities)) {           
                                  $availableAmenities[] = $trimmedAmenity;
                              }
                          }
                  
                      }
                      //   $room_amenities = $cont->room_aminities;
                      //   $room_amenitie_array = json_decode($room_amenities, true); 
                      //   foreach ($desiredAmenities as $amenity) {
                      //       if (in_array($amenity, $room_amenitie_array)) {
                      //           $availableAmenities[] = $amenity; 
                      //       }
                      //   }
                  
                  
                      $amenitiesTexts = [ 
                          'pets' => "$hname takes a 'paw-sitive' approach towards its pet-friendly policy"
                         
                        ];
                        
                        $amenitiesWithTexts = [];
                        
                        if(!empty($availableAmenities)){
                             foreach ($availableAmenities as $amenity) {
                        
                                  if (isset($amenitiesTexts[$amenity])) {
                                  
                                      $amenitiesWithTexts[] = $amenitiesTexts[$amenity];
                                  } else {
                                      // If no text is assigned for the amenity, include the amenity itself
                                      $amenitiesWithTexts[] = $amenity;
                                  }
                              }
                              $amenitiesString = implode(', ', $amenitiesWithTexts);
                         }elseif(!empty($shortFacilitiesArray)){
                              $amenitiesWithTexts = $shortFacilitiesArray;
                              $amenitiesString = implode(', ', $amenitiesWithTexts);
                         }elseif(!empty($amenities)){
                              $amenitiesString = $amenities;
                         }
                                          
                          
                         if($amenitiesString !=""){
                            $currentAnswer .=  " The Hotel offers amenities like $amenitiesString.";
                          }
                  
                      }elseif($stars == 0){
            
                        $shortFacilities = $cont->shortFacilities;
                          if(!empty($shortFacilities)){
                          
                              $shortFacilitiesArray = json_decode($shortFacilities, true);
                      
                              $desiredAmenities = ['local transportation'];
                              
                              $availableAmenities = [];
                              
                              foreach ($desiredAmenities as $amenity) {
                                  if (in_array($amenity, $shortFacilitiesArray)) {
                                      $availableAmenities[] = $amenity; 
                                  }
                              }
                          }
                          if($amenities !=""){
                              $amenitiesArray = explode(',', $amenities);
                              
                              $availableAmenities = [];  
                              foreach ($amenitiesArray as $amenity) {     
                                  $trimmedAmenity = trim($amenity);
                          
                                  if (in_array($trimmedAmenity, $desiredAmenities)) {           
                                      $availableAmenities[] = $trimmedAmenity;
                                  }
                              }
                      
                          }
                          //   $room_amenities = $cont->room_aminities;
                          //   $room_amenitie_array = json_decode($room_amenities, true); 
                          //   foreach ($desiredAmenities as $amenity) {
                          //       if (in_array($amenity, $room_amenitie_array)) {
                          //           $availableAmenities[] = $amenity; 
                          //       }
                          //   }
                      
                      
                          $amenitiesTexts = [ 
                              'pets' => "$hname takes a 'paw-sitive' approach towards its pet-friendly policy"
                             
                            ];
                            
                            $amenitiesWithTexts = [];
                            
                            if(!empty($availableAmenities)){
                                 foreach ($availableAmenities as $amenity) {
                            
                                      if (isset($amenitiesTexts[$amenity])) {
                                      
                                          $amenitiesWithTexts[] = $amenitiesTexts[$amenity];
                                      } else {
                                          // If no text is assigned for the amenity, include the amenity itself
                                          $amenitiesWithTexts[] = $amenity;
                                      }
                                  }
                                  $amenitiesString = implode(', ', $amenitiesWithTexts);
                             }elseif(!empty($shortFacilitiesArray)){
                                  $amenitiesWithTexts = $shortFacilitiesArray;
                                  $amenitiesString = implode(', ', $amenitiesWithTexts);
                             }elseif(!empty($amenities)){
                                  $amenitiesString = $amenities;
                             }
                      
                             if($amenitiesString !=""){
                                $currentAnswer .=  " The Hotel provides amenities like $amenitiesString.";
                              }
                      
                          }
                      
                  echo $id.'---';
                      return $currentAnswer ;
            //end code 
            
            if($currentAnswer !=""){                     
               
                 
                    $data = array(
                    'about'=>$currentAnswer                                                
                    );
                 //  DB::table('TPHotel')->where('id',$id)->update($data);
               
            }     
                  
                      
                     
    
            }
    
            }while (true);
        }  
	
	//end hotel overview
	  public function update_roommnt(){
   
                $timeout = PHP_INT_MAX; 
            
                $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869"; 
                $TRAVEL_PAYOUT_MARKER = "299178"; 
            
                $batchSize = 5; 
                $delaySeconds = 100;
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
                    
                        $data = DB::table('TPHotel')
                        ->select('location_id', 'id','hotelid', 'name', 'amenities', 'room_aminities', 'Languages')
                        ->whereRaw('JSON_VALID(room_aminities)')
                        ->where('room_aminities', '!=', '[]')  
                       // ->where('hotelid',1913485263)                    
                        ->distinct()
						->orderby('id','asc')  
                        ->limit($batchSize)
                        ->get();

                      //  return print_r($data);
                        if ($data->isEmpty()) {
                          
                            dd('empty data');
                            break;
                            
                        }
                
                        $englishNames = [];
            
               
                  if(empty($data)){
                      dd('data is empty');
                  }
            
                 foreach ($data as $cont) {
                     $cid = $cont->location_id;      
                     $hotelid = $cont->hotelid;      
                         $getval = "https://engine.hotellook.com/api/v2/static/hotels.json?locationId=$cid&token=".$TRAVEL_PAYOUT_TOKEN;
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
                       
                          $b =1;	   
                     foreach ($hotels as $hotel) {  
                            
                             $b++; 
                             $hid = $hotel->id;
                          
                             if( $hid == $hotelid){

                             //start mnt
                             $facilities = $hotel->facilities;
                             $amenitiesNames = [];
                             $languages = [];
                             $roomsaminity = [];
                             
                             foreach ($facilities as $facilityId) {
                                 $matchingAmenity = array_filter($mnt, function ($amenity) use ($facilityId) {
                                     return $amenity->id == $facilityId;
                                 });
                                 
                                 if (!empty($matchingAmenity)) {
                                     $amenityName = reset($matchingAmenity)->name;
                                     $groupName = reset($matchingAmenity)->groupName;
                                     
                                     if ($groupName != "Staff languages" && $groupName != "Room") {
                                         $amenitiesNames[] = $amenityName;
                                     }
                                     
                                     if ($groupName == "Staff languages") {
                                         $languages[] = $amenityName;
                                     }
                                     
                                     if ($groupName == "Room") {
                                         $roomsaminity[] = $amenityName;
                                     }
                                 }
                             }
                             
                                $amenitiesString = implode(', ', $amenitiesNames);
                             $languageString = implode(', ', $languages);
                             $roomsaminityString = implode(', ', $roomsaminity);
                             

                                  //end mnt
            
                          
            
                                date_default_timezone_set('Asia/Kolkata');
                                //start ct
                                $ctid =$hotel->cityId;
                                                       
                                   //end new
                
                                    $dt = array(                     
                                        'facilities' => $facilities,
                                        'Languages' => $languageString,
                                        'room_aminities' => $roomsaminityString,
                                        'amenities' => $amenitiesString ,
                                        'md'=>1,
                                        
                                    );
                              
                                    // echo $hid.'----';
                                    // return  $dt;
                                    // die();
                                    DB::table('TPHotel')->where('hotelid',$hid)->update($dt);
             
                                     sleep($delayafterloop);
                        
                                   
                                         $j++;

                                }

                           //endloop
                               }
                               
                               
                           } else {
                               echo "No 'hotels' found in the response for name: $name\n";
                           }
                   
                        
                       }
             sleep($delaySeconds);
                       $offset += $batchSize;
                    } while (true);
            
          }
	  public function update_roommnt2(){
   
                $timeout = PHP_INT_MAX; 
            
                $TRAVEL_PAYOUT_TOKEN = "27bde6e1d4b86710997b1fd75be0d869"; 
                $TRAVEL_PAYOUT_MARKER = "299178"; 
            
                $batchSize = 5; 
                $delaySeconds = 50;
                $delayafterloop = 30;
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
                    
                        $data = DB::table('TPHotel')
                        ->select('location_id', 'id','hotelid', 'name', 'amenities', 'room_aminities', 'Languages')
                        ->whereRaw('JSON_VALID(room_aminities)')
                        ->where('room_aminities', '!=', '[]')  
                       // ->where('hotelid',1913485263)   						  
                        ->distinct()
                        ->limit($batchSize)
						->orderby('id','desc')
                        ->get();

                      //  return print_r($data);
                        if ($data->isEmpty()) {
                          
                            dd('empty data');
                            break;
                            
                        }
                
                        $englishNames = [];
            
               
                  if(empty($data)){
                      dd('data is empty');
                  }
            
					
                 foreach ($data as $cont) {
                     $cid = $cont->location_id;      
                     $hotelid = $cont->hotelid;      
                         $getval = "https://engine.hotellook.com/api/v2/static/hotels.json?locationId=$cid&token=".$TRAVEL_PAYOUT_TOKEN;
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
                       
                          $b =1;	   
                     foreach ($hotels as $hotel) {  
                            
                             $b++; 
                             $hid = $hotel->id;
                          
                             if( $hid == $hotelid){

                             //start mnt
                             $facilities = $hotel->facilities;
                             $amenitiesNames = [];
                             $languages = [];
                             $roomsaminity = [];
                             
                             foreach ($facilities as $facilityId) {
                                 $matchingAmenity = array_filter($mnt, function ($amenity) use ($facilityId) {
                                     return $amenity->id == $facilityId;
                                 });
                                 
                                 if (!empty($matchingAmenity)) {
                                     $amenityName = reset($matchingAmenity)->name;
                                     $groupName = reset($matchingAmenity)->groupName;
                                     
                                     if ($groupName != "Staff languages" && $groupName != "Room") {
                                         $amenitiesNames[] = $amenityName;
                                     }
                                     
                                     if ($groupName == "Staff languages") {
                                         $languages[] = $amenityName;
                                     }
                                     
                                     if ($groupName == "Room") {
                                         $roomsaminity[] = $amenityName;
                                     }
                                 }
                             }
                             
                                $amenitiesString = implode(', ', $amenitiesNames);
                             $languageString = implode(', ', $languages);
                             $roomsaminityString = implode(', ', $roomsaminity);
                             

                                  //end mnt
            
                          
            
                                date_default_timezone_set('Asia/Kolkata');
                                //start ct
                                $ctid =$hotel->cityId;
                                                       
                                   //end new
                
                                    $dt = array(                     
                                        'facilities' => $facilities,
                                        'Languages' => $languageString,
                                        'room_aminities' => $roomsaminityString,
                                        'amenities' => $amenitiesString ,
                                        'md'=>1,
                                        
                                    );
                              
                                    // echo $hid.'----';
                                    // return  $dt;
                                    // die();
                                    DB::table('TPHotel')->where('hotelid',$hid)->update($dt);
             
                                     sleep($delayafterloop);
                        
                                   
                                         $j++;

                                }

                           //endloop
                               }
                               
                               
                           } else {
                               echo "No 'hotels' found in the response for name: $name\n";
                           }
                   
                        
                       }
             sleep($delaySeconds);
                       $offset += $batchSize;
                    } while (true);
            
          }
	
	     public function test_lociq(){
            return view('test.test_lociq');
        }

        public function updatelongtude(){
        
            $batchSize = 5; 
            $delaySeconds = 60;
           
          
        
            do {
                
                $data = DB::table('TPHotel')
                ->select('hotelid')
                ->whereNull('longitude')
                ->where('hotelid', '!=', 0)                           
                ->orderBy('id', 'asc')
                ->limit($batchSize)
                ->get();
       
    
                if ($data->isEmpty()) {                    
                    dd('empty data');
                    break;                    
                }
                              
  
                foreach ($data as $cont) {

                    $hotelid = $cont->hotelid;      
                    
                    $gettemptbl = DB::table('temptbl')
                    ->select('longitude','id')
                    ->where('id',$hotelid) 
                    ->get();
                    
             
                    
                    if(!$gettemptbl->isEmpty()){
                        foreach ($gettemptbl as $gettemptbls) {  
                           
                            $hid = $gettemptbls->id; 
                            $longitude = $gettemptbls->longitude;                        
                        
                            DB::table('TPHotel')
                            ->where('hotelid', $hotelid) 
                            ->update(['longitude' => $longitude]);
                        
                        }
                    }
                


                }

                    sleep($delaySeconds);
                 
            } while (true);
            
        }
	 public function updatelongtude2(){
        
            $batchSize = 5; 
            $delaySeconds = 60;
           
          
        
            do {
                
                $data = DB::table('TPHotel')
                ->select('hotelid')
                ->whereNull('longitude')
                ->where('hotelid', '!=', 0)                           
                ->orderBy('id', 'desc')
                ->limit($batchSize)
                ->get();
       
    
                if ($data->isEmpty()) {                    
                    dd('empty data');
                    break;                    
                }
                              
  
                foreach ($data as $cont) {

                    $hotelid = $cont->hotelid;      
                    
                    $gettemptbl = DB::table('temptbl')
                    ->select('longitude','id')
                    ->where('id',$hotelid) 
                    ->get();
                    
             
                    
                    if(!$gettemptbl->isEmpty()){
                        foreach ($gettemptbl as $gettemptbls) {  
                           
                            $hid = $gettemptbls->id; 
                            $longitude = $gettemptbls->longitude;                        
                        
                            DB::table('TPHotel')
                            ->where('hotelid', $hotelid) 
                            ->update(['longitude' => $longitude]);
                        
                        }
                    }
                


                }

                    sleep($delaySeconds);
                 
            } while (true);
            
        }
	
	
	
	    // swimming pool data 


        public function addnearswimming(request $request){
            
             $locid = $request->get('locationid');       
            $latitude =$request->get('Latitude');       
            $longitude =  $request->get('longnitude');           
         //   $hotelid =  $request->get('hid');  
            $id = $request->get('hid'); 

            $h24 =0;  
            $nbh = 0;  
    //start swmming pool
            $checkdata = DB::table('NbyhotelSwimmingListing')->where('hid',$id)->where('type','Swimming Pool')->get();

            if($checkdata->isEmpty()){
                      
                if( $latitude !="" && $longitude !=""){
                    $nbh = 1; 
                    $searchradius = 44; 
                    $nearby_hotelsswiming = DB::table("TPHotel")           
                        ->select('id', 'name','location_id','slug',
                                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(TPHotel.Latitude)) 
                        * cos(radians(TPHotel.longnitude) - radians(" . $longitude . ")) 
                        + sin(radians(" . $latitude . ")) 
                        * sin(radians(TPHotel.Latitude))) AS distance"))     
                        ->having('distance', '<=', $searchradius)  
                        ->where('amenities', 'like', '%swimming pool%')  
                        ->where('location_id',$locid) 
                        ->where('id', '!=', $id) 
                        ->orderBy('distance')
                        ->limit(4)            
                        ->get();

                //   return print_r( $nearby_hotelsswiming);
                    //    die();
                    $data=array();
                        if(!$nearby_hotelsswiming->isEmpty()){
                        
                            foreach($nearby_hotelsswiming as $nbdata){
                               
                                $data = array(
                                'hid'=>$id,
                                'hotelid'=>$nbdata->id,
                                'name'=>$nbdata->name,
                                'location_id'=>$nbdata->location_id,
                                'slug'=>$nbdata->slug,
                                'distance'=>round($nbdata->distance),
                                'radius'=>$searchradius,  
                                'type'=>'Swimming Pool',                                     
                            );
                          
                         DB::table('NbyhotelSwimmingListing')->insert($data);
                            }
                       //     return print_r( $data );
                        
                            
                        }
                    
                       

                       
                }
   

              
           
        }
 //end swmming pool

    //start 24 hours
    $check24h = DB::table('NbyhotelSwimmingListing')->where('hid',$id)->where('type','24 hour')->get();

    if($check24h->isEmpty()){
         
        if( $latitude !="" && $longitude !=""){
            $h24 =1;   
            $searchradius = 44; 
            $get24hours = DB::table("TPHotel")           
                ->select('id', 'name','location_id','slug',
                            DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
                * cos(radians(TPHotel.Latitude)) 
                * cos(radians(TPHotel.longnitude) - radians(" . $longitude . ")) 
                + sin(radians(" . $latitude . ")) 
                * sin(radians(TPHotel.Latitude))) AS distance"))     
                ->having('distance', '<=', $searchradius)  
                ->where('amenities', 'like', '%24 hour Front Desk Service%')  
                ->where('location_id',$locid) 
                ->where('id', '!=', $id) 
                ->orderBy('distance')
                ->limit(4)            
                ->get();

           return print_r( $get24hours);
            //    die();
            $data2=array();
                if(!$get24hours->isEmpty()){
                
                    foreach($get24hours as $nbdata){
                       
                        $data2 = array(
                        'hid'=>$id,
                        'hotelid'=>$nbdata->id,
                        'name'=>$nbdata->name,
                        'location_id'=>$nbdata->location_id,
                        'slug'=>$nbdata->slug,
                        'distance'=>round($nbdata->distance),
                        'radius'=>$searchradius,  
                        'type'=>'24 hour',                                     
                    );
                  
                 DB::table('NbyhotelSwimmingListing')->insert($data2);
                    }
               //     return print_r( $data );
                
                    
                }
            
               

               
        }


      
   
}
//end 24 hours





        if ($nbh == 1 || $h24 == 1) {
            $nearby_hotelsswiming = DB::table('NbyhotelSwimmingListing')->where('hid', $id)->where('type', 'Swimming Pool')->get();
            $swimming_html = view('get_swimmingdata', ['nearby_hotelsswiming' => $nearby_hotelsswiming])->render();

            $nearby_24hours = DB::table('NbyhotelSwimmingListing')->where('hid', $id)->where('type', '24 hour')->get();
            $hours24_html = view('amenity_result.get24hoursdata', ['nearby_24hours' => $nearby_24hours])->render();

            return response()->json(['swimming_html' => $swimming_html, 'hours24_html' => $hours24_html]);
        }


    }
}
