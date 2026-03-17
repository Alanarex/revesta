<?php

namespace Database\Factories;

use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterCampaignLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id'   => NewsletterCampaign::factory(),
            'subscriber_id' => Newsletter::factory(),
            'sent_at'       => $this->faker->dateTimeBetween('-6 months', 'now'),
            'opened'        => $this->faker->boolean(40),
            'clicked'       => $this->faker->boolean(20),
        ];
    }
}
