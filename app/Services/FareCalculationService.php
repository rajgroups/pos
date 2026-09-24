<?php

namespace App\Services;

use App\Models\BookingFare;
use App\Models\VehicleCategory;
use App\Models\VehicleCategoryPricing;

/**
 * FareCalculationService
 *
 * Single source of truth for all fare, commission, and tax calculations.
 *
 * Responsibilities:
 *   - Calculate fare from vehicle_category_pricings config
 *   - Calculate commission (FIXED or PERCENTAGE) per category
 *   - Calculate GST/tax per category
 *   - Return a structured array used for booking snapshots and API responses
 *   - Provide a helper to persist the snapshot into booking_fares
 *
 * Flutter apps MUST NOT compute any of these values.
 * Controllers MUST NOT contain fare formulae.
 */
class FareCalculationService
{
    // ─── Public API ────────────────────────────────────────────────────────────

    /**
     * Calculate a full fare breakdown for a booking/quote.
     *
     * @param  VehicleCategory  $category  Category including `pricing` relation
     * @param  array            $usage     Keys: distance_km, hours_used, acre_used, weight_ton, waiting_minutes
     * @return array            Structured fare data (see return doc below)
     */
    public function calculate(VehicleCategory $category, array $usage = []): array
    {
        $pricing = $category->pricing;

        if (! $pricing) {
            return $this->emptyFare();
        }

        $pricingType     = $pricing->pricing_type ?? 'fixed';
        $surgeMultiplier = max(1.0, (float) ($pricing->surge_multiplier ?? 1.0));

        // ── Input usage ────────────────────────────────────────────────────────
        $distanceKm      = max(0.0, (float) ($usage['distance_km']     ?? 0));
        $hoursUsed       = max(0.0, (float) ($usage['hours_used']       ?? 0));
        $acresUsed       = max(0.0, (float) ($usage['acre_used']        ?? 0));
        $weightTon       = max(0.0, (float) ($usage['weight_ton']       ?? 0));
        $waitingMinutes  = max(0.0, (float) ($usage['waiting_minutes']  ?? 0));

        // ── Base fare ──────────────────────────────────────────────────────────
        $baseFare        = (float) ($pricing->base_fare   ?? 0);
        $minimumFare     = (float) ($pricing->minimum_fare ?? 0);

        // ── Dynamic fare components ────────────────────────────────────────────
        $distanceCharge  = 0.0;
        $timeCharge      = 0.0;
        $unitRate        = 0.0;
        $usageAmount     = 0.0;

        switch ($pricingType) {
            case 'distance':
                $unitRate       = (float) ($pricing->per_km_rate ?? 0);
                $usageAmount    = $distanceKm;
                $distanceCharge = $distanceKm * $unitRate;
                break;

            case 'hourly':
                $unitRate       = (float) ($pricing->per_hour_rate ?? 0);
                $usageAmount    = $hoursUsed;
                $timeCharge     = $hoursUsed * $unitRate;
                break;

            case 'daily':
                $days           = $hoursUsed > 0 ? max(1, (int) ceil($hoursUsed / 24)) : 1;
                $unitRate       = (float) ($pricing->per_day_rate ?? 0);
                $usageAmount    = $days;
                $timeCharge     = $days * $unitRate;
                break;

            case 'acre':
                $unitRate       = (float) ($pricing->per_acre_rate ?? 0);
                $usageAmount    = $acresUsed;
                $distanceCharge = $acresUsed * $unitRate;
                break;

            case 'weight':
                $unitRate       = (float) ($pricing->per_ton_rate ?? 0);
                $usageAmount    = $weightTon;
                $distanceCharge = $weightTon * $unitRate;
                break;

            case 'fixed':
            default:
                $usageAmount    = 1;
                $unitRate       = 0;
                break;
        }

        // ── Waiting charge ─────────────────────────────────────────────────────
        $waitingChargePerHour = (float) ($pricing->waiting_charge_per_hour ?? 0);
        $waitingCharge        = ($waitingMinutes / 60) * $waitingChargePerHour;

        // ── Night charge ───────────────────────────────────────────────────────
        $nightChargePercent = (float) ($pricing->night_charge_percentage ?? 0);
        // (Night charge is informational; applied to subtotal if > 0)
        $dynamicAmount = $distanceCharge + $timeCharge + $waitingCharge;
        $nightSurge    = $nightChargePercent > 0
            ? round(($baseFare + $dynamicAmount) * $nightChargePercent / 100, 2)
            : 0.0;

        // ── Extra charge & discount ────────────────────────────────────────────
        // Currently 0 – extend here for promo codes, surge, extra stops etc.
        $extraCharge = 0.0;
        $discount    = 0.0;

        // ── Subtotal (before tax) ──────────────────────────────────────────────
        $subtotal = $baseFare + $distanceCharge + $timeCharge + $waitingCharge
                  + $nightSurge + $extraCharge - $discount;

        // Apply surge multiplier
        $subtotal = $subtotal * $surgeMultiplier;

        // Minimum fare floor
        if ($minimumFare > 0 && $subtotal < $minimumFare) {
            $subtotal = $minimumFare;
        }

        $subtotal = round($subtotal, 2);

        // ── Tax ────────────────────────────────────────────────────────────────
        $taxPercentage = (float) ($pricing->tax_percentage ?? 0);
        $taxAmount     = round($subtotal * $taxPercentage / 100, 2);

        // ── User total ─────────────────────────────────────────────────────────
        $userTotal = round($subtotal + $taxAmount, 2);

        // ── Commission ────────────────────────────────────────────────────────
        $commissionType  = $pricing->commission_type  ?? 'percentage';
        $commissionValue = (float) ($pricing->commission_value ?? 0);

        $commissionAmount = $this->computeCommission($subtotal, $commissionType, $commissionValue);

        // ── Driver earnings ────────────────────────────────────────────────────
        // Driver receives subtotal minus admin commission.
        // Tax is collected from user but does NOT go to driver.
        $driverEarnings = round($subtotal - $commissionAmount, 2);

        // ── Legacy compatibility: total_amount ─────────────────────────────────
        // total_amount in booking_fares historically meant "user total".
        $totalAmount = $userTotal;

        return [
            // Pricing metadata
            'pricing_type'     => $pricingType,
            'pricing_id'       => $pricing->id,
            'category_id'      => $category->id,
            'category_name'    => $category->name,
            'surge_multiplier' => $surgeMultiplier,

            // Trip metrics (populated from usage input)
            'distance_km'      => round($distanceKm, 3),
            'duration_minutes' => round($hoursUsed * 60, 2),
            'waiting_minutes'  => round($waitingMinutes, 2),

            // Fare breakdown
            'base_fare'        => round($baseFare, 2),
            'unit_rate'        => round($unitRate, 2),
            'usage_amount'     => round($usageAmount, 2),
            'distance_charge'  => round($distanceCharge, 2),
            'time_charge'      => round($timeCharge, 2),
            'waiting_charge'   => round($waitingCharge, 2),
            'extra_charge'     => round($extraCharge, 2),
            'discount'         => round($discount, 2),
            'subtotal'         => $subtotal,

            // Tax
            'tax_percentage'   => round($taxPercentage, 2),
            'tax_amount'       => $taxAmount,

            // User total
            'user_total'       => $userTotal,

            // Commission
            'commission_type'  => $commissionType,
            'commission_value' => round($commissionValue, 2),
            'commission_amount'=> round($commissionAmount, 2),

            // Driver earnings
            'driver_earnings'  => $driverEarnings,

            // Legacy fields (backward compat with booking_fares existing columns)
            'total_amount'     => $totalAmount,

            // Full snapshot for audit / historical pricing lock
            'snapshot' => [
                'pricing_type'     => $pricingType,
                'pricing_id'       => $pricing->id,
                'category_id'      => $category->id,
                'category_name'    => $category->name,
                'surge_multiplier' => $surgeMultiplier,
                'usage'            => [
                    'distance_km'    => round($distanceKm, 3),
                    'hours_used'     => round($hoursUsed, 2),
                    'acre_used'      => round($acresUsed, 2),
                    'weight_ton'     => round($weightTon, 2),
                    'waiting_minutes'=> round($waitingMinutes, 2),
                ],
                'rates' => [
                    'base_fare'              => round($baseFare, 2),
                    'minimum_fare'           => round($minimumFare, 2),
                    'per_km_rate'            => (float) ($pricing->per_km_rate ?? 0),
                    'per_hour_rate'          => (float) ($pricing->per_hour_rate ?? 0),
                    'per_day_rate'           => (float) ($pricing->per_day_rate ?? 0),
                    'per_acre_rate'          => (float) ($pricing->per_acre_rate ?? 0),
                    'per_ton_rate'           => (float) ($pricing->per_ton_rate ?? 0),
                    'waiting_charge_per_hour'=> (float) ($pricing->waiting_charge_per_hour ?? 0),
                    'night_charge_percentage'=> (float) ($pricing->night_charge_percentage ?? 0),
                    'surge_multiplier'       => $surgeMultiplier,
                    'tax_percentage'         => round($taxPercentage, 2),
                    'commission_type'        => $commissionType,
                    'commission_value'       => round($commissionValue, 2),
                ],
                'calculation' => [
                    'base_fare'       => round($baseFare, 2),
                    'distance_charge' => round($distanceCharge, 2),
                    'time_charge'     => round($timeCharge, 2),
                    'waiting_charge'  => round($waitingCharge, 2),
                    'extra_charge'    => round($extraCharge, 2),
                    'discount'        => round($discount, 2),
                    'subtotal'        => $subtotal,
                    'tax_percentage'  => round($taxPercentage, 2),
                    'tax_amount'      => $taxAmount,
                    'user_total'      => $userTotal,
                    'commission_type' => $commissionType,
                    'commission_value'=> round($commissionValue, 2),
                    'commission_amount'=> round($commissionAmount, 2),
                    'driver_earnings' => $driverEarnings,
                ],
            ],
        ];
    }

    /**
     * Compute admin commission on a given fare subtotal.
     *
     * @param  float   $subtotal          Gross fare (before tax)
     * @param  string  $commissionType    'percentage' | 'fixed'
     * @param  float   $commissionValue   % for percentage, INR for fixed
     * @return float
     */
    public function computeCommission(float $subtotal, string $commissionType, float $commissionValue): float
    {
        if ($commissionType === 'fixed') {
            return max(0.0, $commissionValue);
        }

        // percentage
        $pct = max(0.0, min(100.0, $commissionValue));
        return round($subtotal * $pct / 100, 2);
    }

    /**
     * Persist a fare calculation result into a BookingFare record.
     * Uses updateOrCreate so it's safe to call multiple times (e.g. on completion).
     *
     * @param  \App\Models\Booking  $booking
     * @param  VehicleCategory      $category
     * @param  array                $fare       Return value of calculate()
     * @return BookingFare
     */
    public function syncFare(\App\Models\Booking $booking, VehicleCategory $category, array $fare): BookingFare
    {
        return BookingFare::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'pricing_type'     => $fare['pricing_type'],
                'base_fare'        => $fare['base_fare'],
                'unit_rate'        => $fare['unit_rate'],
                'usage_amount'     => $fare['usage_amount'],
                'distance_charge'  => $fare['distance_charge'],
                'time_charge'      => $fare['time_charge'],
                'waiting_charge'   => $fare['waiting_charge'],
                'extra_charge'     => $fare['extra_charge'],
                'discount'         => $fare['discount'],
                'subtotal'         => $fare['subtotal'],
                'total_amount'     => $fare['total_amount'],
                'commission_type'  => $fare['commission_type'],
                'commission_value' => $fare['commission_value'],
                'commission_amount'=> $fare['commission_amount'],
                'tax_percentage'   => $fare['tax_percentage'],
                'tax_amount'       => $fare['tax_amount'],
                'user_total'       => $fare['user_total'],
                'driver_earnings'  => $fare['driver_earnings'],
                'distance_km'      => $fare['distance_km'],
                'duration_minutes' => $fare['duration_minutes'],
                'waiting_minutes'  => $fare['waiting_minutes'],
                'snapshot'         => $fare['snapshot'],
            ]
        );
    }

    /**
     * Build a structured API fare response for Flutter apps.
     * Flutter MUST display these values without recalculating.
     *
     * @param  array  $fare  Result of calculate()
     * @return array
     */
    public function toApiResponse(array $fare): array
    {
        return [
            'distance_km'      => $fare['distance_km'],
            'duration_minutes' => $fare['duration_minutes'],

            'fare' => [
                'base'       => $fare['base_fare'],
                'distance'   => $fare['distance_charge'],
                'time'       => $fare['time_charge'],
                'waiting'    => $fare['waiting_charge'],
                'extra'      => $fare['extra_charge'],
                'discount'   => $fare['discount'],
                'subtotal'   => $fare['subtotal'],
                'tax'        => $fare['tax_amount'],
                'total'      => $fare['user_total'],
            ],

            'commission' => [
                'type'   => $fare['commission_type'],
                'value'  => $fare['commission_value'],
                'amount' => $fare['commission_amount'],
            ],

            'driver' => [
                'gross'         => $fare['subtotal'],
                'commission'    => $fare['commission_amount'],
                'tax_collected' => $fare['tax_amount'],
                'earnings'      => $fare['driver_earnings'],
            ],
        ];
    }

    // ─── Private helpers ───────────────────────────────────────────────────────

    protected function emptyFare(): array
    {
        return [
            'pricing_type'     => 'fixed',
            'pricing_id'       => null,
            'category_id'      => null,
            'category_name'    => null,
            'surge_multiplier' => 1.0,
            'distance_km'      => 0.0,
            'duration_minutes' => 0.0,
            'waiting_minutes'  => 0.0,
            'base_fare'        => 0.0,
            'unit_rate'        => 0.0,
            'usage_amount'     => 0.0,
            'distance_charge'  => 0.0,
            'time_charge'      => 0.0,
            'waiting_charge'   => 0.0,
            'extra_charge'     => 0.0,
            'discount'         => 0.0,
            'subtotal'         => 0.0,
            'tax_percentage'   => 0.0,
            'tax_amount'       => 0.0,
            'user_total'       => 0.0,
            'commission_type'  => 'percentage',
            'commission_value' => 0.0,
            'commission_amount'=> 0.0,
            'driver_earnings'  => 0.0,
            'total_amount'     => 0.0,
            'snapshot'         => null,
        ];
    }
}
