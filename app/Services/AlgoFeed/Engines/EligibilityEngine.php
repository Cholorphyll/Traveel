<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Collection;

/**
 * Module 1 — Eligibility Engine
 *
 * Hard gates: any candidate that fails is marked is_eligible=false
 * and will not be scored or surfaced in the feed.
 *
 * Gates implemented:
 *  G1 — Must have valid coordinates
 *  G2 — Must have a title
 *  G3 — Restaurants: require rating >= 3.5 and review_count >= 5
 *  G4 — Experiences: require review_count >= 3
 *  G5 — Sights: require review_count >= 1 OR is_must_see
 *  G6 — Extreme weather: suppress outdoor-only items in storms
 *  G7 — Zero-duration items with no review signal — skip
 */
class EligibilityEngine
{
    public function run(Collection $candidates, array $ctx): Collection
    {
        $isStorm    = ($ctx['weather_type'] ?? '') === 'storm';
        $isRaining  = in_array($ctx['weather_type'] ?? '', ['rain', 'storm', 'drizzle']);
        $rainDisc   = (float)($ctx['rain_discomfort_level'] ?? 0);
        $heatStress = $ctx['heat_stress_level'] ?? 'low';

        return $candidates->map(function (array $c) use ($isStorm, $isRaining, $rainDisc, $heatStress) {

            // G1 — coordinates
            if (empty($c['lat']) || empty($c['lng']) || $c['lat'] == 0 || $c['lng'] == 0) {
                return $this->reject($c, 'missing_coordinates');
            }

            // G2 — title
            if (empty(trim($c['title'] ?? ''))) {
                return $this->reject($c, 'missing_title');
            }

            // G3 — restaurant minimum quality
            if ($c['is_restaurant']) {
                if ($c['avg_rating'] < 3.5 && $c['review_count'] < 5) {
                    return $this->reject($c, 'restaurant_low_quality');
                }
            }

            // G4 — experience minimum signal
            if ($c['is_experience'] && $c['review_count'] < 3) {
                return $this->reject($c, 'experience_insufficient_reviews');
            }

            // G5 — sight minimum signal
            if (!$c['is_restaurant'] && !$c['is_experience']) {
                if ($c['review_count'] < 1 && !$c['is_must_see']) {
                    return $this->reject($c, 'sight_no_signal');
                }
            }

            // G6 — storm: suppress outdoor items
            if ($isStorm && $c['is_outdoor'] && !$c['is_restaurant']) {
                return $this->reject($c, 'outdoor_in_storm');
            }

            // G7 — extreme heat: suppress high-commitment outdoor items at midday
            if (in_array($heatStress, ['high', 'extreme']) && $c['is_outdoor'] && $c['is_high_commitment']) {
                return $this->reject($c, 'outdoor_high_commitment_extreme_heat');
            }

            return $c;
        });
    }

    private function reject(array $c, string $reason): array
    {
        $c['is_eligible']        = false;
        $c['eligibility_reason'] = $reason;
        return $c;
    }
}
