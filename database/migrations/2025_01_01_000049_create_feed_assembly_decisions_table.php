<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_assembly_decisions', function (Blueprint $table) {
            $table->bigIncrements('decision_id');
            $table->bigInteger('session_id');
            $table->bigInteger('item_id');
            $table->string('item_type', 50);

            $table->string('slot_type', 100);
            $table->integer('candidate_rank')->nullable();

            $table->decimal('base_slot_score', 10, 4)->default(0);
            $table->decimal('novelty_score', 10, 4)->default(0);
            $table->decimal('repetition_risk_score', 10, 4)->default(0);
            $table->decimal('cooldown_penalty_score', 10, 4)->default(0);
            $table->decimal('fatigue_compatibility_score', 10, 4)->default(0);

            $table->decimal('diversity_bonus_score', 10, 4)->default(0);
            $table->decimal('session_fit_score', 10, 4)->default(0);
            $table->decimal('collection_suitability_score', 10, 4)->default(0);

            $table->decimal('final_assembly_score', 10, 4)->default(0);

            $table->tinyInteger('is_suppressed')->default(0);
            $table->string('suppression_reason', 255)->nullable();
            $table->tinyInteger('is_selected')->default(0);

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index(['session_id', 'slot_type'], 'idx_session_slot');
            $table->index(['session_id', 'is_selected'], 'idx_session_selected');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_assembly_decisions');
    }
};
