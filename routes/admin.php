<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\RideController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletRechargeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\DriverDocumentController;
use App\Http\Controllers\Admin\DriverVehicleAssignmentController;
use App\Http\Controllers\Admin\VehicleCategoryPricingController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleDocumentController;
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
    Route::post('login',[AuthController::class,'adminLoginSubmit'])->name('login.submit');
    Route::get('verify-otp',[AuthController::class,'adminVerifyOtpForm'])->name('login.otp.form');

    // Route::middleware(['admin'])->group( function () {

       Route::get('/dashboard',[HomeController::class,'index'])->name('home');
        // For Category Managment Routes
        Route::resource('category',CategoryController::class);

        // For Brand Management Routes
        Route::resource('brand', BrandController::class);

        // For Document Type Management Routes
        Route::resource('document-types', DocumentTypeController::class);

        // For User Management Routes
        Route::resource('users', UserController::class);

        // For Drivers Management Routes
        Route::resource('drivers', DriverController::class);
        Route::post('drivers/{driver}/toggle-status', [DriverController::class, 'toggleStatus'])->name('drivers.toggle-status');
        Route::post('drivers/{driver}/toggle-verify', [DriverController::class, 'toggleVerify'])->name('drivers.toggle-verify');
        Route::resource('enquiries', \App\Http\Controllers\Admin\EnquiryController::class)->only(['index', 'destroy']);

        // CMS Management
        Route::resource('cms-pages', \App\Http\Controllers\Admin\CmsPageController::class);

        // For Vehicles Management Routes
        Route::get('vehicles/{vehicle}/location', [VehicleController::class, 'location'])->name('vehicles.location');
        Route::resource('vehicles', VehicleController::class);

        // For Documents Management Routes
        Route::resource('driver-documents', DriverDocumentController::class);
        Route::resource('vehicle-documents', VehicleDocumentController::class);

        // For Assignments & Pricing Management Routes

        Route::resource('vehicle-pricing', VehicleCategoryPricingController::class);

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
            Route::get('all', [RideController::class, 'index'])->name('index');
            Route::get('upcoming', [RideController::class, 'upcoming'])->name('upcoming');
            Route::get('active', [RideController::class, 'active'])->name('active');
            Route::get('completed', [RideController::class, 'completed'])->name('complete');
            Route::get('cancelled', [RideController::class, 'cancelled'])->name('cancelled');
            Route::get('{id}', [RideController::class, 'show'])->name('show');
            Route::post('{id}/assign', [RideController::class, 'assignDriver'])->name('action.assign');
            Route::post('{id}/complete', [RideController::class, 'completeRide'])->name('action.complete');
            Route::post('{id}/cancel', [RideController::class, 'cancelRide'])->name('action.cancel');
        });
    // });

        // For SOS Alerts Management Routes
        Route::prefix('sos')->name('sos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SosAlertController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\SosAlertController::class, 'show'])->name('show');
            Route::post('/{id}/resolve', [\App\Http\Controllers\Admin\SosAlertController::class, 'resolve'])->name('resolve');
        });

        // For System Settings
        Route::get('/settings/mode', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'mode'])->name('settings.mode');
        Route::post('/settings/mode', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateMode'])->name('settings.mode.update');
        Route::get('/settings/app', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'appSettings'])->name('settings.app');
        Route::post('/settings/app', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateAppSettings'])->name('settings.app.update');

        // Google Maps Usage Dashboard
        Route::prefix('google-maps')->name('google-maps.')->group(function () {
            Route::get('/usage', [\App\Http\Controllers\Admin\GoogleMapsUsageController::class, 'index'])->name('usage');
            Route::get('/usage/summary', [\App\Http\Controllers\Admin\GoogleMapsUsageController::class, 'summary'])->name('usage.summary');
            Route::get('/usage/daily', [\App\Http\Controllers\Admin\GoogleMapsUsageController::class, 'daily'])->name('usage.daily');
            Route::get('/usage/apis', [\App\Http\Controllers\Admin\GoogleMapsUsageController::class, 'apis'])->name('usage.apis');
            Route::post('/usage/refresh', [\App\Http\Controllers\Admin\GoogleMapsUsageController::class, 'refresh'])->name('usage.refresh');
        });

});
