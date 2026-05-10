<?php

namespace App\Repositories;

use App\Interfaces\VariantAttributeInterface;
use App\Models\VariantAttribute;

class VariantAttributeRepository implements VariantAttributeInterface
{
    protected $model;

    public function __construct(VariantAttribute $variant)
    {
        $this->model = $variant;
    }

    public function all()
    {
        return $this->model->orderBy('id', 'DESC')->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $variant = $this->find($id);
        if (!$variant) {
            return false;
        }

        $variant->update($data);
        return $variant;
    }

    public function delete($id)
    {
        $variant = $this->find($id);
        if (!$variant) {
            return false;
        }

        return $variant->delete();
    }
}
