<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_plan_output', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 120);
            $table->dateTime('planning_ts');
            $table->integer('slot_position');

            $table->string('slot_type', 80);
            $table->string('slot_family', 60)->nullable();
            $table->string('slot_goal', 80)->nullable();

            $table->string('anchor_relation', 60)->nullable();
            $table->string('horizon_bias', 40)->nullable();
            $table->string('sequence_mode', 60)->nullable();

            $table->decimal('planned_score', 10, 4)->default(0);
            $table->string('planning_reason', 500)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->unique(['session_id', 'planning_ts', 'slot_position'], 'uniq_session_position');
            $table->index(['session_id', 'planning_ts'], 'idx_session');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_plan_output');
    }
};
