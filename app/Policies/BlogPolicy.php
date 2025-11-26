<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    /**
     * Determine whether the user can view any blogs.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view published blogs
    }

    /**
     * Determine whether the user can view the blog.
     */
    public function view(?User $user, Blog $blog): bool
    {
        // Published blogs are public
        if ($blog->status === 'published') {
            return true;
        }

        // Drafts/pending can be viewed by author or admin
        return $user && ($user->id === $blog->user_id || $user->isAdmin());
    }

    /**
     * Determine whether the user can create blogs.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create blogs
    }

    /**
     * Determine whether the user can update the blog.
     */
    public function update(User $user, Blog $blog): bool
    {
        return $user->id === $blog->user_id;
    }

    /**
     * Determine whether the user can delete the blog.
     */
    public function delete(User $user, Blog $blog): bool
    {
        return $user->id === $blog->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the blog.
     */
    public function restore(User $user, Blog $blog): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the blog.
     */
    public function forceDelete(User $user, Blog $blog): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve blogs.
     */
    public function approve(User $user, Blog $blog): bool
    {
        return $user->isAdmin() && $blog->status === 'pending';
    }

    /**
     * Determine whether the user can reject blogs.
     */
    public function reject(User $user, Blog $blog): bool
    {
        return $user->isAdmin() && $blog->status === 'pending';
    }

    /**
     * Determine whether the user can manage blogs (admin area).
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
