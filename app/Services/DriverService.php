<?php

namespace App\Services;

use App\Helpers\GeneralHelper;
use App\Models\Driver;
use App\Repositories\DriverRepository;
use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redis;

class DriverService
{
    protected DriverRepository $driverRepository;

    public function __construct(DriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    /**
     * Find driver by mobile number
     *
     * @param string $mobile
     * @return \App\Models\Driver|null
     */
    public function findByMobile(string $mobile): ?Driver
    {
        return $this->driverRepository->findByMobile($mobile);
    }

    /**
     * Update driver details
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDriver(int $id, array $data): bool
    {
        return $this->driverRepository->update($id, $data);
    }

    /**
     * Finds a driver, generates and saves a login OTP.
     *
     * @param string $mobile
     * @return string The generated OTP.
     * @throws ModelNotFoundException if driver is not found.
     */
    public function sendLoginOtp(string $mobile): string
    {
        $driver = $this->findByMobile($mobile);

        if (!$driver) {
            throw new ModelNotFoundException();
        }

        $otp = GeneralHelper::OtpGenerator(true, 4);

        $this->updateDriver($driver->id, ['otp' => $otp]);

        return $otp;
    }

    /**
     * Verifies the login OTP for a driver and optionally updates FCM token.
     *
     * @param string $mobile
     * @param string $otp
     * @param string|null $fcmToken
     * @return Driver The authenticated driver model.
     * @throws ModelNotFoundException if driver is not found.
     * @throws Exception if OTP is invalid.
     */
    public function verifyLoginOtp(string $mobile, string $otp, ?string $fcmToken = null): Driver
    {
        $driver = $this->findByMobile($mobile);

        if (!$driver) {
            throw new ModelNotFoundException();
        }

        if ($driver->otp != $otp) {
            throw new Exception(__('string.common.invalid_otp'));
        }

        $updateData = ['otp' => null];
        if (!empty($fcmToken)) {
            $updateData['fcm_token'] = $fcmToken;
        }

        $this->updateDriver($driver->id, $updateData);

        return $driver->fresh();
    }

    public function findNearbyDrivers(
        float $longitude,
        float $latitude,
        int $radiusKm = 5
    ): array {

            $client = Redis::connection()->client();

            $result = $client->executeRaw([
                'GEOSEARCH',
                'driver_locations',
                'FROMLONLAT',
                (string) $longitude,
                (string) $latitude,
                'BYRADIUS',
                (string) $radiusKm,
                'km',
                'WITHDIST',
                'ASC',
            ]);

            dd($result);
        $results = [];

        foreach ($drivers as $driver) {
            $results[] = [
                'driver_id' => (int) $driver[0],
                'distance'  => (float) $driver[1],
            ];
        }

        return $results;
    }
}
