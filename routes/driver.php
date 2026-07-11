<?php

use App\Http\Controllers\Api\Driver\DriverAuthController;
use App\Http\Controllers\Api\Driver\BookingController;
use Illuminate\Support\Facades\Route;

Route::name('api.driver.')->group(function () {
    Route::post('/send-otp', [DriverAuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('verifyOtp');

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::post('/{booking}/accept', [BookingController::class, 'accept'])->name('accept');
            Route::post('/{booking}/start', [BookingController::class, 'start'])->name('start');
            Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        });
    });
});
