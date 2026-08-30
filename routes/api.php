<?php

use App\Http\Controllers\Api\SmsGatewayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/sms-gateway')->group(function () {
    Route::post('/register', [SmsGatewayController::class, 'register']);

    Route::middleware('auth.sms-gateway')->group(function () {
        Route::post('/heartbeat', [SmsGatewayController::class, 'heartbeat']);
        Route::get('/jobs', [SmsGatewayController::class, 'jobs']);
        Route::post('/jobs/{id}/result', [SmsGatewayController::class, 'reportResult']);
    });
});
