<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FaqController extends Controller
{
   public function index(){
     return view('Faq.index');
   }
   public function searchfaqattracion(request $request){
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
        
    return view('Faq.filter_attr',['attraction'=>$getatr,'val'=>'attraction']);
   }

   public function edit_att_faq($id){
        
    $getfaq = DB::table('SightQuestion')->leftJoin('Sight','Sight.SightId','=','SightQuestion.SightId')
    ->select('SightQuestion.*','Sight.Title')
    ->where('SightQuestion.SightId',$id)->get();
    
    return view('Faq.edit_sight_faq',['getfaq'=>$getfaq]);
    }
  

    public function filter_faq_hotel(request $request){
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
        return view('Faq.filter_attr',['hotellisting'=>$getlisting,'val'=>'hotel']);
    }


    public function edit_hotel_faq($id){
        $hotelfaq = DB::table('HotelQuestion')
        ->Leftjoin('TPHotels','HotelQuestion.HotelId', '=' ,'TPHotels.hotelid')
        ->select('HotelQuestion.*','TPHotels.name')
        ->where('HotelQuestion.HotelId',$id)->get();
        return view('Faq.edit_hotel_faq',['getfaq'=>$hotelfaq]);

    }


    public function search_faq_restaurant(request $request){
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
         return view('Faq.filter_rest',['hotellisting'=>$getlisting]);
    }
    public function edit_restaurant_faq($id){
        $getfaq = DB::table('RestaurantQuestion')
        ->Leftjoin('Restaurant','Restaurant.RestaurantId', '=' ,'RestaurantQuestion.RestaurantId')
        ->select('RestaurantQuestion.*','Restaurant.Title')
        ->where('RestaurantQuestion.RestaurantId',$id)->get();
      
        return view('Faq.edit_rest_faq',['getfaq'=>$getfaq]);
    }
    public function search_faq_experience(request $request){
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

   
         return view('Faq.filter_experience',['data'=>$getatr]);
    }
    
 
    public function edit_experience_faq($id){
        $getfaq = DB::table('ExperienceQuestion')
          ->Leftjoin('Experience','ExperienceQuestion.ExperienceId', '=' ,'Experience.ExperienceId')
          ->select('ExperienceQuestion.*','Experience.Name as expName')
          ->where('ExperienceQuestion.ExperienceId',$id)->get();
          return view('Faq.edit_experience_faq',['getfaq'=>$getfaq]);
      }


    
    // Attraction Q&A CRUD operations
    public function store_attraction_qa(Request $request){
        $validated = $request->validate([
            'hotelQuestid' => 'required|integer',
            'question' => 'required|string|max:255',
            'answer' => 'required|string'
        ]);
        
        try {
            // Log the request data for debugging
            \Log::info('Store attraction Q&A request:', ['data' => $request->all()]);
            
            // Make sure we're using the correct case for column names
            DB::table('HotelQuestion')->insert([
                'HotelId' => $request->hotelQuestid,
                'Question' => $request->question, // Changed from 'question' to 'Question'
                'Answer' => $request->answer,     // Changed from 'answer' to 'Answer'
                'IsActive' => 1, // Set as active by default
                'CreatedDate' => now(),
                'updatedOn' => now(),
                'Likes' => 0, // Initialize likes to 0
                'Tags' => null, // Initialize tags as null
                'User_Name' => null, // Can be updated later if needed
                'Listing' => null, // Can be updated later if needed
                'faq_image' => null // Can be updated later if needed
            ]);
            
            return redirect()->back()->with('success', 'Question added successfully!');
        } catch (\Exception $e) {
            \Log::error('Error adding attraction question:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error adding question: ' . $e->getMessage());
        }
    }
    
    public function update_attraction_qa(Request $request){
        $validated = $request->validate([
            'hotelQuestid' => 'required|integer',
            'question' => 'required|string|max:255',
            'answer' => 'required|string'
        ]);
        
        DB::table('HotelQuestion')
            ->where('hotelQuestid', $request->hotelQuestid)
            ->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'updatedOn' => now()
            ]);
        
        return redirect()->back()->with('success', 'Question updated successfully!');
    }
    
    public function delete_attraction_qa(Request $request){
        $validated = $request->validate([
            'hotelQuestid' => 'required|integer'
        ]);
        
        DB::table('HotelQuestion')
            ->where('hotelQuestid', $request->hotelQuestid)
            ->delete();
        
        return redirect()->back()->with('success', 'Question deleted successfully!');
    }
    

   
      
}
