<?php

namespace App\Services;

use App\Interfaces\DriverDocumentInterface;
use App\Helpers\ImageHelper;

class DriverDocumentService
{
    protected $repo;

    public function __construct(DriverDocumentInterface $repo)
    {
        $this->repo = $repo;
    }

    public function create(array $data)
    {
        if (isset($data['file'])) {
            $data['file_path'] = ImageHelper::uploadImage($data['file'], null, 'driver_documents');
        }
        
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $document = $this->repo->find($id);
        
        if (isset($data['file'])) {
            $data['file_path'] = ImageHelper::uploadImage($data['file'], $document->file_path, 'driver_documents');
        }

        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $document = $this->repo->find($id);
        if ($document && $document->file_path) {
            ImageHelper::deleteImage($document->file_path, 'driver_documents');
        }
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
