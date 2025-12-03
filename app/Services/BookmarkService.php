<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\User;

class BookmarkService
{
    /**
     * Toggle bookmark on a blog.
     */
    public function toggleBookmark(User $user, Blog $blog): array
    {
        $existingBookmark = $blog->bookmarks()
            ->where('user_id', $user->id)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            $bookmarked = false;
        } else {
            $blog->bookmarks()->create([
                'user_id' => $user->id
            ]);
            $bookmarked = true;
        }

        return [
            'bookmarked' => $bookmarked,
            'count' => $blog->bookmarks()->count()
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
