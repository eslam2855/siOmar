<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingResource extends JsonResource
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
            'unit_id' => $this->unit_id,
            
            // Monthly pricing (from UnitMonthPrice table)
            'monthly_pricing' => $this->whenLoaded('unit', function () {
                return $this->unit->getMonthlyPrices(3);
            }),
            
            // Fees
            'cleaning_fee' => $this->cleaning_fee,
            'security_deposit' => $this->security_deposit,
            
            // Status and validity
            'is_active' => $this->is_active,
            'valid_from' => $this->valid_from?->toISOString(),
            'valid_to' => $this->valid_to?->toISOString(),
            
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
