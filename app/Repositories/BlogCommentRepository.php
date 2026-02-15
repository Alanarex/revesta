<?php

namespace App\Repositories;

use App\Models\BlogComment;
use Illuminate\Database\Eloquent\Collection;

class BlogCommentRepository
{
    /**
     * Get top-level comments for a blog with pagination.
     */
    public function getTopLevelComments(int $blogId, int $offset = 0, int $limit = 5): Collection
    {
        return BlogComment::with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->withCount('replies')
            ->where('blog_id', $blogId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * Count total top-level comments for a blog.
     */
    public function countTopLevelComments(int $blogId): int
    {
        return BlogComment::where('blog_id', $blogId)
            ->whereNull('parent_id')
            ->count();
    }

    /**
     * Get replies for a comment with optional offset/limit for paging.
     */
    public function getReplies(int $parentId, int $offset = 0, int $limit = 5): Collection
    {
        return BlogComment::with(['user', 'likes'])
            ->where('parent_id', $parentId)
            ->orderBy('created_at', 'asc')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * Create a new comment.
     */
    public function create(array $data): BlogComment
    {
        return BlogComment::create($data);
    }

    /**
     * Delete a comment.
     */
    public function delete(BlogComment $comment): bool
    {
        return $comment->delete();
    }

    /**
     * Find a comment by ID.
     */
    public function find(int $id): ?BlogComment
    {
        return BlogComment::find($id);
    }
}
