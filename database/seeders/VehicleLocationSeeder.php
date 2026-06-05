<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleLocationSeeder extends Seeder
{
    public function run(): void
    {
        $baseCoordinates = [
            'TN' => ['lat' => 13.0827, 'lng' => 80.2707],
            'KA' => ['lat' => 12.9716, 'lng' => 77.5946],
            'KL' => ['lat' => 9.9312, 'lng' => 76.2673],
            'AP' => ['lat' => 16.5062, 'lng' => 80.6480],
            'TS' => ['lat' => 17.3850, 'lng' => 78.4867],
        ];

        $now = now();

        Vehicle::query()
            ->get(['id', 'vehicle_number', 'status'])
            ->each(function (Vehicle $vehicle) use ($baseCoordinates, $now) {
                $stateCode = substr($vehicle->vehicle_number, 0, 2);
                $base = $baseCoordinates[$stateCode] ?? $baseCoordinates['TN'];
                $offsetSeed = $vehicle->id;
                $latOffset = (($offsetSeed % 9) - 4) * 0.0045;
                $lngOffset = (($offsetSeed % 11) - 5) * 0.0040;
                $isOnline = $vehicle->status === 'active';

                DB::table('vehicle_locations')->updateOrInsert(
                    ['vehicle_id' => $vehicle->id],
                    [
                        'latitude' => round($base['lat'] + $latOffset, 7),
                        'longitude' => round($base['lng'] + $lngOffset, 7),
                        'speed' => $isOnline ? round(20 + (($offsetSeed * 7) % 45), 2) : null,
                        'heading' => $isOnline ? (float) (($offsetSeed * 37) % 360) : null,
                        'accuracy' => round(4 + (($offsetSeed * 3) % 12) / 2, 2),
                        'is_online' => $isOnline,
                        'location_updated_at' => $now->copy()->subMinutes($offsetSeed % 15),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            });
    }
}
