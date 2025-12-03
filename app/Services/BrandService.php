<?php
namespace App\Services;

use App\Interfaces\BrandInterface;

class BrandService{

    protected $repo;

    public function __construct(BrandInterface $brandrepo) {
        $this->repo = $brandrepo;
    }

    public function all(){
        return $this->repo->all();
    }
}
