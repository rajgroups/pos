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
        $vehicleCategories = \App\Models\VehicleCategory::all();
        return view('admin.driver.create', compact('vehicles', 'vehicleCategories'));
    }

    public function store(\App\Http\Requests\Admin\DriverOnboardRequest $request)
    {
        $validated = $request->validated();

        foreach (['profile_photo', 'license_front', 'license_back', 'aadhaar_front', 'aadhaar_back', 'pan_card_file', 'police_verification_file', 'medical_certificate'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $validated[$fileField] = $request->file($fileField)->store('drivers', 'public');
            }
        }

        // Handle Vehicle Files if any
        foreach (['front_image', 'back_image'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $validated[$fileField] = $request->file($fileField)->store('vehicles', 'public');
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $driverData = \Illuminate\Support\Arr::except($validated, [
                'register_vehicle', 'vehicle_category_id', 'vehicle_number', 'brand', 'model', 
                'color', 'manufacture_year', 'rc_number', 'seating_capacity', 'front_image', 'back_image'
            ]);
            
            // Generate OTP for new driver if not set (could be handled in service, but adding for completeness)
            $driverData['otp'] = random_int(1000, 9999);
            
            $driver = $this->driverService->create($driverData);

            if ($request->filled('register_vehicle') && $request->register_vehicle) {
                $vehicleData = \Illuminate\Support\Arr::only($validated, [
                    'vehicle_category_id', 'vehicle_number', 'brand', 'model', 
                    'color', 'manufacture_year', 'rc_number', 'seating_capacity', 'front_image', 'back_image'
                ]);
                $vehicleData['driver_id'] = $driver->id;
                \App\Models\Vehicle::create($vehicleData);
            }
        });

        return redirect()->route('admin.drivers.index')
                         ->with('success', 'Driver onboarded successfully.');
    }

    public function show($id)
    {
        $driver = $this->driverService->getById($id);
        if (!$driver) {
            abort(404);
        }
        $driver->load(['vehicle', 'documents.documentType']);

        $bookings = $driver->bookings()->latest()->paginate(5, ['*'], 'bookings_page');
        $reviews = $driver->reviews()->latest()->paginate(5, ['*'], 'reviews_page');
        $recharges = $driver->rechargeRequests()->latest()->paginate(5, ['*'], 'recharges_page');

        return view('admin.driver.show', compact('driver', 'bookings', 'reviews', 'recharges'));
    }

    public function edit($id)
    {
        $driver = $this->driverService->getById($id);
        $vehicles = \App\Models\Vehicle::whereNull('driver_id')->orWhere('driver_id', $id)->get();
        $vehicleCategories = \App\Models\VehicleCategory::all();
        return view('admin.driver.edit', compact('driver', 'vehicles', 'vehicleCategories'));
    }

    public function update(\App\Http\Requests\Admin\DriverOnboardRequest $request, $id)
    {
        $validated = $request->validated();

        foreach (['profile_photo', 'license_front', 'license_back', 'aadhaar_front', 'aadhaar_back', 'pan_card_file', 'police_verification_file', 'medical_certificate'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $validated[$fileField] = $request->file($fileField)->store('drivers', 'public');
            }
        }

        // Handle Vehicle Files if any
        foreach (['front_image', 'back_image'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $validated[$fileField] = $request->file($fileField)->store('vehicles', 'public');
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request, $id) {
            $driverData = \Illuminate\Support\Arr::except($validated, [
                'register_vehicle', 'vehicle_category_id', 'vehicle_number', 'brand', 'model', 
                'color', 'manufacture_year', 'rc_number', 'seating_capacity', 'front_image', 'back_image'
            ]);
            
            $this->driverService->update($id, $driverData);

            if ($request->filled('register_vehicle') && $request->register_vehicle) {
                $vehicleData = \Illuminate\Support\Arr::only($validated, [
                    'vehicle_category_id', 'vehicle_number', 'brand', 'model', 
                    'color', 'manufacture_year', 'rc_number', 'seating_capacity', 'front_image', 'back_image'
                ]);
                $vehicleData['driver_id'] = $id;
                
                // If driver already has a vehicle, update it, else create
                $vehicle = \App\Models\Vehicle::where('driver_id', $id)->first();
                if ($vehicle) {
                    $vehicle->update($vehicleData);
                } else {
                    \App\Models\Vehicle::create($vehicleData);
                }
            }
        });

        return redirect()->route('admin.drivers.index')
                         ->with('success', 'Driver and vehicle updated successfully.');
    }

    public function destroy($id)
    {
        $this->driverService->delete($id);

        return redirect()->route('admin.drivers.index')
                         ->with('success', 'Driver deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $driver = $this->driverService->getById($id);
        if (!$driver) {
            abort(404);
        }
        
        $newStatus = $driver->status === 'active' ? 'inactive' : 'active';
        $this->driverService->update($id, ['status' => $newStatus]);
        
        return redirect()->back()->with('success', 'Driver status updated to ' . ucfirst($newStatus) . '.');
    }

    public function toggleVerify($id)
    {
        $driver = $this->driverService->getById($id);
        if (!$driver) {
            abort(404);
        }
        
        $newVerify = !$driver->is_verified;
        $this->driverService->update($id, ['is_verified' => $newVerify]);
        
        $statusText = $newVerify ? 'Verified' : 'Unverified';
        return redirect()->back()->with('success', 'Driver verification updated to ' . $statusText . '.');
    }
}
