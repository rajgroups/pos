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
            'dob'      => 'nullable|date',
            'gender'   => 'nullable|in:male,female,other',
            'address'  => 'nullable|string',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'pincode'  => 'nullable|string|max:20',
            
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number'     => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:255|unique:drivers,license_number',
            'license_expiry' => 'nullable|date',
            'license_categories' => 'nullable|array',
            'license_categories.*' => 'string',
            
            'driver_type' => 'nullable|in:car,bike,auto,borewell,tractor,harvester,lorry,mini_van,bus,other',
            'status'      => 'nullable|in:active,inactive,blocked',
            'remarks'     => 'nullable|string',

            'profile_photo' => 'nullable|image|max:5120',
            'license_front' => 'nullable|image|max:5120',
            'license_back' => 'nullable|image|max:5120',
            'aadhaar_front' => 'nullable|image|max:5120',
            'aadhaar_back' => 'nullable|image|max:5120',
            'pan_card_file' => 'nullable|image|max:5120',
            'police_verification_file' => 'nullable|file|max:5120',
            'medical_certificate' => 'nullable|file|max:5120',
        ]);

        foreach (['profile_photo', 'license_front', 'license_back', 'aadhaar_front', 'aadhaar_back', 'pan_card_file', 'police_verification_file', 'medical_certificate'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store('drivers', 'public');
                $validated[$fileField] = $path;
            }
        }

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

        $bookings = $driver->bookings()->latest()->paginate(5, ['*'], 'bookings_page');
        $reviews = $driver->reviews()->latest()->paginate(5, ['*'], 'reviews_page');
        $recharges = $driver->rechargeRequests()->latest()->paginate(5, ['*'], 'recharges_page');

        return view('admin.driver.show', compact('driver', 'bookings', 'reviews', 'recharges'));
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
            'dob'      => 'nullable|date',
            'gender'   => 'nullable|in:male,female,other',
            'address'  => 'nullable|string',
            'city'     => 'nullable|string|max:255',
            'state'    => 'nullable|string|max:255',
            'pincode'  => 'nullable|string|max:20',
            
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number'     => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:255|unique:drivers,license_number,' . $id,
            'license_expiry' => 'nullable|date',
            'license_categories' => 'nullable|array',
            'license_categories.*' => 'string',
            
            'driver_type' => 'nullable|in:car,bike,auto,borewell,tractor,harvester,lorry,mini_van,bus,other',
            'status'      => 'nullable|in:active,inactive,blocked',
            'remarks'     => 'nullable|string',

            'profile_photo' => 'nullable|image|max:5120',
            'license_front' => 'nullable|image|max:5120',
            'license_back' => 'nullable|image|max:5120',
            'aadhaar_front' => 'nullable|image|max:5120',
            'aadhaar_back' => 'nullable|image|max:5120',
            'pan_card_file' => 'nullable|image|max:5120',
            'police_verification_file' => 'nullable|file|max:5120',
            'medical_certificate' => 'nullable|file|max:5120',
        ]);

        foreach (['profile_photo', 'license_front', 'license_back', 'aadhaar_front', 'aadhaar_back', 'pan_card_file', 'police_verification_file', 'medical_certificate'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store('drivers', 'public');
                $validated[$fileField] = $path;
            }
        }

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
