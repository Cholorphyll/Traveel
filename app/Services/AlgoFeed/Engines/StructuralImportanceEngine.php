<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 3 — Structural Importance Engine
 *
 * Determines the structural weight of each item in the destination.
 * Computes: anchor_score, composite_anchor_score, collective_destination_score,
 *           trip_importance_score, structural_importance_score.
 *
 * All scores: 0.0 – 1.0 (normalised)
 */
class StructuralImportanceEngine
{
    public function run(Collection $candidates, array $ctx): Collection
    {
        // Build location-level stats once for normalisation
        $eligible       = $candidates->where('is_eligible', true);
        $maxReviews     = max(1, $eligible->max('review_count'));
        $maxRating      = max(0.1, $eligible->max('avg_rating'));
        $totalEligible  = max(1, $eligible->count());

        return $candidates->map(function (array $c) use ($maxReviews, $maxRating, $totalEligible) {
            if (!$c['is_eligible']) return $c;

            // ── Normalised base signals ───────────────────────────────────────
            $normReviews = $this->logNorm($c['review_count'], $maxReviews);
            $normRating  = $c['avg_rating'] / 5.0;
            $tierWeight  = $this->tierWeight($c['tier']);
            $mustSee     = $c['is_must_see'] ? 1.0 : 0.0;
            $popNorm     = min(1.0, ($c['popularity_raw'] ?? 0) / 100.0);

            // ── Anchor Score ─────────────────────────────────────────────────
            // High structural pull: tier 1, must-see, landmark, high review signal
            $anchorScore = round(
                0.35 * $tierWeight   +
                0.25 * $normReviews  +
                0.20 * $mustSee      +
                0.20 * $normRating   ,
            4);

            // ── Composite Anchor Score ────────────────────────────────────────
            // Areas / multi-experience zones – approximated by walkability proxy
            $walkProxy       = $c['is_outdoor'] ? 0.7 : 0.3;
            $clusterProxy    = 0.5; // would come from entity_structural_base; use prior
            $compositeAnchor = round(
                0.35 * $clusterProxy +
                0.25 * $walkProxy    +
                0.20 * $normReviews  +
                0.20 * $tierWeight   ,
            4);

            // ── Collective Destination Score ──────────────────────────────────
            // Places people go together / group scenes
            $socialProxy        = ($c['is_nightlife'] || $c['is_restaurant']) ? 0.7 : 0.3;
            $collectiveScore    = round(
                0.30 * $clusterProxy +
                0.25 * $socialProxy  +
                0.25 * $normRating   +
                0.20 * $normReviews  ,
            4);

            // ── Trip Importance Score ─────────────────────────────────────────
            // Must-do quality of an item for this destination
            $rarityProxy = $c['tier'] === 1 ? 0.9 : ($c['tier'] === 2 ? 0.6 : 0.3);
            $destSig     = $c['is_must_see'] ? 0.9 : min(1.0, $popNorm + $normRating * 0.4);
            $tripImp     = round(
                0.45 * $tierWeight +
                0.25 * $normReviews+
                0.15 * $rarityProxy+
                0.15 * $destSig    ,
            4);

            // ── Structural Importance Score (master) ──────────────────────────
            // Per spec: weighted review signal + tier + anchor + trip importance
            $weightedRating  = $c['weighted_rating'] ?? ($c['avg_rating'] * log(max(1, $c['review_count']) + 1));
            $normWeighted    = min(1.0, $weightedRating / (5.0 * log($maxReviews + 1)));

            $structuralScore = round(
                0.35 * $normWeighted +
                0.25 * $tierWeight   +
                0.20 * $anchorScore  +
                0.20 * $tripImp      ,
            4);

            $c['anchor_score']                   = min(1.0, $anchorScore);
            $c['composite_anchor_score']         = min(1.0, $compositeAnchor);
            $c['collective_destination_score']   = min(1.0, $collectiveScore);
            $c['trip_importance_score']          = min(1.0, $tripImp);
            $c['structural_importance_score']    = min(1.0, $structuralScore);
            $c['destination_signature_score']    = $destSig;
            $c['rarity_score']                   = $rarityProxy;

            // Derived item-behaviour signals (used by Role Assignment)
            $c['wow_factor_score']               = $c['tier'] === 1 ? min(1.0, $normRating * 1.3) : $normRating * 0.6;
            $c['local_character_score']          = min(1.0, $popNorm * 0.6 + $tierWeight * 0.4);
            $c['dwell_quality']                  = $this->dwellQuality($c['duration_minutes'] ?? 60, $c['avg_rating']);
            $c['review_strength']                = min(1.0, $normReviews * 0.5 + $normRating * 0.5);
            $c['uniqueness']                     = $rarityProxy;
            $c['popularity_score']               = $popNorm;

            return $c;
        });
    }

    private function tierWeight(int $tier): float
    {
        return match ($tier) {
            1 => 1.00,
            2 => 0.75,
            3 => 0.45,
            4 => 0.20,
            default => 0.10,
        };
    }

    private function logNorm(int $reviews, int $maxReviews): float
    {
        if ($reviews <= 0) return 0.0;
        return min(1.0, log($reviews + 1) / log($maxReviews + 1));
    }

    private function dwellQuality(int $minutes, float $rating): float
    {
        $dwellScore = match (true) {
            $minutes >= 120 => 1.0,
            $minutes >= 60  => 0.75,
            $minutes >= 30  => 0.50,
            default         => 0.30,
        };
        return min(1.0, $dwellScore * 0.6 + ($rating / 5.0) * 0.4);
    }
}
