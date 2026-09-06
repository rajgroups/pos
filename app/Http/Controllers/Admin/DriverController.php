<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DriverService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    protected $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    public function index()
    {
        $drivers = $this->driverService->getAll();
        return view('admin.driver.index', compact('drivers'));
    }

    public function create()
    {
        $vehicles = \App\Models\Vehicle::whereNull('driver_id')->get();
        return view('admin.driver.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:drivers,phone',
            'email'    => 'nullable|email|unique:drivers,email',
            'driver_type' => 'required|in:individual,vendor',
        ]);

        $this->driverService->create($validated);

        return redirect()->route('admin.drivers.index')
                         ->with('success', 'Driver created successfully.');
    }

    public function show($id)
    {
        $driver = $this->driverService->getById($id);
        if (!$driver) {
            abort(404);
        }
        $driver->load(['vehicle', 'documents.documentType']);
        return view('admin.driver.show', compact('driver'));
    }

    public function edit($id)
    {
        $driver = $this->driverService->getById($id);
        $vehicles = \App\Models\Vehicle::whereNull('driver_id')->orWhere('driver_id', $id)->get();
        return view('admin.driver.edit', compact('driver', 'vehicles'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'name'     => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20|unique:drivers,phone,' . $id,
            'email'    => 'nullable|email|unique:drivers,email,' . $id,
            'driver_type' => 'nullable|in:individual,vendor',
            'status'   => 'nullable|string',
        ]);

        $this->driverService->update($id, $validated);

        return redirect()->route('admin.drivers.index')
                         ->with('success', 'Driver updated successfully.');
    }

    public function destroy($id)
    {
        $this->driverService->delete($id);

        return redirect()->route('admin.drivers.index')
                         ->with('success', 'Driver deleted successfully.');
    }
}
