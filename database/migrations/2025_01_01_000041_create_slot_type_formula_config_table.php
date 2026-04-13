<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_type_formula_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slot_type', 100);
            $table->string('factor_name', 100);
            $table->decimal('factor_weight', 8, 4);
            $table->tinyInteger('active')->default(1);
            $table->string('version_tag', 50)->default('v1');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['slot_type', 'factor_name', 'version_tag'], 'uniq_slot_factor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_type_formula_config');
    }
};
