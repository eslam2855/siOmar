<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $settings = Setting::getReservationSettings();
        
        // Add legal document settings
        $settings['privacy_policy'] = Setting::getValue('privacy_policy', '');
        $settings['terms_of_service'] = Setting::getValue('terms_of_service', '');
        $settings['cancellation_policy'] = Setting::getValue('cancellation_policy', '');
        $settings['privacy_policy_last_updated'] = Setting::getValue('privacy_policy_last_updated', null);
        $settings['terms_of_service_last_updated'] = Setting::getValue('terms_of_service_last_updated', null);
        $settings['cancellation_policy_last_updated'] = Setting::getValue('cancellation_policy_last_updated', null);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $request->validate([
            'default_reservation_notes' => 'nullable|string|max:1000',
            'default_deposit_percentage' => 'nullable|numeric|min:0|max:100',
            'default_minimum_deposit_amount' => 'nullable|numeric|min:0',
            'minimum_reservation_days' => 'nullable|integer|min:1|max:365',
            'reservation_auto_approve' => 'boolean',
            'require_deposit_for_approval' => 'boolean',
            'privacy_policy' => 'nullable|string|min:10',
            'terms_of_service' => 'nullable|string|min:10',
            'cancellation_policy' => 'nullable|string|min:10',
        ]);

        // Update settings
        Setting::setValue(
            'default_reservation_notes',
            $request->default_reservation_notes,
            'string',
            'reservation',
            'Default notes shown to guests when creating reservations'
        );

        Setting::setValue(
            'default_deposit_percentage',
            $request->default_deposit_percentage,
            'number',
            'reservation',
            'Default deposit percentage required for reservations'
        );

        Setting::setValue(
            'default_minimum_deposit_amount',
            $request->default_minimum_deposit_amount,
            'number',
            'reservation',
            'Default minimum deposit amount (0 means no minimum, percentage will be used)'
        );

        Setting::setValue(
            'minimum_reservation_days',
            $request->minimum_reservation_days ?? 1,
            'number',
            'reservation',
            'Minimum number of days required for a reservation'
        );

        Setting::setValue(
            'reservation_auto_approve',
            $request->boolean('reservation_auto_approve'),
            'boolean',
            'reservation',
            'Whether to automatically approve reservations after deposit verification'
        );

        Setting::setValue(
            'require_deposit_for_approval',
            $request->boolean('require_deposit_for_approval'),
            'boolean',
            'reservation',
            'Whether deposit verification is required before approving reservations'
        );

        // Update legal documents if provided
        if ($request->filled('privacy_policy')) {
            Setting::setValue(
                'privacy_policy',
                $request->privacy_policy,
                'text',
                'legal',
                'Privacy policy content'
            );
            Setting::setValue(
                'privacy_policy_last_updated',
                now()->toDateString(),
                'date',
                'legal',
                'Last update date for privacy policy'
            );
        }

        if ($request->filled('terms_of_service')) {
            Setting::setValue(
                'terms_of_service',
                $request->terms_of_service,
                'text',
                'legal',
                'Terms of service content'
            );
            Setting::setValue(
                'terms_of_service_last_updated',
                now()->toDateString(),
                'date',
                'legal',
                'Last update date for terms of service'
            );
        }

        if ($request->filled('cancellation_policy')) {
            Setting::setValue(
                'cancellation_policy',
                $request->cancellation_policy,
                'text',
                'legal',
                'Cancellation policy content'
            );
            Setting::setValue(
                'cancellation_policy_last_updated',
                now()->toDateString(),
                'date',
                'legal',
                'Last update date for cancellation policy'
            );
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
