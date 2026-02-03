<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BusinessAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated as a business user
        if (Auth::guard('business_user')->check()) {
            return $next($request); // Allow the request to proceed
        }

        // If not authenticated, redirect to the businessindex page
        return redirect()->route('businessindex');
    }
}
