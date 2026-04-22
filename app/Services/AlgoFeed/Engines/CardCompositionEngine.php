<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Facades\DB;

/**
 * Card Composition Engine
 *
 * Spec: files-new/Algo Engines.txt + Algo Engines(12).txt
 *
 * Converts assembled feed cards into UI-ready card structures:
 *   PRIMARY_ENTITY            — single high-importance entity, large layout
 *   ENTITY_WITH_CONTEXT       — primary entity + context strips (nearby food/attractions)
 *   COLLECTION                — carousel of thematically cohesive items
 *   HYBRID                    — primary entity + multiple context sections
 *
 * Sub-modules:
 *   ContextStripBuilder — generates nearby horizontal strips per card
 *   CollectionBuilder   — builds thematic carousels (TRENDING, RIGHT_NOW, etc.)
 *   LayoutResolver      — maps card_type + tier to layout string
 */
class CardCompositionEngine
{
    // Card types (Spec §4)
    private const TYPE_PRIMARY_ENTITY     = 'PRIMARY_ENTITY';
    private const TYPE_ENTITY_WITH_CONTEXT= 'ENTITY_WITH_CONTEXT';
    private const TYPE_COLLECTION         = 'COLLECTION';
    private const TYPE_HYBRID             = 'HYBRID';

    // Layout types (Spec §5)
    private const LAYOUT_LARGE                     = 'LARGE';
    private const LAYOUT_MEDIUM                    = 'MEDIUM';
    private const LAYOUT_CAROUSEL                  = 'CAROUSEL';
    private const LAYOUT_LARGE_WITH_SCROLL         = 'LARGE_WITH_HORIZONTAL_SCROLL';
    private const LAYOUT_MEDIUM_WITH_SCROLL        = 'MEDIUM_WITH_HORIZONTAL_SCROLL';
    private const LAYOUT_MULTI_SECTION             = 'MULTI_SECTION';

    // Context strip types (Spec §4 Case B)
    private const STRIP_NEARBY_RESTAURANTS  = 'NEARBY_RESTAURANTS';
    private const STRIP_NEARBY_ATTRACTIONS  = 'NEARBY_ATTRACTIONS';
    private const STRIP_EXPERIENCES         = 'EXPERIENCES';
    private const STRIP_QUICK_STOPS         = 'QUICK_STOPS';
    private const STRIP_CAFES               = 'CAFES';
    private const STRIP_EVENING_SPOTS       = 'EVENING_SPOTS';

    // Hard rules (Spec §8)
    private const MAX_SECTIONS          = 2;
    private const MAX_ITEMS_PER_SECTION = 5;
    private const NEARBY_WALK_RADIUS_M  = 800;
    private const NEARBY_BIKE_RADIUS_M  = 2000;
    private const NEARBY_CAR_RADIUS_M   = 5000;

    // Collection types (Spec §4 Case C)
    private const COLLECTION_TRENDING          = 'TRENDING';
    private const COLLECTION_RIGHT_NOW         = 'RIGHT_NOW';
    private const COLLECTION_NEARBY_BEST       = 'NEARBY_BEST';
    private const COLLECTION_HIDDEN_GEMS       = 'HIDDEN_GEMS';
    private const COLLECTION_FOOD_CLUSTER      = 'FOOD_CLUSTER';
    private const COLLECTION_QUICK_EXPLORATIONS= 'QUICK_EXPLORATIONS';
    private const COLLECTION_EVENING_SPOTS     = 'EVENING_SPOTS';

    /**
     * Convert assembled feed cards into composed UI cards.
     */
    public function compose(array $feed, array $ctx): array
    {
        $composed = [];
        $position = 0;

        foreach ($feed as $card) {
            $position++;
            $composed[] = $this->composeCard($card, $ctx, $feed, $position);
        }

        return $composed;
    }

    // =========================================================================
    // STEP 1 — Resolve Card Format (Spec §6 Step 1)
    // =========================================================================

    private function resolveCardType(array $card, array $ctx): string
    {
        // Use format_hint if provided
        if (!empty($card['format_hint'])) {
            return $card['format_hint'];
        }

        $tier      = (int)($card['tier']             ?? 3);
        $role      = $card['primary_role']            ?? '';
        $daypart   = $ctx['daypart']                  ?? 'afternoon';
        $hasNearby = !empty($card['nearby']);
        $isAnchor  = in_array($role, ['CURRENT_ANCHOR', 'NEXT_ANCHOR', 'COMPOSITE_ANCHOR']);

        // High importance anchor → PRIMARY_ENTITY
        if ($isAnchor && $tier <= 1) {
            return self::TYPE_PRIMARY_ENTITY;
        }

        // Good context opportunity (anchor with nearby items at meal time)
        if ($isAnchor && $hasNearby && in_array($daypart, ['lunch', 'dinner', 'evening'])) {
            return self::TYPE_ENTITY_WITH_CONTEXT;
        }

        // DISCOVERY_ITEM or browsing → COLLECTION
        if (in_array($role, ['DISCOVERY_ITEM', 'RECOVERY_ITEM', 'TRANSITION_ITEM'])) {
            return self::TYPE_COLLECTION;
        }

        // Mixed signals / multi-section → HYBRID
        if ($hasNearby && count($card['nearby'] ?? []) >= 3) {
            return self::TYPE_HYBRID;
        }

        return self::TYPE_PRIMARY_ENTITY;
    }

    // =========================================================================
    // STEP 2 — Branch by Format (Spec §6 Step 2)
    // =========================================================================

    private function composeCard(array $card, array $ctx, array $allFeed, int $position = 1): array
    {
        $cardType = $this->resolveCardType($card, $ctx);

        return match ($cardType) {
            self::TYPE_ENTITY_WITH_CONTEXT => $this->buildEntityWithContext($card, $ctx),
            self::TYPE_COLLECTION          => $this->buildCollection($card, $ctx, $allFeed),
            self::TYPE_HYBRID              => $this->buildHybrid($card, $ctx),
            default                        => $this->buildPrimaryEntity($card, $ctx, $position),
        };
    }

    // =========================================================================
    // CASE A — PRIMARY_ENTITY (Spec §6 Case A)
    // =========================================================================

    private function buildPrimaryEntity(array $card, array $ctx, int $position = 1): array
    {
        $tier       = (int)($card['tier'] ?? 3);
        $role       = $card['primary_role'] ?? '';
        $entityType = $card['entity_type'] ?? 'sight';
        $isAnchor   = in_array($role, ['CURRENT_ANCHOR', 'NEXT_ANCHOR', 'COMPOSITE_ANCHOR']);

        // Layout: LARGE only for top-position tier-1 non-restaurant anchors; restaurants always MEDIUM
        $useLarge = ($tier <= 1 && $isAnchor && $position <= 3 && $entityType !== 'restaurant');
        $layout = $useLarge ? self::LAYOUT_LARGE : self::LAYOUT_MEDIUM;

        return array_merge($card, [
            'card_type' => self::TYPE_PRIMARY_ENTITY,
            'layout'    => $layout,
            'sections'  => [],
            'meta'      => [
                'reason'     => $this->buildReason($card),
                'confidence' => round((float)($card['assembly_score'] ?? 0), 2),
            ],
        ]);
    }

    // =========================================================================
    // CASE B — ENTITY_WITH_CONTEXT (Spec §6 Case B — most important)
    // =========================================================================

    private function buildEntityWithContext(array $card, array $ctx): array
    {
        // B2: Generate context strips
        $sections = $this->buildContextStripBuilder($card, $ctx);

        // B3: Limit to max 2 sections, 5 items each (Spec §8 Rule 3/4)
        $sections = array_slice($sections, 0, self::MAX_SECTIONS);

        $tier = (int)($card['tier'] ?? 3);
        $layout = $tier <= 1 ? self::LAYOUT_LARGE_WITH_SCROLL : self::LAYOUT_MEDIUM_WITH_SCROLL;

        return array_merge($card, [
            'card_type' => self::TYPE_ENTITY_WITH_CONTEXT,
            'layout'    => $layout,
            'sections'  => $sections,
            'meta'      => [
                'reason'     => $this->buildReason($card),
                'confidence' => round((float)($card['assembly_score'] ?? 0), 2),
            ],
        ]);
    }

    // =========================================================================
    // CASE C — COLLECTION (Spec §6 Case C)
    // =========================================================================

    private function buildCollection(array $card, array $ctx, array $allFeed): array
    {
        $collectionType = $this->resolveCollectionType($card, $ctx);
        $items          = $this->buildCollectionItems($collectionType, $card, $ctx, $allFeed);
        $title          = $this->getCollectionTitle($collectionType, $ctx);

        // Cohesion check — need at least 5 items (Spec §7 Rule 4 / CollectionBuilder Step 6)
        if (count($items) < 5) {
            return $this->buildEntityWithContext($card, $ctx);
        }

        return array_merge($card, [
            'card_type'       => self::TYPE_COLLECTION,
            'layout'          => self::LAYOUT_CAROUSEL,
            'collection_type' => $collectionType,
            'collection_title'=> $title,
            'sections'        => [],
            'items'           => $items,
            'meta'            => [
                'reason'     => "COLLECTION_{$collectionType}",
                'confidence' => round((float)($card['assembly_score'] ?? 0), 2),
            ],
        ]);
    }

    // =========================================================================
    // CASE D — HYBRID (Spec §6 Case D)
    // =========================================================================

    private function buildHybrid(array $card, array $ctx): array
    {
        $sections = $this->buildContextStripBuilder($card, $ctx);
        $sections = array_slice($sections, 0, self::MAX_SECTIONS);

        return array_merge($card, [
            'card_type' => self::TYPE_HYBRID,
            'layout'    => self::LAYOUT_MULTI_SECTION,
            'sections'  => $sections,
            'meta'      => [
                'reason'     => $this->buildReason($card),
                'confidence' => round((float)($card['assembly_score'] ?? 0), 2),
            ],
        ]);
    }

    // =========================================================================
    // SUB-MODULE: ContextStripBuilder (Spec §7.1 / ContextStripBuilder spec)
    // =========================================================================

    private function buildContextStripBuilder(array $card, array $ctx): array
    {
        $daypart      = $ctx['daypart']      ?? 'afternoon';
        $userEnergy   = $ctx['user_energy']  ?? 'MEDIUM';
        $travelMode   = $ctx['travel_mode']  ?? 'WALK';
        $lat          = (float)($card['lat'] ?? 0);
        $lng          = (float)($card['lng'] ?? 0);
        $entityId     = $card['entity_id'];
        $entityType   = $card['entity_type'];
        $duration     = (int)($card['duration_minutes'] ?? 60);

        // Distance threshold (Spec ContextStripBuilder §6 Distance Threshold)
        $radiusM = match (strtoupper($travelMode)) {
            'BIKE' => self::NEARBY_BIKE_RADIUS_M,
            'CAR'  => self::NEARBY_CAR_RADIUS_M,
            default=> self::NEARBY_WALK_RADIUS_M,
        };

        // Step 1: Determine eligible context types (Spec §6 Step 1)
        $eligibleTypes = $this->determineEligibleContextTypes($daypart, $userEnergy, $duration);

        if (empty($eligibleTypes)) return [];

        // Step 2: Fetch candidate pool from nearby index
        $candidates = $this->fetchNearbyContextCandidates($lat, $lng, $radiusM, $entityId, $entityType);

        if (empty($candidates)) {
            // Fallback to pre-computed nearby from card
            $candidates = array_map(fn($n) => (object)$n, $card['nearby'] ?? []);
        }

        // Step 3 & 4: Filter, score, group by type
        $sections = [];
        foreach ($eligibleTypes as $contextType) {
            $filtered = $this->filterCandidatesForContextType($candidates, $contextType, $ctx);
            $scored   = $this->scoreContextCandidates($filtered, $card, $ctx, $contextType, $radiusM);
            usort($scored, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));
            $items = array_slice($scored, 0, self::MAX_ITEMS_PER_SECTION);

            if (empty($items)) continue;

            $sections[] = [
                'section_type' => $contextType,
                'label'        => $this->getContextStripLabel($contextType, $ctx),
                'items'        => $items,
            ];

            if (count($sections) >= self::MAX_SECTIONS) break;
        }

        return $sections;
    }

    private function determineEligibleContextTypes(string $daypart, string $energy, int $duration): array
    {
        $types = [];

        // Meal-time triggers
        if (in_array($daypart, ['lunch', 'morning']))  $types[] = self::STRIP_NEARBY_RESTAURANTS;
        if (in_array($daypart, ['dinner', 'evening'])) $types[] = self::STRIP_NEARBY_RESTAURANTS;
        if (in_array($daypart, ['morning']))            $types[] = self::STRIP_CAFES;

        // Energy-based triggers
        if (in_array(strtoupper($energy), ['LOW'])) {
            $types[] = self::STRIP_QUICK_STOPS;
            $types[] = self::STRIP_CAFES;
        }

        // High dwell time → suggest experiences
        if ($duration >= 60) $types[] = self::STRIP_EXPERIENCES;

        // Default: always suggest nearby attractions
        $types[] = self::STRIP_NEARBY_ATTRACTIONS;

        // Evening spots
        if (in_array($daypart, ['evening', 'night'])) $types[] = self::STRIP_EVENING_SPOTS;

        return array_unique($types);
    }

    private function fetchNearbyContextCandidates(
        float $lat, float $lng, int $radiusM, int $entityId, string $entityType
    ): array {
        try {
            $radiusKm = $radiusM / 1000.0;
            // Rough bounding box for quick SQL filter
            $latDelta = $radiusKm / 111.0;
            $lngDelta = $radiusKm / (111.0 * cos(deg2rad($lat)));

            $rows = DB::table('Sight as s')
                ->select([
                    DB::raw("'sight' as entity_type"),
                    's.SightId as entity_id',
                    's.Title as title',
                    DB::raw("COALESCE(cat.Title,'') as category"),
                    's.Averagerating as rating',
                    's.ReviewCount as review_count',
                    's.Latitude as lat',
                    's.Longitude as lng',
                    's.Slug as slug',
                    DB::raw("NULL as image"),
                    DB::raw("NULL as cuisines"),
                    DB::raw("NULL as price_range"),
                    DB::raw("0 as duration_minutes"),
                ])
                ->leftJoin('Category as cat', 'cat.CategoryId', '=', 's.CategoryId')
                ->whereBetween('s.Latitude', [$lat - $latDelta, $lat + $latDelta])
                ->whereBetween('s.Longitude', [$lng - $lngDelta, $lng + $lngDelta])
                ->where('s.IsActive', 1)
                ->where(function ($q) use ($entityId, $entityType) {
                    if ($entityType === 'sight') $q->where('s.SightId', '!=', $entityId);
                })
                ->limit(60)
                ->get();

            // Also fetch nearby restaurants
            $restaurants = DB::table('Restaurant as r')
                ->select([
                    DB::raw("'restaurant' as entity_type"),
                    'r.RestaurantId as entity_id',
                    'r.Title as title',
                    DB::raw("COALESCE(r.category,'') as category"),
                    'r.Averagerating as rating',
                    'r.ReviewCount as review_count',
                    'r.Latitude as lat',
                    'r.Longitude as lng',
                    'r.Slug as slug',
                    'r.Img1 as image',
                    'r.cuisines as cuisines',
                    'r.PriceRange as price_range',
                    DB::raw("45 as duration_minutes"),
                ])
                ->whereBetween('r.Latitude', [$lat - $latDelta, $lat + $latDelta])
                ->whereBetween('r.Longitude', [$lng - $lngDelta, $lng + $lngDelta])
                ->where('r.IsActive', 1)
                ->where(function ($q) use ($entityId, $entityType) {
                    if ($entityType === 'restaurant') $q->where('r.RestaurantId', '!=', $entityId);
                })
                ->limit(60)
                ->get();

            return array_merge($rows->all(), $restaurants->all());
        } catch (\Throwable) {
            return [];
        }
    }

    private function filterCandidatesForContextType(array $candidates, string $contextType, array $ctx): array
    {
        return array_filter($candidates, function ($c) use ($contextType, $ctx) {
            $type = is_array($c) ? ($c['entity_type'] ?? '') : ($c->entity_type ?? '');

            return match ($contextType) {
                self::STRIP_NEARBY_RESTAURANTS,
                self::STRIP_CAFES             => $type === 'restaurant',
                self::STRIP_NEARBY_ATTRACTIONS,
                self::STRIP_QUICK_STOPS       => $type === 'sight',
                self::STRIP_EXPERIENCES       => $type === 'experience',
                self::STRIP_EVENING_SPOTS     => in_array($type, ['sight', 'restaurant']),
                default                       => true,
            };
        });
    }

    private function scoreContextCandidates(
        array $candidates, array $primaryCard, array $ctx, string $contextType, int $maxRadiusM
    ): array {
        $primaryLat = (float)($primaryCard['lat'] ?? 0);
        $primaryLng = (float)($primaryCard['lng'] ?? 0);
        $maxKm      = $maxRadiusM / 1000.0;
        $daypart    = $ctx['daypart'] ?? 'afternoon';

        $scored = [];
        foreach ($candidates as $c) {
            $cLat   = is_array($c) ? (float)($c['lat'] ?? 0) : (float)($c->lat ?? 0);
            $cLng   = is_array($c) ? (float)($c['lng'] ?? 0) : (float)($c->lng ?? 0);
            $rating = is_array($c) ? (float)($c['rating'] ?? 3) : (float)($c->rating ?? 3);
            $reviews= is_array($c) ? (int)($c['review_count'] ?? 0) : (int)($c->review_count ?? 0);
            $eid    = is_array($c) ? ($c['entity_id'] ?? 0) : ($c->entity_id ?? 0);
            $eType  = is_array($c) ? ($c['entity_type'] ?? '') : ($c->entity_type ?? '');

            $distKm = $this->haversine($primaryLat, $primaryLng, $cLat, $cLng);
            if ($distKm > $maxKm) continue;

            // Scoring formula (Spec ContextStripBuilder §6.4)
            $proximityScore = max(0, 1 - ($distKm / $maxKm));
            $ratingScore    = ($rating / 5.0) * log(max(1, $reviews + 1));
            $ratingScore    = min(1.0, $ratingScore / 3.0); // normalize ~0-1
            $popularityScore= min(1.0, log(max(1, $reviews)) / 10.0);
            $contextMatch   = $this->computeContextMatchScore($c, $contextType, $daypart);

            $finalScore = 0.30 * $proximityScore
                        + 0.20 * $ratingScore
                        + 0.15 * $popularityScore
                        + 0.15 * $contextMatch
                        + 0.10 * 0.5 // diversity — simplified
                        + 0.10 * 0.5; // route score — simplified

            $scored[] = [
                'entity_id'   => $eid,
                'entity_type' => $eType,
                'title'       => is_array($c) ? ($c['title'] ?? '') : ($c->title ?? ''),
                'category'    => is_array($c) ? ($c['category'] ?? '') : ($c->category ?? ''),
                'rating'      => $rating,
                'review_count'=> $reviews,
                'lat'         => $cLat,
                'lng'         => $cLng,
                'slug'        => is_array($c) ? ($c['slug'] ?? '') : ($c->slug ?? ''),
                'image'       => is_array($c) ? ($c['image'] ?? null) : ($c->image ?? null),
                'distance_m'  => (int)($distKm * 1000),
                'cuisines'    => is_array($c) ? ($c['cuisines'] ?? null) : ($c->cuisines ?? null),
                'price_range' => is_array($c) ? ($c['price_range'] ?? null) : ($c->price_range ?? null),
                'score'       => round($finalScore, 4),
                'reason'      => $this->buildContextItemReason($proximityScore, $ratingScore, $contextMatch),
            ];
        }

        return $scored;
    }

    private function computeContextMatchScore(mixed $c, string $contextType, string $daypart): float
    {
        $category = strtolower(is_array($c) ? ($c['category'] ?? '') : ($c->category ?? ''));
        $cuisines = strtolower(is_array($c) ? ($c['cuisines'] ?? '') : ($c->cuisines ?? ''));

        return match ($contextType) {
            self::STRIP_NEARBY_RESTAURANTS => in_array($daypart, ['lunch', 'dinner']) ? 0.9 : 0.6,
            self::STRIP_CAFES              => str_contains($category, 'cafe') || str_contains($cuisines, 'coffee') ? 0.9 : 0.4,
            self::STRIP_QUICK_STOPS        => str_contains($category, 'viewpoint') || str_contains($category, 'park') ? 0.8 : 0.5,
            self::STRIP_EXPERIENCES        => 0.7,
            self::STRIP_EVENING_SPOTS      => in_array($daypart, ['evening', 'night']) ? 0.9 : 0.4,
            default                        => 0.5,
        };
    }

    // =========================================================================
    // SUB-MODULE: CollectionBuilder (Spec §7.2 / CollectionBuilder spec)
    // =========================================================================

    private function resolveCollectionType(array $card, array $ctx): string
    {
        $daypart    = $ctx['daypart']     ?? 'afternoon';
        $role       = $card['primary_role'] ?? '';
        $doNow      = (float)($card['do_now_score'] ?? 0);
        $importance = (float)($card['assembly_score'] ?? 0);

        if (in_array($daypart, ['lunch', 'dinner'])) return self::COLLECTION_RIGHT_NOW;
        if ($doNow > 0.7)                            return self::COLLECTION_RIGHT_NOW;
        if (in_array($daypart, ['evening', 'night'])) return self::COLLECTION_EVENING_SPOTS;
        if ($importance < 0.3)                       return self::COLLECTION_HIDDEN_GEMS;
        if ($role === 'DISCOVERY_ITEM')              return self::COLLECTION_NEARBY_BEST;

        return self::COLLECTION_TRENDING;
    }

    private function buildCollectionItems(
        string $collectionType, array $card, array $ctx, array $allFeed
    ): array {
        // Use feed candidates as collection pool, sorted by relevance
        $usedId = $card['entity_id'];

        $pool = array_filter($allFeed, fn($c) => $c['entity_id'] !== $usedId);

        $items = match ($collectionType) {
            self::COLLECTION_RIGHT_NOW => array_filter($pool, fn($c) =>
                (float)($c['do_now_score'] ?? 0) > 0.5
            ),
            self::COLLECTION_HIDDEN_GEMS => array_filter($pool, fn($c) =>
                (int)($c['review_count'] ?? 999) < 100 && (float)($c['rating'] ?? 0) >= 4.0
            ),
            self::COLLECTION_EVENING_SPOTS => array_filter($pool, fn($c) =>
                in_array($c['entity_type'] ?? '', ['restaurant', 'sight'])
            ),
            self::COLLECTION_FOOD_CLUSTER => array_filter($pool, fn($c) =>
                ($c['entity_type'] ?? '') === 'restaurant'
            ),
            self::COLLECTION_QUICK_EXPLORATIONS => array_filter($pool, fn($c) =>
                (int)($c['duration_minutes'] ?? 999) <= 30
            ),
            default => $pool,
        };

        usort($items, fn($a, $b) =>
            (float)($b['assembly_score'] ?? 0) <=> (float)($a['assembly_score'] ?? 0)
        );

        $result = [];
        foreach (array_slice(array_values($items), 0, 12) as $item) {
            $result[] = [
                'entity_id'    => $item['entity_id'],
                'entity_type'  => $item['entity_type'],
                'title'        => $item['title'],
                'rating'       => $item['rating'],
                'image'        => $item['image'],
                'slug'         => $item['slug'],
                'slugid'       => $item['slugid'],
                'distance_km'  => $item['distance_km']  ?? null,
                'score'        => $item['assembly_score'] ?? 0,
            ];
        }

        return $result;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function getContextStripLabel(string $contextType, array $ctx): string
    {
        $daypart = $ctx['daypart'] ?? 'afternoon';

        return match ($contextType) {
            self::STRIP_NEARBY_RESTAURANTS => in_array($daypart, ['lunch']) ? 'Great lunch spots nearby' : 'Nearby restaurants',
            self::STRIP_NEARBY_ATTRACTIONS => 'More to see nearby',
            self::STRIP_EXPERIENCES        => 'Things to do here',
            self::STRIP_QUICK_STOPS        => 'Quick stops you can add',
            self::STRIP_CAFES              => 'Coffee breaks around here',
            self::STRIP_EVENING_SPOTS      => 'Good spots this evening',
            default                        => 'Nearby',
        };
    }

    private function getCollectionTitle(string $collectionType, array $ctx): string
    {
        $daypart = $ctx['daypart'] ?? 'afternoon';

        return match ($collectionType) {
            self::COLLECTION_RIGHT_NOW          => in_array($daypart, ['lunch']) ? 'Great lunch spots right now' : 'What to do right now',
            self::COLLECTION_TRENDING           => 'Trending around you',
            self::COLLECTION_NEARBY_BEST        => 'Best nearby',
            self::COLLECTION_HIDDEN_GEMS        => 'Hidden gems worth discovering',
            self::COLLECTION_FOOD_CLUSTER       => 'Best food spots in this area',
            self::COLLECTION_QUICK_EXPLORATIONS => 'Quick things to add',
            self::COLLECTION_EVENING_SPOTS      => 'Good spots for this evening',
            default                             => 'Explore more',
        };
    }

    private function buildReason(array $card): string
    {
        $role    = $card['primary_role']   ?? '';
        $moment  = $card['moment_type']    ?? '';
        $urgency = (int)($card['moment_urgency'] ?? 0);

        $parts = array_filter([$role, $moment, $urgency > 0 ? 'URGENT' : null]);
        return implode(' + ', $parts) ?: 'RELEVANCE';
    }

    private function buildContextItemReason(float $prox, float $rating, float $contextMatch): string
    {
        $reasons = [];
        if ($prox > 0.7)        $reasons[] = 'CLOSE';
        if ($rating > 0.6)      $reasons[] = 'HIGH_RATING';
        if ($contextMatch > 0.7)$reasons[] = 'CONTEXT_MATCH';
        return implode(' + ', $reasons) ?: 'NEARBY';
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
