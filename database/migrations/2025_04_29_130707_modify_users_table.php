<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete()->after('remember_token');
            $table->string('civil_status')->nullable()->after('address_id');
            $table->string('family_status')->nullable()->after('civil_status');
            $table->boolean('cookies_accepted')->default(false)->after('family_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'address_id',
                'civil_status',
                'family_status',
                'cookies_accepted',
            ]);
        });
    }
};
