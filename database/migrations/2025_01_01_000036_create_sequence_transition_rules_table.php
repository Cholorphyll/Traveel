<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_transition_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prev_slot_type', 80);
            $table->string('next_slot_type', 80);

            $table->decimal('transition_weight', 8, 4)->default(0);
            $table->string('transition_reason', 255)->nullable();

            $table->string('condition_anchor_stage', 60)->nullable();
            $table->string('condition_weather_mode', 60)->nullable();
            $table->string('condition_meal_window', 60)->nullable();
            $table->string('condition_sequence_mode', 60)->nullable();

            $table->tinyInteger('is_hard_block')->default(0);
            $table->tinyInteger('is_active')->default(1);

            $table->index(['prev_slot_type', 'next_slot_type'], 'idx_prev_next');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_transition_rules');
    }
};
