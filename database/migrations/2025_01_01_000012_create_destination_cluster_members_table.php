<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_cluster_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cluster_id')->unsigned();
            $table->enum('entity_type', ['sight', 'restaurant', 'experience']);
            $table->bigInteger('entity_id')->unsigned();
            $table->decimal('membership_strength', 8, 4)->nullable();
            $table->decimal('centrality_score', 8, 4)->nullable();
            $table->string('role_in_cluster', 50)->nullable();
            $table->dateTime('computed_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();

            $table->unique(['cluster_id', 'entity_type', 'entity_id'], 'uq_cluster_entity');
            $table->index('cluster_id');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index('role_in_cluster');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_cluster_members');
    }
};
