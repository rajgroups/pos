<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UnitService;
use App\Helpers\ValidationHelper;
use App\Helpers\NotifyHelper;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $service;

    public function __construct(UnitService $service)
    {
        $this->service = $service;
    }

    /**
     * Display list of units
     */
    public function index()
    {
        $units = $this->service->all();
        return view('admin.unit.index', compact('units'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.unit.create');
    }

    /**
     * Store new Unit
     */
    public function store(Request $request)
    {
        $validate = ValidationHelper::validate($request->all(), Unit::validationRules());

        if ($validate['status'] === 'error') {
            return back()->withErrors($validate['errors'])->withInput();
        }

        $this->service->store($validate['data']);

        NotifyHelper::success('unit.created_success');

        return redirect()->route('admin.unit.index');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $unit = $this->service->find($id);

        if (!$unit) {
            NotifyHelper::error('unit.not_found');
            return redirect()->route('admin.unit.index');
        }

        return view('admin.unit.edit', compact('unit'));
    }

    /**
     * Update Unit
     */
    public function update(Request $request, $id)
    {
        $validate = ValidationHelper::validate(
            $request->all(),
            Unit::validationRules($id)
        );

        if ($validate['status'] === 'error') {
            return back()->withErrors($validate['errors'])->withInput();
        }

        $this->service->update($id, $validate['data']);

        NotifyHelper::success('unit.updated_success');

        return redirect()->route('admin.unit.index');
    }

    /**
     * Delete Unit
     */
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            NotifyHelper::error('unit.not_found');
            return back();
        }

        NotifyHelper::success('unit.deleted_success');

        return back();
    }

    /**
     * Show Unit Details
     */
    public function show($id)
    {
        $unit = $this->service->find($id);

        if (!$unit) {
            NotifyHelper::error('unit.not_found');
            return redirect()->route('admin.unit.index');
        }

        return view('admin.unit.show', compact('unit'));
    }
}
