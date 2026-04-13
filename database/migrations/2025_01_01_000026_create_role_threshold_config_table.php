<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_threshold_config', function (Blueprint $table) {
            $table->string('role_name', 80)->primary();
            $table->string('role_group', 40)->nullable();
            $table->decimal('min_role_score', 6, 4)->nullable();
            $table->decimal('role_priority_weight', 6, 4)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_threshold_config');
    }
};
