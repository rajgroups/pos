<?php

namespace App\Repositories;

use App\Interfaces\CategoryInterface;
use App\Models\VehicleCategory;

class CategoryRepository implements CategoryInterface
{
    public function all()
    {
        return VehicleCategory::orderBy('id', 'desc')->get();
    }

    public function find($id)
    {
        return VehicleCategory::findOrFail($id);
    }

    public function create(array $data)
    {
        return VehicleCategory::create($data);
    }

    public function update($id, array $data)
    {
        return VehicleCategory::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return VehicleCategory::destroy($id);
    }

    public function getActiveCategories()
    {
        return VehicleCategory::where('is_active', 1)->get();
    }
}
