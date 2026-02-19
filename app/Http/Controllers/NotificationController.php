<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    public function unreadCount()
    {
        $count = $this->notificationService->getUnreadCount(Auth::user());

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'success' => true,
        ]);
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues!',
        ]);
    }
}
