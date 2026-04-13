<?php

namespace App\Services\AlgoPopulators;

use Illuminate\Support\Facades\DB;

/**
 * EntityStructuralImportancePopulator
 * 
 * Computes and stores structural importance scores for all entities.
 * Implements the exact formulas from Module 3 - STRUCTURAL IMPORTANCE ENGINE spec.
 * 
 * Spec: Lines 1259-1585 of Algo Engines(2).txt
 */
class EntityStructuralImportancePopulator
{
    protected $progressCallback = null;
    protected int $batchSize = 500;

    /**
     * Set a progress callback function
     */
    public function setProgressCallback(callable $callback): self
    {
        $this->progressCallback = $callback;
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
     * Run the full importance computation pipeline
     */
    public function populate(?int $locationId = null): array
    {
        $stats = [
            'intrinsic_computed' => 0,
            'relational_computed' => 0,
            'final_scores_computed' => 0,
            'classes_assigned' => 0,
        ];

        // Phase 1: Compute intrinsic scores
        $this->log("Computing intrinsic scores...");
        $stats['intrinsic_computed'] = $this->computeIntrinsicScores($locationId);

        // Phase 2: Compute relational scores (depends on clusters)
        $this->log("Computing relational scores...");
        $stats['relational_computed'] = $this->computeRelationalScores($locationId);

        // Phase 3: Compute final structural importance
        $this->log("Computing final scores...");
        $stats['final_scores_computed'] = $this->computeFinalScores($locationId);

        // Phase 4: Assign structural classes
        $this->log("Assigning structural classes...");
        $stats['classes_assigned'] = $this->assignStructuralClasses($locationId);

        $this->log("Phase 3 complete!");
        return $stats;
    }

    // =========================================================================
    // PHASE 1: Intrinsic Scores
    // Spec: Lines 1241-1262 of Algo Engines(2).txt
    // =========================================================================

    protected function computeIntrinsicScores(?int $locationId): int
    {
        // Get all entities from structural base
        $entities = DB::table('entity_structural_base as esb')
            ->leftJoin('destination_cluster_members as dcm', function ($join) {
                $join->on('esb.entity_type', '=', 'dcm.entity_type')
                     ->on('esb.entity_id', '=', 'dcm.entity_id');
            })
            ->select([
                'esb.id',
                'esb.entity_type',
                'esb.entity_id',
                'esb.location_id',
                'esb.aggregate_rating',
                'esb.review_count',
                'esb.popularity_score',
                'esb.city_review_percentile',
                'esb.category_anchor_prior',
                'esb.category_uniqueness_prior',
                'esb.is_landmark_manual',
                'esb.is_unique_manual',
                'esb.is_destination_restaurant',
                'esb.is_signature_experience',
                'esb.local_density_score',
                'esb.local_category_density',
                'dcm.cluster_id',
                'dcm.membership_strength',
                'dcm.centrality_score',
                'dcm.role_in_cluster',
            ])
            ->when($locationId, fn($q) => $q->where('esb.location_id', $locationId))
            ->get();

        $count = 0;

        foreach ($entities as $entity) {
            // Compute landmark_strength (Spec: Lines 1205-1215)
            $landmarkStrength = $this->computeLandmarkStrength($entity);

            // Compute fame_strength (Spec: Lines 1218-1228)
            $fameStrength = $this->computeFameStrength($entity);

            // Compute uniqueness_strength (Spec: Lines 1231-1241)
            $uniquenessStrength = $this->computeUniquenessStrength($entity);

            // Compute popularity_strength (Spec: Lines 1244-1251)
            $popularityStrength = $this->computePopularityStrength($entity);

            // Compute review_confidence_strength (Spec: Lines 1254-1258)
            $reviewConfidenceStrength = $this->computeReviewConfidenceStrength($entity);

            // Compute intrinsic_importance_score (Spec: Lines 1259-1262)
            $intrinsicImportance = round(
                0.25 * $landmarkStrength +
                0.20 * $fameStrength +
                0.20 * $uniquenessStrength +
                0.20 * $popularityStrength +
                0.15 * $reviewConfidenceStrength,
            4);

            // Insert or update in entity_structural_importance
            DB::table('entity_structural_importance')->updateOrInsert(
                [
                    'entity_type' => $entity->entity_type,
                    'entity_id' => $entity->entity_id,
                ],
                [
                    'location_id' => $entity->location_id,
                    'landmark_strength' => round($landmarkStrength, 4),
                    'fame_strength' => round($fameStrength, 4),
                    'uniqueness_strength' => round($uniquenessStrength, 4),
                    'popularity_strength' => round($popularityStrength, 4),
                    'review_confidence_strength' => round($reviewConfidenceStrength, 4),
                    'intrinsic_importance_score' => round($intrinsicImportance, 4),
                    'computed_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $count++;
        }

        return $count;
    }

    /**
     * Compute landmark_strength
     * Spec: Lines 1205-1215
     */
    protected function computeLandmarkStrength($entity): float
    {
        // Manual override takes precedence
        if ($entity->is_landmark_manual) {
            return 1.0;
        }

        // Otherwise compute from signals
        $rating = $entity->aggregate_rating ?? 0;
        $reviews = $entity->review_count ?? 0;
        $percentile = $entity->city_review_percentile ?? 0;
        $categoryPrior = $entity->category_anchor_prior ?? 0.25;

        return min(1.0,
            0.30 * min(1.0, $rating / 5.0) +
            0.25 * min(1.0, log($reviews + 1) / 10) +
            0.25 * $percentile +
            0.20 * $categoryPrior
        );
    }

    /**
     * Compute fame_strength
     * Spec: Lines 1218-1228
     */
    protected function computeFameStrength($entity): float
    {
        $reviews = $entity->review_count ?? 0;
        $percentile = $entity->city_review_percentile ?? 0;
        $popularity = $entity->popularity_score ?? 0;

        // Normalize popularity (assuming 0-100 scale)
        $popNorm = min(1.0, $popularity / 100);

        return min(1.0,
            0.35 * $percentile +
            0.35 * $popNorm +
            0.30 * min(1.0, log($reviews + 1) / 10)
        );
    }

    /**
     * Compute uniqueness_strength
     * Spec: Lines 1231-1241
     */
    protected function computeUniquenessStrength($entity): float
    {
        // Manual override
        if ($entity->is_unique_manual) {
            return 1.0;
        }

        // Destination restaurant or signature experience boost
        if ($entity->is_destination_restaurant || $entity->is_signature_experience) {
            return 0.85;
        }

        $categoryPrior = $entity->category_uniqueness_prior ?? 0.25;
        $localCatDensity = $entity->local_category_density ?? 0.5;

        // Lower local density = higher uniqueness
        $uniquenessFromDensity = 1 - $localCatDensity;

        return min(1.0,
            0.50 * $categoryPrior +
            0.50 * $uniquenessFromDensity
        );
    }

    /**
     * Compute popularity_strength
     * Spec: Lines 1244-1251
     */
    protected function computePopularityStrength($entity): float
    {
        $popularity = $entity->popularity_score ?? 0;
        $reviews = $entity->review_count ?? 0;
        $percentile = $entity->city_review_percentile ?? 0;

        $popNorm = min(1.0, $popularity / 100);

        return min(1.0,
            0.40 * $popNorm +
            0.35 * $percentile +
            0.25 * min(1.0, log($reviews + 1) / 10)
        );
    }

    /**
     * Compute review_confidence_strength
     * Spec: Lines 1254-1258
     */
    protected function computeReviewConfidenceStrength($entity): float
    {
        $reviews = $entity->review_count ?? 0;

        // More reviews = higher confidence
        // 10 reviews = 0.3, 100 reviews = 0.7, 1000 reviews = 0.95
        if ($reviews >= 1000) return 0.95;
        if ($reviews >= 500) return 0.85;
        if ($reviews >= 100) return 0.70;
        if ($reviews >= 50) return 0.55;
        if ($reviews >= 20) return 0.40;
        if ($reviews >= 10) return 0.30;
        if ($reviews >= 5) return 0.20;

        return min(1.0, $reviews / 10);
    }

    // =========================================================================
    // PHASE 2: Relational Scores
    // Spec: Lines 1267-1370 of Algo Engines(2).txt
    // =========================================================================

    protected function computeRelationalScores(?int $locationId): int
    {
        // Get entities with cluster membership
        $entities = DB::table('entity_structural_importance as esi')
            ->join('entity_structural_base as esb', function ($join) {
                $join->on('esi.entity_type', '=', 'esb.entity_type')
                     ->on('esi.entity_id', '=', 'esb.entity_id');
            })
            ->leftJoin('destination_cluster_members as dcm', function ($join) {
                $join->on('esb.entity_type', '=', 'dcm.entity_type')
                     ->on('esb.entity_id', '=', 'dcm.entity_id');
            })
            ->leftJoin('destination_clusters as dc', 'dcm.cluster_id', '=', 'dc.cluster_id')
            ->select([
                'esi.id',
                'esi.entity_type',
                'esi.entity_id',
                'esb.lat',
                'esb.lng',
                'esb.location_id',
                'esb.aggregate_rating',
                'esb.review_count',
                'esi.intrinsic_importance_score',
                'dcm.cluster_id',
                'dcm.membership_strength',
                'dcm.centrality_score',
                'dcm.role_in_cluster',
                'dc.destination_gravity_score',
                'dc.density_score',
                'dc.cohesion_score',
                'dc.walkability_score',
                'dc.diversity_score',
                'dc.anchor_concentration_score',
                'dc.cluster_collective_strength',
                'dc.cluster_saturation_quality',
            ])
            ->when($locationId, fn($q) => $q->where('esb.location_id', $locationId))
            ->get();

        $count = 0;

        foreach ($entities as $entity) {
            // Compute cluster_membership_strength (Spec: Lines 1272-1280)
            $clusterMembership = $this->computeClusterMembershipStrength($entity);

            // Compute area_gravity_strength (Spec: Lines 1284-1296)
            $areaGravity = $this->computeAreaGravityStrength($entity);

            // Compute co_visit_strength (Spec: Lines 1299-1314)
            $coVisit = $this->computeCoVisitStrength($entity);

            // Compute anchor_adjacency_strength (Spec: Lines 1319-1339)
            $anchorAdjacency = $this->computeAnchorAdjacencyStrength($entity);

            // Compute route_support_strength (Spec: Lines 1342-1359)
            $routeSupport = $this->computeRouteSupportStrength($entity);

            // Compute relational_importance_score (Spec: Lines 1362-1370)
            $relationalImportance = round(
                0.25 * $clusterMembership +
                0.25 * $areaGravity +
                0.20 * $coVisit +
                0.15 * $anchorAdjacency +
                0.15 * $routeSupport,
            4);

            // Update entity_structural_importance
            DB::table('entity_structural_importance')
                ->where('entity_type', $entity->entity_type)
                ->where('entity_id', $entity->entity_id)
                ->update([
                    'cluster_membership_strength' => round($clusterMembership, 4),
                    'area_gravity_strength' => round($areaGravity, 4),
                    'co_visit_strength' => round($coVisit, 4),
                    'anchor_adjacency_strength' => round($anchorAdjacency, 4),
                    'route_support_strength' => round($routeSupport, 4),
                    'relational_importance_score' => round($relationalImportance, 4),
                    'updated_at' => now(),
                ]);

            $count++;
        }

        return $count;
    }

    protected function computeClusterMembershipStrength($entity): float
    {
        if (!$entity->cluster_id) {
            return 0.0;
        }

        $membership = $entity->membership_strength ?? 0.5;
        $centrality = $entity->centrality_score ?? 0.5;

        // Spec: Lines 1272-1280
        return min(1.0,
            0.35 * $membership +
            0.35 * $centrality +
            0.30 * 0.5 // thematic_fit_score placeholder
        );
    }

    protected function computeAreaGravityStrength($entity): float
    {
        if (!$entity->cluster_id) {
            return 0.0;
        }

        $density = $entity->density_score ?? 0;
        $cohesion = $entity->cohesion_score ?? 0;
        $walkability = $entity->walkability_score ?? 0;
        $diversity = $entity->diversity_score ?? 0;
        $anchorConc = $entity->anchor_concentration_score ?? 0;

        // Spec: Lines 1284-1296
        return min(1.0,
            0.25 * $density +
            0.20 * $cohesion +
            0.20 * $walkability +
            0.15 * $diversity +
            0.20 * $anchorConc
        );
    }

    protected function computeCoVisitStrength($entity): float
    {
        if (!$entity->lat || !$entity->lng) {
            return 0.0;
        }

        // Count nearby high-quality entities within 600m
        $nearbyQuality = DB::table('entity_structural_base')
            ->where('location_id', $entity->location_id)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('aggregate_rating', '>=', 4.0)
            ->where('review_count', '>=', 50)
            ->whereRaw("
                (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) <= 0.6
            ", [$entity->lat, $entity->lng, $entity->lat])
            ->count();

        // Spec: Lines 1299-1314
        return min(1.0, $nearbyQuality / 20);
    }

    protected function computeAnchorAdjacencyStrength($entity): float
    {
        if (!$entity->lat || !$entity->lng) {
            return 0.0;
        }

        // Find top anchors within 1.2km
        $topAnchors = DB::table('entity_structural_importance as esi')
            ->join('entity_structural_base as esb', function ($join) {
                $join->on('esi.entity_type', '=', 'esb.entity_type')
                     ->on('esi.entity_id', '=', 'esb.entity_id');
            })
            ->where('esb.location_id', $entity->location_id)
            ->where('esi.intrinsic_importance_score', '>=', 0.70)
            ->whereNotNull('esb.lat')
            ->whereNotNull('esb.lng')
            ->select([
                'esb.lat', 'esb.lng', 'esi.intrinsic_importance_score',
                DB::raw("
                    (6371 * acos(cos(radians({$entity->lat})) * cos(radians(esb.lat)) * cos(radians(esb.lng) - radians({$entity->lng})) + sin(radians({$entity->lat})) * sin(radians(esb.lat)))) as distance
                ")
            ])
            ->havingRaw('distance <= 1.2')
            ->get();

        // Spec: Lines 1319-1339
        $score = 0;
        foreach ($topAnchors as $anchor) {
            $distance = max(0.2, $anchor->distance); // Min 200m
            $decay = 1 / ($distance * $distance); // Distance decay
            $score += $anchor->intrinsic_importance_score * $decay;
        }

        return min(1.0, $score / 5);
    }

    protected function computeRouteSupportStrength($entity): float
    {
        // Simplified approximation
        // Spec: Lines 1342-1359

        $category = $entity->entity_type;

        // Categories that support routes
        $routeSupportCategories = [
            'cafe' => 0.7,
            'restaurant' => 0.5,
            'viewpoint' => 0.6,
            'park' => 0.5,
            'market' => 0.6,
        ];

        $categoryBonus = $routeSupportCategories[$category] ?? 0.3;

        // Low detour bonus (simplified)
        $lowDetourBonus = ($entity->membership_strength ?? 0.3) * 0.5;

        return min(1.0,
            0.40 * $categoryBonus +
            0.30 * $lowDetourBonus +
            0.30 * 0.5 // placeholder for enrichment/pause utility
        );
    }

    // =========================================================================
    // PHASE 3: Final Scores (OPTIMIZED)
    // Spec: Lines 1375-1474 of Algo Engines(2).txt
    // =========================================================================

    protected function computeFinalScores(?int $locationId): int
    {
        // Pre-load all category priors (entity_type + entity_id => prior)
        $this->log("  Pre-loading category priors...");
        $categoryPriors = [];
        $priorQuery = DB::table('entity_structural_base')
            ->select('entity_type', 'entity_id', 'category_anchor_prior')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId));
        foreach ($priorQuery->cursor() as $row) {
            $categoryPriors[$row->entity_type . '_' . $row->entity_id] = $row->category_anchor_prior ?? 0.25;
        }

        // Pre-load cluster memberships with cluster data
        $this->log("  Pre-loading cluster data...");
        $clusterData = [];
        $clusterQuery = DB::table('destination_cluster_members as dcm')
            ->join('destination_clusters as dc', 'dcm.cluster_id', '=', 'dc.cluster_id')
            ->select('dcm.entity_type', 'dcm.entity_id', 'dcm.role_in_cluster',
                     'dc.cluster_collective_strength', 'dc.cluster_saturation_quality');
        foreach ($clusterQuery->cursor() as $row) {
            $key = $row->entity_type . '_' . $row->entity_id;
            $clusterData[$key] = [
                'role' => $row->role_in_cluster ?? 'none',
                'collective_strength' => $row->cluster_collective_strength ?? 0,
                'saturation_quality' => $row->cluster_saturation_quality ?? 0,
            ];
        }

        // Process entities in batches
        $this->log("  Computing final scores...");
        $query = DB::table('entity_structural_importance')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId));

        $count = 0;
        $batch = [];

        foreach ($query->cursor() as $entity) {
            $intrinsic = $entity->intrinsic_importance_score ?? 0;
            $relational = $entity->relational_importance_score ?? 0;
            $landmark = $entity->landmark_strength ?? 0;
            $uniqueness = $entity->uniqueness_strength ?? 0;
            $clusterMembership = $entity->cluster_membership_strength ?? 0;
            $areaGravity = $entity->area_gravity_strength ?? 0;
            $coVisit = $entity->co_visit_strength ?? 0;
            $anchorAdjacency = $entity->anchor_adjacency_strength ?? 0;
            $routeSupport = $entity->route_support_strength ?? 0;

            // Get category_anchor_prior from pre-loaded data
            $key = $entity->entity_type . '_' . $entity->entity_id;
            $categoryPrior = $categoryPriors[$key] ?? 0.25;

            // Get cluster data from pre-loaded data
            $cluster = $clusterData[$key] ?? ['role' => 'none', 'collective_strength' => 0, 'saturation_quality' => 0];
            $roleScore = match ($cluster['role']) {
                'core' => 1.00,
                'support' => 0.72,
                'bridge' => 0.65,
                'fringe' => 0.40,
                default => 0.30,
            };

            // poi_anchor_score (Spec: Lines 1380-1388)
            $poiAnchor = round(
                0.30 * $intrinsic +
                0.20 * $landmark +
                0.20 * $uniqueness +
                0.15 * ($entity->fame_strength ?? 0) +
                0.15 * $categoryPrior,
            4);

            // composite_anchor_score (Spec: Lines 1393-1401)
            $compositeAnchor = round(
                0.30 * $relational +
                0.25 * $areaGravity +
                0.20 * $clusterMembership +
                0.15 * $coVisit +
                0.10 * $anchorAdjacency,
            4);

            // collective_destination_score (Spec: Lines 1406-1426)
            $collectiveDestination = round(
                0.35 * $cluster['collective_strength'] +
                0.25 * $clusterMembership +
                0.20 * $roleScore +
                0.20 * $cluster['saturation_quality'],
            4);

            // soft_anchor_score (Spec: Lines 1431-1438)
            $softAnchor = round(
                0.40 * $routeSupport +
                0.25 * $uniqueness +
                0.20 * $anchorAdjacency +
                0.15 * $coVisit,
            4);

            // trip_value_score (Spec: Lines 1443-1457)
            $tripValue = max(
                round(
                    0.38 * $poiAnchor +
                    0.24 * $compositeAnchor +
                    0.18 * $collectiveDestination +
                    0.20 * $softAnchor,
                4),
                round(
                    0.45 * $intrinsic +
                    0.30 * $relational +
                    0.25 * $softAnchor,
                4)
            );

            // structural_importance_score (Spec: Lines 1463-1471)
            $structuralImportance = round(
                0.35 * $poiAnchor +
                0.25 * $compositeAnchor +
                0.15 * $collectiveDestination +
                0.10 * $softAnchor +
                0.15 * $tripValue,
            4);

            $batch[] = [
                'id' => $entity->id,
                'poi_anchor_score' => min(1.0, $poiAnchor),
                'composite_anchor_score' => min(1.0, $compositeAnchor),
                'collective_destination_score' => min(1.0, $collectiveDestination),
                'soft_anchor_score' => min(1.0, $softAnchor),
                'trip_value_score' => min(1.0, $tripValue),
                'structural_importance_score' => min(1.0, $structuralImportance),
            ];

            if (count($batch) >= $this->batchSize) {
                $this->updateFinalScoresBatch($batch);
                $count += count($batch);
                $this->log("    Computed {$count} final scores...");
                $batch = [];
            }
        }

        // Process remaining
        if (!empty($batch)) {
            $this->updateFinalScoresBatch($batch);
            $count += count($batch);
        }

        return $count;
    }

    /**
     * Update final scores in batch using CASE WHEN
     */
    protected function updateFinalScoresBatch(array $batch): void
    {
        $ids = implode(',', array_column($batch, 'id'));

        $cases = [
            'poi_anchor' => [],
            'composite_anchor' => [],
            'collective_destination' => [],
            'soft_anchor' => [],
            'trip_value' => [],
            'structural_importance' => [],
        ];

        foreach ($batch as $item) {
            $id = (int)$item['id'];
            $cases['poi_anchor'][] = "WHEN id = {$id} THEN {$item['poi_anchor_score']}";
            $cases['composite_anchor'][] = "WHEN id = {$id} THEN {$item['composite_anchor_score']}";
            $cases['collective_destination'][] = "WHEN id = {$id} THEN {$item['collective_destination_score']}";
            $cases['soft_anchor'][] = "WHEN id = {$id} THEN {$item['soft_anchor_score']}";
            $cases['trip_value'][] = "WHEN id = {$id} THEN {$item['trip_value_score']}";
            $cases['structural_importance'][] = "WHEN id = {$id} THEN {$item['structural_importance_score']}";
        }

        DB::statement("
            UPDATE entity_structural_importance
            SET
                poi_anchor_score = CASE " . implode(' ', $cases['poi_anchor']) . " END,
                composite_anchor_score = CASE " . implode(' ', $cases['composite_anchor']) . " END,
                collective_destination_score = CASE " . implode(' ', $cases['collective_destination']) . " END,
                soft_anchor_score = CASE " . implode(' ', $cases['soft_anchor']) . " END,
                trip_value_score = CASE " . implode(' ', $cases['trip_value']) . " END,
                structural_importance_score = CASE " . implode(' ', $cases['structural_importance']) . " END,
                updated_at = NOW()
            WHERE id IN ({$ids})
        ");
    }

    // =========================================================================
    // PHASE 4: Structural Class Assignment (OPTIMIZED)
    // Spec: Lines 1477-1523 of Algo Engines(2).txt
    // =========================================================================

    protected function assignStructuralClasses(?int $locationId): int
    {
        // Pre-load cluster collective strengths (reuse from computeFinalScores)
        $this->log("  Pre-loading cluster data...");
        $clusterCollectives = [];
        $clusterQuery = DB::table('destination_cluster_members as dcm')
            ->join('destination_clusters as dc', 'dcm.cluster_id', '=', 'dc.cluster_id')
            ->select('dcm.entity_type', 'dcm.entity_id', 'dc.cluster_collective_strength');
        foreach ($clusterQuery->cursor() as $row) {
            $clusterCollectives[$row->entity_type . '_' . $row->entity_id] = $row->cluster_collective_strength ?? 0;
        }

        $query = DB::table('entity_structural_importance')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId));

        $count = 0;
        $batch = [];

        foreach ($query->cursor() as $entity) {
            $class = $this->determineStructuralClassOptimized($entity, $clusterCollectives);

            $batch[] = [
                'id' => $entity->id,
                'structural_class' => $class,
            ];

            if (count($batch) >= $this->batchSize) {
                $this->updateStructuralClassBatch($batch);
                $count += count($batch);
                $this->log("    Assigned {$count} structural classes...");
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->updateStructuralClassBatch($batch);
            $count += count($batch);
        }

        return $count;
    }

    protected function determineStructuralClassOptimized($entity, array $clusterCollectives): string
    {
        $poiAnchor = $entity->poi_anchor_score ?? 0;
        $intrinsic = $entity->intrinsic_importance_score ?? 0;
        $compositeAnchor = $entity->composite_anchor_score ?? 0;
        $areaGravity = $entity->area_gravity_strength ?? 0;
        $collectiveDest = $entity->collective_destination_score ?? 0;
        $softAnchor = $entity->soft_anchor_score ?? 0;
        $routeSupport = $entity->route_support_strength ?? 0;

        // Get cluster collective strength from pre-loaded data
        $key = $entity->entity_type . '_' . $entity->entity_id;
        $clusterCollective = $clusterCollectives[$key] ?? 0;

        // Rule 1: poi_anchor (Spec: Lines 1483-1487)
        if ($poiAnchor >= 0.78 && $intrinsic >= 0.70) {
            return 'poi_anchor';
        }

        // Rule 2: composite_anchor (Spec: Lines 1492-1499)
        if ($compositeAnchor >= 0.72 && $areaGravity >= 0.65) {
            return 'composite_anchor';
        }

        // Rule 3: collective_destination (Spec: Lines 1503-1506)
        if ($collectiveDest >= 0.68 && $clusterCollective >= 0.65) {
            return 'collective_destination';
        }

        // Rule 4: soft_anchor (Spec: Lines 1511-1515)
        if ($softAnchor >= 0.58 || $routeSupport >= 0.62) {
            return 'soft_anchor';
        }

        // Rule 5: utility (Spec: Lines 1520-1523)
        return 'utility';
    }

    /**
     * Update structural classes in batch
     */
    protected function updateStructuralClassBatch(array $batch): void
    {
        $ids = implode(',', array_column($batch, 'id'));
        $cases = [];

        foreach ($batch as $item) {
            $id = (int)$item['id'];
            $class = addslashes($item['structural_class']);
            $cases[] = "WHEN id = {$id} THEN '{$class}'";
        }

        DB::statement("
            UPDATE entity_structural_importance
            SET structural_class = CASE " . implode(' ', $cases) . " END,
                updated_at = NOW()
            WHERE id IN ({$ids})
        ");
    }
}
