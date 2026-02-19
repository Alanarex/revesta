<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBlogRejectionNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Blog $blog,
        public User $admin,
        public ?string $reason = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NotificationRepository $notificationRepository): void
    {
        $message = "Your blog '{$this->blog->title}' has been rejected by {$this->admin->name}";
        if ($this->reason) {
            $message .= ". Reason: {$this->reason}";
        }

        $notificationRepository->create([
            'user_id' => $this->blog->user_id,
            'title' => 'Blog Rejected',
            'message' => $message,
            'type' => 'warning',
            'notifiable_type' => Blog::class,
            'notifiable_id' => $this->blog->id,
        ]);
    }
}
