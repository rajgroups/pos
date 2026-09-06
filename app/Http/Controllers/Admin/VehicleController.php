<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VehicleService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    protected $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function index()
    {
        $vehicles = $this->vehicleService->getAll();
        return view('admin.vehicle.index', compact('vehicles'));
    }

    public function create()
    {
        $categories = \App\Models\VehicleCategory::with('subCategories')->whereNull('parent_id')->where('is_active', 1)->get();
        $drivers = \App\Models\Driver::where('status', 'active')->get();
        return view('admin.vehicle.create', compact('categories', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id'      => 'nullable|exists:drivers,id',
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number',
            'brand'          => 'required|string|max:255',
            'model'          => 'required|string|max:255',
            'color'          => 'required|string|max:50',
            'manufacture_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'seating_capacity' => 'required|integer|min:1',
            'load_capacity'    => 'nullable|numeric|min:0',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            
            'rc_number'        => 'nullable|string|max:255',
            'rc_expiry'        => 'nullable|date',
            'insurance_number' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date',
            'permit_number'    => 'nullable|string|max:255',
            'permit_expiry'    => 'nullable|date',
            'fitness_certificate_number' => 'nullable|string|max:255',
            'fitness_expiry'   => 'nullable|date',
            
            'status'           => 'required|in:active,inactive,maintenance,retired',
            'is_verified'      => 'nullable|boolean',
            
            'front_image'      => 'nullable|image|max:5120',
            'back_image'       => 'nullable|image|max:5120',
            'side_image'       => 'nullable|image|max:5120',
        ]);

        $validated['is_verified'] = $request->has('is_verified') ? 1 : 0;

        foreach (['front_image', 'back_image', 'side_image'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $path = $request->file($imageField)->store('vehicles', 'public');
                $validated[$imageField] = $path;
            }
        }

        $this->vehicleService->create($validated);

        return redirect()->route('admin.vehicles.index')
                         ->with('success', 'Vehicle created successfully.');
    }

    public function show($id)
    {
        $vehicle = $this->vehicleService->getById($id);
        if (!$vehicle) {
            abort(404);
        }
        $vehicle->load(['driver', 'vehicleType', 'location', 'documents.documentType']);
        $bookings = \App\Models\Booking::with('user')->where('vehicle_id', $id)->latest()->take(10)->get();
        return view('admin.vehicle.show', compact('vehicle', 'bookings'));
    }

    public function location($id)
    {
        $vehicle = $this->vehicleService->getById($id);
        if (!$vehicle || !$vehicle->location) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json($vehicle->location);
    }

    public function edit($id)
    {
        $vehicle = $this->vehicleService->getById($id);
        $categories = \App\Models\VehicleCategory::with('subCategories')->whereNull('parent_id')->where('is_active', 1)->get();
        $drivers = \App\Models\Driver::where('status', 'active')->get();
        return view('admin.vehicle.edit', compact('vehicle', 'categories', 'drivers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'driver_id'      => 'nullable|exists:drivers,id',
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number,' . $id,
            'brand'          => 'required|string|max:255',
            'model'          => 'required|string|max:255',
            'color'          => 'required|string|max:50',
            'manufacture_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'seating_capacity' => 'required|integer|min:1',
            'load_capacity'    => 'nullable|numeric|min:0',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            
            'rc_number'        => 'nullable|string|max:255',
            'rc_expiry'        => 'nullable|date',
            'insurance_number' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date',
            'permit_number'    => 'nullable|string|max:255',
            'permit_expiry'    => 'nullable|date',
            'fitness_certificate_number' => 'nullable|string|max:255',
            'fitness_expiry'   => 'nullable|date',
            
            'status'           => 'required|in:active,inactive,maintenance,retired',
            'is_verified'      => 'nullable|boolean',
            
            'front_image'      => 'nullable|image|max:5120',
            'back_image'       => 'nullable|image|max:5120',
            'side_image'       => 'nullable|image|max:5120',
        ]);

        $validated['is_verified'] = $request->has('is_verified') ? 1 : 0;

        foreach (['front_image', 'back_image', 'side_image'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $path = $request->file($imageField)->store('vehicles', 'public');
                $validated[$imageField] = $path;
            }
        }

        $this->vehicleService->update($id, $validated);

        return redirect()->route('admin.vehicles.index')
                         ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        $this->vehicleService->delete($id);

        return redirect()->route('admin.vehicles.index')
                         ->with('success', 'Vehicle deleted successfully.');
    }
}
