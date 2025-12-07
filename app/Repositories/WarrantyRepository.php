<?php

namespace App\Repositories;

use App\Interfaces\WarrantyInterface;
use App\Models\Warranty;

class WarrantyRepository implements WarrantyInterface
{
    public function all()
    {
        return Warranty::orderBy('id', 'DESC')->get();
    }

    public function paginate($limit = 20)
    {
        return Warranty::orderBy('id', 'DESC')->paginate($limit);
    }

    public function find($id)
    {
        return Warranty::find($id);
    }

    public function create(array $data)
    {
        return Warranty::create($data);
    }

    public function update($id, array $data)
    {
        $warranty = Warranty::findOrFail($id);
        $warranty->update($data);
        return $warranty;
    }

    public function delete($id)
    {
        $warranty = Warranty::findOrFail($id);
        return $warranty->delete();
    }
}
