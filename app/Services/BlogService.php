<?php

namespace App\Services;

use App\Jobs\NotifyAdminsForBlogApproval;
use App\Jobs\SendBlogApprovalNotification;
use App\Jobs\SendBlogRejectionNotification;
use App\Models\Blog;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\NotificationRepository;

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
    public function getPendingBlogs(int $perPage = 20, ?string $search = null, ?int $authorId = null)
    {
        return $this->blogRepository->getPendingBlogs($perPage, $search, $authorId);
    }

    /**
     * Get authors who have pending blogs.
     */
    public function getAuthorsWithPendingBlogs()
    {
        return $this->blogRepository->getAuthorsWithPendingBlogs();
    }

    /**
     * Get all pending blog IDs (with optional filters).
     */
    public function getAllPendingBlogIds(?string $search = null, ?int $authorId = null): array
    {
        return $this->blogRepository->getAllPendingBlogIds($search, $authorId);
    }

    /**
     * Get user's blogs for profile.
     */
    public function getUserBlogs(int $userId): array
    {
        return [
            'published' => $this->blogRepository->getUserPublishedBlogs($userId),
            'drafts' => $this->blogRepository->getUserDraftBlogs($userId)
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
            'published_at' => $data['status'] === 'published' ? now() : null
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
        if (!$blog->isDraft()) {
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

    /**
     * Bulk action on multiple blogs (approve or reject).
     */
    public function bulkAction(string $action, array $blogIds, User $admin, ?string $reason = null): array
    {
        $processed = 0;
        $failed = 0;

        foreach ($blogIds as $blogId) {
            try {
                $blog = Blog::find($blogId);
                
                if (!$blog || $blog->status !== 'pending') {
                    $failed++;
                    continue;
                }

                if ($action === 'approve') {
                    $this->approveBlog($blog, $admin);
                    $processed++;
                } elseif ($action === 'reject') {
                    $this->rejectBlog($blog, $admin, $reason);
                    $processed++;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }

        $message = $action === 'approve' 
            ? "{$processed} blog(s) approuvé(s)"
            : "{$processed} blog(s) rejeté(s)";

        if ($failed > 0) {
            $message .= ", {$failed} échec(s)";
        }

        return [
            'message' => $message,
            'processed' => $processed,
            'failed' => $failed,
        ];
    }
}
