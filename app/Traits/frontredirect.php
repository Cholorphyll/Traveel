<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CustomBusinessRedirect
{
    protected function redirectTo()
    {
        if (!Auth::guard('FrontendUserLogin')->check()) {
            return route('custom_business_login'); // Change 'custom_business_login' to your desired login route
        }

        return route('user_dashboard');
    }
}
