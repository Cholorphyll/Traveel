<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_slot_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slot_type', 80)->unique();
            $table->string('slot_family', 60);
            $table->string('slot_goal', 80);

            $table->tinyInteger('requires_anchor')->default(0);
            $table->tinyInteger('requires_food_candidates')->default(0);
            $table->tinyInteger('requires_weather_fit')->default(0);
            $table->tinyInteger('requires_opportunity_fit')->default(0);
            $table->tinyInteger('requires_do_now_bias')->default(0);

            $table->string('default_horizon_bias', 40)->nullable();
            $table->string('default_intensity', 40)->nullable();
            $table->string('default_duration_class', 40)->nullable();
            $table->string('default_environment_bias', 40)->nullable();

            $table->integer('suppress_if_recent_count')->default(0);
            $table->integer('max_slots_per_10')->default(99);

            $table->decimal('priority_base_score', 8, 4)->default(0);
            $table->tinyInteger('is_active')->default(1);

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_slot_templates');
    }
};
