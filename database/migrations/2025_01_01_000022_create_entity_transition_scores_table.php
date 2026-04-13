<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_transition_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('from_entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('from_entity_id')->unsigned();
            $table->enum('to_entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('to_entity_id')->unsigned();

            $table->decimal('transition_score', 6, 4)->nullable();

            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['from_entity_type', 'from_entity_id', 'to_entity_type', 'to_entity_id'], 'uniq_transition');
            $table->index(['from_entity_type', 'from_entity_id'], 'idx_from_entity');
            $table->index(['to_entity_type', 'to_entity_id'], 'idx_to_entity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_transition_scores');
    }
};
