<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'housing_id')) {
                $table->dropForeign(['housing_id']);
                $table->unsignedBigInteger('housing_id')->nullable()->change();
                $table->foreign('housing_id')->references('id')->on('housings')->nullOnDelete();
            }

            $table->string('site')->nullable()->after('url');
            $table->string('titre')->nullable()->after('site');
            $table->decimal('prix', 15, 2)->nullable()->after('titre');
            $table->string('localisation')->nullable()->after('prix');
            $table->string('ville')->nullable()->after('localisation');
            $table->string('code_postal', 20)->nullable()->after('ville');
            $table->decimal('surface', 10, 2)->nullable()->after('code_postal');
            $table->decimal('pieces', 8, 2)->nullable()->after('surface');
            $table->text('description')->nullable()->after('pieces');
            $table->string('housing_type_id', 100)->nullable()->after('description');
            $table->string('dpe_class_id', 5)->nullable()->after('housing_type_id');
            $table->string('etage')->nullable()->after('dpe_class_id');
            $table->string('type_travaux')->nullable()->after('etage');
            $table->timestamp('date_extraction')->nullable()->after('type_travaux');

            $table->index(['site', 'ville']);
        });

        Schema::create('ad_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->text('url');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['ad_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_images');

        Schema::table('ads', function (Blueprint $table) {
            $table->dropIndex(['site', 'ville']);

            $table->dropColumn([
                'site',
                'titre',
                'prix',
                'localisation',
                'ville',
                'code_postal',
                'surface',
                'pieces',
                'description',
                'housing_type_id',
                'dpe_class_id',
                'etage',
                'type_travaux',
                'date_extraction',
            ]);

            if (Schema::hasColumn('ads', 'housing_id')) {
                $table->dropForeign(['housing_id']);
                $table->unsignedBigInteger('housing_id')->nullable(false)->change();
                $table->foreign('housing_id')->references('id')->on('housings')->cascadeOnDelete();
            }
        });
    }
};
