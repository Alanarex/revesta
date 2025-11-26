<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogLike;
use App\Models\BlogBookmark;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found! Please run UserSeeder first.');
            return;
        }

        $this->command->info('Creating blogs...');

        // Create blogs with varied statuses using factory
        $publishedBlogs = Blog::factory()
            ->count(20)
            ->published()
            ->create();

        $pendingBlogs = Blog::factory()
            ->count(8)
            ->pending()
            ->create();

        $draftBlogs = Blog::factory()
            ->count(5)
            ->draft()
            ->create();

        $rejectedBlogs = Blog::factory()
            ->count(3)
            ->rejected()
            ->create();

        $allBlogs = $publishedBlogs->concat($pendingBlogs)->concat($draftBlogs)->concat($rejectedBlogs);
        $this->command->info('Created ' . $allBlogs->count() . ' blogs.');

        $this->command->info('Creating comments...');

        // Create comments only for published blogs
        $totalComments = 0;
        foreach ($publishedBlogs as $blog) {
            $numComments = rand(5, 20);
            
            // Create parent comments
            $parentComments = BlogComment::factory()
                ->count($numComments)
                ->forBlog($blog->id)
                ->create();

            $totalComments += $numComments;

            // Add some replies to random parent comments
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

        $this->command->info('Created ' . $totalComments . ' comments (including replies).');
        $this->command->info('Creating likes...');

        // Create likes for published blogs
        $totalLikes = 0;
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

        $this->command->info('Created ' . $totalLikes . ' likes.');
        $this->command->info('Creating bookmarks...');

        // Create bookmarks for published blogs
        $totalBookmarks = 0;
        foreach ($publishedBlogs as $blog) {
            $numBookmarks = rand(2, 18);
            $bookmarkers = $users->random(min($numBookmarks, $users->count()));

            foreach ($bookmarkers as $bookmarker) {
                BlogBookmark::create([
                    'user_id' => $bookmarker->id,
                    'blog_id' => $blog->id,
                ]);
                $totalBookmarks++;
            }
        }

        $this->command->info('Created ' . $totalBookmarks . ' bookmarks.');
        $this->command->info('✅ Blog seeding completed successfully!');
    }
}
