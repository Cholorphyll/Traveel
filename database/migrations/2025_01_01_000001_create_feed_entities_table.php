<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_entities', function (Blueprint $table) {
            $table->bigInteger('item_id')->primary();
            $table->string('item_type', 50);

            // Identity / taxonomy
            $table->boolean('anchor_attraction')->default(0);
            $table->boolean('context_attraction')->default(0);
            $table->boolean('experience')->default(0);
            $table->boolean('food')->default(0);
            $table->boolean('enabler')->default(0);
            $table->boolean('event')->default(0);

            // Quality / popularity
            $table->decimal('popularity_score', 6, 2)->nullable();
            $table->integer('review_count')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->decimal('editorial_priority', 5, 2)->nullable();

            // Operational
            $table->json('opening_hours_json')->nullable();
            $table->integer('duration_min')->nullable();
            $table->json('seasonality_json')->nullable();
            $table->boolean('booking_required')->nullable();
            $table->string('advance_commitment_level', 20)->nullable();

            // Activity
            $table->string('activity_energy', 20)->nullable();
            $table->json('activity_type_json')->nullable();

            // Spatial
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->bigInteger('area_id')->nullable();
            $table->decimal('walkability_score', 5, 2)->nullable();

            // Feed behavior
            $table->boolean('clusterable')->nullable();
            $table->boolean('hero_potential')->nullable();
            $table->boolean('night_compatible')->nullable();
            $table->boolean('family_friendly')->nullable();
            $table->boolean('rain_friendly')->nullable();

            // Comfort / exposure
            $table->string('sun_exposure', 20)->nullable();
            $table->string('rain_exposure', 20)->nullable();
            $table->string('wind_exposure', 20)->nullable();
            $table->boolean('requires_good_visibility')->nullable();
            $table->boolean('benefits_from_clear_sky')->nullable();
            $table->boolean('best_in_heat')->nullable();
            $table->boolean('best_in_cool_evening')->nullable();

            // Implementation extras
            $table->decimal('data_confidence_score', 5, 2)->nullable();
            $table->dateTime('last_foundation_refresh_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('item_type');
            $table->index('area_id');
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_entities');
    }
};
