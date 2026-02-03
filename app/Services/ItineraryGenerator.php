<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItineraryGenerator {
    private $distanceCache = [];
    private $maxResults = 1000;

    // Configurable options with sensible defaults
    private $proximityMeters = 1000;        // default 1 km
    private $maxNearbyPerTier1 = 3;
    private $dynamicRadiusEnabled = true;   // allow radius expansion in sparse cities
    private $maxRadiusMeters = 5000;        // do not expand beyond 5km
    private $radiusStepMeters = 1000;       // expand by 1km per step

    // popularity scoring weights
    private $w_rating = 0.5;
    private $w_count = 0.30;
    private $w_unique = 0.15;
    private $w_hidden = 0.05;

    // distance penalty
    private $alpha = 0.25;            // how strongly distance reduces objective
    private $distanceScaleKm = 5.0;   // normalize distances (5 km typical local scale)

    // thresholds
    private $mustSeeScore = 0.85;
    private $hidden_min_count = 5;
    private $hidden_max_count = 250;
    private $hidden_rating_threshold = 4.5;
    private $restaurant_rating_min = 4.2;
    private $restaurant_count_min = 200;
    private $market_factor = 0.95;
    private $min_city_items = 15;

    // routing caps
    private $maxStops = 12; // max main stops in core route

    public function __construct(array $options = []) {
        if (isset($options['proximityMeters'])) $this->proximityMeters = (int)$options['proximityMeters'];
        if (isset($options['maxNearbyPerTier1'])) $this->maxNearbyPerTier1 = (int)$options['maxNearbyPerTier1'];
        if (isset($options['dynamicRadiusEnabled'])) $this->dynamicRadiusEnabled = (bool)$options['dynamicRadiusEnabled'];
        if (isset($options['maxRadiusMeters'])) $this->maxRadiusMeters = (int)$options['maxRadiusMeters'];
        if (isset($options['radiusStepMeters'])) $this->radiusStepMeters = (int)$options['radiusStepMeters'];

        // optional tuning via constructor
        if (isset($options['w_rating'])) $this->w_rating = (float)$options['w_rating'];
        if (isset($options['w_count'])) $this->w_count = (float)$options['w_count'];
        if (isset($options['w_unique'])) $this->w_unique = (float)$options['w_unique'];
        if (isset($options['w_hidden'])) $this->w_hidden = (float)$options['w_hidden'];
    }

    /**
     * Main generator entry
     */
    public function generateItinerary(array $params = []): array
    {
        try {
            $locationId = $params['locationId'] ?? null;
            $categoryIds = $params['categoryIds'] ?? null;
            $startTime = $params['startTime'] ?? null; // optional: "HH:MM" or DateTime string. If null we assume 11:00.

            Log::info('ItineraryGenerator - locationId: ' . $locationId);
            Log::info('ItineraryGenerator - categoryIds: ' . json_encode($categoryIds));

            if (!$locationId) {
                // fallback: if user passed array of items in $params[0], return that
                if (isset($params[0]) && is_array($params[0])) {
                    return array_slice($params[0], 0, $this->maxResults);
                }
                return [];
            }

            // --- Fetch candidates (Tier 1 primarily) ---
            $tier1Sights = $this->getSights($locationId, null, 1, $categoryIds);
            $tier1Restaurants = $this->getRestaurants($locationId, null, 1);
            $tier1Experiences = $this->getExperiences($locationId, null, 1);

            // promote restaurants/experiences if flagged IsMustSee or high popularity index
            $promotedRestaurants = array_filter($tier1Restaurants, function($r) {
                return (isset($r->IsMustSee) && (int)$r->IsMustSee === 1)
                    || (isset($r->PopularityIndex) && (int)$r->PopularityIndex >= 90);
            });
            $promotedExperiences = array_filter($tier1Experiences, function($e) {
                return (isset($e->tier) && (int)$e->tier === 1)
                    || (isset($e->Exclusive) && !empty($e->Exclusive));
            });

            // unified Tier1 collection
            $allTier1 = collect([]);
            foreach ($tier1Sights as $s) {
                $allTier1->push([
                    'type' => 'sight', 'id' => $s->SightId,
                    'lat' => (float)$s->Latitude, 'lng' => (float)$s->Longitude,
                    'tier' => 1, 'data' => $s
                ]);
            }
            foreach ($promotedRestaurants as $r) {
                $allTier1->push([
                    'type' => 'restaurant', 'id' => $r->RestaurantId ?? $r->SightId,
                    'lat' => (float)$r->Latitude, 'lng' => (float)$r->Longitude,
                    'tier' => 1, 'data' => $r
                ]);
            }
            foreach ($promotedExperiences as $e) {
                $allTier1->push([
                    'type' => 'experience', 'id' => $e->ExperienceId ?? $e->SightId,
                    'lat' => (float)$e->Latitude, 'lng' => (float)$e->Longitude,
                    'tier' => 1, 'data' => $e
                ]);
            }

            // fallback: if no tier1 found, pull top sights
            if ($allTier1->isEmpty()) {
                $fallback = $this->getSights($locationId, 30, null, $categoryIds);
                foreach ($fallback as $s) {
                    $allTier1->push([
                        'type' => 'sight', 'id' => $s->SightId,
                        'lat' => (float)$s->Latitude, 'lng' => (float)$s->Longitude,
                        'tier' => $s->tier ?? 2, 'data' => $s
                    ]);
                }
                if ($allTier1->isEmpty()) return [];
            }

            // --- fetch lower tier items ---
            $tierLowerSights = $this->getSights($locationId, null, [2,3], $categoryIds);
            $tierLowerRestaurants = $this->getRestaurants($locationId, null, [2,3]);
            $tierLowerExperiences = $this->getExperiences($locationId, null, [2,3]);

            $allLowerTier = collect([]);
            foreach ($tierLowerSights as $s) {
                $allLowerTier->push([
                    'type'=>'sight','id'=>$s->SightId,'lat'=>(float)$s->Latitude,'lng'=>(float)$s->Longitude,'tier'=>$s->tier ?? 3,'data'=>$s
                ]);
            }
            foreach ($tierLowerRestaurants as $r) {
                $allLowerTier->push([
                    'type'=>'restaurant','id'=>$r->RestaurantId,'lat'=>(float)$r->Latitude,'lng'=>(float)$r->Longitude,'tier'=>$r->tier ?? 3,'data'=>$r
                ]);
            }
            foreach ($tierLowerExperiences as $e) {
                $allLowerTier->push([
                    'type'=>'experience','id'=>$e->ExperienceId,'lat'=>(float)$e->Latitude,'lng'=>(float)$e->Longitude,'tier'=>$e->tier ?? 3,'data'=>$e
                ]);
            }

            // --- compute cityMaxCount for normalization ---
            $cityMaxCount = $this->getCityMaxReviewCount($locationId);
            if ($cityMaxCount < 1) $cityMaxCount = 1000; // fallback

            // --- compute pop scores for Tier1 items and attach to data for faster access ---
            foreach ($allTier1 as $idx => $it) {
                $pop = $this->computePopScore($it['data'], $cityMaxCount);
                $allTier1[$idx]['data']->pop_score = $pop;
            }

            // ---- Determine start item: most popular (by ReviewCount, then rating then popularity_score) ----
            $startItem = $allTier1->sortByDesc(function($it) {
                $d = $it['data'];
                $score = 0;
                // Strong weight to ReviewCount (popularity)
                $score += isset($d->ReviewCount) ? (int)$d->ReviewCount * 10 : 0;
                // Light weight to rating, but only if review count is decent
                if (isset($d->ReviewCount) && $d->ReviewCount >= 200) {
                    $score += isset($d->Averagerating) ? (float)$d->Averagerating * 5 : 0;
                }
                // Add popularity_score if available
                if (isset($d->popularity_score)) $score += (float)$d->popularity_score;
                return $score;
            })->first();

            // --- Build core route using objective-aware greedy selection ---
            $sortedTier1 = $this->orderByObjectiveGreedy($allTier1, $startItem, $this->maxStops, $cityMaxCount);

            // --- For each main stop, inject nearby lower-tier items (ranked by pop_score) ---
            // prepare lower-tier items 'pop_score'
            foreach ($allLowerTier as $idx => $it) {
                $allLowerTier[$idx]['data']->pop_score = $this->computePopScore($it['data'], $cityMaxCount);
            }

            $finalFeed = collect([]);
            $baseRadiusKm = $this->proximityMeters / 1000.0;

            foreach ($sortedTier1 as $tier1Item) {
                $finalFeed->push($tier1Item);

                // dynamic radius expansion if few matches
                $radiusKm = $baseRadiusKm;
                $injected = 0;
                $maxNearby = $this->maxNearbyPerTier1;

                while ($injected < $maxNearby && $radiusKm <= ($this->maxRadiusMeters / 1000.0)) {
                    // find nearby items within radiusKm
                    // Sort nearby by tier (prefer tier2), then by review/popularity
                    $nearby = $allLowerTier->filter(function($it) use ($tier1Item, $radiusKm) {
                        $dist = $this->calculateDistance($tier1Item['lat'], $tier1Item['lng'], $it['lat'], $it['lng']);
                        return $dist <= $radiusKm;
                    })->sortBy(function($it) {
                        $score = 0;
                        // Prefer tier 2
                        $score += (isset($it['tier']) && $it['tier'] == 2) ? 1000 : 0;
                        $d = $it['data'];
                        // Weight popularity heavily
                        $score += isset($d->ReviewCount) ? (int)$d->ReviewCount * 5 : 0;
                        // Use rating only if ReviewCount is solid (>=200)
                        if (isset($d->ReviewCount) && $d->ReviewCount >= 200) {
                            if (isset($d->Averagerating)) $score += (float)$d->Averagerating * 5;
                        }
                        // Add popularity_score if present
                        if (isset($d->popularity_score)) $score += (float)$d->popularity_score;
                        return -$score; // negative for descending
                    });

                    foreach ($nearby as $nearbyItem) {
                        if ($injected >= $maxNearby) break;
                        // push and remove from allLowerTier so it's not reused
                        $finalFeed->push($nearbyItem);
                        $allLowerTier = $allLowerTier->reject(function($x) use ($nearbyItem) {
                            return $x['type'] === $nearbyItem['type'] && $x['id'] == $nearbyItem['id'];
                        });
                        $injected++;
                    }

                    if ($injected >= $maxNearby) break;
                    if (!$this->dynamicRadiusEnabled) break;

                    // expand radius
                    $radiusKm += ($this->radiusStepMeters / 1000.0);
                }
            }

            // Any remaining lower-tier items (not injected) can be appended at the end (limited)
            $remainingLimit = 30;
            foreach ($allLowerTier->slice(0, $remainingLimit) as $remaining) {
                $finalFeed->push($remaining);
            }

            // --- Insert restaurants at meal windows (simple pass) ---
            // startTime default if not provided
            $start_time_obj = $this->parseStartTimeOrDefault($startTime);
            $finalFeedWithMeals = $this->insertRestaurantsAlongRoute($finalFeed, $start_time_obj, $locationId, $cityMaxCount);

            // Format result for frontend
            $result = $this->formatItinerary($finalFeedWithMeals);

            return $result;

        } catch (\Exception $e) {
            Log::error('ItineraryGenerator error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return [];
        }
    }

    /**
     * Score-aware greedy orderer
     */
    private function orderByObjectiveGreedy(Collection $items, array $startItem, int $maxStops = 12, int $cityMaxCount = 1000): Collection
    {
        $pool = $items->values();
        $ordered = collect([]);

        // find and pop start
        $startIndex = null;
        foreach ($pool as $i => $it) {
            if ($it['type'] === $startItem['type'] && $it['id'] == $startItem['id']) {
                $startIndex = $i;
                break;
            }
        }
        if ($startIndex === null) {
            $current = $pool->shift();
            $ordered->push($current);
        } else {
            $current = $pool->splice($startIndex, 1)[0];
            $ordered->push($current);
        }

        $minObjective = 0.05;
        $searchRadiusKm = max(3.0, $this->proximityMeters / 1000.0);

        while ($pool->isNotEmpty() && $ordered->count() < $maxStops) {
            $best = null; $bestObj = -INF; $bestIdx = null;

            foreach ($pool as $idx => $cand) {
                // skip if missing coords
                if (!isset($cand['lat']) || !isset($cand['lng'])) continue;

                $dKm = $this->calculateDistance($current['lat'], $current['lng'], $cand['lat'], $cand['lng']);
                if ($dKm > ($this->maxRadiusMeters / 1000.0)) continue; // globally too far
                if ($dKm > $searchRadiusKm) continue; // current search window

                // ensure pop_score exists, else compute
                $pop = isset($cand['data']->pop_score) ? (float)$cand['data']->pop_score : $this->computePopScore($cand['data'], $cityMaxCount);
                $penalty = $this->alpha * ($dKm / $this->distanceScaleKm);
                $obj = $pop - $penalty;

                if ($obj > $bestObj) { $bestObj = $obj; $best = $cand; $bestIdx = $idx; }
            }

            if ($best === null || $bestObj < $minObjective) {
                if ($this->dynamicRadiusEnabled && $searchRadiusKm < ($this->maxRadiusMeters / 1000.0)) {
                    $searchRadiusKm += ($this->radiusStepMeters / 1000.0);
                    continue; // try again with expanded radius
                }
                break;
            }

            // append best
            $ordered->push($best);
            $current = $best;
            $pool->splice($bestIdx, 1);
        }

        return $ordered;
    }

    /**
     * Compute popularity score (0..1) for an item.
     */
    private function computePopScore($d, int $cityMaxCount = 1000): float
    {
        // S_rating - try variety of rating fields
        $avg = 0.0;
        if (isset($d->Averagerating) && $d->Averagerating !== null) $avg = (float)$d->Averagerating;
        elseif (isset($d->TAAggregateRating) && $d->TAAggregateRating !== null) $avg = (float)$d->TAAggregateRating;
        elseif (isset($d->ViatorAggregationRating) && $d->ViatorAggregationRating !== null) $avg = (float)$d->ViatorAggregationRating;
        elseif (isset($d->WeightedRating) && $d->WeightedRating !== null) $avg = (float)$d->WeightedRating;

        $S_rating = ($avg > 0) ? max(0, min(1, ($avg - 1.0) / 4.0)) : 0.0;

        // S_count - try multiple review count fields
        $rc = 0;
        if (isset($d->ReviewCount) && $d->ReviewCount !== null) $rc = (int)$d->ReviewCount;
        elseif (isset($d->ViatorReviewCount) && $d->ViatorReviewCount !== null) $rc = (int)$d->ViatorReviewCount;
        elseif (isset($d->TAReviewCount) && $d->TAReviewCount !== null) $rc = (int)$d->TAReviewCount;

        $maxc = max(1, (int)$cityMaxCount);
        $S_count = ($rc > 0) ? (log10($rc + 1) / log10($maxc + 1)) : 0.0;
        $S_count = max(0.0, min(1.0, $S_count));

        // S_unique heuristics
        $S_unique = 0.0;
        if (isset($d->Exclusive) && !empty($d->Exclusive)) $S_unique = 1.0;
        elseif (isset($d->KnownFor) && !empty($d->KnownFor)) $S_unique = 0.6;
        elseif (isset($d->Award) && (int)$d->Award === 1) $S_unique = 0.7;

        // S_hidden boost
        $S_hidden = 0.0;
        if ($rc >= $this->hidden_min_count && $rc <= $this->hidden_max_count && $avg >= $this->hidden_rating_threshold) {
            $S_hidden = 0.2;
        }

        $pop = $this->w_rating * $S_rating + $this->w_count * $S_count + $this->w_unique * $S_unique + $this->w_hidden * $S_hidden;
        // clamp to [0,1]
        return max(0.0, min(1.0, $pop));
    }

    /**
     * Insert restaurants along the route based on estimated times and meal windows.
     * A simple greedy pass that inserts a top restaurant when estimated time falls into meal windows.
     *
     * $feed - collection of items in order (each item: type, id, lat, lng, data)
     * $start_time_obj - DateTime object indicating assumed start of route
     */
    private function insertRestaurantsAlongRoute(Collection $feed, \DateTime $start_time_obj, $locationId, int $cityMaxCount = 1000): Collection
    {
        // average defaults
        $defaultVisitHours = 2.0; // hours per attraction
        $avgSpeedKmh = 20.0; // urban avg speed (used to estimate travel time in km/h)

        // meal windows periods (24-hour)
        $lunchStart = 11.0; $lunchEnd = 14.0;
        $dinnerStart = 18.0; $dinnerEnd = 21.0;

        $result = collect([]);
        $currentTime = clone $start_time_obj;

        // Build a pool of restaurants eligible (from DB) - fetch a larger set for city
        $candidateRestaurants = collect($this->getRestaurants($locationId, 200, null))->map(function($r) {
            // ensure lat/lng floats and create pop_score
            $r->Latitude = (float)$r->Latitude;
            $r->Longitude = (float)$r->Longitude;
            return $r;
        })->filter(function($r) {
            // must have coords
            return !empty($r->Latitude) && !empty($r->Longitude);
        })->values();

        // Precompute pop_score for restaurants
        foreach ($candidateRestaurants as $idx => $r) {
            $candidateRestaurants[$idx]->pop_score = $this->computePopScore($r, $cityMaxCount);
        }

        // Iterate through feed and insert restaurants when estimated time falls into meal windows
        $feedCount = $feed->count();
        for ($i = 0; $i < $feedCount; $i++) {
            $item = $feed[$i];
            $result->push($item);

            // increment currentTime by visit duration
            $durationHours = $this->extractDurationHours($item['data']) ?? $defaultVisitHours;
            $currentTime->modify('+' . round($durationHours * 60) . ' minutes');

            // if there is a next item, estimate travel time to next
            $nextItem = ($i + 1 < $feedCount) ? $feed[$i + 1] : null;
            if ($nextItem) {
                $distKm = $this->calculateDistance($item['lat'], $item['lng'], $nextItem['lat'], $nextItem['lng']);
                $travelMinutes = ($avgSpeedKmh > 0) ? ($distKm / $avgSpeedKmh * 60) : 15;
            } else {
                $distKm = 0;
                $travelMinutes = 0;
            }

            // check meal window for currentTime
            $hourDecimal = (float)$currentTime->format('H') + ((float)$currentTime->format('i') / 60.0);
            $inLunchWindow = ($hourDecimal >= $lunchStart && $hourDecimal <= $lunchEnd);
            $inDinnerWindow = ($hourDecimal >= $dinnerStart && $hourDecimal <= $dinnerEnd);

            if ($inLunchWindow || $inDinnerWindow) {
                // search nearby restaurants around current item and/or between current and next
                $maxSearchKm = 2.0; // look within 2km
                $candidatesNearby = $candidateRestaurants->filter(function($r) use ($item, $nextItem, $maxSearchKm) {
                    $d1 = $this->calculateDistance($item['lat'], $item['lng'], $r->Latitude, $r->Longitude);
                    if ($d1 <= $maxSearchKm) return true;
                    if ($nextItem) {
                        // also consider restaurants near segment mid-point
                        $d2 = $this->calculateDistance($nextItem['lat'], $nextItem['lng'], $r->Latitude, $r->Longitude);
                        return ($d2 <= $maxSearchKm);
                    }
                    return false;
                });

                // filter by quality threshold (must be reasonably popular)
                $candidatesNearby = $candidatesNearby->filter(function($r) {
                    $rating = isset($r->Averagerating) ? (float)$r->Averagerating : 0.0;
                    $rc = isset($r->ReviewCount) ? (int)$r->ReviewCount : 0;
                    // pass if high rating+count OR flagged IsMustSee OR popularity index set
                    if ($rating >= $this->restaurant_rating_min && $rc >= $this->restaurant_count_min) return true;
                    if (isset($r->IsMustSee) && (int)$r->IsMustSee === 1) return true;
                    if (isset($r->PopularityIndex) && (int)$r->PopularityIndex >= 80) return true;
                    // otherwise skip
                    return false;
                });

                // rank candidates by pop_score minus detour penalty
                $ranked = $candidatesNearby->map(function($r) use ($item, $nextItem, $avgSpeedKmh) {
                    $detourKm = $this->calculateDistance($item['lat'], $item['lng'], $r->Latitude, $r->Longitude);
                    // if nextItem exists, consider detour from route segment midpoint
                    if ($nextItem) {
                        $midLat = ($item['lat'] + $nextItem['lat']) / 2.0;
                        $midLng = ($item['lng'] + $nextItem['lng']) / 2.0;
                        $detourKm = min($detourKm, $this->calculateDistance($midLat, $midLng, $r->Latitude, $r->Longitude));
                    }
                    $penalty = $this->alpha * ($detourKm / $this->distanceScaleKm);
                    $obj = (isset($r->pop_score) ? $r->pop_score : 0.0) - $penalty;
                    return ['restaurant' => $r, 'obj' => $obj, 'detourKm' => $detourKm];
                })->sortByDesc(function($val) { return $val['obj']; });

                // pick top candidate if above threshold
                if ($ranked->isNotEmpty()) {
                    $best = $ranked->first();
                    if ($best['obj'] >= 0.15) {
                        // insert restaurant into result
                        $rest = $best['restaurant'];
                        $insertItem = [
                            'type' => 'restaurant',
                            'id' => $rest->RestaurantId ?? ('rest_' . uniqid()),
                            'lat' => (float)$rest->Latitude,
                            'lng' => (float)$rest->Longitude,
                            'tier' => $rest->tier ?? 3,
                            'data' => $rest
                        ];
                        $result->push($insertItem);

                        // remove chosen restaurant from pool so it is not chosen again
                        $candidateRestaurants = $candidateRestaurants->reject(function($r) use ($rest) {
                            return ($r->RestaurantId ?? null) === ($rest->RestaurantId ?? null);
                        });

                        // increment current time by average meal time (assume 1 hour) + a small travel buffer
                        $currentTime->modify('+60 minutes');
                        $currentTime->modify('+' . ceil($best['detourKm'] / max(1,$avgSpeedKmh) * 60) . ' minutes');
                    }
                }
            }

            // finally add travel time from this item to next
            if ($nextItem) $currentTime->modify('+' . ceil($travelMinutes) . ' minutes');
        }

        return $result;
    }

    private function extractDurationHours($data)
    {
        // If database has a duration column, try to parse numeric hours. Accept "2h", "90m", "2:30", "2 hours" naive parsing
        if (isset($data['data']) && is_object($data['data'])) $d = $data['data'];
        else $d = $data;

        if (isset($d->duration) && !empty($d->duration)) {
            $raw = strtolower((string)$d->duration);
            // "2h", "2 hr", "2 hours"
            if (preg_match('/(\d+(?:\.\d+)?)\s*h/', $raw, $m)) {
                return (float)$m[1];
            }
            // "90m"
            if (preg_match('/(\d+(?:\.\d+)?)\s*m/', $raw, $m)) {
                return ((float)$m[1]) / 60.0;
            }
            // "2:30" -> 2.5
            if (preg_match('/^(\d+):(\d+)/', $raw, $m)) {
                return (float)$m[1] + ((float)$m[2] / 60.0);
            }
        }
        // fallback: look for Duration or Duration in Experience
        if (isset($d->Duration) && !empty($d->Duration)) {
            $raw = strtolower((string)$d->Duration);
            if (preg_match('/(\d+(?:\.\d+)?)\s*h/', $raw, $m)) return (float)$m[1];
            if (preg_match('/(\d+(?:\.\d+)?)\s*m/', $raw, $m)) return ((float)$m[1]) / 60.0;
        }

        return null;
    }

    private function parseStartTimeOrDefault($startTime = null): \DateTime
    {
        try {
            if ($startTime) {
                return new \DateTime($startTime);
            }
        } catch (\Exception $e) {
            // ignore and fallback
        }
        // default to 11:00 local (date irrelevant)
        $dt = new \DateTime();
        $dt->setTime(11, 0, 0);
        return $dt;
    }

    /**
     * Format the itinerary to match your frontend expectations
     */
    private function formatItinerary(Collection $itinerary): array
    {
        $result = [];
        foreach ($itinerary as $item) {
            $data = $item['data'];
            if ($item['type'] === 'sight') {
                $sight = (array)$data;
                $sight['visited'] = true;
                $sight['MustSee'] = isset($sight['MustSee']) && $sight['MustSee'] == 1 ? 1 : 0;
                $sight['IsMustSee'] = $sight['MustSee'] ?? 0;
                // ensure SightId exists
                if (!isset($sight['SightId']) && isset($item['id'])) $sight['SightId'] = $item['id'];
                $result[] = $sight;
            } elseif ($item['type'] === 'experience') {
                $experience = (array)$data;
                $experience['visited'] = true;
                $experience['MustSee'] = 0;
                $experience['IsMustSee'] = 0;
                $experience['SightId'] = isset($experience['ExperienceId']) ? 'exp_' . $experience['ExperienceId'] : ('exp_' . ($item['id'] ?? uniqid()));
                $result[] = $experience;
            } elseif ($item['type'] === 'restaurant') {
                $restaurant = (array)$data;
                $restaurant['visited'] = true;
                $restaurant['MustSee'] = 0;
                $restaurant['IsMustSee'] = 0;
                $restaurant['SightId'] = isset($restaurant['RestaurantId']) ? 'rest_' . $restaurant['RestaurantId'] : ('rest_' . ($item['id'] ?? uniqid()));
                $result[] = $restaurant;
            } else {
                // fallback: raw data
                $obj = (array)$data;
                $obj['visited'] = true;
                $result[] = $obj;
            }
        }
        return $result;
    }

    // ---------------- Database fetchers: same signatures as yours, preserved ----------------
    public function getSights($locationId = null, $limit = 30, $tier = null, $categoryIds = null): array
    {
        $query = DB::table('Sight as s')
            ->select(
                's.SightId', 's.Title', 's.Latitude', 's.Longitude',
                's.ReviewCount', 's.Averagerating', 's.tier',
                DB::raw('CASE WHEN s.IsMustSee = 1 THEN 1 ELSE 0 END as IsMustSee'),
                's.LocationId', 's.Slug', 's.IsRestaurant', 's.Address', 's.CategoryId',
                's.MicroSummary', DB::raw("'attraction' as type"), 's.popularity_score',
                's.duration', 's.KnownFor', 's.Award', 's.WeightedRating', 's.MustSee'
            )
            ->whereNotNull('s.Latitude')
            ->whereNotNull('s.Longitude');

        if ($locationId) $query->where('s.LocationId', $locationId);

        if ($tier !== null) {
            if (is_array($tier)) $query->whereIn('s.tier', $tier);
            else $query->where('s.tier', $tier);
        }

        if ($categoryIds !== null && !empty($categoryIds)) {
            $query->whereIn('s.CategoryId', $categoryIds);
            Log::info('ItineraryGenerator: Applied category filtering to getSights', ['categoryIds' => $categoryIds]);
        }

        $query->orderBy('s.IsMustSee', 'asc')
              ->orderBy('s.ReviewCount', 'desc');

        if ($limit) $query->limit($limit);

        return $query->get()->toArray();
    }

    public function getRestaurants($locationId = null, $limit = 15, $tier = null): array
    {
        $query = DB::table('Restaurant as r')
            ->select(
                'r.RestaurantId as RestaurantId', 'r.Title', 'r.Latitude', 'r.Longitude',
                'r.ReviewCount', 'r.Averagerating', 'r.tier', 'r.LocationId', 'r.slugid',
                'r.Slug', 'r.Timings', 'r.PriceRange', 'r.category', 'r.features',
                'r.Address', 'r.IsMustSee', 'r.PopularityIndex', DB::raw("'restaurant' as type"),
                'r.cuisines', 'r.meals', 'r.features', 'r.Timings'
            )
            ->whereNotNull('r.Latitude')
            ->whereNotNull('r.Longitude');

        if ($locationId) $query->where('r.LocationId', $locationId);

        if ($tier !== null) {
            if (is_array($tier)) $query->whereIn('r.tier', $tier);
            else $query->where('r.tier', $tier);
        }

        $query->orderBy('r.tier', 'asc')
              ->orderBy('r.Averagerating', 'desc')
              ->orderBy('r.ReviewCount', 'desc');

        if ($limit) $query->limit($limit);

        return $query->get()->toArray();
    }

    public function getExperiences($locationId = null, $limit = 15, $tier = null): array
    {
        $query = DB::table('Experience as e')
            ->select(
                'e.ExperienceId as ExperienceId', 'e.Name as Title', 'e.Latitude', 'e.Longitude',
                'e.ViatorReviewCount as ReviewCount', 'e.ViatorAggregationRating as Averagerating',
                'e.tier', 'e.LocationId', 'e.slugid', 'e.Slug', 'e.popularity_score',
                DB::raw("'experience' as type"), 'e.Duration as Duration', 'e.Exclusive'
            )
            ->whereNotNull('e.Latitude')
            ->whereNotNull('e.Longitude');

        if ($locationId) $query->where('e.LocationId', $locationId);

        if ($tier !== null) {
            if (is_array($tier)) $query->whereIn('e.tier', $tier);
            else $query->where('e.tier', $tier);
        }

        $query->orderBy('e.tier', 'asc')
              ->orderBy('e.ViatorAggregationRating', 'desc')
              ->orderBy('e.ViatorReviewCount', 'desc');

        if ($limit) $query->limit($limit);

        return $query->get()->toArray();
    }

    // ---------------- Distance helpers ----------------
    // Haversine distance in km (cached)
    private function calculateDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $cacheKey = json_encode([$lat1, $lng1, $lat2, $lng2]);
        if (isset($this->distanceCache[$cacheKey])) return $this->distanceCache[$cacheKey];

        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        $this->distanceCache[$cacheKey] = $distance;
        return $distance;
    }

    /**
     * Useful helper: get city's max review count among sights/restaurants/experiences for normalization
     */
    private function getCityMaxReviewCount($locationId)
    {
        try {
            // Check Sight
            $maxSight = DB::table('Sight')->where('LocationId', $locationId)->max('ReviewCount');
            $maxRest = DB::table('Restaurant')->where('LocationId', $locationId)->max('ReviewCount');
            $maxExp = DB::table('Experience')->where('LocationId', $locationId)->max('ViatorReviewCount');

            $max = max((int)$maxSight, (int)$maxRest, (int)$maxExp, 1);
            return $max;
        } catch (\Exception $e) {
            Log::warning('getCityMaxReviewCount failed: ' . $e->getMessage());
            return 1000;
        }
    }
}
