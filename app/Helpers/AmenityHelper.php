<?php

namespace App\Helpers;

class AmenityHelper
{
    public static function getIconName($amenityName)
    {
        // Load configurations
        $icons = config('amenities.icons');
        $defaultIcon = config('amenities.default_icon', 'wifi');

        // Normalize input
        $amenityName = trim($amenityName);

        // 1. Try exact match (case-sensitive)
        if (isset($icons[$amenityName])) {
            return $icons[$amenityName];
        }

        // 2. Try case-insensitive match
        foreach ($icons as $key => $value) {
            if (strcasecmp($key, $amenityName) === 0) {
                return $value;
            }
        }

        // 3. Try partial match (case-insensitive)
        foreach ($icons as $key => $value) {
            if (stripos($amenityName, $key) !== false || stripos($key, $amenityName) !== false) {
                return $value;
            }
        }

        // 4. Fallback to default icon
        return $defaultIcon;
    }
}
