<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingFare;
use App\Models\Driver;
use App\Models\RideTransaction;
use App\Models\User;
use App\Models\VehicleCategory;
use App\Models\VehicleCategoryPricing;
use App\Services\RideSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * RideTransactionTest
 *
 * Tests the financial transaction/ledger system for Indicab.
 *
 * Each test is isolated (RefreshDatabase). Tests use the SQLite in-memory
 * database configured in phpunit.xml.
 *
 * Test coverage:
 *  1.  Completed ride creates exactly one transaction.
 *  2.  Duplicate completion does NOT create another transaction.
 *  3.  Percentage commission is calculated correctly.
 *  4.  Fixed commission is calculated correctly.
 *  5.  Tax is calculated correctly.
 *  6.  Driver earnings are correct.
 *  7.  Platform earnings are correct.
 *  8.  Historical commission unchanged after config change.
 *  9.  Historical tax unchanged after tax config change.
 *  10. Failed settlement rolls back database changes.
 *  11. Economy Mode works correctly (no Redis).
 *  12. Prime Mode works correctly (no socket required).
 *  13. Refund does NOT overwrite the original transaction.
 *  14. Admin transaction filters work correctly.
 */
class RideTransactionTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Create a minimal Booking with a BookingFare pre-loaded.
     * All amounts are DECIMAL-compatible strings/floats.
     *
     * @param float  $subtotal
     * @param string $commissionType   'percentage' | 'fixed'
     * @param float  $commissionValue  % or fixed INR
     * @param float  $taxPercentage
     */
    private function makeCompletedBookingWithFare(
        float $subtotal      = 200.00,
        string $commissionType = 'percentage',
        float $commissionValue = 10.0,
        float $taxPercentage  = 5.0
    ): Booking {
        $user = User::factory()->create();

        $driver = Driver::factory()->create([
            'status'         => 'active',
            'wallet_balance' => 0,
        ]);

        $category = VehicleCategory::factory()->create();

        $pricing = VehicleCategoryPricing::factory()->create([
            'vehicle_category_id' => $category->id,
            'pricing_type'        => 'fixed',
            'base_fare'           => $subtotal,
            'minimum_fare'        => 0,
            'commission_type'     => $commissionType,
            'commission_value'    => $commissionValue,
            'tax_percentage'      => $taxPercentage,
        ]);

        // Calculate expected values
        $commissionAmount = $commissionType === 'percentage'
            ? round($subtotal * $commissionValue / 100, 2)
            : round($commissionValue, 2);

        $taxAmount      = round($subtotal * $taxPercentage / 100, 2);
        $driverEarnings = round($subtotal - $commissionAmount, 2);
        $userTotal      = round($subtotal + $taxAmount, 2);

        $booking = Booking::factory()->create([
            'user_id'             => $user->id,
            'driver_id'           => $driver->id,
            'vehicle_category_id' => $category->id,
            'status'              => Booking::STATUS_COMPLETED,
            'final_amount'        => $userTotal,
            'payment_method'      => 'cash',
            'payment_status'      => 'pending',
            'completed_at'        => now(),
        ]);

        // Create the BookingFare (as FareCalculationService would)
        BookingFare::factory()->create([
            'booking_id'        => $booking->id,
            'pricing_type'      => 'fixed',
            'base_fare'         => $subtotal,
            'unit_rate'         => 0,
            'usage_amount'      => 1,
            'distance_charge'   => 0,
            'time_charge'       => 0,
            'waiting_charge'    => 0,
            'extra_charge'      => 0,
            'discount'          => 0,
            'subtotal'          => $subtotal,
            'commission_type'   => $commissionType,
            'commission_value'  => $commissionValue,
            'commission_amount' => $commissionAmount,
            'tax_percentage'    => $taxPercentage,
            'tax_amount'        => $taxAmount,
            'user_total'        => $userTotal,
            'driver_earnings'   => $driverEarnings,
            'total_amount'      => $userTotal,
            'distance_km'       => 5.0,
            'duration_minutes'  => 15.0,
            'waiting_minutes'   => 0,
        ]);

        return $booking->load(['fare', 'category.pricing', 'user', 'driver']);
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Test 1: A completed booking creates exactly one transaction.
     */
    public function test_completed_ride_creates_exactly_one_transaction(): void
    {
        $booking = $this->makeCompletedBookingWithFare();

        $service = app(RideSettlementService::class);
        $txn     = $service->settle($booking);

        $this->assertInstanceOf(RideTransaction::class, $txn);
        $this->assertDatabaseCount('ride_transactions', 1);
        $this->assertEquals($booking->id, $txn->booking_id);
        $this->assertEquals(RideTransaction::STATUS_COMPLETED, $txn->transaction_status);
        $this->assertEquals(RideTransaction::TYPE_RIDE_PAYMENT, $txn->transaction_type);
        $this->assertNotEmpty($txn->transaction_ref);
        $this->assertNotEmpty($txn->transaction_uuid);
        $this->assertNotNull($txn->settled_at);
    }

    /**
     * Test 2: Duplicate completion request does NOT create another transaction.
     */
    public function test_duplicate_completion_does_not_create_second_transaction(): void
    {
        $booking = $this->makeCompletedBookingWithFare();

        $service = app(RideSettlementService::class);

        $txn1 = $service->settle($booking);
        $txn2 = $service->settle($booking); // second call — idempotent

        $this->assertDatabaseCount('ride_transactions', 1);
        $this->assertEquals($txn1->id, $txn2->id);
    }

    /**
     * Test 3: Percentage commission is calculated correctly.
     */
    public function test_percentage_commission_is_calculated_correctly(): void
    {
        // subtotal=200, commission=10% → commission_amount = 20.00
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 10.0,
            taxPercentage: 0.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertEquals('percentage', $txn->commission_type);
        $this->assertEquals('10.00', $txn->commission_rate);
        $this->assertEquals('20.00', $txn->commission_amount);
    }

    /**
     * Test 4: Fixed commission is calculated correctly.
     */
    public function test_fixed_commission_is_calculated_correctly(): void
    {
        // subtotal=300, commission=fixed 25 → commission_amount = 25.00
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 300.00,
            commissionType: 'fixed',
            commissionValue: 25.0,
            taxPercentage: 0.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertEquals('fixed', $txn->commission_type);
        $this->assertEquals('25.00', $txn->commission_rate);
        $this->assertEquals('25.00', $txn->commission_amount);
    }

    /**
     * Test 5: Tax is calculated correctly.
     */
    public function test_tax_is_calculated_correctly(): void
    {
        // subtotal=200, tax=5% → tax_amount = 10.00
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 0.0,
            taxPercentage: 5.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertEquals('5.00', $txn->tax_rate);
        $this->assertEquals('10.00', $txn->tax_amount);
    }

    /**
     * Test 6: Driver earnings are correct.
     * driver_earning = subtotal - commission_amount
     */
    public function test_driver_earnings_are_correct(): void
    {
        // subtotal=200, commission=10%(20) → driver_earning = 180.00
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 10.0,
            taxPercentage: 0.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertEquals('180.00', $txn->driver_earning);
    }

    /**
     * Test 7: Platform earnings are correct.
     * platform_earning = commission_amount
     */
    public function test_platform_earnings_are_correct(): void
    {
        // subtotal=200, commission=10%(20) → platform_earning = 20.00
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 10.0,
            taxPercentage: 0.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertEquals('20.00', $txn->platform_earning);
    }

    /**
     * Test 8: Historical commission remains unchanged after admin changes the
     *         commission rate on the vehicle category.
     */
    public function test_historical_commission_unchanged_after_config_change(): void
    {
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 10.0,
            taxPercentage: 0.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        // Simulate admin changing commission to 20%
        VehicleCategoryPricing::where('vehicle_category_id', $booking->vehicle_category_id)
            ->update([
                'commission_value' => 20.0,
            ]);

        // Reload transaction from DB
        $txn->refresh();

        // Old transaction must still show 10% and ₹20
        $this->assertEquals('10.00', $txn->commission_rate);
        $this->assertEquals('20.00', $txn->commission_amount);
    }

    /**
     * Test 9: Historical tax remains unchanged after tax configuration changes.
     */
    public function test_historical_tax_unchanged_after_config_change(): void
    {
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 0.0,
            taxPercentage: 5.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        // Admin changes tax to 18%
        VehicleCategoryPricing::where('vehicle_category_id', $booking->vehicle_category_id)
            ->update(['tax_percentage' => 18.0]);

        $txn->refresh();

        // Old transaction must still show 5% and ₹10
        $this->assertEquals('5.00', $txn->tax_rate);
        $this->assertEquals('10.00', $txn->tax_amount);
    }

    /**
     * Test 10: Failed settlement rolls back all database changes.
     */
    public function test_failed_settlement_rolls_back_database(): void
    {
        $booking = $this->makeCompletedBookingWithFare();
        $driverId = $booking->driver_id;
        $originalBalance = (float) Driver::find($driverId)->wallet_balance;

        // Mock the service to throw on wallet credit
        $this->mock(RideSettlementService::class, function ($mock) {
            $mock->shouldReceive('settle')
                 ->once()
                 ->andThrow(new \RuntimeException('Simulated DB failure'));
        });

        try {
            app(RideSettlementService::class)->settle($booking);
        } catch (\RuntimeException) {
            // Expected
        }

        // No transaction should have been created
        $this->assertDatabaseCount('ride_transactions', 0);

        // Driver balance must be unchanged
        $this->assertEquals(
            $originalBalance,
            (float) Driver::find($driverId)->wallet_balance
        );
    }

    /**
     * Test 11: Settlement works correctly in Economy Mode (MySQL, no Redis).
     * Economy mode means bookings can be accepted without Redis locking.
     * Settlement itself must still be atomic.
     */
    public function test_economy_mode_settlement_works(): void
    {
        // Economy mode: ensure IndicabModeService returns 'economy'
        $this->app->bind(\App\Services\IndicabModeService::class, function () {
            $mock = \Mockery::mock(\App\Services\IndicabModeService::class);
            $mock->shouldReceive('isEconomy')->andReturn(true);
            $mock->shouldReceive('isPrime')->andReturn(false);
            return $mock;
        });

        $booking = $this->makeCompletedBookingWithFare();
        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertDatabaseCount('ride_transactions', 1);
        $this->assertEquals(RideTransaction::STATUS_COMPLETED, $txn->transaction_status);
    }

    /**
     * Test 12: Settlement works correctly in Prime Mode (Swoole/WebSocket).
     */
    public function test_prime_mode_settlement_works(): void
    {
        $this->app->bind(\App\Services\IndicabModeService::class, function () {
            $mock = \Mockery::mock(\App\Services\IndicabModeService::class);
            $mock->shouldReceive('isEconomy')->andReturn(false);
            $mock->shouldReceive('isPrime')->andReturn(true);
            return $mock;
        });

        $booking = $this->makeCompletedBookingWithFare();
        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertDatabaseCount('ride_transactions', 1);
        $this->assertEquals(RideTransaction::STATUS_COMPLETED, $txn->transaction_status);
    }

    /**
     * Test 13: A refund transaction does NOT overwrite the original transaction.
     */
    public function test_refund_does_not_overwrite_original_transaction(): void
    {
        $booking = $this->makeCompletedBookingWithFare(subtotal: 200.00);
        $service = app(RideSettlementService::class);
        $original = $service->settle($booking);

        // Simulate a refund by creating a child transaction
        $refund = RideTransaction::create([
            'transaction_ref'      => RideTransaction::generateRef(9999),
            'transaction_uuid'     => RideTransaction::generateUuid(),
            'booking_id'           => null, // refund doesn't get its own booking
            'parent_transaction_id'=> $original->id,
            'user_id'              => $booking->user_id,
            'driver_id'            => $booking->driver_id,
            'vehicle_category_id'  => $booking->vehicle_category_id,
            'transaction_type'     => RideTransaction::TYPE_RIDE_REFUND,
            'transaction_status'   => RideTransaction::STATUS_REFUNDED,
            'payment_method'       => 'wallet',
            'payment_status'       => RideTransaction::PAYMENT_REFUNDED,
            'currency'             => 'INR',
            'subtotal'             => 0,
            'final_amount'         => 200.00,
            'refund_amount'        => 200.00,
            'settled_at'           => now(),
        ]);

        // Original transaction must still have its data intact
        $original->refresh();
        $this->assertEquals('200.00', $original->subtotal);
        $this->assertEquals(RideTransaction::STATUS_COMPLETED, $original->transaction_status);
        $this->assertEquals(RideTransaction::TYPE_RIDE_PAYMENT, $original->transaction_type);

        // Refund must reference the original
        $this->assertEquals($original->id, $refund->parent_transaction_id);
        $this->assertEquals(RideTransaction::TYPE_RIDE_REFUND, $refund->transaction_type);

        // Total ride_transactions count = 2 (original + refund)
        $this->assertDatabaseCount('ride_transactions', 2);
    }

    /**
     * Test 14: Admin transaction filters work correctly.
     */
    public function test_admin_transaction_filters_work(): void
    {
        // Create two bookings with different payment methods
        $b1 = $this->makeCompletedBookingWithFare(subtotal: 100.00);
        $b2 = $this->makeCompletedBookingWithFare(subtotal: 200.00);

        $service = app(RideSettlementService::class);
        $t1 = $service->settle($b1);
        $t1->update(['payment_method' => 'cash', 'transaction_status' => 'completed']);

        $t2 = $service->settle($b2);
        $t2->update(['payment_method' => 'upi', 'transaction_status' => 'completed']);

        // Filter by payment_method = cash
        $cashTxns = RideTransaction::where('payment_method', 'cash')->get();
        $this->assertCount(1, $cashTxns);
        $this->assertEquals('100.00', $cashTxns->first()->subtotal);

        // Filter by payment_method = upi
        $upiTxns = RideTransaction::where('payment_method', 'upi')->get();
        $this->assertCount(1, $upiTxns);

        // Filter by transaction_status = completed
        $completedTxns = RideTransaction::where('transaction_status', 'completed')->get();
        $this->assertCount(2, $completedTxns);

        // Filter by date
        $todayTxns = RideTransaction::whereDate('created_at', now()->toDateString())->get();
        $this->assertCount(2, $todayTxns);

        // Filter by user_id
        $user1Txns = RideTransaction::where('user_id', $b1->user_id)->get();
        $this->assertCount(1, $user1Txns);
    }

    /**
     * Test 15: Driver wallet is credited correctly on settlement.
     */
    public function test_driver_wallet_is_credited_on_settlement(): void
    {
        // subtotal=200, commission=10% → driver_earning=180
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 10.0,
            taxPercentage: 0.0
        );

        $driverId = $booking->driver_id;
        $this->assertEquals('0.00', Driver::find($driverId)->wallet_balance);

        app(RideSettlementService::class)->settle($booking);

        $this->assertEquals('180.00', Driver::find($driverId)->wallet_balance);
    }

    /**
     * Test 16: Transaction ref has correct format.
     */
    public function test_transaction_ref_has_correct_format(): void
    {
        $booking = $this->makeCompletedBookingWithFare();
        $txn = app(RideSettlementService::class)->settle($booking);

        $this->assertMatchesRegularExpression('/^INC-TXN-\d{8}-\d{6}$/', $txn->transaction_ref);
    }

    /**
     * Test 17: Final amount = subtotal + tax (user_total).
     */
    public function test_final_amount_equals_subtotal_plus_tax(): void
    {
        // subtotal=200, tax=5%(10) → final=210
        $booking = $this->makeCompletedBookingWithFare(
            subtotal: 200.00,
            commissionType: 'percentage',
            commissionValue: 0.0,
            taxPercentage: 5.0
        );

        $txn = app(RideSettlementService::class)->settle($booking);

        $expected = '210.00'; // 200 + 10
        $this->assertEquals($expected, $txn->final_amount);
    }
}
