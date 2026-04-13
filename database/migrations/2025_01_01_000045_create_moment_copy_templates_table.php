<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moment_copy_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('moment_type', 100);
            $table->string('variant_code', 100);
            $table->string('short_label', 120);
            $table->string('medium_label', 255);
            $table->string('support_line', 255)->nullable();
            $table->string('cta_style', 50)->nullable();
            $table->string('tone', 50)->nullable();
            $table->tinyInteger('is_active')->default(1);

            $table->unique('variant_code', 'uq_variant_code');
            $table->index('moment_type', 'idx_moment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moment_copy_templates');
    }
};
