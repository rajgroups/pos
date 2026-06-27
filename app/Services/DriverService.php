<?php

namespace App\Services;

use App\Helpers\GeneralHelper;
use App\Models\Driver;
use App\Repositories\DriverRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * Verifies the login OTP for a driver.
     *
     * @param string $mobile
     * @param string $otp
     * @return Driver The authenticated driver model.
     * @throws ModelNotFoundException if driver is not found.
     * @throws Exception if OTP is invalid.
     */
    public function verifyLoginOtp(string $mobile, string $otp): Driver
    {
        $driver = $this->findByMobile($mobile);

        if (!$driver) {
            throw new ModelNotFoundException();
        }

        if ($driver->otp != $otp) {
            throw new Exception(__('string.common.invalid_otp'));
        }

        $this->updateDriver($driver->id, ['otp' => null]);

        return $driver;
    }
}
