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

Route::get('/', function () {
    return response()->json(['name' => config('app.name'), 'status' => 'online']);
});

Route::get('/all-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    //  Artisan::call('optimize');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    //  Artisan::call('optimize');
    return "All cleared successfully";
});
Route::get('/debug/redis-driver-locations', function () {
    return [
        'driver1' => Redis::get('driver:location:1'),
        'driver2' => Redis::get('driver:location:2'),
    ];
});

Route::get('/socket/connections', function (SocketServer $socket) {
    dd($socket);
    return response()->json([
        'connections' => $socket->connections,
        'drivers' => $socket->drivers,
        'users' => $socket->users,
    ], JSON_PRETTY_PRINT);
});

Route::get('/debug/redis-driver-locations', function () {
    $keys = Redis::keys('driver:location:*');

    $data = [];

    foreach ($keys as $key) {
        $data[$key] = json_decode(Redis::get($key), true);
    }

    return response()->json($data, JSON_PRETTY_PRINT);
});


Route::get('/debug/drivers', function () {
    // dd('hi');
    $geoKey = 'driver_locations'; // Use your actual driverGeoKey()

    $driverIds = Redis::command('ZRANGE', [$geoKey, 0, -1]);

    $drivers = [];

    foreach ($driverIds as $driverId) {
        $location = json_decode(
            Redis::get("driver:location:{$driverId}"),
            true
        );

        $geo = Redis::command('GEOPOS', [$geoKey, $driverId]);

        $drivers[] = [
            'driver_id' => $driverId,
            'latitude' => $location['latitude'] ?? null,
            'longitude' => $location['longitude'] ?? null,
            'updated_at' => $location['updated_at'] ?? null,
            'geo_position' => $geo[0] ?? null,
        ];
    }

    if(empty($drivers)){
        // dd('j');
        return response()->json([], JSON_PRETTY_PRINT);
    }
    return response()->json($drivers, JSON_PRETTY_PRINT);
});
