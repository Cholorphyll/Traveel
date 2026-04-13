<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_session_state', function (Blueprint $table) {
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->time('local_time')->nullable();
            $table->integer('trip_day_index')->nullable();
            $table->integer('minutes_since_last_major_stop')->nullable();
            $table->integer('minutes_since_last_meal')->nullable();

            $table->string('user_exploration_mode', 50)->nullable();
            $table->string('user_hunger_state', 50)->nullable();
            $table->string('user_fatigue_state', 50)->nullable();
            $table->string('session_energy_state', 50)->nullable();

            $table->decimal('evening_fit_score', 8, 6)->nullable();
            $table->decimal('lunch_window_score', 8, 6)->nullable();
            $table->decimal('hunger_need_score', 8, 6)->nullable();
            $table->decimal('fatigue_need_score', 8, 6)->nullable();

            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->primary(['session_id', 'trip_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_session_state');
    }
};
