<?php

namespace Database\Factories;

use App\Models\BlogLike;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogLike>
 */
class BlogLikeFactory extends Factory
{
    protected $model = BlogLike::class;

    public function definition(): array
    {
        return [
            'blog_id' => Blog::inRandomOrder()->value('id') ?? Blog::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
