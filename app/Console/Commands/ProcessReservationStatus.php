<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessReservationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:process-status {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic reservation status transitions based on dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $now = Carbon::now();
        
        $this->info('Processing reservation status transitions...');
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Process confirmed reservations that should be activated
        $this->processConfirmedToActive($now, $isDryRun);

        // Process active reservations that should be completed
        $this->processActiveToCompleted($now, $isDryRun);

        $this->info('Reservation status processing completed!');
    }

    /**
     * Process confirmed reservations that should be activated
     */
    private function processConfirmedToActive(Carbon $now, bool $isDryRun): void
    {
        $reservations = Reservation::where('status', 'confirmed')
            ->where('check_in_date', '<=', $now)
            ->get();

        $this->info("Found {$reservations->count()} confirmed reservations ready for activation");

        foreach ($reservations as $reservation) {
            $this->line("Processing reservation #{$reservation->reservation_number}");

            if (!$isDryRun) {
                $reservation->update([
                    'status' => 'active',
                    'activated_at' => $now,
                ]);

                $this->info("✓ Activated reservation #{$reservation->reservation_number}");
            } else {
                $this->line("  Would activate: #{$reservation->reservation_number} (Check-in: {$reservation->check_in_date->format('Y-m-d')})");
            }
        }
    }

    /**
     * Process active reservations that should be completed
     */
    private function processActiveToCompleted(Carbon $now, bool $isDryRun): void
    {
        $reservations = Reservation::where('status', 'active')
            ->where('check_out_date', '<=', $now)
            ->get();

        $this->info("Found {$reservations->count()} active reservations ready for completion");

        foreach ($reservations as $reservation) {
            $this->line("Processing reservation #{$reservation->reservation_number}");

            if (!$isDryRun) {
                $reservation->update([
                    'status' => 'completed',
                    'completed_at' => $now,
                ]);

                $this->info("✓ Completed reservation #{$reservation->reservation_number}");
            } else {
                $this->line("  Would complete: #{$reservation->reservation_number} (Check-out: {$reservation->check_out_date->format('Y-m-d')})");
            }
        }
    }
}
