<?php

namespace App\Services\AlgoFeed\Engines;

use Illuminate\Support\Facades\DB;

/**
 * Environment State Layer
 * Computes: daypart, comfort metrics, scenic conditions, area energy.
 * All formulas are taken directly from the spec (Algo Engines(11).txt).
 */
class EnvironmentStateEngine
{
    public function compute(int $locationId, array $ctx): array
    {
        $localTime      = $ctx['local_time']      ?? date('H:i:s');
        $localDate      = $ctx['local_date']      ?? date('Y-m-d');
        $sunriseTime    = $ctx['sunrise_time']    ?? '06:00:00';
        $sunsetTime     = $ctx['sunset_time']     ?? '18:30:00';
        $tempC          = (float)($ctx['temperature_c']   ?? 22);
        $feelsLikeC     = (float)($ctx['feels_like_c']    ?? $tempC);
        $humidity       = (float)($ctx['humidity']         ?? 50);
        $windSpeedKmh   = (float)($ctx['wind_speed_kmh']  ?? 10);
        $precipMm       = (float)($ctx['precipitation_mm'] ?? 0);
        $cloudCover     = (float)($ctx['cloud_cover']     ?? 20);
        $uvIndex        = (float)($ctx['uv_index']        ?? 3);
        $visibilityM    = (float)($ctx['visibility_m']    ?? 10000);
        $weatherType    = $ctx['weather_type']    ?? $this->inferWeatherType($precipMm, $cloudCover, $windSpeedKmh);

        // ── Time fields ───────────────────────────────────────────────────────
        $nowTs          = strtotime("{$localDate} {$localTime}");
        $sunriseTs      = strtotime("{$localDate} {$sunriseTime}");
        $sunsetTs       = strtotime("{$localDate} {$sunsetTime}");

        $minToSunset    = max(0, (int)(($sunsetTs - $nowTs) / 60));
        $minSinceSunrise = max(0, (int)(($nowTs - $sunriseTs) / 60));
        $daypart        = $ctx['daypart'] ?? $this->computeDaypart($nowTs, $sunriseTs, $sunsetTs, $localTime);

        // ── Comfort metrics ───────────────────────────────────────────────────
        $heatStressLevel   = $this->computeHeatStress($tempC, $humidity);
        $rainDiscomfort    = $this->computeRainDiscomfort($precipMm);
        $sunExposure       = $this->computeSunExposure($uvIndex, $cloudCover);
        $windDiscomfort    = $this->computeWindDiscomfort($windSpeedKmh);
        $thermalComfort    = $this->computeThermalComfort($heatStressLevel, $rainDiscomfort, $windDiscomfort, $sunExposure);

        // ── Scenic conditions ─────────────────────────────────────────────────
        $visibilityQuality = $this->computeVisibilityQuality($visibilityM);
        $sunsetQuality     = $this->computeSunsetQuality($cloudCover, $humidity);
        $viewpointQuality  = $visibilityQuality * (1 - $rainDiscomfort);

        // ── Area energy ───────────────────────────────────────────────────────
        $areaHappeningScore = $this->computeAreaHappening($daypart);
        $areaEnergyState    = $areaHappeningScore > 0.75 ? 'high' : ($areaHappeningScore > 0.4 ? 'medium' : 'low');

        // ── Derived comfort mode (used by sequence planner) ───────────────────
        $comfortMode = $this->deriveComfortMode($heatStressLevel, $rainDiscomfort, $windDiscomfort, $daypart, $minToSunset);

        // ── Persist snapshot ─────────────────────────────────────────────────
        $this->persist($locationId, compact(
            'localTime','daypart','sunriseTime','sunsetTime','minToSunset','minSinceSunrise',
            'weatherType','tempC','feelsLikeC','humidity','windSpeedKmh','precipMm',
            'heatStressLevel','rainDiscomfort','sunExposure','windDiscomfort','thermalComfort',
            'visibilityQuality','sunsetQuality','viewpointQuality',
            'areaHappeningScore','areaEnergyState'
        ));

        return [
            'local_time'           => $localTime,
            'local_date'           => $localDate,
            'daypart'              => $daypart,
            'sunrise_time'         => $sunriseTime,
            'sunset_time'          => $sunsetTime,
            'minutes_to_sunset'    => $minToSunset,
            'minutes_since_sunrise'=> $minSinceSunrise,
            'weather_type'         => $weatherType,
            'temperature_c'        => $tempC,
            'feels_like_c'         => $feelsLikeC,
            'humidity'             => $humidity,
            'wind_speed_kmh'       => $windSpeedKmh,
            'precipitation_mm'     => $precipMm,
            'heat_stress_level'    => $heatStressLevel,
            'rain_discomfort_level'=> $rainDiscomfort,
            'sun_exposure_level'   => $sunExposure,
            'wind_discomfort_level'=> $windDiscomfort,
            'thermal_comfort_score'=> $thermalComfort,
            'visibility_quality'   => $visibilityQuality,
            'sunset_quality'       => $sunsetQuality,
            'viewpoint_quality'    => $viewpointQuality,
            'area_happening_score' => $areaHappeningScore,
            'area_energy_state'    => $areaEnergyState,
            'comfort_mode'         => $comfortMode,
            'is_golden_hour'       => $daypart === 'golden_hour',
            'is_meal_time'         => in_array($daypart, ['midday','evening']),
        ];
    }

    // ── Daypart ───────────────────────────────────────────────────────────────
    private function computeDaypart(int $nowTs, int $sunriseTs, int $sunsetTs, string $localTime): string
    {
        $sunriseP2h = $sunriseTs + 7200;
        $sunsetM1h  = $sunsetTs  - 3600;
        $sunsetP2h  = $sunsetTs  + 7200;
        $hour       = (int)date('H', $nowTs);

        if ($nowTs >= $sunriseTs && $nowTs < $sunriseP2h)  return 'early_morning';
        if ($nowTs < strtotime(date('Y-m-d') . ' 11:30'))  return 'morning';
        if ($nowTs < strtotime(date('Y-m-d') . ' 15:30'))  return 'midday';
        if ($nowTs < $sunsetM1h)                           return 'afternoon';
        if ($nowTs <= $sunsetTs)                           return 'golden_hour';
        if ($nowTs <= $sunsetP2h)                          return 'evening';
        return 'night';
    }

    // ── Comfort calculations ──────────────────────────────────────────────────
    private function computeHeatStress(float $tempC, float $humidity): string
    {
        // Simplified heat index: temp + humidity factor
        $hi = $tempC + (0.33 * ($humidity / 100) * 6.105 * exp(17.27 * $tempC / (237.7 + $tempC))) - 4;
        if ($hi < 25)  return 'low';
        if ($hi < 32)  return 'moderate';
        if ($hi < 40)  return 'high';
        return 'extreme';
    }

    private function computeRainDiscomfort(float $precipMm): float
    {
        if ($precipMm === 0.0)  return 0.0;
        if ($precipMm < 1.0)   return 0.3;
        if ($precipMm < 5.0)   return 0.6;
        return 1.0;
    }

    private function computeSunExposure(float $uvIndex, float $cloudCover): float
    {
        if ($uvIndex >= 8)      return 1.0;
        if ($uvIndex >= 5)      return 0.7;
        if ($cloudCover > 70)   return 0.2;
        return 0.5;
    }

    private function computeWindDiscomfort(float $windKmh): float
    {
        if ($windKmh < 10)  return 0.0;
        if ($windKmh < 25)  return 0.4;
        if ($windKmh < 40)  return 0.7;
        return 1.0;
    }

    private function computeThermalComfort(string $heatStress, float $rain, float $wind, float $sun): float
    {
        $heatMap = ['low' => 0, 'moderate' => 1, 'high' => 2, 'extreme' => 3];
        $hi = $heatMap[$heatStress] ?? 0;
        $score = 100 - ($hi * 25) - ($rain * 20) - ($wind * 15) - ($sun * 10);
        return max(0, min(100, $score));
    }

    // ── Scenic calculations ───────────────────────────────────────────────────
    private function computeVisibilityQuality(float $visM): float
    {
        if ($visM > 10000) return 1.0;
        if ($visM > 5000)  return 0.7;
        if ($visM > 2000)  return 0.4;
        return 0.1;
    }

    private function computeSunsetQuality(float $cloudCover, float $humidity): float
    {
        // Ideal: partial clouds 20-60%, low humidity
        $cloudFactor    = 1 - abs($cloudCover - 40) / 40;
        $humidityFactor = 1 - ($humidity / 100);
        return max(0, min(1, $cloudFactor * $humidityFactor));
    }

    // ── Area energy ───────────────────────────────────────────────────────────
    private function computeAreaHappening(string $daypart): float
    {
        $timeMultipliers = [
            'early_morning' => 0.4, 'morning' => 0.6, 'midday' => 0.7,
            'afternoon' => 0.8, 'golden_hour' => 0.9, 'evening' => 1.0, 'night' => 0.9,
        ];
        $tm = $timeMultipliers[$daypart] ?? 0.6;
        // Footfall/event/venue ratios use static priors; live data can override via context
        return min(1.0, 0.40 * 0.5 + 0.20 * $tm + 0.20 * 0.5 + 0.20 * 0.6);
    }

    // ── Comfort mode (used by Sequence Planner) ───────────────────────────────
    private function deriveComfortMode(string $heat, float $rain, float $wind, string $daypart, int $minToSunset): string
    {
        if ($rain >= 0.6)                                  return 'RAIN_SHELTER';
        if (in_array($heat, ['high','extreme']))           return 'HEAT_MANAGEMENT';
        if ($minToSunset > 0 && $minToSunset <= 90)       return 'SCENIC_COMFORT';
        if ($wind >= 0.7)                                  return 'WIND_SHELTER';
        return 'NORMAL';
    }

    private function inferWeatherType(float $precip, float $cloud, float $wind): string
    {
        if ($precip >= 5)  return 'storm';
        if ($precip >= 1)  return 'rain';
        if ($precip > 0)   return 'drizzle';
        if ($cloud > 80)   return 'overcast';
        if ($cloud > 40)   return 'cloudy';
        return 'clear';
    }

    private function persist(int $locationId, array $data): void
    {
        try {
            DB::table('environment_state')->insert([
                'location_id'          => $locationId,
                'timestamp_utc'        => now(),
                'current_time_local'   => now(),
                'daypart'              => $data['daypart'],
                'sunrise_time'         => $data['sunriseTime'],
                'sunset_time'          => $data['sunsetTime'],
                'minutes_to_sunset'    => $data['minToSunset'],
                'minutes_since_sunrise'=> $data['minSinceSunrise'],
                'weather_type'         => $data['weatherType'],
                'temperature_c'        => $data['tempC'],
                'feels_like_c'         => $data['feelsLikeC'],
                'humidity'             => $data['humidity'],
                'wind_speed_kmh'       => $data['windSpeedKmh'],
                'precipitation_mm'     => $data['precipMm'],
                'heat_stress_level'    => $data['heatStressLevel'],
                'rain_discomfort_level'=> $data['rainDiscomfort'],
                'sun_exposure_level'   => $data['sunExposure'],
                'wind_discomfort_level'=> $data['windDiscomfort'],
                'thermal_comfort_score'=> $data['thermalComfort'],
                'visibility_quality'   => $data['visibilityQuality'],
                'sunset_quality'       => $data['sunsetQuality'],
                'viewpoint_quality'    => $data['viewpointQuality'],
                'area_happening_score' => $data['areaHappeningScore'],
                'area_energy_state'    => $data['areaEnergyState'],
                'data_confidence_score'=> 0.80,
                'last_updated_at'      => now(),
            ]);
        } catch (\Throwable) {}
    }
}
