<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_slot_candidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 100);
            $table->bigInteger('trip_id')->nullable();
            $table->bigInteger('feed_moment_id');
            $table->string('slot_type', 100);

            $table->string('candidate_type', 50);
            $table->bigInteger('candidate_id')->nullable();
            $table->bigInteger('collection_id')->nullable();

            $table->bigInteger('area_id')->nullable();
            $table->bigInteger('anchor_id')->nullable();

            // Inherited normalized signals
            $table->decimal('base_relevance_score', 8, 4)->default(0);
            $table->decimal('structural_importance', 8, 4)->default(0);
            $table->decimal('trip_progress_value', 8, 4)->default(0);
            $table->decimal('context_fit', 8, 4)->default(0);
            $table->decimal('route_fit', 8, 4)->default(0);
            $table->decimal('proximity_score', 8, 4)->default(0);
            $table->decimal('timing_fit', 8, 4)->default(0);
            $table->decimal('fatigue_compatibility', 8, 4)->default(0);
            $table->decimal('opportunity_score', 8, 4)->default(0);
            $table->decimal('recovery_value', 8, 4)->default(0);
            $table->decimal('do_now_score', 8, 4)->default(0);
            $table->decimal('do_soon_score', 8, 4)->default(0);
            $table->decimal('trip_important_score', 8, 4)->default(0);
            $table->decimal('need_state_match', 8, 4)->default(0);
            $table->decimal('scenic_quality', 8, 4)->default(0);
            $table->decimal('ease_of_access', 8, 4)->default(0);
            $table->decimal('dwell_quality', 8, 4)->default(0);
            $table->decimal('review_strength', 8, 4)->default(0);
            $table->decimal('uniqueness', 8, 4)->default(0);
            $table->decimal('logistics_feasibility', 8, 4)->default(0);
            $table->decimal('popularity_score', 8, 4)->default(0);
            $table->decimal('area_energy_fit', 8, 4)->default(0);
            $table->decimal('current_comfort_fit', 8, 4)->default(0);
            $table->decimal('ease_of_choice', 8, 4)->default(0);

            // Route / operational
            $table->integer('distance_m')->nullable();
            $table->integer('travel_time_min')->nullable();
            $table->decimal('detour_cost_score', 8, 4)->default(0);

            // Eligibility
            $table->tinyInteger('is_eligible')->default(1);
            $table->text('eligibility_reason')->nullable();

            // Penalties
            $table->decimal('repetition_penalty', 8, 4)->default(0);
            $table->decimal('redundancy_penalty', 8, 4)->default(0);
            $table->decimal('oversaturation_penalty', 8, 4)->default(0);
            $table->decimal('fatigue_penalty', 8, 4)->default(0);

            // Final
            $table->decimal('slot_score_raw', 8, 4)->default(0);
            $table->decimal('slot_penalty_total', 8, 4)->default(0);
            $table->decimal('slot_score_final', 8, 4)->default(0);

            $table->integer('rank_position')->nullable();
            $table->tinyInteger('selected')->default(0);

            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'feed_moment_id', 'slot_type'], 'idx_session_slot');
            $table->index(['candidate_type', 'candidate_id'], 'idx_candidate');
            $table->index(['session_id', 'feed_moment_id', 'selected'], 'idx_selected');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_slot_candidates');
    }
};
