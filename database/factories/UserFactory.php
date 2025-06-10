<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Address;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address_id' => Address::factory(),
            'civil_status' => $this->faker->randomElement(['single', 'married', 'divorced']),
            'family_status' => $this->faker->randomElement(['with_children', 'without_children']),
            'cookies_accepted' => $this->faker->boolean,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => null,
        ];
    }
}
