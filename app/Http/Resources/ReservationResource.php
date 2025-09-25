<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_number' => $this->reservation_number,
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'user' => new UserResource($this->whenLoaded('user')),
            'cancellation_policy' => [
                'id' => $this->cancellationPolicy?->id,
                'name' => $this->cancellationPolicy?->name,
                'description' => $this->cancellationPolicy?->description,
                'cancellation_window' => $this->cancellationPolicy?->getFormattedCancellationWindow(),
                'refund_percentage' => $this->cancellationPolicy?->refund_percentage,
            ],
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'nights' => $this->getNightsCount(),
            'guest_information' => [
                'name' => $this->guest_name,
                'phone' => $this->guest_phone,
                'email' => $this->guest_email,
            ],
            'special_requests' => $this->special_requests,
            'admin_notes' => $this->admin_notes,
            'reservation_notes' => $this->admin_notes ?: \App\Models\Setting::getValue('default_reservation_notes', ''),
            'deposit_requirements' => [
                'minimum_deposit_amount' => $this->minimum_deposit_amount ?: \App\Models\Setting::getValue('default_minimum_deposit_amount', 0),
                'deposit_percentage' => $this->deposit_percentage ?: \App\Models\Setting::getValue('default_deposit_percentage', 0),
                'calculated_deposit_amount' => $this->calculateDepositAmount(),
                'required_deposit_amount' => $this->getDepositAmount(),
            ],
            'pricing' => [
                'total_amount' => $this->total_amount,
                'cleaning_fee' => $this->cleaning_fee,
                'security_deposit' => $this->security_deposit,
                'refund_amount' => $this->refund_amount,
            ],
            'transfer_payment' => [
                'transfer_amount' => $this->transfer_amount,
                'transfer_date' => $this->transfer_date?->toISOString(),
                'transfer_image_url' => $this->getTransferImageUrl(),
                'minimum_deposit_amount' => $this->minimum_deposit_amount,
                'deposit_percentage' => $this->deposit_percentage,
                'calculated_deposit_amount' => $this->calculateDepositAmount(),
                'required_deposit_amount' => $this->getDepositAmount(),
                'deposit_verified' => $this->deposit_verified,
                'deposit_verified_at' => $this->deposit_verified_at?->toISOString(),
                'deposit_status' => $this->getDepositStatus(),
                'is_deposit_sufficient' => $this->isDepositSufficient(),
            ],
            'status' => [
                'current' => $this->status,
                'payment' => $this->payment_status,
                'can_cancel' => $this->canBeCancelled(),
                'badge_color' => $this->getStatusBadgeColor(),
                'payment_badge_color' => $this->getPaymentStatusBadgeColor(),
            ],
            'cancellation' => [
                'cancelled_at' => $this->cancelled_at?->toISOString(),
                'cancellation_reason' => $this->cancellation_reason,
                'refunded_at' => $this->refunded_at?->toISOString(),
            ],
            'timestamps' => [
                'confirmed_at' => $this->confirmed_at?->toISOString(),
                'activated_at' => $this->activated_at?->toISOString(),
                'completed_at' => $this->completed_at?->toISOString(),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
        ];
    }
}
