<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Unit;
use App\Models\UnitMonthPrice;
use App\Models\UnitType;
use App\Models\Slider;
use App\Traits\ApiResponseTrait;
use App\Services\ActivityLoggerService;
use App\Notifications\ReservationStatusChangedNotification;
use App\Notifications\DepositVerifiedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use ApiResponseTrait;
    /**
     * Admin dashboard
     */
    public function dashboard(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Use analytics service for comprehensive stats
        $analytics = \App\Services\AnalyticsService::getDashboardAnalytics();
        $stats = array_merge($analytics['overview'], [
            'monthly_revenue' => $analytics['revenue']['current_month_revenue'] ?? 0,
        ]);

        $recentReservations = Reservation::with(['user', 'unit'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent activities for audit trail
        $recentActivities = ActivityLoggerService::getRecentActivities(10);

        return view('admin.dashboard', compact('stats', 'recentReservations', 'recentActivities', 'analytics'));
    }

    /**
     * Show all reservations
     */
    public function reservations(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = Reservation::with(['user', 'unit.unitType', 'unit.primaryImage']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Show pending reservations
     */
    public function pendingReservations(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $reservations = Reservation::with(['user', 'unit.unitType', 'unit.primaryImage'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Show reservation details
     */
    public function showReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Approve a reservation
     */
    public function approveReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be approved');
        }

        $reservation->update([
            'status' => 'confirmed',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->back()->with('success', 'Reservation approved successfully');
    }

    /**
     * Reject a reservation
     */
    public function rejectReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be rejected');
        }

        $reservation->update([
            'status' => 'cancelled',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->back()->with('success', 'Reservation rejected successfully');
    }

    /**
     * Update admin notes and deposit settings
     */
    public function updateAdminNotes(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'minimum_deposit_amount' => 'nullable|numeric|min:0',
            'deposit_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $reservation->update([
            'admin_notes' => $request->admin_notes,
            'minimum_deposit_amount' => $request->minimum_deposit_amount,
            'deposit_percentage' => $request->deposit_percentage,
        ]);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Admin notes and deposit settings updated successfully.');
    }

    /**
     * Update transfer details
     */
    public function updateTransferDetails(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $request->validate([
            'transfer_amount' => 'nullable|numeric|min:0',
            'transfer_date' => 'nullable|date',
            'transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [];
        
        if ($request->has('transfer_amount')) {
            $updateData['transfer_amount'] = $request->transfer_amount;
        }
        
        if ($request->has('transfer_date')) {
            $updateData['transfer_date'] = $request->transfer_date;
        }

        // Handle transfer image upload if provided
        if ($request->hasFile('transfer_image')) {
            $file = $request->file('transfer_image');
            $fileName = 'transfers/' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $fileName);
            $updateData['transfer_image'] = $fileName;
        }

        $reservation->update($updateData);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Transfer details updated successfully.');
    }

    /**
     * Verify deposit
     */
    public function verifyDeposit(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if (!$reservation->transfer_amount) {
            return redirect()->route('admin.reservations.show', $reservation)
                ->with('error', 'No transfer amount found for this reservation.');
        }

        $success = $reservation->verifyDeposit();

        if ($success) {
            // Log the activity
            ActivityLoggerService::logDepositVerification($reservation);

            // Send notification to user
            $reservation->user->notify(new DepositVerifiedNotification($reservation));

            return redirect()->route('admin.reservations.show', $reservation)
                ->with('success', 'Deposit verified successfully.');
        }

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('error', 'Failed to verify deposit.');
    }

    /**
     * Confirm a reservation (after deposit verification)
     */
    public function confirmReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be confirmed');
        }

        if (!$reservation->deposit_verified) {
            return redirect()->back()->with('error', 'Deposit must be verified before confirming reservation');
        }

        $oldStatus = $reservation->status;
        
        $reservation->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'admin_notes' => $request->input('admin_notes', $reservation->admin_notes),
        ]);

        // Log the activity
        ActivityLoggerService::logReservationStatusChange(
            $reservation,
            $oldStatus,
            'confirmed',
            $request->input('admin_notes')
        );

        // Send notification to user
        $reservation->user->notify(new ReservationStatusChangedNotification(
            $reservation,
            $oldStatus,
            'confirmed',
            $request->input('admin_notes')
        ));

        return redirect()->back()->with('success', 'Reservation confirmed successfully');
    }

    /**
     * Activate a reservation (on check-in date)
     */
    public function activateReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if ($reservation->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Only confirmed reservations can be activated');
        }

        if (now()->lt($reservation->check_in_date)) {
            return redirect()->back()->with('error', 'Cannot activate reservation before check-in date');
        }

        $reservation->update([
            'status' => 'active',
            'activated_at' => now(),
            'admin_notes' => $request->input('admin_notes', $reservation->admin_notes),
        ]);

        return redirect()->back()->with('success', 'Reservation activated successfully');
    }

    /**
     * Complete a reservation (after check-out)
     */
    public function completeReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if ($reservation->status !== 'active') {
            return redirect()->back()->with('error', 'Only active reservations can be completed');
        }

        if (now()->lt($reservation->check_out_date)) {
            return redirect()->back()->with('error', 'Cannot complete reservation before check-out date');
        }

        $reservation->update([
            'status' => 'completed',
            'completed_at' => now(),
            'admin_notes' => $request->input('admin_notes', $reservation->admin_notes),
        ]);

        return redirect()->back()->with('success', 'Reservation completed successfully');
    }

    /**
     * Cancel a reservation
     */
    public function cancelReservation(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Only pending or confirmed reservations can be cancelled');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'refund_amount' => 'nullable|numeric|min:0|max:' . $reservation->total_amount,
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
            'refund_amount' => $request->refund_amount,
            'admin_notes' => $request->input('admin_notes', $reservation->admin_notes),
        ]);

        return redirect()->back()->with('success', 'Reservation cancelled successfully');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded,partially_refunded',
        ]);

        $reservation->update([
            'payment_status' => $request->payment_status,
            'admin_notes' => $request->input('admin_notes', $reservation->admin_notes),
        ]);

        return redirect()->back()->with('success', 'Payment status updated successfully');
    }

    /**
     * Show all units
     */
    public function units(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = Unit::with(['unitType', 'primaryImage', 'pricing', 'monthPrices']);

        // Filter by unit type
        if ($request->filled('unit_type_id')) {
            $query->where('unit_type_id', $request->unit_type_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $units = $query->orderBy('created_at', 'desc')->paginate(15);
        $unitTypes = UnitType::all();

        return view('admin.units.index', compact('units', 'unitTypes'));
    }

    /**
     * Show all users
     */
    public function users(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = User::with('roles');

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show unit details
     */
    public function showUnit(Request $request, Unit $unit)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $unit->load(['unitType', 'amenities', 'images', 'monthPrices']);

        return view('admin.units.show', compact('unit'));
    }

    /**
     * Show user details
     */
    public function showUser(Request $request, User $user)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $user->load(['reservations.unit']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show create unit form
     */
    public function createUnit(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $unitTypes = UnitType::all();
        $amenities = \App\Models\Amenity::all();

        // Prepare months (current + next 2)
        $now = now();
        $months = [
            [
                'key' => 'current',
                'year_month' => $now->format('Y-m'),
                'label' => $now->format('F Y'),
            ],
            [
                'key' => 'next',
                'year_month' => $now->copy()->addMonth()->format('Y-m'),
                'label' => $now->copy()->addMonth()->format('F Y'),
            ],
            [
                'key' => 'next_next',
                'year_month' => $now->copy()->addMonths(2)->format('Y-m'),
                'label' => $now->copy()->addMonths(2)->format('F Y'),
            ],
        ];

        return view('admin.units.create', compact('unitTypes', 'amenities', 'months'));
    }

    /**
     * Store a new unit
     */
    public function storeUnit(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unit_number' => 'required|string|max:50|unique:units',
            'description' => 'required|string',
            'unit_type_id' => 'required|exists:unit_types,id',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'max_guests' => 'required|integer|min:1',
            'size_sqm' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            // Monthly pricing (daily rates per month)
            'monthly_price_current' => 'nullable|numeric|min:0',
            'monthly_price_next' => 'nullable|numeric|min:0',
            'monthly_price_next_next' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $unit = Unit::create($request->only([
            'name', 'unit_number', 'description', 'unit_type_id',
            'bedrooms', 'bathrooms', 'max_guests', 'size_sqm',
            'address', 'latitude', 'longitude', 'status'
        ]));

        // Create pricing (fees + base fallback)
        $pricingData = $request->only([
            'cleaning_fee', 'security_deposit'
        ]);
        
        // Ensure null values are converted to 0 for required fields
        $pricingData['cleaning_fee'] = $pricingData['cleaning_fee'] ?? 0;
        $pricingData['security_deposit'] = $pricingData['security_deposit'] ?? 0;
        
        $unit->pricing()->create($pricingData);

        // Create monthly prices (current + next 2 months) if provided
        $now = now();
        $months = [
            'current' => $now->format('Y-m'),
            'next' => $now->copy()->addMonth()->format('Y-m'),
            'next_next' => $now->copy()->addMonths(2)->format('Y-m'),
        ];

        foreach ($months as $key => $ym) {
            $priceValue = $request->input('monthly_price_' . $key);
            if ($priceValue !== null && $priceValue !== '') {
                UnitMonthPrice::updateOrCreate(
                    ['unit_id' => $unit->id, 'year_month' => $ym],
                    ['daily_price' => $priceValue, 'currency' => 'EGP', 'is_active' => true]
                );
            }
        }

        // Attach amenities
        if ($request->has('amenities')) {
            $unit->amenities()->attach($request->amenities);
        }

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('units/' . $unit->id, $filename, 'public');

                $unit->images()->create([
                    'image_path' => $path,
                    'order' => $index,
                    'is_primary' => $index === 0,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('admin.units')->with('success', __('admin.unit_created'));
    }

    /**
     * Show edit unit form
     */
    public function editUnit(Request $request, Unit $unit)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $unit->load(['unitType', 'amenities', 'monthPrices']);
        $unitTypes = UnitType::all();
        $amenities = \App\Models\Amenity::all();

        // Prepare months (current + next 2) and existing prices
        $now = now();
        $months = [
            [
                'key' => 'current',
                'year_month' => $now->format('Y-m'),
                'label' => $now->format('F Y'),
            ],
            [
                'key' => 'next',
                'year_month' => $now->copy()->addMonth()->format('Y-m'),
                'label' => $now->copy()->addMonth()->format('F Y'),
            ],
            [
                'key' => 'next_next',
                'year_month' => $now->copy()->addMonths(2)->format('Y-m'),
                'label' => $now->copy()->addMonths(2)->format('F Y'),
            ],
        ];

        $existing = $unit->monthPrices->keyBy('year_month');

        return view('admin.units.edit', compact('unit', 'unitTypes', 'amenities', 'months', 'existing'));
    }

    /**
     * Update a unit
     */
    public function updateUnit(Request $request, Unit $unit)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unit_number' => 'required|string|max:50|unique:units,unit_number,' . $unit->id,
            'description' => 'required|string',
            'unit_type_id' => 'required|exists:unit_types,id',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'max_guests' => 'required|integer|min:1',
            'size_sqm' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'cleaning_fee' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            // Monthly pricing (daily rates per month)
            'monthly_price_current' => 'nullable|numeric|min:0',
            'monthly_price_next' => 'nullable|numeric|min:0',
            'monthly_price_next_next' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $unit->update($request->only([
            'name', 'unit_number', 'description', 'unit_type_id',
            'bedrooms', 'bathrooms', 'max_guests', 'size_sqm',
            'address', 'latitude', 'longitude', 'status'
        ]));

        // Update or create pricing
        $pricingData = $request->only([
            'cleaning_fee', 'security_deposit'
        ]);
        
        // Ensure null values are converted to 0 for required fields
        $pricingData['cleaning_fee'] = $pricingData['cleaning_fee'] ?? 0;
        $pricingData['security_deposit'] = $pricingData['security_deposit'] ?? 0;
        
        if ($unit->pricing) {
            $unit->pricing()->update($pricingData);
        } else {
            $unit->pricing()->create($pricingData);
        }

        // Upsert monthly prices (current + next 2)
        $now = now();
        $months = [
            'current' => $now->format('Y-m'),
            'next' => $now->copy()->addMonth()->format('Y-m'),
            'next_next' => $now->copy()->addMonths(2)->format('Y-m'),
        ];
        foreach ($months as $key => $ym) {
            $priceValue = $request->input('monthly_price_' . $key);
            if ($priceValue !== null && $priceValue !== '') {
                UnitMonthPrice::updateOrCreate(
                    ['unit_id' => $unit->id, 'year_month' => $ym],
                    ['daily_price' => $priceValue, 'currency' => 'EGP', 'is_active' => true]
                );
            }
        }

        // Sync amenities
        $unit->amenities()->sync($request->input('amenities', []));

        return redirect()->route('admin.units')->with('success', __('admin.unit_updated'));
    }

    /**
     * Delete a unit
     */
    public function destroyUnit(Request $request, Unit $unit)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Delete associated images from storage
        foreach ($unit->images as $image) {
            if (file_exists(storage_path('app/public/' . $image->image_path))) {
                unlink(storage_path('app/public/' . $image->image_path));
            }
        }

        $unit->delete();

        return redirect()->route('admin.units')->with('success', __('admin.unit_deleted'));
    }

    /**
     * Show create user form
     */
    public function createUser(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('admin.users.create');
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'email', 'phone_number']);
        $data['password'] = bcrypt($request->password);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('profiles', $filename, 'public');
            $data['profile_image'] = $path;
        }

        $user = User::create($data);
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Show edit user form
     */
    public function editUser(Request $request, User $user)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'email', 'phone_number']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && file_exists(storage_path('app/public/' . $user->profile_image))) {
                unlink(storage_path('app/public/' . $user->profile_image));
            }

            $image = $request->file('profile_image');
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('profiles', $filename, 'public');
            $data['profile_image'] = $path;
        }

        $user->update($data);

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Delete a user
     */
    public function destroyUser(Request $request, User $user)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Prevent admin from deleting themselves
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account');
        }

        // Delete profile image if exists
        if ($user->profile_image && file_exists(storage_path('app/public/' . $user->profile_image))) {
            unlink(storage_path('app/public/' . $user->profile_image));
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    /**
     * Upload images for a unit
     */
    public function uploadImages(Request $request, Unit $unit)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('units/' . $unit->id, $filename, 'public');

                $caption = $request->input('captions.' . $index) ?? null;
                $isPrimary = $request->input('is_primary') == $index || $unit->images()->count() == 0;

                $unit->images()->create([
                    'image_path' => $path,
                    'caption' => $caption,
                    'order' => $unit->images()->count(),
                    'is_primary' => $isPrimary,
                    'is_active' => true,
                ]);
            }

            return redirect()->back()->with('success', 'Images uploaded successfully');
        }

        return redirect()->back()->with('error', 'No images were uploaded');
    }

    /**
     * Delete an image
     */
    public function deleteImage(Request $request, Unit $unit, $imageId)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $image = $unit->images()->findOrFail($imageId);

        // Delete the file from storage
        if (file_exists(storage_path('app/public/' . $image->image_path))) {
            unlink(storage_path('app/public/' . $image->image_path));
        }

        $image->delete();

        return redirect()->back()->with('success', 'Image deleted successfully');
    }

    /**
     * Set primary image
     */
    public function setPrimaryImage(Request $request, Unit $unit, $imageId)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Remove primary from all images
        $unit->images()->update(['is_primary' => false]);

        // Set the selected image as primary
        $image = $unit->images()->findOrFail($imageId);
        $image->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Primary image updated successfully');
    }

    /**
     * Reorder images
     */
    public function reorderImages(Request $request, Unit $unit)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'image_order' => 'required|array',
            'image_order.*' => 'required|integer|exists:unit_images,id',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        foreach ($request->input('image_order') as $order => $imageId) {
            $unit->images()->where('id', $imageId)->update(['order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'Images reordered successfully']);
    }

    // ==================== SLIDER MANAGEMENT ====================

    /**
     * Show all sliders
     */
    public function sliders(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = Slider::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $sliders = $query->orderBy('order', 'asc')->paginate(15);

        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show create slider form
     */
    public function createSlider(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('admin.sliders.create');
    }

    /**
     * Store a new slider
     */
    public function storeSlider(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'order', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'slider_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('sliders', $filename, 'public');
            $data['image'] = $path;
        }

        // Set default order if not provided
        if (!isset($data['order'])) {
            $data['order'] = Slider::max('order') + 1;
        }

        Slider::create($data);

        return redirect()->route('admin.sliders')->with('success', 'Slider created successfully');
    }

    /**
     * Show edit slider form
     */
    public function editSlider(Request $request, Slider $slider)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update a slider
     */
    public function updateSlider(Request $request, Slider $slider)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'order', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($slider->image && file_exists(storage_path('app/public/' . $slider->image))) {
                unlink(storage_path('app/public/' . $slider->image));
            }

            $image = $request->file('image');
            $filename = 'slider_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('sliders', $filename, 'public');
            $data['image'] = $path;
        }

        $slider->update($data);

        return redirect()->route('admin.sliders')->with('success', 'Slider updated successfully');
    }

    /**
     * Delete a slider
     */
    public function destroySlider(Request $request, Slider $slider)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Delete image file if exists
        if ($slider->image && file_exists(storage_path('app/public/' . $slider->image))) {
            unlink(storage_path('app/public/' . $slider->image));
        }

        $slider->delete();

        return redirect()->route('admin.sliders')->with('success', 'Slider deleted successfully');
    }

    /**
     * Toggle slider status
     */
    public function toggleSliderStatus(Request $request, Slider $slider)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $slider->update(['is_active' => !$slider->is_active]);

        $status = $slider->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Slider {$status} successfully");
    }

    /**
     * Reorder sliders
     */
    public function reorderSliders(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'slider_order' => 'required|array',
            'slider_order.*' => 'required|integer|exists:sliders,id',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        foreach ($request->input('slider_order') as $order => $sliderId) {
            Slider::where('id', $sliderId)->update(['order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'Sliders reordered successfully']);
    }
}
