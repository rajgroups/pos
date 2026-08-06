<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletRechargeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletRechargeController extends Controller
{
    /**
     * Display a listing of wallet recharge requests.
     */
    public function index()
    {
        $requests = WalletRechargeRequest::with('driver')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.recharge_requests.index', compact('requests'));
    }

    /**
     * Approve a recharge request and add the amount to driver's wallet_balance.
     */
    public function approve(Request $request, $id)
    {
        $rechargeRequest = WalletRechargeRequest::findOrFail($id);

        if ($rechargeRequest->status === 'approved') {
            return redirect()->back()->with('info', 'This request is already approved.');
        }

        DB::transaction(function () use ($rechargeRequest, $request) {
            $rechargeRequest->status = 'approved';
            $rechargeRequest->admin_remarks = $request->input('admin_remarks', 'Approved by admin');
            $rechargeRequest->approved_at = now();
            $rechargeRequest->save();

            $driver = $rechargeRequest->driver;
            if ($driver) {
                $driver->wallet_balance = (float) ($driver->wallet_balance ?? 0) + (float) $rechargeRequest->amount;
                $driver->save();
            }
        });

        return redirect()->back()->with('success', 'Recharge request approved and amount added to driver wallet.');
    }

    /**
     * Reject a recharge request.
     */
    public function reject(Request $request, $id)
    {
        $rechargeRequest = WalletRechargeRequest::findOrFail($id);

        if ($rechargeRequest->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot reject an already approved request.');
        }

        $rechargeRequest->status = 'rejected';
        $rechargeRequest->admin_remarks = $request->input('admin_remarks', 'Rejected by admin');
        $rechargeRequest->save();

        return redirect()->back()->with('success', 'Recharge request rejected.');
    }
}
