<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RenovationWorkFactory extends Factory
{
    public function definition(): array
    {
        $options = [
            ['code' => 'isolation_murs',     'label' => 'Isolation des murs'],
            ['code' => 'isolation_toiture',  'label' => 'Isolation de la toiture'],
            ['code' => 'isolation_planchers','label' => 'Isolation des planchers bas'],
            ['code' => 'fenetres',           'label' => 'Remplacement des fenêtres et portes'],
            ['code' => 'chauffage',          'label' => 'Remplacement du système de chauffage'],
            ['code' => 'chauffe_eau',        'label' => 'Chauffe-eau solaire ou thermodynamique'],
            ['code' => 'ventilation',        'label' => 'Ventilation (VMC double flux)'],
            ['code' => 'audit',              'label' => 'Audit énergétique préalable'],
        ];

        return $this->faker->randomElement($options);
    }
}
