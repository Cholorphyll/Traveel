<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Traits\CustomBusinessRedirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;

use Aws\S3\S3Client;


use Vonage\Client;
use Vonage\Client\Credentials\Basic;


class BusinessController extends Controller
{
  use CustomBusinessRedirect;
	   protected $vonageClient;

  public function __construct()
  {
      $basic  = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
      $this->vonageClient = new Client($basic);
  }
	
    protected function redirectTo()
    {
        if (!Auth::guard('business_user')->check()) {
            return route('custom_business_login'); // Change 'custom_business_login' to your desired login route
        }

        return route('business_dashboard');
    }

	 

   

  public function businessindex(){
   // return 2;
   if (session()->has('business_user')){
    
    return redirect()->route('business_dashboard');
   }else{
    return view('business.index');
   } 
    
  }  
  public function save_user(Request $request) 
  {
      // Validate the form data
      $validatedData = $request->validate([          
        'email' => 'required|email|unique:business_user|max:255',
        'username' => 'required|unique:business_user|max:255',
    ]);
  
    // Check if email already exists
    $existingUser = DB::table('business_user')->where('email', $request->get('email'))->first();

    if ($existingUser) {
     
        return 0;
    }
    $existingUsername = DB::table('business_user')->select('busi_username')->where('busi_username', $request->get('username'))->first();

    if ($existingUsername) {
     
        return 'u0';
    }

    $verification_token = sha1(time() .$request->get('email') . uniqid());
    
   
      $data = array(  
        'username' => $request->get('fname').' '.$request->get('lname'),
        'busi_username' =>  $request->get('username'),
        'email' =>  $request->get('email'),
        'password' =>  Hash::make($request->get('password')),
	    	 'EmailVerificationCode'=>$verification_token ,
      );
     
      
      DB::table('business_user')->insert($data);

      
        // Send verification email
        $subject = 'Verify Your Email';
        $body = 'Please click on the following link to verify your email: ' . route('business_varification', ['token' => $verification_token]);
        $to = $request->email;
    
        try {
            // Attempt to send the email
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });
    
            // Email sent successfully
           // return redirect('/user_login')->with('success', 'Registration successful. Please check your email for verification.');
            return 21;
        } catch (\Exception $e) {
            // Email sending failed
            \Log::error('Failed to send email: ' . $e->getMessage());
           // return redirect('/user_login')->with('error', 'Failed to send verification email. Please try again later.');
            return 22;
        }
    

      return redirect()->back()->with('success', 'Form submitted successfully!');
  }
  
  
 
  public function searchLocation(request $request)
  {          

      if(request('val')){     

          $searchText = request('val');
          $lastSpaceIndex = strrpos($searchText, ' ');
          
          if($lastSpaceIndex != ""){
              $locationText = trim(substr($searchText, 0, $lastSpaceIndex));
               $countryText = trim(substr($searchText, $lastSpaceIndex + 1));
          }
          // if($lastSpaceIndex != ""){
          //     $locationText = trim(substr($searchText, 0, $lastSpaceIndex));
          //      $countryText = trim(substr($searchText, $lastSpaceIndex + 1));
          // }
          
          $query = DB::table('Location AS l')
          ->select('l.LocationId AS id', DB::raw("CONCAT(l.Name, ', ', c.Name) AS displayName"),'l.Slug AS Slug')
          ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
          ->where('l.Name', 'LIKE', $searchText . '%') // First match against location names         
          ->limit(5);

          $locations = $query->get();
         

            
          if ($locations->isEmpty()) {
          
              $query = DB::table('Location AS l')
              ->select('l.LocationId AS id', DB::raw("CONCAT(l.Name, ', ', c.Name) AS displayName"),'l.Slug AS Slug')
              ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
              ->where('l.Slug', 'LIKE', $searchText . '%') // First match against location names
            //  ->orWhere('l.Name', 'LIKE', ','. $searchText. '%')
              ->limit(5);

              $locations = $query->get();


          }

          if ($locations->isEmpty()) {
          
              $query = DB::table('Location AS l')
              ->select('l.LocationId AS id', DB::raw("CONCAT(l.Name, ', ', c.Name) AS displayName"),'l.Slug AS Slug')
              ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
              ->where('l.Slug', 'LIKE', $searchText . '%') // First match against location names
            //  ->orWhere('l.Name', 'LIKE', ','. $searchText. '%')
              ->limit(5);

              $locations = $query->get();


          }
        
          if ($locations->isEmpty()) {            

         
              $query = DB::table('Location AS l')
              ->select(
                  'l.LocationId AS id',
                  DB::raw("CONCAT(l.Name, ', ', c.Name) AS displayName"),
                  'l.Slug AS Slug',
                  DB::raw("CONCAT(l.Name, ', ', c.Name) AS concatenatedName")
              )
              ->join('Country AS c', 'l.CountryId', '=', 'c.CountryId')
              ->where(function ($query) use ($searchText) {
                  $query->where(DB::raw("LOWER(CONCAT(l.Name, ' ', c.Name))"), 'LIKE', '%' . strtolower($searchText) . '%');
              })
              ->limit(5);
          
          $locations = $query->get();

          }

          if ($locations->isEmpty()) {
          
            $query = DB::table('TPLocations AS l')
            ->select('l.LocationId AS id', DB::raw("CONCAT(l.cityName, ', ', l.countryName) AS displayName"),'l.Slug AS Slug')
           
            ->where('l.fullName', 'LIKE', $searchText . '%') // First match against location names
            ->orWhere('cityName','LIKE', $searchText . '%')
          //  ->orWhere('l.Name', 'LIKE', ','. $searchText. '%')
            ->limit(5);

            $locations = $query->get();


        }
      //    return   print_r($locations );
          $result = [];
          if(!empty($locations)){
              foreach ($locations as $loc) {
                  $result[] = ['id'=>$loc->id,'value' => $loc->displayName,];
                  }
          }else{
      
               $result[]  = [ 'value' => "Result not founds"];
          }


          return view('business.busi_search_result',['searchresults' => $result]);


       }


  }
   
  public function searchbusiness(request $request)
  {          

      if(request('val')){     

          $searchText = request('val');
         
          // if($lastSpaceIndex != ""){
          //     $locationText = trim(substr($searchText, 0, $lastSpaceIndex));
          //      $countryText = trim(substr($searchText, $lastSpaceIndex + 1));
          // }
          
          $query = DB::table('TPHotel')
          ->select('hotelid as id' ,'slug as Slug','name as displayName')       
          ->where('name', 'LIKE', $searchText . '%') // First match against location names         
          ->limit(5);

          $locations = $query->get();
         $type = 'hotel';

       
          if ($locations->isEmpty()) {
          
            $query = DB::table('Sight')
            ->select('SightId as id','Title as displayName','Slug')
           
            ->where('Title', 'LIKE', $searchText . '%') // First match against location names
          //  ->orWhere('l.Name', 'LIKE', ','. $searchText. '%')
            ->limit(5);

            $locations = $query->get();
            $type = 'sight';

        }

        if ($locations->isEmpty()) {
          
          $query = DB::table('business_detail')         
          ->select('name as displayName' ,'slug as Slug','id')
          ->where('name', 'LIKE', $searchText . '%') 
          ->limit(5);

          $locations = $query->get();
          $type = 'business';

         }

      //    return   print_r($locations );
          $result = [];
          if(!empty($locations)){
              foreach ($locations as $loc) {
                  $result[] = ['id'=>$loc->id, 'Slug' => $loc->Slug,'value' => $loc->displayName,];
               }
          }else{
      
               $result[]  = [ 'value' => "Result not founds"];
          }


          return view('business.busi_search_result',['searchresults' => $result,'type'=>$type]);


       }


  }

//   public function loginbusiness(Request $request)
// {

//   $email = $request->get('email');
//   $password = $request->get('password');

//   // Hash the password
//   $hashedPassword = $password;

//   // Use the hashed password in credentials
//   $credentials = [
//       'email' => $email,
//       'password' => $hashedPassword,
//   ];
//   $authenticationResult = Auth::guard('business_user')->attempt($credentials);

//   //$user = DB::table('business_user')->where('email', $email)->where('password',$hashedPassword)->first();
// // return $user;
// if ($authenticationResult ) {
//   // Authentication successful
//  // return 1;
//   $user = Auth::guard('business_user')->user();
//       // Session::put('business_user', $user->username);
//       // return 2;
//       return redirect()->route('business_dashboard');
//   } else {
//       // return redirect()->route('businessindex');
//       return 0;
//   }
//   //   $email = $request->get('email');
//   //   $password = $request->get('password');
//   //   $credentials = $request->only('email', 'password');
 
//   //   $user = DB::table('business_user')->where('email', $email)->first();    
   
//   //     if (Auth::guard('business_user')->attempt($credentials)) {
//   //       $user = Auth::guard('business_user')->user();     
//   //     //  Session::put('business_user', $user->username);
  
//   // //      return 2;
       
//   //    //   return redirect()->route('business_dashboard');
//   //   } else {
//   //  //   return redirect()->route('businessindex');
//   //       return 0;
//   //   }

//   }

public function loginbusiness(Request $request)
{
    $email = $request->get('email');
    $password = $request->get('password');
    
    // Check if the user with the given email is verified
    $user = DB::table('business_user')
        ->where('email', $email)
        ->where('IsEmailVerify', 1)
        ->first();

    if ($user) {
        // Attempt to authenticate the user with the given credentials
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];
        
      $authenticationResult = Auth::guard('business_user')->attempt($credentials);

    if ($authenticationResult) {

      if($user){
        $busiuser = [
          'id' => $user->id,
          'name' => $user->username,
          'Username' => $user->busi_username,
          'email' => $user->email,
          'user_image' => $user->user_image
      ];
  
      session(['business_user' => $busiuser]);
      }
     
		
		
    // Set a persistent cookie
         $lifetime = 525600; // 1 year in minutes
         $cookieParams = session_get_cookie_params();
         $cookieParams['lifetime'] = $lifetime * 60; // Lifetime in seconds
         session_set_cookie_params($cookieParams);

         // Log the session parameters for debugging
         \Log::info('Session parameters:', $cookieParams);
         
         // Set the session token cookie manually
         Cookie::queue('laravel_session', session()->getId(), $lifetime);
 
 //end cookie 
             $user = Auth::guard('business_user')->user();
     
			 return 2;
			return redirect()->route('business_dashboard');
        } else {
			
            
      		  return 0;
        }
    } else {
        return 0;
    }
}

public function dashboard(){
 
  $user =  Auth::guard('business_user')->user();

  $userId = $user->id;

  if ($user) {
  
      $getbusiness = DB::table('business_detail')->where('userid', $user->id)->get();   
      
      
      return view('business.dashboard', ['getbusiness' => $getbusiness]);
  }

 
 
}
  public function logout(Request $request)
  {
      Auth::logout();

      // Clear the session
      $request->session()->invalidate();

      // Redirect to the home page or wherever you want
      return redirect('/businessindex');
  }
  public function createbusiness(Request $request)
  {
    
 
    $getdata = DB::table('TPHotel_types')->get();
   
    return view('business.createbusiness',['getdata'=>$getdata]);
  }
  public function searchaddress(request $request)
  {          

      if(request('val')){      

          $searchText = request('val'); 
          $query = DB::table('TPHotel as h')
          ->select('h.hotelid as id', 'h.slug as Slug', DB::raw('CONCAT(h.address, ",, ", c.countryName) as displayName'))
          ->leftJoin('TPLocations as c', 'c.id', '=', 'h.location_id')
          ->where('address', 'LIKE', $searchText . '%')
          ->limit(5);
      

          $locations = $query->get();
         

            
          // if ($locations->isEmpty()) {
          
          //     $query = DB::table('Sight')
          //     ->select('id',DB::raw('CONCAT(h.address, ", ", c.Name) as displayName'),'Slug')
          //     ->leftJoin('Country as c', 'c.CountryId', '=', 'h.CountryId')
          //     ->where('address', 'LIKE', $searchText . '%') // First match against location names
          //   //  ->orWhere('l.Name', 'LIKE', ','. $searchText. '%')
          //     ->limit(5);

          //     $locations = $query->get();


          // }

         
      //    return   print_r($locations );
          $result = [];
          if(!empty($locations)){
              foreach ($locations as $loc) {
                  $result[] = ['id'=>$loc->id, 'Slug' => $loc->Slug,'value' => $loc->displayName,];
                  }
          }else{
      
               $result[]  = [ 'value' => "Result not founds"];
          }


          return view('business.address_result',['searchresults' => $result]);


       }


  }
  public function save_business(request $request){
    $user =  Auth::guard('business_user')->user();

    $userId = $user->id;
   $name = $request->get('name');
   $address = $request->get('address');
   $email = $request->get('email');
   $phone = $request->get('phone');
   $website = $request->get('website');
   $cattype = $request->get('cattype');
   $catVal = $request->get('catVal');
   $checkedval = $request->get('checkedval');
   $repreval = $request->get('repreval');
   $slug = Str::slug($name);
     $data = array(
        'name'=>$name,
        'address'=>$address,
        'email'=>$email,
        'phone'=>$phone,
        'website'=>$website,
        'category'=>$cattype,
        'cat_value'=>$catVal,  
        'representative'=> $repreval, 
        'userid'=> $userId,
        'slug'=>$slug,
   );
   return DB::table('business_detail')->insert($data);
  }
 
 
  public function view_bussines($id, $slug)
  {
    

    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Sight as s','s.SightId','=','b.business_id')
    ->leftjoin('Location as l','l.LocationId','=','s.LocationId')
    ->leftjoin('Sight_image as img','img.Sightid','=','b.business_id')
    ->select('b.*','l.slugid','img.*','s.TAAggregateRating','b.id as bid')
    ->where('b.id', $id)->where('b.slug',$slug)->get();  
    $user =  Auth::guard('business_user')->user();
     $getbusiness[0]->bid;

    $userId = $user->id;
    $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  
  // $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  
    return view('business.view_business',['getbusiness'=>$getbusiness,'getallbusiness'=>$getallbusiness]);

  }
  
    




  //new code forgot password 


  
public function buss_forgot_password(){
  return view('business.buss_forgot_password');
}

public function busi_sendlink_forgorpass(Request $request){

 $request->validate([
      'email' => 'required|email',
    
  ]);
 $email = $request->input('email');
 $subject = 'Reset Password';

 $getdata = DB::table('business_user')
 ->where('email', $email)
 ->get();

  if (!$getdata->isEmpty()) {
      $emailEncrypted = Crypt::encryptString($email);
     $url = url('/pass_reset_password').'/'.$emailEncrypted;

  $body = "Please click on the Password Reset link within 24 hours to reset your Travell Business password: <a href=\"{$url}\" target=\"_blank\">Password Reset link</a>";
  
      $to = $email;

      try {
          // Attempt to send the email

    Mail::html($body, function ($message) use ($to, $subject) {
      $message->to($to)
          ->subject($subject);
    });


          // Email sent successfully
          return redirect('/buss_forgot_password')->with('success', 'An email has been sent to your registered email address with instructions on how to reset your password.');
      } catch (\Exception $e) {
          // Email sending failed
          \Log::error('Failed to send email: ' . $e->getMessage());
          return redirect('/buss_forgot_password')->with('error', 'Failed to send verification email. Please try again later.');
      }
  } else {    
      return back()->with('error', 'Account not found. Please choose another email.');
  }
}


public function busi_reset_password($token) {
  try {
      $email = Crypt::decryptString($token);
   //   return   $email;
      return view('business.busi_reset_password', ['email' => $email]);
  } catch (DecryptException $e) {
      // Handle decryption error
      abort(404);
  }
}


public function busi_save_reset_password(request  $request){
  $request->validate([
      'password' => 'required|string|min:8',
      'confirm_password' => 'required|string|min:8|same:password',
  ]);

  $password =  $request->get('confirm_password');
  $email =  $request->get('email');

  $hashedPassword = Hash::make($password);


 $update = DB::table('business_user')
  ->where('Email', $email)
  ->update(['Password' => $hashedPassword]);
  if($update == 1){
    
      return redirect('businessindex')->with([
        'success' => 'Password Updated Successfully.*',
        'openModal' => true
    ]);
  }
  


}


public function business_varification(Request $request)
{
  $token = $request->query('token');
  $user = DB::table('business_user')->where('EmailVerificationCode', $token)->first();

  if ($user) {
    DB::table('business_user')->where('EmailVerificationCode', $token)
      ->update(['IsEmailVerify' => 1]);
      
      return redirect('businessindex')->with([
        'success' => 'Email verification successful. You can now login.',
        'openModal' => true
    ]);
    
  } else {
    return redirect('/signup')->with('error', 'Invalid verification token. Please try again.');
  }
}

public function edit_business_profile(){


  if (session()->has('business_user')) {
    $userData = session('business_user');
    $Username = $userData['Username'];
    $user_image = $userData['user_image'];
    $userId = $userData['id']; 
 }

  
  //  $userId = $user->id;
       // Accessing individual values
  

// $username = $userData['Username'];
// $email = $userData['email'];

    $getdata = DB::table('business_user as u')
    ->leftjoin('Location as l','l.LocationId','=','u.LocationId')
    ->leftjoin('Country as c','c.CountryId','=','l.CountryId')
    ->select('u.*','l.Name as lName','c.Name as cName')
    ->where('u.id',  $userId )
    ->get();   
    


    //return print_r($getpost );
     return view('business.edit_business_profile', ['getdata' => $getdata]);
 

  
}





public function bus_updateprofile(Request $request)
{
    $dataToUpdate = [];
	  $userid = $request->get('userid');
     $busi_username = $request->get('busi_username');

    $getval = DB::table('business_user')->select('busi_username')->where('id', $userid)->where('busi_username',$busi_username)->get();

   if($getval->isEmpty() && $busi_username != ""){

        $request->validate([
            'busi_username' => 'unique:business_user,busi_username' ,
        ]);
    }

    if (!empty($busi_username)) {
        $dataToUpdate['busi_username'] =  $busi_username;
    }
	
	
    $name = $request->get('name');

    $bio = $request->get('bio');
    $ctname = $request->get('location');
    $userid = $request->get('userid');

    $website = $request->get('website');
    
    if (!empty($name)) {
        $dataToUpdate['username'] = $name;
    }
    if (!empty($name)) {
        $dataToUpdate['website'] = $website;
    }

    if (!empty($bio)) {
        $dataToUpdate['Bio'] = $bio;
    }

    if ($ctname !="") {
        $lastCommaPos = strrpos($ctname, ',');
        if ($lastCommaPos !== false) {
            $countryName = substr($ctname, $lastCommaPos + 1);
            $ctname = substr($ctname, 0, $lastCommaPos);

            $getCont = DB::table('Country')->where('Name', $countryName)->first();
            if ($getCont) {
                $ctid = $getCont->CountryId;
                $getLoc = DB::table('Location')->where('Name', $ctname)->where('CountryId', $ctid)->first();
                if ($getLoc) {
                    $dataToUpdate['LocationId'] = $getLoc->LocationId;
                }
            }
        }
    }

//new code
if ($request->hasFile('image')) {
  
   

        $s3 = new S3Client([
            'region' => 'us-west-2',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => 'gXOZqDE4Gt/+d3xwagbIvzUk+RE+W7zHfuya5Zox',
            ],
        ]);


   
        $firstUrl =  $request->file('image');

        $firstUrl = str_replace('/320/', '/375/', $firstUrl);
        $firstUrl = str_replace('/240/', '/229/', $firstUrl);
        $imageContent = file_get_contents($firstUrl);
      

        // Resize image
       // $image = Image::make($imageContent)->resize(376, 229); // Adjust the dimensions as needed

        // Get the image content after resizing
        //     $imageContent = $image->encode('jpg')->stream();

    
                  //  $resizedImageContent = $image->stream('jpg')->getContents();
        $randomNumber = uniqid();
        $filename = 'busiuser' . $userid . '_' . $randomNumber . '.jpg';
        $dataToUpdate['user_image'] = $filename;

        try {
           
            $s3->putObject([
                'Bucket' => 's3-travell',
                'Key' => 'user-images/' . $filename,
                'Body' => $imageContent,
                'ContentType' => 'image/jpeg',
                'ACL' => 'private',
            ]);

         

        } catch (\Exception $e) {
           
            return "Error uploading image: " . $e->getMessage();
        }


   
    }

//end new code 




    // Perform the update only if there is data to update
    if (!empty($dataToUpdate)) {
        DB::table('business_user')->where('id', $userid)->update($dataToUpdate);
    }

    // Retrieve updated data
       $getdata = DB::table('business_user as u')
      ->leftjoin('Location as l','l.LocationId','=','u.LocationId')
      ->leftjoin('Country as c','c.CountryId','=','l.CountryId')
      ->select('u.*','l.Name as lName','c.Name as cName')
      ->where('u.id',  $userid )
      ->get();   
    


        if(! $getdata->isEmpty()){
            $busiuser = [
              'id' => $getdata[0]->id,
              'Username' => $getdata[0]->busi_username,
              'name' => $getdata[0]->username,
              'email' => $getdata[0]->email,
              'user_image' => $getdata[0]->user_image
          ];
      
          session(['business_user' => $busiuser]);


            
        }
       

    // Check if $getdata is not empty before returning the view
    if (!$getdata->isEmpty()) {
        $profileDataView = view('business.busi_profile_data', ['getdata' => $getdata])->render();
        $imageResultView = view('business.busi_image_result', ['getdata' => $getdata])->render();
        $usernavimg = view('business.nav_img_rest')->render();

 
        return response()->json([
            'profile_data' => $profileDataView,
            'image_result' => $imageResultView,
            'usernavimg' => $usernavimg
        ]);
    }

 
}



public function busi_changeprofileimg(Request $request)
{
    
    $userid = $request->get('userid');
    $dataup = $request->get('test');

    if($dataup == ""){
        $dataup = 0;
    }
    $dataToUpdate = [];

   

//new code
if ($request->hasFile('pimage')) {
  
   

        $s3 = new S3Client([
            'region' => 'us-west-2',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => 'gXOZqDE4Gt/+d3xwagbIvzUk+RE+W7zHfuya5Zox',
            ],
        ]);


   
        $firstUrl =  $request->file('pimage');

        $firstUrl = str_replace('/320/', '/375/', $firstUrl);
        $firstUrl = str_replace('/240/', '/229/', $firstUrl);
        $imageContent = file_get_contents($firstUrl);
      

       
        $randomNumber = uniqid();
        $filename = 'busi_user' . $userid . '_' . $randomNumber . '.jpg';
        $dataToUpdate['user_image'] = $filename;

        try {
           
            $s3->putObject([
                'Bucket' => 's3-travell',
                'Key' => 'user-images/' . $filename,
                'Body' => $imageContent,
                'ContentType' => 'image/jpeg',
                'ACL' => 'private',
            ]);

         

        } catch (\Exception $e) {
           
            return "Error uploading image: " . $e->getMessage();
        }


   
    }

//end new code 




    // Perform the update only if there is data to update
    if (!empty($dataToUpdate)) {
        DB::table('business_user')->where('id', $userid)->update($dataToUpdate);
    }

    // Retrieve updated data
    $getdata = DB::table('business_user as u')
        ->select('u.*')
        ->where('u.id', $userid)
        ->get();


    // Check if $getdata is not empty before returning the view
    if (!$getdata->isEmpty()) {
       
            $busiuser = [
              'id' => $getdata[0]->id,
              'Username' => $getdata[0]->busi_username,
              'name' => $getdata[0]->username,
              'email' => $getdata[0]->email,
              'user_image' => $getdata[0]->user_image
          ];
      
          session(['business_user' => $busiuser]);

          if($dataup == 1){
               $imageResultView = view('business.busi_image_result', ['getdata' => $getdata])->render();
                $usernavimg = view('business.nav_img_rest')->render();

            
                return response()->json([
                
                    'image_result' => $imageResultView,
                    'usernavimg' => $usernavimg
                ]);
         }elseif($dataup == 0){
            
            return  redirect()->route('edit_business_profile')->with('success','data updated.');
          }

         
    }

 
}


//add image

public function add_photos($id, $slug){

    $getbusiness = DB::table('business_detail')->where('id', $id)->where('slug',$slug)->get(); 
    
    if($getbusiness[0]->varify_business){
        $user =  Auth::guard('business_user')->user();
		 $sightid = $getbusiness[0]->business_id;
        $userId = $user->id;
        $getallbusiness = DB::table('business_detail')
        ->where('userid', $userId)->get();  
        $getimage = DB::table('Sight_image')->where('Sightid', $sightid)->get();  
//	return 	print_r( $getimage );
        return view('business.add_photos',['getbusiness'=>$getbusiness,'getallbusiness'=>$getallbusiness,'getimage'=>$getimage]);
    }else{
        $type = 'sight';
        return redirect()->route('choose_uploadid',[$id,$type]);
    }
  }
  public function add_sight_busi_img(request $request){
      $image = $request->file('image');
      $id = $request->get('id');
      
      $sightid = $request->get('sightid');
      $sightname = $request->get('sightname');
      
//new code

if ($request->hasFile('image')) {
  
   

    $s3 = new S3Client([
        'region' => 'us-west-2',
        'credentials' => [
            'key' => 'AKIAYEDFDCST62PQXQO5',
            'secret' => 'gXOZqDE4Gt/+d3xwagbIvzUk+RE+W7zHfuya5Zox',
        ],
    ]);



    $firstUrl =  $request->file('image');

    $firstUrl = str_replace('/320/', '/375/', $firstUrl);
    $firstUrl = str_replace('/240/', '/229/', $firstUrl);
    $imageContent = file_get_contents($firstUrl);
  

   
    $randomNumber = uniqid();
    $filename = 'busi_' . $randomNumber . '.jpg';
  

    try {
       
        $s3->putObject([
            'Bucket' => 's3-travell',
            'Key' => 'business-images/' . $filename,
            'Body' => $imageContent,
            'ContentType' => 'image/jpeg',
            'ACL' => 'private',
        ]);

     

    } catch (\Exception $e) {
       
        return "Error uploading image: " . $e->getMessage();
    }



}




//end new code 

        $data = [ 
            'Sightid'=>$sightid,
            'Image' => $filename, 
            'Title'=>$sightname,
            'IsActive'=>1,
        ];

        $getreview = DB::table('Sight_image')->insert($data);



        $getimage = DB::table('Sight_image')->where('Sightid', $sightid)->get();  
        return view('business.upload_image_result',['getimage'=>$getimage]);
        


  }


  public function view_hotel_business($id, $slug)
  {

    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('TPHotel as h','h.id','=','b.business_id')
    ->leftjoin('Temp_Mapping as t','t.LocationId','=','h.location_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','h.id as hid','b.id as bus_id','h.*','t.slugid as slgid','b.business_id','b.varify_business')
    ->where('b.id', $id)->where('b.slug',$slug)->get(); 
    
    $hotelid = $getbusiness[0]->business_id; 

    $getimage = DB::table('TPhotel_images')->where('hotelid', $hotelid)->get();
    $getreview = DB::table('HotelReview')->where('HotelId', $hotelid)->limit(10)->get();

    $user =  Auth::guard('business_user')->user();

    $userId = $user->id;
    $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  
    return view('business.view_hotel_business',['getbusiness'=>$getbusiness,'getallbusiness'=>$getallbusiness,'getimage'=>$getimage,'getreview'=>$getreview]);


  }
	
	  public function all_reviews($id){
		$getbusiness = DB::table('business_detail as b')
		->leftjoin('TPHotel as h','h.id','=','b.business_id')
		->leftjoin('Temp_Mapping as t','t.LocationId','=','h.location_id')
		->select('b.id as bid','b.name as bname','b.slug as bslug','h.id as hid','b.id as bus_id','t.slugid as slgid','b.business_id','h.name')
		->where('b.id', $id)->get(); 

		$hotelid = $getbusiness[0]->business_id; 
		$getreview = DB::table('HotelReview')
		->where('HotelId', $hotelid)
		->paginate(10);

		return view('business.all_reviews',['getbusiness'=>$getbusiness,'getreview'=>$getreview]);
  }

    public function update_hotel_data(request $request){
        $name = $request->get('bname');
        $about = $request->get('about');
        $totalromm = $request->get('totalromm');
        $hotelid = $request->get('hotelid');
        $bus_id = $request->get('bus_id');
        $data = array(
            'name'=> $name,
            'about'=> $about,
            'cntRooms'=> $totalromm,
          
        );
        $update = DB::table('TPHotel')
        ->where('id', $hotelid)
        ->update($data);

        $data2 = array(
            'name'=> $name,
        );
        DB::table('business_detail')
        ->where('id', $bus_id)
        ->update($data2);


        $getbusiness = DB::table('business_detail as b')
        ->leftjoin('TPHotel as h','h.id','=','b.business_id')
        ->select('b.id as bid','b.name as bname','h.id','h.slugid','h.slug','h.slug','h.photos','h.name','h.address','h.stars','h.cntRooms','h.about','h.amenities','h.id as hid','b.id as bus_id')
        ->where('b.id', $bus_id)->get(); 
        
  
       
        return view('business.general_info_result',['getbusiness'=>$getbusiness]);
    }

      
    
    public function update_hotel_amenity(request $request){
        $Languages = $request->get('Languages');
        $amenities = $request->get('amenities');
        $room_aminities = $request->get('room_aminities');
        $hotelid = $request->get('hotelid');
        $bus_id = $request->get('bus_id');
     
        $data = array(
            'Languages'=> $Languages,
            'amenities'=> $amenities,
            'room_aminities'=> $room_aminities          
        );
        $update = DB::table('TPHotel')
        ->where('id', $hotelid)
        ->update($data);

        // $data2 = array(
        //     'name'=> $name,
        // );
        // DB::table('business_detail')
        // ->where('id', $bus_id)
        // ->update($data2);


        $getbusiness = DB::table('business_detail as b')
        ->leftjoin('TPHotel as h','h.id','=','b.business_id')
        ->select('b.id as bid','b.name as bname','h.id','h.slugid','h.slug','h.slug','h.photos','h.name','h.address','h.stars','h.cntRooms','h.about','h.amenities','h.id as hid','b.id as bus_id','h.Languages','h.room_aminities','h.facilities')
        ->where('b.id', $bus_id)->get(); 
        
  
       
        return view('business.update_hotel_amenity',['getbusiness'=>$getbusiness]);
    }


    public function update_hotel_contact(request $request){
        $Website = $request->get('Website');
        $Email = $request->get('Email');
        $Phone = $request->get('Phone');
        $hotelid = $request->get('hotelid');
        $bus_id = $request->get('bus_id');
     
        $data = array(
            'Phone'=> $Phone,
            'Email'=> $Email,
            'Website'=> $Website          
        );

        $update = DB::table('TPHotel')
        ->where('id', $hotelid)
        ->update($data);
        
        $getbusiness = DB::table('business_detail as b')
        ->leftjoin('TPHotel as h','h.id','=','b.business_id')
        ->select('b.id as bid','b.name as bname','h.id as hid','b.id as bus_id','h.*')
        ->where('b.id', $bus_id)->get(); 
       
        return view('business.update_contact',['getbusiness'=>$getbusiness]);
    }

    public function edit_business_location($id){

        $getbusiness = DB::table('business_detail as b')
        ->leftjoin('TPHotel as h','h.id','=','b.business_id')
        ->select('b.id as bid','b.name as bname','h.id as hid','b.id as bus_id','h.*','b.slug as bslug')
        ->where('b.id', $id)->get(); 
      return  view('business.edit_business_location',['getbusiness'=>$getbusiness]);
    }

    public function update_map_data(request $request){
       $address = $request->get('address');
       $postcode = $request->get('postcode');
       $longitude = $request->get('longitude');
       $latitude = $request->get('latitude');
       $hotelid = $request->get('hotelid');
       $business_id = $request->get('bus_id');
       $bus_slug = $request->get('bus_slug');
       $data = array(
        'address'=> $address,
        'Pincode'=> $postcode,
        'longnitude'=> $longitude,
        'Latitude'=> $latitude          
    );
        $update = DB::table('TPHotel')
        ->where('id', $hotelid)
        ->update($data);

// return print_r($data);
        return redirect()->route('edit_business_location',[$business_id]);
    }



public function add_hotel_images( $id){
    $getbusiness = DB::table('business_detail as b')
        ->leftjoin('TPHotel as h','h.id','=','b.business_id')
        ->select('b.id as bid','b.name as bname','h.id as hid','b.id as bus_id','h.*','b.slug as bslug','b.business_id')
        ->where('b.id', $id)->get(); 
        $hotelid = $getbusiness[0]->business_id; 

        $getimage = DB::table('TPhotel_images')->where('hotelid', $hotelid)->get();  
   
      

 return view('business.add_hotel_images',['getbusiness'=>$getbusiness,'getimage'=>$getimage]);
}

  public function save_add_hotel_images(request $request){
    $image = $request->file('image');
    $id = $request->get('id');
    $bid = $request->get('bid');

//new code

if ($request->hasFile('image')) {

 

  $s3 = new S3Client([
      'region' => 'us-west-2',
      'credentials' => [
          'key' => 'AKIAYEDFDCST62PQXQO5',
          'secret' => 'gXOZqDE4Gt/+d3xwagbIvzUk+RE+W7zHfuya5Zox',
      ],
  ]);



  $firstUrl =  $request->file('image');

  $firstUrl = str_replace('/320/', '/375/', $firstUrl);
  $firstUrl = str_replace('/240/', '/229/', $firstUrl);
  $imageContent = file_get_contents($firstUrl);


 
  $randomNumber = uniqid();
  $filename = 'hotel_images' . $randomNumber . '.jpg';


  try {
     
      $s3->putObject([
          'Bucket' => 's3-travell',
          'Key' => 'hotel-images/' . $filename,
          'Body' => $imageContent,
          'ContentType' => 'image/jpeg',
          'ACL' => 'private',
      ]);

   

  } catch (\Exception $e) {
     
      return "Error uploading image: " . $e->getMessage();
  }



}




//end new code 

      $data = [ 
          'hotelid'=>$id,
          'Image' => $filename, 
      ];

      $getreview = DB::table('TPhotel_images')->insert($data);


    //   $getbusiness = DB::table('business_detail as b')
    //   ->leftjoin('TPHotel as h','h.id','=','b.business_id')
    //   ->select('b.id as bid','b.name as bname','h.id as hid','b.id as bus_id','h.*','b.slug as bslug')
    //   ->where('b.id', $bid)->get(); 


     $getimage = DB::table('TPhotel_images')->where('hotelid', $id)->get();  
      return view('business.add_hotel_image_result',['getimage'=>$getimage]);
}













  public function claim_listing($id, $type)
  {

    $user =  Auth::guard('business_user')->user();


  //  return  $type;
   // return print_r( $user );
    $userId = $user->id;

    // $email = $user->email;
    // $username = $user->username;
    
     $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();   

     if($type == "hotel"){      
        $getbusiness = DB::table('TPHotel')->select('name','address','photos','id')->where('hotelid', $id)->get();
     }elseif($type == "sight"){
        $getbusiness = DB::table('Sight as s')
        ->leftjoin('Sight_image as i','i.Sightid','=','s.SightId')
        ->select('s.SightId as id','s.Title as name','s.Address as address','i.Image as photos')
        ->where('s.SightId', $id)->get();  
      //  return print_r($getbusiness);
     }elseif($type == "restaurant"){      
        $getbusiness = DB::table('Restaurant')->select('RestaurantId as id','Title as name','Slug','Address as address')->where('RestaurantId', $id)->get();  
     }

   
     $language = DB::table('language_options')->get();  
    return view('business.claim_listing',['getbusiness'=>$getbusiness,'getallbusiness'=>$getallbusiness,'language'=>$language,'user'=>$user,'type'=>$type]);

  }

  public function claimlist_save(request $request){
    $name  = $request->get('name');
    $email  = $request->get('email');
    $id  = $request->get('userid');
    $business_phone  = $request->get('business_phone');
    $role  = $request->get('role');
    $lang  = $request->get('lang');
    $business_id  = $request->get('business_id');
    $type  = $request->get('type');
    $data = [
        'id' => $id,
        'name' => $name,
        'email' =>  $email ,
        'business_phone' => $business_phone,
        'role' => $role,
        'lang' => $lang,
        'business_id' => $business_id,
        'type' => $type,
    ];
    
    session(['bus_fomdata' => $data]);
    

 
    return response()->json([
    'redirect_url' => route('choose_uploadid', [$business_id, $type])
    ]);
  }


  public function choose_uploadid($business_id,$type){

    $data = session('bus_fomdata');
 
    $name = session('bus_fomdata.name');
 
    $user =  Auth::guard('business_user')->user();
	      $userId = $user->id;


    $getbus = DB::table('business_detail')->where('id',$business_id)->get();  

    if(!$getbus->isEmpty()){
        $business_id = $getbus[0]->business_id;
    }else{
	$getbus = DB::table('business_detail')->where('userid',$userId)->where('business_type',$type)->where('business_id',$business_id)->get(); 
	}
     
  //  return print_r($getbus);
//return $type;

    if($type == "hotel"){
       $getbusiness = DB::table('TPHotel')->select('id','name','address','photos')->where('id', $business_id)->get();  
    }elseif($type == "sight"){
    //    $getbusiness = DB::table('Sight')->select('Title as name','SightId as id')->where('SightId', $business_id)->get();  
       $getbusiness = DB::table('Sight as s')
       ->leftjoin('Sight_image as i','i.Sightid','=','s.SightId')
       ->select('s.SightId as id','s.Title as name','s.Address as address','i.Image as photos')
       ->where('s.SightId', $business_id)->get();  
    }elseif($type == "restaurant"){      
        $getbusiness = DB::table('Restaurant')->select('RestaurantId as id','Title as name','Slug','Address as address')->where('RestaurantId', $business_id)->get();  
    }

  


    $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  
   
    $coutnry = DB::table('Country')->get(); 
    

    
    return view('business.chooseuploadid',['getbusiness'=>$getbusiness,'user'=>$user,'type'=>$type,'getallbusiness'=>$getallbusiness,'coutnry'=>$coutnry,'getbus'=>$getbus]);

  }




public function image_vatification(request $request){
    $country =  $request->get('country');
    $idtype =  $request->get('idtype');
    $image =  $request->get('image');
    $hotelid =  $request->get('business_id');
    $id =  $request->get('id');
   // $hotelid = session('bus_fomdata.business_id');

      $data = session('bus_fomdata');
   // $hotelid = session('bus_fomdata.business_id');
    $phone = session('bus_fomdata.business_phone');
    $userid = session('bus_fomdata.id');
    $type= session('bus_fomdata.type');
    $role= session('bus_fomdata.role');
    $name ="";
    $slug ="";
    $address ="";
    if($type == 'hotel'){
        $category = 'Accommodation';
        $gethotel = DB::table('TPHotel')->where('id',$hotelid)->get();
		
	//return	print_r($gethotel);
        if(!$gethotel->isEmpty()){
            $name = $gethotel[0]->name;
            $slug = $gethotel[0]->slug;
            $address = $gethotel[0]->address;
        }
    }elseif($type == 'sight'){
        $category = 'Things to do';
      //  $gethotel = DB::table('Sight')->where('id',$hotelid)->get();
        $getdata = DB::table('Sight as s')
       ->select('s.SightId as id','s.Title as name','s.Address as address','Slug')
       ->where('s.SightId', $hotelid)->get();  
        if(!$getdata->isEmpty()){
            $name = $getdata[0]->name;
            $slug = $getdata[0]->Slug;
            $address = $getdata[0]->address;
        }
    }elseif($type == 'restaurant'){
        $category = 'Restaurant';
        $getdata = DB::table('Restaurant')->select('RestaurantId as id','Title as name','Slug','Address as address')->where('RestaurantId', $hotelid)->get();  
        if(!$getdata->isEmpty()){
            $name = $getdata[0]->name;
            $slug = $getdata[0]->Slug;
            $address = $getdata[0]->address;
        }
    }

 



    if ($request->hasFile('image')) {

    

        $s3 = new S3Client([
            'region' => 'us-west-2',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => 'gXOZqDE4Gt/+d3xwagbIvzUk+RE+W7zHfuya5Zox',
            ],
        ]);
    
    
    
        $firstUrl =  $request->file('image');
    
        $firstUrl = str_replace('/320/', '/375/', $firstUrl);
        $firstUrl = str_replace('/240/', '/229/', $firstUrl);
        $imageContent = file_get_contents($firstUrl);
    
    
    
        $randomNumber = uniqid();
        $filename = 'userid' . $randomNumber . '.jpg';
    
    
        try {
        
            $s3->putObject([
                'Bucket' => 's3-travell',
                'Key' => 'business-images/' . $filename,
                'Body' => $imageContent,
                'ContentType' => 'image/jpeg',
                'ACL' => 'private',
            ]);
    
        
    
        } catch (\Exception $e) {
        
            return "Error uploading image: " . $e->getMessage();
        }
    
    
    
    }
    

    $data1 = [ 
        'name'=>$name,
        'category'=>$category,
        'address' => $address, 
        'representative'=>1,
        'website'=>null,
        'phone'=>$phone,
        'email'=> null,
        'cat_value' => $type, 
        'userid'=>$userid,
        'slug' => $slug, 
        'business_type'=>$type,
        'business_id'=>$hotelid,
        'imgid_country'=>$country,
        'idtype'=>$idtype,
        'image_id'=>$filename,
        'varify_image'=>1,
    ];
  
   // return  $data1;
   $checkid = DB::table('business_detail')->select('id')->where('business_type',$type)->where('userid',$userid)->where('business_id',$hotelid)->get(); 
      
  if(!$checkid->isEmpty()){
       $id=  $checkid[0]->id;
       $getreview = DB::table('business_detail')->where('id',$id)->update($data1);     
  }else{
       $getreview = DB::table('business_detail')->insert($data1);
  }



        if($type == 'hotel'){
            $getbus = DB::table('business_detail')->where('business_type',$type)->where('userid',$userid)->where('business_id',$hotelid)->get(); 
        }else{
            $getbus = DB::table('business_detail')->where('business_type',$type)->where('userid',$userid)->where('business_id',$hotelid)->get();
        }
        
        return view('business.get_varify_img',['getbus'=>$getbus]);
    
//end image code

}


  

//  phone varification




public function send_business_sms(Request $request)
{
  
    $otp = rand(100000, 999999);
    session(['otp' => $otp]);
    session(['phone' => $request->phone]);
   $phone = $request->phone;
	//return env('VONAGE_FROM_NUMBER');
$response = $this->vonageClient->sms()->send(
    new \Vonage\SMS\Message\SMS($phone, 7018346874, "Your OTP is {$otp}")
);

if ($response && $response->current() && $response->current()->getStatus() === 'accepted') {
    // Message sent successfully (assuming 'accepted' indicates success)
    return response()->json([
        'message' => 'OTP sent successfully!',
    ]);
} else {
   return $response->current()->getStatus() ;
}

   return view('business.verification',['phone'=>$phone]);
 // return redirect()->route('view_otp',[$phone])->with('message', 'OTP sent!');
    
   return response()->json([
    'redirect_url' => route('view_otp', $phone),
    'message' => 'OTP sent!'
   ]);

}

public function view_otp(Request $request,$phone)
{
    $user =  Auth::guard('business_user')->user();

    $userId = $user->id;
    $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  
    $getbusiness = DB::table('business_detail')->where('userid', $user->id)->get();   
    return view('business.otp_page',['phone'=>$phone,'getallbusiness'=>$getallbusiness,'getbusiness'=>$getbusiness]);
  
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|numeric',
    ]);

  if ($request->otp == session('otp')) {
       return redirect()->route('phone.verification.form')->with('message', 'Phone number verified successfully!');
   }

    return redirect()->route('phone.verification.form')->with('error', 'Invalid OTP!');
}
//upload image
	
	
	
public function sightreviews($id){
   
    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Sight as s','s.SightId','=','b.business_id')
    ->leftjoin('Location as t','t.LocationId','=','s.LocationId')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','t.slugid as slgid','b.business_id','s.*','b.varify_business')
    ->where('b.id', $id)->get(); 


    
    if($getbusiness[0]->varify_business == 1){
        $sightid='';
        if(!$getbusiness->isEmpty()){
            $sightid = $getbusiness[0]->SightId; 
        
        }

        $getreview = DB::table('SightReviews')
        ->where('SightId', $sightid)
        ->paginate(10);
        return view('business.sight_reviews',['getbusiness'=>$getbusiness,'getreview'=>$getreview]);
    }else{
        $type = 'sight';
        return redirect()->route('choose_uploadid',[$id,$type]);
    }
   
}
	
//view restaurant code





public function view_rest_business($id, $slug)
{

  $getbusiness = DB::table('business_detail as b')
  ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
  ->leftjoin('Location as l','l.LocationId','=','r.LocationId')
  ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','l.slugid as slgid','b.business_id','b.varify_business')
  ->where('b.id', $id)->where('b.slug',$slug)->get(); 
  
  $hotelid = $getbusiness[0]->business_id; 

  $getimage = DB::table('RestaurantImages')->where('RestaurantId', $hotelid)->get();
  $getreview = DB::table('RestaurantReview')->where('RestaurantId', $hotelid)->limit(10)->get();

  $user =  Auth::guard('business_user')->user();

  $userId = $user->id;
  $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  

  $getcuisen = DB::table('RestaurantCuisine as r')
  ->join('RestaurantCuisineAssociation as rc','r.RestaurantCuisineId','=','rc.RestaurantCuisineId')
  ->where('rc.RestaurantId', $hotelid)->get();  
 
  $getSpecialDiet = DB::table('RestaurantSpecialDiet as r')
  ->join('RestaurantSpecialDietAssociation as rc','r.RestaurantSpecialDietId','=','rc.RestaurantSpecialDietId')
  ->where('rc.RestaurantId', $hotelid)->get();  

  $getfeature = DB::table('RestaurantFeature as r')
    ->join('RestaurantFeatureAssociation as rc','r.RestaurantFeatureId','=','rc.RestaurantFeatureId')
    ->where('rc.RestaurantId', $hotelid)->get(); 

  return view('business.view_restaurant_business',['getbusiness'=>$getbusiness,'getallbusiness'=>$getallbusiness,'getimage'=>$getimage,'getreview'=>$getreview,'getcuisen'=>$getcuisen,'getSpecialDiet'=>$getSpecialDiet,'getfeature'=>$getfeature]);


}



public function edit_ginfo_rest(request $request){
    $name = $request->get('bname');
    $about = $request->get('about');
    $Timings = $request->get('Timings');
    $PriceRange = $request->get('PriceRange');
    $MenuLink = $request->get('MenuLink');
    $bus_id = $request->get('bus_id');
    $business_id = $request->get('business_id');
   
    $data = array(
        'Title'=> $name,
        'about'=> $about,
        'Timings'=> $Timings,
        'PriceRange'=> $PriceRange,
        'MenuLink'=> $MenuLink,
    );


    $update = DB::table('Restaurant')
    ->where('RestaurantId', $business_id)
    ->update($data);

    $data2 = array(
        'name'=> $name,
       );
    DB::table('business_detail')
    ->where('id', $bus_id)
    ->update($data2);

    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->leftjoin('Location as l','l.LocationId','=','r.LocationId')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','l.slugid as slgid','b.business_id')
    ->where('b.id', $bus_id)->get(); 
   

   
    return view('business.general_rest_info_result',['getbusiness'=>$getbusiness]);
}




public function update_restaurant_contact(request $request){
    $Website = $request->get('Website');
    $Email = $request->get('Email');
    $Phone = $request->get('Phone');

    $bus_id = $request->get('bus_id');
    $business_id = $request->get('business_id');
    $data = array(
        'Phone'=> $Phone,
        'Email'=> $Email,
        'Website'=> $Website          
    );

    if($business_id !=""){
        $update = DB::table('Restaurant')
        ->where('RestaurantId', $business_id)
        ->update($data);
    }
    
    
    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','b.business_id')
    ->where('b.id', $bus_id)->get(); 
   
    return view('business.update_restaurant_contact',['getbusiness'=>$getbusiness]);
}

public function edit_brestaurant_location($bus_id){

    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','b.business_id')
    ->where('b.id', $bus_id)->get(); 
  return  view('business.edit_rest_location_data',['getbusiness'=>$getbusiness]);
}

public function update_rest_map_data(request $request){

    $address = $request->get('address');

   $postcode = $request->get('postcode');
   $longitude = $request->get('longitude');
   $latitude = $request->get('latitude');
    $business_id = $request->get('business_id');
   $bus_id = $request->get('bus_id');
   $bus_slug = $request->get('bus_slug');

   $data = array(
    'Address'=> $address,
    'Pincode'=> $postcode,
    'Longitude'=> $longitude,
    'Latitude'=> $latitude          
    );
    if($business_id !=""){
        $update = DB::table('Restaurant')
        ->where('RestaurantId', $business_id)
        ->update($data);
    }
   


    return back();

  //  return redirect()->route('view_rest_business',[$bus_id ,$bus_slug]);

}


public function edit_rest_cuisine($bus_id){
    
    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','b.business_id','b.varify_business')
    ->where('b.id', $bus_id)->get(); 
    $rid ="";
    $rid = $getbusiness[0]->business_id; 

    if($getbusiness[0]->varify_business  != 0){
       


            $allrest = DB::table('RestaurantCuisine')->get(); 

            $getcuisen = collect();
            $getfeature = collect();
            if(!$getbusiness->isEmpty()){
                $rid = $getbusiness[0]->business_id; 
                $getcuisen = DB::table('RestaurantCuisine as r')
            ->join('RestaurantCuisineAssociation as rc','r.RestaurantCuisineId','=','rc.RestaurantCuisineId')
            ->where('rc.RestaurantId', $rid)->get(); 

            
            $getSpecialDiet = DB::table('RestaurantSpecialDiet as r')
            ->join('RestaurantSpecialDietAssociation as rc','r.RestaurantSpecialDietId','=','rc.RestaurantSpecialDietId')
            ->where('rc.RestaurantId', $rid)->get(); 



            $getfeature = DB::table('RestaurantFeature as r')
            ->join('RestaurantFeatureAssociation as rc','r.RestaurantFeatureId','=','rc.RestaurantFeatureId')
            ->where('rc.RestaurantId', $rid)->get(); 

            } 
            $allspecialdiet = DB::table('RestaurantSpecialDiet')->get(); 
            $getallFeatures = DB::table('RestaurantFeature')->get(); 

        return  view('business.edit_rest_cuisine',['getbusiness'=>$getbusiness ,'getcuisen'=>$getcuisen,'allrest'=>$allrest,'allspecialdiet'=>$allspecialdiet,'getSpecialDiet'=>$getSpecialDiet,'getallFeatures'=>$getallFeatures,'getfeature'=>$getfeature]);




    }else{     
        $type = 'restaurant';
        return redirect()->route('choose_uploadid',[$rid,$type]);
    }

}
	
public function update_rest_cuisine(Request $request) {
    
     $cui_names = $request->input('cui_name', []); 
     $special_diet = $request->input('special_diet', []); 
     $features = $request->input('features', []); 
     
     $business_id = $request->input('business_id');
 
    
 
     try {
      
         $existingCuisines = DB::table('RestaurantCuisineAssociation')
             ->where('RestaurantId', $business_id)
             ->pluck('RestaurantCuisineId')
             ->toArray();
     
         $cuisinesToAdd = array_diff($cui_names, $existingCuisines);
         $cuisinesToRemove = array_diff($existingCuisines, $cui_names);
 
    
         foreach ($cuisinesToAdd as $cui_name) {
             DB::table('RestaurantCuisineAssociation')->insert([
                 'RestaurantId' => $business_id,
                 'RestaurantCuisineId' => $cui_name,
             ]);
         }
 
     
         if (!empty($cuisinesToRemove)) {
             DB::table('RestaurantCuisineAssociation')
                 ->where('RestaurantId', $business_id)
                 ->whereIn('RestaurantCuisineId', $cuisinesToRemove)
                 ->delete();
         }
 
         //special diet

         $existingspecialdt = DB::table('RestaurantSpecialDietAssociation')
         ->where('RestaurantId', $business_id)
         ->pluck('RestaurantSpecialDietId')
         ->toArray();
 
        $spdtToAdd = array_diff($special_diet, $existingspecialdt);
        $spToRemove = array_diff($existingspecialdt, $special_diet);


        foreach ($spdtToAdd as $sp_name) {
            DB::table('RestaurantSpecialDietAssociation')->insert([
                'RestaurantId' => $business_id,
                'RestaurantSpecialDietId' => $sp_name,
            ]);
        }

    
        if (!empty($spToRemove)) {
            DB::table('RestaurantSpecialDietAssociation')
                ->where('RestaurantId', $business_id)
                ->whereIn('RestaurantSpecialDietId', $spToRemove)
                ->delete();
        }


         //end special diet

          //features

          $existingfeature = DB::table('RestaurantFeatureAssociation')
          ->where('RestaurantId', $business_id)
          ->pluck('RestaurantFeatureId')
          ->toArray();
  
         $featToAdd = array_diff($features, $existingfeature);
         $featToRemove = array_diff($existingfeature, $features);
 
 
         foreach ($featToAdd as $featToAdds) {
             DB::table('RestaurantFeatureAssociation')->insert([
                 'RestaurantId' => $business_id,
                 'RestaurantFeatureId' => $featToAdds,
             ]);
         }
 
     
         if (!empty($featToRemove)) {
             DB::table('RestaurantFeatureAssociation')
                 ->where('RestaurantId', $business_id)
                 ->whereIn('RestaurantFeatureId', $featToRemove)
                 ->delete();
         }
 
 
          //end features
       
         return back()->with('success', 'Cuisines updated successfully.');
     } catch (\Exception $e) {
         // Rollback transaction on error
         DB::rollBack();
 
         // Log the exception for debugging (optional)
         \Log::error('Failed to update cuisines: '.$e->getMessage());
 
         // Redirect back with error message
         return back()->with('error', 'Failed to update cuisines.');
     }
}





	
	
public function rest_all_reviews($id){
  

    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','b.business_id','b.varify_business')
    ->where('b.id', $id)->get(); 
    
    $hotelid = $getbusiness[0]->business_id; 
    $getreview = DB::table('RestaurantReview')
    ->where('RestaurantId', $hotelid)
    ->paginate(10);

    if($getbusiness[0]->varify_business  != 0){
        return view('business.all_rest_review',['getbusiness'=>$getbusiness,'getreview'=>$getreview]);
    }else{     
        $type = 'restaurant';
        return redirect()->route('choose_uploadid',[$hotelid,$type]);
    }

 
  }



  //image code
  public function add_rest_photos( $id){
    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','b.business_id','b.varify_business')
    ->where('b.id', $id)->get(); 

        $hotelid = $getbusiness[0]->business_id; 

        $getimage = DB::table('RestaurantImages')->where('RestaurantId', $hotelid)->get();  

      
        if($getbusiness[0]->varify_business  != 0){
            return view('business.add_rest_image',['getbusiness'=>$getbusiness,'getimage'=>$getimage]);
        }else{     
            $type = 'restaurant';
            return redirect()->route('choose_uploadid',[$hotelid,$type]);
        }
}


 

  public function bus_add_rest_images(request $request){
      $image = $request->file('image');
      $id = $request->get('id');
     

//new code

if ($request->hasFile('image')) {
  
   

    $s3 = new S3Client([
        'region' => 'us-west-2',
        'credentials' => [
            'key' => 'AKIAYEDFDCST62PQXQO5',
            'secret' => 'gXOZqDE4Gt/+d3xwagbIvzUk+RE+W7zHfuya5Zox',
        ],
    ]);



    $firstUrl =  $request->file('image');

    $firstUrl = str_replace('/320/', '/375/', $firstUrl);
    $firstUrl = str_replace('/240/', '/229/', $firstUrl);
    $imageContent = file_get_contents($firstUrl);
  

   
    $randomNumber = uniqid();
    $filename = 'busi_rest' . $randomNumber . '.jpg';
  

    try {
       
        $s3->putObject([
            'Bucket' => 's3-travell',
            'Key' => 'business-images/' . $filename,
            'Body' => $imageContent,
            'ContentType' => 'image/jpeg',
            'ACL' => 'private',
        ]);

     

    } catch (\Exception $e) {
       
        return "Error uploading image: " . $e->getMessage();
    }



}




//end new code 

        $data = [ 
            'RestaurantId'=>$id,
            'Image' => $filename, 
        ];

        $getreview = DB::table('RestaurantImages')->insert($data);



        $getimage = DB::table('RestaurantImages')->where('RestaurantId', $id)->get();  
        return view('business.upload_rest_result',['getimage'=>$getimage]);
        


  }


  //end image code 

public function delete_rest_image(request $request){


    $id = $request->get('id');
    $restid = $request->get('restid');

    // Find the image record using a DB query
    $image = DB::table('RestaurantImages')->where('Id', $id)->first();

    if (!$image) {
        return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
    }

    try {
        // Initialize the S3 client
        $s3 = new S3Client([
            'region' => 'us-west-2',
            'version' => 'latest',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => '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',
            ],
        ]);

        // Delete the image file from S3
        $filePath = 'business-images/' . $image->Image;
      
        $result = $s3->deleteObject([
            'Bucket' =>'s3-travell',
            'Key' => $filePath,
        ]);

        // Check if the deletion was successful
      //  if ($result) {
            // Delete the image record from the database
            DB::table('RestaurantImages')->where('Id', $id)->delete();

            // Get updated images for the restaurant
            $getimage = DB::table('RestaurantImages')->where('RestaurantId', $restid)->get();

            // Return the updated view
            return view('business.upload_rest_result', ['getimage' => $getimage]);
        // } else {
        //     return response()->json(['success' => false, 'message' => 'Failed to delete image from S3.'], 500);
        // }
    } catch (\Exception $e) {
        $getimage = DB::table('RestaurantImages')->where('RestaurantId', $restid)->get();

        // Return the updated view
        return view('business.upload_rest_result', ['getimage' => $getimage]);
    }

   
}


public function update_menu_link(request $request){
   
     $MenuLink = $request->get('menulink');
    $bus_id = $request->get('bus_id');
    $business_id = $request->get('business_id');
   
    $data = array(     
        'MenuLink'=> $MenuLink,
    );


    $update = DB::table('Restaurant')
    ->where('RestaurantId', $business_id)
    ->update($data);


    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->leftjoin('Location as l','l.LocationId','=','r.LocationId')
    ->select('b.id as bid','b.id as bus_id','r.MenuLink','b.business_id')
    ->where('b.id', $bus_id)->get(); 
   

   
    return view('business.updated_menulink',['getbusiness'=>$getbusiness]);
}


public function add_rest_menu( $id){

    $getbusiness = DB::table('business_detail as b')
    ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
    ->select('b.id as bid','b.name as bname','b.slug as bslug','b.id as bus_id','r.*','b.business_id','b.varify_business')
    ->where('b.id', $id)->get(); 

        $hotelid = $getbusiness[0]->business_id; 
    if($getbusiness[0]->varify_business  != 0){
        return view('business.add_rest_menu',['getbusiness'=>$getbusiness]);
    }else{     
        $type = 'restaurant';
         return redirect()->route('choose_uploadid',[$hotelid,$type]);
    }
}


public function update_menu(request $request){
   
    $menuItems = $request->get('menuitems'); // ["ssbb", "fffaabb", "", "", "", "", ""]
    $bus_id = $request->get('bid');
    $business_id = $request->get('id');
    
    // Filter out empty or null values
    $menuItems = array_filter($menuItems, function($item) {
        return !empty($item);
    });
    
    // Process the menu items into the desired format
    $formattedMenuItems = array_map(function($item, $index) {
        return ['menuitem' => $item];
    }, $menuItems, array_keys($menuItems));
    
    // Convert the formatted menu items array to JSON
    $MenuLink = json_encode($formattedMenuItems);
    
    $data = [
        'Menu' => $MenuLink,
    ];
   // return print_r($data);

     $update = DB::table('Restaurant')
         ->where('RestaurantId', $business_id)
         ->update($data);

  //end data

   $getbusiness = DB::table('business_detail as b')
   ->leftjoin('Restaurant as r','r.RestaurantId','=','b.business_id')
   ->leftjoin('Location as l','l.LocationId','=','r.LocationId')
   ->select('b.id as bid','b.id as bus_id','r.MenuLink','b.business_id','r.Menu')
   ->where('b.id', $bus_id)->get(); 
  

  
   return view('business.rest_menu_result',['getbusiness'=>$getbusiness]);
}


//end view restaurant code 







public function delete_sight_image(request $request){


    $id = $request->get('id');
    $sightid = $request->get('sightid');

    // Find the image record using a DB query
    $image = DB::table('Sight_image')->where('id', $id)->first();

    if (!$image) {
        return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
    }

    try {
        // Initialize the S3 client
        $s3 = new S3Client([
            'region' => 'us-west-2',
            'version' => 'latest',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => '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',
            ],
        ]);

        // Delete the image file from S3
        $filePath = 'business-images/' . $image->Image;
      
        $result = $s3->deleteObject([
            'Bucket' =>'s3-travell',
            'Key' => $filePath,
        ]);

        // Check if the deletion was successful
      //  if ($result) {
            // Delete the image record from the database
            DB::table('Sight_image')->where('id', $id)->delete();

            // Get updated images for the restaurant
           $getimage = DB::table('Sight_image')->where('Sightid', $sightid)->get();
         
            // Return the updated view
            return view('business.upload_image_result', ['getimage' => $getimage]);
        // } else {
        //     return response()->json(['success' => false, 'message' => 'Failed to delete image from S3.'], 500);
        // }
    } catch (\Exception $e) {
        // $getimage = DB::table('RestaurantImages')->where('RestaurantId', $restid)->get();

        // // Return the updated view
        // return view('business.upload_rest_result', ['getimage' => $getimage]);
        $getimage = DB::table('business_image')->where('business_id', $sightid)->get();  
        return view('business.upload_image_result',['getimage'=>$getimage]);
    }

   
}



//sight info edit 

public function edit_sight_info($id){

    $getbusines = DB::table('business_detail as b')
    ->leftjoin('Sight as s','s.SightId','=','b.business_id')
    ->leftjoin('Location as l','l.LocationId','=','s.LocationId')
    ->leftjoin('Category as c','c.CategoryId','=','s.CategoryId')
    ->select('b.*','b.id as bid','b.id as bus_id','b.name as bname','s.*','c.Title as cname','b.slug as bslug')
    ->where('b.id', $id)->get(); 

  
    $user =  Auth::guard('business_user')->user();

    $userId = $user->id;
    $getallbusiness = DB::table('business_detail')->where('userid', $userId)->get();  
    $category = DB::table('Category')->get();  
 
    return view('business.edit_sight_info',['getbusiness'=>$getbusines,'getallbusiness'=>$getallbusiness,'category'=>$category]);   
  
}





    public function save_sight_info(request $request){
        $name = $request->get('bname');
        $about = $request->get('about');
        $phone = $request->get('phone');
        $categoryid = $request->get('categoryid');
        $mustsee = $request->get('mustsee');
        $bus_id = $request->get('bus_id');
        $business_id = $request->get('business_id');
    
        $data = array(
            'Title'=> $name,
            'About'=> $about,
            'Phone'=> $phone,
            'CategoryId'=> $categoryid,
            'IsMustSee'=> $mustsee,
        );


        $update = DB::table('Sight')
        ->where('SightId', $business_id)
        ->update($data);

        $data2 = array(
            'name'=> $name,
        );
        DB::table('business_detail')
        ->where('id', $bus_id)
        ->update($data2);

        $getbusines = DB::table('business_detail as b')
        ->leftjoin('Sight as s','s.SightId','=','b.business_id')
        ->leftjoin('Location as l','l.LocationId','=','s.LocationId')
        ->leftjoin('Category as c','c.CategoryId','=','s.CategoryId')
        ->select('b.*','b.id as bid','b.id as bus_id','b.name as bname','s.*','c.Title as cname')
        ->where('b.id', $bus_id)->get(); 

    
        return view('business.edit_sight_info_result',['getbusiness'=>$getbusines]);
    }

    
  // sight
    public function edit_sight_location($bus_id){

        $getbusiness = DB::table('business_detail as b')
        ->leftjoin('Sight as s','s.SightId','=','b.business_id')
        ->select('b.*','b.id as bid','b.id as bus_id','b.slug as bslug','b.name as bname','s.*')
        ->where('b.id', $bus_id)->get(); 
        $hotelid = $getbusiness[0]->business_id; 
        if($getbusiness[0]->varify_business  != 0){
            return  view('business.edit_sight_location_data',['getbusiness'=>$getbusiness]);
        }else{     
            $type = 'sight';
            return redirect()->route('choose_uploadid',[$hotelid,$type]);
        }
  
    }

    public function update_sight_map_data(request $request){

        $address = $request->get('address');

        $postcode = $request->get('postcode');
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $business_id = $request->get('business_id');
        $bus_id = $request->get('bus_id');
        $bus_slug = $request->get('bus_slug');

        $data = array(
            'Address'=> $address,
            'Pincode'=> $postcode,
            'Longitude'=> $longitude,
            'Latitude'=> $latitude          
            );
        
        // return $data ;
            if($business_id !=""){
                $update = DB::table('Sight')
                ->where('SightId', $business_id)
                ->update($data);
            }
        


            return back();


    }
 

    //delete hotel image

    
public function delete_hotel_image(request $request){


     $id = $request->get('id');
    $restid = $request->get('hotelid');

    // Find the image record using a DB query
    $image = DB::table('TPhotel_images')->where('id', $id)->first();

    if (!$image) {
        return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
    }

    try {
        // Initialize the S3 client
        $s3 = new S3Client([
            'region' => 'us-west-2',
            'version' => 'latest',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => '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',
            ],
        ]);

        // Delete the image file from S3
        $filePath = 'hotel-images/' . $image->image;
      
        $result = $s3->deleteObject([
            'Bucket' =>'s3-travell',
            'Key' => $filePath,
        ]);

        // Check if the deletion was successful
      //  if ($result) {
            // Delete the image record from the database
            DB::table('TPhotel_images')->where('id', $id)->delete();

            // Get updated images for the restaurant
            $getimage = DB::table('TPhotel_images')->where('hotelid', $restid)->get();

            // Return the updated view
            return view('business.add_hotel_image_result', ['getimage' => $getimage]);
        // } else {
        //     return response()->json(['success' => false, 'message' => 'Failed to delete image from S3.'], 500);
        // }
    } catch (\Exception $e) {
        $getimage = DB::table('TPhotel_images')->where('hotelid', $restid)->get();

        // Return the updated view
        return view('business.add_hotel_image_result', ['getimage' => $getimage]);
    }

   
}

//end view restaurant code 	

}
