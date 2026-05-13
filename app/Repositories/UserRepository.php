<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll(int $perPage = 10)
    {
        return User::latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        $user->update($data);

        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);

        return $user->delete();
    }
}
