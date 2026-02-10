<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterCampaign>
 */
class NewsletterCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['draft', 'sent', 'scheduled']);
        $sentAt = $status === 'sent' ? $this->faker->dateTimeBetween('-3 months', 'now') : null;
        $scheduledAt = $status === 'scheduled' ? $this->faker->dateTimeBetween('now', '+1 month') : null;
        $sentCount = $status === 'sent' ? $this->faker->numberBetween(10, 500) : 0;

        return [
            'title' => $this->faker->sentence(),
            'content' => json_encode([
                'ops' => [
                    ['insert' => $this->faker->paragraphs(3, true)],
                ]
            ]),
            'status' => $status,
            'scheduled_at' => $scheduledAt,
            'sent_at' => $sentAt,
            'sent_count' => $sentCount,
        ];
    }

    /**
     * Indicate that the campaign is a draft.
     */
    public function draft(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'sent_at' => null,
                'scheduled_at' => null,
                'sent_count' => 0,
            ];
        });
    }

    /**
     * Indicate that the campaign has been sent.
     */
    public function sent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'sent',
                'sent_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
                'scheduled_at' => null,
                'sent_count' => $this->faker->numberBetween(50, 500),
            ];
        });
    }

    /**
     * Indicate that the campaign is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'scheduled',
                'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
                'sent_at' => null,
                'sent_count' => 0,
            ];
        });
    }
}
