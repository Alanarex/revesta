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
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aid_id')->constrained();
            $table->enum('model', ['user', 'housing']);
            $table->string('attribute');
            $table->enum('condition_type', ['operator', 'range']);
            $table->string('operator')->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->foreignId('fiscal_income_range_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
