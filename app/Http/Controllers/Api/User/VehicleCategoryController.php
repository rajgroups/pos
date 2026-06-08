<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VehicleCategoryIndexRequest;
use App\Http\Resources\VehicleCategoryPricingResource;
use App\Http\Resources\VehicleCategoryResource;
use App\Models\VehicleCategory;
use App\Services\VehicleCategoryService;
use Illuminate\Http\JsonResponse;

class VehicleCategoryController extends Controller
{
    public function __construct(protected VehicleCategoryService $vehicleCategoryService)
    {
    }

    public function index(VehicleCategoryIndexRequest $request): JsonResponse
    {
        $categories = $this->vehicleCategoryService->getCategories(
            $request->boolean('active_only', true)
        );

        return response()->json([
            'status' => true,
            'message' => 'Vehicle categories fetched successfully.',
            'data' => VehicleCategoryResource::collection($categories),
        ]);
    }

    public function pricing(VehicleCategory $vehicleCategory): JsonResponse
    {
        $vehicleCategory->load('pricing');

        if (! $vehicleCategory->pricing) {
            return response()->json([
                'status' => false,
                'message' => 'Pricing not configured for this category.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Vehicle category pricing fetched successfully.',
            'data' => new VehicleCategoryPricingResource($vehicleCategory->pricing),
        ]);
    }
}
