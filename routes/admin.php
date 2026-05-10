<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\VariantAttributeController;
use App\Http\Controllers\admin\WarrantyController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\WarehouseController;
use App\Http\Controllers\admin\StoreController;
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
Route::get('/admin',[HomeController::class,'index'])->name('home');

// Route::prefix('admin')->middleware(['auth','admin'])->name('.admin')->group(function(){

// });

Route::prefix('admin')->name('admin.')->group(function(){
    // Authentication Routes
    Route::get('login',[AuthController::class,'adminLoginForm'])->name('login.form');
    Route::get('verify-otp',[AuthController::class,'adminVerifyOtpForm'])->name('login.otp.form');

    // For Category Managment Routes
    Route::resource('category',CategoryController::class);

});






