<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_type_mapping', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category', 100)->unique();
            $table->json('activity_type_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_type_mapping');
    }
};
