<?php

namespace Database\Seeders;

use App\Models\Blog;
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

        $this->command->info('Creating blogs with varied statuses...');

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
        $this->command->info('✅ Created '.$allBlogs->count().' blogs.');

        // Call related seeders for comments, likes, and bookmarks
        $this->call([
            BlogCommentSeeder::class,
            BlogLikeSeeder::class,
            BlogBookmarkSeeder::class,
        ]);
    }
}
