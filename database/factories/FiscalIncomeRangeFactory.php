<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FiscalIncomeRangeFactory extends Factory
{
    public function definition(): array
    {
        $min = $this->faker->numberBetween(10000, 30000);
        $max = $min + $this->faker->numberBetween(5000, 20000);

        return [
            'name' => $this->faker->word,
            'min' => $min,
            'max' => $max,
            'year' => $this->faker->year,
            'description' => $this->faker->optional()->sentence,
        ];
    }
}