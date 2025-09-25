<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\BulkOperationsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Language switching routes
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');
Route::get('/language', [LanguageController::class, 'getCurrentLanguage'])->name('language.current');

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/admin');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Admin routes (require authentication and admin role)
Route::middleware('auth')->group(function () {
    // Admin dashboard
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Reservations
    Route::get('/admin/reservations', [AdminController::class, 'reservations'])->name('admin.reservations');
    Route::get('/admin/reservations/pending', [AdminController::class, 'pendingReservations'])->name('admin.reservations.pending');
    Route::get('/admin/reservations/{reservation}', [AdminController::class, 'showReservation'])->name('admin.reservations.show');
    Route::post('/admin/reservations/{reservation}/approve', [AdminController::class, 'approveReservation'])->name('admin.reservations.approve');
    Route::post('/admin/reservations/{reservation}/reject', [AdminController::class, 'rejectReservation'])->name('admin.reservations.reject');
    Route::post('/admin/reservations/{reservation}/confirm', [AdminController::class, 'confirmReservation'])->name('admin.reservations.confirm');
    Route::post('/admin/reservations/{reservation}/activate', [AdminController::class, 'activateReservation'])->name('admin.reservations.activate');
    Route::post('/admin/reservations/{reservation}/complete', [AdminController::class, 'completeReservation'])->name('admin.reservations.complete');
    Route::post('/admin/reservations/{reservation}/cancel', [AdminController::class, 'cancelReservation'])->name('admin.reservations.cancel');
    Route::post('/admin/reservations/{reservation}/payment-status', [AdminController::class, 'updatePaymentStatus'])->name('admin.reservations.payment-status');
    Route::post('/admin/reservations/{reservation}/admin-notes', [AdminController::class, 'updateAdminNotes'])->name('admin.reservations.admin-notes');
    Route::post('/admin/reservations/{reservation}/transfer-details', [AdminController::class, 'updateTransferDetails'])->name('admin.reservations.transfer-details');
    Route::post('/admin/reservations/{reservation}/verify-deposit', [AdminController::class, 'verifyDeposit'])->name('admin.reservations.verify-deposit');
    
    // Bulk Operations
    Route::post('/admin/bulk/update-status', [BulkOperationsController::class, 'bulkUpdateStatus'])->name('admin.bulk.update-status');
    Route::post('/admin/bulk/verify-deposits', [BulkOperationsController::class, 'bulkVerifyDeposits'])->name('admin.bulk.verify-deposits');
    Route::post('/admin/bulk/cancel-reservations', [BulkOperationsController::class, 'bulkCancelReservations'])->name('admin.bulk.cancel-reservations');
    
    // Units
    Route::get('/admin/units', [AdminController::class, 'units'])->name('admin.units');
    Route::get('/admin/units/create', [AdminController::class, 'createUnit'])->name('admin.units.create');
    Route::post('/admin/units', [AdminController::class, 'storeUnit'])->name('admin.units.store');
    Route::get('/admin/units/{unit}', [AdminController::class, 'showUnit'])->name('admin.units.show');
    Route::get('/admin/units/{unit}/edit', [AdminController::class, 'editUnit'])->name('admin.units.edit');
    Route::put('/admin/units/{unit}', [AdminController::class, 'updateUnit'])->name('admin.units.update');
    Route::delete('/admin/units/{unit}', [AdminController::class, 'destroyUnit'])->name('admin.units.destroy');
    
    // Unit Images
    Route::post('/admin/units/{unit}/images', [AdminController::class, 'uploadImages'])->name('admin.units.images.upload');
    Route::delete('/admin/units/{unit}/images/{image}', [AdminController::class, 'deleteImage'])->name('admin.units.images.delete');
    Route::post('/admin/units/{unit}/images/{image}/primary', [AdminController::class, 'setPrimaryImage'])->name('admin.units.images.primary');
    Route::post('/admin/units/{unit}/images/reorder', [AdminController::class, 'reorderImages'])->name('admin.units.images.reorder');
    
    // Users
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    // Sliders
    Route::get('/admin/sliders', [AdminController::class, 'sliders'])->name('admin.sliders');
    Route::get('/admin/sliders/create', [AdminController::class, 'createSlider'])->name('admin.sliders.create');
    Route::post('/admin/sliders', [AdminController::class, 'storeSlider'])->name('admin.sliders.store');
    Route::get('/admin/sliders/{slider}/edit', [AdminController::class, 'editSlider'])->name('admin.sliders.edit');
    Route::put('/admin/sliders/{slider}', [AdminController::class, 'updateSlider'])->name('admin.sliders.update');
    Route::delete('/admin/sliders/{slider}', [AdminController::class, 'destroySlider'])->name('admin.sliders.destroy');
    Route::post('/admin/sliders/{slider}/toggle', [AdminController::class, 'toggleSliderStatus'])->name('admin.sliders.toggle');
    Route::post('/admin/sliders/reorder', [AdminController::class, 'reorderSliders'])->name('admin.sliders.reorder');
    
    // Settings
    Route::get('/admin/settings', [App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [App\Http\Controllers\AdminSettingsController::class, 'update'])->name('admin.settings.update');
    
    // Notifications
    Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/notifications/create', [AdminNotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/admin/notifications', [AdminNotificationController::class, 'store'])->name('admin.notifications.store');
    Route::get('/admin/notifications/{notification}', [AdminNotificationController::class, 'show'])->name('admin.notifications.show');
    Route::get('/admin/notifications/{notification}/edit', [AdminNotificationController::class, 'edit'])->name('admin.notifications.edit');
    Route::put('/admin/notifications/{notification}', [AdminNotificationController::class, 'update'])->name('admin.notifications.update');
    Route::delete('/admin/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::post('/admin/notifications/{notification}/toggle', [AdminNotificationController::class, 'toggle'])->name('admin.notifications.toggle');
    Route::get('/admin/notifications/statistics', [AdminNotificationController::class, 'statistics'])->name('admin.notifications.statistics');
});
