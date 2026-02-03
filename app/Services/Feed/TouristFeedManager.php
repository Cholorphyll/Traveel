<?php

namespace App\Services\Feed;

use Illuminate\Support\Facades\DB;

class TouristFeedManager
{
    public function getFeed($locationId, $userLat = null, $userLon = null)
    {
        // 1. Get City Center Coordinates
        $city = DB::table('Location')->where('LocationId', $locationId)->first();
        
        // 2. Determine Context
        $isInCity = false;
        if ($userLat && $userLon && $city) {
            $dist = $this->haversine($userLat, $userLon, $city->Lat, $city->Longitude);
            // If user is within 30km of city center, they are "In City"
            $isInCity = $dist < 30; 
        }

        // 3. Dispatch to specific Engine
        if ($isInCity) {
            $engine = new InCityEngine($locationId, $city, $userLat, $userLon);
        } else {
            $engine = new OutCityEngine($locationId, $city);
        }

        return $engine->generate();
    }

    private function haversine($lat1, $lon1, $lat2, $lon2) {
        if(empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) return 9999;
        $earthRadius = 6371; 
        $dLat = deg2rad((float)$lat2 - (float)$lat1);
        $dLon = deg2rad((float)$lon2 - (float)$lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * sin($dLon/2) * sin($dLon/2);
        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1-$a)));
    }
}
