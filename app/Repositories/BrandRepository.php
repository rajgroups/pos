<?php
namespace App\Repositories;

use App\Interfaces\BrandInterface;
use App\Models\Brand;

class BrandRepository implements BrandInterface{

    protected $brand;

    public function __construct(Brand $brand) {
        $this->brand = $brand;
    }

    public function all(){
        return $this->brand->latest()->paginate(10);
    }

    public function find($id)
    {
        return $this->brand->find($id);
    }

    public function create(array $data)
    {
        $brand = $this->brand->newInstance();
        $brand->fill($data);
        $brand->save();
    }

    public function update($id, array $data)
    {
        $brand = $this->brand->find($id);
        $brand->fill($data);
        return $brand->save();
    }

    public function delete($id)
    {
        return $this->brand->where('id', $id)->delete();
    }
}
