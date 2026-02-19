<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Database\Seeder;

class BlogCommentSeeder extends Seeder
{
    /**
     * Seed comments for published blogs.
     */
    public function run(): void
    {
        $publishedBlogs = Blog::where('status', 'published')->get();

        if ($publishedBlogs->isEmpty()) {
            $this->command->warn('No published blogs found.');

            return;
        }

        $this->command->info('Creating comments for published blogs...');

        $totalComments = 0;

        foreach ($publishedBlogs as $blog) {
            $numComments = rand(5, 20);

            // Create parent comments
            $parentComments = BlogComment::factory()
                ->count($numComments)
                ->forBlog($blog->id)
                ->create();

            $totalComments += $numComments;

            // Add replies to random parent comments
            $commentsWithReplies = $parentComments->random(min(rand(2, 8), $parentComments->count()));
            foreach ($commentsWithReplies as $parentComment) {
                $numReplies = rand(1, 4);
                BlogComment::factory()
                    ->count($numReplies)
                    ->forBlog($blog->id)
                    ->reply($parentComment->id)
                    ->create();

                $totalComments += $numReplies;
            }
        }

        $this->command->info('✅ Created '.$totalComments.' comments (including replies).');
    }
}
