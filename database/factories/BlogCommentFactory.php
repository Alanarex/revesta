<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogComment>
 */
class BlogCommentFactory extends Factory
{
    protected $model = BlogComment::class;

    public function definition(): array
    {
        return [
            'blog_id' => Blog::inRandomOrder()->value('id') ?? Blog::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'content' => $this->generateRandomComment(),
            'parent_id' => null,
        ];
    }

    private function generateRandomComment(): string
    {
        $types = [
            'short' => fn () => $this->faker->sentence(rand(5, 15)),
            'medium' => fn () => $this->faker->sentences(rand(2, 4), true),
            'long' => fn () => $this->faker->paragraph(rand(3, 6)),
        ];

        $type = $this->faker->randomElement(array_keys($types));

        return $types[$type]();
    }

    public function reply(?int $parentId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId ?? BlogComment::inRandomOrder()->value('id'),
        ]);
    }

    public function forBlog(int $blogId): static
    {
        return $this->state(fn (array $attributes) => [
            'blog_id' => $blogId,
        ]);
    }
}
