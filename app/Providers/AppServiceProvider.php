<?php

namespace App\Providers;

use App\Services\Socket\DriverPresenceStore;
use App\Interfaces\SmsServiceInterface;
use App\Services\AndroidSmsGatewayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DriverPresenceStore::class);
        $this->app->singleton(SmsServiceInterface::class, AndroidSmsGatewayService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
