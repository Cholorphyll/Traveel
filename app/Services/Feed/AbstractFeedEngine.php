<?php

namespace App\Services\Feed;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

abstract class AbstractFeedEngine
{
    protected $locationId;
    protected $cityData;
    protected $maxFeedItems = 60;
    protected $scarcityThreshold = 15;

    public function __construct($locationId, $cityData)
    {
        $this->locationId = $locationId;
        $this->cityData = $cityData;
    }

    abstract public function generate();

    // --- SHARED DATA FETCHING ---
    protected function fetchRawData()
    {
        $sights = DB::table('Sight')
            ->select('SightId as id', 'Title as title', 'Latitude', 'Longitude', 'ReviewCount', 'Averagerating', 'tier', DB::raw("'sight' as entity_type"), 'Img1 as image')
            ->where('LocationId', $this->locationId)->where('Status', 1)->get();

        $experiences = DB::table('Experience')
            ->select('ExperienceId as id', 'Name as title', 'Latitude', 'Longitude', 'ReviewCount', 'Averagerating', 'tier', DB::raw("'experience' as entity_type"), 'Img1 as image', 'popularity_score')
            ->where('LocationId', $this->locationId)->where('IsActive', 1)->get();

        $restaurants = DB::table('Restaurant')
            ->select('RestaurantId as id', 'Title as title', 'Latitude', 'Longitude', 'ReviewCount', 'Averagerating', 'tier', DB::raw("'restaurant' as entity_type"))
            ->where('LocationId', $this->locationId)->where('IsActive', 1)->get();

        return [$sights, $experiences, $restaurants];
    }

    // --- SHARED UTILS ---
    protected function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        if(empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) return 9999;
        $earthRadius = 6371; 
        $dLat = deg2rad((float)$lat2 - (float)$lat1);
        $dLon = deg2rad((float)$lon2 - (float)$lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * sin($dLon/2) * sin($dLon/2);
        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1-$a)));
    }

    protected function processClusters($experiences) {
        // Placeholder for clustering logic
        return [collect([]), $experiences];
    }

    protected function formatAlsoAt($items) {
        return collect($items)->map(function($sub) {
            return [
                'id' => $sub->id,
                'title' => $sub->title,
                'tier' => $sub->tier ?? 4,
                'entity_type' => $sub->entity_type,
                'image' => $sub->image ?? null
            ];
        })->toArray();
    }
}
