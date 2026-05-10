<?php

use App\Http\Controllers\Api\DriverAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [DriverAuthController::class, 'login'])->name('api.driver.login');
