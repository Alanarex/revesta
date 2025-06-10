<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Housing;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => $this->faker->url,
            'housing_id' => Housing::inRandomOrder()->first()?->id ?? Housing::factory(),
        ];
    }
}
