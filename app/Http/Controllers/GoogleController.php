<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        // Capture optional redirect URL so we can return the user there after login
        $redirect = $request->query('redirect_url');
        if ($redirect) {
            // Store both a custom key and Laravel's intended URL key
            session([
                'post_login_redirect' => $redirect,
                'url.intended' => $redirect,
            ]);
        }
        return Socialite::driver('google')->redirect();
    }

    // Step 2: Handle callback from Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if(!$user){
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt('defaultpassword') // not really used
                ]);
            }

            // Login the user
            Auth::login($user);
            // Also set legacy session data so existing views keep working
            try {
                session([
                    'frontend_user' => [
                        'Username' => $user->name ?? ($user->email ?? 'User'),
                        'user_image' => $user->avatar ?? ($user->photo ?? null),
                        'Email' => $user->email ?? null,
                        'UserId' => $user->id ?? null,
                    ]
                ]);
            } catch (\Throwable $e) { /* ignore */ }

            // Prefer explicit redirect captured before OAuth
            $intended = session('post_login_redirect');
            if ($intended) {
                session()->forget('post_login_redirect');
                return redirect()->to($intended);
            }
            // Otherwise, use Laravel's intended or fallback
            return redirect()->intended(route('trip.index'));
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }
}
