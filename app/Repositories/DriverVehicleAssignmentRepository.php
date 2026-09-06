<?php
namespace App\Repositories;

use App\Interfaces\DriverVehicleAssignmentInterface;
use App\Models\DriverVehicleAssignment;

class DriverVehicleAssignmentRepository implements DriverVehicleAssignmentInterface{

    protected $model;

    public function __construct(DriverVehicleAssignment $model) {
        $this->model = $model;
    }

    public function all(){
        return $this->model->with(['driver', 'vehicle'])->latest()->paginate(10);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        // Deactivate previous assignments for this driver and vehicle
        if (!empty($data['is_current'])) {
            $this->model->where('driver_id', $data['driver_id'])->update(['is_current' => false]);
            $this->model->where('vehicle_id', $data['vehicle_id'])->update(['is_current' => false]);
        }
        
        $instance = $this->model->newInstance();
        $instance->fill($data);
        $instance->save();
        return $instance;
    }

    public function update($id, array $data)
    {
        $instance = $this->model->find($id);
        
        if (!empty($data['is_current'])) {
            $this->model->where('driver_id', $data['driver_id'] ?? $instance->driver_id)
                        ->where('id', '!=', $id)
                        ->update(['is_current' => false]);
            $this->model->where('vehicle_id', $data['vehicle_id'] ?? $instance->vehicle_id)
                        ->where('id', '!=', $id)
                        ->update(['is_current' => false]);
        }
        
        $instance->fill($data);
        return $instance->save();
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
