<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_structural_priors', function (Blueprint $table) {
            $table->string('category', 150)->primary();
            $table->decimal('category_anchor_prior', 8, 4)->nullable();
            $table->decimal('category_uniqueness_prior', 8, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_structural_priors');
    }
};
