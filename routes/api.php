<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\BulkOperationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Public unit routes with caching
Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/units/{unit}', [UnitController::class, 'show']);
    Route::post('/units/{unit}/calculate-pricing', [UnitController::class, 'calculatePricing']);
    Route::get('/units/{unit}/reserved-days', [ReservationController::class, 'getUnitReservedDays']);
    Route::get('/search/units', [UnitController::class, 'search']);
});

// Public slider routes (for mobile home page) with caching
Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/sliders', [SliderController::class, 'index']);
    Route::get('/sliders/{slider}', [SliderController::class, 'show']);
});

// Public settings routes
Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/reservation', [SettingsController::class, 'reservationSettings']);
    Route::get('/settings/system', [SettingsController::class, 'systemSettings']);
    Route::get('/settings/legal', [SettingsController::class, 'legalSettings']);
    Route::get('/settings/{key}', [SettingsController::class, 'show']);
    
    // Public legal document routes
    Route::get('/privacy-policy', [SettingsController::class, 'getPrivacyPolicy']);
    Route::get('/terms-of-service', [SettingsController::class, 'getTermsOfService']);
    Route::get('/cancellation-policy', [SettingsController::class, 'getCancellationPolicy']);
});

// Protected routes with enhanced security
Route::middleware(['auth:sanctum', 'throttle:300,1'])->group(function () {
    // Unit availability (requires authentication for personalized results)
    Route::get('/units/{unit}/availability', [UnitController::class, 'checkAvailability']);
    
    // Reservations
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
    
    // User profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread/count', [NotificationController::class, 'unreadCount']);
    Route::post('/push-tokens/register', [NotificationController::class, 'registerPushToken']);
    Route::post('/push-tokens/unregister', [NotificationController::class, 'unregisterPushToken']);
    Route::get('/push-tokens', [NotificationController::class, 'pushTokens']);
    
    // Settings (admin only)
    Route::middleware(['admin'])->group(function () {
        Route::put('/settings/{key}', [SettingsController::class, 'update']);
        
        // Legal document management (admin only)
        Route::put('/privacy-policy', [SettingsController::class, 'updatePrivacyPolicy']);
        Route::put('/terms-of-service', [SettingsController::class, 'updateTermsOfService']);
        Route::put('/cancellation-policy', [SettingsController::class, 'updateCancellationPolicy']);
    });
});

// Reservation routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/statistics', [ReservationController::class, 'statistics']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::post('/reservations/{reservation}/upload-transfer', [ReservationController::class, 'uploadTransferImage']);
    Route::get('/cancellation-policies', [ReservationController::class, 'getCancellationPolicies']);
    Route::post('/reservations/bulk-pricing', [ReservationController::class, 'calculateBulkPricing']);
    
    // Admin-only routes
    Route::middleware(['admin'])->group(function () {
        Route::post('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus']);
        Route::post('/reservations/{reservation}/admin-notes', [ReservationController::class, 'updateAdminNotes']);
        Route::post('/reservations/{reservation}/verify-deposit', [ReservationController::class, 'verifyDeposit']);
        Route::post('/reservations/{reservation}/transfer-details', [ReservationController::class, 'updateTransferDetails']);
        
        // Bulk Operations APIs
        Route::post('/bulk/update-status', [BulkOperationsController::class, 'bulkUpdateStatus']);
        Route::post('/bulk/verify-deposits', [BulkOperationsController::class, 'bulkVerifyDeposits']);
        Route::post('/bulk/cancel-reservations', [BulkOperationsController::class, 'bulkCancelReservations']);
        
        // Analytics APIs
        Route::get('/analytics/dashboard', [ReservationController::class, 'getDashboardAnalytics']);
        Route::get('/analytics/revenue', [ReservationController::class, 'getRevenueAnalytics']);
        Route::get('/analytics/reservations', [ReservationController::class, 'getReservationAnalytics']);
        Route::get('/analytics/units', [ReservationController::class, 'getUnitAnalytics']);
        Route::get('/analytics/activities', [ReservationController::class, 'getRecentActivities']);
        
        // Notification management (admin only)
        Route::post('/notifications/create', [NotificationController::class, 'create']);
        Route::get('/notifications/statistics', [NotificationController::class, 'statistics']);
        Route::post('/notifications/process-scheduled', [NotificationController::class, 'processScheduled']);
    });
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
    ]);
}); 