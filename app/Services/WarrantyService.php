<?php

namespace App\Services;

use App\Interfaces\WarrantyInterface;
use App\Helpers\ValidationHelper;
use App\Helpers\NotifyHelper;
use App\Models\Warranty;

class WarrantyService
{
    protected $repo;

    public function __construct(WarrantyInterface $repo)
    {
        $this->repo = $repo;
    }


    public function all()
    {
        return $this->repo->all();
    }

    // public function list($limit = 20)
    // {
    //     return $this->repo->paginate($limit);
    // }

    public function find($id){
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        // Validate
        $validate = ValidationHelper::validate($data, Warranty::rules());

        if ($validate['status'] === 'error') {
            return [
                'status' => 'error',
                'errors' => $validate['errors'],
                'message' => $validate['message']
            ];
        }

        $warranty = $this->repo->create($validate['data']);
        NotifyHelper::success('warranty.created_success');

        return [
            'status' => 'success',
            'data' => $warranty
        ];
    }

    public function edit($id)
    {
        return $this->repo->find($id);
    }

    public function update($id, array $data)
    {
        $validate = ValidationHelper::validate($data, Warranty::rules($id));

        if ($validate['status'] === 'error') {
            return [
                'status' => 'error',
                'errors' => $validate['errors'],
                'message' => $validate['message']
            ];
        }

        $warranty = $this->repo->update($id, $validate['data']);
        NotifyHelper::success('warranty.updated_success');

        return [
            'status' => 'success',
            'data' => $warranty
        ];
    }

    public function delete($id)
    {
        $result = $this->repo->delete($id);
        NotifyHelper::success('warranty.deleted_success');
        return $result;
    }
}
