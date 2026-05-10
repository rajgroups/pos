<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Interfaces\CategoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected $repo;

    public function __construct(CategoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function create(array $data)
    {
        try{
            $data['image'] = ImageHelper::uploadImage($data['image'] ?? null,null,ImageHelper::CATEGORY);
            $data['icon'] = ImageHelper::uploadImage($data['icon'] ?? null,null,ImageHelper::CATEGORY);
        }catch(Exception $e){
            Log::info($e->getMessage());
        }
        // dd($data);
        return $this->repo->create($data);
    }


    public function update($id, array $data)
    {
        $category = $this->repo->find($id);

        // Upload category image if new one uploaded
        if (isset($data['image'])) {
            $data['image'] = ImageHelper::uploadImage(
                $data['image'],
                $category->image,
                ImageHelper::CATEGORY
            );
        }

        // Upload category icon if new one uploaded
        if (isset($data['icon'])) {
            $data['icon'] = ImageHelper::uploadImage(
                $data['icon'],
                $category->icon,
                ImageHelper::CATEGORY
            );
        }

        return $this->repo->update($id, $data);
    }
    public function delete($id)
    {
        $category = $this->repo->find($id);

        // Delete Image
        ImageHelper::deleteImage($category->image, ImageHelper::CATEGORY);

        // Delete Icon
        ImageHelper::deleteImage($category->icon, ImageHelper::CATEGORY);

        return $this->repo->delete($id);
    }

    public function getAll()
    {
        return $this->repo->all();
    }

    public function getById($id)
    {
        return $this->repo->find($id);
    }
}
