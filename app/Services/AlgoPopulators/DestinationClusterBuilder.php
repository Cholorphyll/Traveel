<?php

namespace App\Services\AlgoPopulators;

use Illuminate\Support\Facades\DB;

/**
 * DestinationClusterBuilder
 * 
 * Builds destination clusters using DBSCAN algorithm.
 * Implements the exact logic from Module 3 - STRUCTURAL IMPORTANCE ENGINE spec.
 * 
 * Spec: Lines 661-973 of Algo Engines(2).txt
 */
class DestinationClusterBuilder
{
    // DBSCAN parameters
    protected float $epsilon = 0.0045; // ~500m in degrees (approximate)
    protected int $minPoints = 4;

    protected $progressCallback = null;
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
     * Set DBSCAN epsilon parameter (in degrees)
     */
    public function setEpsilon(float $epsilon): self
    {
        $this->epsilon = $epsilon;
        return $this;
    }

    /**
     * Set DBSCAN minimum points parameter
     */
    public function setMinPoints(int $minPoints): self
    {
        $this->minPoints = $minPoints;
        return $this;
    }

    /**
     * Run the clustering pipeline
     */
    public function build(?int $locationId = null): array
    {
        $stats = [
            'clusters_created' => 0,
            'entities_assigned' => 0,
            'noise_points' => 0,
        ];

        if (!$this->skipTruncate) {
            $this->log("Clearing existing clusters...");
            DB::table('destination_cluster_members')->truncate();
            DB::table('destination_clusters')->truncate();
        } else {
            $this->log("Skipping truncate (resume mode)...");
        }

        // Get all locations
        $locations = DB::table('entity_structural_base')
            ->select('location_id')
            ->distinct()
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->pluck('location_id');

        $this->log("Processing " . count($locations) . " locations...");

        foreach ($locations as $locId) {
            $this->log("  Clustering location {$locId}...");
            $result = $this->clusterLocation($locId);
            $stats['clusters_created'] += $result['clusters'];
            $stats['entities_assigned'] += $result['assigned'];
            $stats['noise_points'] += $result['noise'];
        }

        $this->log("Phase 2 complete!");
        return $stats;
    }

    /**
     * Cluster entities within a single location using DBSCAN
     */
    protected function clusterLocation(int $locationId): array
    {
        // Get all entities for this location
        $entities = DB::table('entity_structural_base')
            ->where('location_id', $locationId)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get(['id', 'entity_type', 'entity_id', 'lat', 'lng', 'category', 'aggregate_rating', 'review_count'])
            ->toArray();

        if (count($entities) < $this->minPoints) {
            return ['clusters' => 0, 'assigned' => 0, 'noise' => count($entities)];
        }

        // Run DBSCAN
        $clusters = $this->dbscan($entities);

        $clusterCount = 0;
        $assignedCount = 0;
        $noiseCount = 0;

        foreach ($clusters as $clusterEntities) {
            if (count($clusterEntities) < $this->minPoints) {
                // Noise points
                $noiseCount += count($clusterEntities);
                continue;
            }

            $clusterCount++;

            // Create cluster record
            $clusterId = $this->createCluster($clusterEntities, $locationId);

            // Assign members
            foreach ($clusterEntities as $entity) {
                $this->assignMember($clusterId, $entity);
                $assignedCount++;
            }
        }

        return [
            'clusters' => $clusterCount,
            'assigned' => $assignedCount,
            'noise' => $noiseCount,
        ];
    }

    /**
     * DBSCAN clustering algorithm
     * Returns array of clusters (each cluster is array of entities)
     */
    protected function dbscan(array $entities): array
    {
        $visited = [];
        $clusters = [];
        $noise = [];

        // Build spatial index for faster neighbor lookup
        $entityMap = [];
        foreach ($entities as $e) {
            $entityMap[$e->id] = $e;
        }

        foreach ($entities as $entity) {
            if (isset($visited[$entity->id])) {
                continue;
            }

            $visited[$entity->id] = true;
            $neighbors = $this->getNeighbors($entity, $entities);

            if (count($neighbors) < $this->minPoints) {
                // Noise point
                $noise[] = [$entity];
            } else {
                // Start new cluster
                $cluster = [];
                $this->expandCluster($entity, $neighbors, $cluster, $visited, $entities);
                $clusters[] = $cluster;
            }
        }

        // Add noise as single-entity clusters (will be filtered later)
        foreach ($noise as $n) {
            $clusters[] = $n;
        }

        return $clusters;
    }

    /**
     * Expand cluster by adding density-reachable points
     */
    protected function expandCluster($entity, array $neighbors, array &$cluster, array &$visited, array $allEntities): void
    {
        $cluster[] = $entity;
        $queue = $neighbors;

        while (!empty($queue)) {
            $current = array_shift($queue);
            $currentId = $current->id;

            if (!isset($visited[$currentId])) {
                $visited[$currentId] = true;
                $currentNeighbors = $this->getNeighbors($current, $allEntities);

                if (count($currentNeighbors) >= $this->minPoints) {
                    $queue = array_merge($queue, $currentNeighbors);
                }
            }

            // Add to cluster if not already there
            $inCluster = false;
            foreach ($cluster as $c) {
                if ($c->id === $currentId) {
                    $inCluster = true;
                    break;
                }
            }
            if (!$inCluster) {
                $cluster[] = $current;
            }
        }
    }

    /**
     * Get neighbors within epsilon distance
     */
    protected function getNeighbors($entity, array $allEntities): array
    {
        $neighbors = [];
        $epsilonKm = $this->epsilon * 111; // Convert to km approximately

        foreach ($allEntities as $other) {
            if ($other->id === $entity->id) continue;

            $dist = $this->haversineDistance(
                $entity->lat, $entity->lng,
                $other->lat, $other->lng
            );

            if ($dist <= $epsilonKm) {
                $neighbors[] = $other;
            }
        }

        return $neighbors;
    }

    /**
     * Create cluster record and compute cluster-level scores
     * Spec: Lines 730-973 of Algo Engines(2).txt
     */
    protected function createCluster(array $entities, int $locationId): int
    {
        // Compute centroid
        $sumLat = 0;
        $sumLng = 0;
        $totalRating = 0;
        $totalReviews = 0;
        $sightCount = 0;
        $restaurantCount = 0;
        $experienceCount = 0;
        $categories = [];

        foreach ($entities as $e) {
            $sumLat += $e->lat;
            $sumLng += $e->lng;
            $totalRating += $e->aggregate_rating ?? 0;
            $totalReviews += $e->review_count ?? 0;

            if ($e->entity_type === 'sight') $sightCount++;
            elseif ($e->entity_type === 'restaurant') $restaurantCount++;
            elseif ($e->entity_type === 'experience') $experienceCount++;

            if ($e->category) {
                $categories[$e->category] = ($categories[$e->category] ?? 0) + 1;
            }
        }

        $count = count($entities);
        $centroidLat = $sumLat / $count;
        $centroidLng = $sumLng / $count;
        $avgRating = $totalRating / $count;

        // Determine cluster type
        $clusterType = $this->determineClusterType($sightCount, $restaurantCount, $experienceCount);

        // Compute density_score (Spec: Lines 801-810)
        $radiusKm = $this->computeClusterRadius($entities, $centroidLat, $centroidLng);
        $areaSqKm = pi() * $radiusKm * $radiusKm;
        $densityScore = min(1.0, $count / max(1, $areaSqKm * 100));

        // Compute cohesion_score (Spec: Lines 813-824)
        $cohesionScore = $this->computeCohesionScore($entities, $centroidLat, $centroidLng);

        // Compute diversity_score (Spec: Lines 827-839)
        $diversityScore = $this->computeDiversityScore($sightCount, $restaurantCount, $experienceCount);

        // Compute walkability_score (Spec: Lines 842-852)
        $walkabilityScore = min(1.0, ($cohesionScore * 0.5) + ($densityScore * 0.5));

        // Compute anchor_concentration_score (Spec: Lines 855-864)
        $anchorConcentration = $this->computeAnchorConcentration($entities);

        // Compute destination_gravity_score (Spec: Lines 867-880)
        $destinationGravity = round(
            0.25 * $densityScore +
            0.20 * $cohesionScore +
            0.15 * $diversityScore +
            0.20 * $walkabilityScore +
            0.20 * $anchorConcentration,
        4);

        // Compute collective identity fields (Spec: Lines 883-949)
        $collectiveIdentity = $this->computeCollectiveIdentity($entities, $categories);
        $clusterThemeClarity = $this->computeThemeClarity($categories, $count);
        $clusterRepeatability = min(1.0, $destinationGravity * 0.8 + $avgRating / 5 * 0.2);
        $clusterSaturationQuality = round(
            0.40 * min(1.0, $avgRating / 5) +
            0.35 * $anchorConcentration +
            0.25 * min(1.0, log($totalReviews + 1) / 10),
        4);

        $clusterCollectiveStrength = round(
            0.30 * $collectiveIdentity +
            0.20 * $clusterThemeClarity +
            0.20 * $clusterRepeatability +
            0.30 * $clusterSaturationQuality,
        4);

        // Boolean flags (Spec: Lines 954-971)
        $isCollectiveDestination = $clusterCollectiveStrength >= 0.68 ? 1 : 0;
        $isCompositeAnchorZone = $destinationGravity >= 0.72 ? 1 : 0;

        // Insert cluster
        return DB::table('destination_clusters')->insertGetId([
            'location_id' => $locationId,
            'cluster_name' => null, // Can be populated manually later
            'cluster_type' => $clusterType,
            'centroid_lat' => $centroidLat,
            'centroid_lng' => $centroidLng,
            'entity_count' => $count,
            'sight_count' => $sightCount,
            'restaurant_count' => $restaurantCount,
            'experience_count' => $experienceCount,
            'avg_rating' => round($avgRating, 4),
            'total_review_count' => $totalReviews,
            'density_score' => round($densityScore, 4),
            'cohesion_score' => round($cohesionScore, 4),
            'diversity_score' => round($diversityScore, 4),
            'walkability_score' => round($walkabilityScore, 4),
            'anchor_concentration_score' => round($anchorConcentration, 4),
            'destination_gravity_score' => $destinationGravity,
            'collective_identity_strength' => round($collectiveIdentity, 4),
            'cluster_theme_clarity' => round($clusterThemeClarity, 4),
            'cluster_repeatability' => round($clusterRepeatability, 4),
            'cluster_saturation_quality' => $clusterSaturationQuality,
            'cluster_collective_strength' => $clusterCollectiveStrength,
            'is_collective_destination' => $isCollectiveDestination,
            'is_composite_anchor_zone' => $isCompositeAnchorZone,
            'computed_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Assign entity to cluster as member
     * Spec: Lines 976-999 of Algo Engines(2).txt
     */
    protected function assignMember(int $clusterId, $entity): void
    {
        // Compute membership_strength (distance to centroid)
        $cluster = DB::table('destination_clusters')->where('cluster_id', $clusterId)->first();

        $distanceToCentroid = $this->haversineDistance(
            $entity->lat, $entity->lng,
            $cluster->centroid_lat, $cluster->centroid_lng
        );

        // Closer = stronger membership
        $membershipStrength = max(0, 1 - ($distanceToCentroid / 1.0)); // 1km max

        // Compute centrality_score (relative position in cluster)
        $centralityScore = $membershipStrength;

        // Determine role_in_cluster (Spec: Lines 1409-1419)
        $roleInCluster = $this->determineRoleInCluster($entity, $membershipStrength);

        DB::table('destination_cluster_members')->insert([
            'cluster_id' => $clusterId,
            'entity_type' => $entity->entity_type,
            'entity_id' => $entity->entity_id,
            'membership_strength' => round($membershipStrength, 4),
            'centrality_score' => round($centralityScore, 4),
            'role_in_cluster' => $roleInCluster,
            'computed_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // =========================================================================
    // Helper methods for cluster computation
    // =========================================================================

    protected function determineClusterType(int $sights, int $restaurants, int $experiences): string
    {
        $total = $sights + $restaurants + $experiences;
        if ($total === 0) return 'mixed';

        $sightRatio = $sights / $total;
        $restRatio = $restaurants / $total;
        $expRatio = $experiences / $total;

        if ($sightRatio > 0.6) return 'sightseeing';
        if ($restRatio > 0.6) return 'food';
        if ($expRatio > 0.6) return 'experience';

        if ($sightRatio > 0.3 && $restRatio > 0.3) return 'mixed';
        if ($restRatio > 0.4 && $sightRatio < 0.2) return 'nightlife';

        return 'mixed';
    }

    protected function computeClusterRadius(array $entities, float $centroidLat, float $centroidLng): float
    {
        $maxDist = 0;
        foreach ($entities as $e) {
            $dist = $this->haversineDistance($centroidLat, $centroidLng, $e->lat, $e->lng);
            $maxDist = max($maxDist, $dist);
        }
        return max(0.1, $maxDist); // Min 100m
    }

    protected function computeCohesionScore(array $entities, float $centroidLat, float $centroidLng): float
    {
        $total = count($entities);
        if ($total <= 1) return 1.0;

        $sumDist = 0;
        foreach ($entities as $e) {
            $sumDist += $this->haversineDistance($centroidLat, $centroidLng, $e->lat, $e->lng);
        }

        $avgDist = $sumDist / $total;
        // Tighter cluster = higher score (500m avg = 0.5, 100m avg = 0.9)
        return max(0, min(1, 1 - ($avgDist / 1.0)));
    }

    protected function computeDiversityScore(int $sights, int $restaurants, int $experiences): float
    {
        $total = $sights + $restaurants + $experiences;
        if ($total === 0) return 0;

        // Entropy-based diversity
        $diversity = 0;
        $types = [$sights, $restaurants, $experiences];

        foreach ($types as $count) {
            if ($count > 0) {
                $p = $count / $total;
                $diversity -= $p * log($p);
            }
        }

        // Normalize to 0-1 (max entropy for 3 types = log(3) ~= 1.099)
        return min(1, $diversity / log(3));
    }

    protected function computeAnchorConcentration(array $entities): float
    {
        // Count high-quality entities (rating >= 4.0, reviews >= 100)
        $anchorCount = 0;
        foreach ($entities as $e) {
            $rating = $e->aggregate_rating ?? 0;
            $reviews = $e->review_count ?? 0;
            if ($rating >= 4.0 && $reviews >= 100) {
                $anchorCount++;
            }
        }

        return min(1, $anchorCount / max(1, count($entities) / 5));
    }

    protected function computeCollectiveIdentity(array $entities, array $categories): float
    {
        // How coherent is the cluster's identity
        $total = count($entities);
        if ($total === 0) return 0;

        // Category concentration
        $maxCategoryCount = max($categories);
        $categoryConcentration = $maxCategoryCount / $total;

        // Type coherence (already computed in diversity)
        return $categoryConcentration;
    }

    protected function computeThemeClarity(array $categories, int $total): float
    {
        if ($total === 0) return 0;
        $maxCategory = max($categories);
        return $maxCategory / $total;
    }

    protected function determineRoleInCluster($entity, float $membershipStrength): string
    {
        // Spec: Lines 1409-1419
        if ($membershipStrength >= 0.85) return 'core';
        if ($membershipStrength >= 0.65) return 'support';
        if ($membershipStrength >= 0.45) return 'bridge';
        return 'fringe';
    }

    protected function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
