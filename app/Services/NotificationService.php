<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\PushToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Carbon\Carbon;

class NotificationService
{
    protected $firebase;
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = config('firebase.projects.app.credentials');
            
            // Check if credentials file exists
            if (!$credentialsPath || !file_exists($credentialsPath)) {
                Log::warning('Firebase credentials not configured or file not found', [
                    'credentials_path' => $credentialsPath
                ]);
                return; // Initialize without Firebase
            }
            
            $this->firebase = (new Factory)
                ->withServiceAccount($credentialsPath);
            
            $this->messaging = $this->firebase->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed', [
                'error' => $e->getMessage(),
                'credentials_path' => config('firebase.projects.app.credentials')
            ]);
            // Don't throw the exception, just log it and continue without Firebase
        }
    }

    /**
     * Create and send a notification
     */
    public function createAndSend(array $data): Notification
    {
        // Create the notification
        $notification = Notification::createNotification($data);

        // Determine target users
        $targetUsers = $this->getTargetUsers($notification);

        // Create user notifications
        $this->createUserNotifications($notification, $targetUsers);

        // Send push notifications if immediate
        if (!$notification->isScheduled()) {
            $this->sendPushNotifications($notification, $targetUsers);
        }

        return $notification;
    }

    /**
     * Send push notification to specific users
     */
    public function sendPushNotification(Notification $notification, array $userIds = []): array
    {
        $targetUsers = empty($userIds) ? $this->getTargetUsers($notification) : User::whereIn('id', $userIds)->get();
        return $this->sendPushNotifications($notification, $targetUsers);
    }

    /**
     * Send push notifications to users
     */
    protected function sendPushNotifications(Notification $notification, $users): array
    {
        $results = [];
        
        foreach ($users as $user) {
            $pushTokens = $user->activePushTokens;
            
            foreach ($pushTokens as $token) {
                try {
                    $result = $this->sendFCMNotification($notification, $token);
                    $results[] = $result;
                    
                    // Update user notification status
                    $userNotification = UserNotification::where('user_id', $user->id)
                        ->where('notification_id', $notification->id)
                        ->first();
                    
                    if ($userNotification) {
                        $userNotification->markAsSent($result);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('Push notification failed', [
                        'user_id' => $user->id,
                        'token_id' => $token->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    $results[] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'token_id' => $token->id
                    ];
                }
            }
        }
        
        return $results;
    }

    /**
     * Send FCM notification using Firebase SDK
     */
    protected function sendFCMNotification(Notification $notification, PushToken $token): array
    {
        try {
            // Check if Firebase is available
            if (!$this->messaging) {
                Log::warning('Firebase messaging not available, skipping push notification', [
                    'notification_id' => $notification->id,
                    'token_id' => $token->id
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Firebase not configured',
                    'token_id' => $token->id
                ];
            }
            
            // Create Firebase notification
            $firebaseNotification = FirebaseNotification::create(
                $notification->title,
                $notification->message
            );

            // Create data payload
            $data = [
                'notification_id' => (string) $notification->id,
                'type' => $notification->type,
                'category' => $notification->category,
                'priority' => $notification->priority,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            // Add notification data if available
            if ($notification->data) {
                $data['data'] = json_encode($notification->data);
            }

            // Create the message
            $message = CloudMessage::withTarget('token', $token->token)
                ->withNotification($firebaseNotification)
                ->withData($data);

            // Add platform-specific configurations
            if ($token->platform === PushToken::PLATFORM_ANDROID) {
                $androidConfig = AndroidConfig::fromArray([
                    'priority' => $this->getFCMPriority($notification->priority),
                    'notification' => [
                        'icon' => 'ic_notification',
                        'sound' => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ]);
                $message = $message->withAndroidConfig($androidConfig);
            } elseif ($token->platform === PushToken::PLATFORM_IOS) {
                $apnsConfig = ApnsConfig::fromArray([
                    'headers' => [
                        'apns-priority' => $this->getFCMPriority($notification->priority) === 'high' ? '10' : '5',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ]);
                $message = $message->withApnsConfig($apnsConfig);
            }

            // Send the message
            $result = $this->messaging->send($message);

            $response = [
                'success' => true,
                'message_id' => $result,
                'user_id' => $token->user_id,
                'token_id' => $token->id,
                'platform' => $token->platform,
            ];

            $token->updateLastUsed();

            return $response;

        } catch (\Exception $e) {
            Log::error('Firebase FCM notification failed', [
                'user_id' => $token->user_id,
                'token_id' => $token->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ]);

            // Handle invalid tokens
            if ($this->isTokenInvalid($e)) {
                $token->deactivate();
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'user_id' => $token->user_id,
                'token_id' => $token->id,
                'platform' => $token->platform,
            ];
        }
    }

    /**
     * Get target users for notification
     */
    protected function getTargetUsers(Notification $notification): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::query();

        if ($notification->is_global) {
            // Global notification for all users
            return $query->get();
        }

        if ($notification->target_users) {
            // Specific users
            $query->whereIn('id', $notification->target_users);
        }

        if ($notification->target_roles) {
            // Users with specific roles
            $query->whereHas('roles', function ($q) use ($notification) {
                $q->whereIn('name', $notification->target_roles);
            });
        }

        return $query->get();
    }

    /**
     * Create user notifications
     */
    protected function createUserNotifications(Notification $notification, $users): void
    {
        foreach ($users as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'is_read' => false,
                'is_sent' => false,
                'is_delivered' => false,
            ]);
        }
    }

    /**
     * Get FCM priority based on notification priority
     */
    protected function getFCMPriority(string $priority): string
    {
        return match($priority) {
            Notification::PRIORITY_URGENT, Notification::PRIORITY_HIGH => 'high',
            default => 'normal',
        };
    }

    /**
     * Check if FCM token is invalid
     */
    protected function isTokenInvalid(\Exception $exception): bool
    {
        $message = $exception->getMessage();
        
        // Check for common invalid token error messages
        $invalidTokenErrors = [
            'InvalidRegistration',
            'NotRegistered',
            'MismatchSenderId',
            'invalid-registration-token',
            'registration-token-not-registered',
            'mismatched-credential',
        ];
        
        foreach ($invalidTokenErrors as $error) {
            if (str_contains(strtolower($message), strtolower($error))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Process scheduled notifications
     */
    public function processScheduledNotifications(): int
    {
        $notifications = Notification::active()
            ->scheduled()
            ->notExpired()
            ->get();

        $processed = 0;

        foreach ($notifications as $notification) {
            $targetUsers = $this->getTargetUsers($notification);
            $this->sendPushNotifications($notification, $targetUsers);
            $processed++;
        }

        return $processed;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $userId, int $notificationId): bool
    {
        $userNotification = UserNotification::where('user_id', $userId)
            ->where('notification_id', $notificationId)
            ->first();

        return $userNotification ? $userNotification->markAsRead() : false;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get user notifications with pagination
     */
    public function getUserNotifications(int $userId, int $perPage = 15)
    {
        return UserNotification::where('user_id', $userId)
            ->with('notification')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadCount(int $userId): int
    {
        return UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Register push token for user
     */
    public function registerPushToken(int $userId, array $tokenData): PushToken
    {
        return PushToken::updateOrCreate(
            [
                'user_id' => $userId,
                'token' => $tokenData['token'],
            ],
            [
                'platform' => $tokenData['platform'],
                'device_id' => $tokenData['device_id'] ?? null,
                'app_version' => $tokenData['app_version'] ?? null,
                'device_info' => $tokenData['device_info'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Unregister push token
     */
    public function unregisterPushToken(int $userId, string $token): bool
    {
        return PushToken::where('user_id', $userId)
            ->where('token', $token)
            ->update(['is_active' => false]) > 0;
    }

    /**
     * Create reservation notification
     */
    public function createReservationNotification(array $data): Notification
    {
        return $this->createAndSend([
            'type' => Notification::TYPE_RESERVATION,
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'priority' => $data['priority'] ?? Notification::PRIORITY_NORMAL,
            'category' => Notification::CATEGORY_RESERVATION,
            'target_users' => $data['target_users'] ?? null,
            'target_roles' => $data['target_roles'] ?? null,
        ]);
    }

    /**
     * Create payment notification
     */
    public function createPaymentNotification(array $data): Notification
    {
        return $this->createAndSend([
            'type' => Notification::TYPE_PAYMENT,
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'priority' => $data['priority'] ?? Notification::PRIORITY_HIGH,
            'category' => Notification::CATEGORY_PAYMENT,
            'target_users' => $data['target_users'] ?? null,
            'target_roles' => $data['target_roles'] ?? null,
        ]);
    }

    /**
     * Create system notification
     */
    public function createSystemNotification(array $data): Notification
    {
        return $this->createAndSend([
            'type' => Notification::TYPE_SYSTEM,
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'priority' => $data['priority'] ?? Notification::PRIORITY_NORMAL,
            'category' => Notification::CATEGORY_SYSTEM,
            'is_global' => $data['is_global'] ?? false,
            'target_users' => $data['target_users'] ?? null,
            'target_roles' => $data['target_roles'] ?? null,
        ]);
    }
}
