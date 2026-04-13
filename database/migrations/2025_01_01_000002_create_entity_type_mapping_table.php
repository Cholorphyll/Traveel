<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_type_mapping', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_category', 100)->unique();
            $table->boolean('anchor_attraction')->default(0);
            $table->boolean('context_attraction')->default(0);
            $table->boolean('experience')->default(0);
            $table->boolean('food')->default(0);
            $table->boolean('enabler')->default(0);
            $table->boolean('event')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_type_mapping');
    }
};
