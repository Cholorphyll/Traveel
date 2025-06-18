<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class NeighbourhoodController extends Controller
{
    public function neiborhood_listing($id,$slug) 
    { 
      //  $start =  date("H:i:s");
     
       
        $getloc = DB::table('Location as l')->select('l.Name as cityName','c.Name as countryName')
        ->join('Country as c','c.CountryId','=','l.CountryId')
        ->where('l.LocationId',$id)
        ->where('l.Slug',$slug)
        ->get();
        if($getloc->isEmpty()){
            abort('404','url not found');
        }
        if(!$getloc->isEmpty()){
          
            $cityName = $getloc[0]->cityName;
            $lname = $getloc[0]->cityName;
            $countryName = $getloc[0]->countryName;

        }
        $getparent = DB::table('Location')->select('LocationLevel','ParentId')->where('LocationId', $id)->limit(1)->get();

        $locationPatent = [];
        $getloclink =collect();

        $getloclink = DB::table('Temp_Mapping as tm')
        ->select('tm.LocationId')
        ->where('tm.Tid',$id)
        ->get();
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
 	
   
        return view('frontend.Neighbourhood.neighbourhood_listing')->with('countryname',$countryName)->with('ctName',$cityName)->with('lname',$lname)->with('locationPatent',$locationPatent)->with('id',$id)->with('slug',$slug)->with('getloclink',$getloclink);
    } 



    public function fetch_neighb_listing(request $request){
            $id = $request->get('locationid');
          
            $slug = $request->get('slug');
            $searchresults=[];
            $searchresults = DB::table('Neighborhood')
                ->where('LocationID',$id) 
                ->paginate(10);

            $searchresults->setPath('nb-' . $id . '-' . $slug);

       
            $getloc = DB::table('Location as l')->select('l.Name as cityName','c.Name as countryName')
            ->join('Country as c','c.CountryId','=','l.CountryId')
            ->where('LocationId',$id)
            ->get();
            
    //  return print_r($getloc);
        if(!$getloc->isEmpty()){
          
            $cityName = $getloc[0]->cityName;
            $lname = $getloc[0]->cityName;
            $countryName = $getloc[0]->countryName;

        }
    
  
        return view('frontend.Neighbourhood.neighbor_result')->with('searchresults',$searchresults)->with('countryname',$countryName)->with('lname',$cityName);
    }
}
