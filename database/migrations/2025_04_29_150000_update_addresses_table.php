<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Add new columns
            $table->string('label')->after('id'); // auto-filled

            // Modify existing columns
            $table->string('postal_code', 10)->change();
            $table->string('departement', 50)->nullable()->change();

            // Add new columns after city
            $table->string('insee_code', 10)->nullable()->after('departement');
            $table->decimal('lat', 10, 7)->nullable()->after('insee_code');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');

            // Add timestamps
            $table->timestamps();

            // Add unique constraint
            $table->unique(['street', 'number', 'postal_code', 'city']);
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropUnique(['street', 'number', 'postal_code', 'city']);
            $table->dropColumn(['label', 'insee_code', 'lat', 'lng', 'created_at', 'updated_at']);
        });
    }
};
