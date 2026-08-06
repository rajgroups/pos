<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RideController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletRechargeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/',[HomeController::class,'index'])->name('index');
Route::get('/admin/empty',function(){return view('admin.empty.empty');})->name('home');


// Route::prefix('admin')->middleware(['auth','admin'])->name('.admin')->group(function(){

// });

Route::prefix('admin')->name('admin.')->group(function(){
    // Authentication Routes
    Route::get('login',[AuthController::class,'adminLoginForm'])->name('login.form');
    Route::get('verify-otp',[AuthController::class,'adminVerifyOtpForm'])->name('login.otp.form');

    // Route::middleware(['admin'])->group( function () {

       Route::get('/dashboard',[HomeController::class,'index'])->name('home');
        // For Category Managment Routes
        Route::resource('category',CategoryController::class);

        // For User Management Routes
        Route::resource('users', UserController::class);

        // For Drivers Management Routes
        Route::resource('drivers', DriverController::class);

        // For Wallet Recharge Requests Management Routes
        Route::prefix('recharge-requests')->name('recharge-requests.')->group(function () {
            Route::get('/', [WalletRechargeController::class, 'index'])->name('index');
            Route::post('/{id}/approve', [WalletRechargeController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [WalletRechargeController::class, 'reject'])->name('reject');
        });


        // For Admin Management Routes
        Route::resource('admin', AdminController::class);


        // For Admin Management Routes
        // Route::resource('ride', AdminController::class);
        Route::prefix('ride')->name('ride.')->group(function () {
            Route::get('active', [RideController::class, 'active'])->name('active');
            Route::get('completed', [RideController::class, 'completed'])->name('complete');
            Route::get('cancelled', [RideController::class, 'cancelled'])->name('cancelled');

        });
    // });

});
