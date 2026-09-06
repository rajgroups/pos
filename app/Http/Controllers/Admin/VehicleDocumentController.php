<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VehicleDocumentService;
use App\Models\Vehicle;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class VehicleDocumentController extends Controller
{
    protected $documentService;

    public function __construct(VehicleDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $documents = $this->documentService->getAll();
        return view('admin.vehicle_documents.index', compact('documents'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'active')->get();
        $documentTypes = DocumentType::where('is_active', 1)->whereIn('for_type', ['vehicle', 'both'])->get();
        return view('admin.vehicle_documents.create', compact('vehicles', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $this->documentService->create($validated);

        return redirect()->route('admin.vehicle-documents.index')
                         ->with('success', 'Vehicle document uploaded successfully.');
    }

    public function edit($id)
    {
        $document = $this->documentService->getById($id);
        $vehicles = Vehicle::where('status', 'active')->get();
        $documentTypes = DocumentType::where('is_active', 1)->whereIn('for_type', ['vehicle', 'both'])->get();
        
        return view('admin.vehicle_documents.edit', compact('document', 'vehicles', 'documentTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'document_type_id' => 'nullable|exists:document_types,id',
            'document_number' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'expiry_date' => 'nullable|date',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $this->documentService->update($id, $validated);

        return redirect()->route('admin.vehicle-documents.index')
                         ->with('success', 'Vehicle document updated successfully.');
    }

    public function destroy($id)
    {
        $this->documentService->delete($id);

        return redirect()->route('admin.vehicle-documents.index')
                         ->with('success', 'Vehicle document deleted successfully.');
    }
}
