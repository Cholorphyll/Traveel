<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 5 — Action Horizon Engine
 *
 * Classifies candidates across four temporal horizons:
 *   do_now_score   — should be done right now this session
 *   do_soon_score  — within today / next few hours
 *   do_later_score — later this trip
 *   next_day_score — best left for a different day
 *
 * Also computes: open_now_score (used by Role Assignment / vw_role_assignment_inputs)
 *
 * All scores normalised 0-1.
 */
class ActionHorizonEngine
{
    public function run(Collection $candidates, array $ctx): Collection
    {
        $daypart       = $ctx['daypart']            ?? 'afternoon';
        $minToSunset   = (int)($ctx['minutes_to_sunset'] ?? 999);
        $tripDayIndex  = (int)($ctx['trip_day_index']    ?? 1);
        $fatigueState  = $ctx['user_fatigue_state']      ?? 'fresh';
        $hungerState   = $ctx['user_hunger_state']       ?? 'normal';
        $weatherType   = $ctx['weather_type']            ?? 'clear';
        $rainDisc      = (float)($ctx['rain_discomfort_level'] ?? 0);
        $thermalScore  = (float)($ctx['thermal_comfort_score'] ?? 70);

        $isGoldenHour  = $daypart === 'golden_hour';
        $isMealTime    = $ctx['is_meal_time'] ?? false;
        $isNight       = in_array($daypart, ['evening', 'night']);

        return $candidates->map(function (array $c) use (
            $daypart, $minToSunset, $tripDayIndex, $fatigueState, $hungerState,
            $weatherType, $rainDisc, $thermalScore, $isGoldenHour, $isMealTime, $isNight
        ) {
            if (!$c['is_eligible']) return $c;

            $tier         = (int)($c['tier'] ?? 4);
            $opNow        = (float)($c['open_now_score']    ?? 0.5);
            $timeWindowed = (float)($c['time_sensitivity_score'] ?? 0);
            $windowStr    = (float)($c['window_strength_score'] ?? 0);
            $opportunityS = (float)($c['opportunity_score'] ?? 0);
            $distKm       = (float)($c['distance_km'] ?? 5.0);
            $duration     = (int)($c['duration_minutes'] ?? 60);

            // ── Immediate accessibility (from spec 6.5.1) ─────────────────────
            $immediateAccess = $this->immediateAccessScore($distKm);

            // ── Accessibility score (from spec 6.5.2) ─────────────────────────
            $accessScore = $this->accessibilityScore($distKm);

            // ── DO NOW score ──────────────────────────────────────────────────
            // Items you should act on right now: open, reachable, time-sensitive
            $doNowComponents = [
                0.30 * $immediateAccess,
                0.25 * $opNow,
                0.20 * $timeWindowed,
                0.15 * $this->weatherImmediateFit($c, $weatherType, $rainDisc, $thermalScore),
                0.10 * ($c['is_must_see'] ? 0.9 : ($tier === 1 ? 0.7 : 0.4)),
            ];
            $doNow = round(array_sum($doNowComponents), 4);

            // Boost: golden-hour scenic items
            if ($isGoldenHour && $c['is_scenic'])    $doNow = min(1.0, $doNow * 1.30);
            // Boost: meal-time restaurants
            if ($isMealTime && $c['is_restaurant'])  $doNow = min(1.0, $doNow * 1.25);
            // Reduce: high-commitment when tired
            if ($fatigueState === 'tired' && $c['is_high_commitment']) $doNow *= 0.50;

            // ── DO SOON score ─────────────────────────────────────────────────
            // Items that make sense within the next 2-4 hours
            $doSoonComponents = [
                0.30 * $accessScore,
                0.25 * min(1.0, ($c['trip_importance_score'] ?? 0)),
                0.20 * $opNow,
                0.15 * min(1.0, ($c['context_relevance_score'] ?? 0)),
                0.10 * $this->futureWeatherFit($c, $weatherType),
            ];
            $doSoon = round(array_sum($doSoonComponents), 4);

            // Boost for do-soon if it's a "can't miss today" landmark
            if ($tier === 1 && $tripDayIndex === 1)  $doSoon = min(1.0, $doSoon * 1.15);

            // ── DO LATER score ────────────────────────────────────────────────
            // Items that can wait until later today or tomorrow
            $doLater = round(
                0.40 * (1.0 - $doNow)   +
                0.30 * (1.0 - $timeWindowed) +
                0.30 * min(1.0, ($c['structural_importance_score'] ?? 0)) ,
            4);

            // ── NEXT DAY score ────────────────────────────────────────────────
            // Best for a different day entirely
            $nextDayComponents = [
                0.35 * (1.0 - $doNow),
                0.25 * ($c['is_high_commitment'] ? 0.8 : 0.3),
                0.25 * (1.0 - $opNow),
                0.15 * ($tripDayIndex > 1 ? 0.3 : 0.8),
            ];
            $nextDay = round(array_sum($nextDayComponents), 4);

            // Night items are not "next day"
            if ($isNight && $c['is_nightlife']) $nextDay *= 0.10;

            $c['do_now_score']                 = min(1.0, $doNow);
            $c['do_soon_score']                = min(1.0, $doSoon);
            $c['do_later_score']               = min(1.0, $doLater);
            $c['next_day_score']               = min(1.0, $nextDay);
            $c['immediate_accessibility_score']= $immediateAccess;
            $c['ease_of_access']               = $accessScore;

            // Seat / shade / calm signals (role assignment needs them)
            $c['seat_likelihood_score']        = $c['is_restaurant'] ? 0.9 : ($c['is_outdoor'] ? 0.3 : 0.6);
            $c['shade_score']                  = $c['is_outdoor'] ? 0.3 : 0.8;
            $c['calmness_score']               = in_array($tier, [1,2]) ? 0.4 : 0.7;
            $c['low_effort_access_score']      = $immediateAccess;
            $c['impulse_stop_score']           = $c['is_quick_stop'] ? 0.85 : 0.25;
            $c['logistics_feasibility']        = min(1.0, ($opNow * 0.6 + $accessScore * 0.4));
            $c['need_state_match']             = max(
                $c['meal_fit_score']      ?? 0,
                $c['recovery_fit_score']  ?? 0,
                $c['scenic_fit_score']    ?? 0
            );
            $c['current_comfort_fit']          = min(1.0, $thermalScore / 100 * ($c['is_outdoor'] ? 1.0 : 0.7) + 0.3);
            $c['ease_of_choice']               = $tier <= 2 ? 0.85 : ($tier === 3 ? 0.55 : 0.35);

            return $c;
        });
    }

    // ── Spec formula 6.5.1 ───────────────────────────────────────────────────
    private function immediateAccessScore(float $distKm): float
    {
        $travelMin = $distKm * 3; // rough 20km/h in-city estimate
        if ($travelMin <= 20) return 1.00;
        if ($travelMin <= 35) return 0.75;
        if ($travelMin <= 50) return 0.45;
        return 0.10;
    }

    // ── Spec formula 6.5.2 ───────────────────────────────────────────────────
    private function accessibilityScore(float $distKm): float
    {
        $travelMin = $distKm * 3;
        if ($travelMin <= 25) return 1.00;
        if ($travelMin <= 40) return 0.70;
        if ($travelMin <= 60) return 0.35;
        return 0.10;
    }

    private function weatherImmediateFit(array $c, string $weather, float $rainDisc, float $thermal): float
    {
        if ($c['is_outdoor']) {
            if ($weather === 'storm') return 0.0;
            if ($rainDisc >= 0.6)     return 0.2;
            return min(1.0, $thermal / 100);
        }
        return ($weather === 'storm' || $rainDisc >= 0.6) ? 0.95 : 0.70;
    }

    private function futureWeatherFit(array $c, string $weather): float
    {
        if ($c['is_outdoor'] && in_array($weather, ['storm','rain'])) return 0.3;
        return 0.7;
    }
}
