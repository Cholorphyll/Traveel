<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_item_context_scores', function (Blueprint $table) {
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('candidate_item_id')->unsigned();

            $table->decimal('context_relevance_score', 8, 6)->nullable();
            $table->decimal('movement_fit_score', 8, 6)->nullable();
            $table->decimal('pace_fit_score', 8, 6)->nullable();
            $table->decimal('energy_fit_score', 8, 6)->nullable();
            $table->decimal('weather_fit_score', 8, 6)->nullable();
            $table->decimal('social_fit_score', 8, 6)->nullable();
            $table->decimal('mood_fit_score', 8, 6)->nullable();
            $table->decimal('meal_fit_score', 8, 6)->nullable();
            $table->decimal('recovery_fit_score', 8, 6)->nullable();
            $table->decimal('scenic_fit_score', 8, 6)->nullable();
            $table->decimal('night_fit_score', 8, 6)->nullable();
            $table->decimal('transition_fit_score', 8, 6)->nullable();
            $table->decimal('discovery_fit_score', 8, 6)->nullable();

            $table->timestamp('computed_at')->useCurrent();

            $table->primary(['session_id', 'trip_id', 'user_id', 'candidate_item_id']);
            $table->index(['session_id', 'trip_id', 'user_id', 'candidate_item_id'], 'idx_session_trip_user_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_item_context_scores');
    }
};
