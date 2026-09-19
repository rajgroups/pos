<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct(protected ReferralService $referralService)
    {
    }

    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalInvites = Referral::where('referrer_id', $user->id)->where('referrer_type', get_class($user))->count();
        $pending = Referral::where('referrer_id', $user->id)->where('referrer_type', get_class($user))->whereIn('status', ['PENDING', 'REGISTERED'])->count();
        $rewarded = Referral::where('referrer_id', $user->id)->where('referrer_type', get_class($user))->where('status', 'REWARDED')->count();
        $totalEarned = Referral::where('referrer_id', $user->id)->where('referrer_type', get_class($user))->where('status', 'REWARDED')->sum('reward_amount');

        return ApiResponseHelper::success('Referral summary fetched.', [
            'total_invites' => $totalInvites,
            'pending_referrals' => $pending,
            'successful_referrals' => $rewarded,
            'total_earned' => (float) $totalEarned,
        ]);
    }

    public function code(Request $request): JsonResponse
    {
        $user = $request->user();
        $code = $this->referralService->getUserReferralCode($user);
        
        $link = url("/ref?code={$code}");
        $message = AppSetting::get('user_referral_share_message', "🚕 Join Indicab! Book cars, bikes, jeeps and more with Indicab.\n\nUse my referral code: {$code}\n\nDownload Indicab User App:\n{$link}");
        
        return ApiResponseHelper::success('Referral code fetched.', [
            'referral_code' => $code,
            'referral_link' => $link,
            'share_message' => $message,
        ]);
    }

    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'referral_code' => 'required|string'
        ]);

        $result = $this->referralService->validateCode($request->referral_code, $request->user());

        if (!$result['valid']) {
            return ApiResponseHelper::error($result['message']);
        }

        return ApiResponseHelper::success('Valid referral code.', [
            'valid' => true,
            'referrer' => [
                'name' => $result['referrer']->name ?? $result['referrer']->mobile ?? 'User'
            ]
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $referrals = Referral::where('referrer_id', $user->id)
            ->where('referrer_type', get_class($user))
            ->with(['referred'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'status' => true,
            'message' => 'Referral history fetched.',
            'data' => $referrals->items(),
            'meta' => [
                'current_page' => $referrals->currentPage(),
                'last_page' => $referrals->lastPage(),
                'total' => $referrals->total(),
            ]
        ]);
    }
}
