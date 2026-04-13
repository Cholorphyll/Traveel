<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_opportunity_state', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 100);
            $table->bigInteger('context_location_id')->nullable();
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id')->unsigned();
            $table->tinyInteger('opportunity_active')->default(0);
            $table->decimal('opportunity_score', 8, 4)->default(0);
            $table->string('opportunity_type_primary', 100)->nullable();
            $table->json('opportunity_type_secondary')->nullable();
            $table->json('need_state_windows_active')->nullable();
            $table->json('active_opportunity_windows')->nullable();
            $table->decimal('temporal_opportunity_score', 8, 4)->default(0);
            $table->decimal('condition_opportunity_score', 8, 4)->default(0);
            $table->decimal('operational_opportunity_score', 8, 4)->default(0);
            $table->decimal('practicality_opportunity_score', 8, 4)->default(0);
            $table->decimal('need_state_opportunity_score', 8, 4)->default(0);
            $table->decimal('opportunity_confidence', 8, 4)->default(0);
            $table->text('opportunity_explanation')->nullable();
            $table->timestamp('computed_at')->useCurrent();

            $table->unique(['session_id', 'entity_type', 'entity_id'], 'uniq_session_entity');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index('session_id');
            $table->index('context_location_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_opportunity_state');
    }
};
