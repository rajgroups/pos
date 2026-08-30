<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\NearbyVehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NearbyVehicleController extends Controller
{
    public function __construct(protected NearbyVehicleService $nearbyVehicleService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vehicle_category_id' => ['required', 'integer', 'exists:vehicle_categories,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0.1', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $radius = isset($validated['radius']) ? (float) $validated['radius'] : 5.0;

        $data = $this->nearbyVehicleService->getNearbyVehicles(
            (int) $validated['vehicle_category_id'],
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            $radius
        );

        return response()->json([
            'success' => true,
            'message' => 'Nearby vehicles fetched successfully',
            'data' => $data,
        ]);
    }
}
