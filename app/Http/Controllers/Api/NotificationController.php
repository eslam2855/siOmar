<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\PushToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        $unreadOnly = $request->boolean('unread_only', false);

        $query = UserNotification::where('user_id', $user->id)
            ->with('notification');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => __('api.notifications_retrieved'),
            'data' => $notifications->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ],
                'unread_count' => $this->notificationService->getUnreadCount($user->id),
            ],
        ]);
    }

    /**
     * Get notification details
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $userNotification = UserNotification::where('user_id', $user->id)
            ->where('notification_id', $id)
            ->with('notification')
            ->first();

        if (!$userNotification) {
            return response()->json([
                'success' => false,
                'message' => __('api.notification_not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('api.notification_retrieved'),
            'data' => $userNotification,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $success = $this->notificationService->markAsRead($user->id, $id);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => __('api.notification_marked_read'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('api.notification_not_found'),
        ], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $count = $this->notificationService->markAllAsRead($user->id);

        return response()->json([
            'success' => true,
            'message' => __('api.notifications_marked_read', ['count' => $count]),
            'data' => [
                'marked_count' => $count,
            ],
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'success' => true,
            'message' => __('api.unread_count_retrieved'),
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }

    /**
     * Register push token
     */
    public function registerPushToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'platform' => 'required|string|in:ios,android,web',
            'device_id' => 'nullable|string',
            'app_version' => 'nullable|string',
            'device_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('api.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        
        $pushToken = $this->notificationService->registerPushToken($user->id, $request->all());

        return response()->json([
            'success' => true,
            'message' => __('api.push_token_registered'),
            'data' => $pushToken,
        ]);
    }

    /**
     * Unregister push token
     */
    public function unregisterPushToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('api.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        
        $success = $this->notificationService->unregisterPushToken($user->id, $request->token);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => __('api.push_token_unregistered'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('api.push_token_not_found'),
        ], 404);
    }

    /**
     * Get user push tokens
     */
    public function pushTokens(Request $request): JsonResponse
    {
        $user = $request->user();
        $tokens = $user->pushTokens()->active()->get();

        return response()->json([
            'success' => true,
            'message' => __('api.push_tokens_retrieved'),
            'data' => $tokens,
        ]);
    }

    /**
     * Create notification (Admin only)
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:reservation,payment,system,legal,maintenance,security',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'data' => 'nullable|array',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'category' => 'nullable|string|in:general,reservation,payment,system,legal,maintenance',
            'is_global' => 'nullable|boolean',
            'target_roles' => 'nullable|array',
            'target_users' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('api.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $notification = $this->notificationService->createAndSend($request->all());

        return response()->json([
            'success' => true,
            'message' => __('api.notification_created'),
            'data' => $notification,
        ], 201);
    }

    /**
     * Get notification statistics (Admin only)
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = [
            'total_notifications' => Notification::count(),
            'active_notifications' => Notification::active()->count(),
            'scheduled_notifications' => Notification::active()->scheduled()->count(),
            'expired_notifications' => Notification::where('expires_at', '<', now())->count(),
            'total_user_notifications' => UserNotification::count(),
            'unread_user_notifications' => UserNotification::unread()->count(),
            'sent_notifications' => UserNotification::sent()->count(),
            'delivered_notifications' => UserNotification::delivered()->count(),
            'total_push_tokens' => PushToken::count(),
            'active_push_tokens' => PushToken::active()->count(),
            'platform_breakdown' => PushToken::active()
                ->selectRaw('platform, count(*) as count')
                ->groupBy('platform')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => __('api.notification_statistics_retrieved'),
            'data' => $stats,
        ]);
    }

    /**
     * Process scheduled notifications (Admin only)
     */
    public function processScheduled(Request $request): JsonResponse
    {
        $processed = $this->notificationService->processScheduledNotifications();

        return response()->json([
            'success' => true,
            'message' => __('api.scheduled_notifications_processed'),
            'data' => [
                'processed_count' => $processed,
            ],
        ]);
    }
}