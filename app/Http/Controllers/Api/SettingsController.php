<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all settings or settings by group
     */
    public function index(Request $request)
    {
        try {
            $group = $request->get('group');
            
            if ($group) {
                $settings = Setting::getByGroup($group);
            } else {
                // Get all settings from database
                $settings = Setting::all()->mapWithKeys(function ($setting) {
                    return [$setting->key => Setting::castValue($setting->value, $setting->type)];
                });
                
                // Add legal documents to the main settings response
                $settings['privacy_policy'] = Setting::getValue('privacy_policy', '');
                $settings['terms_of_service'] = Setting::getValue('terms_of_service', '');
                $settings['privacy_policy_last_updated'] = Setting::getValue('privacy_policy_last_updated', null);
                $settings['terms_of_service_last_updated'] = Setting::getValue('terms_of_service_last_updated', null);
                $settings['cancellation_policy'] = Setting::getValue('cancellation_policy', '');
                $settings['cancellation_policy_last_updated'] = Setting::getValue('cancellation_policy_last_updated', null);
            }

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve settings', 500);
        }
    }

    /**
     * Get a specific setting by key
     */
    public function show($key)
    {
        try {
            $value = Setting::getValue($key);
            
            if ($value === null) {
                return $this->errorResponse('Setting not found', 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $key,
                    'value' => $value,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve setting', 500);
        }
    }

    /**
     * Update a setting
     */
    public function update(Request $request, $key)
    {
        try {
            $request->validate([
                'value' => 'required',
            ]);

            Setting::setValue($key, $request->value);

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully',
                'data' => [
                    'key' => $key,
                    'value' => $request->value,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update setting', 500);
        }
    }

    /**
     * Get reservation settings
     */
    public function reservationSettings()
    {
        try {
            $settings = [
                'default_reservation_notes' => Setting::getValue('default_reservation_notes', ''),
                'default_deposit_percentage' => Setting::getValue('default_deposit_percentage', 0),
                'default_minimum_deposit_amount' => Setting::getValue('default_minimum_deposit_amount', 0),
                'minimum_reservation_days' => Setting::getValue('minimum_reservation_days', 1),
            ];

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve reservation settings', 500);
        }
    }

    /**
     * Get system settings
     */
    public function systemSettings()
    {
        try {
            $settings = [
                'app_name' => config('app.name'),
                'app_version' => config('app.version', '1.0.0'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'debug_mode' => config('app.debug'),
            ];

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve system settings', 500);
        }
    }

    /**
     * Get legal settings (privacy policy, terms of service, and cancellation policy)
     */
    public function legalSettings()
    {
        try {
            $settings = [
                'privacy_policy' => Setting::getValue('privacy_policy', ''),
                'terms_of_service' => Setting::getValue('terms_of_service', ''),
                'cancellation_policy' => Setting::getValue('cancellation_policy', ''),
                'privacy_policy_last_updated' => Setting::getValue('privacy_policy_last_updated', null),
                'terms_of_service_last_updated' => Setting::getValue('terms_of_service_last_updated', null),
                'cancellation_policy_last_updated' => Setting::getValue('cancellation_policy_last_updated', null),
            ];

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve legal settings', 500);
        }
    }

    /**
     * Update privacy policy
     */
    public function updatePrivacyPolicy(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|min:10',
            ]);

            Setting::setValue('privacy_policy', $request->content);
            Setting::setValue('privacy_policy_last_updated', now()->toDateString());

            return response()->json([
                'success' => true,
                'message' => 'Privacy policy updated successfully',
                'data' => [
                    'privacy_policy' => $request->content,
                    'privacy_policy_last_updated' => now()->toDateString(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update privacy policy', 500);
        }
    }

    /**
     * Update terms of service
     */
    public function updateTermsOfService(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|min:10',
            ]);

            Setting::setValue('terms_of_service', $request->content);
            Setting::setValue('terms_of_service_last_updated', now()->toDateString());

            return response()->json([
                'success' => true,
                'message' => 'Terms of service updated successfully',
                'data' => [
                    'terms_of_service' => $request->content,
                    'terms_of_service_last_updated' => now()->toDateString(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update terms of service', 500);
        }
    }

    /**
     * Get privacy policy
     */
    public function getPrivacyPolicy()
    {
        try {
            $privacyPolicy = Setting::getValue('privacy_policy', '');
            $lastUpdated = Setting::getValue('privacy_policy_last_updated', null);

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $privacyPolicy,
                    'last_updated' => $lastUpdated,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve privacy policy', 500);
        }
    }

    /**
     * Get terms of service
     */
    public function getTermsOfService()
    {
        try {
            $termsOfService = Setting::getValue('terms_of_service', '');
            $lastUpdated = Setting::getValue('terms_of_service_last_updated', null);

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $termsOfService,
                    'last_updated' => $lastUpdated,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve terms of service', 500);
        }
    }

    /**
     * Update cancellation policy
     */
    public function updateCancellationPolicy(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|min:10',
            ]);

            Setting::setValue('cancellation_policy', $request->content);
            Setting::setValue('cancellation_policy_last_updated', now()->toDateString());

            return response()->json([
                'success' => true,
                'message' => 'Cancellation policy updated successfully',
                'data' => [
                    'cancellation_policy' => $request->content,
                    'cancellation_policy_last_updated' => now()->toDateString(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update cancellation policy', 500);
        }
    }

    /**
     * Get cancellation policy
     */
    public function getCancellationPolicy()
    {
        try {
            $cancellationPolicy = Setting::getValue('cancellation_policy', '');
            $lastUpdated = Setting::getValue('cancellation_policy_last_updated', null);

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $cancellationPolicy,
                    'last_updated' => $lastUpdated,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve cancellation policy', 500);
        }
    }
}
