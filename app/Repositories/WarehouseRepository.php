<?php

namespace App\Repositories;

use App\Interfaces\WarehouseInterface;
use App\Models\Warehouse;

class WarehouseRepository implements WarehouseInterface
{
    /**
     * Get all warehouses.
     */
    public function all()
    {
        return Warehouse::orderBy('id', 'desc')->get();
    }

    /**
     * Paginate warehouses.
     */
    public function paginate($limit = 20)
    {
        return Warehouse::orderBy('id', 'desc')->paginate($limit);
    }

    /**
     * Find warehouse by ID.
     */
    public function find($id)
    {
        return Warehouse::find($id);
    }

    /**
     * Create new warehouse.
     */
    public function create(array $data)
    {
        return Warehouse::create($data);
    }

    /**
     * Update existing warehouse.
     */
    public function update($id, array $data)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);

        return $warehouse;
    }

    /**
     * Delete warehouse.
     */
    public function delete($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return $warehouse->delete();
    }

    /**
     * Filter warehouses with search, status, type, etc.
     */
    public function filter(array $filters = [])
    {
        $query = Warehouse::query();

        if (!empty($filters['search'])) {
            $query->where('warehouse_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('warehouse_code', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('warehouse_type', $filters['type']);
        }

        return $query->orderBy('id', 'desc')->paginate($filters['limit'] ?? 20);
    }
}
