<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WeatherMonth extends Model
{
    protected $table = 'Weather_Month';
    
    protected $fillable = [
        'location_id',
        'year',
        'month',
        'avg_temperature_max_c',
        'avg_temperature_min_c',
        'avg_temperature_mean_c',
        'avg_apparent_temperature_max_c',
        'avg_apparent_temperature_mean_c',
        'avg_wind_speed_max_ms',
        'avg_cloud_cover_percent',
        'avg_relative_humidity',
        'most_frequent_weather_code',
        'total_precipitation_mm',
        'total_rain_mm',
        'total_snowfall_cm',
        'total_precipitation_hours',
        'avg_precipitation_probability',
        'avg_visibility_km'
    ];

    public $timestamps = false;

    /**
     * Get weather data for a specific city
     *
     * @param int $cityId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWeatherForCity($cityId)
    {
        return self::where('city_id', $cityId)
            ->orderBy('month')
            ->get();
    }

    /**
     * Get the weather condition text based on temperature, wind, and humidity
     * 
     * @return string
     */
    public function getConditionText()
    {
        $condition = DB::table('WeatherCondition_Text')
            ->where('temp_min_c', '<=', $this->avg_apparent_temperature_mean_c)
            ->where('temp_max_c', '>=', $this->avg_apparent_temperature_mean_c)
            ->where('wind_min_ms', '<=', $this->avg_wind_speed_max_ms)
            ->where('wind_max_ms', '>=', $this->avg_wind_speed_max_ms)
            ->where('humidity_min_percent', '<=', $this->avg_relative_humidity)
            ->where('humidity_max_percent', '>=', $this->avg_relative_humidity)
            ->orderBy('id')
            ->first();

        if ($condition) {
            return $condition->condition_text . 
                   ($condition->precipitation_modifier ? ' ' . $condition->precipitation_modifier : '');
        }

        return 'No specific weather summary available for this month';
    }
}