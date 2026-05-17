<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VehicleTypeIndexRequest;
use App\Services\VehicleTypeService;
use Illuminate\Http\JsonResponse;

class VehicleTypeController extends Controller
{
    public function __construct(protected VehicleTypeService $vehicleTypeService)
    {
    }

    public function index(VehicleTypeIndexRequest $request): JsonResponse
    {
        $vehicleTypes = $this->vehicleTypeService->getVehicleTypesWithSubCategories(
            $request->boolean('active_only', true)
        );

        return response()->json([
            'status' => true,
            'message' => 'Vehicle types fetched successfully.',
            'data' => $vehicleTypes,
        ]);
    }
}
