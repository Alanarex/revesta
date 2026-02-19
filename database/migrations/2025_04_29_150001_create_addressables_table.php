<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addressables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_id')->constrained()->cascadeOnDelete();
            $table->morphs('addressable');
            $table->unsignedTinyInteger('type')->default(0); // int constant
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addressables');
    }
};
