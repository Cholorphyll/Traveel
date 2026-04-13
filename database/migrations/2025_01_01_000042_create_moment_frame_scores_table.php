<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moment_frame_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('session_id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('trip_id')->nullable();
            $table->integer('slot_index');
            $table->string('item_type', 50);
            $table->bigInteger('item_id');

            $table->string('moment_primary_family', 50)->nullable();
            $table->string('moment_primary_type', 100)->nullable();
            $table->decimal('moment_primary_score', 8, 4)->nullable();

            $table->string('moment_secondary_family', 50)->nullable();
            $table->string('moment_secondary_type', 100)->nullable();
            $table->decimal('moment_secondary_score', 8, 4)->nullable();

            $table->decimal('moment_confidence', 8, 4)->nullable();
            $table->decimal('moment_urgency_level', 8, 4)->nullable();
            $table->decimal('moment_actionability_level', 8, 4)->nullable();
            $table->decimal('moment_social_intensity', 8, 4)->nullable();
            $table->decimal('moment_energy_fit_level', 8, 4)->nullable();
            $table->decimal('moment_need_fit_level', 8, 4)->nullable();

            $table->string('moment_copy_variant_id', 100)->nullable();
            $table->string('moment_label_short', 120)->nullable();
            $table->string('moment_label_medium', 255)->nullable();
            $table->json('moment_reason_json')->nullable();

            $table->string('moment_cta_style', 50)->nullable();
            $table->tinyInteger('moment_suppress_flag')->default(0);
            $table->tinyInteger('moment_fallback_flag')->default(0);

            $table->dateTime('moment_last_computed_at');

            $table->unique(['session_id', 'slot_index', 'item_type', 'item_id'], 'uq_session_slot_item');
            $table->index(['session_id', 'slot_index'], 'idx_session_slot');
            $table->index(['item_type', 'item_id'], 'idx_item');
            $table->index('moment_primary_type', 'idx_primary_type');
            $table->index('moment_primary_family', 'idx_primary_family');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moment_frame_scores');
    }
};
