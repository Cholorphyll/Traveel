<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
  public function index(){
    return view('category.index');
  }

  public function edit_att_category($id){
    $getsitecat =  DB::table('SightCategory')->leftJoin('Sight','Sight.SightId','=','SightCategory.SightId')->leftJoin('Category','Category.CategoryId','=','SightCategory.CategoryId')->select('SightCategory.*','Category.Title as ctitle','Sight.Title as stitle')->where('SightCategory.SightId',$id)->get();
      return view('category.edit_att_category',['get_cat'=> $getsitecat]);
  }
  public function search_cat_attracion(request $request){
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
        
    return view('category.filter_att',['attraction'=>$getatr,'val'=>'attraction']);

   }

 
    public function search_cat_hotel(request $request){
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
        return view('category.filter_att',['hotellisting'=>$getlisting,'val'=>'hotel']);
    }
    public function edit_hotelcategory($id){
        $categoryIds = DB::table('TPHotels')
        ->where('hotelid', $id)
        ->pluck('CategoryId')
        ->toArray();
        $hname = DB::table('TPHotels')
        ->where('hotelid', $id)->get();
    
        $hotel_categories = [];
        $categoryTypes = [];
        
        foreach ($categoryIds as $categoryId) {
            $categoryTypes = array_merge($categoryTypes, DB::table('TPHotel_types')
                ->whereIn('id', explode(',', $categoryId))->get()->toArray());
        }
        
        return view('category.edit_hotel_category',['hotel_category'=>$categoryTypes,'hname'=>$hname]);
    }

    public function search_cat_experience(request $request){
        $val =  $request->get('value');
        
        $getatr = DB::table('Experience')
        ->select('Experience.*')
        ->where(function ($query) use ($val) {
            $query->where(
                'Experience.ExperienceId', $val)->orWhere(
                    'Experience.Name', $val)->orWhere(
                    'Experience.Slug', $val);

            // URL से ID और अन्य मान निकालें
            if (strpos($val, '-') !== false) {
                $urlParts = explode('-', $val);
                $id = isset($urlParts[2]) ? $urlParts[2] : null;
                
                error_log('Extracted ID: ' . $id);
                
                if ($id) {
                    $query->orWhere('Experience.ExperienceId', $id);
                }
            }
        })->limit(2)
        ->get();
        return view('category.filter_experience',['data'=>$getatr]);
    }

    public function edit_experience_category($id){
        $get_cat = DB::table('CategoryExperienceAssociation')
        ->join('CategoryExperience','CategoryExperience.CategoryExperienceId','CategoryExperienceAssociation.CategoryExperienceId')
        ->select('CategoryExperienceAssociation.*','CategoryExperience.*')
        ->where('CategoryExperienceAssociation.ExperienceId',$id)->get();

        return view('category.edit_exp_category',['get_cat'=>$get_cat]);
    }



}
