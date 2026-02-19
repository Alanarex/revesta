<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBlogApprovalNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Blog $blog,
        public User $admin
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NotificationRepository $notificationRepository): void
    {
        $notificationRepository->create([
            'user_id' => $this->blog->user_id,
            'title' => 'Blog Approved',
            'message' => "Your blog '{$this->blog->title}' has been approved and published by {$this->admin->name}",
            'type' => 'success',
            'notifiable_type' => Blog::class,
            'notifiable_id' => $this->blog->id,
        ]);
    }
}
