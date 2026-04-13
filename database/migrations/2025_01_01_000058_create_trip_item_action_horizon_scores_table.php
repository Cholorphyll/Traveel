<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_item_action_horizon_scores', function (Blueprint $table) {
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('candidate_item_id')->unsigned();

            $table->decimal('do_now_score', 8, 6)->nullable();
            $table->decimal('do_soon_score', 8, 6)->nullable();
            $table->decimal('do_later_score', 8, 6)->nullable();
            $table->decimal('next_day_score', 8, 6)->nullable();

            $table->timestamp('computed_at')->useCurrent();

            $table->primary(['session_id', 'trip_id', 'user_id', 'candidate_item_id']);
            $table->index(['session_id', 'trip_id', 'user_id', 'candidate_item_id'], 'idx_session_trip_user_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_item_action_horizon_scores');
    }
};
