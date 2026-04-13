<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_rendered_output', function (Blueprint $table) {
            $table->bigIncrements('rendered_id');
            $table->bigInteger('session_id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('trip_id')->nullable();

            $table->integer('feed_position');

            $table->bigInteger('item_id');
            $table->string('item_type', 50);

            $table->string('slot_type', 100);
            $table->string('assigned_role', 100)->nullable();
            $table->string('moment_family', 100)->nullable();

            $table->bigInteger('anchor_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('cluster_id')->nullable();
            $table->bigInteger('collection_id')->nullable();

            $table->decimal('final_assembly_score', 10, 4)->default(0);
            $table->tinyInteger('was_rendered')->default(0);
            $table->tinyInteger('was_seen')->default(0);
            $table->tinyInteger('was_clicked')->default(0);
            $table->tinyInteger('was_saved')->default(0);

            $table->dateTime('rendered_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index(['session_id', 'feed_position'], 'idx_session_position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_rendered_output');
    }
};
