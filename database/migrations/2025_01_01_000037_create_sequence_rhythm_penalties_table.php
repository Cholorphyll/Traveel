<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_rhythm_penalties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('penalty_code', 80)->unique();
            $table->string('penalty_description', 255)->nullable();
            $table->decimal('penalty_weight', 8, 4);
            $table->tinyInteger('is_active')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_rhythm_penalties');
    }
};
