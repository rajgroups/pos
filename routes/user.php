<?php

use App\Http\Controllers\Api\AppUpdateController;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\BookingController;
use App\Http\Controllers\Api\User\HelpController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\User\SosController;
use App\Http\Controllers\Api\User\VehicleCategoryController;
use App\Http\Controllers\Api\User\VehicleController;
use App\Http\Controllers\Api\User\VehicleTypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.user.')->group(function () {
    Route::post('/send-otp', [UserAuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp'])->name('verifyOtp');
    Route::get('/check-update', [AppUpdateController::class, 'checkUserApp'])->name('checkUpdate');

    Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('vehicleTypes.index');

    Route::prefix('vehicle-categories')->group(function () {
        Route::get('/', [VehicleCategoryController::class, 'index'])->name('vehicleCategories.index');
        Route::get('/{vehicleCategory}/pricing', [VehicleCategoryController::class, 'pricing'])->name('vehicleCategories.pricing');
    });

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/faqs', [HelpController::class, 'faqs'])->name('faqs');

    // User API CRUD routes (These will be accessible at: /api/user/users)
    Route::apiResource('users', UserController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
        Route::post('/fcm-token', [UserAuthController::class, 'updateFcmToken'])->name('updateFcmToken');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.updatePost');

        Route::get('/support/tickets', [HelpController::class, 'tickets'])->name('support.tickets');
        Route::post('/support/tickets', [HelpController::class, 'createTicket'])->name('support.createTicket');

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::post('/fare-summary', [BookingController::class, 'fareSummary'])->name('fareSummary');
            Route::post('/', [BookingController::class, 'store'])->name('store');
            Route::get('/check/active', [BookingController::class, 'activeRide'])->name('activeRide');
            Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
            Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
            Route::post('/{booking}/retry', [BookingController::class, 'retry'])->name('retry');
            Route::get('/{booking}/fare', [BookingController::class, 'fare'])->name('fare');

            // SOS routes
            Route::post('/{booking}/sos', [SosController::class, 'store'])->name('sos.store');
            Route::get('/{booking}/sos', [SosController::class, 'index'])->name('sos.index');

            // Review routes
            Route::post('/{booking}/review', [ReviewController::class, 'store'])->name('review.store');
            Route::get('/{booking}/review', [ReviewController::class, 'show'])->name('review.show');
        });
    });
});
