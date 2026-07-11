<?php

namespace App\Services\Socket;

use Illuminate\Support\Facades\Redis;

class DriverPresenceStore
{
    public function setDriverConnection(int $driverId, int $fd): void
    {
        Redis::set($this->driverFdKey($driverId), $fd);
    }

    public function setUserConnection(int $userId, int $fd): void
    {
        Redis::set($this->userFdKey($userId), $fd);
    }

    public function forgetDriver(int $driverId): void
    {
        Redis::del($this->driverFdKey($driverId));
        Redis::zrem($this->driverGeoKey(), (string) $driverId);
        Redis::del($this->driverLocationKey($driverId));
    }

    public function forgetUser(int $userId): void
    {
        Redis::del($this->userFdKey($userId));
    }

    public function updateDriverLocation(int $driverId, float $latitude, float $longitude): void
    {
        Redis::set($this->driverLocationKey($driverId), json_encode([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'updated_at' => now()->toIso8601String(),
        ]));

        Redis::connection()->client()->executeRaw([
            'GEOADD',
            $this->driverGeoKey(),
            (string) $longitude,
            (string) $latitude,
            (string) $driverId,
        ]);
    }

    public function getDriverFd(int $driverId): ?int
    {
        $fd = Redis::get($this->driverFdKey($driverId));

        return is_numeric($fd) ? (int) $fd : null;
    }

    public function getUserFd(int $userId): ?int
    {
        $fd = Redis::get($this->userFdKey($userId));

        return is_numeric($fd) ? (int) $fd : null;
    }

    public function findNearbyDriverIds(
        float $latitude,
        float $longitude,
        float $radiusKm = 5
    ): array {
        $client = Redis::connection()->client();

        $results = $client->executeRaw([
            'GEOSEARCH',
            $this->driverGeoKey(),
            'FROMLONLAT',
            (string) $longitude,
            (string) $latitude,
            'BYRADIUS',
            (string) $radiusKm,
            'km',
            'WITHDIST',
            'ASC',
        ]);

        if (! is_array($results)) {
            return [];
        }

        return array_values(array_unique(array_map(
            fn ($row) => (int) ($row[0] ?? 0),
            array_filter($results)
        )));
    }

    protected function driverFdKey(int $driverId): string
    {
        return "driver:fd:{$driverId}";
    }

    protected function userFdKey(int $userId): string
    {
        return "user:fd:{$userId}";
    }

    protected function driverLocationKey(int $driverId): string
    {
        return "driver:location:{$driverId}";
    }

    protected function driverGeoKey(): string
    {
        return 'driver_locations';
    }
}
