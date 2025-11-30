<?php

namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;

class BlogCommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(?User $user, BlogComment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can comment
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, BlogComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, BlogComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the comment.
     */
    public function restore(User $user, BlogComment $comment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the comment.
     */
    public function forceDelete(User $user, BlogComment $comment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can like the comment.
     */
    public function like(User $user, BlogComment $comment): bool
    {
        // Can like comments on published blogs
        if ($comment->blog && $comment->blog->isPublished()) {
            return true;
        }

        // Can like own comments in any blog status
        if ($user->id === $comment->user_id) {
            return true;
        }

        // Cannot like comments on draft/pending/rejected blogs that aren't yours
        return false;
    }
}
