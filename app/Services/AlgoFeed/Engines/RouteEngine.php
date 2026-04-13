<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 7 — Route Engine
 *
 * Computes proximity & route-fit scores for every candidate
 * relative to the user's current position (or location centroid).
 *
 * Adds to each candidate:
 *   distance_km                 float  straight-line km to user
 *   travel_time_minutes         float  estimated travel time
 *   detour_cost_minutes         float  extra time beyond direct path
 *   proximity_score             0-1
 *   route_fit                   0-1    directional coherence
 *   direction_alignment_score   0-1
 *   same_area_cluster_score     0-1
 *   next_leg_alignment_score    0-1
 *   forward_route_fit           0-1
 *   route_alignment_score       0-1
 *   route_convenience_score     0-1
 *   midday_proximity_score      0-1
 *   short_distance_score        0-1
 *   low_detour_score            0-1    (spec formula 6.5.3)
 */
class RouteEngine
{
    private const EARTH_RADIUS_KM = 6371;

    public function run(Collection $candidates, array $ctx): Collection
    {
        $userLat    = (float)($ctx['user_lat']  ?? 0);
        $userLon    = (float)($ctx['user_lon']  ?? 0);
        $anchorLat  = (float)($ctx['anchor_lat'] ?? $userLat);
        $anchorLon  = (float)($ctx['anchor_lon'] ?? $userLon);
        $nextAncLat = (float)($ctx['next_anchor_lat'] ?? 0);
        $nextAncLon = (float)($ctx['next_anchor_lon'] ?? 0);
        $daypart    = $ctx['daypart'] ?? 'afternoon';

        // Use centroid if no user location
        if ($userLat == 0 || $userLon == 0) {
            [$userLat, $userLon] = $this->computeCentroid($candidates);
        }

        $hasNextAnchor = ($nextAncLat != 0 && $nextAncLon != 0);

        return $candidates->map(function (array $c) use (
            $userLat, $userLon, $anchorLat, $anchorLon,
            $nextAncLat, $nextAncLon, $hasNextAnchor, $daypart
        ) {
            if (!$c['is_eligible']) return $c;

            $lat = (float)$c['lat'];
            $lng = (float)$c['lng'];

            // ── Distance & travel time ────────────────────────────────────────
            $distKm      = $this->haversine($userLat, $userLon, $lat, $lng);
            $travelMin   = $this->estimateTravelTime($distKm);

            // ── Detour cost: extra time vs. going to anchor directly ──────────
            $directToAnchor   = $this->estimateTravelTime($this->haversine($userLat, $userLon, $anchorLat, $anchorLon));
            $viaCandidate     = $travelMin + $this->estimateTravelTime($this->haversine($lat, $lng, $anchorLat, $anchorLon));
            $detourCostMin    = max(0, $viaCandidate - $directToAnchor);

            // ── Proximity score ───────────────────────────────────────────────
            $proximityScore = match (true) {
                $distKm <= 0.5 => 1.00,
                $distKm <= 1.5 => 0.85,
                $distKm <= 3.0 => 0.70,
                $distKm <= 6.0 => 0.50,
                $distKm <= 12  => 0.30,
                $distKm <= 25  => 0.15,
                default        => 0.05,
            };

            // ── Low detour score (spec 6.5.3) ─────────────────────────────────
            $lowDetour = match (true) {
                $detourCostMin <= 5  => 1.00,
                $detourCostMin <= 10 => 0.75,
                $detourCostMin <= 15 => 0.45,
                default              => 0.10,
            };

            // ── Short distance score ──────────────────────────────────────────
            $shortDist = match (true) {
                $distKm <= 1  => 1.00,
                $distKm <= 3  => 0.75,
                $distKm <= 6  => 0.45,
                default       => 0.10,
            };

            // ── Direction alignment (bearing coherence) ───────────────────────
            $dirAlign = 0.5;
            if ($hasNextAnchor) {
                $bearingToNext  = $this->bearing($userLat, $userLon, $nextAncLat, $nextAncLon);
                $bearingToItem  = $this->bearing($userLat, $userLon, $lat, $lng);
                $angleDiff      = abs($bearingToNext - $bearingToItem);
                if ($angleDiff > 180) $angleDiff = 360 - $angleDiff;
                $dirAlign = max(0.0, 1.0 - ($angleDiff / 180.0));
            }

            // ── Next-leg alignment ────────────────────────────────────────────
            $nextLegAlign = $hasNextAnchor
                ? max(0.0, 1.0 - ($this->haversine($lat, $lng, $nextAncLat, $nextAncLon) / max(1, $this->haversine($userLat, $userLon, $nextAncLat, $nextAncLon))))
                : 0.5;

            // ── Same-area cluster (approximate: very close to user) ───────────
            $sameAreaCluster = $distKm <= 2.0 ? min(1.0, 1.0 - $distKm / 2.0) : 0.0;

            // ── Forward route fit ─────────────────────────────────────────────
            $forwardFit = $hasNextAnchor
                ? min(1.0, ($dirAlign * 0.6 + $nextLegAlign * 0.4))
                : $proximityScore;

            // ── Route alignment ───────────────────────────────────────────────
            $routeAlign = round(0.40 * $dirAlign + 0.30 * $lowDetour + 0.30 * $nextLegAlign, 4);

            // ── Route convenience ─────────────────────────────────────────────
            $routeConvenience = round(0.40 * $lowDetour + 0.35 * $proximityScore + 0.25 * ($c['open_now_score'] ?? 0.5), 4);

            // ── Midday proximity (used in lunch role) ─────────────────────────
            $middayProx = in_array($daypart, ['midday','afternoon']) ? $proximityScore : $proximityScore * 0.5;

            // ── Route fit (master) ────────────────────────────────────────────
            $routeFit = round(
                0.35 * $proximityScore  +
                0.25 * $routeAlign      +
                0.20 * $lowDetour       +
                0.20 * $forwardFit      ,
            4);

            $c['distance_km']              = round($distKm, 4);
            $c['distance_meters']          = (int)($distKm * 1000);
            $c['travel_time_minutes']      = round($travelMin, 2);
            $c['detour_cost_minutes']      = round($detourCostMin, 2);
            $c['proximity_score']          = $proximityScore;
            $c['route_fit']                = min(1.0, $routeFit);
            $c['direction_alignment_score']= round($dirAlign, 4);
            $c['same_area_cluster_score']  = $sameAreaCluster;
            $c['next_leg_alignment_score'] = round($nextLegAlign, 4);
            $c['forward_route_fit']        = round($forwardFit, 4);
            $c['route_alignment_score']    = min(1.0, $routeAlign);
            $c['route_convenience_score']  = min(1.0, $routeConvenience);
            $c['midday_proximity_score']   = round($middayProx, 4);
            $c['short_distance_score']     = $shortDist;
            $c['low_detour_score']         = $lowDetour;

            return $c;
        });
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 9999.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        return self::EARTH_RADIUS_KM * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function bearing(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dLon = deg2rad($lon2 - $lon1);
        $y = sin($dLon) * cos(deg2rad($lat2));
        $x = cos(deg2rad($lat1)) * sin(deg2rad($lat2)) - sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos($dLon);
        return fmod(rad2deg(atan2($y, $x)) + 360, 360);
    }

    private function estimateTravelTime(float $distKm): float
    {
        // In-city walking/transit estimate: ~20km/h average
        return round(($distKm / 20) * 60, 2);
    }

    private function computeCentroid(Collection $candidates): array
    {
        $eligible = $candidates->where('is_eligible', true)->filter(fn($c) => $c['lat'] != 0);
        if ($eligible->isEmpty()) return [0.0, 0.0];
        return [(float)$eligible->avg('lat'), (float)$eligible->avg('lng')];
    }
}
