<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_structural_base', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id')->unsigned();
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->string('title', 255)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->string('category', 150)->nullable();
            $table->string('sub_category', 150)->nullable();
            $table->decimal('aggregate_rating', 8, 4)->nullable();
            $table->integer('review_count')->nullable();
            $table->decimal('popularity_score', 8, 4)->nullable();
            $table->decimal('recommendation_score', 8, 4)->nullable();
            $table->tinyInteger('is_landmark_manual')->default(0);
            $table->tinyInteger('is_unique_manual')->default(0);
            $table->tinyInteger('is_destination_restaurant')->default(0);
            $table->tinyInteger('is_signature_experience')->default(0);
            $table->decimal('category_anchor_prior', 8, 4)->nullable();
            $table->decimal('category_uniqueness_prior', 8, 4)->nullable();
            $table->decimal('city_review_percentile', 8, 4)->nullable();
            $table->decimal('local_density_score', 8, 4)->nullable();
            $table->decimal('local_category_density', 8, 4)->nullable();
            $table->decimal('city_category_density', 8, 4)->nullable();
            $table->decimal('anchor_signal_coverage', 8, 4)->nullable();
            $table->decimal('tag_distinctiveness_score', 8, 4)->nullable();
            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['entity_type', 'entity_id'], 'uq_entity');
            $table->index('location_id');
            $table->index('entity_type');
            $table->index(['aggregate_rating', 'review_count'], 'idx_rating_reviews');
            $table->index(['lat', 'lng'], 'idx_coords');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_structural_base');
    }
};
