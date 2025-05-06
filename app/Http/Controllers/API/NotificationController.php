<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::byUser($user->id)
            ->orderBy('is_read')
            ->orderByDesc('created_at')
            ->paginate(10);

        $unreadCount = Notification::byUser($user->id)->unread()->count();

        return response()->json([
            'message' => 'Danh sách thông báo',
            'notifications' => NotificationResource::collection($notifications),
            'unread_count' => $unreadCount,
            'pagination' => [
                'total' => $notifications->total(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
            ],
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()->id;

        Notification::byUser($userId)->unread()->update(['is_read' => true]);

        return response()->json([
            'message' => 'Tất cả thông báo đã được đánh dấu là đã đọc.',
        ], 200);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $user = $request->user();

        if ($notification->user_id !== $user->id) {
            return response()->json([
                'message' => 'Bạn không có quyền thực hiện hành động này.',
            ], 403);
        }

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json([
            'message' => 'Thông báo đã được đánh dấu là đã đọc.',
            'notification_id' => $notification->id,
            'order_code' => $notification->order_code,
        ]);
    }
}
