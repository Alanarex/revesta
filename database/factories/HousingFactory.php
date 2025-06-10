<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Address;
use App\Models\FiscalIncomeRange;

class HousingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['apartment', 'house', 'studio']),
            'surface' => $this->faker->randomFloat(2, 20, 200),
            'construction_year' => $this->faker->optional()->year,
            'energy_class' => $this->faker->optional()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G']),
            'adresse_id' => Address::factory(),
            'fiscal_income_id' => FiscalIncomeRange::factory(),
        ];
    }
}