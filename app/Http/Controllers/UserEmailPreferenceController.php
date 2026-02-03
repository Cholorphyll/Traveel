<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserEmailPreference;
use Illuminate\Support\Facades\Auth;

class UserEmailPreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $preferences = UserEmailPreference::getOrCreateForUser($user->id);
        
        return view('user.email-preferences', compact('preferences'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'location_recommendations' => 'boolean',
            'hotel_deals' => 'boolean',
            'explore_suggestions' => 'boolean',
            'weekly_digest' => 'boolean',
            'email_frequency' => 'in:daily,weekly,monthly',
        ]);

        $user = Auth::user();
        $preferences = UserEmailPreference::getOrCreateForUser($user->id);

        $preferences->update([
            'location_recommendations' => $request->has('location_recommendations'),
            'hotel_deals' => $request->has('hotel_deals'),
            'explore_suggestions' => $request->has('explore_suggestions'),
            'weekly_digest' => $request->has('weekly_digest'),
            'email_frequency' => $request->email_frequency ?? 'weekly',
        ]);

        return redirect()->route('user.email-preferences')
            ->with('success', 'Email preferences updated successfully!');
    }

    public function unsubscribe(Request $request)
    {
        if (!Auth::check()) {
            $email = $request->query('email');
            $token = $request->query('token');
            
            if ($email && $token) {
                $user = \App\Models\User::where('email', $email)->first();
                
                if ($user && hash_equals(hash('sha256', $user->email . $user->id), $token)) {
                    $preferences = UserEmailPreference::getOrCreateForUser($user->id);
                    $preferences->update([
                        'location_recommendations' => false,
                        'hotel_deals' => false,
                        'explore_suggestions' => false,
                        'weekly_digest' => false,
                    ]);
                    
                    return view('user.unsubscribe-success');
                }
            }
            
            return view('user.unsubscribe-error');
        }

        $user = Auth::user();
        $preferences = UserEmailPreference::getOrCreateForUser($user->id);
        
        $preferences->update([
            'location_recommendations' => false,
            'hotel_deals' => false,
            'explore_suggestions' => false,
            'weekly_digest' => false,
        ]);

        return redirect()->route('user.email-preferences')
            ->with('success', 'You have been unsubscribed from all email notifications.');
    }
}
