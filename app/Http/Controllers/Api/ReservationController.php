<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Http\Requests\ReservationListRequest;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\CancellationPolicy;
use App\Services\ActivityLoggerService;
use App\Services\AnalyticsService;
use App\Services\NotificationService;
use App\Notifications\ReservationStatusChangedNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    use ApiResponseTrait;
    
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(ReservationListRequest $request)
    {
        $user = $request->user();
        
        $query = $user->reservations()
            ->with(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy']);

        // Apply filter if provided
        if ($request->has('filter') && in_array($request->filter, ['upcoming', 'current', 'finished'])) {
            $query->byFilter($request->filter);
        }

        // Apply status filter if provided
        if ($request->has('status') && in_array($request->status, [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_ACTIVE,
            Reservation::STATUS_COMPLETED,
            Reservation::STATUS_CANCELLED
        ])) {
            $query->where('status', $request->status);
        }

        // Apply date range filter if provided
        if ($request->has('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('check_out_date', '<=', $request->date_to);
        }

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'LIKE', "%{$search}%")
                  ->orWhere('guest_name', 'LIKE', "%{$search}%")
                  ->orWhere('guest_email', 'LIKE', "%{$search}%")
                  ->orWhereHas('unit', function ($unitQuery) use ($search) {
                      $unitQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['created_at', 'check_in_date', 'check_out_date', 'total_amount', 'status'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 10);
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

        $reservations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ReservationResource::collection($reservations),
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
            ],
            'filters' => [
                'applied_filter' => $request->get('filter'),
                'applied_status' => $request->get('status'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'search' => $request->get('search'),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Get reservation statistics for the authenticated user
     */
    public function statistics(Request $request)
    {
        $user = $request->user();
        
        $statistics = [
            'total' => $user->reservations()->count(),
            'upcoming' => $user->reservations()->upcoming()->count(),
            'current' => $user->reservations()->current()->count(),
            'finished' => $user->reservations()->finished()->count(),
            'by_status' => [
                'pending' => $user->reservations()->where('status', Reservation::STATUS_PENDING)->count(),
                'confirmed' => $user->reservations()->where('status', Reservation::STATUS_CONFIRMED)->count(),
                'active' => $user->reservations()->where('status', Reservation::STATUS_ACTIVE)->count(),
                'completed' => $user->reservations()->where('status', Reservation::STATUS_COMPLETED)->count(),
                'cancelled' => $user->reservations()->where('status', Reservation::STATUS_CANCELLED)->count(),
            ],
            'total_spent' => $user->reservations()
                ->whereIn('status', [Reservation::STATUS_COMPLETED, Reservation::STATUS_ACTIVE])
                ->sum('total_amount'),
            'upcoming_spent' => $user->reservations()
                ->upcoming()
                ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:units,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'nullable|integer|min:1',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'required|email|max:255',
            'special_requests' => 'nullable|string',
            'transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'transfer_amount' => 'nullable|numeric|min:0',
            'transfer_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $unit = Unit::with('cancellationPolicy')->findOrFail($request->unit_id);

        // Check if unit is available for the requested dates
        $conflictingReservations = $unit->reservations()
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('check_in_date', '<=', $request->check_in_date)
                            ->where('check_out_date', '>=', $request->check_out_date);
                    });
            })
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->count();

        if ($conflictingReservations > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Unit is not available for the selected dates',
            ], 400);
        }

        // Check minimum reservation days
        $checkIn = \Carbon\Carbon::parse($request->check_in_date);
        $checkOut = \Carbon\Carbon::parse($request->check_out_date);
        $reservationDays = $checkIn->diffInDays($checkOut);
        $minimumDays = \App\Models\Setting::getMinimumReservationDays();

        if ($reservationDays < $minimumDays) {
            return response()->json([
                'success' => false,
                'message' => "Minimum reservation period is {$minimumDays} days. You selected {$reservationDays} days.",
            ], 400);
        }

        // Calculate total amount using the new monthly pricing system
        
        // Calculate total amount using the new monthly pricing system
        $pricingCalculation = $unit->calculateTotalPriceForRange($checkIn, $checkOut);
        $totalAmount = $pricingCalculation['grand_total'];

        // Handle transfer image upload if provided
        $transferImagePath = null;
        if ($request->hasFile('transfer_image')) {
            $file = $request->file('transfer_image');
            $fileName = 'transfers/' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $fileName);
            $transferImagePath = $fileName;
        }

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'unit_id' => $request->unit_id,
            'cancellation_policy_id' => $unit->cancellation_policy_id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'number_of_guests' => $request->number_of_guests,
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'guest_email' => $request->guest_email,
            'total_amount' => $totalAmount,
            'cleaning_fee' => $pricingCalculation['cleaning_fee'],
            'security_deposit' => $pricingCalculation['security_deposit'],
            'special_requests' => $request->special_requests,
            'transfer_image' => $transferImagePath,
            'transfer_amount' => $request->transfer_amount,
            'transfer_date' => $request->transfer_date,
            'admin_notes' => \App\Models\Setting::getValue('default_reservation_notes', ''),
            'minimum_deposit_amount' => \App\Models\Setting::getValue('default_minimum_deposit_amount', 0),
            'deposit_percentage' => \App\Models\Setting::getValue('default_deposit_percentage', 0),
            'status' => Reservation::STATUS_PENDING,
            'payment_status' => Reservation::PAYMENT_PENDING,
        ]);

        $reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy']);

        // Log the reservation creation
        ActivityLoggerService::logReservationCreated($reservation);

        // Send notification to user
        $this->notificationService->createReservationNotification([
            'title' => __('api.reservation_created_title'),
            'message' => __('api.reservation_created_message', ['reservation_number' => $reservation->reservation_number]),
            'data' => [
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'total_amount' => $reservation->total_amount,
                'check_in_date' => $reservation->check_in_date,
                'check_out_date' => $reservation->check_out_date,
            ],
            'priority' => \App\Models\Notification::PRIORITY_NORMAL,
            'target_users' => [$reservation->user_id],
        ]);

        // Send notification to admins
        $this->notificationService->createReservationNotification([
            'title' => __('api.new_reservation_admin_title'),
            'message' => __('api.new_reservation_admin_message', [
                'reservation_number' => $reservation->reservation_number,
                'guest_name' => $reservation->guest_name,
                'total_amount' => $reservation->total_amount
            ]),
            'data' => [
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'guest_name' => $reservation->guest_name,
                'total_amount' => $reservation->total_amount,
                'check_in_date' => $reservation->check_in_date,
                'check_out_date' => $reservation->check_out_date,
            ],
            'priority' => \App\Models\Notification::PRIORITY_HIGH,
            'target_roles' => ['admin'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully and is pending admin approval. You will be notified once it is approved or rejected.',
            'data' => new ReservationResource($reservation),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Reservation $reservation)
    {
        // Ensure user can only view their own reservations
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation',
            ], 403);
        }

        $reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy']);

        return response()->json([
            'success' => true,
            'data' => new ReservationResource($reservation),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        // Ensure user can only update their own reservations
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation',
            ], 403);
        }

        // Only allow updates for pending reservations
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending reservations can be updated',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'check_in_date' => 'sometimes|required|date|after:today',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'number_of_guests' => 'sometimes|nullable|integer|min:1',
            'guest_name' => 'sometimes|required|string|max:255',
            'guest_phone' => 'sometimes|required|string|max:20',
            'guest_email' => 'sometimes|required|email|max:255',
            'special_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        // If dates are being updated, check availability
        if ($request->has('check_in_date') || $request->has('check_out_date')) {
            $checkIn = $request->get('check_in_date', $reservation->check_in_date);
            $checkOut = $request->get('check_out_date', $reservation->check_out_date);

            // Check if unit is available for the requested dates (excluding current reservation)
            $conflictingReservations = $reservation->unit->reservations()
                ->where('id', '!=', $reservation->id)
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                        });
                })
                ->whereIn('status', ['pending', 'confirmed', 'active'])
                ->count();

            if ($conflictingReservations > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit is not available for the selected dates',
                ], 400);
            }

            // Check minimum reservation days
            $checkInDate = \Carbon\Carbon::parse($checkIn);
            $checkOutDate = \Carbon\Carbon::parse($checkOut);
            $reservationDays = $checkInDate->diffInDays($checkOutDate);
            $minimumDays = \App\Models\Setting::getValue('minimum_reservation_days', 1);

            if ($reservationDays < $minimumDays) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum reservation period is {$minimumDays} days. You selected {$reservationDays} days.",
                ], 400);
            }

            // Recalculate total amount if dates changed
            $pricingCalculation = $reservation->unit->calculateTotalPriceForRange($checkInDate, $checkOutDate);
            $request->merge(['total_amount' => $pricingCalculation['grand_total']]);
        }

        // Handle transfer image upload if provided
        $transferImagePath = null;
        if ($request->hasFile('transfer_image')) {
            $file = $request->file('transfer_image');
            $fileName = 'transfers/' . time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public', $fileName);
            $transferImagePath = $fileName;
        }

        // Update the reservation
        $updateData = $request->only([
            'check_in_date',
            'check_out_date',
            'number_of_guests',
            'guest_name',
            'guest_phone',
            'guest_email',
            'special_requests',
            'total_amount',
            'transfer_amount',
            'transfer_date',
        ]);

        if ($transferImagePath) {
            $updateData['transfer_image'] = $transferImagePath;
        }

        $reservation->update($updateData);

        $reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy']);

        // Log the reservation update
        ActivityLoggerService::logReservationUpdated($reservation);

        return response()->json([
            'success' => true,
            'message' => 'Reservation updated successfully',
            'data' => new ReservationResource($reservation),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Reservation $reservation)
    {
        // Ensure user can only delete their own reservations
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation',
            ], 403);
        }

        // Only allow deletion for pending reservations
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending reservations can be deleted',
            ], 400);
        }

        // Log the reservation deletion before deleting
        ActivityLoggerService::log(
            "Reservation deleted: #{$reservation->reservation_number}",
            $reservation,
            $request->user(),
            [
                'reservation_number' => $reservation->reservation_number,
                'total_amount' => $reservation->total_amount,
                'guest_name' => $reservation->guest_name,
            ],
            'deleted',
            'reservations'
        );

        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reservation deleted successfully',
        ]);
    }

    /**
     * Upload transfer receipt image
     */
    public function uploadTransferImage(Request $request, Reservation $reservation)
    {
        // Ensure user can only upload for their own reservations
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'transfer_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'transfer_amount' => 'required|numeric|min:0',
            'transfer_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        // Handle file upload
        if ($request->hasFile('transfer_image')) {
            $file = $request->file('transfer_image');
            $fileName = 'transfers/' . time() . '_' . $file->getClientOriginalName();
            
            // Store the file
            $path = $file->storeAs('public', $fileName);
            
            // Update reservation
            $reservation->update([
                'transfer_image' => $fileName,
                'transfer_amount' => $request->transfer_amount,
                'transfer_date' => $request->transfer_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer receipt uploaded successfully.',
                'data' => new ReservationResource($reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy'])),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file provided.',
        ], 400);
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        // Ensure user can only cancel their own reservations
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        if (!$reservation->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This reservation cannot be cancelled at this time.',
            ], 400);
        }

        $success = $reservation->cancel($request->reason);

        if ($success) {
            $reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy']);
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully.',
                'data' => new ReservationResource($reservation),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to cancel reservation.',
        ], 500);
    }

    /**
     * Get unit reserved days
     */
    public function getUnitReservedDays(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now()->addMonths(6);

        $reservations = $unit->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate, $endDate])
                    ->orWhereBetween('check_out_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('check_in_date', '<=', $startDate)
                            ->where('check_out_date', '>=', $endDate);
                    });
            })
            ->select('check_in_date', 'check_out_date')
            ->get();

        // Create a set of reserved dates
        $reservedDates = [];
        
        foreach ($reservations as $reservation) {
            $currentDate = \Carbon\Carbon::parse($reservation->check_in_date);
            $checkOutDate = \Carbon\Carbon::parse($reservation->check_out_date);
            
            while ($currentDate < $checkOutDate) {
                $dateString = $currentDate->toDateString();
                if (!in_array($dateString, $reservedDates)) {
                    $reservedDates[] = $dateString;
                }
                $currentDate->addDay();
            }
        }

        // Sort dates chronologically
        sort($reservedDates);

        return response()->json([
            'success' => true,
            'data' => $reservedDates,
        ]);
    }

    /**
     * Get cancellation policies
     */
    public function getCancellationPolicies()
    {
        $policies = CancellationPolicy::active()->get();

        return response()->json([
            'success' => true,
            'data' => $policies,
        ]);
    }

    /**
     * Update reservation status (admin only)
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:confirmed,active,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $oldStatus = $reservation->status;
        $newStatus = $request->status;
        $adminNotes = $request->admin_notes;

        $success = false;
        $message = '';

        switch ($request->status) {
            case 'confirmed':
                $success = $reservation->confirm();
                $message = 'Reservation confirmed successfully.';
                break;
            case 'active':
                $success = $reservation->activate();
                $message = 'Reservation activated successfully.';
                break;
            case 'completed':
                $success = $reservation->complete();
                $message = 'Reservation completed successfully.';
                break;
            case 'cancelled':
                $success = $reservation->cancel($request->admin_notes);
                $message = 'Reservation cancelled successfully.';
                break;
        }

        if ($success) {
            // Log the activity
            ActivityLoggerService::logReservationStatusChange(
                $reservation,
                $oldStatus,
                $newStatus,
                $adminNotes
            );

            // Send notification to user
            $reservation->user->notify(new ReservationStatusChangedNotification(
                $reservation,
                $oldStatus,
                $newStatus,
                $adminNotes
            ));

            // Send push notification to user
            $this->notificationService->createReservationNotification([
                'title' => __('api.reservation_status_changed_title'),
                'message' => __('api.reservation_status_changed_message', [
                    'reservation_number' => $reservation->reservation_number,
                    'old_status' => ucfirst($oldStatus),
                    'new_status' => ucfirst($newStatus)
                ]),
                'data' => [
                    'reservation_id' => $reservation->id,
                    'reservation_number' => $reservation->reservation_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'admin_notes' => $adminNotes,
                ],
                'priority' => $newStatus === 'cancelled' ? \App\Models\Notification::PRIORITY_HIGH : \App\Models\Notification::PRIORITY_NORMAL,
                'target_users' => [$reservation->user_id],
            ]);

            $reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy']);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => new ReservationResource($reservation),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update reservation status.',
        ], 500);
    }

    /**
     * Update admin notes and minimum deposit (Admin only)
     */
    public function updateAdminNotes(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000',
            'minimum_deposit_amount' => 'nullable|numeric|min:0',
            'deposit_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $reservation->update([
            'admin_notes' => $request->admin_notes,
            'minimum_deposit_amount' => $request->minimum_deposit_amount,
            'deposit_percentage' => $request->deposit_percentage,
        ]);

        // Log the activity
        ActivityLoggerService::logAdminAction(
            'Updated admin notes for reservation #' . $reservation->reservation_number,
            $reservation,
            [
                'admin_notes' => $request->admin_notes,
                'minimum_deposit_amount' => $request->minimum_deposit_amount,
                'deposit_percentage' => $request->deposit_percentage,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Admin notes updated successfully.',
            'data' => new ReservationResource($reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy'])),
        ]);
    }

    /**
     * Update transfer details (Admin only)
     */
    public function updateTransferDetails(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'transfer_amount' => 'nullable|numeric|min:0',
            'transfer_date' => 'nullable|date',
            'transfer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

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

        // Log the activity
        ActivityLoggerService::logAdminAction(
            'Updated transfer details for reservation #' . $reservation->reservation_number,
            $reservation,
            $updateData
        );

        return response()->json([
            'success' => true,
            'message' => 'Transfer details updated successfully.',
            'data' => new ReservationResource($reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy'])),
        ]);
    }

    /**
     * Verify deposit (Admin only)
     */
    public function verifyDeposit(Request $request, Reservation $reservation)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        if (!$reservation->transfer_amount) {
            return response()->json([
                'success' => false,
                'message' => 'No transfer amount found for this reservation.',
            ], 400);
        }

        if ($reservation->deposit_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Deposit is already verified.',
            ], 400);
        }

        $success = $reservation->verifyDeposit();

        if ($success) {
            // Log the activity
            ActivityLoggerService::logDepositVerification($reservation);

            // Send notification to user
            $reservation->user->notify(new \App\Notifications\DepositVerifiedNotification($reservation));

            // Send push notification to user
            $this->notificationService->createPaymentNotification([
                'title' => __('api.deposit_verified_title'),
                'message' => __('api.deposit_verified_message', [
                    'reservation_number' => $reservation->reservation_number,
                    'amount' => $reservation->transfer_amount
                ]),
                'data' => [
                    'reservation_id' => $reservation->id,
                    'reservation_number' => $reservation->reservation_number,
                    'amount' => $reservation->transfer_amount,
                    'verification_date' => now()->toDateString(),
                ],
                'priority' => \App\Models\Notification::PRIORITY_HIGH,
                'target_users' => [$reservation->user_id],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit verified successfully.',
                'data' => new ReservationResource($reservation->load(['unit.unitType', 'unit.primaryImage', 'cancellationPolicy'])),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to verify deposit.',
        ], 400);
    }

    /**
     * Calculate total cost for multiple date ranges
     */
    public function calculateBulkPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:units,id',
            'date_ranges' => 'required|array|min:1',
            'date_ranges.*.check_in_date' => 'required|date|after:today',
            'date_ranges.*.check_out_date' => 'required|date|after:date_ranges.*.check_in_date',
            'include_fees' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $unit = Unit::with('cancellationPolicy')->findOrFail($request->unit_id);
        $includeFees = $request->get('include_fees', true);
        
        $results = [];
        $totalCost = 0;
        $totalNights = 0;
        $totalCleaningFees = 0;
        $totalSecurityDeposits = 0;

        foreach ($request->date_ranges as $index => $dateRange) {
            $checkIn = \Carbon\Carbon::parse($dateRange['check_in_date']);
            $checkOut = \Carbon\Carbon::parse($dateRange['check_out_date']);
            
            // Check availability for this date range
            $conflictingReservations = $unit->reservations()
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                                ->where('check_out_date', '>=', $checkOut);
                        });
                })
                ->whereIn('status', ['pending', 'confirmed', 'active'])
                ->count();

            $isAvailable = $conflictingReservations === 0;
            
            // Check minimum reservation days
            $nights = $checkIn->diffInDays($checkOut);
            $minimumDays = \App\Models\Setting::getValue('minimum_reservation_days', 1);
            
            if ($nights < $minimumDays) {
                $isAvailable = false;
                $rangeResult = [
                    'range_index' => $index,
                    'check_in_date' => $checkIn->toDateString(),
                    'check_out_date' => $checkOut->toDateString(),
                    'nights' => $nights,
                    'is_available' => false,
                    'error_message' => "Minimum reservation period is {$minimumDays} days. You selected {$nights} days.",
                    'pricing_breakdown' => null,
                    'total_cost' => 0,
                    'daily_average' => 0,
                ];
                $results[] = $rangeResult;
                continue;
            }
            
            // Calculate pricing for this date range
            $pricingCalculation = $unit->calculateTotalPriceForRange($checkIn, $checkOut);
            
            $rangeResult = [
                'range_index' => $index,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'nights' => $nights,
                'is_available' => $isAvailable,
                'pricing_breakdown' => $pricingCalculation,
                'total_cost' => $pricingCalculation['grand_total'],
                'daily_average' => $nights > 0 ? round($pricingCalculation['grand_total'] / $nights, 2) : 0,
            ];

            if ($isAvailable) {
                $totalCost += $pricingCalculation['grand_total'];
                $totalNights += $nights;
                $totalCleaningFees += $pricingCalculation['cleaning_fee'];
                $totalSecurityDeposits += $pricingCalculation['security_deposit'];
            }

            $results[] = $rangeResult;
        }

        // Calculate overall summary
        $summary = [
            'unit_id' => $unit->id,
            'unit_name' => $unit->name,
            'total_ranges' => count($request->date_ranges),
            'available_ranges' => count(array_filter($results, fn($r) => $r['is_available'])),
            'unavailable_ranges' => count(array_filter($results, fn($r) => !$r['is_available'])),
            'total_nights' => $totalNights,
            'total_cost' => $totalCost,
            'total_cleaning_fees' => $totalCleaningFees,
            'total_security_deposits' => $totalSecurityDeposits,
            'grand_total' => $totalCost,
            'average_daily_rate' => $totalNights > 0 ? round($totalCost / $totalNights, 2) : 0,
            'cancellation_policy' => [
                'id' => $unit->cancellationPolicy?->id,
                'name' => $unit->cancellationPolicy?->name,
                'description' => $unit->cancellationPolicy?->description,
                'cancellation_window' => $unit->cancellationPolicy?->getFormattedCancellationWindow(),
                'refund_percentage' => $unit->cancellationPolicy?->refund_percentage,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'date_ranges' => $results,
            ],
        ]);
    }

    /**
     * Get comprehensive dashboard analytics (Admin only)
     */
    public function getDashboardAnalytics(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $analytics = AnalyticsService::getDashboardAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get revenue analytics (Admin only)
     */
    public function getRevenueAnalytics(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $analytics = AnalyticsService::getRevenueAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get reservation analytics (Admin only)
     */
    public function getReservationAnalytics(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $analytics = AnalyticsService::getReservationAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get unit analytics (Admin only)
     */
    public function getUnitAnalytics(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $analytics = AnalyticsService::getUnitAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get recent activities (Admin only)
     */
    public function getRecentActivities(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $limit = $request->get('limit', 50);
        $activities = ActivityLoggerService::getRecentActivities($limit);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
