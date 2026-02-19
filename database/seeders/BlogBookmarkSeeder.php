<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogBookmark;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogBookmarkSeeder extends Seeder
{
    /**
     * Seed bookmarks for published blogs.
     */
    public function run(): void
    {
        $publishedBlogs = Blog::where('status', 'published')->get();
        $users = User::all();

        if ($publishedBlogs->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No published blogs or users found.');

            return;
        }

        $this->command->info('Creating bookmarks for published blogs...');

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

        $this->command->info('✅ Created '.$totalBookmarks.' bookmarks.');
    }
}
