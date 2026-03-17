<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        $types = ['blog_approved', 'blog_rejected', 'new_aid', 'simulation_ready', 'system'];

        return [
            'user_id'         => User::factory(),
            'type'            => $this->faker->randomElement($types),
            'message'         => $this->faker->sentence(),
            'notifiable_id'   => null,
            'notifiable_type' => null,
            'reason'          => $this->faker->optional(0.5)->sentence(),
            'read'            => $this->faker->boolean(30),
        ];
    }

    public function unread(): static
    {
        return $this->state(['read' => false]);
    }

    public function read(): static
    {
        return $this->state(['read' => true]);
    }
}
