<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Listing;
use Illuminate\Auth\Middleware\IsAdmin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ListingController extends Controller
{
    public function create()
    {
        $country = DB::table('Country')->orderby('Name')->get();
       
        return view('listing.add_listing',['country'=>$country]);
    }
    public function store(Request $request)
    {
        $get = $request->get('ctname');
		$parentid = null;
		if($get !=""){	
			$parts = explode(',', $get);
			$state = trim($parts[0]);

			$getpid =  DB::table('Location')->where('Name',$state)->get();
			$parentid = $getpid[0]->LocationId;
        }
    
        $request->validate([
               'location_name' => 'required',
               'page_slug' => 'required',
               'Countryid' => 'required',
        ]); 
        $Listing = new Listing;
        $Listing->ParentId=$parentid;
        $Listing->CountryId=$request->get('Countryid');
        $Listing->Name=$request->get('location_name');
        $Listing->Slug=Str::slug($request->get('page_slug'));
        $Listing->MetaTagTitle=$request->get('meta_title');
        $Listing->MetaTagDescription=$request->get('meta_desc');
        $Listing->UploadLocation=$request->get('upload_location');
        $Listing->About=$request->get('about');
        $Listing->IsActive = 1;
        $Listing->save();
    
        return redirect()->route('search_location')
        ->with('success','Location Added successfully.');             
    }

    public function edit_listing($id){
      
       // $Listing =  Listing::find($id);
        $Listing = DB::table('Location')->where('LocationId',$id)->get();
        $pid = $Listing[0]->ParentId;
        $getLoc =  DB::table('Location')->where('LocationId',$pid)->get();
        $country = DB::table('Country')->orderby('Name')->get();
       
       return view('listing.edit_listing',['country'=>$country, 'listing'=>$Listing ,'getLoc'=> $getLoc]);
    }
    public function update(Request $request)
    {
       $id = $request->get('locId');
       $get = $request->get('parLoc');
		$parentid =null;
		if($get !=""){
			$parts = explode(',', $get);
			$state = trim($parts[0]);
			$getpid =  DB::table('Location')->where('Name',$state)->get();
       		$parentid = $getpid[0]->LocationId;
		}

	
        
        // $request->validate([
        //     'location_name' => 'required',
        //     'page_slug' => 'required',
        //     'Countryid' => 'required',
        // ]); 
        $Listing = Listing::find($id);
        $Listing->ParentId=$parentid;
        $Listing->CountryId=$request->get('Countryid');
        $Listing->Name=$request->get('Name');
        $Listing->Slug=Str::slug($request->get('page_slug'));
        $Listing->MetaTagTitle=$request->get('meta_title');
        $Listing->MetaTagDescription=$request->get('meta_desc');
        $Listing->UploadLocation=$request->get('pincode');
        $Listing->About=$request->get('about');
        $Listing->save();
    
        $Listing = DB::table('Location')->where('LocationId',$id)->get();
        $pid = $Listing[0]->ParentId;
        $getLoc =  DB::table('Location')->where('LocationId',$pid)->get();
        $country = DB::table('Country')->orderby('Name')->get();
       
       return view('listing.updated_location',['country'=>$country, 'listing'=>$Listing ,'getLoc'=> $getLoc]);           
    }

    public function preview($id){
        $getlisting = DB::table('Location')->leftJoin('Country','Country.CountryId','=','Location.CountryId')->select('Location.*','Country.Name as countryname')
        ->where('Location.LocationId',$id)->get();
        return view('listing.location_preview',['listing'=>$getlisting]);
    }
    
    public function location(){
        return view('listing.location');
    }

    public function search_location(request $request) {
        $val = $request->get('value');
        
        $getlisting = DB::table('Location')
            ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
            ->select('Location.*', 'Country.Name as countryname')
            ->where(function ($query) use ($val) {
                $query->where('Location.LocationId', 'LIKE', '%' . $val . '%')
                      ->orWhere('Location.Name', 'LIKE', '%' . $val . '%')
                      ->orWhere('Location.Slug', 'LIKE', '%' . $val . '%');
            })
            ->limit(10)
            ->get();
            
        if ($getlisting->isEmpty()) {
            $query = DB::table('Location AS l')
                ->select('l.*', 'c.Name as countryname')
                ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
                ->where(function ($query) use ($val) {
                    $query->where(DB::raw("LOWER(CONCAT(l.Name, ' ', c.Name))"), 'LIKE', '%' . strtolower($val) . '%');
                })
                ->limit(10);
            
            $getlisting = $query->get();
        }
           
        return view('listing.filter_location', ['listing' => $getlisting]);
    }
     
    public function search_parentcon(Request $request)
        {
        $search = $request->get('val');
        
        $result = array();

        $query = DB::table('Country')
            ->where('Name', 'LIKE', '%' . $search . '%')
            ->get();

        foreach ($query as $country) {
            $result[] = ['id' => $country->CountryId, 'value' => $country->Name];
        }

        return response()->json($result);
    }
    
    /* -------------- Faqs ------------------*/

  
    public function add_faq(){
        return view('listing.add_faq');
    }
    Public function store_loc_faq(request $request){
        $request->validate([
            'ctname' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ]);
        $locId ="";
     
            $ctname =  $request->get('ctname');
            $getLoc =  DB::table('Location')->where('Name',$ctname)->get();
           
            if($getLoc->isEmpty()){
                return redirect()->route('add_faq')->with('error','City not found. Please try again.');
            }else{

                $locId =  $getLoc[0]->LocationId;
            }

       
        $currentDate = Carbon::today()->toDateString();
        $data = array(
            'LocationId' => $locId,
            'Question' => $request->get('question'),
            'Answer' => $request->get('answer'),
            'IsActive' => 1,
            'CreatedDate' => $currentDate,
        );
        
        DB::table('LocationQuestion')->insert($data);

        return redirect()->route('search_location')->with('success','Faq added Successfully.');
    }
   
    public function edit_faq($id){
        $getfaq = DB::table('LocationQuestion')->leftJoin('Location','Location.LocationId','=','LocationQuestion.LocationId')
        ->select('LocationQuestion.*','Location.Name')
        ->where('LocationQuestion.LocationId',$id)->get();
      $getloc =  DB::table('Location')->where('LocationId',$id)->get();
        return view('listing.edit_faq',['getfaq'=>$getfaq , 'getloc'=>$getloc]);
    } 

    public function update_edit(request $request){
         $id =  $request->get('faqId');
         $locationid = $request->get('locationid');
         $answers = $request->input('answer');
         if( $request->input('answer') == ""){
            $answers = " ";
         }
         $modifiedAnswer = str_replace(["\r\n", "\r", "\n"], "|||", $answers);
         $listing =[];
       if (strpos($modifiedAnswer, '|||') !== false) {
          $items = explode("|||", $modifiedAnswer);        
            $items = array_map('trim', $items);
            $items = array_filter($items);
            $answers = array_shift($items);
            $restData = implode("|||", $items);
            $removenum = preg_replace('/\d+\.\s*/', ',', $restData);
        
            $list = str_replace("|||", "", $removenum);
            $arrayList = explode(",", $list);
           
            foreach($arrayList as $list){
               $getid = DB::table('Sight')->where('Title',$list)->get();
               
               if (!empty($list)) {
                    if(!$getid->isEmpty()){
                        $sightid = $getid[0]->SightId;               
                        $listing[] = ["name" => $list, "url" => $sightid];
                   }else{
                    $answers = $request->input('answer');
                   }
                }

            }

          //  return json_encode($listing);
       }

        $currentDate = Carbon::today()->toDateString();
        $data = array( 
            'Question' => $request->get('question'),
            'Answer' => $answers,
            'listing' =>$listing,
            'IsActive' => 1,
            'CreatedDate' => $currentDate,
        );
        
        DB::table('LocationQuestion')->where('LocationQuestionId',$id)->update($data);

      
        $getfaq = DB::table('LocationQuestion')->leftJoin('Location','Location.LocationId','=','LocationQuestion.LocationId')
        ->select('LocationQuestion.*','Location.Name')
        ->where('LocationQuestion.LocationId',$locationid)->get();
        return view('listing.updated_faq',['getfaq'=>$getfaq]);
    }


    public function add_location_faq(request $request){
        $question = str_replace('?','',$request->get('checkboxText')) ;

        $locationid = $request->get('locationid');
        $getdata = DB::table('LocationQuestion')->where('Question',$question)->where('LocationId',$locationid)->get();
      
        if(!$getdata->isEmpty()){
            return 3;
        }
        $data = array(
             'Question'=>$question,
             'LocationId'=>$locationid,
             'IsActive' => 1,
             'CreatedDate' => now(),
             'Answer'=>'',
        );
        DB::table('LocationQuestion')->insert($data);
 
        $getfaq = DB::table('LocationQuestion')->leftJoin('Location','Location.LocationId','=','LocationQuestion.LocationId')
        ->select('LocationQuestion.*','Location.Name')
        ->where('LocationQuestion.LocationId',$locationid)->get();
        return view('listing.updated_faq',['getfaq'=>$getfaq]);
   
     }

}