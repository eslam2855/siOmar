<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ActivityLoggerService;
use App\Notifications\ReservationStatusChangedNotification;
use App\Notifications\DepositVerifiedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BulkOperationsController extends Controller
{
    /**
     * Bulk update reservation status
     */
    public function bulkUpdateStatus(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'reservation_ids' => 'required|array|min:1',
            'reservation_ids.*' => 'exists:reservations,id',
            'new_status' => 'required|in:confirmed,active,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reservationIds = $request->reservation_ids;
        $newStatus = $request->new_status;
        $adminNotes = $request->admin_notes;

        DB::beginTransaction();

        try {
            $updatedCount = 0;
            $failedCount = 0;
            $results = [];

            foreach ($reservationIds as $reservationId) {
                $reservation = Reservation::find($reservationId);
                
                if (!$reservation) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => 'Reservation not found',
                    ];
                    continue;
                }

                // Check if status change is valid
                if (!$this->canChangeStatus($reservation, $newStatus)) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => "Cannot change status from {$reservation->status} to {$newStatus}",
                    ];
                    continue;
                }

                $oldStatus = $reservation->status;
                
                // Update status
                $updateData = ['status' => $newStatus];
                
                switch ($newStatus) {
                    case 'confirmed':
                        $updateData['confirmed_at'] = now();
                        break;
                    case 'active':
                        $updateData['activated_at'] = now();
                        break;
                    case 'completed':
                        $updateData['completed_at'] = now();
                        break;
                    case 'cancelled':
                        $updateData['cancelled_at'] = now();
                        break;
                }

                if ($adminNotes) {
                    $updateData['admin_notes'] = $adminNotes;
                }

                $reservation->update($updateData);

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

                $updatedCount++;
                $results[] = [
                    'id' => $reservationId,
                    'status' => 'success',
                    'message' => "Status updated to {$newStatus}",
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk status update completed. {$updatedCount} updated, {$failedCount} failed.",
                'data' => [
                    'updated_count' => $updatedCount,
                    'failed_count' => $failedCount,
                    'results' => $results,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk verify deposits
     */
    public function bulkVerifyDeposits(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'reservation_ids' => 'required|array|min:1',
            'reservation_ids.*' => 'exists:reservations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reservationIds = $request->reservation_ids;

        DB::beginTransaction();

        try {
            $verifiedCount = 0;
            $failedCount = 0;
            $results = [];

            foreach ($reservationIds as $reservationId) {
                $reservation = Reservation::find($reservationId);
                
                if (!$reservation) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => 'Reservation not found',
                    ];
                    continue;
                }

                if (!$reservation->transfer_amount) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => 'No transfer amount found',
                    ];
                    continue;
                }

                if ($reservation->deposit_verified) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => 'Deposit already verified',
                    ];
                    continue;
                }

                $success = $reservation->verifyDeposit();

                if ($success) {
                    // Log the activity
                    ActivityLoggerService::logDepositVerification($reservation);

                    // Send notification to user
                    $reservation->user->notify(new DepositVerifiedNotification($reservation));

                    $verifiedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'success',
                        'message' => 'Deposit verified',
                    ];
                } else {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => 'Failed to verify deposit',
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk deposit verification completed. {$verifiedCount} verified, {$failedCount} failed.",
                'data' => [
                    'verified_count' => $verifiedCount,
                    'failed_count' => $failedCount,
                    'results' => $results,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk cancel reservations
     */
    public function bulkCancelReservations(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'reservation_ids' => 'required|array|min:1',
            'reservation_ids.*' => 'exists:reservations,id',
            'cancellation_reason' => 'required|string|max:500',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reservationIds = $request->reservation_ids;
        $cancellationReason = $request->cancellation_reason;
        $refundAmount = $request->refund_amount;

        DB::beginTransaction();

        try {
            $cancelledCount = 0;
            $failedCount = 0;
            $results = [];

            foreach ($reservationIds as $reservationId) {
                $reservation = Reservation::find($reservationId);
                
                if (!$reservation) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => 'Reservation not found',
                    ];
                    continue;
                }

                if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                    $failedCount++;
                    $results[] = [
                        'id' => $reservationId,
                        'status' => 'failed',
                        'message' => "Cannot cancel reservation with status: {$reservation->status}",
                    ];
                    continue;
                }

                $oldStatus = $reservation->status;
                
                $reservation->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $cancellationReason,
                    'refund_amount' => $refundAmount,
                ]);

                // Log the activity
                ActivityLoggerService::logReservationStatusChange(
                    $reservation,
                    $oldStatus,
                    'cancelled',
                    $cancellationReason
                );

                // Send notification to user
                $reservation->user->notify(new ReservationStatusChangedNotification(
                    $reservation,
                    $oldStatus,
                    'cancelled',
                    $cancellationReason
                ));

                $cancelledCount++;
                $results[] = [
                    'id' => $reservationId,
                    'status' => 'success',
                    'message' => 'Reservation cancelled',
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk cancellation completed. {$cancelledCount} cancelled, {$failedCount} failed.",
                'data' => [
                    'cancelled_count' => $cancelledCount,
                    'failed_count' => $failedCount,
                    'results' => $results,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if status change is valid
     */
    private function canChangeStatus(Reservation $reservation, string $newStatus): bool
    {
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['active', 'cancelled'],
            'active' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        return in_array($newStatus, $validTransitions[$reservation->status] ?? []);
    }
}
