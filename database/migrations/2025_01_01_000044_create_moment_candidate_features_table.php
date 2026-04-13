<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moment_candidate_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('session_id');
            $table->integer('slot_index');
            $table->string('item_type', 50);
            $table->bigInteger('item_id');

            $table->string('current_daypart', 50)->nullable();
            $table->integer('current_hour')->nullable();
            $table->integer('minutes_to_sunset')->nullable();
            $table->integer('minutes_to_close')->nullable();

            $table->decimal('distance_minutes', 8, 2)->nullable();
            $table->decimal('route_detour_minutes', 8, 2)->nullable();
            $table->decimal('route_continuity_score', 8, 4)->nullable();

            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->decimal('rain_probability', 8, 4)->nullable();
            $table->decimal('uv_index', 8, 4)->nullable();

            $table->tinyInteger('open_now')->nullable();
            $table->string('indoor_outdoor', 20)->nullable();
            $table->integer('avg_dwell_minutes')->nullable();
            $table->decimal('seated_restfulness_score', 8, 4)->nullable();
            $table->decimal('low_commitment_score', 8, 4)->nullable();
            $table->decimal('scenic_value_score', 8, 4)->nullable();
            $table->decimal('sunset_value_score', 8, 4)->nullable();
            $table->decimal('nightlife_affinity_score', 8, 4)->nullable();
            $table->decimal('hidden_gem_score', 8, 4)->nullable();
            $table->decimal('detour_worthiness_score', 8, 4)->nullable();

            $table->string('user_energy_state', 50)->nullable();
            $table->string('user_need_state', 50)->nullable();

            $table->decimal('proximity_score', 8, 4)->nullable();
            $table->decimal('actionability_score', 8, 4)->nullable();
            $table->decimal('urgency_score', 8, 4)->nullable();
            $table->decimal('need_fit_score', 8, 4)->nullable();
            $table->decimal('social_intensity_score', 8, 4)->nullable();
            $table->decimal('opportunity_score', 8, 4)->nullable();
            $table->decimal('recovery_score', 8, 4)->nullable();

            $table->dateTime('created_at')->useCurrent();

            $table->unique(['session_id', 'slot_index', 'item_type', 'item_id'], 'uq_session_slot_item');
            $table->index(['session_id', 'slot_index'], 'idx_session_slot');
            $table->index(['item_type', 'item_id'], 'idx_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moment_candidate_features');
    }
};
