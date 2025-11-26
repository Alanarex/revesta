<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    /**
     * Get user's unread notifications.
     */
    public function getUnreadNotifications(User $user): Collection
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Count unread notifications for a user.
     */
    public function countUnreadNotifications(User $user): int
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Create a notification.
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): bool
    {
        return $notification->update(['read_at' => now()]);
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(User $user): int
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Get all admin users.
     */
    public function getAdminUsers(): Collection
    {
        return User::where('role', 'admin')->get();
    }
}
