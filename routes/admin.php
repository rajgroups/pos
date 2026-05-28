<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
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
    // });

});
