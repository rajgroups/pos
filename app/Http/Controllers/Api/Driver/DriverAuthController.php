<?php

namespace App\Http\Controllers\Api\Driver;

use App\Helpers\ApiResponseHelper;
use App\Helpers\KeywordHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\DriverService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverAuthController extends Controller
{
    protected DriverService $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Authenticate driver using mobile number
     *
     * Validate mobile number,
     * verify driver existence,
     * generate OTP,
     * and return API response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request): JsonResponse
    {
        // Validate mobile number
        $validated = ValidationHelper::ValidateMobile(
            $request->mobile
        );

        // Return validation error response
        if ($validated['status'] == KeywordHelper::ERROR) {
            return ApiResponseHelper::error(
                $validated['message'],
                $validated['errors']
            );
        }

        try {
            $otp = $this->driverService->sendLoginOtp($request->mobile);

            // Return success response with OTP
            return ApiResponseHelper::success(
                __('string.common.driver_found'),
                [
                    'otp' => $otp
                ],
                200
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::error(
                __('string.common.no_driver_found'),
                [],
                404
            );
        }
    }

    /**
     * Verify driver login OTP
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = ValidationHelper::ValidateOtp($request);

        if ($validated['status'] == KeywordHelper::ERROR) {
            return ApiResponseHelper::error($validated['message'], $validated['errors']);
        }

        try {
            $driver = $this->driverService->verifyLoginOtp($request->mobile, $request->otp);

            $token = $driver->createToken('auth_token')->plainTextToken;

            $formatData = [
                'id' => $driver->id,
                'name' => $driver->name ?? null,
                'email' => $driver->email ?? null,
                'mobile' => $driver->phone ?? null,
            ];

            return ApiResponseHelper::success(__('string.common.login_success'), [
                'token' => $token,
                'driver' => $formatData
            ], 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseHelper::error(__('string.common.no_driver_found'), [], 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error($e->getMessage(), [], 422);
        }
    }
}
