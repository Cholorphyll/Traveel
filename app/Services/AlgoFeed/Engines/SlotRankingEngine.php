<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 9 — Slot Ranking Engine
 *
 * For each slot in the sequence plan, picks the best-matching candidate
 * from the eligible pool using a slot-type-specific formula.
 *
 * Returns an array of filled slots:
 *  [
 *    'slot_position'    => int,
 *    'slot_type'        => string,
 *    'slot_family'      => string,
 *    'slot_goal'        => string,
 *    'horizon_bias'     => string,
 *    'planned_score'    => float,
 *    'planning_reason'  => string,
 *    'candidate'        => array,   ← winning candidate with scores
 *    'slot_score_final' => float,
 *  ]
 */
class SlotRankingEngine
{
    // Penalty config
    private const MAX_REPETITION_PENALTY  = 0.25;
    private const REPETITION_STEP         = 0.08;

    public function rank(Collection $candidates, array $sequencePlan, array $ctx): array
    {
        $eligible = $candidates->where('is_eligible', true)->values();
        $used     = []; // candidate_item_id → used in slot position
        $recentCategories = [];

        $filledSlots = [];

        foreach ($sequencePlan as $slot) {
            $slotType   = $slot['slot_type'];
            $slotFamily = $slot['slot_family'];

            // Score all candidates for this slot
            $scored = $this->scoreForSlot($eligible, $slotType, $slotFamily, $ctx, $used, $recentCategories);

            if ($scored->isEmpty()) {
                // No candidate matched — skip slot
                continue;
            }

            $winner = $scored->first();

            $used[$winner['candidate_item_id']] = $slot['slot_position'];

            // Track recent categories for repetition penalty
            $recentCategories[] = $winner['candidate_item_type'] . ':' . ($winner['category_title'] ?? 'misc');
            if (count($recentCategories) > 6) array_shift($recentCategories);

            $filledSlots[] = array_merge($slot, [
                'candidate'        => $winner,
                'slot_score_final' => round($winner['slot_score_final'], 4),
            ]);
        }

        return $filledSlots;
    }

    // ─── Score candidates for a specific slot ────────────────────────────────

    private function scoreForSlot(
        Collection $candidates, string $slotType, string $slotFamily,
        array $ctx, array $used, array $recentCategories
    ): Collection {

        $scored = $candidates
            ->filter(fn($c) => !isset($used[$c['candidate_item_id']]))
            ->filter(fn($c) => $this->slotHardGate($c, $slotType, $ctx))
            ->map(function (array $c) use ($slotType, $slotFamily, $ctx, $recentCategories) {
                $raw = $this->computeSlotScore($c, $slotType, $slotFamily, $ctx);

                // Penalty layer
                $repPenalty  = $this->repetitionPenalty($c, $recentCategories);
                $fatPenalty  = $this->fatiguePenalty($c, $ctx);
                $redundancy  = $this->redundancyPenalty($c, $recentCategories);
                $totalPenalty = min(0.80, $repPenalty + $fatPenalty + $redundancy);

                $c['slot_score_raw']      = round($raw, 4);
                $c['slot_penalty_total']  = round($totalPenalty, 4);
                $c['slot_score_final']    = round(max(0, $raw - $totalPenalty), 4);
                $c['slot_type']           = $slotType;
                $c['slot_family']         = $slotFamily;
                return $c;
            })
            ->sortByDesc('slot_score_final')
            ->values();

        return $scored;
    }

    // ─── Slot-specific scoring formulas (spec section 9.5 – 9.7) ────────────

    private function computeSlotScore(array $c, string $slotType, string $slotFamily, array $ctx): float
    {
        $si   = (float)($c['structural_importance_score'] ?? 0);
        $anc  = (float)($c['anchor_score']               ?? 0);
        $ti   = (float)($c['trip_importance_score']      ?? 0);
        $dn   = (float)($c['do_now_score']               ?? 0);
        $ds   = (float)($c['do_soon_score']              ?? 0);
        $opp  = (float)($c['opportunity_score']          ?? 0);
        $cr   = (float)($c['context_relevance_score']    ?? 0);
        $rf   = (float)($c['route_fit']                  ?? 0);
        $rr   = (float)($c['review_strength']            ?? 0);
        $on   = (float)($c['open_now_score']             ?? 0.5);
        $mf   = (float)($c['meal_fit_score']             ?? 0);
        $rec  = (float)($c['recovery_fit_score']         ?? 0);
        $sc   = (float)($c['scenic_fit_score']           ?? 0);
        $wow  = (float)($c['wow_factor_score']           ?? 0);
        $wf   = (float)($c['weather_fit_score']          ?? 0);
        $uni  = (float)($c['uniqueness']                 ?? 0);
        $ea   = (float)($c['ease_of_access']             ?? 0.5);
        $ns   = (float)($c['need_state_match']           ?? 0);
        $ws   = (float)($c['window_strength_score']      ?? 0);
        $ts   = (float)($c['time_sensitivity_score']     ?? 0);
        $wnd  = (float)($c['reachability_before_window_end'] ?? 0.5);
        $lc   = (float)($c['local_character_score']      ?? 0);
        $tp   = (float)($c['trip_importance_score']      ?? 0);
        $fe   = (float)($c['forward_route_fit']          ?? 0);
        $lo   = (float)($c['low_detour_score']           ?? 0.5);
        $pop  = (float)($c['popularity_score']           ?? 0);

        return match (true) {
            // ANCHOR slots
            in_array($slotType, ['CURRENT_ANCHOR_SLOT','NEXT_ANCHOR_SLOT','COMPOSITE_ANCHOR_SLOT'])
                => 0.35*$si + 0.25*$anc + 0.20*$cr + 0.10*$rf + 0.10*$rr,

            // HERO / OPPORTUNITY slots
            in_array($slotType, ['HERO_OPPORTUNITY_SLOT','SUNSET_DESTINATION_SLOT','WINDOWED_SLOT'])
                => 0.25*$opp + 0.25*$ws + 0.20*$ts + 0.15*$wnd + 0.15*$wow,

            // DO_SOON / DO_LATER reassurance
            in_array($slotType, ['DO_SOON_SLOT','TRIP_REASSURANCE_SLOT','TRIP_IMPORTANT_SLOT'])
                => 0.30*$ti + 0.25*$si + 0.20*$ds + 0.15*$fe + 0.10*$rr,

            // RECOVERY slots
            in_array($slotType, ['RECOVERY_SCENIC_SLOT','QUICK_BREAK_SLOT','COFFEE_BREAK_SLOT','INDOOR_RESET_SLOT'])
                => 0.30*$rec + 0.25*$cr + 0.20*$ea + 0.15*$on + 0.10*$rf,

            // FOOD slots
            in_array($slotType, ['LUNCH_SLOT','DINNER_DESTINATION_SLOT','FOOD_COLLECTION_SLOT'])
                => 0.35*$mf + 0.25*$ns + 0.20*$on + 0.10*$rf + 0.10*$rr,

            // SCENIC slots
            in_array($slotType, ['SCENIC_SLOT'])
                => 0.30*$sc + 0.25*$wf + 0.20*$cr + 0.15*$wow + 0.10*$rf,

            // DISCOVERY slots
            in_array($slotType, ['DISCOVERY_SLOT'])
                => 0.22*$uni + 0.18*$cr + 0.14*$rf + 0.12*$rr + 0.12*$sc + 0.12*$tp + 0.10*$ea,

            // SOCIAL EVENING
            in_array($slotType, ['SOCIAL_EVENING_SLOT'])
                => 0.30*$cr + 0.25*$rr + 0.20*$on + 0.15*$mf + 0.10*$rf,

            // TRANSITION
            in_array($slotType, ['TRANSITION_SLOT'])
                => 0.35*$lo + 0.30*$rf + 0.20*$cr + 0.15*$ea,

            // DEFAULT: general quality score
            default
                => 0.30*$si + 0.25*$cr + 0.20*$dn + 0.15*$rr + 0.10*$rf,
        };
    }

    // ─── Hard eligibility gates per slot type ────────────────────────────────

    private function slotHardGate(array $c, string $slotType, array $ctx): bool
    {
        $daypart  = $ctx['daypart'] ?? 'afternoon';
        $isNight  = in_array($daypart, ['evening','night']);
        $isLunch  = $daypart === 'midday';
        $isDinner = in_array($daypart, ['evening','golden_hour']);

        return match ($slotType) {
            // Anchor slots: must be a sight/experience, not a restaurant
            'CURRENT_ANCHOR_SLOT', 'NEXT_ANCHOR_SLOT', 'COMPOSITE_ANCHOR_SLOT'
                => !$c['is_restaurant'] && ($c['tier'] <= 3),

            // Hero: opportunity must be strong
            'HERO_OPPORTUNITY_SLOT'
                => ($c['opportunity_score'] ?? 0) >= 0.55 && !$c['is_restaurant'],

            // Sunset: must be scenic
            'SUNSET_DESTINATION_SLOT'
                => ($c['is_scenic'] || ($c['is_outdoor'] && ($c['scenic_fit_score'] ?? 0) >= 0.4)),

            // Food slots: must be restaurants
            'LUNCH_SLOT', 'DINNER_DESTINATION_SLOT', 'FOOD_COLLECTION_SLOT', 'SOCIAL_EVENING_SLOT'
                => $c['is_restaurant'],

            // Recovery: quick-stop or restaurant
            'QUICK_BREAK_SLOT', 'COFFEE_BREAK_SLOT', 'RECOVERY_SCENIC_SLOT'
                => !$c['is_high_commitment'],

            // Indoor reset: must not be outdoor
            'INDOOR_RESET_SLOT'
                => !($c['is_outdoor'] ?? false) || $c['is_restaurant'],

            // Discovery: tier 2-4 only
            'DISCOVERY_SLOT'
                => ($c['tier'] ?? 4) >= 2,

            default => true,
        };
    }

    // ─── Penalty layer ────────────────────────────────────────────────────────

    private function repetitionPenalty(array $c, array $recent): float
    {
        $catKey = $c['candidate_item_type'] . ':' . ($c['category_title'] ?? 'misc');
        $count  = count(array_filter($recent, fn($r) => $r === $catKey));
        return min(self::MAX_REPETITION_PENALTY, $count * self::REPETITION_STEP);
    }

    private function redundancyPenalty(array $c, array $recent): float
    {
        // Rough semantic similarity: same type in last 3 shown
        $last3   = array_slice($recent, -3);
        $catKey  = $c['candidate_item_type'];
        $matches = count(array_filter($last3, fn($r) => str_starts_with($r, $catKey)));
        return $matches >= 2 ? 0.15 : 0;
    }

    private function fatiguePenalty(array $c, array $ctx): float
    {
        $fatigue   = $ctx['user_fatigue_state'] ?? 'fresh';
        $energyCap = $fatigue === 'tired' ? 0.3 : ($fatigue === 'fresh' ? 1.0 : 0.65);
        $demand    = $c['is_high_commitment'] ? 0.85 : ($c['is_quick_stop'] ? 0.15 : 0.45);
        return max(0, ($demand - $energyCap)) * 0.4;
    }
}
