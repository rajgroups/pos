<?php

use App\Http\Controllers\admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\UnitController;
use App\Http\Controllers\admin\CategoryController;

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
    
    // For Unit Management Routes
    Route::resource('unit',UnitController::class);

    // For Category Managment Routes
    Route::resource('category',CategoryController::class);
});






