<?php

namespace App\Repositories;

use App\Models\Driver;

class DriverRepository
{
    /**
     * Find driver by mobile number
     *
     * @param string $mobile
     * @return \App\Models\Driver|null
     */
    public function findByMobile(string $mobile)
    {
        return Driver::where('phone', $mobile)->first();
    }

    /**
     * Update driver details
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $driver = Driver::find($id);
        if ($driver) {
            foreach ($data as $key => $value) {
                $driver->{$key} = $value;
            }

            return $driver->save();
        }

        return false;
    }
}
