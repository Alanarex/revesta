<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name')->after('first_name');

            $table->string('phone')->nullable()->after('password');
            $table->foreignId('address_id')->nullable()->constrained()->after('phone');
            $table->tinyInteger('civil_status')->after('address_id');
            $table->tinyInteger('family_status')->after('civil_status');
            $table->boolean('cookies_accepted')->default(false)->after('family_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'phone',
                'address_id',
                'civil_status',
                'family_status',
                'cookies_accepted'
            ]);
            $table->renameColumn('first_name', 'name');
        });
    }
};
