<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function checkPermission($roles)
    {
        if (!in_array(auth()->user()->role, (array)$roles)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
