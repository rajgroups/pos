<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\IndicabModeService;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    public function __construct(protected IndicabModeService $modeService) {}

    /**
     * Get public configuration for the apps.
     */
    public function index(): JsonResponse
    {
        return ApiResponseHelper::success('Configuration fetched successfully.', [
            'communication_mode' => $this->modeService->getMode(),
        ]);
    }
}
