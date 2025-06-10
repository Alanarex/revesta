<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Aid;
use App\Models\FiscalIncomeRange;

class ConditionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'aid_id' => Aid::factory(),
            'model' => $this->faker->word,
            'attribute' => $this->faker->word,
            'condition_type' => $this->faker->randomElement(['string', 'numeric', 'range']),
            'operator' => $this->faker->randomElement(['=', '<', '>', 'in']),
            'value' => $this->faker->optional()->word,
            'fiscal_income_range_id' => $this->faker->optional()->randomElement([null, FiscalIncomeRange::factory()]),
        ];
    }
}