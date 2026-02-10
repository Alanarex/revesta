<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Newsletter>
 */
class NewsletterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'ip_address' => $this->faker->ipv4(),
            'subscribed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'verified_at' => $this->faker->randomElement([
                $this->faker->dateTimeBetween('-6 months', 'now'),
                null,
            ]),
        ];
    }

    /**
     * Indicate that the subscriber is verified.
     */
    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'verified_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            ];
        });
    }

    /**
     * Indicate that the subscriber is unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'verified_at' => null,
            ];
        });
    }
}
