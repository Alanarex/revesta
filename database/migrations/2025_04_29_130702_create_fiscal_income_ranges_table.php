<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fiscal_income_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min', 12, 2)->unsigned()->nullable();
            $table->decimal('max', 12, 2)->unsigned()->nullable();
            $table->year('year')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_income_ranges');
    }
};
