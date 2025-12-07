<?php

namespace App\Services;

use App\Interfaces\WarehouseInterface;
use App\Helpers\ValidationHelper;
use App\Helpers\NotifyHelper;
use App\Models\Warehouse;

class WarehouseService
{
    protected $repo;

    public function __construct(WarehouseInterface $repo)
    {
        $this->repo = $repo;
    }

    public function list($limit = 20)
    {
        return $this->repo->paginate($limit);
    }

    public function create(array $data)
    {
        // Validate
        $validate = ValidationHelper::validate($data, Warehouse::rules());

        if ($validate['status'] === 'error') {
            return $validate; // return validation structure
        }

        $warehouse = $this->repo->create($validate['data']);
        NotifyHelper::successMessage(__('string.warehouse.created_success') ?? 'Warehouse created successfully.');

        return [
            'status' => 'success',
            'data' => $warehouse
        ];
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function update($id, array $data)
    {
        $validate = ValidationHelper::validate($data, Warehouse::rules($id));

        if ($validate['status'] === 'error') {
            return $validate;
        }

        $warehouse = $this->repo->update($id, $validate['data']);
        NotifyHelper::successMessage(__('string.warehouse.updated_success') ?? 'Warehouse updated successfully.');

        return [
            'status' => 'success',
            'data' => $warehouse
        ];
    }

    public function delete($id)
    {
        $deleted = $this->repo->delete($id);

        if ($deleted) {
            NotifyHelper::successMessage(__('string.warehouse.deleted_success') ?? 'Warehouse deleted successfully.');
            return true;
        }

        return false;
    }
}
