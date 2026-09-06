<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class IndicabModeService
{
    public const MODE_ECONOMY = 'economy';
    public const MODE_PRIME = 'prime';

    protected const CACHE_KEY = 'indicab_communication_mode';

    /**
     * Get the current active mode.
     * Defaults to economy for safety.
     *
     * @return string
     */
    public function getMode(): string
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $mode = AppSetting::get('indicab_mode', self::MODE_ECONOMY);
            return in_array($mode, [self::MODE_ECONOMY, self::MODE_PRIME], true) ? $mode : self::MODE_ECONOMY;
        });
    }

    /**
     * Check if the application is currently running in Economy mode.
     *
     * @return bool
     */
    public function isEconomy(): bool
    {
        return $this->getMode() === self::MODE_ECONOMY;
    }

    /**
     * Check if the application is currently running in Prime mode.
     *
     * @return bool
     */
    public function isPrime(): bool
    {
        return $this->getMode() === self::MODE_PRIME;
    }

    /**
     * Set the application mode dynamically.
     *
     * @param string $mode
     * @return bool
     */
    public function setMode(string $mode): bool
    {
        if (!in_array($mode, [self::MODE_ECONOMY, self::MODE_PRIME], true)) {
            return false;
        }

        AppSetting::set('indicab_mode', $mode, 'system');
        
        // Clear cache so the new mode is applied immediately on next request
        Cache::forget(self::CACHE_KEY);

        return true;
    }
}
