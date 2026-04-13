<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_context_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 120);
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('trip_id')->nullable();

            $table->dateTime('planning_ts');
            $table->date('local_date');
            $table->tinyInteger('local_hour');
            $table->tinyInteger('local_minute');
            $table->string('daypart', 40)->nullable();

            $table->bigInteger('current_anchor_entity_id')->nullable();
            $table->bigInteger('next_anchor_entity_id')->nullable();
            $table->decimal('current_anchor_strength', 8, 4)->nullable();
            $table->decimal('next_anchor_strength', 8, 4)->nullable();
            $table->string('anchor_progress_stage', 50)->nullable();

            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->string('movement_state', 40)->nullable();
            $table->string('travel_mode', 40)->nullable();

            $table->string('weather_mode', 50)->nullable();
            $table->string('comfort_mode', 50)->nullable();
            $table->decimal('thermal_load_score', 8, 4)->nullable();
            $table->decimal('precipitation_risk_score', 8, 4)->nullable();
            $table->decimal('wind_comfort_score', 8, 4)->nullable();

            $table->string('action_horizon_bias', 50)->nullable();
            $table->decimal('top_feed_urgency_score', 8, 4)->nullable();

            $table->string('meal_window_state', 50)->nullable();
            $table->decimal('break_need_score', 8, 4)->nullable();
            $table->decimal('fatigue_load_score', 8, 4)->nullable();
            $table->decimal('hunger_probability_score', 8, 4)->nullable();
            $table->decimal('hydration_need_score', 8, 4)->nullable();

            $table->string('active_opportunity_class', 80)->nullable();
            $table->decimal('active_opportunity_strength', 8, 4)->nullable();

            $table->integer('recent_heavy_count')->default(0);
            $table->integer('recent_long_count')->default(0);
            $table->integer('recent_indoor_count')->default(0);
            $table->integer('recent_food_count')->default(0);
            $table->integer('recent_anchor_count')->default(0);
            $table->integer('recent_recovery_count')->default(0);

            $table->string('current_sequence_mode', 60)->nullable();
            $table->integer('feed_depth_position')->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['session_id', 'planning_ts'], 'idx_session_time');
            $table->index('trip_id', 'idx_trip');
            $table->index(['current_anchor_entity_id', 'next_anchor_entity_id'], 'idx_anchor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_context_state');
    }
};
