<?php

namespace App\Repositories;

use App\Interfaces\UnitInterface;
use App\Models\Unit;

class UnitRepository implements UnitInterface
{
    protected $model;

    public function __construct(Unit $unit)
    {
        $this->model = $unit;
    }

    /**
     * Get all records.
     */
    public function all()
    {
        return $this->model->orderBy('id', 'DESC')->get();
    }

    /**
     * Find a record by ID.
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new unit.
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a unit.
     */
    public function update($id, array $data)
    {
        $unit = $this->find($id);
        if (!$unit) return false;

        $unit->update($data);
        return $unit;
    }

    /**
     * Delete a unit.
     */
    public function delete($id)
    {
        $unit = $this->find($id);
        if (!$unit) return false;

        return $unit->delete();
    }
}
