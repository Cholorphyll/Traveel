<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_journey_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned()->nullable();

            $table->bigInteger('current_anchor_entity_id')->nullable();
            $table->bigInteger('previous_anchor_entity_id')->nullable();

            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();

            $table->bigInteger('active_movement_zone_id')->nullable();
            $table->bigInteger('active_local_cluster_id')->nullable();
            $table->bigInteger('active_district_cluster_id')->nullable();

            $table->string('route_pattern', 50)->nullable();
            $table->decimal('route_direction_bearing', 6, 2)->nullable();

            $table->bigInteger('last_entity_id')->nullable();
            $table->bigInteger('second_last_entity_id')->nullable();
            $table->bigInteger('third_last_entity_id')->nullable();

            $table->integer('cumulative_distance_today_m')->nullable();
            $table->decimal('cumulative_walk_time_today_min', 8, 2)->nullable();
            $table->decimal('cumulative_movement_burden_score', 8, 2)->nullable();

            $table->string('current_mode_preference', 20)->nullable();
            $table->string('fatigue_state', 20)->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->index('user_id');
            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_journey_state');
    }
};
