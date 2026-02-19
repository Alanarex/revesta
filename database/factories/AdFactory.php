<?php

namespace Database\Factories;

use App\Models\Housing;
use Illuminate\Database\Eloquent\Factories\Factory;

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
