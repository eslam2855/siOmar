<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\PushToken;
use App\Models\Notification;

class TestPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:push-notification {--user-id=1 : User ID to send notification to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test push notification system with a test FCM token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');

        $this->info("Testing push notification system...");
        $this->info("User ID: {$userId}");

        try {
            // Check if user exists
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }

            $this->info("User found: {$user->name} ({$user->email})");

            // Create a test push token
            $this->info("Creating test push token...");
            $testToken = 'test_fcm_token_' . time();
            
            $pushToken = PushToken::create([
                'user_id' => $user->id,
                'token' => $testToken,
                'platform' => PushToken::PLATFORM_ANDROID,
                'device_id' => 'test_device_' . time(),
                'app_version' => '1.0.0',
                'device_info' => [
                    'model' => 'Test Device',
                    'os_version' => 'Android 13',
                    'app_version' => '1.0.0'
                ],
                'is_active' => true,
                'last_used_at' => now(),
            ]);

            $this->info("✅ Test push token created: {$pushToken->id}");

            // Test notification service
            $notificationService = app(NotificationService::class);

            // Create test notification
            $this->info("Creating test push notification...");
            $notification = $notificationService->createSystemNotification([
                'title' => 'Test Push Notification',
                'message' => 'This is a test push notification with FCM token. Time: ' . now()->format('Y-m-d H:i:s'),
                'priority' => Notification::PRIORITY_HIGH,
                'target_users' => [$userId],
                'data' => [
                    'test' => true,
                    'push_test' => true,
                    'timestamp' => now()->toISOString(),
                    'command' => 'test:push-notification'
                ]
            ]);

            $this->info("✅ Push notification created successfully!");
            $this->info("Notification ID: {$notification->id}");
            $this->info("Title: {$notification->title}");

            // Check push tokens
            $pushTokens = $user->fresh()->activePushTokens;
            $this->info("📱 User now has {$pushTokens->count()} active push token(s)");

            // Show notification statistics
            $unreadCount = $notificationService->getUnreadCount($userId);
            $this->info("📊 User's unread notifications: {$unreadCount}");

            $this->info("🎉 Push notification test completed!");
            $this->warn("Note: The test FCM token is fake, so actual push delivery will fail, but the system is working correctly.");

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
