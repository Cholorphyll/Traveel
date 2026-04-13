<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_opportunity_profile', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id')->unsigned();

            // Time / daypart fit
            $table->decimal('sunrise_relevance_score', 6, 3)->default(0);
            $table->decimal('sunset_relevance_score', 6, 3)->default(0);
            $table->decimal('morning_relevance_score', 6, 3)->default(0);
            $table->decimal('afternoon_relevance_score', 6, 3)->default(0);
            $table->decimal('evening_relevance_score', 6, 3)->default(0);
            $table->decimal('night_relevance_score', 6, 3)->default(0);

            // Weather / environment fit
            $table->decimal('indoor_suitability_score', 6, 3)->default(0);
            $table->decimal('outdoor_dependency_score', 6, 3)->default(0);
            $table->decimal('clear_weather_bonus_score', 6, 3)->default(0);
            $table->decimal('rainy_weather_penalty_score', 6, 3)->default(0);

            // Practicality fit
            $table->decimal('crowd_avoidance_value', 6, 3)->default(0);
            $table->decimal('early_day_advantage_score', 6, 3)->default(0);
            $table->decimal('half_day_fit_score', 6, 3)->default(0);
            $table->decimal('quick_stop_fit_score', 6, 3)->default(0);
            $table->decimal('short_commitment_fit_score', 6, 3)->default(0);

            // Need-state fit (food)
            $table->decimal('breakfast_fit_score', 6, 3)->default(0);
            $table->decimal('lunch_fit_score', 6, 3)->default(0);
            $table->decimal('coffee_fit_score', 6, 3)->default(0);
            $table->decimal('dinner_fit_score', 6, 3)->default(0);
            $table->decimal('late_night_fit_score', 6, 3)->default(0);

            // Need-state fit (comfort)
            $table->decimal('hydration_fit_score', 6, 3)->default(0);
            $table->decimal('cooling_break_fit_score', 6, 3)->default(0);
            $table->decimal('shade_break_fit_score', 6, 3)->default(0);
            $table->decimal('sit_down_rest_score', 6, 3)->default(0);

            // Social fit
            $table->decimal('drinks_fit_score', 6, 3)->default(0);
            $table->decimal('stroll_fit_score', 6, 3)->default(0);
            $table->decimal('nightlife_warmup_fit_score', 6, 3)->default(0);

            // Visit shape
            $table->integer('typical_visit_duration_mins')->nullable();

            $table->decimal('profile_confidence', 6, 3)->default(0.50);
            $table->text('source_url')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id'], 'uniq_entity');
            $table->index('entity_type');
            $table->index('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_opportunity_profile');
    }
};
