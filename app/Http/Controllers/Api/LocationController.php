<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $locations = DB::table('Location')
            ->select('LocationId as id', 'Name as name')
            ->where('Name', 'like', '%' . $query . '%')
            ->orderBy('Name')
            ->paginate(10);
            
        return response()->json([
            'data' => $locations->items(),
            'total' => $locations->total()
        ]);
    }
}
