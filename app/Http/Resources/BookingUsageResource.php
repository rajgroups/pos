<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'distance_km' => $this->distance_km,
            'hours_used' => $this->hours_used,
            'acre_used' => $this->acre_used,
            'weight_ton' => $this->weight_ton,
        ];
    }
}
