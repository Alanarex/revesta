<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogBookmark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogBookmark>
 */
class BlogBookmarkFactory extends Factory
{
    protected $model = BlogBookmark::class;

    public function definition(): array
    {
        return [
            'blog_id' => Blog::inRandomOrder()->value('id') ?? Blog::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
        ];
    }
}
