<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * BookingFareResource
 *
 * Exposes the complete fare breakdown to Flutter apps.
 * Flutter MUST display these values without recalculating anything.
 */
class BookingFareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'booking_id'       => $this->booking_id,
            'pricing_type'     => $this->pricing_type,

            // ── Fare breakdown ────────────────────────────────────────────
            'base_fare'        => $this->base_fare,
            'distance_charge'  => $this->distance_charge ?? 0,
            'time_charge'      => $this->time_charge ?? 0,
            'waiting_charge'   => $this->waiting_charge ?? 0,
            'extra_charge'     => $this->extra_charge,
            'discount'         => $this->discount,
            'subtotal'         => $this->subtotal ?? $this->total_amount,

            // ── Tax ───────────────────────────────────────────────────────
            'tax_percentage'   => $this->tax_percentage ?? 0,
            'tax_amount'       => $this->tax_amount ?? 0,

            // ── User total ─────────────────────────────────────────────────
            'user_total'       => $this->user_total ?? $this->total_amount,
            'total_amount'     => $this->total_amount, // legacy compat

            // ── Commission ─────────────────────────────────────────────────
            'commission_type'  => $this->commission_type ?? 'percentage',
            'commission_value' => $this->commission_value ?? 0,
            'commission_amount'=> $this->commission_amount ?? 0,

            // ── Driver earnings ────────────────────────────────────────────
            'driver_earnings'  => $this->driver_earnings ?? 0,

            // ── Trip metrics ───────────────────────────────────────────────
            'distance_km'      => $this->distance_km,
            'duration_minutes' => $this->duration_minutes,
            'waiting_minutes'  => $this->waiting_minutes,

            // ── Snapshot (full audit trail) ────────────────────────────────
            'snapshot'         => $this->snapshot,
        ];
    }
}
