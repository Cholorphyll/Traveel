<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CustomBusinessRedirect
{
    protected function redirectTo()
    {
        if (!Auth::guard('business_user')->check()) {
            return route('custom_business_login'); // Change 'custom_business_login' to your desired login route
        }

        return route('business_dashboard');
    }
}
