<?php

use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\BookingController;
use App\Http\Controllers\Api\User\VehicleCategoryController;
use App\Http\Controllers\Api\User\VehicleController;
use App\Http\Controllers\Api\User\VehicleTypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.user.')->group(function () {
    Route::post('/send-otp', [UserAuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp'])->name('verifyOtp');

    Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('vehicleTypes.index');

    Route::prefix('vehicle-categories')->group(function () {
        Route::get('/', [VehicleCategoryController::class, 'index'])->name('vehicleCategories.index');
        Route::get('/{vehicleCategory}/pricing', [VehicleCategoryController::class, 'pricing'])->name('vehicleCategories.pricing');
    });

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');

    // User API CRUD routes (These will be accessible at: /api/user/users)
    Route::apiResource('users', UserController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::post('/fare-summary', [BookingController::class, 'fareSummary'])->name('fareSummary');
            Route::post('/', [BookingController::class, 'store'])->name('store');
            Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
            Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
            Route::get('/{booking}/fare', [BookingController::class, 'fare'])->name('fare');
        });
    });
});
