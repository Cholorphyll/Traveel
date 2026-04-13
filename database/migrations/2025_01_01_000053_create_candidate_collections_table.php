<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('collection_type', 100);
            $table->bigInteger('area_id')->nullable();
            $table->bigInteger('anchor_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->decimal('centroid_lat', 10, 7)->nullable();
            $table->decimal('centroid_lng', 10, 7)->nullable();
            $table->integer('member_count')->default(0);

            $table->decimal('review_strength', 8, 4)->default(0);
            $table->decimal('need_state_match', 8, 4)->default(0);
            $table->decimal('ease_of_choice', 8, 4)->default(0);
            $table->decimal('area_energy_fit', 8, 4)->default(0);
            $table->decimal('current_comfort_fit', 8, 4)->default(0);
            $table->decimal('route_fit', 8, 4)->default(0);
            $table->decimal('context_fit', 8, 4)->default(0);

            $table->timestamp('created_at')->useCurrent();

            $table->index('collection_type');
            $table->index('area_id');
            $table->index('anchor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_collections');
    }
};
