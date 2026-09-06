<?php
namespace App\Repositories;

use App\Interfaces\VehicleDocumentInterface;
use App\Models\VehicleDocument;

class VehicleDocumentRepository implements VehicleDocumentInterface{

    protected $model;

    public function __construct(VehicleDocument $model) {
        $this->model = $model;
    }

    public function all(){
        return $this->model->with(['vehicle', 'documentType'])->latest()->paginate(10);
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
