<?php

namespace App\Interfaces;

interface StoreInterface extends BaseCrudRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []);
    public function findBySlug(string $slug);
    public function restore(int $id);
    public function forceDelete(int $id);
}
