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
        Schema::create('fiscal_income_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min', 10, 2)->nullable();
            $table->decimal('max', 10, 2)->nullable();
            $table->year('year');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_income_ranges');
    }
};
