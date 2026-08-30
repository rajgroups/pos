<?php

use App\Http\Controllers\Api\AppUpdateController;
use App\Http\Controllers\Api\Driver\BookingController;
use App\Http\Controllers\Api\Driver\DriverAuthController;
use App\Http\Controllers\Api\Driver\WalletController;
use App\Http\Controllers\Api\EnquiryController;
use Illuminate\Support\Facades\Route;

Route::name('api.driver.')->group(function () {
    Route::post('/send-otp', [DriverAuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp'])->name('verifyOtp');
    Route::get('/check-update', [AppUpdateController::class, 'checkDriverApp'])->name('checkUpdate');
    Route::post('/partner-enquiry', [EnquiryController::class, 'store'])->name('partnerEnquiry');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/fcm-token', [DriverAuthController::class, 'updateFcmToken'])->name('updateFcmToken');
        Route::get('/dashboard', [BookingController::class, 'dashboard'])->name('dashboard');
        Route::post('/profile/online-status', [BookingController::class, 'toggleOnlineStatus'])->name('toggleOnlineStatus');
        Route::post('/wallet/recharge-request', [WalletController::class, 'requestRecharge'])->name('wallet.rechargeRequest');

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/check/active', [BookingController::class, 'activeRide'])->name('activeRide');
            Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
            Route::get('/{booking}/fare', [BookingController::class, 'fare'])->name('fare');
            Route::post('/{booking}/accept', [BookingController::class, 'accept'])->name('accept');
            Route::post('/{booking}/arrived', [BookingController::class, 'arrived'])->name('arrived');
            Route::post('/{booking}/start', [BookingController::class, 'start'])->name('start');
            Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        });
    });
});

