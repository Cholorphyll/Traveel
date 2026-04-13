<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_candidate_memory_features', function (Blueprint $table) {
            $table->bigInteger('session_id');
            $table->bigInteger('item_id');
            $table->string('item_type', 50);

            $table->integer('item_recently_shown_count')->default(0);
            $table->integer('item_recently_seen_count')->default(0);
            $table->integer('item_recently_clicked_count')->default(0);
            $table->integer('item_recently_saved_count')->default(0);

            $table->integer('same_anchor_recent_count')->default(0);
            $table->integer('same_district_recent_count')->default(0);
            $table->integer('same_cluster_recent_count')->default(0);
            $table->integer('same_collection_recent_count')->default(0);
            $table->integer('same_moment_family_recent_count')->default(0);
            $table->integer('same_role_recent_count')->default(0);
            $table->integer('same_category_recent_count')->default(0);
            $table->integer('same_vibe_recent_count')->default(0);

            $table->integer('last_shown_minutes_ago')->nullable();
            $table->integer('last_anchor_shown_minutes_ago')->nullable();
            $table->integer('last_district_shown_minutes_ago')->nullable();
            $table->integer('last_collection_shown_minutes_ago')->nullable();

            $table->decimal('novelty_score', 8, 4)->default(0);
            $table->decimal('repetition_risk_score', 8, 4)->default(0);
            $table->decimal('cooldown_penalty_score', 8, 4)->default(0);
            $table->decimal('fatigue_compatibility_score', 8, 4)->default(0);

            $table->primary(['session_id', 'item_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_candidate_memory_features');
    }
};
