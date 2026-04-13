<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_horizon_metadata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('entity_id')->unsigned();

            $table->tinyInteger('effort_level')->nullable();
            $table->tinyInteger('reservation_need_level')->nullable();
            $table->tinyInteger('schedule_dependency_level')->nullable();
            $table->tinyInteger('transit_dependency_level')->nullable();
            $table->tinyInteger('scarcity_level')->nullable();
            $table->tinyInteger('substitution_density_level')->nullable();
            $table->tinyInteger('landmark_level')->nullable();
            $table->tinyInteger('planning_relevance_level')->nullable();
            $table->tinyInteger('time_window_strength_level')->nullable();

            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['entity_type', 'entity_id'], 'uniq_horizon_meta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_horizon_metadata');
    }
};
