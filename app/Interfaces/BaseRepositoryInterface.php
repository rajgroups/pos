<?php

namespace App\Interfaces;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Find a specific record by ID.
     *
     * @param  int  $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create a new record.
     *
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update an existing record by ID.
     *
     * @param  int    $id
     * @param  array  $data
     * @return bool|mixed
     */
    public function update($id, array $data);

    /**
     * Delete a record by ID.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete($id);
}
