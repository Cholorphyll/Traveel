<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('environment_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('location_id');
            $table->dateTime('timestamp_utc');

            // Time
            $table->dateTime('current_time_local')->nullable();
            $table->string('daypart', 30)->nullable();
            $table->time('sunrise_time')->nullable();
            $table->time('sunset_time')->nullable();
            $table->integer('minutes_to_sunset')->nullable();
            $table->integer('minutes_since_sunrise')->nullable();

            // Weather
            $table->string('weather_type', 30)->nullable();
            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->decimal('feels_like_c', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->nullable();
            $table->decimal('wind_speed_kmh', 6, 2)->nullable();
            $table->decimal('precipitation_mm', 6, 2)->nullable();

            // Comfort
            $table->string('heat_stress_level', 20)->nullable();
            $table->decimal('rain_discomfort_level', 4, 2)->nullable();
            $table->decimal('sun_exposure_level', 4, 2)->nullable();
            $table->decimal('wind_discomfort_level', 4, 2)->nullable();
            $table->decimal('thermal_comfort_score', 6, 2)->nullable();

            // Scenic
            $table->decimal('visibility_quality', 4, 2)->nullable();
            $table->decimal('sunset_quality', 4, 2)->nullable();
            $table->decimal('viewpoint_quality', 4, 2)->nullable();

            // Area energy
            $table->decimal('area_happening_score', 6, 4)->nullable();
            $table->string('area_energy_state', 20)->nullable();

            // Meta
            $table->decimal('data_confidence_score', 4, 2)->nullable();
            $table->dateTime('last_updated_at')->nullable();

            $table->index(['location_id', 'timestamp_utc'], 'idx_location_time');
            $table->index('daypart');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environment_state');
    }
};
