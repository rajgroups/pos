<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DriverDocumentService;
use App\Models\Driver;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DriverDocumentController extends Controller
{
    protected $documentService;

    public function __construct(DriverDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $documents = $this->documentService->getAll();
        return view('admin.driver_documents.index', compact('documents'));
    }

    public function create()
    {
        $drivers = Driver::where('status', 'active')->get();
        $documentTypes = DocumentType::where('is_active', 1)->whereIn('for_type', ['driver', 'both'])->get();
        return view('admin.driver_documents.create', compact('drivers', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $this->documentService->create($validated);

        return redirect()->route('admin.driver-documents.index')
                         ->with('success', 'Driver document uploaded successfully.');
    }

    public function edit($id)
    {
        $document = $this->documentService->getById($id);
        $drivers = Driver::where('status', 'active')->get();
        $documentTypes = DocumentType::where('is_active', 1)->whereIn('for_type', ['driver', 'both'])->get();
        
        return view('admin.driver_documents.edit', compact('document', 'drivers', 'documentTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'driver_id' => 'nullable|exists:drivers,id',
            'document_type_id' => 'nullable|exists:document_types,id',
            'document_number' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'expiry_date' => 'nullable|date',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $this->documentService->update($id, $validated);

        return redirect()->route('admin.driver-documents.index')
                         ->with('success', 'Driver document updated successfully.');
    }

    public function destroy($id)
    {
        $this->documentService->delete($id);

        return redirect()->route('admin.driver-documents.index')
                         ->with('success', 'Driver document deleted successfully.');
    }
}
