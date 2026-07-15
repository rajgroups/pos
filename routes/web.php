<?php

use App\Services\Socket\SocketServer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

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

include(base_path('routes/admin.php'));

Route::get('/all-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    //  Artisan::call('optimize');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    //  Artisan::call('optimize');
    return "All cleared successfully";
});
Route::get('/socket/connections', function (SocketServer $socket) {
    dd($socket);
    return response()->json([
        'connections' => $socket->connections,
        'drivers' => $socket->drivers,
        'users' => $socket->users,
    ], JSON_PRETTY_PRINT);
});
