<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use App\Repositories\BlogCommentRepository;
use App\Repositories\NotificationRepository;

class CommentService
{
    public function __construct(
        protected BlogCommentRepository $blogCommentRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Get comments for a blog with pagination.
     */
    public function getComments(int $blogId, int $offset = 0, int $limit = 5): array
    {
        $comments = $this->blogCommentRepository->getTopLevelComments($blogId, $offset, $limit);
        $total = $this->blogCommentRepository->countTopLevelComments($blogId);

        return [
            'comments' => $comments,
            'total' => $total,
            'hasMore' => ($offset + $limit) < $total,
        ];
    }

    /**
     * Get replies for a specific parent comment with paging.
     */
    public function getReplies(int $parentId, int $offset = 0, int $limit = 5): array
    {
        $replies = $this->blogCommentRepository->getReplies($parentId, $offset, $limit);
        // total replies count (we can count directly)
        $total = $this->blogCommentRepository->getRepliesCount ?? null;

        // If repository doesn't provide a count helper, compute from model
        if ($total === null) {
            $total = \App\Models\BlogComment::where('parent_id', $parentId)->count();
        }

        return [
            'replies' => $replies,
            'total' => $total,
            'hasMore' => ($offset + $limit) < $total,
        ];
    }

    /**
     * Create a new comment.
     */
    public function createComment(User $user, Blog $blog, string $content, ?int $parentId = null): BlogComment
    {
        $comment = $this->blogCommentRepository->create([
            'blog_id' => $blog->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'content' => $content,
        ]);

        // Attach the author relation to avoid an immediate extra query when
        // the controller wants to return the created comment with its user.
        $comment->setRelation('user', $user);

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
                'notifiable_id' => $comment->id,
            ]);
        }

        // If it's a reply, notify the parent comment author
        if ($parentId) {
            $parentComment = $this->blogCommentRepository->find($parentId);
            if ($parentComment && $parentComment->user_id !== $user->id && $parentComment->user_id !== $blog->user_id) {
                $this->notificationRepository->create([
                    'user_id' => $parentComment->user_id,
                    'title' => 'New Reply',
                    'message' => "{$user->name} replied to your comment on '{$blog->title}'",
                    'type' => 'info',
                    'notifiable_type' => BlogComment::class,
                    'notifiable_id' => $comment->id,
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
        return $this->blogCommentRepository->delete($comment);
    }
}
