<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moment_type_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('moment_family', 50);
            $table->string('moment_type', 100);
            $table->tinyInteger('is_active')->default(1);

            $table->decimal('min_proximity_score', 8, 4)->nullable();
            $table->decimal('max_proximity_score', 8, 4)->nullable();

            $table->decimal('min_actionability_score', 8, 4)->nullable();
            $table->decimal('min_need_fit_score', 8, 4)->nullable();
            $table->decimal('min_social_intensity', 8, 4)->nullable();
            $table->decimal('min_opportunity_score', 8, 4)->nullable();
            $table->decimal('min_energy_fit_score', 8, 4)->nullable();
            $table->decimal('min_hidden_gem_score', 8, 4)->nullable();

            $table->tinyInteger('requires_open_now')->nullable();
            $table->tinyInteger('requires_indoor')->nullable();
            $table->tinyInteger('requires_outdoor')->nullable();
            $table->tinyInteger('requires_sunset_window')->nullable();
            $table->tinyInteger('requires_closing_window')->nullable();
            $table->tinyInteger('requires_food_category')->nullable();
            $table->tinyInteger('requires_low_commitment')->nullable();

            $table->json('preferred_dayparts')->nullable();
            $table->json('preferred_categories')->nullable();

            $table->decimal('priority_weight', 8, 4)->default(1);

            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('moment_type', 'uq_moment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moment_type_rules');
    }
};
