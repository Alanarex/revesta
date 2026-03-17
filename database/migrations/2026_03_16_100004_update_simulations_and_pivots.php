<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            $table->foreignId('ad_id')->nullable()->after('user_id')
                ->constrained('ads')->nullOnDelete();

            $table->decimal('gain_energetique', 10, 2)->nullable()->after('date');
            $table->string('aid_path_id', 100)->nullable()->after('gain_energetique');
            $table->boolean('condition_depenses')->nullable()->after('aid_path_id');
            $table->decimal('montant_total_aides', 15, 2)->nullable()->after('condition_depenses');
            $table->decimal('pourcentage_bien', 8, 2)->nullable()->after('montant_total_aides');
            $table->json('aides_details')->nullable()->after('pourcentage_bien');
        });

        if (! Schema::hasTable('renovation_works')) {
            Schema::create('renovation_works', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('label');
                $table->timestamps();
            });
        }

        Schema::create('renovation_work_simulation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained('simulations')->cascadeOnDelete();
            $table->foreignId('renovation_work_id')->constrained('renovation_works')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['simulation_id', 'renovation_work_id'], 'rws_sim_work_unique');
        });

        Schema::table('aid_simulation', function (Blueprint $table) {
            if (! Schema::hasColumn('aid_simulation', 'raw_name')) {
                $table->string('raw_name')->nullable()->after('aid_id');
            }

            if (! Schema::hasColumn('aid_simulation', 'details')) {
                $table->json('details')->nullable()->after('amount');
            }

            $table->index(['simulation_id', 'aid_id']);
        });
    }

    public function down(): void
    {
        Schema::table('aid_simulation', function (Blueprint $table) {
            $table->dropIndex(['simulation_id', 'aid_id']);

            $columns = [];

            if (Schema::hasColumn('aid_simulation', 'raw_name')) {
                $columns[] = 'raw_name';
            }

            if (Schema::hasColumn('aid_simulation', 'details')) {
                $columns[] = 'details';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('renovation_work_simulation');
        Schema::dropIfExists('renovation_works');

        Schema::table('simulations', function (Blueprint $table) {
            if (Schema::hasColumn('simulations', 'ad_id')) {
                $table->dropConstrainedForeignId('ad_id');
            }

            $table->dropColumn([
                'gain_energetique',
                'aid_path_id',
                'condition_depenses',
                'montant_total_aides',
                'pourcentage_bien',
                'aides_details',
            ]);
        });
    }
};
