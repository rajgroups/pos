<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Referral;
use App\Models\User;
use App\Models\Driver;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Get or generate a referral code for a user or driver.
     */
    public function getUserReferralCode(Model $userOrDriver): string
    {
        if (empty($userOrDriver->referral_code)) {
            $userOrDriver->referral_code = $this->generateUniqueCode();
            $userOrDriver->save();
        }

        return $userOrDriver->referral_code;
    }

    /**
     * Generate a unique 8-character referral code.
     */
    protected function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists() || Driver::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Validate a referral code and check if the user is eligible to apply it.
     */
    public function validateCode(string $code, ?Model $registeringEntity = null): array
    {
        $isEnabled = AppSetting::get('referral_enabled', '1');
        if (!$isEnabled || $isEnabled === '0' || $isEnabled === 'false') {
            return ['valid' => false, 'message' => 'Referral system is currently disabled.'];
        }

        $referrer = User::where('referral_code', strtoupper($code))->first() 
                 ?? Driver::where('referral_code', strtoupper($code))->first();

        if (!$referrer) {
            return ['valid' => false, 'message' => 'Invalid referral code.'];
        }

        if ($registeringEntity && get_class($registeringEntity) === get_class($referrer) && $registeringEntity->id === $referrer->id) {
            return ['valid' => false, 'message' => 'You cannot refer yourself.'];
        }

        return [
            'valid' => true,
            'referrer' => $referrer
        ];
    }

    /**
     * Apply a referral code for a newly registered entity.
     */
    public function applyReferral(Model $newEntity, string $code): void
    {
        $validation = $this->validateCode($code, $newEntity);

        if (!$validation['valid']) {
            return;
        }

        $referrer = $validation['referrer'];

        // Check if already referred (handles either referred_by or referred_by_id depending on model)
        $referredById = $newEntity instanceof User ? $newEntity->referred_by : $newEntity->referred_by_id;
        
        if (Referral::where('referred_id', $newEntity->id)->where('referred_type', get_class($newEntity))->exists() || $referredById) {
            return; 
        }

        DB::transaction(function () use ($referrer, $newEntity) {
            if ($newEntity instanceof User) {
                $newEntity->referred_by = $referrer->id;
                $newEntity->referred_by_type = get_class($referrer);
            } else {
                $newEntity->referred_by_id = $referrer->id;
                $newEntity->referred_by_type = get_class($referrer);
            }
            $newEntity->save();

            Referral::create([
                'referrer_id' => $referrer->id,
                'referrer_type' => get_class($referrer),
                'referred_id' => $newEntity->id,
                'referred_type' => get_class($newEntity),
                'referral_code' => $referrer->referral_code,
                'status' => 'REGISTERED',
                'reward_amount' => 0,
            ]);
        });
    }

    /**
     * Qualify and reward the referrer.
     */
    public function qualifyReferral(Model $referredEntity): void
    {
        $isEnabled = AppSetting::get('referral_enabled', '1');
        if (!$isEnabled || $isEnabled === '0' || $isEnabled === 'false') {
            return;
        }

        DB::transaction(function () use ($referredEntity) {
            $referral = Referral::where('referred_id', $referredEntity->id)
                ->where('referred_type', get_class($referredEntity))
                ->where('status', 'REGISTERED')
                ->lockForUpdate()
                ->first();

            if (!$referral) {
                return;
            }

            $referral->status = 'QUALIFIED';
            $referral->qualified_at = now();
            $referral->save();

            $referrerReward = (float) AppSetting::get('referrer_reward', '0');
            $referredReward = (float) AppSetting::get('referred_reward', '0');

            if ($referrerReward > 0) {
                $this->creditWallet($referral->referrer_id, $referral->referrer_type, $referrerReward, 'REFERRAL_REWARD', $referral->id, Referral::class, "Referral reward for inviting " . class_basename($referredEntity) . " #{$referredEntity->id}");
            }

            if ($referredReward > 0) {
                $this->creditWallet($referral->referred_id, $referral->referred_type, $referredReward, 'REFERRAL_REWARD', $referral->id, Referral::class, "Welcome referral reward");
            }

            $referral->reward_amount = $referrerReward;
            $referral->status = 'REWARDED';
            $referral->rewarded_at = now();
            $referral->save();
        });
    }

    protected function creditWallet(int $userId, string $userType, float $amount, string $type, ?string $refId = null, ?string $refType = null, ?string $desc = null): void
    {
        $modelClass = $userType;
        $user = $modelClass::whereKey($userId)->lockForUpdate()->first();
        if (!$user) return;

        $user->wallet_balance = (float) ($user->wallet_balance ?? 0) + $amount;
        $user->save();

        WalletTransaction::create([
            'user_id' => $user->id,
            'user_type' => $userType,
            'type' => $type,
            'amount' => $amount,
            'reference_id' => $refId,
            'reference_type' => $refType,
            'description' => $desc,
        ]);
    }
}
