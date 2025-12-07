<?php

namespace App\Services;

use App\Interfaces\StoreInterface;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

class StoreService
{
    protected $repo;
    protected $dir = 'store/';

    public function __construct(StoreInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(int $perPage = 15, array $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function getById(int $id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        // Slug Auto-Generation
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['store_name']);
        }

        // logo
        if (!empty($data['logo'])) {
            $data['logo'] = ImageHelper::uploadImage($data['logo'], null, $this->dir);
        }

        // banner
        if (!empty($data['banner'])) {
            $data['banner'] = ImageHelper::uploadImage($data['banner'], null, $this->dir);
        }

        // gallery
        if (!empty($data['gallery']) && is_array($data['gallery'])) {
            $gallery = [];
            foreach ($data['gallery'] as $img) {
                $gallery[] = ImageHelper::uploadImage($img, null, $this->dir);
            }
            $data['gallery'] = $gallery;
        }

        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        $store = $this->repo->find($id);

        // logo
        if (!empty($data['logo'])) {
            $data['logo'] = ImageHelper::uploadImage($data['logo'], $store->logo, $this->dir);
        }

        // banner
        if (!empty($data['banner'])) {
            $data['banner'] = ImageHelper::uploadImage($data['banner'], $store->banner, $this->dir);
        }

        // gallery (merge existing + new)
        if (!empty($data['gallery'])) {
            $newGallery = [];

            foreach ($data['gallery'] as $g) {
                if (is_file($g)) {
                    $newGallery[] = ImageHelper::uploadImage($g, null, $this->dir);
                } else {
                    $newGallery[] = $g; // keep existing
                }
            }

            $data['gallery'] = $newGallery;
        }

        return $this->repo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repo->delete($id);
    }

    public function restore(int $id)
    {
        return $this->repo->restore($id);
    }

    public function forceDelete(int $id)
    {
        return $this->repo->forceDelete($id);
    }
}
