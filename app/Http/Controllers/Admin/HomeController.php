<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\RideTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // ── Today's financial summary (from ride_transactions ledger) ──────────
        $today = RideTransaction::completed()
            ->ridePayments()
            ->today()
            ->selectRaw('
                COUNT(*) as total_rides,
                COALESCE(SUM(subtotal), 0) as gross_revenue,
                COALESCE(SUM(tax_amount), 0) as tax_collected,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(driver_earning), 0) as driver_earnings,
                COALESCE(SUM(platform_earning), 0) as platform_earnings,
                COALESCE(SUM(final_amount), 0) as total_billed
            ')->first();

        // ── This month's financial summary ─────────────────────────────────────
        $thisMonth = RideTransaction::completed()
            ->ridePayments()
            ->thisMonth()
            ->selectRaw('
                COUNT(*) as total_rides,
                COALESCE(SUM(subtotal), 0) as gross_revenue,
                COALESCE(SUM(tax_amount), 0) as tax_collected,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(driver_earning), 0) as driver_earnings,
                COALESCE(SUM(platform_earning), 0) as platform_earnings,
                COALESCE(SUM(final_amount), 0) as total_billed
            ')->first();

        // ── Booking counts ─────────────────────────────────────────────────────
        $bookingStats = [
            'total'    => Booking::count(),
            'active'   => Booking::whereIn('status', Booking::ACTIVE_STATUSES)->count(),
            'today'    => Booking::whereDate('created_at', now()->toDateString())->count(),
        ];

        // ── User/Driver counts ─────────────────────────────────────────────────
        $totalUsers   = User::count();
        $totalDrivers = Driver::where('status', 'active')->count();

        // ── Revenue trend — last 14 days ───────────────────────────────────────
        $revenueTrend = RideTransaction::completed()
            ->ridePayments()
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as rides,
                COALESCE(SUM(subtotal), 0) as gross_revenue,
                COALESCE(SUM(commission_amount), 0) as commission,
                COALESCE(SUM(driver_earning), 0) as driver_earnings,
                COALESCE(SUM(tax_amount), 0) as tax_collected
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Payment method breakdown ───────────────────────────────────────────
        $paymentMethods = RideTransaction::completed()
            ->ridePayments()
            ->thisMonth()
            ->selectRaw('
                payment_method,
                COUNT(*) as rides,
                COALESCE(SUM(final_amount), 0) as total_amount
            ')
            ->groupBy('payment_method')
            ->get();

        // ── Recent transactions ────────────────────────────────────────────────
        $recentTransactions = RideTransaction::with([
            'booking:id,booking_no',
            'user:id,name,mobile',
            'driver:id,name,phone',
        ])
        ->latest()
        ->take(10)
        ->get();

        return view('admin.home', compact(
            'today',
            'thisMonth',
            'bookingStats',
            'totalUsers',
            'totalDrivers',
            'revenueTrend',
            'paymentMethods',
            'recentTransactions'
        ));
    }
}
