<?php

namespace App\Services\Feed;

class OutCityEngine extends AbstractFeedEngine
{
    public function generate()
    {
        list($sights, $experiences, $restaurants) = $this->fetchRawData();
        list($clusterNodes, $standaloneExp) = $this->processClusters($experiences);

        // 1. OUT-CITY SPECIFIC POOLING (Strict Tiers)
        // -------------------------------------------
        
        // Anchors (Large)
        $anchors = collect([])
            ->merge($sights->where('tier', 1))
            ->merge($standaloneExp->where('tier', 1))
            ->merge($clusterNodes)
            ->map(fn($i) => $this->setCard($i, 'card_large'));

        // Standard (Medium)
        $standard = collect([])
            ->merge($sights->where('tier', 2))
            ->merge($standaloneExp->where('tier', 2))
            ->map(fn($i) => $this->setCard($i, 'card_medium'));

        // Restaurants (Strict: Top 5 Tier 2 only)
        $topFood = $restaurants->where('tier', 2)->sortByDesc('ReviewCount')->take(5)
            ->map(fn($i) => $this->setCard($i, 'card_medium'));
        $standard = $standard->merge($topFood);

        // Fillers (Potential Small)
        $fillers = collect([])->merge($sights->where('tier', 3))->merge($standaloneExp->where('tier', 3));
        
        // Leftovers (Also At)
        $leftovers = collect([])
            ->merge($sights->where('tier', 4))
            ->merge($restaurants->diffKeys($topFood)); // All other food

        // 2. GROUPING (300m Rule)
        // -----------------------
        $parents = $anchors->merge($standard);
        $children = $fillers->merge($leftovers);

        $parents = $parents->map(function($p) use (&$children) {
            $p->also_at = [];
            $nearby = $children->filter(fn($c) => $this->getDistance($p->Latitude, $p->Longitude, $c->Latitude, $c->Longitude) < 0.3);
            if ($nearby->isNotEmpty()) {
                $p->also_at = $nearby->values()->toArray();
                $children = $children->diffKeys($nearby);
            }
            return $p;
        });

        // 3. SPINE STRATEGY
        // -----------------
        // Only allow Tier 3 as small cards if total count is low
        $routingPool = $parents;
        if ($routingPool->count() < 50) {
            $survivingFillers = $children->where('tier', 3)->map(fn($i) => $this->setCard($i, 'card_small'));
            $routingPool = $routingPool->merge($survivingFillers);
        }

        // 4. ROUTING (Static Global Optimization)
        // ---------------------------------------
        $startNode = $routingPool->where('display_type', 'card_large')->sortByDesc('ReviewCount')->first() 
                     ?? $routingPool->sortByDesc('ReviewCount')->first();

        // (Insert Outlier Logic Here if needed...)

        return $this->runSpineRoute($routingPool, $startNode);
    }

    private function runSpineRoute($pool, $startNode)
    {
        $route = collect([]);
        $pool = $pool->reject(fn($i) => $i->id == $startNode->id && $i->entity_type == $startNode->entity_type);
        
        $startNode->routing_note = "Starting Point";
        $startNode->effort_label = $this->getEffortLabel($startNode);
        $route->push($startNode);
        
        $currLat = $startNode->Latitude;
        $currLon = $startNode->Longitude;

        while ($pool->isNotEmpty() && $route->count() < $this->maxFeedItems) {
            // Out-City Weighting: Heavily favor Tier 1s
            $next = $pool->map(function($item) use ($currLat, $currLon) {
                $dist = $this->getDistance($currLat, $currLon, $item->Latitude, $item->Longitude);
                $pop = ($item->ReviewCount ?: 1);
                if ($item->tier == 1) $pop *= 1.5; // Magnetic Pull
                $item->score = pow($dist, 1.5) / $pop;
                return $item;
            })->sortBy('score')->first();

            $next->routing_note = "Next Stop";
            $next->effort_label = $this->getEffortLabel($next);
            
            $route->push($next);
            $currLat = $next->Latitude;
            $currLon = $next->Longitude;
            $pool = $pool->reject(fn($i) => $i->id == $next->id && $i->entity_type == $next->entity_type);
        }

        return $this->formatOutput($route);
    }

    private function getEffortLabel($item) {
        if ($item->tier == 1) return "Iconic";
        if ($item->entity_type == 'experience') return "Popular Activity";
        return null;
    }

    private function setCard($item, $type) { $item->display_type = $type; return $item; }

    private function formatOutput($route) {
        $feed = [];
        $order = 1;
        foreach($route as $node) {
            $feed[] = [
                'step_order' => $order++,
                'display_type' => $node->display_type,
                'entity_type' => $node->entity_type,
                'routing_note' => $node->routing_note,
                'effort_label' => $node->effort_label,
                'data' => [ 'id' => $node->id, 'title' => $node->title, 'rating' => $node->Averagerating, 'image' => $node->image ?? null ],
                'also_at' => $this->formatAlsoAt($node->also_at ?? [])
            ];
        }
        return $feed;
    }
}
