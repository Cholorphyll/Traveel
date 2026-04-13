<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 4 — Opportunity Engine
 *
 * Detects time-windowed opportunities: sunset window, meal windows,
 * nightlife windows, weather windows, crowd advantages, limited access.
 *
 * Output fields added per candidate (all 0-1):
 *   opportunity_score             overall opportunity strength
 *   sunset_window_score
 *   meal_window_score
 *   nightlife_window_score
 *   weather_window_score
 *   limited_access_window_score
 *   crowd_advantage_window_score
 *   window_strength_score         best single window
 *   time_sensitivity_score        how urgent the window is
 *   window_uniqueness_score       how rare this kind of window is
 *   reachability_before_window_end  0-1
 *   closing_soon_flag             bool
 */
class OpportunityEngine
{
    public function run(Collection $candidates, array $ctx): Collection
    {
        $minToSunset  = (int)($ctx['minutes_to_sunset']    ?? 999);
        $daypart      = $ctx['daypart']                    ?? 'afternoon';
        $weatherType  = $ctx['weather_type']               ?? 'clear';
        $thermalScore = (float)($ctx['thermal_comfort_score'] ?? 70);
        $sunsetQuality= (float)($ctx['sunset_quality']     ?? 0.5);
        $hungerState  = $ctx['user_hunger_state']          ?? 'normal';
        $rainDisc     = (float)($ctx['rain_discomfort_level'] ?? 0);

        $isSunsetWindow  = $minToSunset > 0 && $minToSunset <= 90;
        $isGoldenHour    = $daypart === 'golden_hour';
        $isMealTime      = $ctx['is_meal_time']    ?? false;
        $isLunchWindow   = in_array($daypart, ['midday']);
        $isDinnerWindow  = in_array($daypart, ['evening']);
        $isNightWindow   = in_array($daypart, ['evening','night']);

        return $candidates->map(function (array $c) use (
            $minToSunset, $daypart, $weatherType, $thermalScore, $sunsetQuality,
            $hungerState, $rainDisc, $isSunsetWindow, $isGoldenHour,
            $isMealTime, $isLunchWindow, $isDinnerWindow, $isNightWindow
        ) {
            if (!$c['is_eligible']) return $c;

            // ── Sunset window ─────────────────────────────────────────────────
            $sunsetScore = 0.0;
            if ($c['is_scenic'] || $c['is_outdoor']) {
                if ($isGoldenHour)                                   $sunsetScore = $sunsetQuality;
                elseif ($isSunsetWindow && $minToSunset <= 60)      $sunsetScore = 0.85 * $sunsetQuality;
                elseif ($isSunsetWindow)                            $sunsetScore = 0.55 * $sunsetQuality;
            }

            // ── Meal window ───────────────────────────────────────────────────
            $mealScore = 0.0;
            if ($c['is_restaurant']) {
                if ($isLunchWindow && ($hungerState === 'hungry' || $isMealTime)) $mealScore = 1.0;
                elseif ($isLunchWindow)                                            $mealScore = 0.70;
                elseif ($isDinnerWindow)                                           $mealScore = 0.85;
                elseif ($isMealTime)                                               $mealScore = 0.65;
                else                                                               $mealScore = 0.30;
            }

            // ── Nightlife window ──────────────────────────────────────────────
            $nightlifeScore = 0.0;
            if ($c['is_nightlife'] || ($c['is_restaurant'] && $isNightWindow)) {
                $nightlifeScore = $isNightWindow ? 0.90 : 0.20;
            }

            // ── Weather window ────────────────────────────────────────────────
            // Items that are unusually good because of current weather
            $weatherScore = 0.0;
            if ($weatherType === 'clear' && $c['is_outdoor']) {
                $weatherScore = min(1.0, $thermalScore / 100 * 0.9);
            } elseif (in_array($weatherType, ['rain','storm']) && !$c['is_outdoor']) {
                $weatherScore = 0.85; // indoor items shine in bad weather
            }

            // ── Limited access window ─────────────────────────────────────────
            // Tier-1 items with implied scarcity (crowds, capacity)
            $limitedScore = 0.0;
            if ($c['tier'] === 1 && $c['is_must_see']) {
                $limitedScore = 0.70;
            }

            // ── Crowd advantage ───────────────────────────────────────────────
            // Morning slots at iconic sites = lower crowds
            $crowdScore = 0.0;
            if ($c['tier'] <= 2 && in_array($daypart, ['early_morning','morning'])) {
                $crowdScore = 0.75;
            }

            // ── Aggregates ────────────────────────────────────────────────────
            $windowStrength = max($sunsetScore, $mealScore, $nightlifeScore, $weatherScore, $limitedScore, $crowdScore);

            $timeSensitivity = 0.0;
            if ($isGoldenHour)                         $timeSensitivity = 1.0;
            elseif ($isSunsetWindow && $sunsetScore > 0.5) $timeSensitivity = 0.85;
            elseif ($isLunchWindow || $isDinnerWindow)  $timeSensitivity = 0.70;

            $windowUniqueness = $c['tier'] === 1 ? 0.85 : ($c['is_scenic'] ? 0.65 : 0.40);

            // Reachability: simplified — if candidate is within 30 min travel, full reachability
            $distKm = (float)($c['distance_km'] ?? 5.0);
            $reachability = match (true) {
                $distKm <= 2  => 1.00,
                $distKm <= 5  => 0.80,
                $distKm <= 10 => 0.55,
                $distKm <= 20 => 0.30,
                default       => 0.10,
            };

            // Overall opportunity score
            $oppScore = round(
                0.30 * $windowStrength    +
                0.25 * $timeSensitivity   +
                0.20 * $reachability      +
                0.15 * $windowUniqueness  +
                0.10 * min(1.0, ($c['structural_importance_score'] ?? 0)) ,
            4);

            $c['opportunity_score']              = min(1.0, $oppScore);
            $c['sunset_window_score']            = $sunsetScore;
            $c['meal_window_score']              = $mealScore;
            $c['nightlife_window_score']         = $nightlifeScore;
            $c['weather_window_score']           = $weatherScore;
            $c['limited_access_window_score']    = $limitedScore;
            $c['crowd_advantage_window_score']   = $crowdScore;
            $c['window_strength_score']          = $windowStrength;
            $c['time_sensitivity_score']         = $timeSensitivity;
            $c['window_uniqueness_score']        = $windowUniqueness;
            $c['reachability_before_window_end'] = $reachability;
            $c['closing_soon_flag']              = $timeSensitivity >= 0.85;
            $c['service_window_active']          = $mealScore >= 0.65 || $nightlifeScore >= 0.65;

            // Convenience scores used by Role Assignment
            $c['open_now_score']                 = $this->openNowScore($c, $daypart);
            $c['late_open_score']                = $c['is_nightlife'] ? 0.9 : ($c['is_restaurant'] ? 0.6 : 0.2);
            $c['time_of_day_visual_fit']         = $this->visualTimeFit($c, $daypart);
            $c['weather_visual_bonus']           = ($c['is_scenic'] && $weatherType === 'clear') ? 0.8 : 0.2;
            $c['opening_window_future_fit']      = min(1.0, ($c['tier'] <= 2 ? 0.7 : 0.4));

            return $c;
        });
    }

    private function openNowScore(array $c, string $daypart): float
    {
        // Approximate: restaurants are open midday+evening, sights all day
        if ($c['is_restaurant']) {
            return in_array($daypart, ['midday','afternoon','golden_hour','evening','night']) ? 0.90 : 0.40;
        }
        return in_array($daypart, ['night']) ? 0.30 : 0.80;
    }

    private function visualTimeFit(array $c, string $daypart): float
    {
        if ($c['is_scenic']) {
            return match ($daypart) {
                'golden_hour'  => 1.00,
                'early_morning'=> 0.85,
                'afternoon'    => 0.65,
                'morning'      => 0.70,
                default        => 0.35,
            };
        }
        return 0.50;
    }
}
