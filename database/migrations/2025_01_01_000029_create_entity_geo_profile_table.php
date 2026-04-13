<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_geo_profile', function (Blueprint $table) {
            $table->bigInteger('entity_id')->primary();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->string('geo_hash_6', 12)->nullable();
            $table->string('geo_hash_7', 12)->nullable();
            $table->string('geo_hash_8', 12)->nullable();

            $table->bigInteger('local_cluster_id')->nullable();
            $table->bigInteger('district_cluster_id')->nullable();
            $table->bigInteger('movement_zone_id')->nullable();

            $table->integer('elevation_m')->nullable();
            $table->decimal('walkability_score', 5, 2)->nullable();
            $table->decimal('barrier_score', 5, 2)->nullable();

            $table->integer('nearest_transit_distance_m')->nullable();
            $table->integer('nearest_parking_distance_m')->nullable();
            $table->decimal('pedestrian_access_score', 5, 2)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('local_cluster_id');
            $table->index('district_cluster_id');
            $table->index('movement_zone_id');
            $table->index(['lat', 'lng'], 'idx_coords');
            $table->index('geo_hash_6');
            $table->index('geo_hash_7');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_geo_profile');
    }
};
