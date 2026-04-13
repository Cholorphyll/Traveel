<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_route_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned()->nullable();
            $table->bigInteger('candidate_entity_id')->unsigned();

            $table->bigInteger('reference_entity_id')->nullable();
            $table->string('route_pattern', 50)->nullable();

            $table->decimal('route_continuity_score', 6, 2)->nullable();
            $table->decimal('movement_cost_score', 6, 2)->nullable();
            $table->decimal('direction_coherence_score', 6, 2)->nullable();
            $table->decimal('backtrack_penalty_score', 6, 2)->nullable();
            $table->decimal('cluster_fit_score', 6, 2)->nullable();
            $table->decimal('excursion_penalty_score', 6, 2)->nullable();
            $table->decimal('route_override_score', 6, 2)->nullable();

            $table->decimal('final_route_score', 6, 2)->nullable();

            $table->timestamp('computed_at')->nullable();

            $table->index(['user_id', 'trip_id', 'candidate_entity_id'], 'idx_user_trip_candidate');
            $table->index('final_route_score', 'idx_route_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_route_scores');
    }
};
