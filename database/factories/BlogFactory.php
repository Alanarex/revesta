<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['draft', 'pending', 'published', 'rejected']);

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'title' => $this->faker->sentence(rand(4, 10)),
            'short_description' => $this->faker->text(rand(100, 200)),
            'content' => $this->generateRandomContent(),
            'status' => $status,
            'published_at' => $status === 'published' ? $this->faker->dateTimeBetween('-60 days', 'now') : null,
            'rejection_reason' => $status === 'rejected' ? $this->faker->sentence(rand(8, 15)) : null,
        ];
    }

    private function generateRandomContent(): string
    {
        $content = '<h2>'.$this->faker->sentence(rand(3, 6)).'</h2>';
        $content .= '<p>'.$this->faker->paragraphs(rand(2, 4), true).'</p>';

        $sections = rand(2, 5);
        for ($i = 0; $i < $sections; $i++) {
            $content .= '<h3>'.$this->faker->sentence(rand(2, 5)).'</h3>';

            $contentType = rand(1, 4);
            if ($contentType === 1) {
                $content .= '<p>'.$this->faker->paragraphs(rand(1, 3), true).'</p>';
            } elseif ($contentType === 2) {
                $content .= '<ul>';
                $items = rand(3, 7);
                for ($j = 0; $j < $items; $j++) {
                    $content .= '<li>'.$this->faker->sentence(rand(5, 12)).'</li>';
                }
                $content .= '</ul>';
            } elseif ($contentType === 3) {
                $content .= '<ol>';
                $items = rand(3, 6);
                for ($j = 0; $j < $items; $j++) {
                    $content .= '<li>'.$this->faker->sentence(rand(5, 12)).'</li>';
                }
                $content .= '</ol>';
            } else {
                $content .= '<blockquote><p><strong>'.$this->faker->word().':</strong> ';
                $content .= $this->faker->sentence(rand(10, 20)).'</p></blockquote>';
            }

            if (rand(1, 2) === 1) {
                $content .= '<p>'.$this->faker->paragraphs(rand(1, 2), true).'</p>';
            }
        }

        return $content;
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'rejection_reason' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'published_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'published_at' => null,
            'rejection_reason' => $this->faker->sentence(rand(8, 15)),
        ]);
    }
}
