<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_operational_windows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id')->unsigned();
            $table->string('window_type', 100);
            $table->dateTime('window_start')->nullable();
            $table->dateTime('window_end')->nullable();
            $table->enum('urgency_curve', ['linear', 'fast_rise', 'hard_cutoff'])->default('linear');
            $table->decimal('importance_multiplier', 6, 3)->default(1.000);
            $table->tinyInteger('active_flag')->default(1);
            $table->text('source_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index(['window_type', 'window_start', 'window_end'], 'idx_window');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_operational_windows');
    }
};
