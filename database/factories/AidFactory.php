<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AidFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'provider' => $this->faker->company,
            'description' => $this->faker->optional()->sentence,
            'type' => $this->faker->randomElement(['grant', 'loan', 'subsidy']),
        ];
    }
}