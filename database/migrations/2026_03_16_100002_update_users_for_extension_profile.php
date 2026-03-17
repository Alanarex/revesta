<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'code_postal')) {
                $table->string('code_postal', 10)->nullable()->after('telephone');
            }

            if (! Schema::hasColumn('users', 'revenus')) {
                $table->decimal('revenus', 15, 2)->nullable()->after('code_postal');
            }

            if (! Schema::hasColumn('users', 'nombre_personnes')) {
                $table->unsignedSmallInteger('nombre_personnes')->nullable()->after('revenus');
            }

            if (! Schema::hasColumn('users', 'residence_principale')) {
                $table->boolean('residence_principale')->nullable()->after('nombre_personnes');
            }

            if (! Schema::hasColumn('users', 'budget_achat')) {
                $table->decimal('budget_achat', 15, 2)->nullable()->after('residence_principale');
            }

            if (! Schema::hasColumn('users', 'surface_logement')) {
                $table->decimal('surface_logement', 10, 2)->nullable()->after('budget_achat');
            }

            if (! Schema::hasColumn('users', 'budget_travaux')) {
                $table->decimal('budget_travaux', 15, 2)->nullable()->after('surface_logement');
            }

            if (! Schema::hasColumn('users', 'taxe_fonciere')) {
                $table->decimal('taxe_fonciere', 15, 2)->nullable()->after('budget_travaux');
            }

            if (! Schema::hasColumn('users', 'condition_depenses')) {
                $table->boolean('condition_depenses')->nullable()->after('taxe_fonciere');
            }

            if (! Schema::hasColumn('users', 'notifications_aides')) {
                $table->boolean('notifications_aides')->default(true)->after('condition_depenses');
            }

            if (! Schema::hasColumn('users', 'notifications_prix')) {
                $table->boolean('notifications_prix')->default(true)->after('notifications_aides');
            }

            if (! Schema::hasColumn('users', 'accept_analytics')) {
                $table->boolean('accept_analytics')->default(true)->after('notifications_prix');
            }

            if (! Schema::hasColumn('users', 'user_status_id')) {
                $table->string('user_status_id', 100)->nullable()->after('accept_analytics');
            }

            if (! Schema::hasColumn('users', 'dpe_actuel_id')) {
                $table->string('dpe_actuel_id', 5)->nullable()->after('user_status_id');
            }

            if (! Schema::hasColumn('users', 'dpe_vise_id')) {
                $table->string('dpe_vise_id', 5)->nullable()->after('dpe_actuel_id');
            }

            if (! Schema::hasColumn('users', 'construction_period_id')) {
                $table->string('construction_period_id', 100)->nullable()->after('dpe_vise_id');
            }

            if (! Schema::hasColumn('users', 'housing_type_id')) {
                $table->string('housing_type_id', 100)->nullable()->after('construction_period_id');
            }

            if (! Schema::hasColumn('users', 'energy_gain_target_id')) {
                $table->unsignedTinyInteger('energy_gain_target_id')->nullable()->after('housing_type_id');
            }

            if (! Schema::hasColumn('users', 'aid_path_id')) {
                $table->string('aid_path_id', 100)->nullable()->after('energy_gain_target_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'telephone',
                'code_postal',
                'revenus',
                'nombre_personnes',
                'residence_principale',
                'budget_achat',
                'surface_logement',
                'budget_travaux',
                'taxe_fonciere',
                'condition_depenses',
                'notifications_aides',
                'notifications_prix',
                'accept_analytics',
                'user_status_id',
                'dpe_actuel_id',
                'dpe_vise_id',
                'construction_period_id',
                'housing_type_id',
                'energy_gain_target_id',
                'aid_path_id',
            ];

            $existingColumns = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('users', $column)));

            if (! empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
