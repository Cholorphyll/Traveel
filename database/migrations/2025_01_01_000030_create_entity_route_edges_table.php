<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_route_edges', function (Blueprint $table) {
            $table->bigInteger('from_entity_id')->unsigned();
            $table->bigInteger('to_entity_id')->unsigned();

            $table->integer('straight_line_distance_m');
            $table->integer('route_distance_m')->nullable();
            $table->decimal('route_duration_walk_min', 8, 2)->nullable();
            $table->decimal('route_duration_drive_min', 8, 2)->nullable();
            $table->decimal('route_duration_transit_min', 8, 2)->nullable();

            $table->integer('elevation_gain_m')->nullable();
            $table->integer('elevation_loss_m')->nullable();

            $table->decimal('walk_friction_score', 6, 2)->nullable();
            $table->decimal('transit_friction_score', 6, 2)->nullable();
            $table->decimal('access_friction_score', 6, 2)->nullable();

            $table->tinyInteger('same_local_cluster')->default(0);
            $table->tinyInteger('same_district_cluster')->default(0);
            $table->tinyInteger('same_movement_zone')->default(0);

            $table->integer('barrier_crossing_count')->default(0);
            $table->decimal('mode_switch_complexity_score', 6, 2)->nullable();

            $table->decimal('route_coherence_prior', 6, 2)->nullable();

            $table->primary(['from_entity_id', 'to_entity_id']);
            $table->index('to_entity_id', 'idx_to_entity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_route_edges');
    }
};
