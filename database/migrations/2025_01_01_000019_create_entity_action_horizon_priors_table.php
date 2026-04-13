<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_action_horizon_priors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('entity_id')->unsigned();

            // Trip-important priors
            $table->decimal('trip_anchor_importance_score', 6, 4)->nullable();
            $table->decimal('trip_regret_risk_score', 6, 4)->nullable();
            $table->decimal('trip_uniqueness_score', 6, 4)->nullable();
            $table->decimal('trip_logistics_dependency_score', 6, 4)->nullable();
            $table->decimal('trip_deadline_risk_score', 6, 4)->nullable();
            $table->decimal('trip_planning_relevance_score', 6, 4)->nullable();

            // Horizon helper priors
            $table->decimal('base_booking_urgency_score', 6, 4)->nullable();
            $table->decimal('base_time_window_strength_score', 6, 4)->nullable();
            $table->decimal('effort_level_score', 6, 4)->nullable();

            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['entity_type', 'entity_id'], 'uniq_entity_horizon_priors');
            $table->index('trip_anchor_importance_score', 'idx_trip_anchor');
            $table->index('trip_regret_risk_score', 'idx_trip_regret');
            $table->index('trip_uniqueness_score', 'idx_trip_unique');
            $table->index('trip_deadline_risk_score', 'idx_trip_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_action_horizon_priors');
    }
};
