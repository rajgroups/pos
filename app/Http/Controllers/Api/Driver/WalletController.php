<?php

namespace App\Http\Controllers\Api\Driver;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\WalletRechargeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Submit a wallet recharge request.
     * Amount is NOT added to driver wallet balance until admin approves it.
     */
    public function requestRecharge(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error('Unauthorized.', null, 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed.', $validator->errors(), 422);
        }

        $rechargeRequest = WalletRechargeRequest::create([
            'driver_id' => $driver->id,
            'amount' => $request->input('amount'),
            'payment_method' => $request->input('payment_method', 'online'),
            'transaction_id' => $request->input('transaction_id'),
            'status' => 'pending',
        ]);

        return ApiResponseHelper::success(
            'Wallet recharge request submitted successfully. Pending admin approval.',
            [
                'id' => $rechargeRequest->id,
                'amount' => (float) $rechargeRequest->amount,
                'status' => $rechargeRequest->status,
                'created_at' => $rechargeRequest->created_at->toIso8601String(),
            ]
        );
    }
}
