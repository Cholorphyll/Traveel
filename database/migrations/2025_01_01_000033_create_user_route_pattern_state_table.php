<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_route_pattern_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned()->nullable();

            $table->decimal('avg_step_distance_m', 8, 2)->nullable();
            $table->decimal('max_step_distance_m', 8, 2)->nullable();
            $table->decimal('zone_switch_rate', 6, 2)->nullable();
            $table->decimal('bearing_consistency_score', 6, 2)->nullable();
            $table->decimal('return_to_hub_tendency_score', 6, 2)->nullable();
            $table->decimal('recent_movement_burden_score', 6, 2)->nullable();

            $table->string('inferred_route_pattern', 50)->nullable();

            $table->timestamp('computed_at')->nullable();

            $table->index(['user_id', 'trip_id'], 'idx_user_trip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_route_pattern_state');
    }
};
