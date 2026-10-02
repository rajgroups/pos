<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingFare;
use App\Models\Driver;
use App\Models\RideTransaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RideSettlementService
 *
 * Single source of truth for financially settling a completed Indicab ride.
 *
 * Responsibilities:
 *  - Verify the booking is in a settleable state
 *  - Check idempotency (no duplicate settlement)
 *  - Pull the already-calculated fare from booking_fares
 *  - Snapshot commission and tax from that fare record
 *  - Create the ride_transactions ledger record
 *  - Credit the driver's wallet_balance
 *  - Record the wallet movement in wallet_transactions
 *  - Everything runs inside a single DB::transaction() — rollback on any failure
 *
 * Controllers and BookingService MUST NOT contain any settlement logic.
 * This service is the only place where ride_transactions rows are created.
 */
class RideSettlementService
{
    /**
     * Settle a completed booking.
     *
     * This method is idempotent:
     *  - If a ride_transaction already exists for this booking, it is returned
     *    immediately without creating a duplicate.
     *  - The DB UNIQUE constraint on booking_id provides a final safety net
     *    against race conditions.
     *
     * @param  Booking $booking  Must already have status = 'completed'
     * @return RideTransaction   The financial ledger record
     *
     * @throws \Throwable  Propagates on any settlement failure (triggers rollback)
     */
    public function settle(Booking $booking): RideTransaction
    {
        // ── Idempotency check ──────────────────────────────────────────────────
        // If settlement already exists, return it — do NOT process again.
        $existing = RideTransaction::where('booking_id', $booking->id)->first();
        if ($existing) {
            Log::info('RideSettlementService: booking already settled', [
                'booking_id'      => $booking->id,
                'transaction_ref' => $existing->transaction_ref,
            ]);
            return $existing;
        }

        return DB::transaction(function () use ($booking) {

            // ── Re-fetch booking with lock + relations ─────────────────────────
            /** @var Booking $booking */
            $booking = Booking::with(['fare', 'category.pricing', 'user', 'driver'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            // ── Double-check idempotency inside the transaction ────────────────
            $existing = RideTransaction::where('booking_id', $booking->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            // ── Verify booking is completed ────────────────────────────────────
            if ($booking->status !== Booking::STATUS_COMPLETED) {
                throw new \RuntimeException(
                    "Cannot settle booking #{$booking->id}: status is '{$booking->status}', expected 'completed'."
                );
            }

            // ── Pull fare data from booking_fares ──────────────────────────────
            // FareCalculationService already synced this at booking creation and
            // at completion (if usage was updated). It is our source of truth.
            $fare = $booking->fare;

            // ── Build settlement amounts ───────────────────────────────────────
            [
                $baseFare,
                $distanceFare,
                $timeFare,
                $waitingFare,
                $extraCharges,
                $surgeAmount,
                $discountAmount,
                $subtotal,
                $taxRate,
                $taxType,
                $taxAmount,
                $commissionType,
                $commissionRate,
                $commissionAmount,
                $driverEarning,
                $finalAmount,
                $distanceKm,
                $durationMinutes,
                $waitingMinutes,
            ] = $this->extractAmounts($booking, $fare);

            // Platform earning = commission collected from driver
            $platformEarning = $commissionAmount;

            // Paid amount = what the user actually paid (final_amount on booking)
            $paidAmount = (float) ($booking->final_amount > 0
                ? $booking->final_amount
                : $finalAmount);

            // ── Create RideTransaction row ─────────────────────────────────────
            $transaction = RideTransaction::create([
                'transaction_ref'    => RideTransaction::generateUuid(), // temp; updated below
                'transaction_uuid'   => RideTransaction::generateUuid(),
                'booking_id'         => $booking->id,
                'user_id'            => $booking->user_id,
                'driver_id'          => $booking->driver_id,
                'vehicle_category_id'=> $booking->vehicle_category_id,
                'transaction_type'   => RideTransaction::TYPE_RIDE_PAYMENT,
                'transaction_status' => RideTransaction::STATUS_COMPLETED,
                'payment_method'     => $booking->payment_method ?? 'cash',
                'payment_status'     => $booking->payment_status === 'paid'
                    ? RideTransaction::PAYMENT_PAID
                    : RideTransaction::PAYMENT_PENDING,
                'currency'           => 'INR',
                // Fare breakdown snapshot
                'base_fare'          => $baseFare,
                'distance_fare'      => $distanceFare,
                'time_fare'          => $timeFare,
                'waiting_fare'       => $waitingFare,
                'extra_charges'      => $extraCharges,
                'surge_amount'       => $surgeAmount,
                'discount_amount'    => $discountAmount,
                'subtotal'           => $subtotal,
                // Tax snapshot (immutable historical record)
                'tax_type'           => $taxType,
                'tax_rate'           => $taxRate,
                'tax_amount'         => $taxAmount,
                // Commission snapshot (immutable historical record)
                'commission_type'    => $commissionType,
                'commission_rate'    => $commissionRate,
                'commission_amount'  => $commissionAmount,
                // Settlement
                'driver_earning'     => $driverEarning,
                'platform_earning'   => $platformEarning,
                'final_amount'       => $finalAmount,
                'paid_amount'        => $paidAmount,
                'refund_amount'      => 0,
                // Trip metrics snapshot
                'distance_km'        => $distanceKm,
                'duration_minutes'   => $durationMinutes,
                'waiting_minutes'    => $waitingMinutes,
                'settled_at'         => now(),
            ]);

            // ── Update transaction_ref to use the real DB ID ───────────────────
            $transaction->transaction_ref = RideTransaction::generateRef($transaction->id);
            $transaction->save();

            // ── Credit driver wallet ───────────────────────────────────────────
            if ($booking->driver_id && $driverEarning > 0) {
                $this->creditDriverWallet($booking->driver, $driverEarning, $transaction);
            }

            Log::info('RideSettlementService: booking settled successfully', [
                'booking_id'         => $booking->id,
                'transaction_ref'    => $transaction->transaction_ref,
                'subtotal'           => $subtotal,
                'tax_amount'         => $taxAmount,
                'commission_amount'  => $commissionAmount,
                'driver_earning'     => $driverEarning,
                'platform_earning'   => $platformEarning,
                'final_amount'       => $finalAmount,
            ]);

            return $transaction;
        });
    }

    /**
     * Extract and normalize all financial amounts from the booking and its fare.
     * Falls back to zeros gracefully if fare is missing.
     *
     * @return array  Ordered list of scalar values for settlement
     */
    private function extractAmounts(Booking $booking, ?BookingFare $fare): array
    {
        if ($fare) {
            $baseFare        = (float) ($fare->base_fare        ?? 0);
            $distanceFare    = (float) ($fare->distance_charge  ?? 0);
            $timeFare        = (float) ($fare->time_charge      ?? 0);
            $waitingFare     = (float) ($fare->waiting_charge   ?? 0);
            $extraCharges    = (float) ($fare->extra_charge     ?? 0);
            $surgeAmount     = 0.0; // night_surge not separately stored; baked into subtotal
            $discountAmount  = (float) ($fare->discount         ?? 0);
            $subtotal        = (float) ($fare->subtotal         ?? 0);
            $taxRate         = (float) ($fare->tax_percentage   ?? 0);
            $taxType         = 'GST'; // Tax type label (configurable future extension)
            $taxAmount       = (float) ($fare->tax_amount       ?? 0);
            $commissionType  = $fare->commission_type  ?? 'percentage';
            $commissionRate  = (float) ($fare->commission_value  ?? 0);
            $commissionAmount= (float) ($fare->commission_amount ?? 0);
            $driverEarning   = (float) ($fare->driver_earnings  ?? 0);
            // finalAmount = user_total (subtotal + tax)
            $finalAmount     = (float) ($fare->user_total       ?? $subtotal + $taxAmount);
            $distanceKm      = (float) ($fare->distance_km      ?? 0);
            $durationMinutes = (float) ($fare->duration_minutes ?? 0);
            $waitingMinutes  = (float) ($fare->waiting_minutes  ?? 0);
        } else {
            // No fare record: use booking-level final_amount as best fallback
            $rawAmount      = (float) ($booking->final_amount > 0
                ? $booking->final_amount
                : $booking->estimated_amount);

            $baseFare        = $rawAmount;
            $distanceFare    = 0.0;
            $timeFare        = 0.0;
            $waitingFare     = 0.0;
            $extraCharges    = 0.0;
            $surgeAmount     = 0.0;
            $discountAmount  = 0.0;
            $subtotal        = $rawAmount;
            $taxRate         = 0.0;
            $taxType         = null;
            $taxAmount       = 0.0;
            $commissionType  = null;
            $commissionRate  = 0.0;
            $commissionAmount= 0.0;
            $driverEarning   = $rawAmount;
            $finalAmount     = $rawAmount;
            $distanceKm      = null;
            $durationMinutes = null;
            $waitingMinutes  = null;
        }

        // Round all monetary values to 2 decimal places
        return [
            round($baseFare, 2),
            round($distanceFare, 2),
            round($timeFare, 2),
            round($waitingFare, 2),
            round($extraCharges, 2),
            round($surgeAmount, 2),
            round($discountAmount, 2),
            round($subtotal, 2),
            round($taxRate, 2),
            $taxType,
            round($taxAmount, 2),
            $commissionType,
            round($commissionRate, 2),
            round($commissionAmount, 2),
            round($driverEarning, 2),
            round($finalAmount, 2),
            $distanceKm !== null ? round($distanceKm, 3) : null,
            $durationMinutes !== null ? round($durationMinutes, 2) : null,
            $waitingMinutes !== null ? round($waitingMinutes, 2) : null,
        ];
    }

    /**
     * Credit the driver's wallet_balance and record a WalletTransaction entry.
     *
     * @param  Driver|null     $driver
     * @param  float           $amount
     * @param  RideTransaction $transaction
     */
    private function creditDriverWallet(?Driver $driver, float $amount, RideTransaction $transaction): void
    {
        if (! $driver || $amount <= 0) {
            return;
        }

        // Lock the driver row to prevent race conditions on wallet_balance
        $driver = Driver::lockForUpdate()->findOrFail($driver->id);

        $driver->increment('wallet_balance', $amount);

        $bookingNo = $transaction->booking->booking_no ?? $transaction->booking_id;

        // Record the wallet movement for driver's transaction history
        WalletTransaction::create([
            'user_id'        => $driver->id,
            'user_type'      => Driver::class,
            'type'           => 'ride_earning',
            'amount'         => $amount,
            'reference_id'   => (string) $transaction->id,
            'reference_type' => RideTransaction::class,
            'description'    => "Ride earning for booking #{$bookingNo}",
        ]);

        Log::info('RideSettlementService: driver wallet credited', [
            'driver_id'      => $driver->id,
            'amount'         => $amount,
            'transaction_ref'=> $transaction->transaction_ref,
        ]);
    }

    /**
     * Build a clean financial summary array for API responses.
     * Used by booking completion APIs to return financial breakdown.
     *
     * @param  RideTransaction $txn
     * @return array
     */
    public function toApiResponse(RideTransaction $txn): array
    {
        return [
            'transaction_ref'  => $txn->transaction_ref,
            'transaction_uuid' => $txn->transaction_uuid,
            'currency'         => $txn->currency,
            'fare_breakdown'   => [
                'base_fare'      => (float) $txn->base_fare,
                'distance_fare'  => (float) $txn->distance_fare,
                'time_fare'      => (float) $txn->time_fare,
                'waiting_fare'   => (float) $txn->waiting_fare,
                'extra_charges'  => (float) $txn->extra_charges,
                'surge_amount'   => (float) $txn->surge_amount,
                'discount'       => (float) $txn->discount_amount,
                'subtotal'       => (float) $txn->subtotal,
            ],
            'tax'              => [
                'type'   => $txn->tax_type,
                'rate'   => (float) $txn->tax_rate,
                'amount' => (float) $txn->tax_amount,
            ],
            'commission'       => [
                'type'   => $txn->commission_type,
                'rate'   => (float) $txn->commission_rate,
                'amount' => (float) $txn->commission_amount,
            ],
            'settlement'       => [
                'gross_fare'      => (float) $txn->subtotal,
                'tax'             => (float) $txn->tax_amount,
                'commission'      => (float) $txn->commission_amount,
                'driver_earning'  => (float) $txn->driver_earning,
                'platform_earning'=> (float) $txn->platform_earning,
                'final_amount'    => (float) $txn->final_amount,
                'paid_amount'     => (float) $txn->paid_amount,
            ],
            'payment'          => [
                'method' => $txn->payment_method,
                'status' => $txn->payment_status,
            ],
        ];
    }
}
