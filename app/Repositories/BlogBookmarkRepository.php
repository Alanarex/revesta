<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BlogBookmarkRepository
{
    /**
     * Toggle bookmark on a blog.
     */
    public function toggle(User $user, Blog $blog): array
    {
        // Attempt to delete an existing bookmark first; if none deleted, insert.
        $deleted = DB::table('blog_bookmarks')
            ->where('blog_id', $blog->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted) {
            $bookmarked = false;
        } else {
            // Insert if not exists (use insertOrIgnore to avoid unique-constraint race)
            $inserted = DB::table('blog_bookmarks')->insertOrIgnore([
                'user_id' => $user->id,
                'blog_id' => $blog->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bookmarked = $inserted > 0;
        }

        // Return current count — still a single aggregate query.
        $count = DB::table('blog_bookmarks')
            ->where('blog_id', $blog->id)
            ->count();

        return [
            'bookmarked' => $bookmarked,
            'count' => $count,
        ];
    }

    /**
     * Check if user has bookmarked the blog.
     */
    public function hasBookmarked(User $user, Blog $blog): bool
    {
        return $blog->bookmarks()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get user's bookmarked blogs.
     */
    public function getUserBookmarks(User $user)
    {
        return $user->blogBookmarks()
            ->with(['blog.user', 'blog.likes', 'blog.comments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->pluck('blog');
    }
}
