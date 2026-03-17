<?php

namespace Database\Factories;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ad_id' => Ad::factory(),
            'url'   => $this->faker->imageUrl(800, 600, 'house'),
            'order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
