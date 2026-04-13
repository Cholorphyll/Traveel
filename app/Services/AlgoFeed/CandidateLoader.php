<?php

namespace App\Services\AlgoFeed;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * CandidateLoader — Loads and normalises Sight / Experience / Restaurant rows
 * into a unified candidate shape that all downstream engines consume.
 *
 * Unified candidate fields (all nullable unless noted):
 *   candidate_item_id   string   "{type}_{id}"
 *   candidate_item_type string   sight|experience|restaurant
 *   entity_id           int
 *   entity_type         string   sight|experience|restaurant
 *   title               string
 *   lat                 float
 *   lng                 float
 *   review_count        int
 *   avg_rating          float    0-5
 *   tier                int      1-4
 *   img                 string|null
 *   slug                string|null
 *   slugid              string|null
 *   is_must_see         bool
 *   duration_minutes    int      avg dwell time
 *   popularity_raw      float    0-100
 *   location_id         int
 *   address             string|null
 *   category_title      string|null
 *   short_desc          string|null
 *   cuisines            string|null  (restaurant)
 *   price_range         string|null  (restaurant)
 *   timings             string|null  (restaurant/sight)
 *   is_landmark         bool
 *   is_restaurant       bool
 *   is_experience       bool
 *   is_outdoor          bool
 *   is_scenic           bool
 *   is_nightlife        bool
 *   is_high_commitment  bool
 *   is_quick_stop       bool
 *   is_eligible         bool     (default true — eligibility engine may flip)
 */
class CandidateLoader
{
    protected int $locationId;

    // Categories considered scenic/viewpoint
    private const SCENIC_KEYWORDS   = ['viewpoint','scenic','panorama','sunset','vista','overlook','waterfront'];
    private const NIGHTLIFE_KEYWORDS = ['bar','pub','club','nightclub','lounge','rooftop bar','disco'];
    private const OUTDOOR_KEYWORDS   = ['park','garden','beach','hiking','trail','nature','forest','mountain','lake','waterfall','outdoor'];

    public function __construct(int $locationId)
    {
        $this->locationId = $locationId;
    }

    public function load(): Collection
    {
        $sights      = $this->loadSights();
        $experiences = $this->loadExperiences();
        $restaurants = $this->loadRestaurants();

        return $sights->merge($experiences)->merge($restaurants)->values();
    }

    // ─── Sights ──────────────────────────────────────────────────────────────

    private function loadSights(): Collection
    {
        $rows = DB::table('Sight as s')
            ->select(
                's.SightId', 's.Title', 's.Latitude', 's.Longitude',
                's.ReviewCount', 's.Averagerating', 's.tier',
                's.Img1', 's.IsMustSee', 's.MustSee', 's.LocationId',
                's.Slug', 's.slugid', 's.Address', 's.CategoryId',
                's.MicroSummary as short_desc', 's.popularity_score',
                's.duration', 's.WeightedRating', 's.KnownFor',
                DB::raw("'sight' as entity_type"),
                's.Status'
            )
            ->where('s.LocationId', $this->locationId)
            ->where('s.Status', 1)
            ->whereNotNull('s.Latitude')
            ->whereNotNull('s.Longitude')
            ->get();

        return $rows->map(fn($r) => $this->normalizeSight($r));
    }

    private function normalizeSight(object $r): array
    {
        $isMustSee = (bool)($r->IsMustSee ?? $r->MustSee ?? false);
        $tier      = (int)($r->tier ?? 4);
        $popRaw    = (float)($r->popularity_score ?? 0);
        $duration  = $this->parseDuration($r->duration ?? null);
        $catTitle  = $this->resolveCategoryTitle($r->CategoryId ?? null);

        return array_merge(
            $this->baseFields('sight', $r->SightId, $r),
            [
                'is_must_see'        => $isMustSee,
                'duration_minutes'   => $duration,
                'popularity_raw'     => $popRaw,
                'category_title'     => $catTitle,
                'short_desc'         => $r->short_desc ?? null,
                'is_landmark'        => $tier <= 2 || $isMustSee,
                'is_restaurant'      => false,
                'is_experience'      => false,
                'is_high_commitment' => $duration > 90,
                'is_quick_stop'      => $duration > 0 && $duration <= 30,
                'is_outdoor'         => $this->hasKeyword($catTitle . ' ' . ($r->KnownFor ?? ''), self::OUTDOOR_KEYWORDS),
                'is_scenic'          => $this->hasKeyword($catTitle . ' ' . ($r->KnownFor ?? ''), self::SCENIC_KEYWORDS),
                'is_nightlife'       => false,
                'is_area'            => false,
                'weighted_rating'    => (float)($r->WeightedRating ?? 0),
            ]
        );
    }

    // ─── Experiences ─────────────────────────────────────────────────────────

    private function loadExperiences(): Collection
    {
        $rows = DB::table('Experience as e')
            ->select(
                'e.ExperienceId', 'e.Name as Title', 'e.Latitude', 'e.Longitude',
                'e.ViatorReviewCount as ReviewCount',
                'e.ViatorAggregationRating as Averagerating',
                'e.tier', 'e.Img1', 'e.LocationId',
                'e.Slug', 'e.slugid', 'e.popularity_score',
                'e.Duration as duration', 'e.Exclusive',
                DB::raw("'experience' as entity_type")
            )
            ->where('e.LocationId', $this->locationId)
            ->where('e.IsActive', 1)
            ->whereNotNull('e.Latitude')
            ->whereNotNull('e.Longitude')
            ->get();

        return $rows->map(fn($r) => $this->normalizeExperience($r));
    }

    private function normalizeExperience(object $r): array
    {
        $tier     = (int)($r->tier ?? 3);
        $duration = $this->parseDuration($r->duration ?? null);

        return array_merge(
            $this->baseFields('experience', $r->ExperienceId, $r),
            [
                'is_must_see'        => $tier === 1,
                'duration_minutes'   => $duration,
                'popularity_raw'     => (float)($r->popularity_score ?? 0),
                'category_title'     => 'Experience',
                'short_desc'         => null,
                'is_landmark'        => $tier <= 2,
                'is_restaurant'      => false,
                'is_experience'      => true,
                'is_high_commitment' => $duration > 120,
                'is_quick_stop'      => $duration > 0 && $duration <= 45,
                'is_outdoor'         => false,
                'is_scenic'          => false,
                'is_nightlife'       => false,
                'is_area'            => false,
                'weighted_rating'    => 0.0,
            ]
        );
    }

    // ─── Restaurants ─────────────────────────────────────────────────────────

    private function loadRestaurants(): Collection
    {
        $rows = DB::table('Restaurant as r')
            ->select(
                'r.RestaurantId', 'r.Title', 'r.Latitude', 'r.Longitude',
                'r.ReviewCount', 'r.Averagerating', 'r.tier',
                'r.LocationId', 'r.Slug', 'r.slugid',
                'r.Timings', 'r.PriceRange', 'r.cuisines', 'r.meals',
                'r.Address', 'r.IsMustSee', 'r.PopularityIndex',
                'r.features', 'r.category',
                DB::raw("'restaurant' as entity_type")
            )
            ->where('r.LocationId', $this->locationId)
            ->where('r.IsActive', 1)
            ->whereNotNull('r.Latitude')
            ->whereNotNull('r.Longitude')
            ->get();

        return $rows->map(fn($r) => $this->normalizeRestaurant($r));
    }

    private function normalizeRestaurant(object $r): array
    {
        $tier    = (int)($r->tier ?? 3);
        $catStr  = strtolower((string)($r->category ?? '') . ' ' . (string)($r->features ?? ''));
        $isNight = $this->hasKeyword($catStr, self::NIGHTLIFE_KEYWORDS);

        return array_merge(
            $this->baseFields('restaurant', $r->RestaurantId, $r),
            [
                'is_must_see'        => (bool)($r->IsMustSee ?? false),
                'duration_minutes'   => 60,
                'popularity_raw'     => (float)($r->PopularityIndex ?? 0),
                'category_title'     => 'Restaurant',
                'short_desc'         => null,
                'cuisines'           => $r->cuisines ?? null,
                'price_range'        => $r->PriceRange ?? null,
                'timings'            => $r->Timings ?? null,
                'is_landmark'        => $tier === 1,
                'is_restaurant'      => true,
                'is_experience'      => false,
                'is_high_commitment' => false,
                'is_quick_stop'      => false,
                'is_outdoor'         => false,
                'is_scenic'          => false,
                'is_nightlife'       => $isNight,
                'is_area'            => false,
                'weighted_rating'    => 0.0,
            ]
        );
    }

    // ─── Shared helpers ──────────────────────────────────────────────────────

    private function baseFields(string $type, int $entityId, object $r): array
    {
        $tier     = (int)($r->tier ?? 4);
        $reviews  = (int)($r->ReviewCount ?? 0);
        $rating   = (float)($r->Averagerating ?? 0);

        return [
            'candidate_item_id'   => "{$type}_{$entityId}",
            'candidate_item_type' => $type,
            'entity_id'           => $entityId,
            'entity_type'         => $type,
            'title'               => $r->Title ?? '',
            'lat'                 => (float)($r->Latitude ?? 0),
            'lng'                 => (float)($r->Longitude ?? 0),
            'review_count'        => $reviews,
            'avg_rating'          => $rating,
            'tier'                => $tier,
            'img'                 => $r->Img1 ?? null,
            'slug'                => $r->Slug ?? null,
            'slugid'              => $r->slugid ?? null,
            'location_id'         => (int)($r->LocationId ?? 0),
            'address'             => $r->Address ?? null,
            // Eligibility default
            'is_eligible'         => true,
            'eligibility_reason'  => null,
            // All score fields initialised to 0 for safety
            'context_relevance_score'      => 0.0,
            'structural_importance_score'  => 0.0,
            'anchor_score'                 => 0.0,
            'trip_importance_score'        => 0.0,
            'opportunity_score'            => 0.0,
            'do_now_score'                 => 0.0,
            'do_soon_score'                => 0.0,
            'do_later_score'               => 0.0,
            'next_day_score'               => 0.0,
            'proximity_score'              => 0.0,
            'route_fit'                    => 0.0,
            'primary_role'                 => null,
            'role_group'                   => null,
            'role_confidence'              => 0.0,
            'role_selection_score'         => 0.0,
            'slot_type'                    => null,
            'slot_score_final'             => 0.0,
            'moment_primary_family'        => null,
            'moment_primary_type'          => null,
            'moment_label_short'           => null,
            'moment_label_medium'          => null,
            'moment_urgency_level'         => 0.0,
            'final_assembly_score'         => 0.0,
            'is_suppressed'                => false,
            // Role-assignment input signals (defaults; overwritten by engines)
            'lingerability_score'          => 0.4,
            'cluster_energy_score'         => 0.3,
            'walkability_score'            => 0.5,
            'surrounding_density_score'    => 0.4,
            'composite_anchor_score'       => 0.0,
            'collective_destination_score' => 0.0,
            'destination_dining_score'     => 0.0,
            'dinner_ambience_score'        => 0.0,
            'post_activity_recovery_fit'   => 0.0,
            'evening_fit_score'            => 0.0,
            'lunch_window_score'           => 0.0,
            'hunger_need_score'            => 0.0,
            'fatigue_need_score'           => 0.0,
            'slot_family'                  => null,
            'distance_km'                  => 5.0,
            'low_detour_score'             => 0.5,
            'quick_service_score'          => 0.5,
        ];
    }

    private function parseDuration(?string $raw): int
    {
        if (empty($raw)) return 60;
        // Try numeric first (stored as minutes)
        if (is_numeric($raw)) return (int)$raw;
        // Try "X hours Y minutes" patterns
        $hours = 0; $mins = 0;
        if (preg_match('/(\d+)\s*h/i', $raw, $m)) $hours = (int)$m[1];
        if (preg_match('/(\d+)\s*m/i', $raw, $m)) $mins  = (int)$m[1];
        $total = $hours * 60 + $mins;
        return $total > 0 ? $total : 60;
    }

    private function hasKeyword(string $haystack, array $keywords): bool
    {
        $lower = strtolower($haystack);
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) return true;
        }
        return false;
    }

    private function resolveCategoryTitle(?int $categoryId): string
    {
        if (!$categoryId) return 'Attraction';
        static $cache = [];
        if (!isset($cache[$categoryId])) {
            $row = DB::table('Category')->where('CategoryId', $categoryId)->value('Title');
            $cache[$categoryId] = $row ?? 'Attraction';
        }
        return $cache[$categoryId];
    }
}
