<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\User;
use App\Repositories\BlogBookmarkRepository;

class BookmarkService
{
    public function __construct(
        protected BlogBookmarkRepository $blogBookmarkRepository
    ) {}

    /**
     * Toggle bookmark on a blog.
     */
    public function toggleBookmark(User $user, Blog $blog): array
    {
        return $this->blogBookmarkRepository->toggle($user, $blog);
    }

    /**
     * Check if user has bookmarked the blog.
     */
    public function hasBookmarked(User $user, Blog $blog): bool
    {
        return $this->blogBookmarkRepository->hasBookmarked($user, $blog);
    }

    /**
     * Get user's bookmarked blogs.
     */
    public function getUserBookmarks(User $user)
    {
        return $this->blogBookmarkRepository->getUserBookmarks($user);
    }
}
