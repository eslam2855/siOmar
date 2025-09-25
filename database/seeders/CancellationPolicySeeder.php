<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class CancellationPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default cancellation policy content
        $defaultCancellationPolicy = "Cancellation Policy

1. Cancellation Timeframes:
   - Free cancellation up to 24 hours before check-in
   - 50% refund for cancellations between 24-48 hours before check-in
   - No refund for cancellations less than 24 hours before check-in

2. Refund Process:
   - Refunds will be processed within 5-7 business days
   - Refunds will be issued to the original payment method
   - Processing fees may apply

3. Special Circumstances:
   - Weather-related cancellations: Full refund available
   - Medical emergencies: Case-by-case review
   - Force majeure events: Full refund available

4. No-Show Policy:
   - No-shows will be charged the full reservation amount
   - No refunds will be provided for no-shows

5. Modification Policy:
   - Date changes are subject to availability
   - Rate differences may apply for modifications
   - Modifications must be made at least 48 hours in advance

For questions about cancellations, please contact our customer service team.";

        // Set the default cancellation policy
        Setting::setValue(
            'cancellation_policy',
            $defaultCancellationPolicy,
            'text',
            'legal',
            'Default cancellation policy content'
        );

        Setting::setValue(
            'cancellation_policy_last_updated',
            now()->toDateString(),
            'date',
            'legal',
            'Last update date for cancellation policy'
        );

        $this->command->info('Default cancellation policy has been set.');
    }
}