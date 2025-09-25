<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Notification;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification {--user-id=1 : User ID to send notification to} {--type=system : Notification type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification system by sending a test notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $type = $this->option('type');

        $this->info("Testing notification system...");
        $this->info("User ID: {$userId}");
        $this->info("Type: {$type}");

        try {
            // Check if user exists
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }

            $this->info("User found: {$user->name} ({$user->email})");

            // Test Firebase connection
            $this->info("Testing Firebase connection...");
            $notificationService = app(NotificationService::class);
            $this->info("✅ Firebase connection successful!");

            // Create test notification
            $this->info("Creating test notification...");
            $notification = $notificationService->createSystemNotification([
                'title' => 'Test Notification',
                'message' => 'This is a test notification from the SiOmar system. Time: ' . now()->format('Y-m-d H:i:s'),
                'priority' => Notification::PRIORITY_NORMAL,
                'target_users' => [$userId],
                'data' => [
                    'test' => true,
                    'timestamp' => now()->toISOString(),
                    'command' => 'test:notification'
                ]
            ]);

            $this->info("✅ Notification created successfully!");
            $this->info("Notification ID: {$notification->id}");
            $this->info("Title: {$notification->title}");
            $this->info("Message: {$notification->message}");

            // Check if user has push tokens
            $pushTokens = $user->activePushTokens;
            if ($pushTokens->count() > 0) {
                $this->info("📱 User has {$pushTokens->count()} active push token(s):");
                foreach ($pushTokens as $token) {
                    $this->info("  - Platform: {$token->platform}, Device: {$token->device_id}");
                }
            } else {
                $this->warn("⚠️  User has no active push tokens. Notification will only be stored in-app.");
            }

            // Show notification statistics
            $unreadCount = $notificationService->getUnreadCount($userId);
            $this->info("📊 User's unread notifications: {$unreadCount}");

            $this->info("🎉 Test completed successfully!");

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}