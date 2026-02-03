<?php
// DEPRECATED: Use App\Services\Feed\TouristFeedManager, OutCityEngine, InCityEngine, AbstractFeedEngine for all new feed logic.
// This file is now deprecated and non-functional.


namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TouristFeedEngine
{
    private $location_id;
    private $feed_capacity;
    private $distanceCache = [];
    
    // New algorithm properties
    protected $maxFeedItems = 60;
    protected $tier3InclusionThreshold = 50;
    protected $scarcityThreshold = 15;

    public function __construct($location_id, $city_tourism_score)
    {
        $this->location_id = $location_id;
        
        // Defines feed length based on computed city score (0-100)
        if ($city_tourism_score > 80) {
            $this->feed_capacity = 50;
        } elseif ($city_tourism_score > 40) {
            $this->feed_capacity = 25;
        } else {
            $this->feed_capacity = 15;
        }
        
        // Update maxFeedItems based on city score
        $this->maxFeedItems = $this->feed_capacity;
    }

    /**
     * Haversine Distance (Km)
     */
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        try {
            $lon1 = deg2rad((float)$lon1);
            $lat1 = deg2rad((float)$lat1);
            $lon2 = deg2rad((float)$lon2);
            $lat2 = deg2rad((float)$lat2);
            
            $dlon = $lon2 - $lon1;
            $dlat = $lat2 - $lat1;
            
            $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
            $c = 2 * asin(sqrt($a));
            $r = 6371; // Radius of earth in kilometers
            
            return $c * $r;
        } catch (\Exception $e) {
            return 9999; // Return high distance if coords are missing
        }
    }

    /**
     * Get Distance with caching for performance
     */
    private function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Create cache key
        $key = sprintf('%.6f,%.6f,%.6f,%.6f', $lat1, $lon1, $lat2, $lon2);
        
        // Check cache
        if (isset($this->distanceCache[$key])) {
            return $this->distanceCache[$key];
        }
        
        // Calculate and cache
        $distance = $this->haversine($lat1, $lon1, $lat2, $lon2);
        $this->distanceCache[$key] = $distance;
        
        return $distance;
    }

    /**
     * STEP 1: SELECTION (The Waterfall)
     * Filters listings into 'Main Cards' and 'Satellites'.
     */
    public function selectCandidates($sights_df, $experiences_df)
    {
        // 1. Normalize Data - add type identifier
        foreach ($sights_df as $sight) {
            $sight->type = 'attraction';
        }
        
        foreach ($experiences_df as $exp) {
            $exp->type = 'experience';
        }
        
        // Combine Sights and Tier 1/2 Experiences for initial pool
        $pool = collect($sights_df)->merge(
            collect($experiences_df)->filter(function($exp) {
                return isset($exp->tier) && in_array($exp->tier, [1, 2]);
            })
        );

        // 2. The Sacred Layer (Tier 1 & 2) - both sights and experiences
        $main_cards = $pool->filter(function($item) {
            return isset($item->tier) && in_array($item->tier, [1, 2]);
        });
        
        // 3. High-value Tier 3 experiences (can be promoted to main cards if slots available)
        $high_val_exp = collect($experiences_df)->filter(function($exp) {
            return isset($exp->tier) && $exp->tier == 3
                   && isset($exp->popularity_score) && $exp->popularity_score > 60;
        });
        
        // 4. Fill Remaining Slots with Tier 3
        $current_count = $main_cards->count();
        $slots_left = $this->feed_capacity - $current_count;
        
        $selected_tier_3 = collect([]);
        
        if ($slots_left > 0) {
            // Get Tier 3 candidates sorted by review count
            $candidates_t3 = collect($sights_df)->filter(function($sight) {
                return isset($sight->tier) && $sight->tier == 3;
            })->sortByDesc('ReviewCount');
            
            $valid_t3 = [];
            
            // Check proximity to existing main cards to avoid redundancy
            foreach ($candidates_t3 as $t3) {
                if (count($valid_t3) >= $slots_left) {
                    break;
                }
                
                $is_satellite = false;
                foreach ($main_cards as $main) {
                    $dist = $this->haversine($t3->Latitude, $t3->Longitude, $main->Latitude, $main->Longitude);
                    if ($dist < 0.5) {
                        $is_satellite = true;
                        break;
                    }
                }
                
                if (!$is_satellite) {
                    $valid_t3[] = $t3;
                }
            }
            
            $selected_tier_3 = collect($valid_t3);
        }

        // Final Main Deck
        if (!$selected_tier_3->isEmpty()) {
            $final_deck = $main_cards->merge($selected_tier_3);
        } else {
            $final_deck = $main_cards;
        }

        // Identify Satellites - sights and experiences not in main deck
        $main_ids = $final_deck->map(function($item) {
            return $item->SightId ?? $item->ExperienceId ?? null;
        })->filter();
        
        $leftover_sights = collect($sights_df)->filter(function($sight) use ($main_ids) {
            return !$main_ids->contains($sight->SightId);
        });
        
        $leftover_experiences = collect($experiences_df)->filter(function($exp) use ($main_ids) {
            return !$main_ids->contains($exp->SightId) && (!isset($exp->tier) || !in_array($exp->tier, [1, 2]));
        });
        
        return [$final_deck, $leftover_sights, $leftover_experiences];
    }

    /**
     * STEP 2: IDENTIFY STARTING POINT (Anchor vs Outlier)
     */
    public function determineStartNode($deck)
    {
        // 1. Find Tier 1 Highest Rated
        $tier1s = $deck->filter(function($item) {
            return isset($item->tier) && $item->tier == 1;
        });
        
        if ($tier1s->isEmpty()) {
            $tier1s = $deck; // Fallback
        }
            
        $start_node = $tier1s->sortByDesc('ReviewCount')->first();
        
        // 2. Calculate Centroid of the Deck
        $avg_lat = $deck->avg('Latitude');
        $avg_lon = $deck->avg('Longitude');
        
        // 3. Calculate Average Spread
        $distances_from_center = $deck->map(function($row) use ($avg_lat, $avg_lon) {
            return $this->haversine($row->Latitude, $row->Longitude, $avg_lat, $avg_lon);
        });
        $avg_spread = $distances_from_center->avg();
        
        // 4. Check Outlier
        $dist_start_to_center = $this->haversine($start_node->Latitude, $start_node->Longitude, $avg_lat, $avg_lon);
        
        $is_outlier = false;
        if ($dist_start_to_center > (2 * $avg_spread) && $dist_start_to_center > 5.0) { // Hard min 5km
            $is_outlier = true;
        }
            
        return [$start_node, $is_outlier, $avg_lat, $avg_lon];
    }

    /**
     * STEP 3: THE ROUTING (Weighted TSP)
     * NO DAY/NIGHT SPLITTING - Single pool approach
     */
    public function optimizeRoute($deck, $start_node, $is_outlier, $center_lat, $center_lon)
    {
        // Create single pool (no day/night separation per final requirements)
        $deck_collection = collect($deck);  
        $ordered_route = [];
        
        // Add Start Node
        $ordered_route[] = [
            'type' => 'main',
            'card' => $start_node,
            'note' => 'Starting Point'
        ];

        // If Outlier, jump to center
        if ($is_outlier) {
            if (!$deck_collection->isEmpty()) {
                $next_stop = $deck_collection->sortBy(function($item) use ($center_lat, $center_lon) {
                    return $this->haversine($item->Latitude, $item->Longitude, $center_lat, $center_lon);
                })->first();
                
                if ($next_stop) {
                    $dist_to_center = $this->haversine($next_stop->Latitude, $next_stop->Longitude, $center_lat, $center_lon);
                    
                    $ordered_route[] = [
                        'type' => 'transit',
                        'note' => 'Transit to City Center (' . round($dist_to_center) . ' km)'
                    ];
                    
                    $ordered_route[] = [
                        'type' => 'main',
                        'card' => $next_stop,
                        'note' => 'City Center Hub'
                    ];
                    
                    $current_lat = (float)$next_stop->Latitude;
                    $current_lon = (float)$next_stop->Longitude;
                    
                    $next_id = $next_stop->SightId ?? $next_stop->ExperienceId ?? null;
                    $deck_collection = $deck_collection->filter(function($item) use ($next_id) {
                        $item_id = $item->SightId ?? $item->ExperienceId ?? null;
                        return $item_id != $next_id;
                    });
                }
            } else {
                $current_lat = (float)$start_node->Latitude;
                $current_lon = (float)$start_node->Longitude;
            }
        } else {
            $current_lat = (float)$start_node->Latitude;
            $current_lon = (float)$start_node->Longitude;
        }

        // GREEDY WEIGHTED ROUTING LOOP (Single pool - no day/night split)
        while (!$deck_collection->isEmpty()) {
            // Calculate Cost for all remaining items
            // Formula: Distance^1.5 / ReviewCount (punishes distance, rewards popularity)
            
            $winner = $deck_collection->map(function($item) use ($current_lat, $current_lon) {
                $dist = $this->haversine($current_lat, $current_lon, $item->Latitude, $item->Longitude);
                $popularity_factor = ($item->ReviewCount && $item->ReviewCount > 0) ? $item->ReviewCount : 1;
                $effort_score = pow($dist, 1.5) / $popularity_factor;
                $item->effort_score = $effort_score;
                return $item;
            })->sortBy('effort_score')->first();
            
            $ordered_route[] = [
                'type' => 'main',
                'card' => $winner,
                'note' => 'Next Stop'
            ];
            
            $current_lat = (float)$winner->Latitude;
            $current_lon = (float)$winner->Longitude;
            
            $winner_id = $winner->SightId ?? $winner->ExperienceId ?? null;
            $deck_collection = $deck_collection->filter(function($item) use ($winner_id) {
                $item_id = $item->SightId ?? $item->ExperienceId ?? null;
                return $item_id != $winner_id;
            });
        }
        
        return $ordered_route;
    }

    /**
     * STEP 4: ASSEMBLY (Attach Satellites & Restaurants)
     * New simplified structure with step_order, type, data, nearby
     */
    public function assembleFeed($route, $leftover_sights, $leftover_experiences, $restaurants_df)
    {
        $feed_output = [];
        $step_order = 1;
        
        foreach ($route as $step) {
            // Handle Transit Cards
            if ($step['type'] === 'transit') {
                $feed_output[] = [
                    'step_order' => $step_order++,
                    'type' => 'transit',
                    'routing_note' => $step['note'],
                    'data' => [
                        'message' => $step['note']
                    ],
                    'nearby' => null
                ];
                continue;
            }
            
            // Main Card
            $main_card = $step['card'];
            $main_lat = (float)$main_card->Latitude;
            $main_lon = (float)$main_card->Longitude;
            
            // 1. Find Nearby Sights (Tier 3/4) - Max 5
            $nearby_sights = [];
            if (!empty($leftover_sights)) {
                $nearby = collect($leftover_sights)->filter(function($item) use ($main_lat, $main_lon) {
                    return $this->haversine($main_lat, $main_lon, $item->Latitude, $item->Longitude) < 1.0;
                })->take(5);
                
                $nearby_sights = $nearby->map(function($item) {
                    return [
                        'id' => $item->SightId,
                        'title' => $item->Title,
                        'tier' => $item->tier ?? 4,
                        'rating' => $item->Averagerating ?? null,
                        'image' => null, // Will be fetched from sightImages in blade
                        'slug' => $item->Slug ?? null,
                        'slugid' => $item->slugid ?? null
                    ];
                })->values()->toArray();
                
                // CRITICAL: Remove used sights from leftover pool to prevent duplicates
                $used_sight_ids = $nearby->pluck('SightId')->toArray();
                $leftover_sights = collect($leftover_sights)->filter(function($item) use ($used_sight_ids) {
                    return !in_array($item->SightId, $used_sight_ids);
                });
            }
            
            // 2. Find Nearby Experiences (Tier 3/4) - Max 5
            $nearby_experiences = [];
            if (!empty($leftover_experiences)) {
                $nearby_exp = collect($leftover_experiences)->filter(function($item) use ($main_lat, $main_lon) {
                    return $this->haversine($main_lat, $main_lon, $item->Latitude, $item->Longitude) < 1.0;
                })->take(5);
                
                $nearby_experiences = $nearby_exp->map(function($item) {
                    return [
                        'id' => $item->SightId,
                        'title' => $item->Title ?? $item->Name ?? '',
                        'rating' => $item->Averagerating ?? null,
                        'image' => $item->Img1 ?? null,
                        'slug' => $item->Slug ?? null,
                        'slugid' => $item->slugid ?? null
                    ];
                })->values()->toArray();
                
                // CRITICAL: Remove used experiences from leftover pool to prevent duplicates
                $used_exp_ids = $nearby_exp->pluck('SightId')->toArray();
                $leftover_experiences = collect($leftover_experiences)->filter(function($item) use ($used_exp_ids) {
                    return !in_array($item->SightId, $used_exp_ids);
                });
            }
            
            // 3. Find Nearby Restaurants (Rating >= 4.0) - Max 3
            $nearby_restaurants = [];
            if (!empty($restaurants_df)) {
                $nearby_food = collect($restaurants_df)->filter(function($item) use ($main_lat, $main_lon) {
                    return isset($item->Averagerating) && $item->Averagerating >= 4.0
                           && $this->haversine($main_lat, $main_lon, $item->Latitude, $item->Longitude) < 100.0;
                })->sortByDesc('ReviewCount')->take(3);
                
                $nearby_restaurants = $nearby_food->map(function($item) {
                    return [
                        'id' => $item->RestaurantId,
                        'title' => $item->Title,
                        'cuisines' => $item->cuisines ?? '',
                        'rating' => $item->Averagerating ?? null,
                        'slug' => $item->Slug ?? null,
                        'slugid' => $item->slugid ?? null
                    ];
                })->values()->toArray();
                
                // CRITICAL: Remove used restaurants from pool to prevent duplicates
                $used_rest_ids = $nearby_food->pluck('RestaurantId')->toArray();
                $restaurants_df = collect($restaurants_df)->filter(function($item) use ($used_rest_ids) {
                    return !in_array($item->RestaurantId, $used_rest_ids);
                });
            }
            
            // Build Final JSON Object with new structure
            $item_type = $main_card->type ?? 'attraction';
            $item_id = $main_card->SightId ?? $main_card->ExperienceId ?? null;
            
            $feed_item = [
                'step_order' => $step_order++,
                'type' => $item_type, // 'attraction' or 'experience'
                'routing_note' => $step['note'],
                'data' => [
                    'id' => $item_id,
                    'title' => $main_card->Title ?? $main_card->Name ?? '',
                    'tier' => $main_card->tier ?? null,
                    'rating' => $main_card->Averagerating ?? null,
                    'review_count' => $main_card->ReviewCount ?? 0,
                    'image' => $main_card->Img1 ?? null,
                    'category_title' => $main_card->CategoryTitle ?? ($main_card->category_title ?? null),
                    'short_description' => $main_card->short_description ?? ($main_card->MicroSummary ?? null),
                    'latitude' => $main_card->Latitude ?? null,
                    'longitude' => $main_card->Longitude ?? null,
                    'slug' => $main_card->Slug ?? null,
                    'slugid' => $main_card->slugid ?? null
                ],
                'nearby' => [
                    'sights' => $nearby_sights,
                    'experiences' => $nearby_experiences,
                    'restaurants' => $nearby_restaurants
                ]
            ];
            
            $feed_output[] = $feed_item;
        }
        
        return $feed_output;
    }

    /**
     * NEW: Process Clusters (Group experiences > 3 items within 200m)
     */
    protected function processClusters($experiences)
    {
        // For MVP: Return empty clusters and treat all exp as standalone
        // In production, implement spatial clustering logic
        return [collect([]), $experiences];
    }

    /**
     * NEW: Classify Nodes into Anchors, Standard, Fillers, Leftovers
     */
    protected function classifyNodes($sights, $standaloneExperiences, $restaurants, $clusterNodes, $isScarceCity)
    {
        // Group A: ANCHORS (Large Cards)
        $anchors = collect([])
            ->merge($sights->where('tier', 1))
            ->merge($standaloneExperiences->where('tier', 1))
            ->merge($clusterNodes)
            ->map(function($item) { 
                $item->display_type = 'card_large'; 
                $item->also_at = [];
                if (!isset($item->CategoryTitle) && isset($item->category_title)) {
                    $item->CategoryTitle = $item->category_title;
                }
                return $item; 
            });

        // Group B: STANDARD (Medium Cards)
        $standard = collect([])
            ->merge($sights->where('tier', 2))
            ->merge($standaloneExperiences->where('tier', 2))
            ->map(function($item) { 
                $item->display_type = 'card_medium'; 
                $item->also_at = [];
                if (!isset($item->CategoryTitle) && isset($item->category_title)) {
                    $item->CategoryTitle = $item->category_title;
                }
                return $item; 
            });

        // Group C: RESTAURANTS (Selective Medium Cards)
        // Only Top 5 Tier 2 Restaurants get independent Medium cards
        $topRestaurants = $restaurants->where('tier', 2)
            ->sortByDesc('ReviewCount')
            ->take(5)
            ->map(function($item) { 
                $item->display_type = 'card_medium'; 
                $item->also_at = [];
                $item->entity_type = 'restaurant';
                return $item; 
            });
        
        $standard = $standard->merge($topRestaurants);

        // Group D: FILLERS (Small Cards candidates)
        // Tier 3 Sights/Experiences
        $fillers = collect([])
            ->merge($sights->where('tier', 3))
            ->merge($standaloneExperiences->where('tier', 3));

        // Group E: LEFTOVERS (For "Also At" grouping only)
        // Tier 4s (unless scarce city), Remaining Restaurants
        $leftovers = collect([]);
        
        if ($isScarceCity) {
            // If city is empty, promote T4 to Fillers (Small Cards)
            $fillers = $fillers->merge($sights->where('tier', 4))
                               ->merge($standaloneExperiences->where('tier', 4));
        } else {
            // Otherwise, T4s are just leftovers
            $leftovers = $leftovers->merge($sights->where('tier', 4))
                                   ->merge($standaloneExperiences->where('tier', 4));
        }
        
        // Add remaining restaurants to leftovers
        $remainingRestaurants = $restaurants->diffKeys($topRestaurants);
        $leftovers = $leftovers->merge($remainingRestaurants);

return [$anchors, $standard, $fillers, $leftovers];
    }

    /**
     * NEW: Attach "Also At" children within 300m to parents (OPTIMIZED)
     */
    protected function attachAlsoAt($anchors, $standard, $fillers, $leftovers)
    {
        $parents = $anchors->merge($standard);
        $childrenPool = $fillers->merge($leftovers);
        
        // Early exit if no children
        if ($childrenPool->isEmpty()) {
            return [$parents, $childrenPool];
        }

        // Convert to arrays for faster iteration
        $childrenArray = $childrenPool->values()->all();
        $usedChildIndices = [];
        
        $parents = $parents->map(function($parent) use ($childrenArray, &$usedChildIndices) {
            if (!isset($parent->also_at)) {
                $parent->also_at = [];
            }
            
            $nearby = [];
            $maxAlsoAt = 8; // Limit also_at items per parent
            
            // Find children within 300m using cached distance
            foreach ($childrenArray as $index => $child) {
                // Skip already used children
                if (isset($usedChildIndices[$index])) {
                    continue;
                }
                
                // Stop if we have enough
                if (count($nearby) >= $maxAlsoAt) {
                    break;
                }
                
                $dist = $this->getDistance($parent->Latitude, $parent->Longitude, $child->Latitude, $child->Longitude);
                
                if ($dist < 0.3) {
                    $nearby[] = [
                        'id' => $child->SightId ?? $child->ExperienceId ?? $child->RestaurantId ?? null,
                        'title' => $child->Title ?? $child->Name ?? '',
                        'tier' => $child->tier ?? 4,
                        'entity_type' => $child->entity_type ?? ($child->RestaurantId ?? false ? 'restaurant' : 'sight'),
                        'image' => $child->Img1 ?? null,
                        'rating' => $child->Averagerating ?? null,
                        'slug' => $child->Slug ?? null,
                        'slugid' => $child->slugid ?? null
                    ];
                    $usedChildIndices[$index] = true;
                }
            }
            
            if (!empty($nearby)) {
                $parent->also_at = $nearby;
            }
            
            return $parent;
        });
        
        // Remove used children from pool
        $remainingChildren = collect();
        foreach ($childrenArray as $index => $child) {
            if (!isset($usedChildIndices[$index])) {
                $remainingChildren->push($child);
            }
        }

        return [$parents, $remainingChildren];
    }

    /**
     * NEW: Finalize Routing Pool (decide which Tier 3s get independent cards)
     */
    protected function finalizeRoutingPool($parents, $childrenPool)
    {
        // Start with Large and Medium cards
        $routingPool = $parents;
        
        // Extract surviving fillers (Tier 3s that weren't consumed by parents)
        $survivingFillers = $childrenPool->where('tier', 3);
        
        // Density Rule: Include Tier 3s (Small Cards) only if Large+Medium count is low
        if ($routingPool->count() < $this->tier3InclusionThreshold) {
            $survivingFillers = $survivingFillers
                ->take(20) // Limit Tier 3 additions for performance
                ->map(function($item) {
                    $item->display_type = 'card_small';
                    if (!isset($item->also_at)) {
                        $item->also_at = [];
                    }
                    return $item;
                });
            $routingPool = $routingPool->merge($survivingFillers);
        }
        
        // Cap total routing pool size for performance
        if ($routingPool->count() > 80) {
            $routingPool = $routingPool->take(80);
        }

        return $routingPool;
    }

    /**
     * NEW: Determine Start Node for new algorithm
     */
    protected function determineStartNodeNew($routingPool)
    {
        if ($routingPool->isEmpty()) {
            return [null, false, ['lat' => 0, 'lon' => 0]];
        }

        // Prefer Tier 1 Anchor as start
        $startNode = $routingPool->where('display_type', 'card_large')
            ->sortByDesc('ReviewCount')
            ->first();
            
        if (!$startNode) {
            $startNode = $routingPool->sortByDesc('ReviewCount')->first();
        }

        // Centroid Check (OPTIMIZED)
        $avgLat = $routingPool->avg('Latitude');
        $avgLon = $routingPool->avg('Longitude');
        $centerCoords = ['lat' => $avgLat, 'lon' => $avgLon];
        
        $isOutlier = false;
        if ($startNode && $routingPool->count() > 5) {
            $distToCenter = $this->getDistance($startNode->Latitude, $startNode->Longitude, $avgLat, $avgLon);
            
            // Sample-based spread calculation for performance (use max 20 items)
            $sampleSize = min(20, $routingPool->count());
            $sample = $routingPool->random($sampleSize);
            $avgSpread = $sample->map(fn($i) => $this->getDistance($i->Latitude, $i->Longitude, $avgLat, $avgLon))->avg();
            
            if ($distToCenter > 5.0 && $distToCenter > ($avgSpread * 2)) {
                $isOutlier = true;
            }
        }

        return [$startNode, $isOutlier, $centerCoords];
    }

    /**
     * NEW: Optimize Route with new algorithm
     */
    protected function optimizeRouteNew($deck, $startNode, $isOutlier, $centerCoords, $context = [])
    {
        if ($deck->isEmpty() || !$startNode) {
            return collect([]);
        }

        // Get ID helper
        $getId = function($item) {
            return $item->SightId ?? $item->ExperienceId ?? $item->RestaurantId ?? null;
        };

        // Remove Start Node from pool
        $startId = $getId($startNode);
        $pool = $deck->reject(fn($item) => $getId($item) == $startId);
        
        $route = collect([]);
        
        // Add Start
        $startNode->routing_note = "Starting Point";
        $route->push($startNode);

        $currentLat = $startNode->Latitude;
        $currentLon = $startNode->Longitude;

        // Transit logic
        if ($isOutlier) {
            $nextStop = $pool->sortBy(fn($i) => $this->haversine($i->Latitude, $i->Longitude, $centerCoords['lat'], $centerCoords['lon']))->first();
            
            if ($nextStop) {
                // Insert Transit "Card"
                $transitObj = (object)[
                    'display_type' => 'transit',
                    'entity_type' => 'transit',
                    'data' => ['message' => 'Heading to the main city center'],
                    'also_at' => []
                ];
                $route->push($transitObj);

                $nextStop->routing_note = "City Center Hub";
                $route->push($nextStop);
                
                $currentLat = $nextStop->Latitude;
                $currentLon = $nextStop->Longitude;
                $nextId = $getId($nextStop);
                $pool = $pool->reject(fn($item) => $getId($item) == $nextId);
            }
        }

        // Greedy Loop (OPTIMIZED)
        $counter = 0;
        $poolArray = $pool->values()->all();
        
        while (!empty($poolArray) && $counter < $this->maxFeedItems) {
            $bestScore = PHP_FLOAT_MAX;
            $bestIndex = -1;
            $bestItem = null;
            
            // Context-aware scoring
            foreach ($poolArray as $index => $item) {
                $score = $this->calculateScore($item, $currentLat, $currentLon, $context);
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestIndex = $index;
                    $bestItem = $item;
                }
            }
            
            if ($bestItem === null) {
                break;
            }

            $bestItem->routing_note = "Next Stop";
            $route->push($bestItem);

            $currentLat = $bestItem->Latitude;
            $currentLon = $bestItem->Longitude;
            
            // Remove from pool array
            array_splice($poolArray, $bestIndex, 1);
            $counter++;
        }

        return $route;
    }

    /**
     * Context-aware scoring (weather, time, meal, proximity magnetism)
     */
    protected function calculateScore($item, $currLat, $currLon, $context = [])
    {
        $dist = $this->getDistance($currLat, $currLon, $item->Latitude, $item->Longitude);
        $popularity = ($item->ReviewCount > 0 ? $item->ReviewCount : 1);

        // BASE ADJUSTMENTS (Tier Multiplier)
        if (isset($item->tier)) {
            if ($item->tier == 1) $popularity *= 1.5;
            elseif ($item->tier == 2) $popularity *= 1.2;
        }

        $mode = $context['mode'] ?? 'out_city';
        if ($mode === 'out_city') {
            // OUT-CITY: heavily penalize distance, reward fame
            return pow($dist, 1.5) / $popularity;
        } else {
            // IN-CITY: adjust for weather/time/meal/proximity
            $penalty = 1.0;
            $weather = $context['weather'] ?? 'clear';
            $time_bucket = $context['time_bucket'] ?? 'midday';
            $isMealTime = $context['is_meal_time'] ?? false;
            $userLat = $context['user_lat'] ?? $currLat;
            $userLon = $context['user_lon'] ?? $currLon;

            // Weather: Hot -> Penalize Outdoor
            $isOutdoor = method_exists($this, 'isOutdoor') ? $this->isOutdoor($item) : false;
            if ($weather === 'hot' && $time_bucket === 'midday' && $isOutdoor) {
                $penalty *= 1.3;
            }
            if ($weather === 'rain' && !$isOutdoor) {
                $popularity *= 1.2;
            }
            // Time: Night -> Penalize "Day" activities, Boost "Night"
            if ($time_bucket === 'night') {
                $isNightLife = method_exists($this, 'isNightLife') ? $this->isNightLife($item) : false;
                if ($isNightLife) $popularity *= 1.5;
                elseif ($isOutdoor) $penalty *= 1.5;
            }
            // Proximity Magnetism
            $distToUser = $this->getDistance($userLat, $userLon, $item->Latitude, $item->Longitude);
            if ($distToUser < 0.5) {
                $popularity *= 2.0;
            }
            return ($dist * $penalty) / $popularity;
        }
    }

    /**
     * NEW: Format Feed for output
     */
    protected function formatFeed($route, $context = [])
    {
        $feed = [];
        $order = 1;

        $lastDist = 0;
        $firstItem = true;
        foreach ($route as $node) {
            // Skip User Location node if present
            if (isset($node->entity_type) && $node->entity_type === 'user_loc') continue;

            // INJECT INSIGHT CARD (In-City Only)
            if ($firstItem && ($context['mode'] ?? 'out_city') === 'in_city') {
                if (($context['weather'] ?? '') === 'hot') {
                    $feed[] = $this->makeInsightCard($order++, "It's 42°C. We've prioritized indoor spots and short walks.");
                } elseif (($context['is_meal_time'] ?? false)) {
                    $feed[] = $this->makeInsightCard($order++, "It's lunch time. Look for our suggested dining stops.");
                }
                $firstItem = false;
            }

            if ($node->display_type === 'transit') {
                $feed[] = [
                    'step_order' => $order++,
                    'display_type' => 'transit',
                    'type' => 'transit',
                    'entity_type' => 'transit',
                    'routing_note' => $node->data['message'] ?? 'Transit',
                    'data' => $node->data,
                    'also_at' => []
                ];
                continue;
            }

            // Format "Also At" Items
            $alsoAtFormatted = $node->also_at ?? [];

            $feed[] = [
                'step_order' => $order++,
                'display_type' => $node->display_type,
                'type' => $node->entity_type ?? ($node->type ?? 'attraction'),
                'entity_type' => $node->entity_type ?? ($node->type ?? 'attraction'),
                'routing_note' => $this->generateRoutingNote($node, $context, $node->Latitude ?? null, $node->Longitude ?? null),
                'data' => [
                    'id' => $node->SightId ?? $node->ExperienceId ?? $node->RestaurantId ?? null,
                    'title' => $node->Title ?? $node->Name ?? '',
                    'tier' => $node->tier ?? null,
                    'rating' => $node->Averagerating ?? 0,
                    'review_count' => $node->ReviewCount ?? 0,
                    'image' => $node->Img1 ?? null,
                    'category_title' => $node->CategoryTitle ?? null,
                    'short_description' => $node->short_description ?? ($node->MicroSummary ?? null),
                    'latitude' => $node->Latitude ?? null,
                    'longitude' => $node->Longitude ?? null,
                    'slug' => $node->Slug ?? null,
                    'slugid' => $node->slugid ?? null
                ],
                'also_at' => $alsoAtFormatted
            ];
        }

        return $feed;
    }

    protected function makeInsightCard($order, $message) {
        return [
            'step_order' => $order,
            'display_type' => 'insight',
            'entity_type' => 'insight',
            'data' => ['message' => $message, 'icon' => 'info']
        ];
    }

    protected function generateRoutingNote($item, $context, $currLat, $currLon)
    {
        $dist = isset($item->Latitude, $item->Longitude, $currLat, $currLon) ? $this->getDistance($currLat, $currLon, $item->Latitude, $item->Longitude) : 0;
        if (($context['mode'] ?? 'out_city') === 'out_city') {
            if ($dist > 5.0) return "Explore a new area";
            return "Nearby";
        } else {
            // In-City Contextual Notes
            if (($context['is_meal_time'] ?? false) && ($item->entity_type ?? null) === 'restaurant') return "Great for Lunch";
            if ($dist < 0.5) return "Within walking distance";
            if (($context['time_bucket'] ?? '') === 'evening' && method_exists($this, 'isNightLife') && $this->isNightLife($item)) return "Best after sunset";
            return "Good option right now";
        }
    }

    protected function getTimeBucket() {
        $h = (int)date('G');
        if ($h >= 8 && $h < 11) return 'morning';
        if ($h >= 11 && $h < 15) return 'midday';
        if ($h >= 15 && $h < 19) return 'evening';
        return 'night';
    }

    protected function isMealTime() {
        $h = (int)date('G');
        return ($h >= 12 && $h <= 14) || ($h >= 19 && $h <= 21);
    }

    protected function isOutdoor($item) {
        $keywords = ['park', 'garden', 'walk', 'zoo', 'beach', 'hike'];
        $knownFor = strtolower($item->KnownFor ?? '');
        foreach ($keywords as $kw) {
            if (strpos($knownFor, $kw) !== false) return true;
        }
        return false;
    }

    protected function isNightLife($item) {
        $keywords = ['club', 'bar', 'pub', 'night', 'casino', 'show'];
        $knownFor = strtolower($item->KnownFor ?? '');
        foreach ($keywords as $kw) {
            if (strpos($knownFor, $kw) !== false) return true;
        }
        return false;
    }

    /**
     * MAIN ENTRY POINT - Updated to use new algorithm
     */
    public function generateFeed($sights_df, $experiences_df, $restaurants_df = [], $context = [])
    {
        try {
            // Convert arrays to collections
            $sights = collect($sights_df);
            $experiences = collect($experiences_df);
            $restaurants = collect($restaurants_df);

            // CONTEXT-SENSITIVE RESTAURANT LOGIC
            $validRestaurants = collect([]);
            $mode = $context['mode'] ?? 'out_city';
            $isMealTime = $context['is_meal_time'] ?? false;
            $userLat = $context['user_lat'] ?? null;
            $userLon = $context['user_lon'] ?? null;
            if ($mode === 'out_city') {
                // Out-City: Strict. Top 5 Tier 2 only.
                $validRestaurants = $restaurants->where('tier', 2)->sortByDesc('ReviewCount')->take(5)
                    ->map(function($r){ $r->display_type = 'card_medium'; $r->entity_type = 'restaurant'; return $r; });
            } else {
                // In-City: Conditional Unlock
                if ($isMealTime) {
                    // If it's meal time, unlock restaurants near the USER (within 5km) or High Tier
                    $validRestaurants = $restaurants->filter(function($r) use ($userLat, $userLon) {
                        $dist = $this->getDistance($userLat, $userLon, $r->Latitude, $r->Longitude);
                        // Tier 1/2 always allowed, others only if near
                        return ($r->tier <= 2) || ($dist < 5.0 && $r->Averagerating >= 4.0);
                    })->map(function($r){ $r->display_type = 'card_small'; $r->entity_type = 'restaurant'; return $r; });
                }
            }
            // Use $validRestaurants for downstream logic instead of $restaurants
            $restaurants = $validRestaurants;
            
            // Add entity_type to items
            $sights = $sights->map(function($item) {
                $item->entity_type = 'attraction';
                $item->type = 'attraction';
                return $item;
            });
            
            $experiences = $experiences->map(function($item) {
                $item->entity_type = 'experience';
                $item->type = 'experience';
                return $item;
            });
            
            // 1. PRE-PROCESSING & CLUSTERING
            list($clusterNodes, $standaloneExperiences) = $this->processClusters($experiences);
            
            // 2. SCARCITY CHECK
            $totalItems = $sights->count() + $standaloneExperiences->count();
            $isScarceCity = $totalItems < $this->scarcityThreshold;
            
            // 3. CLASSIFY NODES
            list($anchors, $standard, $fillers, $leftovers) = $this->classifyNodes(
                $sights, 
                $standaloneExperiences, 
                $restaurants, 
                $clusterNodes, 
                $isScarceCity
            );
            
            // 4. THE 300M GROUPING RULE ("Also At")
            list($parents, $childrenPool) = $this->attachAlsoAt($anchors, $standard, $fillers, $leftovers);
            
            // 5. FINALIZE ROUTING POOL
            $routingPool = $this->finalizeRoutingPool($parents, $childrenPool);
            
            // 6. DETERMINE START & OUTLIER
            list($startNode, $isOutlier, $centerCoords) = $this->determineStartNodeNew($routingPool);
            
            // 7. ROUTE OPTIMIZATION
            $route = $this->optimizeRouteNew($routingPool, $startNode, $isOutlier, $centerCoords, $context);
            
            // 8. FORMAT OUTPUT
            return $this->formatFeed($route, $context);
            
        } catch (\Exception $e) {
            Log::error('TouristFeedEngine Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
