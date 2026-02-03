<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
 

public function register()
{
    Breadcrumbs::for('home', function ($trail) {
        $trail->push('Home', route('home'));
    });

    // Add more breadcrumbs here
}

}
