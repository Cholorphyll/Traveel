<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing.index'); 
    }

    public function search_landing_page(Request $request)
    {
        $val = $request->get('value');
        $type = strtolower($request->get('type'));

        // Initialize as empty paginator if no matching type
        $getatr = DB::table('TPHotel_landing')->paginate(1)->appends(request()->query());

        switch ($type) {
            case 'hotel':
                $getatr = DB::table('TPHotel_landing')
                    ->select('id', 'Name', 'location_id')
                    ->where('Name', 'LIKE', '%' . $val . '%')
                    ->orWhere('id', $val)
                    ->orWhere('location_id', $val)
                    ->paginate(10)
                    ->appends(request()->query());
                break;

            case 'attraction':
                $getatr = DB::table('SightLanding')
                    ->select('ID', 'Page_Name', 'Slug', 'Meta_Title')
                    ->where('Page_Name', 'LIKE', $val . '%')
                    ->orWhere('Slug', 'LIKE', $val . '%')
                    ->orWhere('ID', $val)
                    ->paginate(10)
                    ->appends(request()->query());
                break;

            case 'restaurant':
                $getatr = DB::table('Restaurant_landing')
                    ->select('id','Name','location_id')
                    ->where('Name', 'LIKE', '%' . $val . '%')
                    ->orWhere('id', $val)
                    ->orWhere('location_id', $val)
                    ->paginate(10)
                    ->appends(request()->query());
                break;

            case 'experience':
                $getatr = DB::table('Experience_landing')
                    ->select('id','Name','location_id')
                    ->where('id', $val)
                    ->orWhere('location_id', $val)
                    ->paginate(10)
                    ->appends(request()->query());
                break;

		case 'hotellisting':
                $getatr = DB::table('hotel_listing_landing')
                    ->select('id','Name','location_id')
                    ->where('id', $val)
                    ->orWhere('location_id', $val)
                    ->paginate(10)
                    ->appends(request()->query());
                break;
        }
        if ($request->ajax()) {
            return view('landing.filterdata_partial', [
                'data' => $getatr,
                'type' => $type, // Ensure $type is passed here
            ])->render();
        }

        return view('landing.filterdata', [
            'data' => $getatr,
            'type' => $type,
        ]);
    }

    // edit attraction landing 

    public function add_sight_landing_page($id){
        $result = DB::table('SightLanding')->where('SightId',$id)->get();
        return view('landing.add_sight_landing_page',['result'=>$result]);
    }
   

    public function edit_sight_landing($id){
        $get_landing = DB::table('SightLanding')->where('ID',$id)->get();
        return view('landing.edit_sight_landing_page',['get_landing'=>$get_landing]);
    }
    
 
  //end edit attraction landing

  //edit hotel landing
    public function edit_hotel_landing($id){
        $getlanding = DB::table('TPHotel_landing')
        ->Leftjoin('TPHotels','TPHotels.hotelid','=','TPHotel_landing.hotelid')
        ->select('TPHotel_landing.*','TPHotels.name','TPHotels.hotelid')
        ->where('TPHotel_landing.id',$id)->get();
        return view('landing.edit_hotel_landing_page',['getlanding'=>$getlanding]);
    }
  //end edit hotel landing

    public function search_addlanding(){
        return view('landing.search_addlanding'); 
    }
    public function search_add_landing_page(Request $request)
    {


        $val = $request->get('value');
        $type = $request->get('type');

        try {
            if ($type == 'Attraction') {
                $getatr = DB::table('Sight')
                    ->select([
                        'SightId',
                        'Title',
                        'Slug'
                    ])
                    ->where(function ($query) use ($val) {
                        $query->where('SightId', '=', $val)
                              ->orWhere('Title', '=', $val)
                              ->orWhere('Slug', '=',$val);
                    })
                    ->limit(2)
                    ->get();
            }
            elseif ($type == 'Hotel') {
                $getatr = DB::table('TPHotel')
                    ->select([
                        'hotelid',
                        'name',
                        'slug'
                    ])
                    ->where(function ($query) use ($val) {
            $query->where('hotelid', '=', $val) // Exact match on hotelid
                  ->orWhere('name', '=', $val)  // Exact match on name
                  ->orWhere('slug', '=', $val);
                    })
                    ->limit(2)
                    ->get();

            }
            elseif ($type == 'Restaurant' || $type == 'Restaurent') {  // Support both spellings

                $getatr = DB::table('Restaurant')
                    ->select([
                        'RestaurantId',
                        'Title',
                        'Slug'
                    ])
                    ->where(function ($query) use ($val) {
                        $query->where('RestaurantId', '=', $val)
                              ->orWhere('Title', '=',$val)
                              ->orWhere('Slug', '=', $val);
                    })
                    ->limit(2)
                    ->get();


                // Set type to Restaurant for view consistency
                $type = 'Restaurant';
            }
            elseif ($type == 'Experience') {
                $getatr = DB::table('Experience')
                    ->select([
                        'ExperienceId',
                        'Name',
                        'Slug'
                    ])
                    ->where(function ($query) use ($val) {
                        $query->where('ExperienceId', '=',$val)
                              ->orWhere('Name', '=',$val)
                              ->orWhere('Slug', '=',$val);
                    })
                    ->limit(2)
                    ->get();

            }
            elseif ($type == 'hotellisting') {
                $getatr = DB::table('Location')
                    ->select([
                        'LocationId',
                        'Name',
                        'Slug'
                    ])
                    ->where(function ($query) use ($val) {
                        $query->where('LocationId', '=', $val)
                              ->orWhere('Name', '=', $val )
                              ->orWhere('Slug', '=', $val);
                    })
                    ->limit(2)
                    ->get();

            }
            else {
                $getatr = collect([]);

            }

            return view('landing.filter_add_result_data', [
                'data' => $getatr,
                'type' => $type
            ])->render();

        } catch (\Exception $e) {
           
            
            return response()->json([
                'error' => 'An error occurred while searching',
                'message' => $e->getMessage()
            ], 500);
        }
    }
  
    public function add_hotel_landing($id){
        $result = DB::table('TPHotel_landing')->where('hotelid',$id)->get();
        return view('landing.add_hotel_landing',['result'=> $result]);
    }
    public function add_exp_landing($id){
        $get_landing = DB::table('Experience_landing')->where('id',$id)->get();
        return view('landing.add_exp_landing',['get_landing'=>$get_landing]);
    }
  
    public function search_language(Request $request)
    {
        $search = $request->get('val');
        $result = array();
    
        $query = DB::table('ExperienceLanguage')
            ->where('ExperienceLanguage.Language', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();
    
        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->ExperienceLanguageId,
                'value' => $cat->Language,
            ];
        }
    
        return response()->json($result);
    }

    public function store_exp_landing(request $request){
        $name = $request->name;
        $slug = $request->slug;
        $meta_title = $request->meta_title;
        $meta_desc = $request->meta_desc;
        $about = $request->about; 
        $exp_id = $request->exp_id;  
        $nearbytype = $request->nearbytype;
        $nearby = $request->nearby;
   
        $category_value = json_encode($request->category_value);
        $star_rating = json_encode($request->star_rating);
        $duration_value = json_encode($request->duration_value);
       
        $Experience_Tags_val = json_encode($request->Experience_Tags_val);    
        $Mobile_Ticket_value = json_encode($request->Mobile_Ticket_value);
        $Languages_value = json_encode($request->Languages_value);

        $data = array(
            'Page_Name' =>$name,
            'Slug' =>$slug,
            'Meta_Title' =>$meta_title,
            'Meta_Description' =>$meta_desc,
            'About' =>$about,
            'exp_id' =>$exp_id,
            'Near_Type' =>$nearbytype,
            'Nearby' =>$nearby,
            'Category' =>$category_value,
            'Ratings' =>$star_rating,
            'Duration' =>$duration_value,
            'Experience_Tags' =>$Experience_Tags_val,
            'Mobile_Ticket' =>$Mobile_Ticket_value,
            'Languages' =>$Languages_value,
            'status' => 0,


        );
        return  DB::table('Experience_landing')->insert($data);
         
    }

    public function edit_exp_landing($id){
        $result = DB::table('Experience_landing')->where('ID',$id)->get();
        return view('landing.edit_exp_landing',['get_landing'=>$result]);
    }

 

       public function update_exp_landing(request $request){
        $name = $request->name;
        $slug = $request->slug;
        $meta_title = $request->meta_title;
        $meta_desc = $request->meta_desc;
        $about = $request->about; 
        $exp_id = $request->exp_id;  
        $nearbytype = $request->nearbytype;
        $nearby = $request->nearby;
        $id = $request->id;
        $category_value = json_encode($request->category_value);
        $star_rating = json_encode($request->star_rating);
        $duration_value = json_encode($request->duration_value);
       
        $Experience_Tags_val = json_encode($request->Experience_Tags_val);    
        $Mobile_Ticket_value = json_encode($request->Mobile_Ticket_value);
        $Languages_value = json_encode($request->Languages_value);

        $data = array(
            'Page_Name' =>$name,
            'Slug' =>$slug,
            'Meta_Title' =>$meta_title,
            'Meta_Description' =>$meta_desc,
            'About' =>$about,
            'exp_id' =>$exp_id,
            'Near_Type' =>$nearbytype,
            'Nearby' =>$nearby,
            'Category' =>$category_value,
            'Ratings' =>$star_rating,
            'Duration' =>$duration_value,
            'Experience_Tags' =>$Experience_Tags_val,
            'Mobile_Ticket' =>$Mobile_Ticket_value,
            'Languages' =>$Languages_value,
            'status' => 0,
        );
        return  DB::table('Experience_landing')->where('ID',$id)->update($data);
         
    }

    public function hidepage_exp(request $request){
        $landingid =  $request->get('landing');
            $data = array(
                'status' => 0,
            );
        
        return DB::table('Experience_landing')->where('ID',$landingid)->update($data);
    }

    public function delete_landing_exp(request $request){
        $id =  $request->get('landing');
        return  DB::table('Experience_landing')->where('ID',$id)->delete();   
   }

   // store hotel listing landing 
   

   public function store_hotel_listing_landing(request $request){
 
    $name = $request->name;
    $slug = $request->slug;
    $meta_title = $request->meta_title;
    $meta_desc = $request->meta_desc;
    $about = $request->about; 
    $keywords = $request->keywords;  
    $nearbyid = $request->nearbyid;
    $nearbytype = "";
    if($nearbyid !=""){
        $nearbytype = $request->nearbytype;
    }   
    $nearby = $request->nearby;
  
    $locationId = $request->locationId;
    $hotelmntarray = is_array($request->hotelmntarray) ? implode(',', $request->hotelmntarray) : '';
    $ratingarray = '';
    if (is_array($request->ratingarray)) {
    $cleanedRatings = array_map(function($rating) {
        return str_replace(' Star', '', $rating);
    }, $request->ratingarray);
    $ratingarray = implode(',', $cleanedRatings);
    }

    $data = array(
        'name' => $name,
        'slug' => $slug,
        'amenity' => $hotelmntarray,
        'keyword' => $keywords,
        'rating' => $ratingarray,
        'location_id'=>$locationId,

        'nearby_type'=>$nearbytype,
        'nearby_name'=>$nearby,
        'nearby_id'=>$nearbyid,    
        'meta_tag_title' => $meta_title,
        'meta_tag_description' => $meta_desc,
        'about' => $about,  

    );


   return DB::table('hotel_listing_landing')->insert($data);
	
}  

	public function store_hotel_listing_landing_csv(Request $request ,)
{
	 // $locationId = $id;

    if ($request->hasFile('csv_file')) {
        $file = $request->file('csv_file');
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // Extract headers
        $headers = $data[0];
        unset($data[0]);

        $bulkInsertData = [];

        foreach ($data as $row) {
            $rowData = array_combine($headers, $row);

            // Prepare data for insertion
            $hotelmntarray = isset($rowData['amenity']) ? $rowData['amenity'] : '';
            $ratingarray = isset($rowData['rating']) ? str_replace(' Star', '', $rowData['rating']) : '';

            $bulkInsertData[] = [
                'name' => $rowData['keyword'] ?? '',
                'slug' => $rowData['keyword1'] ?? '',
                'amenity' => $rowData['Features'] ?? '',
                'keyword' => $rowData['keyword1'] ?? '',
                'rating' => $ratingarray,
                'location_id' => is_numeric($rowData['LocationId'] ?? null) ? (int)$rowData['LocationId'] : null,
                'nearby_type' => $rowData['PropertyType'] ?? '',
                'nearby_name' => $rowData['nearby_name'] ?? '',
                'nearby_id' => $rowData['HotelBran'] ?? '',
                'meta_tag_title' => $rowData['keyword1'] ?? '',
                'meta_tag_description' => $rowData['Featuress'] ?? '',
                'about' => $rowData['Attraction'] ?? ''
            ];
        }

        // Insert all data in bulk
        if (!empty($bulkInsertData)) {
            DB::table('hotel_listing_landing')->insert($bulkInsertData);
        }

        return redirect('https://www.where2.co/landing/show-all-hotel-landing-listings')
                ->with('success', 'Landing pages created successfully.');
    }

    return response()->json(['error' => 'No CSV file uploaded.'], 400);
}

	
public function showHotelListings()
{
    $listings = DB::table('hotel_listing_landing')
        ->select([
            'name',
            'slug',
            'meta_tag_title',
            'location_id',
            'id'
        ])
        ->orderBy('id', 'desc')
        ->paginate(10);

     return view('landing/show_all_hotel_listing_landing', [
        'listings' => $listings
    ]);
}

public function add_hotel_listing_landing($id){
//  $get_landing = DB::table('Location')->select('LocationId as locationid')->where('LocationId',$id)->get();
	 $locationId = session('locationId'); 
    return view('landing.add_hotel_listing_landing',compact('id'));
}

public function deleteHotelListing($id)
{
    try {
        DB::table('hotel_listing_landing')->where('id', $id)->delete();
        return redirect()->route('show-all-hotel-landing-listings')->with('success', 'Landing page deleted successfully');
    } catch (\Exception $e) {
        return redirect()->route('show-all-hotel-landing-listings')->with('error', 'Error deleting landing page');
    }
}



public function editHotelListing($id)
{
    $listing = DB::table('hotel_listing_landing')->where('id', $id)->first();
    if (!$listing) {
        return redirect()->route('hotel.listings')->with('error', 'Landing page not found');
    }

    $ratings = explode(',', $listing->rating);
    $amenities = explode(',', $listing->amenity);

    return view('landing.edit_hotel_listing_landing', compact('listing', 'ratings', 'amenities'));
}

// Method to update the hotel listing
public function updateHotelListing(Request $request, $id)
{
 
    $hotelmntarray = is_array($request->hotelmntarray) ? implode(',', $request->hotelmntarray) : '';
    $ratingarray = '';
    if (is_array($request->ratingarray)) {
    $cleanedRatings = array_map(function($rating) {
        return str_replace(' Star', '', $rating);
    }, $request->ratingarray);
    $ratingarray = implode(',', $cleanedRatings);
    }
       // 'location_id' => $request->locationId,
  //  try {
        $data = [
            'name' => $request->name,
            'slug' => $request->slug,
            'meta_tag_title' => $request->meta_title,
            'meta_tag_description' => $request->meta_desc,
            'keyword' => $request->keywords,
            'about' => $request->about,
        
            'nearby_type' => $request->nearbytype,
            'nearby_name' => $request->nearby,
            'nearby_id' => $request->nearbyid,
            'rating' => $ratingarray,
            'amenity' => $hotelmntarray,
        ];
//return $data;
      return  DB::table('hotel_listing_landing')->where('id', $id)->update($data);

      //  return redirect()->route('hotel.listings')->with('success', 'Landing page updated successfully');
  /*  } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error updating landing page: ' . $e->getMessage());
    }*/
}

public function hotel_listing_landing_search(Request $request)
{
    // Start with a base query
    $query = DB::table('hotel_listing_landing');

    // Add search functionality for name
    if ($request->has('search')) {
        $searchTerm = $request->search;
        $query->where('name', 'LIKE', "%{$searchTerm}%");
    }

    // Get paginated results with search parameter preserved in pagination
    $listings = $query->orderBy('id', 'desc')
                     ->paginate(10)
                     ->withQueryString();

                  //   return $listings;
    return view('landing.gethotel_listing_landing_result',['listings'=>$listings]);
}

public function updateHotelLanding(Request $request, $id)
{
    try {
        Log::info('Received update request for hotel landing', [
            'id' => $id,
            'request_data' => $request->all()
        ]);

        $data = [
            // Basic Information
            'Name' => $request->input('Name'),
            'Slug' => $request->input('Slug'),
            'MetaTagTitle' => $request->input('MetaTagTitle'),
            'MetaTagDescription' => $request->input('MetaTagDescription'),
            'About' => $request->input('About'),
            
            // Page Type and Nearby Information
            'page_type' => $request->input('page_type'),
            'Nearby_Type' => $request->input('Nearby_Type'),
            'NearbyId' => $request->input('NearbyId'),
            'Nearbyname' => $request->input('Nearbyname'),
            
            // Hotel Features
            'Rating' => $request->input('Rating'),
            'HotelAmenities' => $request->input('HotelAmenities'),
            'Room_Amenities' => $request->input('Room_Amenities'),
            'Hotel_Pricing' => $request->input('Hotel_Pricing'),
            'RoomType' => $request->input('RoomType'),
            'Distance' => $request->input('Distance'),
            'Hotel_Style' => $request->input('Hotel_Style'),
            'OnSiteRestaurants' => $request->input('OnSiteRestaurants'),
            'hotel_tags' => $request->input('hotel_tags'),
            'PublicTransitAccess' => $request->input('PublicTransitAccess'),
            'Access' => $request->input('Access'),
            'updated_at' => now()
        ];

        Log::info('Prepared data for update:', ['data' => $data]);

        // Remove null values
        $data = array_filter($data, function($value) {
            return !is_null($value);
        });

        if (empty($data)) {
            throw new \Exception('No valid data provided for update');
        }

        $updated = DB::table('TPHotel_landing')
            ->where('id', $id)
            ->update($data);

        Log::info('Update result:', ['updated' => $updated]);

        if (!$updated) {
            Log::warning('No rows were updated in the database', [
                'id' => $id,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Hotel landing page updated successfully',
            'updated' => $updated
        ]);
    } catch (\Exception $e) {
        Log::error('Hotel landing update error: ' . $e->getMessage(), [
            'exception' => $e,
            'id' => $id,
            'request_data' => $request->all()
        ]);
        return response()->json([
            'status' => 'error',
            'message' => 'Error updating hotel landing page: ' . $e->getMessage()
        ], 500);
    }
}

//end hotel listing landing 

	
	
	 public function edit($id)
    {
        $getlanding = DB::table('Restaurant_landing')->where('RestaurantId', $id)->get();

        if ($getlanding->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'Restaurant not found.');
        }

        return view('landing.edit_restaurant_landing', compact('getlanding'));
    }

public function update_restaurant_landing(Request $request)
{
    // Validate input
    $request->validate([
        'id' => 'required|integer|exists:Restaurant_landing,id',
        'Name' => 'required|max:255',
        'Slug' => 'required|max:255',
        'MetaTagTitle' => 'required|max:100',
        'MetaTagDescription' => 'required|max:2000',
        'About' => 'required|max:2000',
        'restaurant_tags' => 'nullable',
        'Amenities' => 'nullable',
        'DiningOptions' => 'nullable',
        'CuisineType' => 'nullable|max:255',
        'Rating' => 'nullable',
        'Restaurant_Pricing' => 'nullable',
        'Distance' => 'nullable',
        'Restaurant_Style' => 'nullable',
        'OnSiteDining' => 'nullable',
        'RestaurantTags' => 'nullable',
        'PublicTransitAccess' => 'nullable',
        'RestaurantAmenities' => 'nullable',
        'Access' => 'nullable',
        'Nearby_Type' => 'nullable|max:255',
        'Nearbyname' => 'nullable|max:255',
        'NearbyId' => 'nullable|max:255',
        'location_id' => 'nullable|integer',
        'LocationId' => 'nullable|integer',
        'neighborhoodId' => 'nullable|integer',
    ]);

    // Update the restaurant landing entry
    DB::table('Restaurant_landing')
        ->where('id', $request->id)
        ->update([
            'Name' => $request->Name,
            'Slug' => $request->Slug,
            'MetaTagTitle' => $request->MetaTagTitle,
            'MetaTagDescription' => $request->MetaTagDescription,
            'About' => $request->About,
            'restaurant_tags' => $request->restaurant_tags,
            'Amenities' => $request->Amenities,
            'DiningOptions' => $request->DiningOptions,
            'CuisineType' => $request->CuisineType,
            'Rating' => $request->Rating,
            'Restaurant_Pricing' => $request->Restaurant_Pricing,
            'Distance' => $request->Distance,
            'Restaurant_Style' => $request->Restaurant_Style,
            'OnSiteDining' => $request->OnSiteDining,
            'RestaurantTags' => $request->RestaurantTags,
            'PublicTransitAccess' => $request->PublicTransitAccess,
            'RestaurantAmenities' => $request->RestaurantAmenities,
            'Access' => $request->Access,
            'Nearby_Type' => $request->Nearby_Type,
            'Nearbyname' => $request->Nearbyname,
            'NearbyId' => $request->NearbyId,
            'location_id' => $request->location_id,
            'LocationId' => $request->LocationId,
            'neighborhoodId' => $request->neighborhoodId,
            'status' => $request->status, // Optional, if status is provided
            'updated_at' => now(), // Add a timestamp if your table supports it
        ]);

    // Redirect with a success message
    return redirect()->route('edit_restaurant', ['id' => $request->id])
        ->with('success', 'Restaurant landing updated successfully.');
}

    public function filter(Request $request)
    {
        $type = strtolower($request->get('type'));
        $val = $request->get('value');
        $filter = strtolower($request->get('filter'));
        
        $query = null;
        
        switch ($type) {
            case 'hotel':
                $query = DB::table('TPHotel_landing')
                    ->select('id', 'Name', 'location_id')
                    ->where(function($q) use ($val) {
                        $q->where('id', $val)
                          ->orWhere('location_id', $val);
                    });
                break;
                
            case 'attraction':
                $query = DB::table('SightLanding')
                    ->select('ID', 'Page_Name', 'Slug', 'Meta_Title')
                    ->where(function($q) use ($val) {
                        $q->where('Page_Name', 'LIKE', $val . '%')
                          ->orWhere('Slug', 'LIKE', $val . '%')
                          ->orWhere('ID', $val);
                    });
                break;
                
            case 'restaurant':
                $query = DB::table('Restaurant_landing')
                    ->select('id', 'Name', 'location_id')
                    ->where(function($q) use ($val) {
                        $q->where('id', $val)
                          ->orWhere('location_id', $val);
                    });
                break;
        }
        
        if ($query && $filter) {
            // Apply filter to all text columns
            switch ($type) {
                case 'hotel':
                    $query->where('Name', 'LIKE', '%' . $filter . '%');
                    break;
                case 'attraction':
                    $query->where(function($q) use ($filter) {
                        $q->where('Page_Name', 'LIKE', '%' . $filter . '%')
                          ->orWhere('Meta_Title', 'LIKE', '%' . $filter . '%');
                    });
                    break;
                case 'restaurant':
                    $query->where('Name', 'LIKE', '%' . $filter . '%');
                    break;
            }
        }
        
        $data = $query ? $query->paginate(10)->appends($request->all()) : collect([]);
        
        return view('landing.filterdata_partial', [
            'data' => $data,
            'type' => $type
        ]);
    }
}
