<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('context_live_conditions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('context_location_id');
            $table->dateTime('local_datetime');
            $table->string('weather_condition', 100)->nullable();
            $table->decimal('temperature_c', 6, 2)->nullable();
            $table->decimal('rain_probability', 6, 3)->nullable();
            $table->decimal('cloud_cover', 6, 3)->nullable();
            $table->decimal('wind_speed_kph', 6, 2)->nullable();
            $table->decimal('visibility_km', 6, 2)->nullable();
            $table->decimal('uv_index', 6, 2)->nullable();
            $table->dateTime('sunrise_time')->nullable();
            $table->dateTime('sunset_time')->nullable();
            $table->tinyInteger('is_weekend')->default(0);
            $table->tinyInteger('is_holiday')->default(0);
            $table->string('daypart', 50)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['context_location_id', 'local_datetime'], 'idx_location_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('context_live_conditions');
    }
};
