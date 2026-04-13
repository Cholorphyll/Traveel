<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Module 11 — Feed Assembly & Memory Layer
 *
 * Takes the framed, ranked slots and assembles the final feed by:
 *   A) Diversity Engine    — prevents category/district/role repetition
 *   B) Exposure Memory     — suppresses recently shown items
 *   C) Session Evolution   — orientation → expansion → adaptive phases
 *   D) Collection Governance — controls when collection cards appear
 *
 * Returns the final feed array ready for the frontend.
 */
class FeedAssemblyEngine
{
    // Max cards per feed
    private const MAX_FEED_CARDS = 18;

    // Diversity limits
    private const MAX_SAME_TYPE_IN_ROW   = 2;
    private const MAX_RESTAURANTS_TOTAL  = 4;
    private const MAX_RECOVERY_TOTAL     = 3;
    private const MAX_SAME_ROLE_TOTAL    = 3;

    // Memory cooldown (minutes) — items shown recently are suppressed
    private const EXPOSURE_COOLDOWN_MINUTES = 120;

    public function assemble(array $framedSlots, Collection $allCandidates, array $ctx): array
    {
        $sessionId  = $ctx['session_id'] ?? uniqid();
        $userId     = $ctx['user_id']    ?? null;
        $tripId     = $ctx['trip_id']    ?? null;
        $feedPos    = (int)($ctx['current_feed_position'] ?? 0);
        $daypart    = $ctx['daypart']    ?? 'afternoon';

        // Load exposure memory for this user/session
        $exposedIds = $this->loadExposureMemory($userId, $sessionId);

        $feed             = [];
        $restaurantCount  = 0;
        $recoveryCount    = 0;
        $roleCounts       = [];
        $recentTypes      = [];
        $position         = 1;

        foreach ($framedSlots as $slot) {
            if ($position > self::MAX_FEED_CARDS) break;

            $c = $slot['candidate'];

            // ── B: Exposure Memory — skip recently shown items ───────────────
            if (isset($exposedIds[$c['candidate_item_id']])) continue;

            // ── A: Diversity Engine ──────────────────────────────────────────

            // Restaurant cap
            if ($c['is_restaurant'] && $restaurantCount >= self::MAX_RESTAURANTS_TOTAL) continue;

            // Recovery cap
            $isRecovery = in_array($slot['slot_family'] ?? '', ['RECOVERY']);
            if ($isRecovery && $recoveryCount >= self::MAX_RECOVERY_TOTAL) continue;

            // Same role cap
            $role = $c['primary_role'] ?? 'DISCOVERY_ITEM';
            if (($roleCounts[$role] ?? 0) >= self::MAX_SAME_ROLE_TOTAL) continue;

            // Same type in a row
            $type = $c['candidate_item_type'];
            $lastTwo = array_values(array_slice($recentTypes, -2));
            if (count($lastTwo) === 2 && count(array_unique($lastTwo)) === 1 && $lastTwo[0] === $type) continue;

            // ── C: Session Evolution — phase-based weight adjustments ─────────
            $phase        = $this->computeFeedPhase($position);
            $assemblyScore = $this->computeAssemblyScore($c, $slot, $phase);

            // ── Build feed card ───────────────────────────────────────────────
            $card = $this->buildCard($c, $slot, $position, $assemblyScore, $daypart, $allCandidates);

            $feed[] = $card;

            // Update counters
            if ($c['is_restaurant']) $restaurantCount++;
            if ($isRecovery)         $recoveryCount++;
            $roleCounts[$role]  = ($roleCounts[$role] ?? 0) + 1;
            $recentTypes[]      = $type;
            $position++;

            // Persist exposure
            $this->writeExposureMemory($sessionId, $userId, $tripId, $c, $position - 1);
        }

        // ── Backfill empty positions with high-quality candidates ─────────────
        if ($position <= 6) {
            $feed = $this->backfill($feed, $allCandidates, $exposedIds, $position, $ctx);
        }

        return $feed;
    }

    // ── Feed phase (spec 11.26) ───────────────────────────────────────────────

    private function computeFeedPhase(int $pos): string
    {
        if ($pos <= 5)  return 'ORIENTATION';
        if ($pos <= 15) return 'EXPANSION';
        return 'ADAPTIVE';
    }

    // ── Assembly score: phase-weighted final ranking ──────────────────────────

    private function computeAssemblyScore(array $c, array $slot, string $phase): float
    {
        $slotScore  = (float)($slot['slot_score_final']  ?? 0);
        $roleScore  = (float)($c['role_selection_score'] ?? 0);
        $novelty    = (float)($c['uniqueness']           ?? 0);
        $diversity  = (float)($c['discovery_fit_score']  ?? 0.3);

        return match ($phase) {
            'ORIENTATION' => 0.50 * $slotScore + 0.30 * $roleScore + 0.10 * $novelty  + 0.10 * $diversity,
            'EXPANSION'   => 0.40 * $slotScore + 0.25 * $roleScore + 0.20 * $novelty  + 0.15 * $diversity,
            'ADAPTIVE'    => 0.30 * $slotScore + 0.20 * $roleScore + 0.30 * $novelty  + 0.20 * $diversity,
            default       => 0.40 * $slotScore + 0.30 * $roleScore + 0.30 * $novelty,
        };
    }

    // ── Build the final feed card ─────────────────────────────────────────────

    private function buildCard(
        array $c, array $slot, int $position, float $assemblyScore,
        string $daypart, Collection $allCandidates
    ): array {
        // Nearby support items (same area, not selected)
        $nearby = $this->getNearbySupport($c, $allCandidates);

        return [
            'position'              => $position,
            'slot_type'             => $slot['slot_type']    ?? null,
            'slot_family'           => $slot['slot_family']  ?? null,
            'slot_goal'             => $slot['slot_goal']    ?? null,
            'planning_reason'       => $slot['planning_reason'] ?? null,

            // Core item data
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
            'category'              => $c['category_title'] ?? null,
            'short_description'     => $c['short_desc']     ?? null,
            'address'               => $c['address']        ?? null,
            'lat'                   => $c['lat'],
            'lng'                   => $c['lng'],
            'duration_minutes'      => $c['duration_minutes'] ?? null,
            'cuisines'              => $c['cuisines']        ?? null,
            'price_range'           => $c['price_range']    ?? null,

            // Moment framing
            'moment_label_short'    => $c['moment_label_short']  ?? null,
            'moment_label_medium'   => $c['moment_label_medium'] ?? null,
            'moment_type'           => $c['moment_primary_type'] ?? null,
            'moment_family'         => $c['moment_primary_family'] ?? null,
            'moment_urgency'        => $c['moment_urgency_level'] ?? 0,
            'cta_style'             => $c['moment_cta_style']    ?? 'explore',

            // Role & scoring metadata (for debug / A-B testing)
            'primary_role'          => $c['primary_role']         ?? null,
            'role_group'            => $c['role_group']           ?? null,
            'role_confidence'       => $c['role_confidence']      ?? 0,
            'assembly_score'        => round($assemblyScore, 4),
            'slot_score_final'      => $c['slot_score_final']      ?? 0,
            'do_now_score'          => $c['do_now_score']          ?? 0,
            'opportunity_score'     => $c['opportunity_score']     ?? 0,
            'distance_km'           => $c['distance_km']           ?? null,
            'travel_time_minutes'   => $c['travel_time_minutes']   ?? null,

            // Nearby support cards
            'nearby'                => $nearby,
        ];
    }

    // ── Nearby support items ──────────────────────────────────────────────────

    private function getNearbySupport(array $c, Collection $allCandidates): array
    {
        $lat     = $c['lat'];
        $lng     = $c['lng'];
        $mainId  = $c['candidate_item_id'];

        $nearby = $allCandidates
            ->where('is_eligible', true)
            ->filter(fn($n) => $n['candidate_item_id'] !== $mainId)
            ->filter(function ($n) use ($lat, $lng) {
                $d = $this->haversine($lat, $lng, $n['lat'], $n['lng']);
                return $d <= 0.5; // within 500m
            })
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

        return $nearby;
    }

    // ── Backfill if feed is sparse ────────────────────────────────────────────

    private function backfill(
        array $feed, Collection $allCandidates, array $exposedIds, int $startPos, array $ctx
    ): array {
        $usedIds  = array_flip(array_column($feed, 'entity_id'));
        $daypart  = $ctx['daypart'] ?? 'afternoon';

        $backfillCandidates = $allCandidates
            ->where('is_eligible', true)
            ->filter(fn($c) => !isset($exposedIds[$c['candidate_item_id']]))
            ->filter(fn($c) => !in_array($c['entity_id'], array_keys($usedIds)))
            ->sortByDesc('structural_importance_score')
            ->take(self::MAX_FEED_CARDS - count($feed));

        $pos = $startPos;
        foreach ($backfillCandidates as $c) {
            $feed[] = $this->buildCard($c, [
                'slot_type'      => 'DO_SOON_SLOT',
                'slot_family'    => 'DO_SOON',
                'slot_goal'      => 'Backfill — high importance item',
                'planning_reason'=> 'backfill',
                'slot_score_final' => $c['structural_importance_score'] ?? 0,
            ], $pos++, (float)($c['structural_importance_score'] ?? 0), $daypart, $allCandidates);
        }

        return $feed;
    }

    // ── Exposure memory ───────────────────────────────────────────────────────

    private function loadExposureMemory(?int $userId, string $sessionId): array
    {
        if (!$userId) return [];
        try {
            $cutoff = now()->subMinutes(self::EXPOSURE_COOLDOWN_MINUTES);
            $rows = DB::table('feed_exposure_memory')
                ->where('user_id', $userId)
                ->where('last_shown_at', '>=', $cutoff)
                ->pluck('candidate_item_id')
                ->toArray();
            return array_fill_keys($rows, true);
        } catch (\Throwable) {
            return [];
        }
    }

    private function writeExposureMemory(
        string $sessionId, ?int $userId, ?int $tripId, array $c, int $pos
    ): void {
        if (!$userId) return;
        try {
            $existing = DB::table('feed_exposure_memory')
                ->where('user_id', $userId)
                ->where('candidate_item_id', $c['candidate_item_id'])
                ->first();
            $newCount = ($existing->exposure_count ?? 0) + 1;
            DB::table('feed_exposure_memory')->updateOrInsert(
                ['user_id' => $userId, 'candidate_item_id' => $c['candidate_item_id']],
                [
                    'session_id'          => $sessionId,
                    'trip_id'             => $tripId,
                    'candidate_item_type' => $c['candidate_item_type'],
                    'entity_id'           => $c['entity_id'],
                    'entity_type'         => $c['entity_type'],
                    'feed_position'       => $pos,
                    'assigned_role'       => $c['primary_role'] ?? null,
                    'slot_type'           => $c['slot_type']    ?? null,
                    'was_rendered'        => 1,
                    'last_shown_at'       => now(),
                    'exposure_count'      => $newCount,
                ]
            );
        } catch (\Throwable) {}
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
