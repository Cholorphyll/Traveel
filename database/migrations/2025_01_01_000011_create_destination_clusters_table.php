<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_clusters', function (Blueprint $table) {
            $table->bigIncrements('cluster_id');
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->string('cluster_name', 255)->nullable();
            $table->string('cluster_type', 100)->nullable();
            $table->decimal('centroid_lat', 10, 8)->nullable();
            $table->decimal('centroid_lng', 11, 8)->nullable();
            $table->integer('entity_count')->nullable();
            $table->integer('sight_count')->nullable();
            $table->integer('restaurant_count')->nullable();
            $table->integer('experience_count')->nullable();
            $table->decimal('avg_rating', 8, 4)->nullable();
            $table->bigInteger('total_review_count')->nullable();
            $table->decimal('density_score', 8, 4)->nullable();
            $table->decimal('cohesion_score', 8, 4)->nullable();
            $table->decimal('diversity_score', 8, 4)->nullable();
            $table->decimal('walkability_score', 8, 4)->nullable();
            $table->decimal('anchor_concentration_score', 8, 4)->nullable();
            $table->decimal('destination_gravity_score', 8, 4)->nullable();
            $table->decimal('collective_identity_strength', 8, 4)->nullable();
            $table->decimal('cluster_theme_clarity', 8, 4)->nullable();
            $table->decimal('cluster_repeatability', 8, 4)->nullable();
            $table->decimal('cluster_saturation_quality', 8, 4)->nullable();
            $table->decimal('cluster_collective_strength', 8, 4)->nullable();
            $table->tinyInteger('is_collective_destination')->default(0);
            $table->tinyInteger('is_composite_anchor_zone')->default(0);
            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->index('location_id');
            $table->index('cluster_type');
            $table->index('destination_gravity_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_clusters');
    }
};
