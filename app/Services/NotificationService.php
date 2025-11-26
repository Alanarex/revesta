<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\NotificationRepository;

class NotificationService
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Get unread notifications for a user.
     */
    public function getUnreadNotifications(User $user)
    {
        return $this->notificationRepository->getUnreadNotifications($user);
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $this->notificationRepository->countUnreadNotifications($user);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): bool
    {
        return $this->notificationRepository->markAsRead($notification);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return $this->notificationRepository->markAllAsRead($user);
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(Notification $notification): bool
    {
        return $this->notificationRepository->delete($notification);
    }
}
