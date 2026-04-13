<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_weather_affinity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('entity_id')->unsigned();

            $table->decimal('clear_score', 6, 4)->nullable();
            $table->decimal('cloudy_score', 6, 4)->nullable();
            $table->decimal('rainy_score', 6, 4)->nullable();
            $table->decimal('windy_score', 6, 4)->nullable();
            $table->decimal('hot_score', 6, 4)->nullable();
            $table->decimal('cold_score', 6, 4)->nullable();

            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['entity_type', 'entity_id'], 'uniq_entity_weather_affinity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_weather_affinity');
    }
};
