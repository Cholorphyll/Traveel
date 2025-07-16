<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testFilterCounts(Request $request)
    {
        $controller = new HotelFilterCountsController();
        $response = $controller->getFilterCounts($request);
        return $response;
    }
}
