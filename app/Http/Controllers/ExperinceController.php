<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ExperinceController extends Controller
{
    public function index()
    {
        return view('experience.index'); 
    }
    public function search_experince(request $request){
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

        return view('experience.filter_data',['data'=>$getatr]);
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

    public function searchCity(request $request){
        $search = $request->get('val');

        $result = array();
    
        $query = DB::table('Location')
            ->Leftjoin('Country', 'Location.CountryId', '=', 'Country.CountryId')
            ->select('Location.LocationId','Location.Name as lname','Country.Name as countryName','Country.CountryId')
            ->where('Location.Name', 'LIKE', '%' . $search . '%')
            ->limit(4)
            ->get();
    
        foreach ($query as $loc) {
            $result[] = [
                'id' => $loc->LocationId,
                'value' => $loc->lname,
                'country' => $loc->countryName,
                'countryid' => $loc->CountryId
            ];
        }
    
        return response()->json($result);
    }
    public function add_experience(){
        return view('experience.add_experience');
    }

    public function store_experience(request $request){
       $name = $request->get('name'); 
       $slug =  Str::slug($request->get('slug')); 
       $MetaTagTitle = $request->get('MetaTagTitle'); 
       $MetaTagDescription = $request->get('MetaTagDescription'); 
       $about = $request->get('about'); 
       $addressline1 = $request->get('addressline1'); 
       $addressline2 = $request->get('addressline2'); 
       $neighborhood = $request->get('neighborhood'); 
       $ctname = $request->get('ctname'); 
       $pincode = $request->get('pincode'); 
       $Latitude = $request->get('Latitude'); 
       $Longitude = $request->get('Longitude'); 
       $mobile_tickets = $request->get('mobile_tickets'); 
       $duration = $request->get('duration');   
       $Confirmation = $request->get('Confirmation'); 
       $free_cancellation = $request->get('free_cancellation'); 
       $pickup = $request->get('pickup'); 
       $bookingfee = $request->get('bookingfee'); 
       $Language = $request->get('Language'); 
       $website = $request->get('website'); 
       $phone = $request->get('phone'); 
       $email = $request->get('email'); 
       $adult_price = $request->get('adult_price'); 
       $price_minor = $request->get('price_minor'); 
       $Inclusions = $request->get('Inclusions'); 
       $exclusions = $request->get('exclusions'); 
       $departure_point = $request->get('departure_point'); 
       $return_point = $request->get('return_point'); 
       $additionaldetails = $request->get('additionaldetails'); 

       $locationid="";
        if($ctname != ""){
            $location = DB::table('Location')->where('Name',$ctname)->get();
            $locationid = $location[0]->LocationId;
        }
     
       $data = array(
            'Name'=>$name,
            'SeoTag'=>$name,
            'Slug'=>$slug,
            'MetaTitle'=>$MetaTagTitle,
            'MetaDescription'=>$MetaTagDescription,
            'about'=>$about,
            'Pincode'=>$pincode,
            'Latitude'=>$Latitude,
            'Longitude'=>$Longitude,          
            'Duration'=>$duration,
            'Confirmation'=>$Confirmation,
            'FreeCancellation'=>$free_cancellation,
            'Pickup'=>$pickup,
            'booking_fee'=>$bookingfee,
            'mobile_tickets'=>$mobile_tickets,           
            'website'=>$website,         
            'adult_price'=>$adult_price,
            'minor_price'=>$price_minor,
            'Inclusive'=>$Inclusions,
            'Exclusive'=>$exclusions,
            'DeparturePoint'=>$departure_point,
            'ReturnDetails'=>$return_point,
            'AdditionalInformation'=>$additionaldetails,
            'LocationId'=> $locationid,
            'IsActive'=>0,  
       );

       $lastexpid= DB::table('Experience')->insertGetId($data);

       

       $selectedLanguages = $request->input('language');

        if($selectedLanguages !=""){
        $languageIds = DB::table('ExperienceLanguage')
            ->whereIn('Language', $selectedLanguages)
            ->pluck('ExperienceLanguageId');
        foreach ($languageIds as $languageId) {
            $langdata = array(
                'ExperienceId'=>$lastexpid,
                'ExperienceLanguageId'=>$languageId,
            );
            DB::table('ExperienceLanguageAssociation')->insert($langdata);
        }
        }


       $address = $addressline1 . ($addressline2 != "" ? ', ' . $addressline2 : '');
        $contactdetail = array(
            'ExperienceId'=>$lastexpid,
            'Address'=>$address,
            'Phone'=>$phone,
            'Email'=>$email,
        );
        DB::table('ExperienceContactDetail')->insert($contactdetail);

        if($neighborhood !=""){
            $neighbdata= array(
                'ExperienceId'=>$lastexpid,
                'Name'=>$neighborhood,
                'Latitude'=>$Latitude,
                'Longitude'=>$Longitude,
                'Pincode'=>$pincode,
            );
            DB::table('ExperienceNeighborhoods')->insert($neighbdata);
        }

        return redirect()->route('experience')->with('success','Data added Successfully.');

    }
    public function edit_experience($id){
     
        $get_exp = DB::table('Experience')
            ->leftJoin('Location', 'Experience.LocationId', '=', 'Location.LocationId')
            ->select('Experience.*','Location.Name as Lname','Location.CountryId')
            ->where('Experience.ExperienceId', $id)
            ->get();
        
            $getcountry =[];
            if(!$get_exp->isEmpty() ){
                if($get_exp[0]->CountryId !=""){
                $getcountry =  DB::table('Country')->where('CountryId',$get_exp[0]->CountryId)->get();
                }
            }        
        $get_neigh =  DB::table('ExperienceNeighborhoods')->where('ExperienceId',$id)->get();
        $ExpContactdetail =  DB::table('ExperienceContactDetail')->where('ExperienceId',$id)->get();
      


        $getlang = DB::table('ExperienceLanguageAssociation')
            ->leftJoin('ExperienceLanguage', 'ExperienceLanguageAssociation.ExperienceLanguageId', '=', 'ExperienceLanguage.ExperienceLanguageId')
            ->select('ExperienceLanguageAssociation.*','ExperienceLanguage.Language')
            ->where('ExperienceLanguageAssociation.ExperienceId', $id)
            ->get();

        return view('experience.edit_experience',['get_exp'=>$get_exp,'get_neigh'=>$get_neigh,'ExpContactdetail'=>$ExpContactdetail,'getcountry'=>$getcountry,'getlang'=>$getlang]);
    }

    public function update_experience(request $request){
       $name = $request->get('name'); 
       $slug =  Str::slug($request->get('slug')); 
       $MetaTagTitle = $request->get('MetaTagTitle'); 
       $MetaTagDescription = $request->get('meta_desc'); 
       $about = $request->get('about'); 
       $addressline1 = $request->get('addressline1'); 
       $addressline2 = $request->get('addressline2'); 
       $neighborhood = $request->get('neighborhood'); 
       $ctname = $request->get('ctname'); 
       $pincode = $request->get('pincode'); 
       $Latitude = $request->get('Latitude'); 
       $Longitude = $request->get('Longitude'); 
       $mobile_tickets = $request->get('mobile_tickets'); 
       $duration = $request->get('duration');   
       $Confirmation = $request->get('Confirmation'); 
       $free_cancellation = $request->get('free_cancellation'); 
       $pickup = $request->get('pickup'); 
       $bookingfee = $request->get('bookingfee'); 
       $Language = $request->get('Language'); 
       $website = $request->get('website'); 
       $phone = $request->get('phone'); 
       $email = $request->get('email'); 
       $adult_price = $request->get('adult_price'); 
       $price_minor = $request->get('price_minor'); 
       $Inclusions = $request->get('Inclusions'); 
       $exclusions = $request->get('exclusions'); 
       $departure_point = $request->get('departure_point'); 
       $return_point = $request->get('return_point'); 
       $additionaldetails = $request->get('additionaldetails'); 

       $exp_id = $request->get('exp_id'); 

       $locationid="";
        if($ctname != ""){
            $location = DB::table('Location')->where('Name',$ctname)->get();
            $locationid = $location[0]->LocationId;
        }
     
       $data = array(
            'Name'=>$name,
            'SeoTag'=>$name,
            'Slug'=>$slug,
            'MetaTitle'=>$MetaTagTitle,
            'MetaDescription'=>$MetaTagDescription,
            'about'=>$about,
            'Pincode'=>$pincode,
            'Latitude'=>$Latitude,
            'Longitude'=>$Longitude,          
            'Duration'=>$duration,
            'Confirmation'=>$Confirmation,
            'FreeCancellation'=>$free_cancellation,
            'Pickup'=>$pickup,
            'booking_fee'=>$bookingfee,
            'mobile_tickets'=>$mobile_tickets,           
            'website'=>$website,         
            'adult_price'=>$adult_price,
            'minor_price'=>$price_minor,
            'Inclusive'=>$Inclusions,
            'Exclusive'=>$exclusions,
            'DeparturePoint'=>$departure_point,
            'ReturnDetails'=>$return_point,
            'AdditionalInformation'=>$additionaldetails,
            'LocationId'=> $locationid,
            'IsActive'=>0,  
       );

       $lastexpid= DB::table('Experience')->where('ExperienceId',$exp_id)->update($data);

       
      $returnlan = DB::table('ExperienceLanguageAssociation')->where('ExperienceId',$exp_id)->get();
       $selectedLanguages = $request->input('language');

        if($selectedLanguages !=""){
        $languageIds = DB::table('ExperienceLanguage')
            ->whereIn('Language', $selectedLanguages)
            ->pluck('ExperienceLanguageId');
        foreach ($languageIds as $languageId) {
            $langdata = array(
                'ExperienceId'=>$exp_id,
                'ExperienceLanguageId'=>$languageId,
            );
        if(!$returnlan->isEmpty()){
            DB::table('ExperienceLanguageAssociation')->where('ExperienceId',$exp_id)->update($langdata);
        }else{
            DB::table('ExperienceLanguageAssociation')->insert($langdata);
        }
            
        }
        }


       $address = $addressline1 . ($addressline2 != "" ? ', ' . $addressline2 : '');
        $contactdetail = array(
            'ExperienceId'=>$exp_id,
            'Address'=>$address,
            'Phone'=>$phone,
            'Email'=>$email,
        );
       $getctdetail = DB::table('ExperienceContactDetail')->where('ExperienceId',$exp_id)->get();
        if(!$getctdetail->isEmpty()){
            DB::table('ExperienceContactDetail')->where('ExperienceId',$exp_id)->update($contactdetail);
        }else{
            DB::table('ExperienceContactDetail')->insert($contactdetail);
        }


        if($neighborhood !=""){
            $neighbdata= array(
                'ExperienceId'=>$exp_id,
                'Name'=>$neighborhood,
                'Latitude'=>$Latitude,
                'Longitude'=>$Longitude,
                'Pincode'=>$pincode,
            );

            $getctdetail = DB::table('ExperienceNeighborhoods')->where('ExperienceId',$exp_id)->get();
            if(!$getctdetail->isEmpty()){
                return   DB::table('ExperienceNeighborhoods')->where('ExperienceId',$exp_id)->update($neighbdata);           
            }else{
                return  DB::table('ExperienceNeighborhoods')->insert($neighbdata);
            }

            
        }

        // redirect()->route('experience')->with('success','Data added Successfully.');
    }

    public function edit_exp_faq($id){
      $getfaq = DB::table('ExperienceQuestion')
        ->Leftjoin('Experience','ExperienceQuestion.ExperienceId', '=' ,'Experience.ExperienceId')
        ->select('ExperienceQuestion.*','Experience.Name as expName')
        ->where('ExperienceQuestion.ExperienceId',$id)->get();
        return view('experience.edit_exp_faq',['getfaq'=>$getfaq]);
    }

   
    public function add_exp_faq(request $request){
        $question = $request->get('checkboxText');
        $ExperienceId = $request->get('ExperienceId');
        $data = array(
             'Question'=>$question,
             'ExperienceId'=>$ExperienceId,
             'IsActive' => 1,
             'CreatedDate' => now(),
        );
        DB::table('ExperienceQuestion')->insert($data);
 
        $hotelfaq = DB::table('ExperienceQuestion')
        ->Leftjoin('Experience','ExperienceQuestion.ExperienceId', '=' ,'Experience.ExperienceId')
        ->select('ExperienceQuestion.*','Experience.Name as expName')
        ->where('ExperienceQuestion.ExperienceId',$ExperienceId)->get();
       return view('experience.edit_exp_faq',['getfaq'=>$hotelfaq]);
     }

     public function update_exp_faq(request $request){

        $id =  $request->get('faqId');
       // $currentDate = Carbon::today()->toDateString();
        $data = array(
            'Question' => $request->get('question'),
            'Answer' => $request->get('answer'),
            'IsActive' => 1,
            'CreatedDate' => now(),
        );
        
        return  DB::table('ExperienceQuestion')->where('id',$id)->update($data);

    }

    //add Itinerary
    public function add_Itinerary(){
        return view('experience.add_Itinerary');
    }
    public function store_itinerary(Request $request)
    {
        $exp_id = $request->get('exp_id');
        $itineraries = $request->input('Itinerary', []);
        $sequence = 1;
    
        foreach ($itineraries as $itinerary) {
            foreach ($itinerary as $day => $attraction) {   
             
                $newItinerary = [
                    'ExperienceId' => $exp_id,
                    'ItnineraryDay' => $day,
                    'Name' => $attraction,
                    'ItninerarySequence' => $sequence,
                    'IsActive' => 1,
                ];
    
                DB::table('ExperienceItninerary')->insert($newItinerary);
    
                $sequence++;
            }
        }
        
        return redirect()->route('edit_itinerary', [$exp_id])->with('success', 'Data Added Successfully.');
    }
    

//     public function store_itinerary(Request $request)
//     {
//         $attractions = $request->input('Itinerary', []);
//         print_r();
//         die();
//         $days = $request->input('ItnineraryDay', []);
//         $sequences = $request->input('ItninerarySequence', []);
//         $experienceId = $request->input('experience_id');
    
//         foreach ($attractions as $index => $attraction) {
//          echo  'a'.  $attraction;
//             echo 'd'. $days[$index];
//             echo 'seq'. $sequences[$index];
//             // $newItinerary = [
//             //     'ExperienceId' => $experienceId,
//             //     'ItnineraryDay' => $days[$index],
//             //     'Name' => $attraction,
//             //     'ItninerarySequence' => $sequences[$index],
//             //     'IsActive' => 1,
//             // ];
    
//             // DB::table('ExperienceItninerary')->insert($newItinerary);
//         }
//  //   print_r($newItinerary );

//         // Redirect or return response
//     }
    
    
  
    
    

    
  

    public function edit_itinerary($id){
        $getdata = DB::table('ExperienceItninerary')->where('ExperienceId',$id)->get();
      
        return view('experience.edit_Itinerary',['getdata'=>$getdata]);
    }

    public function update_itinerary(request $request){
        $exp_id = $request->get('exp_id');
        $id = $request->get('id');
       return $value = $request->input('value');
       

        // foreach ($itineraries as $itinerary) {
        //     foreach ($itinerary as $day => $attraction) {   
             
        //         $newItinerary = [
        //             'ExperienceId' => $exp_id,
        //             'ItnineraryDay' => $day,
        //             'Name' => $attraction,
        //             'ItninerarySequence' => $sequence,
        //             'IsActive' => 1,
        //         ];
    
        //         DB::table('ExperienceItninerary')->where('ExperienceId',$id)->update($newItinerary);
    
        //         $sequence++;
        //     }
        // }
      
             
        $newItinerary = [               
            'Name' => $value,                   
        ];

        return    DB::table('ExperienceItninerary')->where('ExperienceItnineraryId',$id)->where('ExperienceId',$exp_id)->update($newItinerary);
            
         
        return redirect()->route('edit_itinerary', [$exp_id])->with('success', 'Data Added Successfully.');
    }

  //add new
  public function additinerary(Request $request)
  {
      $exp_id = $request->get('exp_id');
      $itineraries = $request->input('dataArray'); // Decode JSON to array
  
      $lastSequence = DB::table('ExperienceItninerary')->where('ExperienceId', $exp_id)->orderBy('ItninerarySequence', 'desc')->value('ItninerarySequence');
      
      if(!empty($lastSequence)){
          $sequence = $lastSequence + 1; // Increment the sequence
      } else {
          $sequence = 1;
      }
  
      foreach ($itineraries as $day => $attraction) {
          $newItinerary = [
              'ExperienceId' => $exp_id,
              'ItnineraryDay' => $attraction,
              'Name' =>  $day,
              'ItninerarySequence' => $sequence,
              'IsActive' => 1,
          ];
  
             DB::table('ExperienceItninerary')->insert($newItinerary);
          $sequence++;
      }
      
      return response()->json([
        'status' => 'success',
        'message' => 'Data Added Successfully.'
    ]);
  }
  
 // review start

  public function exp_reviews($id){
    $getreview = DB::table('ExperienceReview')->where('ExperienceId',$id)->get();
    $getexp = DB::table('Experience')->where('ExperienceId',$id)->get();
    return view('experience.exp_reviews',['getreview'=>$getreview,'getexp'=>$getexp]);
    
  }
    

    public function update_expreview(request $request){
        $id = $request->get('id');
        $expid = $request->get('expid');
        
          DB::table('ExperienceReview')->where('Id',$id)->update(['IsApprove'=>$request->get('value')]);
          $getreviews =  DB::table('ExperienceReview')->where('ExperienceId',$expid)->where('IsApprove',$request->get('value'))->get();
          if($request->get('fval') != ""){
            $val1 =$request->get('fval');
          }else{
            $val1 =$request->get('value');
          }

          
          return view('experience.sort_review',['getreview'=>$getreviews,'val'=>$val1]);  
        
     
    }
    
    public function ftrexprewview(request $request){
        $val1 = $request->get('val');
        $id = $request->get('id');
       
        
        if (strpos($val1, ',') !== false) {
            $explodedValues = explode(',', $val1);
             $val1 =  $explodedValues[0];
             $val2 =  $explodedValues[1];
             $val3 =  $explodedValues[2];
             $getreviews =  DB::table('ExperienceReview')->where('ExperienceId',$id)->where('IsApprove',$val1)->orWhere('IsApprove',$val2)->orWhere('IsApprove',$val3)->get();
        } else {
            $getreviews =  DB::table('ExperienceReview')->where('ExperienceId',$id)->where('IsApprove',$val1)->get();
        }
       
      
        
        return view('experience.sort_review',['getreview'=>$getreviews,'val'=>$val1]);  
    }

    public function sortexpReview(request $request){
        if($request->get('val') != ""){
            $orderby = $request->get('val');
        }else{
            $orderby = "desc";
        }
        $id = $request->get('id');

      
        if(!empty($request->get('filter_option'))){
            //filter with options like aprove            
            $filter_option = $request->get('filter_option');

            if (strpos($filter_option, ',') !== false) {
                $explodedValues = explode(',', $filter_option);
                 $val1 =  $explodedValues[0];
                 $val2 =  $explodedValues[1];
                 $val3 =  $explodedValues[2];
                 $getreviews =  DB::table('ExperienceReview')->where('ExperienceId',$id)->where('IsApprove',$val1)->orWhere('IsApprove',$val2)->orWhere('IsApprove',$val3)->orderby('Id',$orderby)->get();
            }else{
                $getreviews =  DB::table('ExperienceReview')->where('ExperienceId',$id)->where('IsApprove',$filter_option)->orderby('Id',$orderby)->get();
            }     

        }else{
          $filter_option = 0;          
            $getreviews =  DB::table('ExperienceReview')->where('ExperienceId',$id)->orderby('Id',$orderby)->get();
        }

        return view('experience.sort_review',['getreview'=>$getreviews,'val'=>$filter_option]);  
    }

    public function filterexpbyid(request $request){ 
        
        $val = $request->get('val');
        $getreviews =  DB::table('ExperienceReview')->where(function($query) use ($val) {
            if (strlen($val) <= 1) {
                $query->where('Id', 'LIKE', '%' . $val . '%');
            } else {
                $query->where('Id', $val);
            }
        })
        ->limit(3)->get();
        
         return view('experience.sort_review',['getreview'=>$getreviews]);  
        
    }  

    // end review start
    // category start

    public function edit_exp_category($id){
        $get_cat = DB::table('CategoryExperienceAssociation')
        ->join('CategoryExperience','CategoryExperience.CategoryExperienceId','CategoryExperienceAssociation.CategoryExperienceId')
        ->select('CategoryExperienceAssociation.*','CategoryExperience.*')
        ->where('CategoryExperienceAssociation.ExperienceId',$id)->get();


        return view('experience.edit_exp_category',['get_cat'=>$get_cat]);
    }

    public function search_exp_category(Request $request)
    {
      
        $search = $request->get('val');

        $result = array();
    
        $query = DB::table('CategoryExperience')
            ->where('CategoryExperience.Name', 'LIKE', $search . '%')
            ->limit(4)
            ->get();
    
        foreach ($query as $cat) {
            $result[] = [
                'id' => $cat->CategoryExperienceId,
                'value' => $cat->Name,
            ];
        }
    
        return response()->json($result);
    }

    
public function addexpcat(Request $request)
{
    $category = $request->input('value');
    $exp_id = $request->input('id');

    // Get the CategoryExperienceId for the given category
    $categoryExperienceId = DB::table('CategoryExperience')
        ->where('Name', $category)
        ->value('CategoryExperienceId');

    if ($categoryExperienceId !== null) {
        // Check if the association already exists
        $associationExists = DB::table('CategoryExperienceAssociation')
            ->where('ExperienceId', $exp_id)
            ->where('CategoryExperienceId', $categoryExperienceId)
            ->exists();

        if (!$associationExists) {
            // Insert the association if it doesn't exist
            $data = array(
                'CategoryExperienceId' => $categoryExperienceId,
                'ExperienceId' => $exp_id
            );

            DB::table('CategoryExperienceAssociation')->insert($data);
        }else{
            return 2;
        }
    }else{
        return 3;
    }

    // Retrieve the updated list of associations
    $get_cat = DB::table('CategoryExperienceAssociation')
        ->join('CategoryExperience', 'CategoryExperience.CategoryExperienceId', '=', 'CategoryExperienceAssociation.CategoryExperienceId')
        ->select('CategoryExperienceAssociation.*', 'CategoryExperience.*')
        ->where('CategoryExperienceAssociation.ExperienceId', $exp_id)
        ->get();

    return view('experience.filter_exp_cat', ['get_cat' => $get_cat,'expid'=>$exp_id]);
}




  public function deleteexp_category(request $request){
   $id = $request->get('id');
   $expid = $request->get('expid');
  
   DB::table('CategoryExperienceAssociation')->where('CategoryExperienceAssociationId',$id)->where('ExperienceId',$expid)->delete();

   $get_cat = DB::table('CategoryExperienceAssociation')
   ->join('CategoryExperience','CategoryExperience.CategoryExperienceId','CategoryExperienceAssociation.CategoryExperienceId')
   ->select('CategoryExperienceAssociation.*','CategoryExperience.*')
   ->where('CategoryExperienceAssociation.ExperienceId',$expid)->get();


   return view('experience.filter_exp_cat',['get_cat'=>$get_cat,'expid'=>$expid]);

  }
     //end category start

     //edit image
     public function edit_exp_images($id){

        $getimage = DB::table('ExperienceImage')->where('ExperienceId',$id)->get();
        $getexp = DB::table('Experience')->where('ExperienceId',$id)->get();
        return view('experience.edit_exp_images',['getimage'=>$getimage,'getexp'=>$getexp]);
  
    }
    public function upload_exp_Image(Request $request,$id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
      
        $imagetype = $request->get('image_type');
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('experience-images'), $imageName);


            if($imagetype == 1){
                $data = array(
                    'ExperienceId'=>$id,
                    'Image'=>$imageName,
                    'IsPrimary'=>1,
                    'IsActive'=>1
                );
            }else{
                $data = array(
                    'ExperienceId'=>$id,
                    'Image'=>$imageName,
                    'IsPrimary'=>0,
                    'IsActive'=>1,
                );
            }
           
            $getreview = DB::table('ExperienceImage')->insert($data);

            return redirect()->route('edit_exp_images', ['id' => $id])->with('success', 'Image uploaded successfully.');


        }
 
    }

    public function delete_exp_image(request $request){
        $id = $request->get('id');
        $expid =  $request->get('expid');
  
        $image =  DB::table('ExperienceImage')->where('ExperienceImageId',$id)->get();
            $image[0]->Image;
       
        if ($image) {
  
          $imagePath = public_path('experience-images/' . $image[0]->Image);
          if (File::exists($imagePath)) {
              File::delete($imagePath);
              DB::table('ExperienceImage')->where('ExperienceImageId', $id)->delete();
  
              $getrest = DB::table('Experience')->where('ExperienceId',$expid)->get();
              $getreview = DB::table('ExperienceImage')->where('ExperienceId',$expid)->get();
      
              
              return view('experience.filter_exp_image',['getimage'=>$getreview,'expid'=>$getrest]);
          } else {
              
              DB::table('ExperienceImage')->where('ExperienceImageId', $id)->delete();
  
              $getrest = DB::table('Experience')->where('ExperienceId',$expid)->get();
              $getreview = DB::table('ExperienceImage')->where('ExperienceId',$expid)->get();
      
              
              return view('experience.filter_exp_image',['getimage'=>$getreview,'expid'=>$getrest]);
          }
  
        }
    
        return response()->json(['message' => 'Image not found'], 404);
      }

      
      public function filter_exp_image(request $request){
        $expid = $request->get('expid');
        $val = $request->get('val');
        if($val != ""){
            $getreviews =  DB::table('ExperienceImage')->where(function($query) use ($val) {
                if (strlen($val) <= 1) {
                   // $query->where('Id', 'LIKE', '%' . $val . '%');
                    $query->where('ExperienceImageId', $val);
                } else {
                    $query->where('ExperienceImageId', $val);
                }
            })
            ->limit(2)->get();
        }else{
            $getreviews =  DB::table('ExperienceImage')->where('ExperienceId', $expid)
            ->get();
        }
        $getrest = DB::table('Experience')->where('ExperienceId',$expid)->get();

    
        return view('experience.filter_exp_image',['getimage'=>$getreviews,'getexp'=>$getrest]); 
        
    }  
    
}
