<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('context_taxonomy', function (Blueprint $table) {
            $table->bigIncrements('context_taxonomy_id');
            $table->string('context_key', 100)->unique('uniq_context_key');
            $table->string('context_label', 150)->nullable();
            $table->enum('context_type', ['primary', 'secondary', 'route', 'role']);
            $table->string('parent_context_key', 100)->nullable();
            $table->text('description')->nullable();
            $table->json('allowed_item_types')->nullable();
            $table->json('allowed_categories')->nullable();
            $table->integer('default_walk_radius_meters')->nullable();
            $table->integer('default_drive_radius_meters')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->index('context_type');
            $table->index('parent_context_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('context_taxonomy');
    }
};
