<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'default_reservation_notes',
                'value' => 'Welcome to our property! Please enjoy your stay.',
                'type' => 'string',
                'group' => 'reservation',
                'description' => 'Default notes shown for new reservations',
            ],
            [
                'key' => 'default_deposit_percentage',
                'value' => '30',
                'type' => 'number',
                'group' => 'reservation',
                'description' => 'Default deposit percentage required',
            ],
            [
                'key' => 'default_minimum_deposit_amount',
                'value' => '100',
                'type' => 'number',
                'group' => 'reservation',
                'description' => 'Minimum deposit amount in currency units',
            ],
            [
                'key' => 'minimum_reservation_days',
                'value' => '1',
                'type' => 'number',
                'group' => 'reservation',
                'description' => 'Minimum number of days for a reservation',
            ],
            [
                'key' => 'app_name',
                'value' => 'SiOmar',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Application name',
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Application version',
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@siomar.com',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Primary contact email',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+1234567890',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Primary contact phone',
            ],
            [
                'key' => 'privacy_policy',
                'value' => 'This is the default privacy policy. Please update this content through the admin panel.',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'Privacy policy content',
            ],
            [
                'key' => 'terms_of_service',
                'value' => 'This is the default terms of service. Please update this content through the admin panel.',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'Terms of service content',
            ],
            [
                'key' => 'privacy_policy_last_updated',
                'value' => now()->toDateString(),
                'type' => 'date',
                'group' => 'legal',
                'description' => 'Last update date for privacy policy',
            ],
            [
                'key' => 'terms_of_service_last_updated',
                'value' => now()->toDateString(),
                'type' => 'date',
                'group' => 'legal',
                'description' => 'Last update date for terms of service',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
