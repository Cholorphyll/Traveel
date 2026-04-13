<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_operating_windows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience', 'hotel', 'area']);
            $table->bigInteger('entity_id')->unsigned();

            $table->tinyInteger('day_of_week');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->tinyInteger('is_closed')->default(0);

            $table->unique(['entity_type', 'entity_id', 'day_of_week'], 'uniq_operating_window');
            $table->index(['entity_type', 'entity_id', 'day_of_week'], 'idx_entity_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_operating_windows');
    }
};
