<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_time_affinity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('entity_id')->unsigned();

            $table->decimal('sunrise_score', 6, 4)->nullable();
            $table->decimal('morning_score', 6, 4)->nullable();
            $table->decimal('midday_score', 6, 4)->nullable();
            $table->decimal('afternoon_score', 6, 4)->nullable();
            $table->decimal('sunset_score', 6, 4)->nullable();
            $table->decimal('evening_score', 6, 4)->nullable();
            $table->decimal('night_score', 6, 4)->nullable();

            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['entity_type', 'entity_id'], 'uniq_entity_time_affinity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_time_affinity');
    }
};
