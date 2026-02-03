<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\ProfileImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $profileImageService;

    public function __construct(ProfileImageService $profileImageService)
    {
        $this->profileImageService = $profileImageService;
    }

    /**
     * Display the user profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->session()->has('frontend_user')) {
            $userData = $request->session()->get('frontend_user');
            $userId = $userData['UserId'];
            
            // Get complete user data from database
            $user = DB::table('FrontendUserLogin')
                    ->where('UserId', $userId)
                    ->first();
        } else {
            // Create empty user object if not in session
            $user = (object)[];
        }
        
        return view('profile.profile', compact('user'));
    }
    
    /**
     * Display the edit profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $userId = $request->session()->get('UserId') ?? ($request->session()->get('frontend_user.UserId') ?? null);
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get user data
        $user = DB::table('FrontendUserLogin')
            ->where('UserId', $userId)
            ->first();
            
        
        
        // Get user's current location if exists
        $currentLocation = null;
        if (!empty($user->LocationId)) {
            $currentLocation = DB::table('Location')
                ->select('LocationId', 'Name')
                ->where('LocationId', $user->LocationId)
                ->first();
        }
        
        // Ensure profile picture URL is properly formatted
        if (!empty($user->st_profilelink) && !filter_var($user->st_profilelink, FILTER_VALIDATE_URL)) {
            $user->st_profilelink = str_replace('public/', '', $user->st_profilelink);
        }
        
        return view('profile.edit_profile', [
            'user' => $user,
            'currentLocation' => $currentLocation
        ]);
    }
    
    /**
     * Update the user profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            // Verify user session - check both possible session keys
            $userId = $request->session()->get('UserId') ?? ($request->session()->get('frontend_user.UserId') ?? null);
            
            if (!$userId) {
                throw new \Exception('User not authenticated');
            }

            // Validate request data
            $validator = Validator::make($request->all(), [
                'FirstName' => 'required|string|max:255',
                'Bio' => 'nullable|string|max:500',
                'LocationId' => 'nullable',
                'website' => 'nullable|url|max:255',
                'Instagram' => 'nullable|string|max:100',
                'Twitter' => 'nullable|string|max:100',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Log the update attempt
            \Log::info('Profile update attempt', [
                'user_id' => $userId,
                'data' => $request->except(['_token'])
            ]);

            // Get location ID if location name is provided
            $locationId = null;
            if ($request->filled('LocationId')) {
                if (is_numeric($request->LocationId)) {
                    $locationId = (int)$request->LocationId;
                } else {
                    // If it's a string, try to find the location by name
                    $location = DB::table('Location')
                        ->where('Name', $request->LocationId)
                        ->first();
                    $locationId = $location ? $location->LocationId : null;
                }
            }

            // Prepare update data
            $updateData = [
                'FirstName' => $request->FirstName,
                'Bio' => $request->Bio,
                'LocationId' => $locationId,
                'Website' => $request->website,
                'Instagram' => $request->Instagram,
                'Twitter' => $request->Twitter,
                'UpdatedOn' => now()
            ];

            // Remove null values to prevent overwriting with null
            $updateData = array_filter($updateData, function($value) {
                return $value !== null;
            });

            // Execute update
            $result = DB::table('FrontendUserLogin')
                ->where('UserId', $userId)
                ->update($updateData);

            if ($result === false) {
                throw new \Exception('Database update failed');
            }

            // Log successful update
            \Log::info('Profile update successful', [
                'user_id' => $userId,
                'changes' => $updateData
            ]);
            
            return redirect()->back()->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            // Log the error with more context
            \Log::error('Profile update error', [
                'user_id' => $userId ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', 'password'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ], [
            'profile_picture.required' => 'Please select an image to upload.',
            'profile_picture.image' => 'The file must be an image.',
            'profile_picture.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, or webp.',
            'profile_picture.max' => 'The image may not be greater than 5MB in size.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                
                // Delete old profile picture if exists
                $userId = $request->session()->has('UserId') 
                    ? $request->session()->get('UserId')
                    : ($request->session()->has('frontend_user.UserId') 
                        ? $request->session()->get('frontend_user.UserId')
                        : null);
                
                $user = DB::table('FrontendUserLogin')->where('UserId', $userId)->first();
                if ($user && !empty($user->st_profilelink)) {
                    $this->profileImageService->deleteProfileImage($user->st_profilelink);
                }
                
                // Upload new profile picture to S3
                $path = $this->profileImageService->uploadProfileImage(
                    $request->file('profile_picture'),
                    $userId
                );

                if (!$path) {
                    throw new \Exception('Failed to upload image');
                }

                // Update user's profile picture path in database
                DB::table('FrontendUserLogin')
                    ->where('UserId', $userId)
                    ->update([
                        'st_profilelink' => $path,
                        'UpdatedOn' => now()
                    ]);

                // Update session data
                if ($request->session()->has('frontend_user')) {
                    $userData = $request->session()->get('frontend_user');
                    $userData['st_profilelink'] = $path;
                    $request->session()->put('frontend_user', $userData);
                }
                
                return redirect()->back()->with('success', 'Profile picture updated successfully!');
            }
            
            return redirect()->back()->with('error', 'No image file was uploaded.');
            
        } catch (\Exception $e) {
            \Log::error('Profile picture upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while uploading the image. Please try again.');
        }
    }
    
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Remove the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removePicture(Request $request)
    {
        // Get user ID from session
        $userId = $request->session()->has('UserId') 
            ? $request->session()->get('UserId')
            : ($request->session()->has('frontend_user.UserId') 
                ? $request->session()->get('frontend_user.UserId')
                : null);
        
        try {
            // Get user data
            $user = DB::table('FrontendUserLogin')->where('UserId', $userId)->first();
            
            if ($user && !empty($user->st_profilelink)) {
                // Delete the profile picture file
                $oldImagePath = 'public/' . ltrim($user->st_profilelink, '/');
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
                
                // Update database
                DB::table('FrontendUserLogin')
                    ->where('UserId', $userId)
                    ->update([
                        'st_profilelink' => null,
                        'UpdatedOn' => now()
                    ]);
                
                // Update session data
                if ($request->session()->has('frontend_user')) {
                    $userData = $request->session()->get('frontend_user');
                    $userData['st_profilelink'] = null;
                    $request->session()->put('frontend_user', $userData);
                }
                
                return redirect()->back()->with('success', 'Profile picture removed successfully.');
            }
            
            return redirect()->back()->with('error', 'No profile picture found to remove.');
            
        } catch (\Exception $e) {
            \Log::error('Remove profile picture error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while removing the profile picture. Please try again.');
        }
    }
    
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
            $userId = $request->session()->get('UserId');
            
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_new_password' => 'required|same:new_password',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            // Get the user's current password
            $user = DB::table('FrontendUserLogin')
                ->where('UserId', $userId)
                ->first();
            
            // Check if the current password is correct
            if (!Hash::check($request->password, $user->Password)) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }
            
            // Update the password
            $updated = DB::table('FrontendUserLogin')
                ->where('UserId', $userId)
                ->update([
                    'Password' => Hash::make($request->new_password),
                    'UpdatedOn' => now()
                ]);
                
            if ($updated) {
                return redirect()->back()->with('success', 'Password updated successfully.');
            }
            
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
    }

     /**
     * Display the user's trip details
     */
    public function tripDetail(Request $request)
    {
        $defaultUser = (object)[
            'ProfilePicture' => null,
            'FirstName' => '',
            'LastName' => '',
            'CreatedAt' => now(),
            'st_profilelink' => null
        ];
    
        if ($request->session()->has('frontend_user')) {
            $userData = $request->session()->get('frontend_user');
            $userId = $userData['UserId'];
            
            // Get complete user data from database
            $user = DB::table('FrontendUserLogin')
                    ->where('UserId', $userId)
                    ->first();
            
            // Get user's reviews
            $reviews = DB::table('SightReviews')
                ->leftJoin('sight_review_image', 'SightReviews.Id', '=', 'sight_review_image.SightReviewId')
                ->select(
                    'SightReviews.*',
                    DB::raw('GROUP_CONCAT(DISTINCT sight_review_image.Image) as review_images')
                )
                ->where('Email', $user->Email)
                ->groupBy('SightReviews.Id')
                ->orderBy('SightReviews.CreatedDate', 'desc')
                ->paginate(10);
            
            // Get user's trips (if you have a trips table)
            $trips = []; // Add your trip fetching logic here if needed
    
            // Merge with default values
            $user = (object)array_merge((array)$defaultUser, (array)$user);
            
            // Get full S3 URL for profile picture
            if (!empty($user->st_profilelink)) {
                $user->ProfilePicture = $this->profileImageService->getProfileImageUrl($user->st_profilelink);
            } else {
                $user->ProfilePicture = $this->profileImageService->getProfileImageUrl(null);
            }
        } else {
            // Use default user object if not in session
            $user = $defaultUser;
            $user->ProfilePicture = $this->profileImageService->getProfileImageUrl(null);
            $reviews = collect([]); // Empty collection if not logged in
            $trips = []; // Empty array if not logged in
        }
        
        $activeTab = $request->query('tab', 'about');
        
        return view('profile.trip_detail', [
            'user' => $user,
            'reviews' => $reviews,
            'trips' => $trips,
            'activeTab' => $activeTab
        ]);
    }
}
