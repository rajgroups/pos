<?php

namespace App\Repositories;

use App\Interfaces\CategoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryInterface
{
    public function all()
    {
        return Category::orderBy('id', 'desc')->get();
    }

    public function find($id)
    {
        return Category::findOrFail($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        return Category::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Category::destroy($id);
    }

    public function getActiveCategories()
    {
        return Category::where('status', 1)->get();
    }
}
