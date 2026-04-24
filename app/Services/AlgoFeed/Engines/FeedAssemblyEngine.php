<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Module 11 — Feed Assembly & Memory Layer (Full Spec)
 *
 * Spec: files-new/Algo Engines(14).txt
 *
 * Applies post-ranking orchestration:
 *   A) Memory features   — novelty, repetition risk, cooldown penalty, fatigue compatibility
 *   B) Hard suppression  — exact item, moment family, category saturation, anchor overuse
 *   C) Assembly scoring  — final_assembly_score = 0.45*base + memory bonuses - penalties
 *   D) Session evolution — orientation → expansion → adaptive phases
 *   E) Session state     — tracks fatigue, anchor density, exploration mode
 */
class FeedAssemblyEngine
{
    private const MAX_FEED_CARDS = 18;

    // Hard suppression thresholds (Spec 11.22)
    private const ITEM_COOLDOWN_MINUTES       = 90;
    private const ANCHOR_COOLDOWN_MINUTES     = 60;
    private const DISTRICT_COOLDOWN_MINUTES   = 45;
    private const COLLECTION_COOLDOWN_MINUTES = 90;
    private const CLUSTER_COOLDOWN_MINUTES    = 60;

    private const MAX_SAME_MOMENT_FAMILY = 3;
    private const MAX_SAME_CATEGORY      = 4;
    private const MAX_SAME_ANCHOR_ROLE   = 5;
    private const COLLECTION_DENSITY_CAP = 0.35;

    // Diversity bonuses — card window sizes (Spec 11.18)
    private const CATEGORY_WINDOW  = 6;
    private const VIBE_WINDOW      = 5;
    private const DISTRICT_WINDOW  = 4;
    private const ROLE_WINDOW      = 6;

    // Recent card tracking window for memory features
    private const MEMORY_WINDOW = 12;

    public function assemble(array $framedSlots, Collection $allCandidates, array $ctx): array
    {
        $sessionId = $ctx['session_id'] ?? uniqid('feed_', true);
        $userId    = $ctx['user_id']    ?? null;
        $tripId    = $ctx['trip_id']    ?? null;
        $daypart   = $ctx['daypart']    ?? 'afternoon';

        // ── Load session state ─────────────────────────────────────────────
        $sessionState = $this->loadSessionState($sessionId);

        // ── Load recent exposure history for memory features ───────────────
        $recentMemory = $this->loadRecentMemory($userId, $sessionId);

        // ── Assemble feed ──────────────────────────────────────────────────
        $feed         = [];
        $recentCards  = []; // last N cards for diversity tracking
        $position     = 1;

        foreach ($framedSlots as $slot) {
            if ($position > self::MAX_FEED_CARDS) break;

            $c = $slot['candidate'];
            $candidateId = $c['candidate_item_id'];

            // ── Compute memory features for this candidate ─────────────────
            $memFeatures = $this->computeMemoryFeatures($c, $slot, $recentMemory, $recentCards, $sessionState);

            // ── Apply hard suppression rules (Spec 11.22) ─────────────────
            $suppression = $this->checkHardSuppression($c, $slot, $memFeatures, $sessionState, $recentCards);
            if ($suppression) continue;

            // ── Compute final assembly score (Spec 11.21) ──────────────────
            $assemblyScore = $this->computeFinalAssemblyScore($c, $slot, $memFeatures, $sessionState, $position);

            // ── Build card ─────────────────────────────────────────────────
            $card = $this->buildCard($c, $slot, $position, $assemblyScore, $daypart, $allCandidates, $memFeatures);
            $feed[]       = $card;

            // Track for diversity window
            $recentCards[] = [
                'candidate_item_id' => $candidateId,
                'entity_type'       => $c['entity_type'],
                'primary_role'      => $c['primary_role']          ?? null,
                'moment_family'     => $c['moment_primary_family'] ?? null,
                'anchor_id'         => $c['nearest_anchor_id']     ?? null,
                'category'          => $c['category_title']        ?? null,
                'is_collection'     => (int)($c['is_collection']   ?? 0),
                'structural_class'  => $c['structural_class']      ?? null,
            ];
            if (count($recentCards) > self::MEMORY_WINDOW) {
                array_shift($recentCards);
            }

            // Persist exposure to DB only for authenticated users
            if ($userId) {
                $this->writeExposureMemory($sessionId, $userId, $tripId, $c, $slot, $position);
            }

            $position++;
        }

        // ── Backfill if sparse ─────────────────────────────────────────────
        if (count($feed) < self::MAX_FEED_CARDS) {
            $feed = $this->backfill($feed, $allCandidates, $recentMemory, $position, $ctx, $allCandidates);
        }

        // ── Interleave: first 3 slots = non-restaurants; then 1 restaurant per 4 positions ──
        $nonRests = array_values(array_filter($feed, fn($c) => ($c['entity_type'] ?? '') !== 'restaurant'));
        $rests     = array_values(array_filter($feed, fn($c) => ($c['entity_type'] ?? '') === 'restaurant'));

        if (!empty($rests)) {
            $interleaved = [];
            $ni = 0; $ri = 0; $slot = 0;
            while ($ni < count($nonRests) || $ri < count($rests)) {
                // First 3 slots and every position that isn't the 4th in its group → non-restaurant
                $useRest = ($slot >= 3) && ($slot % 4 === 3) && ($ri < count($rests)) && ($ni < count($nonRests));
                if ($useRest) {
                    $interleaved[] = $rests[$ri++];
                } elseif ($ni < count($nonRests)) {
                    $interleaved[] = $nonRests[$ni++];
                } else {
                    $interleaved[] = $rests[$ri++];
                }
                $slot++;
            }
            $feed = $interleaved;
        }

        // ── Ensure position 1 highlights a must-see (tier-1) non-restaurant if available ──
        if (!empty($feed) && (int)($feed[0]['tier'] ?? 99) > 1) {
            foreach ($feed as $idx => $card) {
                if ($idx === 0) {
                    continue;
                }
                $tier = (int)($card['tier'] ?? 99);
                $type = $card['entity_type'] ?? '';
                if ($tier <= 1 && $type !== 'restaurant') {
                    [$feed[0], $feed[$idx]] = [$feed[$idx], $feed[0]];
                    break;
                }
            }
        }

        // ── Update session state (authenticated users only) ─────────────
        if ($userId) {
            $this->updateSessionState($sessionId, $userId, $tripId, $feed, $recentCards, $sessionState);
        }

        return $feed;
    }

    // =========================================================================
    // MEMORY FEATURE COMPUTATION (Spec 11.8–11.16)
    // =========================================================================

    private function computeMemoryFeatures(
        array $c, array $slot, array $recentMemory, array $recentCards, array $sessionState
    ): array {
        $candidateId   = $c['candidate_item_id'];
        $anchorId      = $c['nearest_anchor_id']     ?? null;
        $momentFamily  = $c['moment_primary_family'] ?? null;
        $role          = $c['primary_role']          ?? null;
        $category      = $c['category_title']        ?? null;
        $structClass   = $c['structural_class']      ?? null;
        $isCollection  = (bool)($c['is_collection']  ?? false);

        // Item-level counts from recent memory
        $itemShownCount = $recentMemory[$candidateId]['count'] ?? 0;
        $lastShownMins  = $recentMemory[$candidateId]['mins_ago'] ?? 9999;

        // Structural counts from recent card window
        $sameAnchorCount    = 0;
        $sameMomentCount    = 0;
        $sameRoleCount      = 0;
        $sameCategoryCount  = 0;
        $sameCollCount      = 0;

        foreach ($recentCards as $rc) {
            if ($rc['anchor_id']      === $anchorId      && $anchorId)      $sameAnchorCount++;
            if ($rc['moment_family']  === $momentFamily  && $momentFamily)  $sameMomentCount++;
            if ($rc['primary_role']   === $role          && $role)          $sameRoleCount++;
            if ($rc['category']       === $category      && $category)      $sameCategoryCount++;
            if ($rc['is_collection']  && $isCollection)                     $sameCollCount++;
        }

        // ── Novelty score (Spec 11.13) ─────────────────────────────────────
        $itemNovelty     = 1 / (1 + $itemShownCount);
        $anchorNovelty   = 1 / (1 + $sameAnchorCount);
        $momentNovelty   = 1 / (1 + $sameMomentCount);
        $roleNovelty     = 1 / (1 + $sameRoleCount);
        $categoryNovelty = 1 / (1 + $sameCategoryCount);

        $noveltyScore = round(
            0.20 * $itemNovelty +
            0.20 * (1 / (1 + ($recentMemory[$candidateId]['district_count'] ?? 0))) +
            0.15 * $anchorNovelty +
            0.15 * $categoryNovelty +
            0.10 * 1.0 + // vibe novelty — simplified
            0.10 * $momentNovelty +
            0.10 * $roleNovelty,
            4
        );

        // ── Repetition risk score (Spec 11.14) ────────────────────────────
        $anchorNorm   = min($sameAnchorCount / 3, 1.0);
        $categoryNorm = min($sameCategoryCount / 3, 1.0);
        $momentNorm   = min($sameMomentCount / 2, 1.0);
        $roleNorm     = min($sameRoleCount / 2, 1.0);
        $collNorm     = min($sameCollCount / 2, 1.0);

        $repetitionRisk = round(
            0.20 * $anchorNorm +
            0.20 * $categoryNorm +
            0.15 * 0.0 + // vibe norm — simplified
            0.15 * $momentNorm +
            0.10 * $roleNorm +
            0.10 * $collNorm +
            0.10 * 0.0, // cluster norm — simplified
            4
        );

        // ── Cooldown penalty score (Spec 11.15) ───────────────────────────
        $itemCooldown = $lastShownMins < 9999
            ? exp(-$lastShownMins / self::ITEM_COOLDOWN_MINUTES)
            : 0.0;

        $cooldownPenalty = round(
            0.40 * $itemCooldown +
            0.25 * 0.0 + // anchor cooldown — simplified
            0.15 * 0.0 + // district cooldown — simplified
            0.10 * 0.0 + // collection cooldown
            0.10 * 0.0,  // cluster cooldown
            4
        );

        // ── Fatigue compatibility score (Spec 11.16) ──────────────────────
        $effortLoad     = (float)($c['effort_level_score']    ?? 0.5);
        $walkCost       = (float)($c['route_cost_score']      ?? 0.5);
        $cogWeight      = (float)($c['decision_friction_score'] ?? 0.3);

        $fatigueCompat = round(1 - (
            0.25 * $effortLoad +
            0.20 * 0.3 + // decision friction default
            0.20 * $walkCost +
            0.20 * $cogWeight +
            0.15 * $effortLoad
        ), 4);
        $fatigueCompat = max(0.0, min(1.0, $fatigueCompat));

        $currentFatigue = (float)($sessionState['current_fatigue_score'] ?? 0.3);
        $adjustedFatigueCompat = round(1 - abs($fatigueCompat - (1 - $currentFatigue)), 4);

        // ── Diversity bonus score (Spec 11.18) ────────────────────────────
        $recentWindow = array_slice($recentCards, -6);
        $recentCategories = array_column($recentWindow, 'category');
        $recentRoles      = array_column(array_slice($recentCards, -6), 'primary_role');
        $recentMoments    = array_column(array_slice($recentCards, -5), 'moment_family');

        $categoryBonus  = !in_array($category, $recentCategories) ? 1.0 : 0.5;
        $roleBonus      = !in_array($role, $recentRoles) ? 1.0 : 0.5;
        $momentBonus    = !in_array($momentFamily, $recentMoments) ? 1.0 : 0.5;

        $diversityBonus = round(
            0.25 * $categoryBonus +
            0.20 * 1.0 + // vibe bonus — simplified
            0.15 * 1.0 + // district bonus — simplified
            0.15 * $roleBonus +
            0.15 * 1.0 + // effort diversity — simplified
            0.10 * $momentBonus,
            4
        );

        return [
            'novelty_score'              => $noveltyScore,
            'repetition_risk_score'      => $repetitionRisk,
            'cooldown_penalty_score'     => $cooldownPenalty,
            'fatigue_compatibility_score'=> $adjustedFatigueCompat,
            'diversity_bonus_score'      => $diversityBonus,
            'same_anchor_count'          => $sameAnchorCount,
            'same_moment_family_count'   => $sameMomentCount,
            'same_category_count'        => $sameCategoryCount,
            'same_role_count'            => $sameRoleCount,
            'same_collection_count'      => $sameCollCount,
            'item_shown_count'           => $itemShownCount,
            'last_shown_minutes_ago'     => $lastShownMins,
        ];
    }

    // =========================================================================
    // SESSION FIT SCORE (Spec 11.19)
    // =========================================================================

    private function computeSessionFitScore(array $c, int $position, array $sessionState): float
    {
        $anchorStrength = (float)($c['structural_importance_score'] ?? 0.5);
        $explorationFit = (float)($c['discovery_fit_score']         ?? 0.5);
        $currentFatigue = (float)($sessionState['current_fatigue_score']       ?? 0.3);
        $currentExplore = (float)($sessionState['current_exploration_mode_score'] ?? 0.5);
        $currentConfidence = (float)($sessionState['current_decision_confidence_score'] ?? 0.5);

        // Anchor density target shifts with feed position
        $desiredAnchorDensity = match(true) {
            $position <= 5  => 0.70,
            $position <= 15 => 0.50,
            default         => 0.35,
        };

        $anchorDensityFit = 1 - abs($anchorStrength - $desiredAnchorDensity);
        $explorationMatch  = 1 - abs($explorationFit - $currentExplore);
        $confidenceMatch   = 1 - abs($anchorStrength - $currentConfidence);

        // Fatigue fit — light items score better when user fatigued
        $effortLoad = (float)($c['effort_level_score'] ?? 0.5);
        $fatigueFit = 1 - abs((1 - $effortLoad) - (1 - $currentFatigue));

        return round(
            0.25 * $anchorDensityFit +
            0.20 * $explorationMatch +
            0.20 * $fatigueFit +
            0.20 * $confidenceMatch +
            0.15 * 0.5, // progression fit — simplified
            4
        );
    }

    // =========================================================================
    // COLLECTION SUITABILITY (Spec 11.20)
    // =========================================================================

    private function computeCollectionSuitability(array $c, array $memFeatures, array $sessionState): float
    {
        if (!($c['is_collection'] ?? false)) return 0.5;

        $contextFit       = (float)($c['context_fit_score']   ?? 0.5);
        $lightness        = 1 - (float)($c['effort_level_score'] ?? 0.5);
        $novelty          = 1 / (1 + $memFeatures['same_collection_count']);
        $collDensity      = (float)($sessionState['current_collection_density_score'] ?? 0);
        $nonOveruse       = max(0.0, 1 - $collDensity);

        return round(
            0.30 * $contextFit +
            0.20 * 0.5 + // time fit — simplified
            0.20 * $lightness +
            0.15 * $novelty +
            0.15 * $nonOveruse,
            4
        );
    }

    // =========================================================================
    // FINAL ASSEMBLY SCORE (Spec 11.21)
    // =========================================================================

    private function computeFinalAssemblyScore(
        array $c, array $slot, array $memFeatures, array $sessionState, int $position
    ): float {
        $baseScore      = (float)($slot['slot_score_final'] ?? ($c['slot_score_final'] ?? 0));
        $novelty        = $memFeatures['novelty_score'];
        $repetition     = $memFeatures['repetition_risk_score'];
        $cooldown       = $memFeatures['cooldown_penalty_score'];
        $diversity      = $memFeatures['diversity_bonus_score'];
        $fatigue        = $memFeatures['fatigue_compatibility_score'];
        $sessionFit     = $this->computeSessionFitScore($c, $position, $sessionState);
        $collectionFit  = $this->computeCollectionSuitability($c, $memFeatures, $sessionState);

        $score = 0.45 * $baseScore
               + 0.15 * $novelty
               + 0.10 * $diversity
               + 0.10 * $sessionFit
               + 0.10 * $fatigue
               + 0.10 * $collectionFit
               - 0.15 * $repetition
               - 0.15 * $cooldown;

        return round(max(0.0, min(1.0, $score)), 4);
    }

    // =========================================================================
    // HARD SUPPRESSION RULES (Spec 11.22)
    // =========================================================================

    private function checkHardSuppression(
        array $c, array $slot, array $memFeatures, array $sessionState, array $recentCards
    ): ?string {
        // Rule 1 — exact item shown too recently
        if ($memFeatures['last_shown_minutes_ago'] < self::ITEM_COOLDOWN_MINUTES) {
            return 'ITEM_COOLDOWN';
        }

        // Rule 2 — same moment family overused
        if ($memFeatures['same_moment_family_count'] >= self::MAX_SAME_MOMENT_FAMILY) {
            return 'MOMENT_FAMILY_SATURATED';
        }

        // Rule 3 — collection overload
        $collDensity   = (float)($sessionState['current_collection_density_score'] ?? 0);
        $isCollection  = (bool)($c['is_collection'] ?? false);
        if ($isCollection && $collDensity > self::COLLECTION_DENSITY_CAP) {
            return 'COLLECTION_OVERLOAD';
        }

        // Rule 4 — same category saturated
        if ($memFeatures['same_category_count'] >= self::MAX_SAME_CATEGORY) {
            return 'CATEGORY_SATURATED';
        }

        // Rule 5 — anchor oversaturation
        $role = $c['primary_role'] ?? '';
        $isAnchorRole = in_array($role, ['CURRENT_ANCHOR', 'NEXT_ANCHOR']);
        if ($isAnchorRole && $memFeatures['same_anchor_count'] >= self::MAX_SAME_ANCHOR_ROLE) {
            return 'ANCHOR_OVERSATURATED';
        }

        // Rule 6 — low fatigue fit when user is very fatigued
        $currentFatigue = (float)($sessionState['current_fatigue_score'] ?? 0);
        if ($currentFatigue > 0.75 && $memFeatures['fatigue_compatibility_score'] < 0.30) {
            return 'LOW_FATIGUE_FIT';
        }

        return null;
    }

    // =========================================================================
    // CARD BUILDER
    // =========================================================================

    private function buildCard(
        array $c, array $slot, int $position, float $assemblyScore,
        string $daypart, Collection $allCandidates, array $memFeatures
    ): array {
        $nearby = $this->getNearbySupport($c, $allCandidates);

        return [
            'position'              => $position,
            'slot_type'             => $slot['slot_type']      ?? null,
            'slot_family'           => $slot['slot_family']    ?? null,
            'slot_goal'             => $slot['slot_goal']      ?? null,
            'planning_reason'       => $slot['planning_reason'] ?? null,

            // Core entity data
            'type'                  => $c['candidate_item_type'],
            'entity_id'             => $c['entity_id'],
            'entity_type'           => $c['entity_type'],
            'title'                 => $c['title'],
            'slug'                  => $c['slug'],
            'slugid'                => $c['slugid'],
            'image'                 => $c['img'],
            'rating'                => $c['avg_rating'],
            'review_count'          => $c['review_count'],
            'tier'                  => $c['tier'],
            'category'              => $c['category_title']    ?? null,
            'short_description'     => $c['short_desc']        ?? null,
            'address'               => $c['address']           ?? null,
            'lat'                   => $c['lat'],
            'lng'                   => $c['lng'],
            'duration_minutes'      => $c['duration_minutes']  ?? null,
            'cuisines'              => $c['cuisines']           ?? null,
            'price_range'           => $c['price_range']       ?? null,

            // Moment framing
            'moment_label_short'    => $c['moment_label_short']    ?? null,
            'moment_label_medium'   => $c['moment_label_medium']   ?? null,
            'moment_type'           => $c['moment_primary_type']   ?? null,
            'moment_family'         => $c['moment_primary_family'] ?? null,
            'moment_urgency'        => $c['moment_urgency_level']  ?? 0,
            'cta_style'             => $c['moment_cta_style']      ?? 'explore',

            // Role
            'primary_role'          => $c['primary_role']      ?? null,
            'role_group'            => $c['role_group']        ?? null,
            'role_confidence'       => $c['role_confidence']   ?? 0,

            // Scores
            'assembly_score'        => $assemblyScore,
            'slot_score_final'      => $c['slot_score_final']  ?? 0,
            'do_now_score'          => $c['do_now_score']      ?? 0,
            'opportunity_score'     => $c['opportunity_score'] ?? 0,
            'novelty_score'         => $memFeatures['novelty_score'],
            'diversity_score'       => $memFeatures['diversity_bonus_score'],

            // Route
            'distance_km'           => $c['distance_km']          ?? null,
            'travel_time_minutes'   => $c['travel_time_minutes']   ?? null,

            // Context strips (nearby)
            'nearby'                => $nearby,
        ];
    }

    // =========================================================================
    // NEARBY SUPPORT
    // =========================================================================

    private function getNearbySupport(array $c, Collection $allCandidates): array
    {
        $lat    = $c['lat'];
        $lng    = $c['lng'];
        $mainId = $c['candidate_item_id'];

        return $allCandidates
            ->where('is_eligible', true)
            ->filter(fn($n) => $n['candidate_item_id'] !== $mainId)
            ->filter(fn($n) => $this->haversine($lat, $lng, $n['lat'], $n['lng']) <= 0.5)
            ->sortByDesc('structural_importance_score')
            ->take(5)
            ->map(fn($n) => [
                'entity_id'   => $n['entity_id'],
                'entity_type' => $n['entity_type'],
                'title'       => $n['title'],
                'type'        => $n['candidate_item_type'],
                'tier'        => $n['tier'],
                'rating'      => $n['avg_rating'],
                'image'       => $n['img'],
                'slug'        => $n['slug'],
                'slugid'      => $n['slugid'],
            ])
            ->values()
            ->toArray();
    }

    // =========================================================================
    // BACKFILL
    // =========================================================================

    private function backfill(
        array $feed, Collection $allCandidates, array $recentMemory,
        int $startPos, array $ctx, Collection $candidates
    ): array {
        $usedIds = array_flip(array_column($feed, 'entity_id'));
        $daypart = $ctx['daypart'] ?? 'afternoon';

        $extras = $allCandidates
            ->where('is_eligible', true)
            ->filter(fn($c) => !isset($recentMemory[$c['candidate_item_id']]))
            ->filter(fn($c) => !isset($usedIds[$c['entity_id']]))
            ->sortByDesc('structural_importance_score')
            ->take(self::MAX_FEED_CARDS - count($feed));

        $pos = $startPos;
        foreach ($extras as $c) {
            $emptyMem = [
                'novelty_score' => 0.8, 'repetition_risk_score' => 0,
                'cooldown_penalty_score' => 0, 'fatigue_compatibility_score' => 0.7,
                'diversity_bonus_score' => 0.7,
            ];
            $feed[] = $this->buildCard($c, [
                'slot_type'        => 'DO_SOON_SLOT',
                'slot_family'      => 'DO_SOON',
                'slot_goal'        => 'backfill',
                'planning_reason'  => 'backfill',
                'slot_score_final' => $c['structural_importance_score'] ?? 0,
            ], $pos++, (float)($c['structural_importance_score'] ?? 0), $daypart, $candidates, $emptyMem);
        }

        return $feed;
    }

    // =========================================================================
    // SESSION STATE (Spec 11.25–11.26)
    // =========================================================================

    private function loadSessionState(string $sessionId): array
    {
        try {
            $row = DB::table('feed_session_state')->where('session_id', $sessionId)->first();
            return $row ? (array)$row : [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function updateSessionState(
        string $sessionId, ?int $userId, ?int $tripId,
        array $feed, array $recentCards, array $prevState
    ): void {
        try {
            $feedCount = count($feed);
            if ($feedCount === 0) return;

            // Anchor density (Spec 11.5)
            $anchorRoles = ['CURRENT_ANCHOR', 'NEXT_ANCHOR', 'COMPOSITE_ANCHOR', 'COLLECTIVE_DESTINATION'];
            $anchorWeights = [
                'CURRENT_ANCHOR'         => 1.0,
                'NEXT_ANCHOR'            => 0.9,
                'COMPOSITE_ANCHOR'       => 0.8,
                'COLLECTIVE_DESTINATION' => 0.7,
            ];

            $anchorDensity = 0.0;
            $collectionCount = 0;
            foreach ($recentCards as $rc) {
                $role = $rc['primary_role'] ?? '';
                $anchorDensity  += ($anchorWeights[$role] ?? 0);
                $collectionCount += (int)($rc['is_collection'] ?? 0);
            }
            $n = max(1, count($recentCards));
            $anchorDensity      = round($anchorDensity / $n, 4);
            $collectionDensity  = round($collectionCount / $n, 4);

            // Decision confidence (Spec 11.5)
            $avgAnchorStrength  = 0.0;
            foreach ($feed as $card) {
                $avgAnchorStrength += (float)($card['assembly_score'] ?? 0);
            }
            $decisionConfidence = round($avgAnchorStrength / max(1, $feedCount), 4);

            // Exploration mode — inverse of anchor density
            $explorationMode = round(max(0, 1 - $anchorDensity), 4);

            // Fatigue — based on effort density
            $prevFatigue   = (float)($prevState['current_fatigue_score'] ?? 0.3);
            $heavyItems    = array_filter($feed, fn($card) => ($card['do_now_score'] ?? 0) > 0.7);
            $heavyDensity  = count($heavyItems) / max(1, $feedCount);
            $newFatigue    = round(min(1.0, $prevFatigue * 0.7 + $heavyDensity * 0.3), 4);

            DB::table('feed_session_state')->updateOrInsert(
                ['session_id' => $sessionId],
                [
                    'user_id'                          => $userId,
                    'trip_id'                          => $tripId,
                    'current_feed_position'            => $feedCount,
                    'cards_rendered_count'             => $feedCount,
                    'current_anchor_density_score'     => $anchorDensity,
                    'current_collection_density_score' => $collectionDensity,
                    'current_decision_confidence_score'=> $decisionConfidence,
                    'current_exploration_mode_score'   => $explorationMode,
                    'current_fatigue_score'            => $newFatigue,
                    'last_feed_refresh_at'             => now(),
                    'created_at'                       => now(),
                    'updated_at'                       => now(),
                ]
            );
        } catch (\Throwable) {}
    }

    // =========================================================================
    // EXPOSURE MEMORY
    // =========================================================================

    private function loadRecentMemory(?int $userId, string $sessionId): array
    {
        try {
            $cutoff = now()->subMinutes(self::ITEM_COOLDOWN_MINUTES * 2);
            $rows = DB::table('feed_exposure_memory')
                ->where(function ($q) use ($userId, $sessionId) {
                    if ($userId) $q->where('user_id', $userId);
                    else         $q->where('session_id', $sessionId);
                })
                ->where('last_shown_at', '>=', $cutoff)
                ->get(['candidate_item_id', 'last_shown_at', 'exposure_count']);

            $memory = [];
            foreach ($rows as $row) {
                $minsAgo = now()->diffInMinutes($row->last_shown_at);
                $memory[$row->candidate_item_id] = [
                    'count'          => $row->exposure_count,
                    'mins_ago'       => $minsAgo,
                    'district_count' => 0,
                ];
            }
            return $memory;
        } catch (\Throwable) {
            return [];
        }
    }

    private function writeExposureMemory(
        string $sessionId, ?int $userId, ?int $tripId,
        array $c, array $slot, int $pos
    ): void {
        try {
            $existing = DB::table('feed_exposure_memory')
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->where('candidate_item_id', $c['candidate_item_id'])
                ->first();

            $count = ($existing->exposure_count ?? 0) + 1;

            DB::table('feed_exposure_memory')->updateOrInsert(
                [
                    'user_id'          => $userId,
                    'candidate_item_id'=> $c['candidate_item_id'],
                ],
                [
                    'session_id'          => $sessionId,
                    'trip_id'             => $tripId,
                    'candidate_item_type' => $c['candidate_item_type'],
                    'entity_id'           => $c['entity_id'],
                    'entity_type'         => $c['entity_type'],
                    'slot_type'           => $slot['slot_type']         ?? null,
                    'assigned_role'       => $c['primary_role']         ?? null,
                    'moment_family'       => $c['moment_primary_family'] ?? null,
                    'feed_position'       => $pos,
                    'was_rendered'        => 1,
                    'last_shown_at'       => now(),
                    'shown_at'            => now(),
                    'exposure_count'      => $count,
                    'updated_at'          => now(),
                    'created_at'          => now(),
                ]
            );
        } catch (\Throwable) {}
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
