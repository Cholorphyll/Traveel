<?php

namespace App\Services\AlgoPopulators;

use Illuminate\Support\Facades\DB;

/**
 * EntityOpportunityProfilePopulator
 * 
 * Populates entity_opportunity_profile table from Sight, Restaurant, Experience.
 * Implements the exact logic from Module 4 - OPPORTUNITY ENGINE spec.
 * 
 * Spec: Lines 389-1000 of Algo Engines(3).txt
 */
class EntityOpportunityProfilePopulator
{
    protected $progressCallback = null;
    protected int $batchSize = 50;
    protected bool $skipTruncate = false;

    /**
     * Set a progress callback function
     */
    public function setProgressCallback(callable $callback): self
    {
        $this->progressCallback = $callback;
        return $this;
    }

    /**
     * Skip truncation (for resuming)
     */
    public function skipTruncate(bool $skip = true): self
    {
        $this->skipTruncate = $skip;
        return $this;
    }

    /**
     * Log progress message
     */
    protected function log(string $message): void
    {
        if ($this->progressCallback) {
            ($this->progressCallback)($message);
        }
    }

    /**
     * Run the full population pipeline
     */
    public function populate(?int $locationId = null): array
    {
        $stats = [
            'sights_processed' => 0,
            'restaurants_processed' => 0,
            'experiences_processed' => 0,
        ];

        // Clear existing profiles
        if (!$this->skipTruncate) {
            $this->log("Clearing existing profiles...");
            DB::table('entity_opportunity_profile')->truncate();
        } else {
            $this->log("Skipping truncate (resume mode)...");
        }

        // Process each entity type
        $this->log("Processing sights...");
        $stats['sights_processed'] = $this->processSights($locationId);
        gc_collect_cycles();

        $this->log("Processing restaurants...");
        $stats['restaurants_processed'] = $this->processRestaurants($locationId);
        gc_collect_cycles();

        $this->log("Processing experiences...");
        $stats['experiences_processed'] = $this->processExperiences($locationId);
        gc_collect_cycles();

        $this->log("Phase 4 complete!");
        return $stats;
    }

    // =========================================================================
    // SIGHTS (OPTIMIZED with cursor and batch)
    // =========================================================================

    protected function processSights(?int $locationId): int
    {
        $query = DB::table('Sight as s')
            ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
            ->select([
                's.SightId',
                's.Title',
                's.CategoryId',
                'c.Title as category_title',
                's.KnownFor',
                's.Averagerating',
                's.ReviewCount',
                's.duration',
                's.LocationId',
            ])
            ->where('s.Status', 1);

        if ($locationId) {
            $query->where('s.LocationId', $locationId);
        }

        $count = 0;
        $batch = [];

        foreach ($query->cursor() as $s) {
            $batch[] = $this->buildSightProfile($s);

            if (count($batch) >= $this->batchSize) {
                DB::table('entity_opportunity_profile')->insert($batch);
                $count += count($batch);
                $this->log("    Processed {$count} sights...");
                unset($batch);
                $batch = [];
                gc_collect_cycles();
            }
        }

        if (!empty($batch)) {
            DB::table('entity_opportunity_profile')->insert($batch);
            $count += count($batch);
        }

        gc_collect_cycles();
        return $count;
    }

    protected function buildSightProfile($s): array
    {
        $category = strtolower($s->category_title ?? '');
        $knownFor = strtolower($s->KnownFor ?? '');

        // Time/daypart scores (Spec: Lines 401-453)
        $sunriseScore = $this->computeSunriseRelevance($category, $knownFor);
        $sunsetScore = $this->computeSunsetRelevance($category, $knownFor);
        $morningScore = $this->computeMorningRelevance($category, $knownFor);
        $afternoonScore = $this->computeAfternoonRelevance($category);
        $eveningScore = $this->computeEveningRelevance($category, $knownFor);
        $nightScore = $this->computeNightRelevance($category, $knownFor);

        // Weather/environment scores (Spec: Lines 502-563)
        $indoorScore = $this->computeIndoorSuitability(null, $category);
        $outdoorScore = $this->computeOutdoorDependency(null, $category);
        $clearWeatherBonus = $this->computeClearWeatherBonus($category, $knownFor);
        $rainyPenalty = $this->computeRainyWeatherPenalty($category);

        // Practicality scores (Spec: Lines 591-723)
        $crowdAvoidance = $this->computeCrowdAvoidanceValue($s->ReviewCount);
        $earlyDayAdvantage = $this->computeEarlyDayAdvantage($category, $crowdAvoidance);
        $duration = $this->parseDuration($s->duration);
        $halfDayFit = $this->computeHalfDayFitScore($duration);
        $quickStopFit = $this->computeQuickStopFitScore($duration);
        $shortCommitmentFit = $this->computeShortCommitmentFitScore($quickStopFit, $category);

        // Comfort scores (Spec: Lines 836-892)
        $hydrationFit = $this->computeHydrationFitScore('sight', $category);
        $coolingBreakFit = $this->computeCoolingBreakFitScore($indoorScore, 0, $hydrationFit);
        $shadeBreakFit = $this->computeShadeBreakFitScore($category, $knownFor);
        $sitDownRest = $this->computeSitDownRestScore('sight', $category);

        // Social scores (Spec: Lines 895-948)
        $drinksFit = 0.0;
        $strollFit = $this->computeStrollFitScore($category);
        $nightlifeWarmup = $this->computeNightlifeWarmupFitScore($category, $knownFor);

        return [
            'entity_type' => 'sight',
            'entity_id' => $s->SightId,
            'sunrise_relevance_score' => $sunriseScore,
            'sunset_relevance_score' => $sunsetScore,
            'morning_relevance_score' => $morningScore,
            'afternoon_relevance_score' => $afternoonScore,
            'evening_relevance_score' => $eveningScore,
            'night_relevance_score' => $nightScore,
            'indoor_suitability_score' => $indoorScore,
            'outdoor_dependency_score' => $outdoorScore,
            'clear_weather_bonus_score' => $clearWeatherBonus,
            'rainy_weather_penalty_score' => $rainyPenalty,
            'crowd_avoidance_value' => $crowdAvoidance,
            'early_day_advantage_score' => $earlyDayAdvantage,
            'half_day_fit_score' => $halfDayFit,
            'quick_stop_fit_score' => $quickStopFit,
            'short_commitment_fit_score' => $shortCommitmentFit,
            'breakfast_fit_score' => 0,
            'lunch_fit_score' => 0,
            'coffee_fit_score' => 0,
            'dinner_fit_score' => 0,
            'late_night_fit_score' => 0,
            'hydration_fit_score' => $hydrationFit,
            'cooling_break_fit_score' => $coolingBreakFit,
            'shade_break_fit_score' => $shadeBreakFit,
            'sit_down_rest_score' => $sitDownRest,
            'drinks_fit_score' => $drinksFit,
            'stroll_fit_score' => $strollFit,
            'nightlife_warmup_fit_score' => $nightlifeWarmup,
            'typical_visit_duration_mins' => $duration,
            'profile_confidence' => $this->computeProfileConfidence($s->Averagerating, $s->ReviewCount),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // =========================================================================
    // RESTAURANTS (OPTIMIZED with cursor and batch)
    // =========================================================================

    protected function processRestaurants(?int $locationId): int
    {
        $query = DB::table('Restaurant as r')
            ->select([
                'r.RestaurantId',
                'r.Title',
                'r.category',
                'r.cuisines',
                'r.features',
                'r.meals',
                'r.Averagerating',
                'r.ReviewCount',
                'r.LocationId',
            ])
            ->where('r.IsActive', 1);

        if ($locationId) {
            $query->where('r.LocationId', $locationId);
        }

        $count = 0;
        $batch = [];

        foreach ($query->cursor() as $r) {
            $batch[] = $this->buildRestaurantProfile($r);

            if (count($batch) >= $this->batchSize) {
                DB::table('entity_opportunity_profile')->insert($batch);
                $count += count($batch);
                $this->log("    Processed {$count} restaurants...");
                unset($batch);
                $batch = [];
                gc_collect_cycles();
            }
        }

        if (!empty($batch)) {
            DB::table('entity_opportunity_profile')->insert($batch);
            $count += count($batch);
        }

        gc_collect_cycles();
        return $count;
    }

    protected function buildRestaurantProfile($r): array
    {
        $category = strtolower($r->category ?? '');
        $cuisines = strtolower($r->cuisines ?? '');
        $features = strtolower($r->features ?? '');
        $meals = strtolower($r->meals ?? '');
        $combined = $category . ' ' . $cuisines . ' ' . $features . ' ' . $meals;

        // Time/daypart scores
        $sunriseScore = 0.0;
        $sunsetScore = str_contains($combined, 'rooftop') ? 0.85 : 0.20;
        $morningScore = $this->computeRestaurantMorningRelevance($combined);
        $afternoonScore = 0.60;
        $eveningScore = $this->computeRestaurantEveningRelevance($combined);
        $nightScore = $this->computeRestaurantNightRelevance($combined);

        // Weather/environment scores
        $indoorScore = 0.85; // Restaurants are naturally indoor-friendly
        $outdoorScore = str_contains($combined, 'outdoor') || str_contains($combined, 'terrace') ? 0.60 : 0.20;
        $clearWeatherBonus = str_contains($combined, 'rooftop') || str_contains($combined, 'terrace') ? 0.70 : 0.20;
        $rainyPenalty = 0.10;

        // Practicality scores
        $crowdAvoidance = $this->computeCrowdAvoidanceValue($r->ReviewCount);
        $earlyDayAdvantage = 0.30;
        $duration = 60; // Default restaurant duration
        $halfDayFit = 0.20;
        $quickStopFit = str_contains($combined, 'cafe') || str_contains($combined, 'bakery') ? 0.75 : 0.40;
        $shortCommitmentFit = str_contains($combined, 'cafe') || str_contains($combined, 'fast') ? 0.80 : 0.50;

        // Food fit scores (Spec: Lines 731-831)
        $breakfastFit = $this->computeBreakfastFitScore($combined);
        $lunchFit = $this->computeLunchFitScore($combined);
        $coffeeFit = $this->computeCoffeeFitScore($combined);
        $dinnerFit = $this->computeDinnerFitScore($combined);
        $lateNightFit = $this->computeLateNightFitScore($combined);

        // Comfort scores
        $hydrationFit = $this->computeHydrationFitScore('restaurant', $category);
        $coolingBreakFit = $this->computeCoolingBreakFitScore($indoorScore, $sitDownRest = 0.7, $hydrationFit);
        $shadeBreakFit = 0.60;
        $sitDownRest = $this->computeSitDownRestScore('restaurant', $category);

        // Social scores (Spec: Lines 895-948)
        $drinksFit = $this->computeDrinksFitScore($combined);
        $strollFit = 0.15;
        $nightlifeWarmup = $this->computeNightlifeWarmupFitScore($category, $features);

        return [
            'entity_type' => 'restaurant',
            'entity_id' => $r->RestaurantId,
            'sunrise_relevance_score' => $sunriseScore,
            'sunset_relevance_score' => $sunsetScore,
            'morning_relevance_score' => $morningScore,
            'afternoon_relevance_score' => $afternoonScore,
            'evening_relevance_score' => $eveningScore,
            'night_relevance_score' => $nightScore,
            'indoor_suitability_score' => $indoorScore,
            'outdoor_dependency_score' => $outdoorScore,
            'clear_weather_bonus_score' => $clearWeatherBonus,
            'rainy_weather_penalty_score' => $rainyPenalty,
            'crowd_avoidance_value' => $crowdAvoidance,
            'early_day_advantage_score' => $earlyDayAdvantage,
            'half_day_fit_score' => $halfDayFit,
            'quick_stop_fit_score' => $quickStopFit,
            'short_commitment_fit_score' => $shortCommitmentFit,
            'breakfast_fit_score' => $breakfastFit,
            'lunch_fit_score' => $lunchFit,
            'coffee_fit_score' => $coffeeFit,
            'dinner_fit_score' => $dinnerFit,
            'late_night_fit_score' => $lateNightFit,
            'hydration_fit_score' => $hydrationFit,
            'cooling_break_fit_score' => $coolingBreakFit,
            'shade_break_fit_score' => $shadeBreakFit,
            'sit_down_rest_score' => $sitDownRest,
            'drinks_fit_score' => $drinksFit,
            'stroll_fit_score' => $strollFit,
            'nightlife_warmup_fit_score' => $nightlifeWarmup,
            'typical_visit_duration_mins' => $duration,
            'profile_confidence' => $this->computeProfileConfidence($r->Averagerating, $r->ReviewCount),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // =========================================================================
    // EXPERIENCES (OPTIMIZED with cursor and batch)
    // =========================================================================

    protected function processExperiences(?int $locationId): int
    {
        $query = DB::table('Experience as e')
            ->select([
                'e.ExperienceId',
                'e.Name',
                'e.Duration',
                'e.ViatorAggregationRating',
                'e.ViatorReviewCount',
                'e.LocationId',
            ])
            ->where('e.IsActive', 1);

        if ($locationId) {
            $query->where('e.LocationId', $locationId);
        }

        $count = 0;
        $batch = [];

        foreach ($query->cursor() as $e) {
            $batch[] = $this->buildExperienceProfile($e);

            if (count($batch) >= $this->batchSize) {
                DB::table('entity_opportunity_profile')->insert($batch);
                $count += count($batch);
                $this->log("    Processed {$count} experiences...");
                unset($batch);
                $batch = [];
                gc_collect_cycles();
            }
        }

        if (!empty($batch)) {
            DB::table('entity_opportunity_profile')->insert($batch);
            $count += count($batch);
        }

        gc_collect_cycles();
        return $count;
    }

    protected function buildExperienceProfile($e): array
    {
        // Experience table doesn't have Category column, use defaults
        $duration = $this->parseDuration($e->Duration);

        // Time/daypart scores (defaults for experiences)
        $sunriseScore = 0.10;
        $sunsetScore = 0.20;
        $morningScore = 0.40;
        $afternoonScore = 0.60;
        $eveningScore = 0.50;
        $nightScore = 0.25;

        // Weather/environment scores (default outdoor)
        $indoorScore = 0.30;
        $outdoorScore = 0.80;
        $clearWeatherBonus = 0.70;
        $rainyPenalty = 0.60;

        // Practicality scores
        $crowdAvoidance = $this->computeCrowdAvoidanceValue($e->ViatorReviewCount);
        $earlyDayAdvantage = 0.35;
        $halfDayFit = $this->computeHalfDayFitScore($duration);
        $quickStopFit = $this->computeQuickStopFitScore($duration);
        $shortCommitmentFit = $this->computeShortCommitmentFitScore($quickStopFit, 'experience');

        // Food fit scores (experiences typically don't serve food)
        $breakfastFit = 0.10;
        $lunchFit = 0.15;
        $coffeeFit = 0.10;
        $dinnerFit = 0.15;
        $lateNightFit = 0.10;

        // Comfort scores
        $hydrationFit = 0.30;
        $coolingBreakFit = 0.35;
        $shadeBreakFit = 0.30;
        $sitDownRest = 0.30;

        // Social scores
        $drinksFit = 0.20;
        $strollFit = 0.20;
        $nightlifeWarmup = 0.20;

        return [
            'entity_type' => 'experience',
            'entity_id' => $e->ExperienceId,
            'sunrise_relevance_score' => $sunriseScore,
            'sunset_relevance_score' => $sunsetScore,
            'morning_relevance_score' => $morningScore,
            'afternoon_relevance_score' => $afternoonScore,
            'evening_relevance_score' => $eveningScore,
            'night_relevance_score' => $nightScore,
            'indoor_suitability_score' => $indoorScore,
            'outdoor_dependency_score' => $outdoorScore,
            'clear_weather_bonus_score' => $clearWeatherBonus,
            'rainy_weather_penalty_score' => $rainyPenalty,
            'crowd_avoidance_value' => $crowdAvoidance,
            'early_day_advantage_score' => $earlyDayAdvantage,
            'half_day_fit_score' => $halfDayFit,
            'quick_stop_fit_score' => $quickStopFit,
            'short_commitment_fit_score' => $shortCommitmentFit,
            'breakfast_fit_score' => $breakfastFit,
            'lunch_fit_score' => $lunchFit,
            'coffee_fit_score' => $coffeeFit,
            'dinner_fit_score' => $dinnerFit,
            'late_night_fit_score' => $lateNightFit,
            'hydration_fit_score' => $hydrationFit,
            'cooling_break_fit_score' => $coolingBreakFit,
            'shade_break_fit_score' => $shadeBreakFit,
            'sit_down_rest_score' => $sitDownRest,
            'drinks_fit_score' => $drinksFit,
            'stroll_fit_score' => $strollFit,
            'nightlife_warmup_fit_score' => $nightlifeWarmup,
            'typical_visit_duration_mins' => $duration,
            'profile_confidence' => $this->computeProfileConfidence($e->ViatorAggregationRating, $e->ViatorReviewCount),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // =========================================================================
    // COMPUTATION METHODS - Following exact spec formulas
    // =========================================================================

    /**
     * Compute sunrise_relevance_score
     * Spec: Lines 401-429
     */
    protected function computeSunriseRelevance(string $category, string $knownFor): float
    {
        $score = 0.0;

        // Category bonus
        if (in_array($category, ['viewpoint', 'park', 'waterfront', 'scenic walk', 'bridge'])) {
            $score += 0.60;
        }

        // Tag bonuses
        if (str_contains($knownFor, 'sunrise')) $score += 0.25;
        if (str_contains($knownFor, 'view') || str_contains($knownFor, 'panoramic')) $score += 0.20;

        return min(1.0, $score);
    }

    /**
     * Compute sunset_relevance_score
     * Spec: Lines 435-453
     */
    protected function computeSunsetRelevance(string $category, string $knownFor): float
    {
        $score = 0.0;

        // Category bonus
        if (in_array($category, ['viewpoint', 'waterfront', 'scenic walk', 'bridge', 'plaza'])) {
            $score += 0.65;
        }

        // Tag bonuses
        if (str_contains($knownFor, 'sunset')) $score += 0.25;
        if (str_contains($knownFor, 'view') || str_contains($knownFor, 'skyline')) $score += 0.20;

        return min(1.0, $score);
    }

    /**
     * Compute morning_relevance_score
     * Spec: Lines 458-464
     */
    protected function computeMorningRelevance(string $category, string $knownFor): float
    {
        if (in_array($category, ['market', 'park', 'landmark', 'viewpoint'])) {
            return 0.75;
        }
        if (str_contains($knownFor, 'morning') || str_contains($knownFor, 'early')) {
            return 0.65;
        }
        return 0.40;
    }

    /**
     * Compute afternoon_relevance_score
     * Spec: Lines 468-474
     */
    protected function computeAfternoonRelevance(string $category): float
    {
        if (in_array($category, ['museum', 'gallery', 'church', 'indoor attraction'])) {
            return 0.80;
        }
        return 0.50;
    }

    /**
     * Compute evening_relevance_score
     * Spec: Lines 478-484
     */
    protected function computeEveningRelevance(string $category, string $knownFor): float
    {
        if (in_array($category, ['waterfront', 'plaza', 'bridge'])) {
            return 0.70;
        }
        if (str_contains($knownFor, 'sunset') || str_contains($knownFor, 'evening')) {
            return 0.75;
        }
        return 0.45;
    }

    /**
     * Compute night_relevance_score
     * Spec: Lines 488-494
     */
    protected function computeNightRelevance(string $category, string $knownFor): float
    {
        if (str_contains($category, 'night') || str_contains($category, 'club')) {
            return 0.90;
        }
        if (str_contains($knownFor, 'illuminated') || str_contains($knownFor, 'lit')) {
            return 0.80;
        }
        return 0.25;
    }

    /**
     * Compute indoor_suitability_score
     * Spec: Lines 506-530
     */
    protected function computeIndoorSuitability(?int $isIndoor, string $category): float
    {
        if ($isIndoor === 1) return 0.90;

        if (in_array($category, ['museum', 'gallery', 'church', 'indoor attraction'])) {
            return 0.85;
        }

        return 0.20;
    }

    /**
     * Compute outdoor_dependency_score
     * Spec: Lines 543-563
     */
    protected function computeOutdoorDependency(?int $isIndoor, string $category): float
    {
        if ($isIndoor === 0) return 0.75;

        if (in_array($category, ['park', 'viewpoint', 'waterfront', 'scenic walk', 'bridge'])) {
            return 0.90;
        }

        return 0.20;
    }

    /**
     * Compute clear_weather_bonus_score
     * Spec: Lines 568-577
     */
    protected function computeClearWeatherBonus(string $category, string $knownFor): float
    {
        if (in_array($category, ['viewpoint', 'park', 'waterfront', 'garden'])) {
            return 0.70;
        }
        if (str_contains($knownFor, 'scenic') || str_contains($knownFor, 'view')) {
            return 0.60;
        }
        return 0.20;
    }

    /**
     * Compute rainy_weather_penalty_score
     * Spec: Lines 580-588
     */
    protected function computeRainyWeatherPenalty(string $category): float
    {
        if (in_array($category, ['viewpoint', 'waterfront', 'beach', 'park'])) {
            return 0.80;
        }
        return 0.20;
    }

    /**
     * Compute crowd_avoidance_value
     * Spec: Lines 596-625
     */
    protected function computeCrowdAvoidanceValue(?int $reviewCount): float
    {
        $reviews = $reviewCount ?? 0;

        if ($reviews >= 5000) return 0.90;
        if ($reviews >= 2000) return 0.75;
        if ($reviews >= 500) return 0.55;
        if ($reviews >= 100) return 0.35;

        return 0.20;
    }

    /**
     * Compute early_day_advantage_score
     * Spec: Lines 629-645
     */
    protected function computeEarlyDayAdvantage(string $category, float $crowdAvoidance): float
    {
        $score = 0.0;

        if (in_array($category, ['landmark', 'viewpoint', 'market', 'park'])) {
            $score += 0.45;
        }

        if ($crowdAvoidance >= 0.70) {
            $score += 0.35;
        }

        return min(1.0, $score);
    }

    /**
     * Compute half_day_fit_score
     * Spec: Lines 651-670
     */
    protected function computeHalfDayFitScore(int $duration): float
    {
        if ($duration >= 180) return 0.95;
        if ($duration >= 120) return 0.75;
        if ($duration >= 90) return 0.55;

        return 0.15;
    }

    /**
     * Compute quick_stop_fit_score
     * Spec: Lines 675-694
     */
    protected function computeQuickStopFitScore(int $duration): float
    {
        if ($duration <= 20) return 0.95;
        if ($duration <= 40) return 0.80;
        if ($duration <= 60) return 0.60;

        return 0.20;
    }

    /**
     * Compute short_commitment_fit_score
     * Spec: Lines 699-723
     */
    protected function computeShortCommitmentFitScore(float $quickStopFit, string $category): float
    {
        $lowFrictionCategories = ['cafe', 'bakery', 'plaza', 'market', 'viewpoint'];
        $categoryBonus = in_array($category, $lowFrictionCategories) ? 0.30 : 0.10;

        return min(1.0,
            0.50 * $quickStopFit +
            0.30 * $categoryBonus +
            0.20 * 0.5 // no_reservation_bonus placeholder
        );
    }

    /**
     * Compute breakfast_fit_score
     * Spec: Lines 735-751
     */
    protected function computeBreakfastFitScore(string $combined): float
    {
        if (str_contains($combined, 'breakfast') || str_contains($combined, 'brunch') || str_contains($combined, 'bakery')) {
            return 0.90;
        }
        if (str_contains($combined, 'cafe') || str_contains($combined, 'coffee')) {
            return 0.65;
        }
        return 0.20;
    }

    /**
     * Compute lunch_fit_score
     * Spec: Lines 756-772
     */
    protected function computeLunchFitScore(string $combined): float
    {
        if (str_contains($combined, 'lunch') || str_contains($combined, 'casual') || str_contains($combined, 'market')) {
            return 0.85;
        }
        if (str_contains($combined, 'restaurant')) {
            return 0.60;
        }
        return 0.30;
    }

    /**
     * Compute coffee_fit_score
     * Spec: Lines 777-791
     */
    protected function computeCoffeeFitScore(string $combined): float
    {
        if (str_contains($combined, 'coffee') || str_contains($combined, 'cafe') || str_contains($combined, 'tea') || str_contains($combined, 'bakery')) {
            return 0.92;
        }
        return 0.15;
    }

    /**
     * Compute dinner_fit_score
     * Spec: Lines 796-811
     */
    protected function computeDinnerFitScore(string $combined): float
    {
        if (str_contains($combined, 'fine dining') || str_contains($combined, 'dinner') || str_contains($combined, 'evening')) {
            return 0.90;
        }
        if (str_contains($combined, 'restaurant')) {
            return 0.70;
        }
        return 0.25;
    }

    /**
     * Compute late_night_fit_score
     * Spec: Lines 816-831
     */
    protected function computeLateNightFitScore(string $combined): float
    {
        if (str_contains($combined, 'late night') || str_contains($combined, 'bar') || str_contains($combined, 'dessert') || str_contains($combined, 'street food')) {
            return 0.85;
        }
        return 0.10;
    }

    /**
     * Compute hydration_fit_score
     * Spec: Lines 840-852
     */
    protected function computeHydrationFitScore(string $entityType, string $category): float
    {
        if ($entityType === 'restaurant') {
            if (str_contains($category, 'cafe') || str_contains($category, 'juice') || str_contains($category, 'tea')) {
                return 0.85;
            }
            return 0.60;
        }
        return 0.25;
    }

    /**
     * Compute cooling_break_fit_score
     * Spec: Lines 855-870
     */
    protected function computeCoolingBreakFitScore(float $indoorScore, float $sitDownRest, float $hydrationFit): float
    {
        return min(1.0,
            0.50 * $indoorScore +
            0.30 * $sitDownRest +
            0.20 * $hydrationFit
        );
    }

    /**
     * Compute shade_break_fit_score
     * Spec: Lines 873-881
     */
    protected function computeShadeBreakFitScore(string $category, string $knownFor): float
    {
        if (str_contains($category, 'arcade') || str_contains($category, 'market')) {
            return 0.75;
        }
        if (str_contains($knownFor, 'shaded') || str_contains($knownFor, 'covered')) {
            return 0.70;
        }
        return 0.30;
    }

    /**
     * Compute sit_down_rest_score
     * Spec: Lines 884-892
     */
    protected function computeSitDownRestScore(string $entityType, string $category): float
    {
        if ($entityType === 'restaurant') {
            if (str_contains($category, 'cafe') || str_contains($category, 'tea')) {
                return 0.85;
            }
            return 0.70;
        }
        if ($entityType === 'sight') {
            if (str_contains($category, 'museum') || str_contains($category, 'gallery')) {
                return 0.50;
            }
        }
        return 0.30;
    }

    /**
     * Compute drinks_fit_score
     * Spec: Lines 899-915
     */
    protected function computeDrinksFitScore(string $combined): float
    {
        if (str_contains($combined, 'bar') || str_contains($combined, 'cocktail') || str_contains($combined, 'pub') || str_contains($combined, 'wine')) {
            return 0.90;
        }
        return 0.10;
    }

    /**
     * Compute stroll_fit_score
     * Spec: Lines 920-936
     */
    protected function computeStrollFitScore(string $category): float
    {
        if (in_array($category, ['scenic walk', 'waterfront', 'plaza', 'neighborhood', 'bridge', 'park'])) {
            return 0.88;
        }
        return 0.20;
    }

    /**
     * Compute nightlife_warmup_fit_score
     * Spec: Lines 941-948
     */
    protected function computeNightlifeWarmupFitScore(string $category, string $features): float
    {
        $combined = $category . ' ' . $features;

        if (str_contains($combined, 'rooftop') || str_contains($combined, 'lounge') || str_contains($combined, 'terrace')) {
            return 0.75;
        }
        return 0.20;
    }

    /**
     * Restaurant-specific morning relevance
     */
    protected function computeRestaurantMorningRelevance(string $combined): float
    {
        if (str_contains($combined, 'breakfast') || str_contains($combined, 'brunch') || str_contains($combined, 'bakery')) {
            return 0.90;
        }
        if (str_contains($combined, 'cafe') || str_contains($combined, 'coffee')) {
            return 0.75;
        }
        return 0.30;
    }

    /**
     * Restaurant-specific evening relevance
     */
    protected function computeRestaurantEveningRelevance(string $combined): float
    {
        if (str_contains($combined, 'fine dining') || str_contains($combined, 'dinner')) {
            return 0.85;
        }
        if (str_contains($combined, 'restaurant')) {
            return 0.70;
        }
        return 0.40;
    }

    /**
     * Restaurant-specific night relevance
     */
    protected function computeRestaurantNightRelevance(string $combined): float
    {
        if (str_contains($combined, 'bar') || str_contains($combined, 'pub') || str_contains($combined, 'late night')) {
            return 0.85;
        }
        return 0.30;
    }

    /**
     * Compute profile confidence based on data quality
     */
    protected function computeProfileConfidence(?float $rating, ?int $reviewCount): float
    {
        $ratingConfidence = $rating ? min(1.0, $rating / 5.0) : 0.3;
        $reviewConfidence = $reviewCount ? min(1.0, log($reviewCount + 1) / 7) : 0.3;

        return round(0.5 * $ratingConfidence + 0.5 * $reviewConfidence, 3);
    }

    /**
     * Parse duration string to minutes
     */
    protected function parseDuration(?string $raw): int
    {
        if (empty($raw)) return 60;

        if (is_numeric($raw)) return (int)$raw;

        $hours = 0;
        $mins = 0;

        if (preg_match('/(\d+)\s*h/i', $raw, $m)) $hours = (int)$m[1];
        if (preg_match('/(\d+)\s*m/i', $raw, $m)) $mins = (int)$m[1];

        $total = $hours * 60 + $mins;

        return $total > 0 ? $total : 60;
    }
}
