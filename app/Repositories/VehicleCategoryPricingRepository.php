<?php
namespace App\Repositories;

use App\Interfaces\VehicleCategoryPricingInterface;
use App\Models\VehicleCategoryPricing;

class VehicleCategoryPricingRepository implements VehicleCategoryPricingInterface{

    protected $model;

    public function __construct(VehicleCategoryPricing $model) {
        $this->model = $model;
    }

    public function all(){
        return $this->model->with('vehicleCategory')->latest()->paginate(10);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        $instance = $this->model->newInstance();
        $instance->fill($data);
        $instance->save();
        return $instance;
    }

    public function update($id, array $data)
    {
        $instance = $this->model->find($id);
        $instance->fill($data);
        return $instance->save();
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
