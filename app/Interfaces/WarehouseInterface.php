<?php

namespace App\Interfaces;

use App\Interfaces\BaseCrudRepositoryInterface;

interface WarehouseInterface extends BaseCrudRepositoryInterface
{
    /**
     * Paginate warehouse list.
     *
     * @param int $limit
     * @return mixed
     */
    public function paginate($limit = 20);

    /**
     * Apply filtering on warehouses (optional usage).
     *
     * @param array $filters
     * @return mixed
     */
    public function filter(array $filters = []);
}
