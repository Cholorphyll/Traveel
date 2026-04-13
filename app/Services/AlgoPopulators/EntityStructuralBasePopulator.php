<?php

namespace App\Services\AlgoPopulators;

use Illuminate\Support\Facades\DB;

/**
 * EntityStructuralBasePopulator
 * 
 * Populates entity_structural_base table from Sight, Restaurant, Experience.
 * Implements the exact logic from Module 3 - STRUCTURAL IMPORTANCE ENGINE spec.
 * 
 * Optimized with batch processing and progress callbacks.
 */
class EntityStructuralBasePopulator
{
    protected $progressCallback = null;
    protected int $batchSize = 500;
    protected bool $skipTruncate = false;

    /**
     * Set a progress callback function
     * Usage: $populator->setProgressCallback(fn($msg) => $this->info($msg));
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
            'sights_inserted' => 0,
            'restaurants_inserted' => 0,
            'experiences_inserted' => 0,
            'priors_updated' => 0,
            'percentiles_computed' => 0,
            'density_computed' => 0,
        ];

        // Step A: Truncate and insert normalized entities
        if (!$this->skipTruncate) {
            $this->log("Truncating entity_structural_base...");
            DB::table('entity_structural_base')->truncate();
        } else {
            $this->log("Skipping truncate (resume mode)...");
        }

        $this->log("Inserting sights (batch mode)...");
        $stats['sights_inserted'] = $this->insertSightsBatch($locationId);

        $this->log("Inserting restaurants (batch mode)...");
        $stats['restaurants_inserted'] = $this->insertRestaurantsBatch($locationId);

        $this->log("Inserting experiences (batch mode)...");
        $stats['experiences_inserted'] = $this->insertExperiencesBatch($locationId);

        // Step B: Update category priors
        $this->log("Updating category priors...");
        $stats['priors_updated'] = $this->updateCategoryPriors();

        // Step C: Compute percentiles
        $this->log("Computing percentiles...");
        $stats['percentiles_computed'] = $this->computePercentilesBatch($locationId);

        // Step D: Compute density scores
        $this->log("Computing density scores...");
        $stats['density_computed'] = $this->computeDensityScoresBatch($locationId);

        // Update timestamp
        DB::table('entity_structural_base')->update(['computed_at' => now()]);

        $this->log("Phase 1 complete!");
        return $stats;
    }

    // =========================================================================
    // STEP A: Insert normalized entities (BATCH MODE)
    // =========================================================================

    /**
     * Insert sights using batch inserts
     */
    protected function insertSightsBatch(?int $locationId): int
    {
        $query = DB::table('Sight as s')
            ->leftJoin('Category as c', 's.CategoryId', '=', 'c.CategoryId')
            ->select([
                's.SightId as entity_id',
                's.LocationId as location_id',
                's.Title as title',
                's.Latitude as lat',
                's.Longitude as lng',
                'c.Title as category',
                's.KnownFor as sub_category',
                's.Averagerating as aggregate_rating',
                's.ReviewCount as review_count',
                's.popularity_score as popularity_score',
                's.WeightedRating as recommendation_score',
            ])
            ->whereNotNull('s.Latitude')
            ->whereNotNull('s.Longitude')
            ->where('s.Status', 1);

        if ($locationId) {
            $query->where('s.LocationId', $locationId);
        }

        $total = 0;
        $batch = [];

        foreach ($query->cursor() as $row) {
            $batch[] = [
                'entity_type' => 'sight',
                'entity_id' => $row->entity_id,
                'location_id' => $row->location_id,
                'title' => $row->title,
                'lat' => $row->lat,
                'lng' => $row->lng,
                'category' => $this->normalizeCategory($row->category),
                'sub_category' => $row->sub_category,
                'aggregate_rating' => $row->aggregate_rating,
                'review_count' => $row->review_count ?? 0,
                'popularity_score' => $row->popularity_score,
                'recommendation_score' => $row->recommendation_score,
                'computed_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $this->batchSize) {
                DB::table('entity_structural_base')->insert($batch);
                $total += count($batch);
                $this->log("  Inserted {$total} sights...");
                $batch = [];
            }
        }

        // Insert remaining
        if (!empty($batch)) {
            DB::table('entity_structural_base')->insert($batch);
            $total += count($batch);
        }

        return $total;
    }

    /**
     * Insert restaurants using batch inserts
     */
    protected function insertRestaurantsBatch(?int $locationId): int
    {
        $query = DB::table('Restaurant as r')
            ->select([
                'r.RestaurantId as entity_id',
                'r.LocationId as location_id',
                'r.Title as title',
                'r.Latitude as lat',
                'r.Longitude as lng',
                'r.category as category',
                'r.cuisines as sub_category',
                'r.Averagerating as aggregate_rating',
                'r.ReviewCount as review_count',
                'r.PopularityIndex as popularity_score',
            ])
            ->whereNotNull('r.Latitude')
            ->whereNotNull('r.Longitude')
            ->where('r.IsActive', 1);

        if ($locationId) {
            $query->where('r.LocationId', $locationId);
        }

        $total = 0;
        $batch = [];

        foreach ($query->cursor() as $row) {
            $batch[] = [
                'entity_type' => 'restaurant',
                'entity_id' => $row->entity_id,
                'location_id' => $row->location_id,
                'title' => $row->title,
                'lat' => $row->lat,
                'lng' => $row->lng,
                'category' => $this->normalizeRestaurantCategory($row->category, $row->sub_category),
                'sub_category' => $row->sub_category,
                'aggregate_rating' => $row->aggregate_rating,
                'review_count' => $row->review_count ?? 0,
                'popularity_score' => $row->popularity_score,
                'recommendation_score' => null,
                'computed_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $this->batchSize) {
                DB::table('entity_structural_base')->insert($batch);
                $total += count($batch);
                $this->log("  Inserted {$total} restaurants...");
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('entity_structural_base')->insert($batch);
            $total += count($batch);
        }

        return $total;
    }

    /**
     * Insert experiences using batch inserts
     */
    protected function insertExperiencesBatch(?int $locationId): int
    {
        $query = DB::table('Experience as e')
            ->select([
                'e.ExperienceId as entity_id',
                'e.LocationId as location_id',
                'e.Name as title',
                'e.Latitude as lat',
                'e.Longitude as lng',
                'e.ViatorAggregationRating as aggregate_rating',
                'e.ViatorReviewCount as review_count',
                'e.popularity_score as popularity_score',
            ])
            ->whereNotNull('e.Latitude')
            ->whereNotNull('e.Longitude')
            ->where('e.IsActive', 1);

        if ($locationId) {
            $query->where('e.LocationId', $locationId);
        }

        $total = 0;
        $batch = [];

        foreach ($query->cursor() as $row) {
            $batch[] = [
                'entity_type' => 'experience',
                'entity_id' => $row->entity_id,
                'location_id' => $row->location_id,
                'title' => $row->title,
                'lat' => $row->lat,
                'lng' => $row->lng,
                'category' => 'experience', // No Category column, use default
                'sub_category' => null,
                'aggregate_rating' => $row->aggregate_rating,
                'review_count' => $row->review_count ?? 0,
                'popularity_score' => $row->popularity_score,
                'recommendation_score' => null,
                'computed_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $this->batchSize) {
                DB::table('entity_structural_base')->insert($batch);
                $total += count($batch);
                $this->log("  Inserted {$total} experiences...");
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('entity_structural_base')->insert($batch);
            $total += count($batch);
        }

        return $total;
    }

    // =========================================================================
    // STEP B: Update category priors
    // Spec: Lines 626-642 of Algo Engines(2).txt
    // =========================================================================

    protected function updateCategoryPriors(): int
    {
        // First ensure category_structural_priors is populated
        $this->ensureCategoryPriorsExist();

        // Update entity_structural_base with priors
        $affected = DB::statement("
            UPDATE entity_structural_base esb
            LEFT JOIN category_structural_priors csp ON esb.category = csp.category
            SET 
                esb.category_anchor_prior = COALESCE(csp.category_anchor_prior, 0.25),
                esb.category_uniqueness_prior = COALESCE(csp.category_uniqueness_prior, 0.25)
        ");

        return $affected ?? 0;
    }

    /**
     * Ensure category_structural_priors table has default values
     * Spec: Lines 385-417 of Algo Engines(2).txt
     */
    protected function ensureCategoryPriorsExist(): void
    {
        $priors = [
            // High anchor categories
            ['category' => 'museum', 'category_anchor_prior' => 0.90, 'category_uniqueness_prior' => 0.70],
            ['category' => 'monument', 'category_anchor_prior' => 0.95, 'category_uniqueness_prior' => 0.85],
            ['category' => 'landmark', 'category_anchor_prior' => 1.00, 'category_uniqueness_prior' => 0.90],
            ['category' => 'palace', 'category_anchor_prior' => 0.95, 'category_uniqueness_prior' => 0.85],
            ['category' => 'church', 'category_anchor_prior' => 0.70, 'category_uniqueness_prior' => 0.50],
            ['category' => 'cathedral', 'category_anchor_prior' => 0.85, 'category_uniqueness_prior' => 0.70],
            ['category' => 'castle', 'category_anchor_prior' => 0.90, 'category_uniqueness_prior' => 0.80],
            ['category' => 'viewpoint', 'category_anchor_prior' => 0.75, 'category_uniqueness_prior' => 0.60],
            ['category' => 'park', 'category_anchor_prior' => 0.55, 'category_uniqueness_prior' => 0.40],
            ['category' => 'market', 'category_anchor_prior' => 0.65, 'category_uniqueness_prior' => 0.55],
            ['category' => 'bridge', 'category_anchor_prior' => 0.70, 'category_uniqueness_prior' => 0.55],
            ['category' => 'waterfront', 'category_anchor_prior' => 0.60, 'category_uniqueness_prior' => 0.45],
            ['category' => 'plaza', 'category_anchor_prior' => 0.50, 'category_uniqueness_prior' => 0.35],
            ['category' => 'gallery', 'category_anchor_prior' => 0.70, 'category_uniqueness_prior' => 0.55],
            ['category' => 'aquarium', 'category_anchor_prior' => 0.65, 'category_uniqueness_prior' => 0.50],
            ['category' => 'zoo', 'category_anchor_prior' => 0.70, 'category_uniqueness_prior' => 0.45],
            
            // Experience categories
            ['category' => 'river_cruise', 'category_anchor_prior' => 0.75, 'category_uniqueness_prior' => 0.65],
            ['category' => 'walking_tour', 'category_anchor_prior' => 0.50, 'category_uniqueness_prior' => 0.35],
            ['category' => 'bus_tour', 'category_anchor_prior' => 0.40, 'category_uniqueness_prior' => 0.25],
            ['category' => 'food_tour', 'category_anchor_prior' => 0.55, 'category_uniqueness_prior' => 0.45],
            ['category' => 'wine_tasting', 'category_anchor_prior' => 0.60, 'category_uniqueness_prior' => 0.50],
            ['category' => 'cooking_class', 'category_anchor_prior' => 0.50, 'category_uniqueness_prior' => 0.45],
            
            // Restaurant categories
            ['category' => 'destination_restaurant', 'category_anchor_prior' => 0.70, 'category_uniqueness_prior' => 0.60],
            ['category' => 'fine_dining', 'category_anchor_prior' => 0.55, 'category_uniqueness_prior' => 0.45],
            ['category' => 'restaurant', 'category_anchor_prior' => 0.20, 'category_uniqueness_prior' => 0.15],
            ['category' => 'cafe', 'category_anchor_prior' => 0.25, 'category_uniqueness_prior' => 0.20],
            ['category' => 'bar', 'category_anchor_prior' => 0.35, 'category_uniqueness_prior' => 0.30],
            ['category' => 'pub', 'category_anchor_prior' => 0.35, 'category_uniqueness_prior' => 0.25],
            ['category' => 'cocktail_bar', 'category_anchor_prior' => 0.40, 'category_uniqueness_prior' => 0.35],
            ['category' => 'nightclub', 'category_anchor_prior' => 0.30, 'category_uniqueness_prior' => 0.25],
            
            // Default fallback
            ['category' => 'attraction', 'category_anchor_prior' => 0.40, 'category_uniqueness_prior' => 0.30],
        ];

        foreach ($priors as $prior) {
            DB::table('category_structural_priors')->updateOrInsert(
                ['category' => $prior['category']],
                [
                    'category_anchor_prior' => $prior['category_anchor_prior'],
                    'category_uniqueness_prior' => $prior['category_uniqueness_prior'],
                    'updated_at' => now(),
                ]
            );
        }
    }

    // =========================================================================
    // STEP C: Compute percentiles (BATCH MODE with raw SQL)
    // Spec: Lines 442-460 of Algo Engines(2).txt
    // =========================================================================

    protected function computePercentilesBatch(?int $locationId): int
    {
        // Use raw SQL for efficient percentile calculation
        $locationClause = $locationId ? "WHERE location_id = {$locationId}" : "";

        $affected = DB::statement("
            UPDATE entity_structural_base e
            JOIN (
                SELECT 
                    id,
                    location_id,
                    review_count,
                    PERCENT_RANK() OVER (
                        PARTITION BY location_id 
                        ORDER BY review_count ASC
                    ) as pct_rank
                FROM entity_structural_base
                WHERE review_count IS NOT NULL
                " . ($locationId ? "AND location_id = {$locationId}" : "") . "
            ) ranked ON e.id = ranked.id
            SET e.city_review_percentile = ROUND(ranked.pct_rank, 4)
        ");

        // Count updated rows
        $count = DB::table('entity_structural_base')
            ->whereNotNull('city_review_percentile')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->count();

        $this->log("  Computed percentiles for {$count} entities");
        return $count;
    }

    // =========================================================================
    // STEP D: Compute density scores (BATCH MODE)
    // Spec: Lines 463-517 of Algo Engines(2).txt
    // =========================================================================

    protected function computeDensityScoresBatch(?int $locationId): int
    {
        // Pre-compute city-level stats per location
        $this->log("  Pre-computing city-level category densities...");
        $this->computeCityCategoryDensities($locationId);

        // Get all entities with coordinates, process in chunks
        $query = DB::table('entity_structural_base')
            ->select(['id', 'lat', 'lng', 'category', 'location_id'])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId));

        $totalUpdated = 0;
        $batch = [];

        foreach ($query->cursor() as $entity) {
            // Compute local_density_score (entities within 250m, 500m, 900m)
            $nearby250 = $this->countNearbyEntitiesOptimized($entity->lat, $entity->lng, 250, $entity->location_id);
            $nearby500 = $this->countNearbyEntitiesOptimized($entity->lat, $entity->lng, 500, $entity->location_id);
            $nearby900 = $this->countNearbyEntitiesOptimized($entity->lat, $entity->lng, 900, $entity->location_id);

            // Get max counts for normalization (cached per location)
            $max250 = max(1, $this->getLocationEntityCount($entity->location_id) / 50);
            $max500 = max(1, $max250 * 2);
            $max900 = max(1, $max250 * 4);

            // Formula from spec: 0.45 * 250m + 0.35 * 500m + 0.20 * 900m
            $localDensity = min(1.0,
                0.45 * min(1.0, $nearby250 / $max250) +
                0.35 * min(1.0, $nearby500 / $max500) +
                0.20 * min(1.0, $nearby900 / $max900)
            );

            // Compute local_category_density (same category within 500m)
            $sameCategoryNearby = $this->countNearbyEntitiesOptimized($entity->lat, $entity->lng, 500, $entity->location_id, $entity->category);
            $localCategoryDensity = min(1.0, $sameCategoryNearby / max(1, $nearby500));

            // City category density is pre-computed
            $cityCategoryDensity = $this->getCityCategoryDensity($entity->location_id, $entity->category);

            $batch[] = [
                'id' => $entity->id,
                'local_density_score' => round($localDensity, 4),
                'local_category_density' => round($localCategoryDensity, 4),
                'city_category_density' => round($cityCategoryDensity, 4),
            ];

            if (count($batch) >= $this->batchSize) {
                $this->updateDensityBatch($batch);
                $totalUpdated += count($batch);
                $this->log("  Computed density for {$totalUpdated} entities...");
                $batch = [];
            }
        }

        // Process remaining
        if (!empty($batch)) {
            $this->updateDensityBatch($batch);
            $totalUpdated += count($batch);
        }

        return $totalUpdated;
    }

    // Cache for location entity counts
    protected array $locationEntityCounts = [];

    protected function getLocationEntityCount(int $locationId): int
    {
        if (!isset($this->locationEntityCounts[$locationId])) {
            $this->locationEntityCounts[$locationId] = DB::table('entity_structural_base')
                ->where('location_id', $locationId)
                ->count();
        }
        return $this->locationEntityCounts[$locationId];
    }

    // Cache for city category densities
    protected array $cityCategoryDensities = [];

    protected function computeCityCategoryDensities(?int $locationId): void
    {
        $query = DB::table('entity_structural_base')
            ->select('location_id', 'category', DB::raw('COUNT(*) as count'))
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->groupBy('location_id', 'category');

        foreach ($query->get() as $row) {
            $total = $this->getLocationEntityCount($row->location_id);
            $this->cityCategoryDensities[$row->location_id][$row->category] = $total > 0 ? $row->count / $total : 0;
        }
    }

    protected function getCityCategoryDensity(int $locationId, string $category): float
    {
        return $this->cityCategoryDensities[$locationId][$category] ?? 0;
    }

    /**
     * Update density scores in batch using CASE WHEN
     */
    protected function updateDensityBatch(array $batch): void
    {
        $ids = implode(',', array_column($batch, 'id'));

        $localDensityCases = [];
        $localCatDensityCases = [];
        $cityCatDensityCases = [];

        foreach ($batch as $item) {
            $id = (int)$item['id'];
            $localDensityCases[] = "WHEN id = {$id} THEN {$item['local_density_score']}";
            $localCatDensityCases[] = "WHEN id = {$id} THEN {$item['local_category_density']}";
            $cityCatDensityCases[] = "WHEN id = {$id} THEN {$item['city_category_density']}";
        }

        DB::statement("
            UPDATE entity_structural_base
            SET 
                local_density_score = CASE " . implode(' ', $localDensityCases) . " END,
                local_category_density = CASE " . implode(' ', $localCatDensityCases) . " END,
                city_category_density = CASE " . implode(' ', $cityCatDensityCases) . " END
            WHERE id IN ({$ids})
        ");
    }

    /**
     * Count entities within radius using optimized bounding box + distance filter
     */
    protected function countNearbyEntitiesOptimized(float $lat, float $lng, int $radiusMeters, int $locationId, ?string $category = null): int
    {
        // Approximate degree distance (1 degree ~ 111km at equator)
        $radiusKm = $radiusMeters / 1000;
        $deltaLat = $radiusKm / 111.0;
        $deltaLng = $radiusKm / (111.0 * cos(deg2rad($lat)));

        // Use raw SQL for efficiency - count within bounding box + haversine filter
        $categoryClause = $category ? "AND category = ?" : "";
        $params = [$lat - $deltaLat, $lat + $deltaLat, $lng - $deltaLng, $lng + $deltaLng, $locationId, $lat, $lat, $lng, $radiusKm];

        if ($category) {
            array_splice($params, 5, 0, [$category]);
        }

        $sql = "
            SELECT COUNT(*) as cnt FROM entity_structural_base
            WHERE lat BETWEEN ? AND ?
            AND lng BETWEEN ? AND ?
            AND location_id = ?
            {$categoryClause}
            AND (
                6371 * acos(
                    LEAST(1, GREATEST(-1, 
                        COS(RADIANS(?)) * COS(RADIANS(lat)) * 
                        COS(RADIANS(lng) - RADIANS(?)) + 
                        SIN(RADIANS(?)) * SIN(RADIANS(lat))
                    ))
                )
            ) <= ?
        ";

        $result = DB::select($sql, $params);
        return max(0, (int)($result[0]->cnt ?? 0) - 1); // Exclude self
    }

    // =========================================================================
    // Category normalization helpers
    // =========================================================================

    protected function normalizeCategory(?string $raw): string
    {
        if (!$raw) return 'attraction';

        $map = [
            'art museum' => 'museum',
            'science museum' => 'museum',
            'history museum' => 'museum',
            'natural history museum' => 'museum',
            'museum' => 'museum',
            'monument' => 'monument',
            'landmark' => 'landmark',
            'viewpoint' => 'viewpoint',
            'park' => 'park',
            'garden' => 'park',
            'church' => 'church',
            'cathedral' => 'cathedral',
            'castle' => 'castle',
            'palace' => 'palace',
            'market' => 'market',
            'bridge' => 'bridge',
            'waterfront' => 'waterfront',
            'plaza' => 'plaza',
            'square' => 'plaza',
            'gallery' => 'gallery',
            'aquarium' => 'aquarium',
            'zoo' => 'zoo',
        ];

        $lower = strtolower(trim($raw));
        return $map[$lower] ?? 'attraction';
    }

    protected function normalizeRestaurantCategory(?string $category, ?string $cuisines): string
    {
        $combined = strtolower(($category ?? '') . ' ' . ($cuisines ?? ''));

        if (str_contains($combined, 'cafe') || str_contains($combined, 'coffee')) {
            return 'cafe';
        }
        if (str_contains($combined, 'bar') || str_contains($combined, 'pub')) {
            if (str_contains($combined, 'cocktail')) return 'cocktail_bar';
            return 'bar';
        }
        if (str_contains($combined, 'fine dining') || str_contains($combined, 'michelin')) {
            return 'fine_dining';
        }
        if (str_contains($combined, 'nightclub') || str_contains($combined, 'club')) {
            return 'nightclub';
        }

        return 'restaurant';
    }

    protected function normalizeExperienceCategory(?string $raw): string
    {
        if (!$raw) return 'experience';

        $lower = strtolower(trim($raw));

        if (str_contains($lower, 'cruise') || str_contains($lower, 'boat')) {
            return 'river_cruise';
        }
        if (str_contains($lower, 'walking') || str_contains($lower, 'foot')) {
            return 'walking_tour';
        }
        if (str_contains($lower, 'bus') || str_contains($lower, 'hop')) {
            return 'bus_tour';
        }
        if (str_contains($lower, 'food') || str_contains($lower, 'culinary')) {
            return 'food_tour';
        }
        if (str_contains($lower, 'wine') || str_contains($lower, 'tasting')) {
            return 'wine_tasting';
        }
        if (str_contains($lower, 'cooking')) {
            return 'cooking_class';
        }

        return 'experience';
    }
}
