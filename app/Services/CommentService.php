<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\NotificationRepository;

class CommentService
{
    public function __construct(
        protected CommentRepository $commentRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Get comments for a blog with pagination.
     */
    public function getComments(int $blogId, int $offset = 0, int $limit = 5): array
    {
        $comments = $this->commentRepository->getTopLevelComments($blogId, $offset, $limit);
        $total = $this->commentRepository->countTopLevelComments($blogId);

        return [
            'comments' => $comments,
            'total' => $total,
            'hasMore' => ($offset + $limit) < $total
        ];
    }

    /**
     * Get replies for a specific parent comment with paging.
     */
    public function getReplies(int $parentId, int $offset = 0, int $limit = 5): array
    {
        $replies = $this->commentRepository->getReplies($parentId, $offset, $limit);
        // total replies count (we can count directly)
        $total = $this->commentRepository->getRepliesCount ?? null;

        // If repository doesn't provide a count helper, compute from model
        if ($total === null) {
            $total = \App\Models\BlogComment::where('parent_id', $parentId)->count();
        }

        return [
            'replies' => $replies,
            'total' => $total,
            'hasMore' => ($offset + $limit) < $total
        ];
    }

    /**
     * Create a new comment.
     */
    public function createComment(User $user, Blog $blog, string $content, ?int $parentId = null): BlogComment
    {
        $comment = $this->commentRepository->create([
            'blog_id' => $blog->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'content' => $content
        ]);

        // Notify blog author (if not self-comment)
        if ($blog->user_id !== $user->id) {
            $message = $parentId 
                ? "{$user->name} replied to a comment on your blog '{$blog->title}'"
                : "{$user->name} commented on your blog '{$blog->title}'";

            $this->notificationRepository->create([
                'user_id' => $blog->user_id,
                'title' => 'New Comment',
                'message' => $message,
                'type' => 'info',
                'notifiable_type' => BlogComment::class,
                'notifiable_id' => $comment->id
            ]);
        }

        // If it's a reply, notify the parent comment author
        if ($parentId) {
            $parentComment = $this->commentRepository->find($parentId);
            if ($parentComment && $parentComment->user_id !== $user->id && $parentComment->user_id !== $blog->user_id) {
                $this->notificationRepository->create([
                    'user_id' => $parentComment->user_id,
                    'title' => 'New Reply',
                    'message' => "{$user->name} replied to your comment on '{$blog->title}'",
                    'type' => 'info',
                    'notifiable_type' => BlogComment::class,
                    'notifiable_id' => $comment->id
                ]);
            }
        }

        return $comment;
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(BlogComment $comment): bool
    {
        return $this->commentRepository->delete($comment);
    }
}
