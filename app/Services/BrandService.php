<?php
namespace App\Services;

use App\Helpers\ImageHelper;
use App\Interfaces\BrandInterface;
use Illuminate\Support\Str;

class BrandService{

    protected $repo;

    public function __construct(BrandInterface $brandrepo) {
        $this->repo = $brandrepo;
    }

    public function all(){
        return $this->repo->all();
    }

    public function create(array $data){

        $data['image']  = ImageHelper::uploadImage($data['image'] ?? null,null,ImageHelper::BRAND);
        $data['icon']   = ImageHelper::uploadImage($data['icon'] ?? null, null, ImageHelper::BRAND);
        $data['slug']   = Str::slug($data['slug']);
        return $this->repo->create($data);
    }

    public function getById($id)
    {
        return $this->repo->find($id);
    }

    public function update($id, array $data)
    {
        $brand = $this->repo->find($id);

        // Handle Image Change
        if (isset($data['image'])) {
            ImageHelper::deleteImage(ImageHelper::BRAND, $brand->image);
            $data['image'] = ImageHelper::uploadImage($data['image'],null, ImageHelper::BRAND);
        }

        // Handle Icon Change
        if (isset($data['icon'])) {
            ImageHelper::deleteImage(ImageHelper::BRAND, $brand->icon);
            $data['icon'] = ImageHelper::uploadImage($data['icon'], null,ImageHelper::BRAND);
        }

        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $brand = $this->repo->find($id);

        // Delete images from storage
        if ($brand->image) {
            ImageHelper::deleteImage(ImageHelper::BRAND, $brand->image);
        }

        if ($brand->icon) {
            ImageHelper::deleteImage(ImageHelper::BRAND, $brand->icon);
        }

        return $this->repo->delete($id);
    }

}
