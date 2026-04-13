<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_item_eligibility', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('item_type', ['sight', 'restaurant', 'experience', 'event']);
            $table->bigInteger('item_id');
            $table->tinyInteger('eligible')->default(1);
            $table->enum('eligibility_status', ['pass', 'fail'])->default('pass');
            $table->string('hard_fail_category', 100)->nullable();
            $table->json('reason_codes_json')->nullable();
            $table->dateTime('last_evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['item_type', 'item_id'], 'uniq_item');
            $table->index('eligible');
            $table->index('eligibility_status');
            $table->index('last_evaluated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_item_eligibility');
    }
};
