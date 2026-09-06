<?php

namespace App\Repositories;

use App\Models\Driver;
use App\Interfaces\DriverInterface;

class DriverRepository implements DriverInterface
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
    public function update($id, array $data)
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

    public function all()
    {
        return Driver::latest()->paginate(10);
    }

    public function find($id)
    {
        return Driver::find($id);
    }

    public function create(array $data)
    {
        $driver = new Driver();
        $driver->fill($data);
        $driver->save();
        return $driver;
    }

    public function delete($id)
    {
        return Driver::where('id', $id)->delete();
    }
}
