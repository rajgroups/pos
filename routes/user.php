<?php

use App\Http\Controllers\Api\UserAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserAuthController::class, 'login'])->name('api.user.login');
