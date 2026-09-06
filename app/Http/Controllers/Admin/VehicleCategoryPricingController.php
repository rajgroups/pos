<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VehicleCategoryPricingService;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;

class VehicleCategoryPricingController extends Controller
{
    protected $pricingService;

    public function __construct(VehicleCategoryPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function index()
    {
        $pricings = $this->pricingService->getAll();
        return view('admin.vehicle_category_pricing.index', compact('pricings'));
    }

    public function create()
    {
        $categories = VehicleCategory::where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['subCategories' => function ($query) {
                $query->where('is_active', 1);
            }])
            ->orderBy('sort_order')
            ->get();
        return view('admin.vehicle_category_pricing.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'pricing_type' => 'required|in:standard,premium',
            'base_fare' => 'required|numeric|min:0',
            'minimum_fare' => 'required|numeric|min:0',
            'per_km_rate' => 'required|numeric|min:0',
            'per_hour_rate' => 'required|numeric|min:0',
            'per_day_rate' => 'required|numeric|min:0',
            'per_acre_rate' => 'required|numeric|min:0',
            'per_ton_rate' => 'required|numeric|min:0',
            'waiting_charge_per_hour' => 'required|numeric|min:0',
            'night_charge_percentage' => 'required|numeric|min:0|max:100',
            'surge_multiplier' => 'required|numeric|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $this->pricingService->create($validated);

        return redirect()->route('admin.vehicle-pricing.index')
            ->with('success', 'Pricing rule created successfully.');
    }

    public function edit($id)
    {
        $pricing = $this->pricingService->getById($id);
        $categories = VehicleCategory::where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['subCategories' => function ($query) {
                $query->where('is_active', 1);
            }])
            ->orderBy('sort_order')
            ->get();

        return view('admin.vehicle_category_pricing.edit', compact('pricing', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vehicle_category_id' => 'nullable|exists:vehicle_categories,id',
            'pricing_type' => 'nullable|in:standard,premium',
            'base_fare' => 'nullable|numeric|min:0',
            'minimum_fare' => 'nullable|numeric|min:0',
            'per_km_rate' => 'nullable|numeric|min:0',
            'per_hour_rate' => 'nullable|numeric|min:0',
            'per_day_rate' => 'nullable|numeric|min:0',
            'per_acre_rate' => 'nullable|numeric|min:0',
            'per_ton_rate' => 'nullable|numeric|min:0',
            'waiting_charge_per_hour' => 'nullable|numeric|min:0',
            'night_charge_percentage' => 'nullable|numeric|min:0|max:100',
            'surge_multiplier' => 'nullable|numeric|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $this->pricingService->update($id, $validated);

        return redirect()->route('admin.vehicle-pricing.index')
            ->with('success', 'Pricing rule updated successfully.');
    }

    public function destroy($id)
    {
        $this->pricingService->delete($id);

        return redirect()->route('admin.vehicle-pricing.index')
            ->with('success', 'Pricing rule deleted successfully.');
    }
}
