<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'tr_user_lat',
        'tr_user_lng',
        'tr_user_geo_ts',
        'tr_geo_dismissed',
    ];
}
