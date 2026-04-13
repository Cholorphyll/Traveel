<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_context_affinity', function (Blueprint $table) {
            $table->bigIncrements('entity_context_affinity_id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id');
            $table->bigInteger('entity_location_id');
            $table->string('primary_context', 100)->nullable();
            $table->string('active_context_zone', 100)->nullable();
            $table->decimal('context_affinity_score', 6, 2)->nullable();
            $table->string('entity_context_role', 50)->nullable();
            $table->string('context_fit_reason', 255)->nullable();
            $table->decimal('distance_score', 6, 2)->nullable();
            $table->decimal('line_of_sight_score', 6, 2)->nullable();
            $table->decimal('review_signal_score', 6, 2)->nullable();
            $table->decimal('category_match_score', 6, 2)->nullable();
            $table->decimal('semantic_match_score', 6, 2)->nullable();
            $table->decimal('time_fit_score', 6, 2)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->index(['entity_type', 'entity_id'], 'idx_entity_lookup');
            $table->index('entity_location_id');
            $table->index(['primary_context', 'active_context_zone'], 'idx_context');
            $table->index('context_affinity_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_context_affinity');
    }
};
