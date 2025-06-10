<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('housings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->float('surface');
            $table->year('construction_year')->nullable();
            $table->string('energy_class')->nullable();

            $table->foreignId('adresse_id')->constrained('addresses')->cascadeOnDelete();
            $table->foreignId('fiscal_income_id')->nullable()->constrained('fiscal_income_ranges')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housings');
    }
};
