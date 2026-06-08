<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingFareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'pricing_type' => $this->pricing_type,
            'base_fare' => $this->base_fare,
            'unit_rate' => $this->unit_rate,
            'usage_amount' => $this->usage_amount,
            'extra_charge' => $this->extra_charge,
            'discount' => $this->discount,
            'total_amount' => $this->total_amount,
            'snapshot' => $this->snapshot,
        ];
    }
}
