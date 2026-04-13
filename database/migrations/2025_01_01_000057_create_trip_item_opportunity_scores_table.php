<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_item_opportunity_scores', function (Blueprint $table) {
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('candidate_item_id')->unsigned();

            $table->decimal('opportunity_score', 8, 6)->nullable();
            $table->decimal('sunset_window_score', 8, 6)->nullable();
            $table->decimal('meal_window_score', 8, 6)->nullable();
            $table->decimal('nightlife_window_score', 8, 6)->nullable();
            $table->decimal('weather_window_score', 8, 6)->nullable();
            $table->decimal('limited_access_window_score', 8, 6)->nullable();
            $table->decimal('crowd_advantage_window_score', 8, 6)->nullable();
            $table->decimal('window_strength_score', 8, 6)->nullable();
            $table->decimal('time_sensitivity_score', 8, 6)->nullable();
            $table->decimal('window_uniqueness_score', 8, 6)->nullable();
            $table->decimal('reachability_before_window_end', 8, 6)->nullable();

            $table->timestamp('computed_at')->useCurrent();

            $table->primary(['session_id', 'trip_id', 'user_id', 'candidate_item_id']);
            $table->index(['session_id', 'trip_id', 'user_id', 'candidate_item_id'], 'idx_session_trip_user_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_item_opportunity_scores');
    }
};
