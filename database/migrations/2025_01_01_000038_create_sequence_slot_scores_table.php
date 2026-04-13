<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_slot_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 120);
            $table->dateTime('planning_ts');
            $table->integer('slot_position');
            $table->string('slot_type', 80);

            $table->decimal('base_score', 10, 4)->default(0);
            $table->decimal('anchor_progress_score', 10, 4)->default(0);
            $table->decimal('rhythm_score', 10, 4)->default(0);
            $table->decimal('opportunity_score', 10, 4)->default(0);
            $table->decimal('weather_fit_score', 10, 4)->default(0);
            $table->decimal('action_horizon_score', 10, 4)->default(0);
            $table->decimal('need_state_score', 10, 4)->default(0);
            $table->decimal('novelty_score', 10, 4)->default(0);
            $table->decimal('suppression_penalty', 10, 4)->default(0);
            $table->decimal('final_slot_score', 10, 4)->default(0);

            $table->string('debug_reason', 500)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'slot_position', 'final_slot_score'], 'idx_session_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_slot_scores');
    }
};
