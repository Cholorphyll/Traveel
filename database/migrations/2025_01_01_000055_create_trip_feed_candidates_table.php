<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_feed_candidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('candidate_item_id')->unsigned();
            $table->string('candidate_item_type', 50)->nullable();
            $table->bigInteger('entity_id')->unsigned()->nullable();
            $table->string('entity_type', 50)->nullable();

            // Identity / item flags
            $table->tinyInteger('is_landmark')->default(0);
            $table->tinyInteger('is_area')->default(0);
            $table->tinyInteger('is_restaurant')->default(0);
            $table->tinyInteger('is_cafe')->default(0);
            $table->tinyInteger('is_nightlife')->default(0);
            $table->tinyInteger('is_viewpoint')->default(0);
            $table->tinyInteger('is_scenic')->default(0);
            $table->tinyInteger('is_quick_stop')->default(0);
            $table->tinyInteger('is_high_commitment')->default(0);
            $table->tinyInteger('is_utility')->default(0);
            $table->tinyInteger('is_social_area')->default(0);

            // Item behavior / metadata
            $table->integer('avg_dwell_minutes')->nullable();
            $table->decimal('quick_service_score', 6, 4)->nullable();
            $table->decimal('low_effort_access_score', 6, 4)->nullable();
            $table->decimal('seat_likelihood_score', 6, 4)->nullable();
            $table->decimal('shade_score', 6, 4)->nullable();
            $table->decimal('calmness_score', 6, 4)->nullable();
            $table->decimal('visual_reward_score', 6, 4)->nullable();
            $table->decimal('photo_value_score', 6, 4)->nullable();
            $table->decimal('wow_factor_score', 6, 4)->nullable();
            $table->decimal('local_character_score', 6, 4)->nullable();
            $table->decimal('novelty_score', 6, 4)->nullable();
            $table->decimal('serendipity_score', 6, 4)->nullable();
            $table->decimal('cluster_energy_score', 6, 4)->nullable();
            $table->decimal('lingerability_score', 6, 4)->nullable();
            $table->decimal('walkability_score', 6, 4)->nullable();
            $table->decimal('surrounding_density_score', 6, 4)->nullable();
            $table->decimal('experience_variety_score', 6, 4)->nullable();
            $table->decimal('cluster_identity_score', 6, 4)->nullable();
            $table->decimal('destination_signature_score', 6, 4)->nullable();
            $table->decimal('rarity_score', 6, 4)->nullable();
            $table->decimal('utility_score', 6, 4)->nullable();
            $table->decimal('impulse_stop_score', 6, 4)->nullable();
            $table->decimal('destination_dining_score', 6, 4)->nullable();
            $table->decimal('dinner_ambience_score', 6, 4)->nullable();
            $table->decimal('post_activity_recovery_fit', 6, 4)->nullable();

            // Real-time availability / access
            $table->tinyInteger('opening_status_now')->nullable();
            $table->decimal('open_now_score', 6, 4)->nullable();
            $table->tinyInteger('closing_soon_flag')->default(0);
            $table->tinyInteger('service_window_active')->default(0);
            $table->decimal('late_open_score', 6, 4)->nullable();
            $table->decimal('time_of_day_visual_fit', 6, 4)->nullable();
            $table->decimal('weather_visual_bonus', 6, 4)->nullable();
            $table->decimal('opening_window_future_fit', 6, 4)->nullable();

            // Route / movement
            $table->integer('distance_meters')->nullable();
            $table->decimal('travel_time_minutes', 6, 2)->nullable();
            $table->decimal('detour_cost_minutes', 6, 2)->nullable();
            $table->decimal('direction_alignment_score', 6, 4)->nullable();
            $table->decimal('same_area_cluster_score', 6, 4)->nullable();
            $table->decimal('next_leg_alignment_score', 6, 4)->nullable();
            $table->decimal('forward_route_fit', 6, 4)->nullable();
            $table->decimal('route_alignment_score', 6, 4)->nullable();
            $table->decimal('route_convenience_score', 6, 4)->nullable();
            $table->decimal('midday_proximity_score', 6, 4)->nullable();
            $table->decimal('short_distance_score', 6, 4)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'trip_id', 'user_id'], 'idx_feed_session');
            $table->index(['session_id', 'trip_id', 'user_id', 'candidate_item_id'], 'idx_feed_candidates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_feed_candidates');
    }
};
