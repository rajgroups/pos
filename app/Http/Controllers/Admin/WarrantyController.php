<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WarrantyService;
use Illuminate\Http\Request;
use App\Helpers\ValidationHelper;
use App\Models\Warranty;
use App\Helpers\NotifyHelper;

class WarrantyController extends Controller
{
    protected $service;

    public function __construct(WarrantyService $service)
    {
        $this->service = $service;
    }

    /**
     * List all warranties
     */
    public function index()
    {
        $warranties = $this->service->all();
        return view('admin.warranty.index', compact('warranties'));
    }

    /**
     * Create form view
     */
    public function create()
    {
        return view('admin.warranty.create');
    }

    /**
     * Store warranty
     */
    public function store(Request $request)
    {
        // Validate using ValidationHelper + Warranty::rules()
        $validate = ValidationHelper::validate($request->all(), Warranty::rules());

        if ($validate['status'] === 'error') {
            return back()
                ->withErrors($validate['errors'])
                ->withInput();
        }

        // Save warranty via service
        $this->service->create($validate['data']);

        // Success message
        NotifyHelper::success('warranty.created_success');

        return redirect()->route('admin.warranty.index');
    }

    /**
     * Edit view
     */
    public function edit($id)
    {
        $warranty = $this->service->find($id);

        if (!$warranty) {
            NotifyHelper::error('warranty.not_found');
            return redirect()->route('admin.warranty.index');
        }

        return view('admin.warranty.edit', compact('warranty'));
    }

    /**
     * Update warranty
     */
    public function update(Request $request, $id)
    {
        $validate = ValidationHelper::validate($request->all(), Warranty::rules($id));

        if ($validate['status'] === 'error') {
            return back()
                ->withErrors($validate['errors'])
                ->withInput();
        }

        $updated = $this->service->update($id, $validate['data']);

        if (!$updated) {
            NotifyHelper::error('warranty.not_found');
            return back();
        }

        NotifyHelper::success('warranty.updated_success');

        return redirect()->route('admin.warranty.index');
    }

    /**
     * Delete warranty
     */
    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            NotifyHelper::error('warranty.delete_failed');
            return back();
        }

        NotifyHelper::success('warranty.deleted_success');
        return redirect()->route('admin.warranty.index');
    }
}
