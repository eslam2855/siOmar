<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled notifications that are ready to be sent';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Processing scheduled notifications...');

        $processed = $notificationService->processScheduledNotifications();

        if ($processed > 0) {
            $this->info("Successfully processed {$processed} scheduled notifications.");
        } else {
            $this->info('No scheduled notifications were ready to be processed.');
        }

        return Command::SUCCESS;
    }
}