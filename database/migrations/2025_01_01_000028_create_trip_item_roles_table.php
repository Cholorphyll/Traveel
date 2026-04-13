<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_item_roles', function (Blueprint $table) {
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('candidate_item_id')->unsigned();
            $table->string('candidate_item_type', 50)->nullable();

            $table->json('assigned_roles');
            $table->string('primary_role', 80);
            $table->json('secondary_roles')->nullable();
            $table->string('role_group', 40);

            $table->decimal('role_confidence', 8, 6);
            $table->decimal('primary_role_score', 8, 6);
            $table->decimal('second_best_role_score', 8, 6)->nullable();
            $table->decimal('role_priority', 8, 6);
            $table->decimal('role_selection_score', 8, 6);

            $table->json('role_reason_codes')->nullable();

            $table->timestamp('evaluated_at')->useCurrent();

            $table->primary(['session_id', 'trip_id', 'user_id', 'candidate_item_id']);
            $table->index('primary_role', 'idx_primary_role');
            $table->index('role_group', 'idx_role_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_item_roles');
    }
};
