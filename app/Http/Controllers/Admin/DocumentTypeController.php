<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\KeywordHelper;
use App\Helpers\NotifyHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\DocumentTypeService;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    protected $service;

    public function __construct(DocumentTypeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $documentTypes = $this->service->getAll();
        return view('admin.document_types.list', compact('documentTypes'));
    }

    public function create()
    {
        return view('admin.document_types.create');
    }

    public function store(Request $request)
    {
        $validate = ValidationHelper::validateDocumentType($request->all());

        if ($validate['status'] === KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validate['message']);
            return back()->withInput();
        }

        $this->service->create($request->all());

        NotifyHelper::success('Document type created successfully.');
        return back();
    }

    public function edit($id)
    {
        $validate = ValidationHelper::validateDocumentTypeExist($id);

        if($validate['status'] == KeywordHelper::ERROR){
            NotifyHelper::errorMessage($validate['message']);
            return redirect()->route('admin.document-types.index');
        }

        $documentType = $this->service->getById($id);

        return view('admin.document_types.edit', compact('documentType'));
    }

    public function update(Request $request, $id)
    {
        $validated = ValidationHelper::validateDocumentType($request->all(), true, $id);

        if ($validated['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validated['message']);
            return back()->withInput();
        }

        $updated = $this->service->update($id, $validated['data']);

        if ($updated) {
            NotifyHelper::success('Document type updated successfully.');
            return redirect()->route('admin.document-types.index');
        }

        NotifyHelper::errorMessage('Something went wrong.');
        return back();
    }

    public function destroy($id)
    {
        $validate = ValidationHelper::validateDocumentTypeExist($id);

        if ($validate['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validate['message']);
            return back();
        }

        $this->service->delete($id);

        NotifyHelper::success('Document type deleted successfully.');
        return back();
    }
}
