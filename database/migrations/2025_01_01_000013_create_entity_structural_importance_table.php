<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_structural_importance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id')->unsigned();
            $table->bigInteger('location_id')->unsigned()->nullable();

            $table->decimal('poi_anchor_score', 8, 4)->nullable();
            $table->decimal('composite_anchor_score', 8, 4)->nullable();
            $table->decimal('collective_destination_score', 8, 4)->nullable();
            $table->decimal('soft_anchor_score', 8, 4)->nullable();
            $table->decimal('trip_value_score', 8, 4)->nullable();
            $table->decimal('structural_importance_score', 8, 4)->nullable();
            $table->string('structural_class', 50)->nullable();

            $table->decimal('intrinsic_importance_score', 8, 4)->nullable();
            $table->decimal('relational_importance_score', 8, 4)->nullable();

            $table->decimal('landmark_strength', 8, 4)->nullable();
            $table->decimal('fame_strength', 8, 4)->nullable();
            $table->decimal('uniqueness_strength', 8, 4)->nullable();
            $table->decimal('popularity_strength', 8, 4)->nullable();
            $table->decimal('review_confidence_strength', 8, 4)->nullable();

            $table->decimal('cluster_membership_strength', 8, 4)->nullable();
            $table->decimal('area_gravity_strength', 8, 4)->nullable();
            $table->decimal('co_visit_strength', 8, 4)->nullable();
            $table->decimal('route_support_strength', 8, 4)->nullable();
            $table->decimal('anchor_adjacency_strength', 8, 4)->nullable();

            $table->bigInteger('destination_cluster_id')->unsigned()->nullable();
            $table->text('structural_notes')->nullable();

            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['entity_type', 'entity_id'], 'uq_entity_structural');
            $table->index('location_id');
            $table->index('structural_class');
            $table->index('structural_importance_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_structural_importance');
    }
};
