<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class ReviewController extends Controller
{
   public function index(){
    return view('reviews.index');
   }

   public function search_r_attracion(request $request){
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
            
        return view('reviews.filter_attr',['attraction'=>$getatr,'val'=>'attraction']);
    }
    public function edit_review(request $request,$id){
        $get_attname = DB::table('Sight')->select('Sight.Title')->where('SightId',$id)->get();
        $get_attreview =  DB::table('SightReviews')
        ->leftJoin('Users','Users.UserId','=','SightReviews.UserId')
        ->select('SightReviews.*','Users.FirstName','Users.LastName')->orderby('SightReviews.id','desc')
        ->where('SightReviews.SightId',$id)
        ->get();
        return view('reviews.edit_attraction_review',['get_attreview'=> $get_attreview,'get_attname'=>$get_attname]);
    }
    public function filter_r_hotel(request $request){
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
        return view('reviews.filter_attr',['hotellisting'=>$getlisting,'val'=>'hotel']);
    }
   
    public function edithotel_review($id){
        $gethid = DB::table('TPHotels')->where('hotelid',$id)->get();
     
        $getreviews =  DB::table('HotelReview')->where('HotelId',$id)->where('IsApprove',0)->get();
        
        return view('reviews.edit_hotel_review',['hotelreview'=>$getreviews,'gethid'=>$gethid]);
    }


    public function searchr_restaurant(request $request){
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
               
         return view('reviews.filter_restaurent',['hotellisting'=>$getlisting]);
    }
    
    public function edit_restaurant_reviews($id){
        
        $getrest = DB::table('Restaurant')->where('RestaurantId',$id)->get();
        $getreview = DB::table('RestaurantReview')->where('RestaurantId',$id)->get();
        return view('reviews.edit_restaurant',['restreview'=>$getreview,'getrest'=>$getrest]);
    }
    
    public function searchr_experience(request $request){
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

   
         return view('reviews.filter_experience',['data'=>$getatr]);
    }
    

    public function edit_exp_reviews($id){
        $getreview = DB::table('ExperienceReview')->where('ExperienceId',$id)->get();
        $getexp = DB::table('Experience')->where('ExperienceId',$id)->get();
        return view('reviews.edit_experience_review',['getreview'=>$getreview,'getexp'=>$getexp]);
        
      }

}
