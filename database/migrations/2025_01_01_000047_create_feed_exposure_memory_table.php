<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_exposure_memory', function (Blueprint $table) {
            $table->bigIncrements('exposure_id');

            // Identity
            $table->string('candidate_item_id', 80)->comment('"{type}_{entity_id}" composite key');
            $table->string('session_id', 80)->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('trip_id')->nullable();

            // Entity reference
            $table->bigInteger('entity_id')->nullable();
            $table->string('entity_type', 50)->nullable();
            $table->string('candidate_item_type', 50)->nullable();

            // Slot / role context at time of exposure
            $table->string('slot_type', 100)->nullable();
            $table->string('assigned_role', 100)->nullable();
            $table->string('moment_family', 100)->nullable();

            // Position
            $table->integer('feed_position')->nullable();
            $table->integer('shown_rank_position')->nullable();

            // Interaction flags
            $table->tinyInteger('was_rendered')->default(0);
            $table->tinyInteger('was_seen')->default(0);
            $table->tinyInteger('was_clicked')->default(0);
            $table->tinyInteger('was_saved')->default(0);
            $table->tinyInteger('was_dismissed')->default(0);
            $table->tinyInteger('was_skipped_fast')->default(0);

            // Timestamps
            $table->integer('exposure_count')->default(1);
            $table->dateTime('last_shown_at')->nullable();
            $table->dateTime('shown_at')->nullable();
            $table->dateTime('seen_at')->nullable();
            $table->dateTime('clicked_at')->nullable();
            $table->dateTime('saved_at')->nullable();
            $table->dateTime('dismissed_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index(['user_id', 'candidate_item_id'], 'idx_user_candidate');
            $table->index(['user_id', 'last_shown_at'], 'idx_user_last_shown');
            $table->index(['session_id', 'entity_id', 'entity_type'], 'idx_session_entity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_exposure_memory');
    }
};
