<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_need_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 100);
            $table->bigInteger('context_location_id')->nullable();
            $table->dateTime('local_datetime');
            $table->decimal('hunger_level', 6, 3)->default(0);
            $table->decimal('coffee_need_level', 6, 3)->default(0);
            $table->decimal('hydration_need_level', 6, 3)->default(0);
            $table->decimal('fatigue_level', 6, 3)->default(0);
            $table->decimal('cooling_need_level', 6, 3)->default(0);
            $table->decimal('sit_down_need_level', 6, 3)->default(0);
            $table->decimal('social_energy_level', 6, 3)->default(0);
            $table->decimal('stroll_readiness_level', 6, 3)->default(0);
            $table->decimal('nightlife_readiness_level', 6, 3)->default(0);
            $table->decimal('short_commitment_preference', 6, 3)->default(0);
            $table->decimal('half_day_block_readiness', 6, 3)->default(0);
            $table->json('active_need_windows')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('session_id');
            $table->index('context_location_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_need_state');
    }
};
