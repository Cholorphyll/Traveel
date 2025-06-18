<?php

namespace App\Http\Controllers\Frontend;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Crypt;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Session;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\Mime\Part\TextPart;
class SigninController extends Controller
{
   
    public function signup(){
        return view('front_login.signup');
    }
 public function register_user(Request $request)
    {   
        $exists = DB::table('FrontendUserLogin')->where('email', $request->email)->exists();
        if ($exists) {
            return response()->json(['emailExists' => $exists]);
        } 
        // Generate a verification token
        $verification_token = sha1(time() . $request->email . uniqid());
    
       
        // Hash the password
        $hashedPassword = Hash::make($request->password);
 

        // Create user
       $userId = DB::table('FrontendUserLogin')->insertGetId([
		   'FirstName' => $request->first_name,
            'LastName' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $hashedPassword,
            'CreatedOn' => now(),
            'UpdatedOn' => now(),
            'IsBlocked'=>0,
            'IsActive'=>1,
            'IsUpdate'=>0,
            'Role'=>1,
            'IsEmailVerify'=>0,
            'PhoneNumber'=>0,
			'EmailVerificationCode'=>$verification_token,
        ]);
    
        // Send verification email
        $subject = 'Verify Your Email';
        $body = 'Please click on the following link to verify your email: ' . route('varification', ['token' => $verification_token]);
        $to = $request->email;
    
        try {
            // Attempt to send the email
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });
                return 1;           
        } catch (\Exception $e) {
           \Log::error('Mail sending failed: ' . $e->getMessage());
            return 0;
         
        }
    }
  
	 public function user_login(){
		$type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
        if (session()->has('frontend_user')) {
            return redirect()->route('user_dashboard'); 
        }else{
            return view('front_login.user_login')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
        }
  
    }
	public function varification(Request $request)
	{
		$token = $request->query('token');
		$user = DB::table('FrontendUserLogin')->where('EmailVerificationCode', $token)->first();

		if ($user) {
			DB::table('FrontendUserLogin')->where('EmailVerificationCode', $token)
				->update(['IsEmailVerify' => 1]);
                
            return redirect('/?openModal=signInModal')->with('success', 'Email verification successful. You can now login.');

		
		} else {
            return redirect('/?openModal=signInModal')->with('error', 'Invalid verification token. Please try again.');
		}
	}  
	

	public function checkemail(Request $request)
	{
		$email = $request->input('email');
		$user = DB::table('FrontendUserLogin')
			->where('Email', $email)
			->first();
		if ($user) {       
			session('user_Email',$user->Email);
			return $user->Email;
		} else {
			return 0;
		}
	}





public function userlongin(Request $request)
{
    $email = $request->input('email');
    $password = $request->input('password');


   
    $user = DB::table('FrontendUserLogin')
        ->where('Email', $email)
        ->first();


    if ($user && !empty($password) && Hash::check($password, $user->Password) &&  $user->IsEmailVerify ==1) {
      

        $userData = [
            'UserId' => $user->UserId,
            'Username' => $user->FirstName,
            'email' => $user->Email,
			'user_image' => $user->st_profilelink
        ];

        session(['frontend_user' => $userData]);	
		   // Set a persistent cookie
            $lifetime = 525600; // 1 year in minutes
            $cookieParams = session_get_cookie_params();
            $cookieParams['lifetime'] = $lifetime * 60; // Lifetime in seconds
            session_set_cookie_params($cookieParams);
         //  Log::info('Session parameters:', $cookieParams);
            
            
            Cookie::queue('laravel_session', session()->getId(), $lifetime);
		
		//end cookie 
        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirectUrl' => route('user_dashboard') // The URL to redirect to after login
        ]);
    } else {
        return 0;
        return redirect()->back()->withErrors(['email' => 'Invalid email or password']);
    }
}


public function front_dashboard()
{
            $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
	
    if (session()->has('frontend_user')) {
        $userData = session('frontend_user');
      //  $userId = $user->id;
           // Accessing individual values
             $userId = $userData['UserId']; 

    // $username = $userData['Username'];
    // $email = $userData['email'];
  
        $getdata = DB::table('FrontendUserLogin as u')
        ->leftjoin('Location as l','l.LocationId','=','u.LocationId')
        ->leftjoin('Country as c','c.CountryId','=','l.CountryId')
        ->select('u.*','l.Name as lName','c.Name as cName')
        ->where('UserId',  $userId )
        ->get();   
        


        $getpost = DB::table('Userpostlist as u')
       // ->leftjoin('UserPostimages as p','p.UserId','=','u.UserId')
        // ->leftjoin('UserPostCategory as cat','cat.UserId','=','u.UserId')
        //  ->leftjoin('postcategory as pc','pc.postcategoryid','=','cat.PostCategoryId')
       // ->select('u.*','pc.name as catname','p.postimge')
       ->select('u.*')
        ->where('u.UserId',  $userId )
        ->get();   

        $getpostimages = DB::table('UserPostimages as upi')      
        ->select('upi.*')
         ->where('upi.UserId',  $userId )
         ->get();   

         $UserPostCategory = DB::table('UserPostCategory as cat')
          ->leftjoin('postcategory as pc','pc.postcategoryid','=','cat.PostCategoryId')        
         ->select('cat.*')
          ->where('cat.UserId',  $userId )
          ->get();   


        //return print_r($getpost );
        return view('front_login.user_dashboard', [
    'getdata' => $getdata,
    'getpost' => $getpost,
    'getpostimages' => $getpostimages,
])->with([
    'type' => $type,
    'lname' => $lname,
    'checkin' => $checkin,
    'checkinDate' => $checkinDate,
    'checkoutDate' => $checkoutDate,
    'guest' => $guest,
    'rooms' => $rooms,
    'slugid' => $slugid,
    'slug' => $slug,
    'Username' => $Username,
    'sightImages' => $sightImages,
]);
     
    } else {
        return redirect()->route('user_login'); // Redirect to login route
    }
}

public function save_user(Request $request)
{
    // Validate the form data
    $validatedData = $request->validate([          
      'email' => 'required|email|unique:business_user|max:255',
  ]);

  // Check if email already exists
  $existingUser = DB::table('business_user')->where('email', $request->get('email'))->first();

  if ($existingUser) {
      // Email already exists, return 0 or handle accordingly
      return 0;
  }
    
   //return $request->get('fname');
    $data = array( 
      'username' => $request->get('fname').' '.$request->get('lname'),
  
      'email' =>  $request->get('email'),
      'password' =>  Hash::make($request->get('password')),
    );
   
    // Create a new user instance and save it to the database
    DB::table('business_user')->insert($data);
  

    return redirect()->back()->with('success', 'Form submitted successfully!');
}

//   public function front_dashboard(){
 
//     $user =  Auth::guard('frontend_user')->user();

//     return $userId = $user->id;
  
//     // if ($user) {
    
//     //     $getbusiness = DB::table('business_detail')->where('userid', $user->id)->get();   
        
//     //     return view('business.dashboard', ['getbusiness' => $getbusiness]);
//     // }

   
   
//   }
public function userlogout()
{
    // Destroy the 'frontend_user' session
    Session::forget('frontend_user');
    
    // Redirect to the homepage route
    return redirect()->route('homepage');
}



public function search_loc_city(Request $request)
{
 

    $search = $request->get('val');

    $result = array();

    $query = DB::table('Location')
        ->join('Country', 'Location.CountryId', '=', 'Country.CountryId')
        ->select('Location.LocationId','Location.Name as lname','Country.Name as countryName')
        ->where('Location.Name', 'LIKE', $search . '%')
        ->limit(4)
        ->get();

        if($query->isEmpty()){
              $query = DB::table('Location as l')
                ->join('Country as c', 'l.CountryId', '=', 'c.CountryId')
                ->select('l.LocationId','l.Name as lname','c.Name as countryName')
                ->where(DB::raw("LOWER(CONCAT(l.Name, ' ', c.Name))"), 'LIKE', strtolower($search) . '%')
                ->limit(4)
                ->get();
        }
  


    foreach ($query as $loc) {
        $result[] = [
            'id' => $loc->LocationId,
            'value' => $loc->lname,
            'country' => $loc->countryName // Assuming 'CountryName' is the column name for the country name in the 'Country' table
        ];
    }

    return response()->json($result);
}




public function search_loc_citys(Request $request)
{
 

    $search = $request->get('val');

    $result = array();

    $query = DB::table('Location')
        ->join('Country', 'Location.CountryId', '=', 'Country.CountryId')
        ->select('Location.LocationId','Location.Name as lname','Country.Name as countryName')
        ->where('Location.Name', 'LIKE', $search . '%')
        ->limit(4)
        ->get();

        if($query->isEmpty()){
              $query = DB::table('Location as l')
                ->join('Country as c', 'l.CountryId', '=', 'c.CountryId')
                ->select('l.LocationId','l.Name as lname','c.Name as countryName')
                ->where(DB::raw("LOWER(CONCAT(l.Name, ' ', c.Name))"), 'LIKE', strtolower($search) . '%')
                ->limit(4)
                ->get();
        }
  


    foreach ($query as $loc) {
        $result[] = [
            'id' => $loc->LocationId,
            'value' => $loc->lname,
            'country' => $loc->countryName // Assuming 'CountryName' is the column name for the country name in the 'Country' table
        ];
    }

    return response()->json($result);
}

public function updateprofile(Request $request)
{
	
	$userid = $request->get('userid');
    $Username = $request->get('Username');
   $getval = DB::table('FrontendUserLogin')->select('Username')->where('UserId', $userid)->where('Username',$Username)->get();

   if($getval->isEmpty() && $Username != ""){

        $request->validate([
            'Username' => 'unique:FrontendUserLogin,Username' ,
        ]);
    }
 $dataToUpdate = [];
    if (!empty($Username)) {
	//	return $Username;
        $dataToUpdate['Username'] =  $Username;
    }
	
	
    $fname = $request->get('fname');
    $lname = $request->get('lname');
    $bio = $request->get('bio');
    $ctname = $request->get('location');
    $userid = $request->get('userid');

   

    if (!empty($fname)) {
        $dataToUpdate['FirstName'] = $fname;
    }

    if (!empty($lname)) {
        $dataToUpdate['LastName'] = $lname;
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
        $filename = 'user' . $userid . '_' . $randomNumber . '.jpg';
        $dataToUpdate['st_profilelink'] = $filename;

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
        DB::table('FrontendUserLogin')->where('UserId', $userid)->update($dataToUpdate);
    }

    // Retrieve updated data
    $getdata = DB::table('FrontendUserLogin as u')
        ->leftJoin('Location as l', 'l.LocationId', '=', 'u.LocationId')
        ->leftJoin('Country as c', 'c.CountryId', '=', 'l.CountryId')
        ->select('u.*', 'l.Name as lName', 'c.Name as cName')
        ->where('UserId', $userid)
        ->get();

    // Check if $getdata is not empty before returning the view
    if (!$getdata->isEmpty()) {
		
	
            $userData = [
                'UserId' => $getdata[0]->UserId,
                'Username' => $getdata[0]->Username,
                'email' => $getdata[0]->Email,
                'user_image' => $getdata[0]->st_profilelink
            ];
    
            session(['frontend_user' => $userData]);
  
       
        $profileDataView = view('front_login.profile_data', ['getdata' => $getdata])->render();
        $imageResultView = view('front_login.image_result', ['getdata' => $getdata])->render();
  		$usernavimg = view('front_login.nav_image_result')->render();

        return response()->json([
            'profile_data' => $profileDataView,
            'image_result' => $imageResultView,
			'usernavimg' => $usernavimg
        ]);
    }

 
}

    public function addpost(Request $request)
    {
        $description = $request->get('description');
        $preferences = implode(',', $request->input('preferences')); 


        $postcategoryIds = DB::table('postcategory')
            ->select('postcategoryid')
            ->whereIn('name', explode(',', $preferences))
            ->get()
            ->pluck('postcategoryid');

   
        $ctname = $request->get('location');
        $userid = $request->get('userid');

          // If user ID is not in request, try to get it from session
    if (empty($userid) && session()->has('frontend_user')) {
        $userData = session('frontend_user');
        $userid = $userData['UserId'];
    }
    
    // Convert empty string to 'unknown' for logging purposes
    $logUserId = !empty($userid) ? $userid : 'unknown';

        $dataToUpdate = [];

        
        if ($ctname !="") {
            $lastCommaPos = strrpos($ctname, ',');
            if ($lastCommaPos !== false) {
                $countryName = substr($ctname, $lastCommaPos + 1);
                $ctname1 = substr($ctname, 0, $lastCommaPos);

                $getCont = DB::table('Country')->where('Name', $countryName)->first();
                if ($getCont) {
                    $ctid = $getCont->CountryId;
                    $getLoc = DB::table('Location')->where('Name', $ctname1)->where('CountryId', $ctid)->first();
                    if ($getLoc) {
                        $LocationId = $getLoc->LocationId;
                    }else{

                        return 0;
                    }
                }else{

                    return 0;
                }
            }else{

                return 0;
            }
        }else{

            return 0;
        }


        $dataToUpdate = array(

            'UserId'=>  $userid,
            'LocationId'=> $LocationId,
            'LocationName'=>$ctname,
            'Description'=>  $description,
            
        );
        
    
        $lastid =  DB::table('Userpostlist')->insertGetId($dataToUpdate);

        foreach ($postcategoryIds as $postcategoryId) {
         
            DB::table('UserPostCategory')->insert([
                'Userid' => $userid,
                'PostId' => $lastid,
                'PostCategoryId' => $postcategoryId,
                'IsActive' => 1, 
                'CreatedOn' => now(),
                'UpdateOn' => now(), 
                'DeleteOn' => null,
            ]);
        }
        
    //new code
   

    if ($request->hasFile('images')) {

        $s3 = new S3Client([
            'region' => 'us-west-2',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => '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',
            ],
            'http' => [
                'verify' => false // Disable SSL verification for local development
            ],
            'version' => 'latest',
        ]);
        $images = $request->file('images');
    
        foreach ($images as $image) {
         
            $randomNumber = uniqid();
            $filename = 'post' . $lastid . '_' . $randomNumber . '.jpg';
    
            // Upload the image to S3
            try {
                $s3->putObject([
                    'Bucket' => 's3-travell',
                    'Key' => 'post-images/' . $filename,
                    'Body' => file_get_contents($image),
                    'ContentType' => 'image/jpeg',
                    'ACL' => 'private',
                ]);
            } catch (\Exception $e) {
                return "Error uploading image: " . $e->getMessage();
            }
    
          
             DB::table('UserPostimages')->insert(['postimge' => $filename ,'postid'=>$lastid,'UserId'=>$userid]);
        }
    }

    $getpost = DB::table('Userpostlist as u')  
    ->select('u.*')
     ->where('u.UserId',  $userid )
     ->get();   


    // Check if $getdata is not empty before returning the view
    if (!$getpost->isEmpty()) {
        $getpostimages = DB::table('UserPostimages as upi')      
        ->select('upi.*')
         ->where('upi.UserId',  $userid )
         ->get();   
   
         $UserPostCategory = DB::table('UserPostCategory as cat')
          ->leftjoin('postcategory as pc','pc.postcategoryid','=','cat.PostCategoryId')        
         ->select('cat.*')
          ->where('cat.UserId',  $userid )
          ->get();   
     
     return  $profileDataView = view('front_login.post_result', ['getpost'=>$getpost,'getpostimages'=>$getpostimages])->render();
             
    }
   
}


public function changeprofileimg(Request $request)
{
    // Try to get user ID from request first
    $userid = $request->get('userid');
    
    // If user ID is not in request, try to get it from session
    if (empty($userid) && session()->has('frontend_user')) {
        $userData = session('frontend_user');
        $userid = $userData['UserId'];
    }
    
    // Convert empty string to 'unknown' for logging purposes
    $logUserId = !empty($userid) ? $userid : 'unknown';

    $dataToUpdate = [];

//new code
if ($request->hasFile('pimage')) {
    
    try {
        $s3 = new S3Client([
            'region' => 'us-west-2',
            'credentials' => [
                'key' => 'AKIAYEDFDCST62PQXQO5',
                'secret' => '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',
            ],
            'http' => [
                'verify' => false // Disable SSL verification for local development
            ],
            'version' => 'latest',
        ]);
        
        
        $firstUrl = $request->file('pimage');

        $firstUrl = str_replace('/320/', '/375/', $firstUrl);
        $firstUrl = str_replace('/240/', '/229/', $firstUrl);
        
        $imageContent = file_get_contents($firstUrl);
        
        $randomNumber = uniqid();
        $filename = 'user' . $userid . '_' . $randomNumber . '.jpg';
        $dataToUpdate['st_profilelink'] = $filename;
        
        
        $result = $s3->putObject([
            'Bucket' => 's3-travell',
            'Key' => 'user-images/' . $filename,
            'Body' => $imageContent,
            'ContentType' => 'image/jpeg',
            'ACL' => 'private',
        ]);
        
        
        // Log the URL of the uploaded image
        $imageUrl = $s3->getObjectUrl('s3-travell', 'user-images/' . $filename);

    } catch (\Exception $e) {
        return "Error uploading image: " . $e->getMessage();
    }
}

    // Perform the update only if there is data to update
    if (!empty($dataToUpdate)) {
        DB::table('FrontendUserLogin')->where('UserId', $userid)->update($dataToUpdate);
    }

    // Retrieve updated data
    $getdata = DB::table('FrontendUserLogin as u')
        ->select('u.*')
        ->where('u.UserId', $userid)
        ->get();

    // Check if $getdata is not empty before returning the view
    if (!$getdata->isEmpty()) {
		
		 $userData = [
            'UserId' => $getdata[0]->UserId,
            'Username' => $getdata[0]->Username,
            'email' => $getdata[0]->Email,
            'user_image' => $getdata[0]->st_profilelink
        ];

        session(['frontend_user' => $userData]);
 
        $imageResultView = view('front_login.image_result', ['getdata' => $getdata])->render();
 		 $usernavimg = view('front_login.nav_image_result')->render();

        return response()->json([
           
            'image_result' => $imageResultView,
			 'usernavimg' => $usernavimg
        ]);
    }
}

public function forgot_password(){
    return view('front_login.forgot_password');
}


public function sendlink_forgorpassword(Request $request)
{
    $email = $request->input('email');
    $subject = 'Reset Password';

    $getdata = DB::table('FrontendUserLogin')
        ->where('Email', $email)
        ->get();

    if (!$getdata->isEmpty()) {
		 $firstname = $getdata[0]->FirstName;
        $emailEncrypted = Crypt::encryptString($email);
        $url = url('/?openModal=resetPswdModal&token=' . urlencode($emailEncrypted));
		$displayUrl = strlen($url) > 30 ? substr($url, 0, 30) . '...' : $url;
        // Define the HTML content of the email
        $body = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <title>Email Forgot Password</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
          </head>
          <style type="text/css">
            body { width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; }
          </style>
          <body>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#DCDADA" align="center" style="margin:0;padding:0">
              <tbody>
                <tr>
                  <td align="center">
                    <table width="652" cellspacing="0" cellpadding="0" border="0" align="center">
                      <tbody>
                        <tr>
                          <td height="60" align="center"></td>
                        </tr>
                        <tr>     
                          <td align="center">
                            <a href="#">
                               <img src="https://www.travell.co/public/frontend/hotel-detail/images/travell-logo.png" width="248">
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td height="60" align="center"></td>
                        </tr>
                      </tbody>
                    </table>
        
                    <table width="652" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" style="border-radius:40px;">
                      <tbody>
                        <tr>
                          <td height="40" align="center"></td>
                        </tr>
                        <tr>
                          <td style="color:#000000" align="center">
                            <p style="font-family:Helvetica,Arial, sans-serif;font-size:26px;font-weight:500;line-height:31px;margin:0;padding:0" align="center">Hi $firstname,<br>let’s reset your password </p>
                          </td>
                        </tr>
                        <tr>
                          <td height="36" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <p style="color: #6A6A6A;font-size: 18px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;letter-spacing: 0.2px;line-height: 22px;margin: 0;" align="center">Seems like you forgot your password for Travell. If this is<br> true, click below to reset your password</p>
                          </td>
                        </tr>
                        <tr>
                          <td height="36" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <a style="color: #fff;display: block;font-size: 16px;font-weight: bold;font-family:Helvetica,Arial, sans-serif;letter-spacing: 0.6px;line-height: 51px;border-radius: 12px;background: #FF4B01;text-decoration: none;width: 384px;-webkit-text-size-adjust:none;mso-hide:all;" href="$url" target="_blank">Reset Your Password</a>
                          </td>
                        </tr>
                        <tr>
                          <td height="36" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <p style="color: #6A6A6A;font-size: 18px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;letter-spacing: 0.2px;line-height: 22px;margin: 0" align="center">If the above button does not work you, copy and paste the<br> following into your browser’s address bar:</p>
                          </td>
                        </tr>
                        <tr>
                          <td height="18" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <a href="$url" style="color: #2C4DDE;font-size: 14px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;line-height: 18px;margin: 0 0 15px;" align="center" target="_blank">$displayUrl</a>
                          </td>
                        </tr>
                        <tr>
                          <td height="30" align="center"></td>
                        </tr>
                      </tbody>
                    </table>
        
                    <table width="652" cellspacing="0" cellpadding="0" border="0" align="center">
                      <tbody>
                        <tr>
                          <td align="center">
                            <table width="652" cellspacing="0" cellpadding="0" border="0" align="center">
                              <tbody>
                                <tr>
                                  <td height="20" align="center"></td>
                                </tr>
                                <tr>
                                  <td align="center">
                                    <p style="color: #555555;font-size: 16px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;letter-spacing: 0.2px;line-height: 28px;margin: 0;" align="center">If you did not forget your password, you can safely<br> ignore this email.</p>
                                  </td>
                                </tr>
                                <tr>
                                  <td height="50" align="center"></td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </body>
        </html>
        HTML;

        $to = $email;

        try {
            // Attempt to send the email
            Mail::html($body, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
    
            return $to;
          
        } catch (\Exception $e) {
            return 2;
        }
    } else {    
        return 0;
    }
}


public function reset_password($token) {
    try {
        $email = Crypt::decryptString($token);
     //   return   $email;
        return view('front_login.reset_password', ['email' => $email]);
    } catch (DecryptException $e) {
        // Handle decryption error
        abort(404);
    }
}


public function save_reset_password(request  $request){
 
    $password =  $request->get('password'); 
    $email = Crypt::decryptString($request->get('email'));
    $hashedPassword = Hash::make($password);

   // return $email;
   $update = DB::table('FrontendUserLogin')
    ->where('Email', $email)
    ->update(['Password' => $hashedPassword]);
    if($update == 1){
        return 1;
      //  return redirect('/?openModal=signInModal')->with('success', 'Password reset successful. You can now login.');
        
      //  return redirect('/user_login')->with('success','Password Updated Successfully.');
    }
    


}

public function search_city(Request $request)
{
  
    $search = $request->get('val');

    $result = array();

    $query = DB::table('Location')
        ->join('Country', 'Location.CountryId', '=', 'Country.CountryId')
        ->select('Location.LocationId','Location.Name as lname','Country.Name as countryName','Country.CountryId')
        ->where('Location.Name', 'LIKE',  $search . '%')
        ->orWhere(DB::raw("LOWER(CONCAT(Location.Name, ' ', Country.Name))"), 'LIKE',  strtolower($search) . '%')
        ->limit(4)
        ->get();
    
    foreach ($query as $loc) {
        $result[] = [
            'id' => $loc->LocationId,
            'value' => $loc->lname,
            'country' => $loc->countryName 
        ];
    }

    return response()->json($result);
}

	
public function sendContactQuery(Request $request)
{
    // Validate form fields
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'location' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    // Extract form data
    $name = $request->input('name');
    $email = $request->input('email');
    $phone = $request->input('phone');
    $location = $request->input('location');
    $message = $request->input('message');

    // Prepare email data
    $subject = 'New Query from Contact Us Form';
    $to = 'Travell.co@outlook.in';

    $body = "You have received a new query from the contact form:\n\n"
        . "Name: $name\nEmail: $email\nPhone: $phone\nLocation: $location\nMessage: $message";

    try {
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });

        \Log::info('Email successfully sent to: ' . $to);

        // Return a JSON response for the frontend to handle
        return response()->json(['success' => true, 'message' => 'Your message has been sent successfully.']);
    } catch (\Exception $e) {
        \Log::error('Mail sending failed: ' . $e->getMessage());

        // Return a JSON response for the frontend to handle errors
        return response()->json(['success' => false, 'message' => 'There was an error sending your message. Please try again later.']);
    }
}

	
}
