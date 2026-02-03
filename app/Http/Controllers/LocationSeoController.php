<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocationSeoSettings;
use Illuminate\Support\Facades\DB;

class LocationSeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->check() || auth()->user()->isActive == 2) {
            return redirect()->route('login');
        }

        $locations = DB::table('Location')
            ->leftJoin('location_seo_settings', 'Location.LocationId', '=', 'location_seo_settings.location_id')
            ->select('Location.LocationId', 'Location.Name', 'location_seo_settings.allow_index')
            ->paginate(50);

        return view('admin.location_seo.index', compact('locations'));
    }

    public function update(Request $request)
    {
        try {
            $locationId = $request->input('location_id');
            $showInIndex = $request->input('show_in_index');
    
            DB::table('Location')
                ->where('LocationId', $locationId)
                ->update(['show_in_index' => $showInIndex]);
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}