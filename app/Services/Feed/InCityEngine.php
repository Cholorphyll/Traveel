<?php

namespace App\Services\Feed;

use Carbon\Carbon;

class InCityEngine extends AbstractFeedEngine
{
    protected $userLat;
    protected $userLon;
    protected $currentHour;
    protected $weather; // 'clear', 'rain', 'hot'

    public function __construct($locationId, $cityData, $lat, $lon)
    {
        parent::__construct($locationId, $cityData);
        $this->userLat = $lat;
        $this->userLon = $lon;
        $this->currentHour = Carbon::now()->hour;
        // In prod, inject WeatherService here to set $this->weather
    }

    public function generate()
    {
        list($sights, $experiences, $restaurants) = $this->fetchRawData();
        list($clusterNodes, $standaloneExp) = $this->processClusters($experiences);

        // 1. IN-CITY POOLING (Context Aware)
        // ----------------------------------
        
        // Anchors & Standard similar to Out-City, but we add Restaurants differently
        $pool = collect([])
            ->merge($sights->whereIn('tier', [1,2]))
            ->merge($standaloneExp->whereIn('tier', [1,2]))
            ->merge($clusterNodes);

        // RESTAURANT LOGIC (Dynamic Injection)
        // Only include if near meal times (11-2, 7-9) OR if it's very popular
        $isMealTime = ($this->currentHour >= 11 && $this->currentHour <= 14) || ($this->currentHour >= 19 && $this->currentHour <= 21);
        
        if ($isMealTime) {
            // Add top restaurants to the main routing pool
            $pool = $pool->merge($restaurants->whereIn('tier', [1,2,3]));
        }

        // TIER 3 LOGIC (Proximity Injection)
        // Include Tier 3s ONLY if they are within 2km of the user
        $nearbyT3 = $sights->where('tier', 3)->filter(fn($i) => 
            $this->getDistance($this->userLat, $this->userLon, $i->Latitude, $i->Longitude) < 2.0
        );
        $pool = $pool->merge($nearbyT3);

        // 2. ASSIGN CARD SIZES (Dynamic)
        $pool = $pool->map(function($item) {
            // Logic: Things closer to user get bigger prominence in In-City
            $dist = $this->getDistance($this->userLat, $this->userLon, $item->Latitude, $item->Longitude);
            
            if ($item->tier == 1) $item->display_type = 'card_large';
            elseif ($dist < 1.0) $item->display_type = 'card_medium'; // Upgrade close items
            else $item->display_type = 'card_small';
            
            return $item;
        });

        // 3. GROUPING (300m Rule still applies for cleanup)
        // We skip strict grouping code here for brevity, but it should mirror OutCity step 2
        // to prevent duplicates.

        // 4. ROUTING (Real-Time Context Greedy)
        // -------------------------------------
        // Start Node is the USER
        return $this->runContextRoute($pool);
    }

    private function runContextRoute($pool)
    {
        $route = collect([]);
        $currLat = $this->userLat;
        $currLon = $this->userLon;

        $counter = 0;
        while ($pool->isNotEmpty() && $counter < $this->maxFeedItems) {
            
            $next = $pool->map(function($item) use ($currLat, $currLon) {
                $dist = $this->getDistance($currLat, $currLon, $item->Latitude, $item->Longitude);
                $pop = ($item->ReviewCount ?: 1);

                // --- IN-CITY WEIGHTS ---
                
                // 1. Time Bias (Nightlife)
                if ($this->currentHour >= 18 && str_contains(strtolower($item->title), 'club')) {
                    $pop *= 2.0; // Huge boost at night
                }
                
                // 2. Weather Bias
                // if ($this->weather == 'rain' && isIndoor($item)) $pop *= 1.5;

                // 3. Restaurant Bias
                if ($item->entity_type == 'restaurant') {
                     // If meal time, boost proximity score
                     $dist *= 0.5; // Make it feel closer/cheaper to go to
                }

                $item->score = pow($dist, 1.5) / $pop;
                return $item;
            })->sortBy('score')->first();

            // Dynamic Note
            $distFromUser = $this->getDistance($this->userLat, $this->userLon, $next->Latitude, $next->Longitude);
            if ($distFromUser < 0.5) $next->routing_note = "Walking distance";
            elseif ($next->entity_type == 'restaurant') $next->routing_note = "Eat Nearby";
            else $next->routing_note = "Next Stop";

            $route->push($next);
            $currLat = $next->Latitude;
            $currLon = $next->Longitude;
            $pool = $pool->reject(fn($i) => $i->id == $next->id && $i->entity_type == $next->entity_type);
            $counter++;
        }

        // Inject Insights (e.g., "Heat Warning")
        $feed = $this->formatOutput($route);
        // $this->injectInsights($feed);
        return $feed;
    }

    private function formatOutput($route) {
        // ... (Same formatting logic, just mapping to array) ...
        $feed = [];
        $order = 1;
        foreach($route as $node) {
            $feed[] = [
                'step_order' => $order++,
                'display_type' => $node->display_type,
                'entity_type' => $node->entity_type,
                'routing_note' => $node->routing_note,
                'data' => [ 'id' => $node->id, 'title' => $node->title, 'rating' => $node->Averagerating, 'image' => $node->image ?? null ],
                'also_at' => [] // Fill if grouping logic used
            ];
        }
        return $feed;
    }
}
