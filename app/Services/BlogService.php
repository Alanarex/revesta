<?php

namespace App\Services;

use App\Jobs\NotifyAdminsForBlogApproval;
use App\Jobs\SendBlogApprovalNotification;
use App\Jobs\SendBlogRejectionNotification;
use App\Models\Blog;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Database\Eloquent\Collection;

class BlogService
{
    public function __construct(
        protected BlogRepository $blogRepository,
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Get published blogs with search.
     */
    public function getPublishedBlogs(?string $search = null, int $perPage = 10, ?int $authUserId = null)
    {
        return $this->blogRepository->getPublishedBlogs($search, $perPage, $authUserId);
    }

    /**
     * Get pending blogs for admin.
     */
    public function getAllBlogs(int $perPage = 20, ?string $search = null, ?int $authorId = null, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null)
    {
        return $this->blogRepository->getAllBlogs($perPage, $search, $authorId, $status, $dateFrom, $dateTo);
    }

    /**
     * Get a user's blogs with precomputed tags for filters.
     */
    public function getUserBlogsWithTags(int $profileUserId, ?int $authUserId = null, bool $includeBookmarked = true): Collection
    {
        return $this->blogRepository->getUserBlogsWithTags($profileUserId, $authUserId, $includeBookmarked);
    }

    /**
     * Get all authors who have created blogs.
     */
    public function getAllAuthors()
    {
        return $this->blogRepository->getAllAuthors();
    }

    /**
     * Get user's blogs.
     */
    public function getUserBlogs(int $userId): array
    {
        // Backwards-compatible: simple passthrough to repository. New callers may pass filter params.
        return [
            'published' => $this->blogRepository->getUserPublishedBlogs($userId),
            'drafts' => $this->blogRepository->getUserDraftBlogs($userId),
        ];
    }

    /**
     * Get user's blogs with optional civil_status filter and optionally include bookmarked blogs.
     */
    public function getUserBlogsWithFilter(int $userId, ?string $civilStatus = null, bool $includeBookmarked = false): array
    {
        return [
            'published' => $this->blogRepository->getUserPublishedBlogsWithFilter($userId, $civilStatus, $includeBookmarked),
            'drafts' => $this->blogRepository->getUserDraftBlogs($userId),
        ];
    }

    /**
     * Find a blog with relations.
     */
    public function findBlog(int $id, ?int $authUserId = null): ?Blog
    {
        return $this->blogRepository->findWithRelations($id, $authUserId);
    }

    /**
     * Create a new blog.
     */
    public function createBlog(User $user, array $data): Blog
    {
        $blogData = [
            'user_id' => $user->id,
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'content' => $data['content'],
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ];

        $blog = $this->blogRepository->create($blogData);

        // Notify admins if requesting approval
        if ($data['status'] === 'pending') {
            $this->notifyAdminsForApproval($blog, $user);
        }

        return $blog;
    }

    /**
     * Update a blog.
     */
    public function updateBlog(Blog $blog, array $data): bool
    {
        $updateData = [
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'content' => $data['content'],
            'status' => $data['status'],
        ];

        // If changing to pending, notify admins
        if ($data['status'] === 'pending' && $blog->status !== 'pending') {
            $this->notifyAdminsForApproval($blog, $blog->user);
        }

        // If publishing, set published_at
        if ($data['status'] === 'published' && $blog->status !== 'published') {
            $updateData['published_at'] = now();
        }

        return $this->blogRepository->update($blog, $updateData);
    }

    /**
     * Publish a blog (submit for approval).
     */
    public function publishBlog(Blog $blog): bool
    {
        // Only allow publishing if it's a draft
        if (! $blog->isDraft()) {
            return false;
        }

        $updateData = [
            'status' => Blog::PENDING,
        ];

        // Notify admins for approval
        $this->notifyAdminsForApproval($blog, $blog->user);

        return $this->blogRepository->update($blog, $updateData);
    }

    /**
     * Delete a blog.
     */
    public function deleteBlog(Blog $blog): bool
    {
        return $this->blogRepository->delete($blog);
    }

    /**
     * Approve a blog (admin action).
     */
    public function approveBlog(Blog $blog, User $admin): bool
    {
        $result = $this->blogRepository->approve($blog);

        if ($result) {
            // Dispatch job to send notification in background
            SendBlogApprovalNotification::dispatch($blog, $admin);
        }

        return $result;
    }

    /**
     * Reject a blog (admin action).
     */
    public function rejectBlog(Blog $blog, User $admin, ?string $reason = null): bool
    {
        $result = $this->blogRepository->reject($blog, $reason);

        if ($result) {
            // Dispatch job to send notification in background
            SendBlogRejectionNotification::dispatch($blog, $admin, $reason);
        }

        return $result;
    }

    /**
     * Notify admins for blog approval request.
     */
    protected function notifyAdminsForApproval(Blog $blog, User $author): void
    {
        // Dispatch job to notify admins in background
        NotifyAdminsForBlogApproval::dispatch($blog, $author);
    }
}
