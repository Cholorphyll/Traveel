<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 8 — Sequence Planner Engine
 *
 * Converts upstream intelligence into an ordered slot plan (feed skeleton).
 * Uses a greedy sequential planner with rhythm penalties and transition rules.
 *
 * Returns array of slot plans:
 *   [ ['slot_position'=>1, 'slot_type'=>'CURRENT_ANCHOR_SLOT', 'slot_family'=>'ANCHOR', ...], ... ]
 */
class SequencePlannerEngine
{
    private const DEFAULT_SLOT_COUNT = 14;

    // Slot family -> types mapping (abridged canonical set from spec 25)
    private const SLOT_TEMPLATES = [
        ['slot_type'=>'CURRENT_ANCHOR_SLOT',     'slot_family'=>'ANCHOR',     'slot_goal'=>'Show the primary anchor for current position', 'horizon_bias'=>'DO_NOW',   'base_score'=>0.90],
        ['slot_type'=>'DO_NOW_ACTION_SLOT',       'slot_family'=>'ACTION',     'slot_goal'=>'Immediate actionable stop',                   'horizon_bias'=>'DO_NOW',   'base_score'=>0.88],
        ['slot_type'=>'HERO_OPPORTUNITY_SLOT',    'slot_family'=>'OPPORTUNITY','slot_goal'=>'Time-windowed hero moment',                   'horizon_bias'=>'DO_NOW',   'base_score'=>0.87],
        ['slot_type'=>'LUNCH_SLOT',               'slot_family'=>'FOOD',       'slot_goal'=>'Lunch stop in the area',                      'horizon_bias'=>'DO_NOW',   'base_score'=>0.82],
        ['slot_type'=>'NEXT_ANCHOR_SLOT',         'slot_family'=>'ANCHOR',     'slot_goal'=>'The next major stop on the journey',          'horizon_bias'=>'DO_SOON',  'base_score'=>0.80],
        ['slot_type'=>'RECOVERY_SCENIC_SLOT',     'slot_family'=>'RECOVERY',   'slot_goal'=>'A scenic breather between heavy items',       'horizon_bias'=>'DO_NOW',   'base_score'=>0.72],
        ['slot_type'=>'QUICK_BREAK_SLOT',         'slot_family'=>'RECOVERY',   'slot_goal'=>'Short easy pause',                           'horizon_bias'=>'DO_NOW',   'base_score'=>0.70],
        ['slot_type'=>'FOOD_COLLECTION_SLOT',     'slot_family'=>'FOOD',       'slot_goal'=>'Casual food cluster nearby',                  'horizon_bias'=>'DO_NOW',   'base_score'=>0.68],
        ['slot_type'=>'DISCOVERY_SLOT',           'slot_family'=>'DISCOVERY',  'slot_goal'=>'Serendipitous local find',                    'horizon_bias'=>'DO_SOON',  'base_score'=>0.62],
        ['slot_type'=>'COMPOSITE_ANCHOR_SLOT',    'slot_family'=>'ANCHOR',     'slot_goal'=>'Area / zone anchor',                         'horizon_bias'=>'DO_SOON',  'base_score'=>0.75],
        ['slot_type'=>'TRANSITION_SLOT',          'slot_family'=>'TRANSITION', 'slot_goal'=>'Bridges gap between two major stops',        'horizon_bias'=>'DO_NOW',   'base_score'=>0.60],
        ['slot_type'=>'SUNSET_DESTINATION_SLOT',  'slot_family'=>'OPPORTUNITY','slot_goal'=>'Sunset / golden-hour spot',                  'horizon_bias'=>'DO_NOW',   'base_score'=>0.88],
        ['slot_type'=>'DINNER_DESTINATION_SLOT',  'slot_family'=>'FOOD',       'slot_goal'=>'Dinner destination',                         'horizon_bias'=>'DO_NOW',   'base_score'=>0.85],
        ['slot_type'=>'SOCIAL_EVENING_SLOT',      'slot_family'=>'EVENING',    'slot_goal'=>'Social evening stop',                        'horizon_bias'=>'DO_SOON',  'base_score'=>0.78],
        ['slot_type'=>'DO_SOON_SLOT',             'slot_family'=>'DO_SOON',    'slot_goal'=>'Good stop in the next hour or two',          'horizon_bias'=>'DO_SOON',  'base_score'=>0.65],
        ['slot_type'=>'TRIP_IMPORTANT_SLOT',      'slot_family'=>'TRIP_IMPORTANT','slot_goal'=>'Must-do for this destination',            'horizon_bias'=>'DO_LATER', 'base_score'=>0.70],
        ['slot_type'=>'TRIP_REASSURANCE_SLOT',    'slot_family'=>'DO_SOON',    'slot_goal'=>'Gentle reminder of important item',          'horizon_bias'=>'DO_LATER', 'base_score'=>0.55],
        ['slot_type'=>'OPTIONAL_DETOUR_SLOT',     'slot_family'=>'OPTIONAL',   'slot_goal'=>'Worth a small detour',                       'horizon_bias'=>'DO_LATER', 'base_score'=>0.50],
        ['slot_type'=>'INDOOR_RESET_SLOT',        'slot_family'=>'RECOVERY',   'slot_goal'=>'Indoor shelter stop',                        'horizon_bias'=>'DO_NOW',   'base_score'=>0.68],
        ['slot_type'=>'COFFEE_BREAK_SLOT',        'slot_family'=>'RECOVERY',   'slot_goal'=>'Quick coffee or snack stop',                 'horizon_bias'=>'DO_NOW',   'base_score'=>0.65],
    ];

    // Rhythm penalty rules: after N items of family X, penalize X again
    private const RHYTHM_PENALTIES = [
        'ANCHOR'     => ['max' => 2, 'penalty' => 0.40],
        'FOOD'       => ['max' => 2, 'penalty' => 0.35],
        'RECOVERY'   => ['max' => 2, 'penalty' => 0.22],
        'DISCOVERY'  => ['max' => 3, 'penalty' => 0.15],
        'OPPORTUNITY'=> ['max' => 1, 'penalty' => 0.30],
    ];

    // Transition bonuses (prev_family -> next_family)
    private const TRANSITION_BONUSES = [
        'ANCHOR'     => ['RECOVERY' => 0.20, 'FOOD' => 0.15, 'TRANSITION' => 0.10],
        'FOOD'       => ['ANCHOR' => 0.10, 'DISCOVERY' => 0.15],
        'RECOVERY'   => ['ANCHOR' => 0.15, 'OPPORTUNITY' => 0.12],
        'OPPORTUNITY'=> ['RECOVERY' => 0.18, 'FOOD' => 0.12],
    ];

    public function plan(Collection $candidates, array $ctx): array
    {
        $daypart      = $ctx['daypart']           ?? 'afternoon';
        $comfortMode  = $ctx['comfort_mode']      ?? 'NORMAL';
        $minToSunset  = (int)($ctx['minutes_to_sunset'] ?? 999);
        $fatigueState = $ctx['user_fatigue_state'] ?? 'fresh';
        $hungerState  = $ctx['user_hunger_state']  ?? 'normal';
        $isMealTime   = $ctx['is_meal_time']       ?? false;
        $isGolden     = $daypart === 'golden_hour';
        $isNight      = in_array($daypart, ['evening','night']);
        $isLunch      = $daypart === 'midday';
        $isDinner     = in_array($daypart, ['evening','golden_hour']);

        // Build a simplified state from candidates
        $currentAnchor = $candidates->where('primary_role', 'CURRENT_ANCHOR')->sortByDesc('role_selection_score')->first();
        $nextAnchor    = $candidates->where('primary_role', 'NEXT_ANCHOR')->sortByDesc('role_selection_score')->first();

        $state = [
            'daypart'                  => $daypart,
            'comfort_mode'             => $comfortMode,
            'anchor_progress_stage'    => $currentAnchor ? 'AT_ANCHOR' : 'MOVING',
            'current_anchor_strength'  => $currentAnchor ? (float)($currentAnchor['anchor_score'] ?? 0.5) : 0,
            'next_anchor_entity_id'    => $nextAnchor ? $nextAnchor['entity_id'] : null,
            'meal_window_state'        => $isLunch ? 'LUNCH_WINDOW' : ($isDinner ? 'DINNER_WINDOW' : 'NONE'),
            'break_need_score'         => $fatigueState === 'tired' ? 0.80 : 0.30,
            'hunger_probability_score' => $hungerState === 'hungry' ? 0.80 : ($isMealTime ? 0.60 : 0.30),
            'active_opportunity_strength' => (float)($candidates->max('opportunity_score') ?? 0),
            'active_opportunity_class' => $isGolden ? 'SUNSET_WINDOW' : 'NONE',
            'top_feed_urgency_score'   => $this->computeUrgency($candidates),
            'action_horizon_bias'      => $this->computeHorizonBias($daypart, $fatigueState, $minToSunset),
        ];

        $plan       = [];
        $tempMemory = ['anchor_count'=>0,'food_count'=>0,'recovery_count'=>0,'heavy_count'=>0,'long_count'=>0,'family_counts'=>[]];
        $prevFamily = null;
        $nSlots     = self::DEFAULT_SLOT_COUNT;

        for ($pos = 1; $pos <= $nSlots; $pos++) {
            $slotScores = [];

            foreach (self::SLOT_TEMPLATES as $tpl) {
                if (!$this->isEligible($tpl, $state, $tempMemory, $daypart)) continue;

                $score = $this->scoreSlot($tpl, $state, $tempMemory, $pos, $nSlots);
                $score += $this->transitionBonus($prevFamily, $tpl['slot_family']);
                $score -= $this->rhythmPenalty($tpl['slot_family'], $tempMemory);

                $slotScores[] = ['tpl' => $tpl, 'score' => max(0, $score)];
            }

            if (empty($slotScores)) break;

            usort($slotScores, fn($a, $b) => $b['score'] <=> $a['score']);
            $best = $slotScores[0];

            $plan[] = [
                'slot_position'   => $pos,
                'slot_type'       => $best['tpl']['slot_type'],
                'slot_family'     => $best['tpl']['slot_family'],
                'slot_goal'       => $best['tpl']['slot_goal'],
                'horizon_bias'    => $best['tpl']['horizon_bias'],
                'sequence_mode'   => $state['anchor_progress_stage'],
                'planned_score'   => round($best['score'], 4),
                'planning_reason' => $this->explainSlot($best['tpl']['slot_type'], $state),
            ];

            $prevFamily = $best['tpl']['slot_family'];
            $this->updateMemory($tempMemory, $best['tpl']['slot_family']);
        }

        return $plan;
    }

    // ── Eligibility rules (spec section 30) ──────────────────────────────────

    private function isEligible(array $tpl, array $state, array $mem, string $daypart): bool
    {
        $mealState = $state['meal_window_state'];
        $isNight   = in_array($daypart, ['evening','night']);

        return match ($tpl['slot_type']) {
            'LUNCH_SLOT'              => in_array($mealState, ['LUNCH_WINDOW']) || $state['hunger_probability_score'] >= 0.75,
            'DINNER_DESTINATION_SLOT' => $isNight || $mealState === 'DINNER_WINDOW',
            'HERO_OPPORTUNITY_SLOT'   => $state['active_opportunity_strength'] >= 0.60,
            'NEXT_ANCHOR_SLOT'        => $state['next_anchor_entity_id'] !== null && in_array($state['anchor_progress_stage'], ['AT_ANCHOR','MOVING','SATURATING']),
            'RECOVERY_SCENIC_SLOT'    => $state['break_need_score'] >= 0.35 || $mem['heavy_count'] >= 1,
            'QUICK_BREAK_SLOT'        => $state['break_need_score'] >= 0.35 || $mem['heavy_count'] >= 1,
            'COFFEE_BREAK_SLOT'       => $state['break_need_score'] >= 0.30,
            'SUNSET_DESTINATION_SLOT' => $state['active_opportunity_class'] === 'SUNSET_WINDOW',
            'SOCIAL_EVENING_SLOT'     => $isNight,
            'INDOOR_RESET_SLOT'       => in_array($state['comfort_mode'], ['RAIN_SHELTER','HEAT_MANAGEMENT']),
            default                   => true,
        };
    }

    // ── Slot scoring (spec section 25) ───────────────────────────────────────

    private function scoreSlot(array $tpl, array $state, array $mem, int $pos, int $nSlots): float
    {
        $base          = $tpl['base_score'];
        $anchorProg    = $this->anchorProgressScore($tpl, $state);
        $rhythm        = 0; // handled separately
        $opportunity   = $this->opportunityScore($tpl, $state);
        $weatherFit    = $this->weatherFitScore($tpl, $state);
        $actionHorizon = $this->actionHorizonScore($tpl, $state, $pos, $nSlots);
        $needState     = $this->needStateScore($tpl, $state);
        $novelty       = $this->noveltyScore($tpl, $mem);

        return $base + $anchorProg + $opportunity + $weatherFit + $actionHorizon + $needState + $novelty;
    }

    private function anchorProgressScore(array $tpl, array $state): float
    {
        $stage   = $state['anchor_progress_stage'];
        $anchStr = $state['current_anchor_strength'];
        $family  = $tpl['slot_family'];

        if ($stage === 'AT_ANCHOR' && $family === 'ANCHOR') return 0.35 * $anchStr;
        if ($stage === 'MOVING'    && $family === 'ANCHOR') return 0.25 * $anchStr;
        if ($stage === 'SATURATING'&& $family === 'ANCHOR') return 0.15 * $anchStr;
        if ($stage === 'MOVING'    && $family === 'TRANSITION') return 0.20;
        return 0;
    }

    private function opportunityScore(array $tpl, array $state): float
    {
        $opp = $state['active_opportunity_strength'];
        if ($opp < 0.50) return 0;
        if (in_array($tpl['slot_type'], ['HERO_OPPORTUNITY_SLOT','SUNSET_DESTINATION_SLOT'])) return 0.28 * $opp;
        if ($tpl['slot_family'] === 'OPPORTUNITY') return 0.20 * $opp;
        return 0;
    }

    private function weatherFitScore(array $tpl, array $state): float
    {
        $mode   = $state['comfort_mode'];
        $family = $tpl['slot_family'];
        $type   = $tpl['slot_type'];

        if ($mode === 'HEAT_MANAGEMENT' && in_array($type, ['QUICK_BREAK_SLOT','INDOOR_RESET_SLOT','LUNCH_SLOT','FOOD_COLLECTION_SLOT'])) return 0.35;
        if ($mode === 'RAIN_SHELTER'    && in_array($type, ['INDOOR_RESET_SLOT','FOOD_COLLECTION_SLOT','LUNCH_SLOT','DO_SOON_SLOT']))     return 0.32;
        if ($mode === 'SCENIC_COMFORT'  && in_array($type, ['RECOVERY_SCENIC_SLOT','SUNSET_DESTINATION_SLOT','NEXT_ANCHOR_SLOT']))       return 0.28;
        return 0;
    }

    private function actionHorizonScore(array $tpl, array $state, int $pos, int $nSlots): float
    {
        $urgency = $state['top_feed_urgency_score'];
        $bias    = $state['action_horizon_bias'];
        $posWeight = $pos <= 3 ? 1.0 : ($pos <= 6 ? 0.75 : ($pos <= 10 ? 0.45 : 0.20));

        $horizonCompat = match ($tpl['horizon_bias']) {
            'DO_NOW'   => $bias === 'DO_NOW'  ? 1.0 : 0.5,
            'DO_SOON'  => $bias === 'DO_SOON' ? 1.0 : 0.6,
            'DO_LATER' => $bias === 'DO_LATER'? 1.0 : 0.4,
            default    => 0.5,
        };

        return $horizonCompat * $urgency * $posWeight;
    }

    private function needStateScore(array $tpl, array $state): float
    {
        $hunger = $state['hunger_probability_score'];
        $break  = $state['break_need_score'];
        $meal   = $state['meal_window_state'];

        if ($meal === 'LUNCH_WINDOW'  && $hunger >= 0.65 && in_array($tpl['slot_type'], ['LUNCH_SLOT','FOOD_COLLECTION_SLOT']))          return 0.45;
        if ($meal === 'DINNER_WINDOW' && $hunger >= 0.60 && in_array($tpl['slot_type'], ['DINNER_DESTINATION_SLOT','SOCIAL_EVENING_SLOT'])) return 0.48;
        if ($break  >= 0.70 && in_array($tpl['slot_type'], ['QUICK_BREAK_SLOT','RECOVERY_SCENIC_SLOT','COFFEE_BREAK_SLOT','INDOOR_RESET_SLOT'])) return 0.42;
        return 0;
    }

    private function noveltyScore(array $tpl, array $mem): float
    {
        $family = $tpl['slot_family'];
        $freq   = $mem['family_counts'][$family] ?? 0;
        if ($freq === 0) return 0.20;
        return max(0, 0.20 - $freq * 0.06);
    }

    private function rhythmPenalty(string $family, array $mem): float
    {
        $cfg = self::RHYTHM_PENALTIES[$family] ?? null;
        if (!$cfg) return 0;
        $count = $mem['family_counts'][$family] ?? 0;
        return $count >= $cfg['max'] ? $cfg['penalty'] : 0;
    }

    private function transitionBonus(?string $prev, string $next): float
    {
        if (!$prev) return 0;
        return self::TRANSITION_BONUSES[$prev][$next] ?? 0;
    }

    private function updateMemory(array &$mem, string $family): void
    {
        $mem['family_counts'][$family] = ($mem['family_counts'][$family] ?? 0) + 1;
        if ($family === 'ANCHOR')   $mem['anchor_count']++;
        if ($family === 'FOOD')     $mem['food_count']++;
        if ($family === 'RECOVERY') $mem['recovery_count']++;
    }

    private function computeUrgency(Collection $candidates): float
    {
        $maxNow = $candidates->where('is_eligible', true)->max('do_now_score') ?? 0;
        $maxOpp = $candidates->where('is_eligible', true)->max('opportunity_score') ?? 0;
        return min(1.0, ($maxNow * 0.6 + $maxOpp * 0.4));
    }

    private function computeHorizonBias(string $daypart, string $fatigue, int $minToSunset): string
    {
        if ($minToSunset <= 90) return 'DO_NOW';
        if (in_array($daypart, ['early_morning','morning'])) return 'DO_NOW';
        if ($fatigue === 'tired') return 'DO_SOON';
        return 'DO_NOW';
    }

    private function explainSlot(string $slotType, array $state): string
    {
        return match ($slotType) {
            'CURRENT_ANCHOR_SLOT'    => 'Primary anchor for current position',
            'HERO_OPPORTUNITY_SLOT'  => 'Active opportunity window detected',
            'SUNSET_DESTINATION_SLOT'=> 'Golden-hour / sunset window open',
            'LUNCH_SLOT'             => 'Meal window: lunch',
            'DINNER_DESTINATION_SLOT'=> 'Meal window: dinner',
            'RECOVERY_SCENIC_SLOT'   => 'Rhythm reset after heavy sequence',
            'QUICK_BREAK_SLOT'       => 'Break need detected',
            'NEXT_ANCHOR_SLOT'       => 'Next anchor in journey path',
            default                  => 'Standard slot selection',
        };
    }
}
