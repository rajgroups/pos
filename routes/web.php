<?php

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

// language
Route::get('/redis-clear', function () {
    Redis::flushdb(); // Clears only current database

    return response()->json(['message' => 'Current database cleared']);
});

Route::get('/redis-view', function () {
    $keys = Redis::keys('*');
    $data = [];

    foreach ($keys as $key) {
        $type = Redis::type($key);
        $ttl = Redis::ttl($key);

        switch ($type) {
            case 'string':
                $value = Redis::get($key);
                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => json_decode($value, true) ?? $value
                ];
                break;

            case 'hash':
                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => Redis::hGetAll($key)
                ];
                break;

            case 'list':
                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => Redis::lRange($key, 0, -1)
                ];
                break;

            case 'set':
                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => Redis::sMembers($key)
                ];
                break;

            case 'zset':
                $members = Redis::zRange($key, 0, -1);
                $parsedValue = [];

                foreach ($members as $member) {
                    // Check if it's geo data (has coordinates)
                    $coord = Redis::geopos($key, $member);
                    if ($coord[0][0] !== null) {
                        $parsedValue[$member] = [
                            'score' => Redis::zScore($key, $member),
                            'latitude' => $coord[0][1],
                            'longitude' => $coord[0][0]
                        ];
                    } else {
                        $parsedValue[$member] = Redis::zScore($key, $member);
                    }
                }

                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => $parsedValue
                ];
                break;

            case 'none':
                // Try to get raw value anyway
                $rawValue = Redis::get($key);
                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => $rawValue ?: 'Key exists but expired or empty',
                    'raw_attempt' => $rawValue
                ];
                break;

            default:
                $data[$key] = [
                    'type' => $type,
                    'ttl' => $ttl,
                    'value' => "Unsupported type: {$type}"
                ];
                break;
        }
    }

    return response()->json([
        'total_keys' => count($keys),
        'data' => $data
    ]);
});
Route::get('/drivers', function () {
    $drivers = Redis::zrange('drivers', 0, -1);

    $locations = [];
    foreach ($drivers as $driverId) {
        $coord = Redis::geopos('drivers', $driverId);
        $locations[] = [
            'driver_id' => $driverId,
            'latitude' => $coord[0][1],
            'longitude' => $coord[0][0]
        ];
    }

    return response()->json($locations);
});
Route::get('/debug-notyf', function () {
    notyf()->addSuccess('This should show now!');
    return view('debug'); // a test view
});

