<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_item_role_scores', function (Blueprint $table) {
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('candidate_item_id')->unsigned();

            $table->string('role_name', 80);
            $table->string('role_group', 40);

            $table->decimal('role_score', 8, 6);
            $table->tinyInteger('eligible_flag');
            $table->decimal('role_priority_weight', 8, 6);
            $table->decimal('role_selection_score', 8, 6);

            $table->json('reason_codes_json')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'trip_id', 'user_id'], 'idx_role_session');
            $table->index('candidate_item_id', 'idx_role_item');
            $table->index('role_name', 'idx_role_name');
            $table->index('eligible_flag', 'idx_role_eligible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_item_role_scores');
    }
};
