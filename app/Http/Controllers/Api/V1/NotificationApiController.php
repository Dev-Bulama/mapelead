<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\NotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $notifications = NotificationLog::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success([
            'notifications' => $notifications->getCollection()->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'is_read'    => (bool) $n->is_read,
                'url'        => $n->url,
                'data'       => $n->data,
                'created_at' => $n->created_at?->toDateTimeString(),
                'read_at'    => $n->read_at?->toDateTimeString(),
            ])->values(),
            'meta'          => [
                'current_page'  => $notifications->currentPage(),
                'last_page'     => $notifications->lastPage(),
                'total'         => $notifications->total(),
                'unread_count'  => NotificationLog::where('user_id', $request->user()->id)->where('is_read', false)->count(),
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = NotificationLog::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return $this->success(['unread_count' => $count]);
    }

    public function markRead(Request $request, int $notificationId): JsonResponse
    {
        $notification = NotificationLog::where('id', $notificationId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return $this->error('Notification not found', 404);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return $this->success([], message: 'Notification marked as read');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        NotificationLog::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success([], message: 'All notifications marked as read');
    }
}
