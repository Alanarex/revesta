<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aid_id')->constrained('aids')->cascadeOnDelete();
            $table->string('model');
            $table->string('attribute');
            // $table->string('condition_type'); // e.g. "string", "numeric", "range"
            $table->string('operator'); // e.g. "=", "<", "in", etc.
            $table->string('value')->nullable(); // raw or serialized value

            $table->foreignId('fiscal_income_range_id')->nullable()->constrained('fiscal_income_ranges')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};