<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll(int $perPage = 10)
    {
        return User::latest()->paginate($perPage);
    }

    public function findByMobile($mobile)
    {
        return User::select('mobile','id','name','mobile','otp')->where('mobile', $mobile)->first();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    /**
     * Update user data
     *
     * @param int $id
     * @param object $data
     *
     * @return \App\Models\User
     */
    public function update($id, object $data)
    {
        $user = $this->findById($id);

        foreach ($data as $key => $value) {
            $user->$key = $value;
        }

        $user->save();

        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);

        return $user->delete();
    }
}
