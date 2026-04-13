<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_session_state', function (Blueprint $table) {
            $table->string('session_id', 80)->primary();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('trip_id')->nullable();

            $table->bigInteger('current_anchor_id')->nullable();
            $table->string('current_anchor_type', 50)->nullable();

            $table->integer('current_feed_position')->default(0);
            $table->integer('cards_rendered_count')->default(0);
            $table->integer('cards_seen_count')->default(0);
            $table->integer('cards_clicked_count')->default(0);
            $table->integer('cards_saved_count')->default(0);
            $table->integer('cards_skipped_count')->default(0);

            $table->decimal('current_anchor_density_score', 8, 4)->default(0);
            $table->decimal('current_collection_density_score', 8, 4)->default(0);
            $table->decimal('current_decision_confidence_score', 8, 4)->default(0);
            $table->decimal('current_exploration_mode_score', 8, 4)->default(0);
            $table->decimal('current_fatigue_score', 8, 4)->default(0);

            $table->dateTime('last_feed_refresh_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_session_state');
    }
};
