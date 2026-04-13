<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('context_zones', function (Blueprint $table) {
            $table->bigIncrements('context_zone_id');
            $table->bigInteger('entity_location_id');
            $table->string('zone_name', 255)->nullable();
            $table->string('primary_context', 100)->nullable();
            $table->string('active_context_zone', 100)->nullable();
            $table->json('secondary_contexts')->nullable();
            $table->decimal('center_lat', 10, 8)->nullable();
            $table->decimal('center_lng', 11, 8)->nullable();
            $table->integer('radius_meters')->nullable();
            $table->decimal('context_confidence', 5, 4)->nullable();
            $table->json('context_signature')->nullable();
            $table->string('source_url', 500)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->index('entity_location_id');
            $table->index('primary_context');
            $table->index('active_context_zone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('context_zones');
    }
};
