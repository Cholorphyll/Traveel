<?php

namespace App\Services\AlgoFeed\Engines;

/**
 * Module 10 — Moment Framing Engine
 *
 * Converts ranked feed decisions into human-readable "why now" framing.
 * Each filled slot receives:
 *   moment_primary_family   string
 *   moment_primary_type     string
 *   moment_label_short      string   ≤ 4 words
 *   moment_label_medium     string   ≤ 10 words
 *   moment_urgency_level    float    0-1
 *   moment_confidence       float    0-1
 *   moment_cta_style        string
 */
class MomentFramingEngine
{
    // Moment families (spec 10.4)
    private const FAMILY_IMMEDIATE_ACTION    = 'IMMEDIATE_ACTION';
    private const FAMILY_OPPORTUNITY         = 'OPPORTUNITY';
    private const FAMILY_SOCIAL_ENERGY       = 'SOCIAL_ENERGY';
    private const FAMILY_NEED_STATE          = 'NEED_STATE';
    private const FAMILY_RECOVERY            = 'RECOVERY';
    private const FAMILY_DISCOVERY           = 'DISCOVERY';
    private const FAMILY_PLANNING_GUIDANCE   = 'PLANNING_GUIDANCE';
    private const FAMILY_TRANSITION          = 'TRANSITION';
    private const FAMILY_ANCHOR_REINFORCEMENT= 'ANCHOR_REINFORCEMENT';

    // Copy templates per moment type [short_label, medium_label, cta_style]
    private const COPY_TEMPLATES = [
        'RIGHT_HERE_RIGHT_NOW'     => ['Good right now',    'Easy stop from where you are',       'go_now'],
        'COOL_INDOOR_BREAK'        => ['Cool indoor break', 'Good indoor break nearby',            'step_in'],
        'BEST_BEFORE_SUNSET'       => ['Before sunset',     'Great stop before sunset',            'go_now'],
        'QUICK_BITE_NEARBY'        => ['Quick bite nearby', 'Easy food stop in this area',         'eat_here'],
        'WORTH_A_SHORT_RIDE'       => ['Worth the ride',    'Worth a short ride from here',        'head_over'],
        'SAVE_FOR_TOMORROW'        => ['Save for tomorrow', 'Better as a planned stop',            'save_it'],
        'GOLDEN_HOUR_SPOT'         => ['Golden hour spot',  'Best viewed in the next 30 minutes',  'go_now'],
        'ICONIC_MUST_DO'           => ['Iconic stop',       'A must-do for this destination',      'add_to_plan'],
        'GREAT_FOR_TONIGHT'        => ['Great tonight',     'Perfect for this evening',            'plan_tonight'],
        'HIDDEN_LOCAL_GEM'         => ['Local gem',         'A favourite among locals',            'discover'],
        'EASY_LUNCH_STOP'          => ['Easy lunch',        'Good casual lunch nearby',            'eat_here'],
        'BEST_DINNER_AREA'         => ['Dinner area',       'Great dinner options in this area',   'book_now'],
        'GOOD_NEXT_STOP'           => ['Good next stop',    'Fits well from where you are',        'head_over'],
        'BEST_ONCE_HEAT_DROPS'     => ['Better later',      'Best once the heat drops',            'save_it'],
        'GOOD_AREA_PICK'           => ['Good area pick',    'Popular in this area',                'explore'],
    ];

    public function frame(array $rankedSlots, array $ctx): array
    {
        $daypart      = $ctx['daypart']            ?? 'afternoon';
        $minToSunset  = (int)($ctx['minutes_to_sunset'] ?? 999);
        $weatherType  = $ctx['weather_type']       ?? 'clear';
        $heatStress   = $ctx['heat_stress_level']  ?? 'low';
        $fatigueState = $ctx['user_fatigue_state'] ?? 'fresh';
        $hungerState  = $ctx['user_hunger_state']  ?? 'normal';
        $isGolden     = $daypart === 'golden_hour';
        $isNight      = in_array($daypart, ['evening','night']);
        $isMealTime   = $ctx['is_meal_time'] ?? false;
        $isHot        = in_array($heatStress, ['high','extreme']);

        return array_map(function (array $slot) use (
            $daypart, $minToSunset, $weatherType, $heatStress,
            $fatigueState, $hungerState, $isGolden, $isNight, $isMealTime, $isHot
        ) {
            $c = $slot['candidate'];

            // ── Feature extraction ────────────────────────────────────────────
            $oppScore   = (float)($c['opportunity_score']    ?? 0);
            $doNow      = (float)($c['do_now_score']         ?? 0);
            $doSoon     = (float)($c['do_soon_score']        ?? 0);
            $routeFit   = (float)($c['route_fit']            ?? 0);
            $mealFit    = (float)($c['meal_fit_score']       ?? 0);
            $recFit     = (float)($c['recovery_fit_score']   ?? 0);
            $scenicFit  = (float)($c['scenic_fit_score']     ?? 0);
            $distKm     = (float)($c['distance_km']          ?? 5);
            $isScenic   = (bool)($c['is_scenic']             ?? false);
            $isRest     = (bool)($c['is_restaurant']         ?? false);
            $isIndoor   = !(bool)($c['is_outdoor']           ?? false);
            $tier       = (int)($c['tier']                   ?? 4);
            $winStr     = (float)($c['window_strength_score']?? 0);

            // ── Family scoring (spec 10.7 – 10.14) ───────────────────────────
            $familyScores = [];

            // IMMEDIATE_ACTION
            $familyScores[self::FAMILY_IMMEDIATE_ACTION] =
                0.35 * $doNow + 0.30 * $routeFit + 0.20 * (float)($c['open_now_score'] ?? 0.5) + 0.15 * ($distKm <= 2 ? 1.0 : 0.3);

            // OPPORTUNITY
            $familyScores[self::FAMILY_OPPORTUNITY] =
                0.40 * $oppScore + 0.30 * $winStr + 0.20 * (float)($c['time_sensitivity_score'] ?? 0) + 0.10 * $doNow;

            // SOCIAL_ENERGY
            $familyScores[self::FAMILY_SOCIAL_ENERGY] =
                0.35 * (float)($c['social_fit_score'] ?? 0) + 0.30 * (float)($c['context_relevance_score'] ?? 0) + 0.20 * (float)($c['review_strength'] ?? 0) + 0.15 * ($isNight ? 1.0 : 0.3);

            // NEED_STATE
            $familyScores[self::FAMILY_NEED_STATE] =
                0.40 * max($mealFit, $recFit) + 0.30 * (float)($c['need_state_match'] ?? 0) + 0.30 * $doNow;

            // RECOVERY
            $familyScores[self::FAMILY_RECOVERY] =
                0.50 * $recFit + 0.25 * (float)($c['ease_of_access'] ?? 0.5) + 0.25 * (float)($c['open_now_score'] ?? 0.5);

            // DISCOVERY
            $familyScores[self::FAMILY_DISCOVERY] =
                0.30 * (float)($c['uniqueness'] ?? 0) + 0.25 * (float)($c['local_character_score'] ?? 0) + 0.25 * (float)($c['discovery_fit_score'] ?? 0) + 0.20 * $routeFit;

            // PLANNING_GUIDANCE
            $familyScores[self::FAMILY_PLANNING_GUIDANCE] =
                0.40 * $doSoon + 0.30 * (float)($c['trip_importance_score'] ?? 0) + 0.30 * (float)($c['structural_importance_score'] ?? 0);

            // TRANSITION
            $familyScores[self::FAMILY_TRANSITION] =
                0.50 * $routeFit + 0.30 * (float)($c['transition_fit_score'] ?? 0) + 0.20 * $doNow;

            // ANCHOR_REINFORCEMENT
            $familyScores[self::FAMILY_ANCHOR_REINFORCEMENT] =
                0.40 * (float)($c['anchor_score'] ?? 0) + 0.30 * (float)($c['structural_importance_score'] ?? 0) + 0.30 * $doNow;

            // Pick primary family
            arsort($familyScores);
            $primaryFamily  = array_key_first($familyScores);
            $primaryScore   = $familyScores[$primaryFamily];

            // ── Moment type selection ─────────────────────────────────────────
            [$momentType, $urgency] = $this->selectMomentType(
                $c, $primaryFamily, $primaryScore,
                $isGolden, $isNight, $isMealTime, $isHot, $minToSunset, $distKm
            );

            // ── Suppression check (spec 10.16) ────────────────────────────────
            $confidence = min(1.0, $primaryScore * 0.7 + $urgency * 0.3);
            $suppressed = ($primaryScore < 0.58 || $confidence < 0.55);

            // ── Copy resolution ───────────────────────────────────────────────
            $copy = $suppressed
                ? ['Good area pick', 'Popular in this area', 'explore']
                : (self::COPY_TEMPLATES[$momentType] ?? self::COPY_TEMPLATES['GOOD_AREA_PICK']);

            $slot['candidate']['moment_primary_family'] = $primaryFamily;
            $slot['candidate']['moment_primary_type']   = $momentType;
            $slot['candidate']['moment_label_short']    = $copy[0];
            $slot['candidate']['moment_label_medium']   = $copy[1];
            $slot['candidate']['moment_urgency_level']  = round($urgency, 4);
            $slot['candidate']['moment_confidence']     = round($confidence, 4);
            $slot['candidate']['moment_cta_style']      = $copy[2];
            $slot['candidate']['is_moment_suppressed']  = $suppressed;
            $slot['moment_label_short']                 = $copy[0];
            $slot['moment_label_medium']                = $copy[1];

            return $slot;
        }, $rankedSlots);
    }

    // ─── Moment type resolution ───────────────────────────────────────────────

    private function selectMomentType(
        array $c, string $family, float $score,
        bool $isGolden, bool $isNight, bool $isMealTime,
        bool $isHot, int $minToSunset, float $distKm
    ): array {

        $doNow  = (float)($c['do_now_score']      ?? 0);
        $winStr = (float)($c['window_strength_score'] ?? 0);
        $mealFt = (float)($c['meal_fit_score']    ?? 0);
        $tier   = (int)($c['tier'] ?? 4);

        // Urgency
        $urgency = match (true) {
            $isGolden && $c['is_scenic']                   => 1.00,
            $minToSunset <= 45 && $c['is_scenic']          => 0.95,
            $isMealTime && $c['is_restaurant']             => 0.85,
            $winStr >= 0.75                                => 0.85,
            $doNow >= 0.70                                 => 0.75,
            $doNow >= 0.50                                 => 0.55,
            default                                        => 0.35,
        };

        $type = match (true) {
            $isGolden && $c['is_scenic']                        => 'GOLDEN_HOUR_SPOT',
            $minToSunset <= 90 && $c['is_scenic']               => 'BEST_BEFORE_SUNSET',
            $family === self::FAMILY_OPPORTUNITY && $winStr >= 0.6 => 'RIGHT_HERE_RIGHT_NOW',
            $family === self::FAMILY_NEED_STATE && $c['is_restaurant'] && $isMealTime => 'QUICK_BITE_NEARBY',
            $family === self::FAMILY_NEED_STATE && $c['is_restaurant'] && $isNight    => 'BEST_DINNER_AREA',
            $family === self::FAMILY_NEED_STATE && $c['is_restaurant']                => 'EASY_LUNCH_STOP',
            $family === self::FAMILY_RECOVERY && $isHot && !($c['is_outdoor'] ?? false) => 'COOL_INDOOR_BREAK',
            $family === self::FAMILY_RECOVERY                   => 'RIGHT_HERE_RIGHT_NOW',
            $family === self::FAMILY_SOCIAL_ENERGY && $isNight  => 'GREAT_FOR_TONIGHT',
            $family === self::FAMILY_DISCOVERY                  => 'HIDDEN_LOCAL_GEM',
            $family === self::FAMILY_ANCHOR_REINFORCEMENT && $tier === 1 => 'ICONIC_MUST_DO',
            $family === self::FAMILY_PLANNING_GUIDANCE          => 'SAVE_FOR_TOMORROW',
            $family === self::FAMILY_TRANSITION                 => 'GOOD_NEXT_STOP',
            $isHot && ($c['is_outdoor'] ?? false)               => 'BEST_ONCE_HEAT_DROPS',
            $distKm > 5                                         => 'WORTH_A_SHORT_RIDE',
            $doNow >= 0.55                                      => 'RIGHT_HERE_RIGHT_NOW',
            default                                             => 'GOOD_AREA_PICK',
        };

        return [$type, $urgency];
    }
}
