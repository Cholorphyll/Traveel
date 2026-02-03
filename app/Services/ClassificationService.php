<?php

namespace App\Services;

use DB;

class ClassificationService
{
    public function classifyCity($cityId)
    {
        // STEP 1: Get search volume
        $city = DB::table('Location')->where('LocationId', $cityId)->first();
        $searchVolume = $city->avg_monthly_searches;

        // STEP 2: Get attractions, experiences, restaurants for this city
        $sights = DB::table('Sight')->where('LocationId', $cityId)->get();
        $experiences = DB::table('Experience')->where('LocationId', $cityId)->get();
        $restaurants = DB::table('Restaurant')->where('LocationId', $cityId)->get();

        // Merge for max review calculation (attractions & experiences only)
        $merged = collect($sights)->merge($experiences);

        // STEP 3: City popularity benchmark = highest review count
        $cityTopReviews = $merged->max('ReviewCount') ?? 0;

        // STEP 4: SMALL CITY CHECK (<12 items)
        $totalItems = $merged->count();
        $isSmallCity = $totalItems < 12;

        // STEP 5: Classification for each item
        foreach ($sights as $sight) {
            $this->classifyAttraction($sight, $cityTopReviews, $searchVolume, $isSmallCity);
        }

        foreach ($experiences as $exp) {
            $this->classifyAttraction($exp, $cityTopReviews, $searchVolume, $isSmallCity, true);
        }

        foreach ($restaurants as $restaurant) {
            $this->classifyRestaurant($restaurant, $cityTopReviews, $searchVolume);
        }
    }

    // -------------------------------------------------------------
    // ATTRACTIONS + EXPERIENCES
    // -------------------------------------------------------------

    private function classifyAttraction($row, $cityTopReviews, $searchVolume, $isSmallCity, $isExperience = false)
    {
        $reviews = $row->ReviewCount ?? 0;

        // -------- MUST SEE RULES --------
        $isMustSee = false;

        // A) CITY-ADJUSTED THRESHOLD (if search volume available)
        if ($searchVolume > 0) {
            if ($searchVolume > 500000) {     // Big cities
                if ($reviews >= ($cityTopReviews * 0.35)) $isMustSee = true;
            } elseif ($searchVolume > 100000) {
                if ($reviews >= ($cityTopReviews * 0.25)) $isMustSee = true;
            } else {
                if ($reviews >= ($cityTopReviews * 0.20)) $isMustSee = true;
            }
        }

        // B) NO SEARCH VOLUME → 50% OF TOP
        if (!$isMustSee && $searchVolume == 0) {
            if ($reviews >= ($cityTopReviews * 0.50)) {
                $isMustSee = true;
            }
        }

        // C) FALLBACK GLOBAL BASELINE
        if (!$isMustSee) {
            if ($reviews >= 500) {
                $isMustSee = true;
            }
        }

        if ($isMustSee) {
            DB::table($this->getTable($isExperience))
                ->where($this->getKey($isExperience), $row->{$this->getKey($isExperience)})
                ->update(['classification' => 1]);
            return;
        }

        // -------- WORTH-IT RULES --------
        $isWorthIt = false;

        // A) mid popularity if search volume
        if ($searchVolume > 0) {
            if ($reviews >= ($cityTopReviews * 0.20)) {
                $isWorthIt = true;
            }
        }

        // B) 20–30% of top when search volume missing
        if (!$isWorthIt && $searchVolume == 0) {
            if ($reviews >= ($cityTopReviews * 0.25)) {
                $isWorthIt = true;
            }
        }

        // C) SMALL CITY RULE
        if (!$isWorthIt && $isSmallCity) {
            if ($reviews > 0) $isWorthIt = true;
        }

        if ($isWorthIt) {
            DB::table($this->getTable($isExperience))
                ->where($this->getKey($isExperience), $row->{$this->getKey($isExperience)})
                ->update(['classification' => 2]);
            return;
        }

        // -------- OPTIONAL --------
        DB::table($this->getTable($isExperience))
            ->where($this->getKey($isExperience), $row->{$this->getKey($isExperience)})
            ->update(['classification' => 3]);
    }

    // -------------------------------------------------------------
    // RESTAURANTS (STRICTER RULES)
    // -------------------------------------------------------------

    private function classifyRestaurant($row, $cityTopReviews, $searchVolume)
    {
        $reviews = $row->ReviewCount ?? 0;

        // MUST-SEE
        $isMust = false;

        if ($searchVolume > 0) {
            if ($reviews >= $cityTopReviews * 0.40) $isMust = true;
        }

        if (!$isMust && $searchVolume == 0) {
            if ($reviews >= $cityTopReviews * 0.50) $isMust = true;
        }

        // Higher fallback baseline
        if (!$isMust && $reviews >= 750) {
            $isMust = true;
        }

        if ($isMust) {
            DB::table('Restaurant')
                ->where('RestaurantId', $row->RestaurantId)
                ->update(['classification' => 1]);
            return;
        }

        // Restaurants CAN'T be worth-it standalone → ONLY optional
        DB::table('Restaurant')
            ->where('RestaurantId', $row->RestaurantId)
            ->update(['classification' => 3]);
    }

    // -------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------

    private function getTable($isExperience)
    {
        return $isExperience ? 'Experience' : 'Sight';
    }

    private function getKey($isExperience)
    {
        return $isExperience ? 'ExperienceId' : 'SightId';
    }
}
