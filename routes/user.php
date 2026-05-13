<?php

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserAuthController::class, 'login'])->name('api.user.login');

// User API CRUD routes (These will be accessible at: /api/user/users)
Route::apiResource('users', UserController::class);
