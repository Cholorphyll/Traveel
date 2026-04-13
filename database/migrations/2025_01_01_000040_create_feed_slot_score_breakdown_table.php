<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_slot_score_breakdown', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id', 100);
            $table->bigInteger('feed_moment_id');
            $table->string('slot_type', 100);

            $table->string('candidate_type', 50);
            $table->bigInteger('candidate_id')->nullable();
            $table->bigInteger('collection_id')->nullable();

            $table->string('factor_name', 100);
            $table->decimal('factor_value', 8, 4)->default(0);
            $table->decimal('factor_weight', 8, 4)->default(0);
            $table->decimal('weighted_contribution', 8, 4)->default(0);

            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_id', 'feed_moment_id', 'slot_type', 'candidate_type', 'candidate_id'], 'idx_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_slot_score_breakdown');
    }
};
