<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_entity_action_horizon_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 120);
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('trip_id')->nullable();
            $table->enum('entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('entity_id')->unsigned();

            // Do Now scores
            $table->decimal('open_now_score', 6, 4)->nullable();
            $table->decimal('immediate_time_fit_score', 6, 4)->nullable();
            $table->decimal('immediate_weather_fit_score', 6, 4)->nullable();
            $table->decimal('immediate_queue_risk_score', 6, 4)->nullable();
            $table->decimal('immediate_accessibility_score', 6, 4)->nullable();
            $table->decimal('immediate_energy_fit_score', 6, 4)->nullable();
            $table->decimal('immediate_distance_fit_score', 6, 4)->nullable();
            $table->decimal('immediate_booking_urgency_score', 6, 4)->nullable();
            $table->decimal('do_now_score', 6, 4)->nullable();

            // DO SOON scores
            $table->decimal('upcoming_time_window_score', 6, 4)->nullable();
            $table->decimal('upcoming_booking_deadline_score', 6, 4)->nullable();
            $table->decimal('upcoming_weather_opportunity_score', 6, 4)->nullable();
            $table->decimal('upcoming_sequence_fit_score', 6, 4)->nullable();
            $table->decimal('upcoming_route_fit_score', 6, 4)->nullable();
            $table->decimal('upcoming_trip_stage_fit_score', 6, 4)->nullable();
            $table->decimal('do_soon_score', 6, 4)->nullable();

            // TRIP IMPORTANT scores
            $table->decimal('trip_anchor_importance_score', 6, 4)->nullable();
            $table->decimal('trip_regret_risk_score', 6, 4)->nullable();
            $table->decimal('trip_uniqueness_score', 6, 4)->nullable();
            $table->decimal('trip_logistics_dependency_score', 6, 4)->nullable();
            $table->decimal('trip_deadline_risk_score', 6, 4)->nullable();
            $table->decimal('trip_planning_relevance_score', 6, 4)->nullable();
            $table->decimal('trip_important_score', 6, 4)->nullable();

            $table->string('assigned_horizon', 30)->nullable();
            $table->dateTime('computed_at')->useCurrent();

            $table->unique(['session_id', 'entity_type', 'entity_id'], 'uniq_session_entity_horizon');
            $table->index(['session_id', 'assigned_horizon'], 'idx_session_horizon');
            $table->index(['entity_type', 'entity_id'], 'idx_entity_horizon');
            $table->index('do_now_score');
            $table->index('do_soon_score');
            $table->index('trip_important_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_entity_action_horizon_scores');
    }
};
