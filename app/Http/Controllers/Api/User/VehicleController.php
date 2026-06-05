<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Helpers\KeywordHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(protected VehicleService $vehicleService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = ValidationHelper::validateVehicleIndex($request->all());

        if ($validated[KeywordHelper::STATUS] === KeywordHelper::ERROR) {
            return ApiResponseHelper::error(
                $validated[KeywordHelper::MESSAGE],
                $validated[KeywordHelper::ERRORS]
            );
        }

        $vehicles = $this->vehicleService->getVehicles($validated[KeywordHelper::DATA]);

        return ApiResponseHelper::success(
            'Vehicles fetched successfully.',
            $vehicles
        );
    }
}
