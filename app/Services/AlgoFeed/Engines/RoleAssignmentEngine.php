<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 6 — Role Assignment Engine
 *
 * Converts upstream scores into feed roles. Every eligible candidate receives:
 *   primary_role          string   e.g. CURRENT_ANCHOR, RECOVERY_ITEM …
 *   secondary_roles       array
 *   role_group            string   structural|journey|opportunity|support|planning|need_state
 *   role_confidence       float    0-1
 *   role_selection_score  float    0-1
 *   role_priority         float    higher = shown earlier
 *
 * Role definitions and formulas follow the spec (Algo Engines(10).txt) exactly.
 */
class RoleAssignmentEngine
{
    // Role priority weights (higher = more urgent / top-feed)
    private const ROLE_PRIORITY = [
        'CURRENT_ANCHOR'        => 1.00,
        'WINDOWED_EXPERIENCE'   => 0.98,
        'NEXT_ANCHOR'           => 0.95,
        'DINNER_DESTINATION'    => 0.88,
        'HERO_EXPERIENCE'       => 0.90,
        'MEAL_WINDOW_ITEM'      => 0.70,
        'LUNCH_FIT_ITEM'        => 0.66,
        'COMPOSITE_ANCHOR'      => 0.85,
        'COLLECTIVE_DESTINATION'=> 0.80,
        'TRIP_IMPORTANT_ITEM'   => 0.78,
        'SCENIC_MOMENT'         => 0.75,
        'RECOVERY_ITEM'         => 0.72,
        'DO_SOON_ITEM'          => 0.72,
        'TRANSITION_ITEM'       => 0.65,
        'QUICK_BREAK_ITEM'      => 0.50,
        'DISCOVERY_ITEM'        => 0.55,
        'SOCIAL_CLUSTER_ITEM'   => 0.52,
        'UTILITY_SUPPORT'       => 0.40,
        'NEXT_DAY_CANDIDATE'    => 0.45,
        'LATE_NIGHT_FOOD_ITEM'  => 0.73,
    ];

    public function run(Collection $candidates, array $ctx): Collection
    {
        $daypart     = $ctx['daypart']       ?? 'afternoon';
        $isNight     = in_array($daypart, ['evening','night']);
        $isLunchTime = $daypart === 'midday';
        $isDinner    = in_array($daypart, ['evening','golden_hour']);
        $isGolden    = $daypart === 'golden_hour';
        $isLateNight = $daypart === 'night';

        return $candidates->map(function (array $c) use ($daypart, $isNight, $isLunchTime, $isDinner, $isGolden, $isLateNight) {
            if (!$c['is_eligible']) return $c;

            $scores = $this->scoreAllRoles($c, $daypart, $isNight, $isLunchTime, $isDinner, $isGolden, $isLateNight);

            // Filter eligible roles (must exceed threshold)
            $eligible = array_filter($scores, fn($s) => $s['score'] >= $s['threshold'] && $s['eligible']);

            if (empty($eligible)) {
                // Fallback: assign lowest-priority role that fits
                $c['primary_role']         = 'DISCOVERY_ITEM';
                $c['role_group']           = 'support';
                $c['role_confidence']      = 0.40;
                $c['role_selection_score'] = 0.40;
                $c['role_priority']        = 0.40;
                $c['secondary_roles']      = [];
                return $c;
            }

            // Sort by role_selection_score descending
            uasort($eligible, fn($a, $b) => $b['selection_score'] <=> $a['selection_score']);
            $roleNames = array_keys($eligible);

            $primaryRole   = $roleNames[0];
            $primaryData   = $eligible[$primaryRole];
            $secondaryRoles = array_slice($roleNames, 1, 3);

            // Confidence formula from spec 6.12
            $secondBest = count($eligible) > 1 ? $eligible[$roleNames[1]]['score'] : 0;
            $confidence = min(1.0,
                0.50 * $primaryData['score'] +
                0.20 * max(0, $primaryData['score'] - $secondBest) +
                0.15 * min(1.0, ($c['structural_importance_score'] ?? 0)) +
                0.15 * min(1.0, ($c['context_relevance_score'] ?? 0))
            );

            $c['primary_role']         = $primaryRole;
            $c['role_group']           = $primaryData['group'];
            $c['role_confidence']      = round($confidence, 4);
            $c['role_selection_score'] = round($primaryData['selection_score'], 4);
            $c['role_priority']        = self::ROLE_PRIORITY[$primaryRole] ?? 0.50;
            $c['secondary_roles']      = $secondaryRoles;
            $c['all_role_scores']      = $scores;

            return $c;
        });
    }

    // ─── Score all roles ──────────────────────────────────────────────────────

    private function scoreAllRoles(array $c, string $daypart, bool $isNight, bool $isLunch, bool $isDinner, bool $isGolden, bool $isLateNight): array
    {
        $si  = (float)($c['structural_importance_score'] ?? 0);
        $anc = (float)($c['anchor_score']               ?? 0);
        $ti  = (float)($c['trip_importance_score']      ?? 0);
        $opp = (float)($c['opportunity_score']          ?? 0);
        $dn  = (float)($c['do_now_score']               ?? 0);
        $ds  = (float)($c['do_soon_score']              ?? 0);
        $dl  = (float)($c['do_later_score']             ?? 0);
        $nd  = (float)($c['next_day_score']             ?? 0);
        $rf  = (float)($c['route_fit']                  ?? 0);
        $on  = (float)($c['open_now_score']             ?? 0.5);
        $mf  = (float)($c['meal_fit_score']             ?? 0);
        $rec = (float)($c['recovery_fit_score']         ?? 0);
        $sc  = (float)($c['scenic_fit_score']           ?? 0);
        $cr  = (float)($c['context_relevance_score']    ?? 0);
        $ws  = (float)($c['window_strength_score']      ?? 0);
        $ts  = (float)($c['time_sensitivity_score']     ?? 0);
        $wnd = (float)($c['reachability_before_window_end'] ?? 0.5);
        $soc = (float)($c['social_fit_score']           ?? 0);
        $mw  = (float)($c['meal_window_score']          ?? 0);
        $nw  = (float)($c['nightlife_window_score']     ?? 0);
        $rr  = (float)($c['review_strength']            ?? 0);
        $wf  = (float)($c['weather_fit_score']          ?? 0);
        $lo  = (float)($c['late_open_score']            ?? 0);
        $rc  = (float)($c['route_convenience_score']    ?? 0);
        $ut  = $c['is_restaurant'] ? 0.7 : ($c['is_quick_stop'] ? 0.5 : 0.2);
        $wow = (float)($c['wow_factor_score']           ?? 0);
        $uni = (float)($c['uniqueness']                 ?? 0);
        $lc  = (float)($c['local_character_score']      ?? 0);
        $nx  = (float)($c['night_fit_score']            ?? 0);
        $fe  = (float)($c['forward_route_fit']          ?? 0);

        $roles = [];

        // ── STRUCTURAL ROLES ─────────────────────────────────────────────────

        // CURRENT_ANCHOR
        $s = 0.40*$anc + 0.30*$si + 0.20*$dn + 0.10*$on;
        $roles['CURRENT_ANCHOR'] = $this->role($s, 0.70, $s * 1.0, 'structural', $c['tier'] <= 2);

        // NEXT_ANCHOR
        $s = 0.35*$anc + 0.30*$si + 0.25*$ds + 0.10*$rf;
        $roles['NEXT_ANCHOR'] = $this->role($s, 0.65, $s * 0.95, 'structural', $c['tier'] <= 2);

        // COMPOSITE_ANCHOR
        $s = 0.35*($c['composite_anchor_score'] ?? $anc*0.8) + 0.25*$si + 0.20*$cr + 0.20*$rf;
        $roles['COMPOSITE_ANCHOR'] = $this->role($s, 0.65, $s * 0.85, 'structural', true);

        // COLLECTIVE_DESTINATION
        $colDs = (float)($c['collective_destination_score'] ?? 0);
        $s = 0.35*$colDs + 0.25*$rr + 0.20*$soc + 0.20*$cr;
        $roles['COLLECTIVE_DESTINATION'] = $this->role($s, 0.60, $s * 0.80, 'structural', true);

        // TRIP_IMPORTANT_ITEM
        $s = 0.45*$ti + 0.25*$si + 0.15*$dn + 0.15*$wnd;
        $roles['TRIP_IMPORTANT_ITEM'] = $this->role($s, 0.65, $s * 0.78, 'structural', true);

        // ── JOURNEY ROLES ─────────────────────────────────────────────────────

        // RECOVERY_ITEM
        $s = 0.35*$rec + 0.25*$cr + 0.20*$on + 0.20*$rc;
        $roles['RECOVERY_ITEM'] = $this->role($s, 0.55, $s * 0.72, 'journey',
            $rec >= 0.40 || $c['is_quick_stop'] || $c['is_restaurant']);

        // TRANSITION_ITEM
        $s = 0.35*$rf + 0.25*($c['transition_fit_score'] ?? 0) + 0.20*$dn + 0.20*$on;
        $roles['TRANSITION_ITEM'] = $this->role($s, 0.55, $s * 0.65, 'journey', true);

        // DISCOVERY_ITEM
        $s = 0.22*$uni + 0.20*$lc + 0.18*$cr + 0.20*$rr + 0.20*($c['discovery_fit_score'] ?? 0);
        $roles['DISCOVERY_ITEM'] = $this->role($s, 0.50, $s * 0.55, 'journey', $c['tier'] >= 3);

        // SCENIC_MOMENT
        $s = 0.35*$sc + 0.25*$wf + 0.20*$wow + 0.20*$cr;
        $roles['SCENIC_MOMENT'] = $this->role($s, 0.55, $s * 0.75, 'journey', $c['is_scenic'] || $sc >= 0.4);

        // HERO_EXPERIENCE
        $s = 0.30*$wow + 0.25*$si + 0.25*$opp + 0.20*$cr;
        $roles['HERO_EXPERIENCE'] = $this->role($s, 0.65, $s * 0.90, 'journey', $c['tier'] <= 2);

        // ── OPPORTUNITY ROLES ─────────────────────────────────────────────────

        // WINDOWED_EXPERIENCE
        $s = 0.25*$opp + 0.25*$ws + 0.20*$ts + 0.15*$wnd + 0.15*($c['window_uniqueness_score'] ?? 0);
        $roles['WINDOWED_EXPERIENCE'] = $this->role($s, 0.70, $s * 1.0, 'opportunity', $ws >= 0.50);

        // NIGHTLIFE_DRIVER
        $s = 0.25*$nw + 0.25*$nx + 0.20*$soc + 0.15*($c['cluster_energy_score'] ?? 0.3) + 0.15*$lo;
        $roles['NIGHTLIFE_DRIVER'] = $this->role($s, 0.66, $s * 0.87, 'opportunity',
            $isNight && ($c['is_nightlife'] || ($c['is_restaurant'] && $isNight)));

        // ── SUPPORT ROLES ─────────────────────────────────────────────────────

        // UTILITY_SUPPORT
        $s = 0.35*$ut + 0.25*$mf + 0.20*$rc + 0.20*$on;
        $roles['UTILITY_SUPPORT'] = $this->role($s, 0.55, $s * 0.40, 'support', true);

        // SOCIAL_CLUSTER_ITEM
        $s = 0.28*$soc + 0.22*($c['cluster_energy_score'] ?? 0.3) + 0.18*($c['lingerability_score'] ?? 0.3) + 0.17*0.4 + 0.15*($c['walkability_score'] ?? 0.5);
        $roles['SOCIAL_CLUSTER_ITEM'] = $this->role($s, 0.60, $s * 0.52, 'support', true);

        // ── PLANNING ROLES ────────────────────────────────────────────────────

        // DO_SOON_ITEM
        $s = 0.35*$ds + 0.25*$ti + 0.20*($c['opening_window_future_fit'] ?? 0.4) + 0.20*$fe;
        $roles['DO_SOON_ITEM'] = $this->role($s, 0.62, $s * 0.72, 'planning', true);

        // NEXT_DAY_CANDIDATE
        $s = 0.35*$nd + 0.25*$ti + 0.25*(1-$dn) + 0.15*($dn < 0.40 && $nd > 0.70 ? 1.0 : 0.30);
        $roles['NEXT_DAY_CANDIDATE'] = $this->role($s, 0.60, $s * 0.45, 'planning',
            $dn < 0.50 && $nd >= 0.50);

        // ── NEED STATE ROLES ──────────────────────────────────────────────────

        // MEAL_WINDOW_ITEM
        $s = 0.30*$mf + 0.25*$mw + 0.20*($c['hunger_need_score'] ?? $mf) + 0.15*$on + 0.10*($c['service_window_active'] ? 1.0 : 0.0);
        $roles['MEAL_WINDOW_ITEM'] = $this->role($s, 0.60, $s * 0.70, 'need_state', $c['is_restaurant']);

        // DINNER_DESTINATION
        $da = ($c['dinner_ambience_score'] ?? ($c['is_restaurant'] && $isDinner ? 0.7 : 0.2));
        $s = 0.22*$mf + 0.22*$da + 0.20*($c['evening_fit_score'] ?? ($isDinner ? 0.8 : 0.3)) + 0.18*($c['destination_dining_score'] ?? 0.3) + 0.18*$soc;
        $roles['DINNER_DESTINATION'] = $this->role($s, 0.68, $s * 0.88, 'need_state',
            $c['is_restaurant'] && $isDinner);

        // LUNCH_FIT_ITEM
        $lw = ($c['lunch_window_score'] ?? ($isLunch ? 0.85 : 0.2));
        $s = 0.28*$mf + 0.22*$lw + 0.20*$rc + 0.15*($c['quick_service_score'] ?? 0.5) + 0.15*($c['midday_proximity_score'] ?? $rf);
        $roles['LUNCH_FIT_ITEM'] = $this->role($s, 0.62, $s * 0.66, 'need_state',
            $c['is_restaurant'] && ($isLunch || $mf >= 0.5));

        // QUICK_BREAK_ITEM
        $dwell = $c['duration_minutes'] ?? 60;
        $dwellS = $dwell <= 20 ? 1.0 : ($dwell <= 40 ? 0.70 : ($dwell <= 60 ? 0.40 : 0.10));
        $detourS = $c['low_detour_score'] ?? 0.5;
        $s = 0.25*$dwellS + 0.22*$rec + 0.20*($c['transition_fit_score'] ?? 0) + 0.18*$detourS + 0.15*($c['impulse_stop_score'] ?? 0);
        $roles['QUICK_BREAK_ITEM'] = $this->role($s, 0.57, $s * 0.50, 'need_state',
            $c['is_quick_stop'] || $rec >= 0.5);

        // LATE_NIGHT_FOOD_ITEM
        $s = 0.25*$lo + 0.25*$mf + 0.20*$nx + 0.15*($c['post_activity_recovery_fit'] ?? 0.3) + 0.15*($c['short_distance_score'] ?? 0.5);
        $roles['LATE_NIGHT_FOOD_ITEM'] = $this->role($s, 0.62, $s * 0.73, 'need_state',
            $c['is_restaurant'] && $isLateNight);

        return $roles;
    }

    private function role(float $score, float $threshold, float $selScore, string $group, bool $eligible): array
    {
        return [
            'score'           => round(min(1.0, $score), 4),
            'threshold'       => $threshold,
            'selection_score' => round(min(1.0, $selScore), 4),
            'group'           => $group,
            'eligible'        => $eligible && $score >= $threshold,
        ];
    }
}
