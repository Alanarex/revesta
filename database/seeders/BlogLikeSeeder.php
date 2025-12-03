<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogLike;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogLikeSeeder extends Seeder
{
    /**
     * Seed likes for published blogs and their comments.
     */
    public function run(): void
    {
        $publishedBlogs = Blog::where('status', 'published')->get();
        $users = User::all();

        if ($publishedBlogs->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No published blogs or users found.');
            return;
        }

        $this->command->info('Creating likes for blogs and comments...');

        $totalLikes = 0;

        // Create likes for published blogs
        foreach ($publishedBlogs as $blog) {
            $numLikes = rand(5, 35);
            $likers = $users->random(min($numLikes, $users->count()));

            foreach ($likers as $liker) {
                BlogLike::create([
                    'user_id' => $liker->id,
                    'likeable_type' => Blog::class,
                    'likeable_id' => $blog->id,
                ]);
                $totalLikes++;
            }

            // Like some comments
            $comments = $blog->comments()->whereNull('parent_id')->get();
            foreach ($comments as $comment) {
                if (rand(1, 3) === 1) { // 33% chance
                    $numCommentLikes = rand(0, 12);
                    $commentLikers = $users->random(min($numCommentLikes, $users->count()));

                    foreach ($commentLikers as $liker) {
                        BlogLike::firstOrCreate([
                            'user_id' => $liker->id,
                            'likeable_type' => BlogComment::class,
                            'likeable_id' => $comment->id,
                        ]);
                        $totalLikes++;
                    }
                }
            }
        }

        $this->command->info('✅ Created ' . $totalLikes . ' likes.');
    }
}
