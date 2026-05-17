<?php

use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\VehicleTypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/send-otp', [UserAuthController::class, 'sendOtp'])->name('api.user.sendOtp');
Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp'])->name('api.user.verifyOtp');
Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('api.user.vehicleTypes.index');

// User API CRUD routes (These will be accessible at: /api/user/users)
Route::apiResource('users', UserController::class);
