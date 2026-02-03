<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the user settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get user data from session like in welcome.blade.php
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
        
        // Pass user data to view
        return view('profile.settings', compact('user'));
    }

    /**
     * Update user account information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAccount(Request $request)
    {
        // Try to get user ID from different possible session keys
        $userId = $request->session()->get('UserId') ?? 
                 ($request->session()->get('frontend_user')['UserId'] ?? null);
                 
        // Get current user data
        $user = DB::table('FrontendUserLogin')
            ->where('UserId', $userId)
            ->first();
        // Only validate and update the fields that are present in the request
        $rules = [
            'Username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('FrontendUserLogin', 'Username')->ignore($userId, 'UserId')
            ]
        ];
        
        try {
            $validated = $request->validate($rules);
            $updateData = [
                'Username' => $validated['Username'],
                'UpdatedOn' => now()
            ];
            
            // Only update FirstName if it's in the request
            if ($request->has('FirstName')) {
                $updateData['FirstName'] = $request->FirstName;
            }
            
            // Only update LastName if it's in the request
            if ($request->has('LastName')) {
                $updateData['LastName'] = $request->LastName;
            }
            
            // Only update Email if it's in the request
            if ($request->has('Email')) {
                $rules['Email'] = [
                    'required',
                    'email',
                    'max:50',
                    Rule::unique('FrontendUserLogin', 'Email')->ignore($userId, 'UserId')
                ];
                $updateData['Email'] = $request->Email;
            }
            
            DB::table('FrontendUserLogin')
                ->where('UserId', $userId)
                ->update($updateData);
                
            return back()->with('success', 'Account updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating account: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update user preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePreferences(Request $request)
    {
        $userId = $request->session()->get('UserId');
        
        // Validate based on which form was submitted
        if ($request->has('date_format')) {
            // User settings form validation
            $request->validate([
                'date_format' => 'required|in:mm/dd/yyyy,dd/mm/yyyy',
                'distance_format' => 'required|in:miles,kilometers',
                'time_format' => 'required|in:12hour,24hour',
                'place_descriptions' => 'sometimes',
                'travel_tips' => 'sometimes',
            ]);
            
            // Get existing preferences or create new ones
            $preferences = session('user_preferences', []);
            
            // Update with new values
            $preferences['date_format'] = $request->date_format;
            $preferences['distance_format'] = $request->distance_format;
            $preferences['time_format'] = $request->time_format;
            $preferences['place_descriptions'] = $request->has('place_descriptions');
            $preferences['travel_tips'] = $request->has('travel_tips');
            
            // Save to session
            session(['user_preferences' => $preferences]);
            
            // Save to database if needed
            DB::table('UserPreferences')->updateOrInsert(
                ['UserId' => $userId],
                [
                    'DateFormat' => $request->date_format,
                    'DistanceFormat' => $request->distance_format,
                    'TimeFormat' => $request->time_format,
                    'PlaceDescriptions' => $request->has('place_descriptions') ? 1 : 0,
                    'TravelTips' => $request->has('travel_tips') ? 1 : 0,
                    'UpdatedAt' => now()
                ]
            );
        } else if ($request->has('language')) {
            // Preferences tab validation
            $request->validate([
                'language' => 'required|in:en,es,fr,de',
                'currency' => 'required|in:USD,EUR,GBP,INR',
                'theme' => 'required|in:light,dark,system',
            ]);
            
            // Get existing preferences or create new ones
            $preferences = session('user_preferences', []);
            
            // Update with new values
            $preferences['language'] = $request->language;
            $preferences['currency'] = $request->currency;
            $preferences['theme'] = $request->theme;
            
            // Save to session
            session(['user_preferences' => $preferences]);
            
            // Save to database if needed
            DB::table('UserPreferences')->updateOrInsert(
                ['UserId' => $userId],
                [
                    'Language' => $request->language,
                    'Currency' => $request->currency,
                    'Theme' => $request->theme,
                    'UpdatedAt' => now()
                ]
            );
        }

        return redirect()->back()->with('success', 'Preferences updated successfully.');
    }

    /**
     * Update notification settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateNotifications(Request $request)
    {
        // Update notification preferences in session or database
        session([
            'notification_settings' => [
                'email_notifications' => $request->has('email_notifications'),
                'trip_updates' => $request->has('trip_updates'),
                'new_messages' => $request->has('new_messages'),
                'new_activities' => $request->has('new_activities'),
                'push_notifications' => $request->has('push_notifications'),
            ]
        ]);

        return redirect()->back()->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Update user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

}
