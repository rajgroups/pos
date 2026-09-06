<?php
namespace App\Repositories;

use App\Interfaces\DocumentTypeInterface;
use App\Models\DocumentType;

class DocumentTypeRepository implements DocumentTypeInterface{

    protected $model;

    public function __construct(DocumentType $model) {
        $this->model = $model;
    }

    public function all(){
        return $this->model->latest()->paginate(10);
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
