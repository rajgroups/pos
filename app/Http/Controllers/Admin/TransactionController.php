<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\RideTransaction;
use App\Models\User;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    /**
     * Admin Transaction History — paginated, filtered, searchable listing.
     */
    public function index(Request $request)
    {
        $query = RideTransaction::with([
            'booking:id,booking_no',
            'user:id,name,mobile',
            'driver:id,name,phone',
            'vehicleCategory:id,name',
        ]);

        // ── Search ─────────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_ref', 'like', "%{$search}%")
                  ->orWhereHas('booking', fn ($bq) =>
                      $bq->where('booking_no', 'like', "%{$search}%")
                  )
                  ->orWhereHas('user', fn ($uq) =>
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                  )
                  ->orWhereHas('driver', fn ($dq) =>
                      $dq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                  );
            });
        }

        // ── Filters ────────────────────────────────────────────────────────────
        if ($request->filled('transaction_status')) {
            $query->where('transaction_status', $request->transaction_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('vehicle_category_id')) {
            $query->where('vehicle_category_id', $request->vehicle_category_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // ── Date range filter ──────────────────────────────────────────────────
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // ── Sorting ────────────────────────────────────────────────────────────
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'highest_amount':
                $query->orderByDesc('final_amount');
                break;
            case 'highest_commission':
                $query->orderByDesc('commission_amount');
                break;
            default:
                $query->latest();
                break;
        }

        $transactions   = $query->paginate(20)->withQueryString();
        $vehicleCategories = VehicleCategory::orderBy('name')->get(['id', 'name']);

        // ── Summary totals for the current filtered result ─────────────────────
        $summaryQuery = RideTransaction::where('transaction_status', RideTransaction::STATUS_COMPLETED)
            ->where('transaction_type', RideTransaction::TYPE_RIDE_PAYMENT);

        // Apply same filters to summary
        if ($request->filled('from_date')) {
            $summaryQuery->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $summaryQuery->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('vehicle_category_id')) {
            $summaryQuery->where('vehicle_category_id', $request->vehicle_category_id);
        }

        $summary = $summaryQuery->selectRaw('
            COUNT(*) as total_rides,
            COALESCE(SUM(subtotal), 0) as total_gross,
            COALESCE(SUM(tax_amount), 0) as total_tax,
            COALESCE(SUM(commission_amount), 0) as total_commission,
            COALESCE(SUM(driver_earning), 0) as total_driver_earning,
            COALESCE(SUM(platform_earning), 0) as total_platform_earning,
            COALESCE(SUM(final_amount), 0) as total_final_amount
        ')->first();

        return view('admin.transactions.index', compact(
            'transactions',
            'vehicleCategories',
            'summary'
        ));
    }

    /**
     * Admin Transaction Detail — full financial breakdown for one transaction.
     */
    public function show(RideTransaction $transaction)
    {
        $transaction->load([
            'booking.pickupLocation',
            'booking.dropLocation',
            'booking.usage',
            'user',
            'driver',
            'vehicleCategory',
            'childTransactions',
        ]);

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Export transactions as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = RideTransaction::with([
            'booking:id,booking_no',
            'user:id,name,mobile',
            'driver:id,name,phone',
            'vehicleCategory:id,name',
        ]);

        // Apply same filters as index
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('transaction_status')) {
            $query->where('transaction_status', $request->transaction_status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('vehicle_category_id')) {
            $query->where('vehicle_category_id', $request->vehicle_category_id);
        }

        $filename = 'indicab_transactions_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, [
                'Transaction ID',
                'Booking ID',
                'Date',
                'User',
                'User Phone',
                'Driver',
                'Driver Phone',
                'Vehicle Category',
                'Base Fare',
                'Distance Fare',
                'Time Fare',
                'Waiting Fare',
                'Extra Charges',
                'Discount',
                'Subtotal (Gross)',
                'Tax Type',
                'Tax Rate (%)',
                'Tax Amount',
                'Commission Type',
                'Commission Rate',
                'Commission Amount',
                'Driver Earning',
                'Platform Earning',
                'Final Amount',
                'Paid Amount',
                'Refund Amount',
                'Payment Method',
                'Payment Status',
                'Transaction Status',
                'Currency',
                'Distance (km)',
                'Duration (min)',
                'Settled At',
            ]);

            $query->chunk(500, function ($transactions) use ($handle) {
                foreach ($transactions as $txn) {
                    fputcsv($handle, [
                        $txn->transaction_ref,
                        $txn->booking?->booking_no,
                        $txn->created_at?->format('Y-m-d H:i:s'),
                        $txn->user?->name,
                        $txn->user?->mobile,
                        $txn->driver?->name,
                        $txn->driver?->phone,
                        $txn->vehicleCategory?->name,
                        $txn->base_fare,
                        $txn->distance_fare,
                        $txn->time_fare,
                        $txn->waiting_fare,
                        $txn->extra_charges,
                        $txn->discount_amount,
                        $txn->subtotal,
                        $txn->tax_type,
                        $txn->tax_rate,
                        $txn->tax_amount,
                        $txn->commission_type,
                        $txn->commission_rate,
                        $txn->commission_amount,
                        $txn->driver_earning,
                        $txn->platform_earning,
                        $txn->final_amount,
                        $txn->paid_amount,
                        $txn->refund_amount,
                        $txn->payment_method,
                        $txn->payment_status,
                        $txn->transaction_status,
                        $txn->currency,
                        $txn->distance_km,
                        $txn->duration_minutes,
                        $txn->settled_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Dashboard financial summary data (JSON for AJAX calls).
     */
    public function dashboardSummary(Request $request)
    {
        $today = RideTransaction::completed()
            ->ridePayments()
            ->today()
            ->selectRaw('
                COUNT(*) as rides,
                COALESCE(SUM(subtotal), 0) as gross_revenue,
                COALESCE(SUM(tax_amount), 0) as tax_collected,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(driver_earning), 0) as driver_earnings,
                COALESCE(SUM(platform_earning), 0) as platform_earnings
            ')->first();

        $thisMonth = RideTransaction::completed()
            ->ridePayments()
            ->thisMonth()
            ->selectRaw('
                COUNT(*) as rides,
                COALESCE(SUM(subtotal), 0) as gross_revenue,
                COALESCE(SUM(tax_amount), 0) as tax_collected,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(driver_earning), 0) as driver_earnings,
                COALESCE(SUM(platform_earning), 0) as platform_earnings
            ')->first();

        return response()->json([
            'today'      => $today,
            'this_month' => $thisMonth,
        ]);
    }
}
