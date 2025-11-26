<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BlogRepository
{
    /**
     * Get all published blogs with pagination and optimized queries.
     */
    public function getPublishedBlogs(?string $search = null, int $perPage = 10, ?int $authUserId = null): LengthAwarePaginator
    {
        $query = Blog::select(['id', 'title', 'short_description', 'user_id', 'status', 'published_at', 'created_at', 'updated_at'])
            ->with(['user:id,first_name,last_name,email'])
            ->withCount(['likes', 'comments'])
            ->published()
            ->searchByTitle($search);

        // If an authenticated user id is provided, preload whether that user bookmarked
        // each blog to avoid per-row EXISTS() calls in views.
        if ($authUserId) {
            $query->withCount([
                'bookmarks as bookmarked_by_auth' => function ($q) use ($authUserId) {
                    $q->where('user_id', $authUserId);
                },
                'likes as liked_by_auth' => function ($q) use ($authUserId) {
                    $q->where('user_id', $authUserId);
                }
            ]);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get pending blogs for admin approval with optimized queries.
     */
    public function getPendingBlogs(int $perPage = 20, ?string $search = null, ?int $authorId = null): LengthAwarePaginator
    {
        $query = Blog::select(['id', 'title', 'short_description', 'user_id', 'status', 'created_at', 'updated_at'])
            ->with(['user:id,first_name,last_name,email'])
            ->where('status', 'pending');

        // Apply search filter
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Apply author filter
        if ($authorId) {
            $query->where('user_id', $authorId);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get authors who have pending blogs.
     */
    public function getAuthorsWithPendingBlogs()
    {
        return Blog::select('user_id')
            ->with(['user:id,first_name,last_name'])
            ->where('status', 'pending')
            ->groupBy('user_id')
            ->get()
            ->pluck('user')
            ->sortBy('first_name');
    }

    /**
     * Get all pending blog IDs (with optional filters).
     */
    public function getAllPendingBlogIds(?string $search = null, ?int $authorId = null): array
    {
        $query = Blog::select('id')
            ->where('status', 'pending');

        // Apply search filter
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Apply author filter
        if ($authorId) {
            $query->where('user_id', $authorId);
        }

        return $query->pluck('id')->toArray();
    }

    /**
     * Get user's published blogs with counts.
     */
    public function getUserPublishedBlogs(int $userId): Collection
    {
        return Blog::select(['id', 'title', 'short_description', 'user_id', 'status', 'published_at', 'created_at', 'updated_at'])
            ->withCount(['likes', 'comments'])
            ->where('user_id', $userId)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Get user's draft blogs.
     */
    public function getUserDraftBlogs(int $userId): Collection
    {
        return Blog::where('user_id', $userId)
            ->whereIn('status', ['draft', 'pending', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Find blog by ID with relationships and optimized queries.
     */
    public function findWithRelations(int $id, ?int $authUserId = null): ?Blog
    {
        // Load the blog with basic relations and counts first.
        $blog = Blog::with(['user:id,first_name,last_name,email'])
            ->withCount(['likes', 'bookmarks', 'comments'])
            ->withCount([
                'likes as liked_by_auth' => function ($qq) use ($authUserId) {
                    $qq->where('user_id', $authUserId);
                },
                'bookmarks as bookmarked_by_auth' => function ($qq) use ($authUserId) {
                    $qq->where('user_id', $authUserId);
                }
            ])
            ->find($id);

        if (! $blog) {
            return null;
        }

        // Load all comments for this blog in a single query with user and likes counts
        // including whether the authenticated user liked each comment. We'll then
        // assemble the nested replies tree in memory to avoid further DB hits from
        // the recursive Blade partial.
        $comments = BlogComment::where('blog_id', $blog->id)
            ->with(['user:id,first_name,last_name,email'])
            ->withCount('likes')
            ->withCount(['likes as liked_by_auth' => function ($qq) use ($authUserId) {
                $qq->where('user_id', $authUserId);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        // Prepare a map of comments by id for quick parent lookups and an array for
        // top-level comments.
        $commentsById = $comments->keyBy('id');
        $topLevel = collect();

        // Initialize replies collections and replies_count attribute.
        foreach ($comments as $c) {
            $c->setRelation('replies', collect());
            $c->setAttribute('replies_count', 0);
        }

        // Attach children to their parents, or collect as top-level if no parent.
        foreach ($comments as $c) {
            if ($c->parent_id) {
                if ($commentsById->has($c->parent_id)) {
                    $parent = $commentsById->get($c->parent_id);
                    $replies = $parent->getRelation('replies');
                    $replies->push($c);
                    $parent->setRelation('replies', $replies);
                    $parent->setAttribute('replies_count', $parent->replies->count());
                } else {
                    // Orphaned reply (parent not found) — treat as top-level
                    $topLevel->push($c);
                }
            } else {
                $topLevel->push($c);
            }
        }

        // Order top-level comments descending as before (most recent first)
        $topLevel = $topLevel->sortByDesc('created_at')->values();

        // Attach this assembled collection to the blog so views can use $blog->comments
        $blog->setRelation('comments', $topLevel);

        return $blog;
    }

    /**
     * Create a new blog.
     */
    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    /**
     * Update a blog.
     */
    public function update(Blog $blog, array $data): bool
    {
        return $blog->update($data);
    }

    /**
     * Delete a blog.
     */
    public function delete(Blog $blog): bool
    {
        return $blog->delete();
    }

    /**
     * Approve a blog.
     */
    public function approve(Blog $blog): bool
    {
        return $blog->update([
            'status' => 'published',
            'published_at' => now()
        ]);
    }

    /**
     * Reject a blog.
     */
    public function reject(Blog $blog, ?string $reason = null): bool
    {
        return $blog->update([
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);
    }
}
