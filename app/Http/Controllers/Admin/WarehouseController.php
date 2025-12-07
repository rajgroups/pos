<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Helpers\ValidationHelper;
use App\Helpers\NotifyHelper;

class WarehouseController extends Controller
{
    protected $service;

    public function __construct(WarehouseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // simple filters (optional): search by name/code, status, type
        $limit = 20;
        $warranties = $this->service->list($limit);
        // If you need filtering, you can update the repository to accept filters
        return view('admin.warehouses.index', ['warehouses' => $warranties]);
    }

    public function create()
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        // Validate using ValidationHelper
        $validate = ValidationHelper::validate($request->all(), Warehouse::rules());

        if ($validate['status'] === 'error') {
            return back()
                ->withErrors($validate['errors'])
                ->withInput();
        }

        $result = $this->service->create($validate['data']);

        if (isset($result['status']) && $result['status'] === 'error') {
            return back()->withErrors($result['errors'] ?? [ $result['message'] ?? 'Validation error' ])->withInput();
        }

        return redirect()->route('admin.warehouses.index');
    }

    public function edit($id)
    {
        $warehouse = $this->service->find($id);
        if (!$warehouse) {
            NotifyHelper::error('warehouse.not_found');
            return redirect()->route('admin.warehouses.index');
        }
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, $id)
    {
        $validate = ValidationHelper::validate($request->all(), Warehouse::rules($id));

        if ($validate['status'] === 'error') {
            return back()->withErrors($validate['errors'])->withInput();
        }

        $result = $this->service->update($id, $validate['data']);

        if (isset($result['status']) && $result['status'] === 'error') {
            return back()->withErrors($result['errors'] ?? [ $result['message'] ?? 'Validation error' ])->withInput();
        }

        return redirect()->route('admin.warehouses.index');
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            NotifyHelper::error('warehouse.delete_failed');
            return back();
        }

        return redirect()->route('admin.warehouses.index');
    }
}
