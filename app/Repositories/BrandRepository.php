<?php
namespace App\Repositories;

use App\Interfaces\BrandInterface;
use App\Models\Brand;

class BrandRepository implements BrandInterface{

    public function all(){
        return Brand::all();
    }

    public function find($id)
    {
        throw new \Exception('Not implemented');
    }

    public function create(array $data)
    {
        throw new \Exception('Not implemented');
    }

    public function update($id, array $data)
    {
        throw new \Exception('Not implemented');
    }

    public function delete($id)
    {
        throw new \Exception('Not implemented');
    }
}
