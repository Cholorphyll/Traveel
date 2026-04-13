<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_user_preference_memory', function (Blueprint $table) {
            $table->bigInteger('user_id')->primary();

            $table->decimal('museum_affinity_score', 8, 4)->default(0);
            $table->decimal('food_affinity_score', 8, 4)->default(0);
            $table->decimal('nightlife_affinity_score', 8, 4)->default(0);
            $table->decimal('scenic_affinity_score', 8, 4)->default(0);
            $table->decimal('local_discovery_affinity_score', 8, 4)->default(0);
            $table->decimal('anchor_preference_score', 8, 4)->default(0);
            $table->decimal('exploration_preference_score', 8, 4)->default(0);
            $table->decimal('collection_tolerance_score', 8, 4)->default(0);

            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_user_preference_memory');
    }
};
