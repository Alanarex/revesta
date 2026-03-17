<?php

namespace Database\Seeders;

use App\Models\RenovationWork;
use Illuminate\Database\Seeder;

class RenovationWorkSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            ['code' => 'isolation_murs',      'label' => 'Isolation des murs par l\'extérieur (ITE)'],
            ['code' => 'isolation_toiture',   'label' => 'Isolation de la toiture / combles'],
            ['code' => 'isolation_planchers', 'label' => 'Isolation des planchers bas'],
            ['code' => 'fenetres',            'label' => 'Remplacement des fenêtres et portes'],
            ['code' => 'chauffage',           'label' => 'Remplacement du système de chauffage'],
            ['code' => 'chauffe_eau',         'label' => 'Chauffe-eau solaire ou thermodynamique'],
            ['code' => 'ventilation',         'label' => 'Ventilation (VMC double flux)'],
            ['code' => 'audit',               'label' => 'Audit énergétique préalable'],
        ];

        foreach ($works as $work) {
            RenovationWork::firstOrCreate(['code' => $work['code']], $work);
        }

        $this->command->info('✅ Seeded '.count($works).' renovation work types.');
    }
}
