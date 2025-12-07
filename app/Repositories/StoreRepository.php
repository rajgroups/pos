<?php

namespace App\Repositories;

use App\Interfaces\StoreInterface;
use App\Interfaces\BaseCrudRepositoryInterface;
use App\Models\Store;

class StoreRepository implements StoreInterface
{
    protected $model;

    public function __construct(Store $model)
    {
        $this->model = $model;
    }

    /** ------------------------------------
     * Base CRUD (from BaseCrudRepositoryInterface)
     * ------------------------------------ */

    public function all()
    {
        return $this->model->latest()->get();
    }

    public function find($id)
    {
        return $this->model->withTrashed()->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $store = $this->model->withTrashed()->findOrFail($id);
        $store->update($data);

        return $store;
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    /** ------------------------------------
     * EXTRA OPERATIONS
     * ------------------------------------ */

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $query->where('store_name', 'LIKE', "%" . $filters['search'] . "%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['trashed'])) {
            $query->onlyTrashed();
        }

        return $query->latest()->paginate($perPage);
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function restore(int $id)
    {
        return $this->model->withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id)
    {
        $store = $this->model->withTrashed()->findOrFail($id);

        if (method_exists($store, 'deleteFiles')) {
            $store->deleteFiles();
        }

        return $store->forceDelete();
    }
}
