<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 2 — Context Engine
 *
 * Computes context fit scores for every candidate, mapping current
 * session conditions (time, weather, energy, movement) onto items.
 *
 * Output fields added to each candidate:
 *   context_relevance_score  0-1  overall context fit
 *   movement_fit_score       0-1
 *   pace_fit_score           0-1
 *   energy_fit_score         0-1
 *   weather_fit_score        0-1
 *   social_fit_score         0-1
 *   mood_fit_score           0-1
 *   meal_fit_score           0-1
 *   recovery_fit_score       0-1
 *   scenic_fit_score         0-1
 *   night_fit_score          0-1
 *   transition_fit_score     0-1
 *   discovery_fit_score      0-1
 */
class ContextEngine
{
    public function run(Collection $candidates, array $ctx): Collection
    {
        $daypart      = $ctx['daypart']           ?? 'afternoon';
        $weatherType  = $ctx['weather_type']      ?? 'clear';
        $thermalScore = (float)($ctx['thermal_comfort_score'] ?? 70);
        $heatStress   = $ctx['heat_stress_level'] ?? 'low';
        $rainDisc     = (float)($ctx['rain_discomfort_level'] ?? 0);
        $fatigueState = $ctx['user_fatigue_state']    ?? 'fresh';
        $hungerState  = $ctx['user_hunger_state']      ?? 'normal';
        $energyState  = $ctx['session_energy_state']   ?? 'active';
        $isGolden     = ($daypart === 'golden_hour');
        $isNight      = in_array($daypart, ['evening', 'night']);
        $isMealTime   = $ctx['is_meal_time'] ?? false;

        return $candidates->map(function (array $c) use (
            $daypart, $weatherType, $thermalScore, $heatStress, $rainDisc,
            $fatigueState, $hungerState, $energyState, $isGolden, $isNight, $isMealTime
        ) {
            if (!$c['is_eligible']) return $c;

            // ── Weather fit ───────────────────────────────────────────────────
            $weatherFit = $this->computeWeatherFit($c, $weatherType, $rainDisc, $heatStress, $thermalScore);

            // ── Scenic fit ────────────────────────────────────────────────────
            $scenicFit = $this->computeScenicFit($c, $isGolden, $weatherType, $thermalScore);

            // ── Night fit ─────────────────────────────────────────────────────
            $nightFit = $this->computeNightFit($c, $isNight, $daypart);

            // ── Meal fit ──────────────────────────────────────────────────────
            $mealFit = $this->computeMealFit($c, $isMealTime, $hungerState, $daypart);

            // ── Recovery fit ──────────────────────────────────────────────────
            $recoveryFit = $this->computeRecoveryFit($c, $fatigueState);

            // ── Energy fit ────────────────────────────────────────────────────
            $energyFit = $this->computeEnergyFit($c, $energyState, $fatigueState);

            // ── Movement / pace fit ───────────────────────────────────────────
            $movementFit    = $c['is_quick_stop'] ? 0.8 : 0.5;
            $paceFit        = $fatigueState === 'tired' ? ($c['is_high_commitment'] ? 0.2 : 0.9) : 0.6;

            // ── Social fit ────────────────────────────────────────────────────
            $socialFit = $this->computeSocialFit($c, $daypart);

            // ── Mood fit ──────────────────────────────────────────────────────
            $moodFit = $this->computeMoodFit($c, $daypart, $energyState);

            // ── Transition fit ────────────────────────────────────────────────
            $transitionFit = ($c['is_quick_stop'] || $c['avg_rating'] >= 4.0) ? 0.7 : 0.4;

            // ── Discovery fit ─────────────────────────────────────────────────
            $discoveryFit = ($c['tier'] >= 3) ? 0.75 : 0.4;

            // ── Overall context relevance ─────────────────────────────────────
            $contextRelevance = round(
                0.25 * $weatherFit +
                0.15 * $energyFit  +
                0.15 * $mealFit    +
                0.12 * $recoveryFit+
                0.10 * $scenicFit  +
                0.08 * $nightFit   +
                0.08 * $movementFit+
                0.07 * $socialFit  ,
            4);

            $c['context_relevance_score'] = min(1.0, $contextRelevance);
            $c['movement_fit_score']      = $movementFit;
            $c['pace_fit_score']          = $paceFit;
            $c['energy_fit_score']        = $energyFit;
            $c['weather_fit_score']       = $weatherFit;
            $c['social_fit_score']        = $socialFit;
            $c['mood_fit_score']          = $moodFit;
            $c['meal_fit_score']          = $mealFit;
            $c['recovery_fit_score']      = $recoveryFit;
            $c['scenic_fit_score']        = $scenicFit;
            $c['night_fit_score']         = $nightFit;
            $c['transition_fit_score']    = $transitionFit;
            $c['discovery_fit_score']     = $discoveryFit;

            return $c;
        });
    }

    private function computeWeatherFit(array $c, string $weather, float $rainDisc, string $heat, float $thermal): float
    {
        // Outdoor items suffer in bad weather
        if ($c['is_outdoor']) {
            if ($weather === 'storm')   return 0.05;
            if ($rainDisc >= 0.6)       return 0.20;
            if ($rainDisc >= 0.3)       return 0.50;
            if (in_array($heat, ['high','extreme'])) return 0.40;
            return min(1.0, $thermal / 100);
        }
        // Indoor items are unaffected or slightly boosted by bad weather
        if ($weather === 'storm' || $rainDisc >= 0.6) return 0.95;
        return 0.70;
    }

    private function computeScenicFit(array $c, bool $isGolden, string $weather, float $thermal): float
    {
        if (!$c['is_scenic']) return 0.20;
        if ($isGolden && $weather === 'clear') return 1.00;
        if ($isGolden) return 0.80;
        if ($weather === 'clear') return 0.70;
        return 0.40;
    }

    private function computeNightFit(array $c, bool $isNight, string $daypart): float
    {
        if (!$isNight) {
            return $c['is_nightlife'] ? 0.20 : 0.50;
        }
        if ($c['is_nightlife'])  return 1.00;
        if ($c['is_restaurant']) return 0.85;
        if ($c['is_outdoor'] && !$c['is_scenic']) return 0.30;
        return 0.50;
    }

    private function computeMealFit(array $c, bool $isMealTime, string $hunger, string $daypart): float
    {
        if (!$c['is_restaurant']) return 0.10;
        if (!$isMealTime && $hunger === 'normal') return 0.40;
        if ($hunger === 'hungry' || $isMealTime) return 1.00;
        return 0.65;
    }

    private function computeRecoveryFit(array $c, string $fatigue): float
    {
        if ($fatigue === 'fresh')  return $c['is_high_commitment'] ? 0.60 : 0.50;
        if ($fatigue === 'tired') {
            if ($c['is_quick_stop'] && !$c['is_outdoor']) return 1.00;
            if ($c['is_restaurant'])                       return 0.85;
            if ($c['is_high_commitment'])                  return 0.10;
            return 0.55;
        }
        return 0.50;
    }

    private function computeEnergyFit(array $c, string $energy, string $fatigue): float
    {
        $high = $energy === 'active' && $fatigue === 'fresh';
        if ($high && $c['is_high_commitment']) return 0.90;
        if ($high)                             return 0.70;
        if ($c['is_quick_stop'])               return 0.80;
        if ($c['is_high_commitment'])          return 0.30;
        return 0.55;
    }

    private function computeSocialFit(array $c, string $daypart): float
    {
        $isEvening = in_array($daypart, ['evening', 'night', 'golden_hour']);
        if ($c['is_restaurant'] && $isEvening) return 0.90;
        if ($c['is_nightlife'])                return 0.95;
        if ($c['is_restaurant'])               return 0.65;
        return 0.45;
    }

    private function computeMoodFit(array $c, string $daypart, string $energy): float
    {
        if ($energy === 'active' && $c['is_high_commitment'])  return 0.80;
        if ($daypart === 'morning' && $c['is_scenic'])         return 0.85;
        if ($daypart === 'golden_hour' && $c['is_scenic'])     return 1.00;
        return 0.55;
    }
}
