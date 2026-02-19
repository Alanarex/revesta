<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyAdminsForBlogApproval implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Blog $blog,
        public User $author
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NotificationRepository $notificationRepository): void
    {
        $admins = $notificationRepository->getAdminUsers();

        foreach ($admins as $admin) {
            $notificationRepository->create([
                'user_id' => $admin->id,
                'title' => 'New Blog Approval Request',
                'message' => "{$this->author->name} has requested approval for the blog: '{$this->blog->title}'",
                'type' => 'info',
                'notifiable_type' => Blog::class,
                'notifiable_id' => $this->blog->id,
            ]);
        }
    }
}
