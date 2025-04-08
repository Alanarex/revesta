<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('housings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->float('surface')->nullable();
            $table->year('construction_year')->nullable();
            $table->tinyInteger('energy_class');
            $table->foreignId('address_id')->nullable()->constrained();
            $table->foreignId('fiscal_income_range_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housings');
    }
};
