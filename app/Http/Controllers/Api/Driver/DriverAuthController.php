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

            $responseData = [];
            if (config('services.sms_gateway.debug_return_otp', true)) {
                $responseData['otp'] = $otp;
            }

            // Return success response with optional OTP
            return ApiResponseHelper::success(
                __('string.common.driver_found'),
                $responseData,
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
            $fcmToken = $request->input('fcm_token');
            $driver = $this->driverService->verifyLoginOtp($request->mobile, $request->otp, $fcmToken);

            $token = $driver->createToken('auth_token')->plainTextToken;

            $driver->load('vehicle.category'); // Eager load vehicle and vehicle category

            $vehicleData = null;
            if ($driver->vehicle) {
                $vehicleData = [
                    'id' => $driver->vehicle->id,
                    'registration_number' => $driver->vehicle->vehicle_number,
                    'color' => $driver->vehicle->color,
                    'status' => $driver->vehicle->status,
                    'type_name' => $driver->vehicle->category ? $driver->vehicle->category->name : null,
                ];
            }

            $formatData = [
                'id' => $driver->id,
                'name' => $driver->name ?? null,
                'email' => $driver->email ?? null,
                'mobile' => $driver->phone ?? null,
                'fcm_token' => $driver->fcm_token ?? null,
                'wallet_balance' => (float) ($driver->wallet_balance ?? 0),
                'vehicle' => $vehicleData,
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

    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $driver = $request->user();
        if (!$driver) {
            return ApiResponseHelper::error('Unauthorized access.', [], 401);
        }

        $this->driverService->updateDriver($driver->id, [
            'fcm_token' => $request->fcm_token,
        ]);

        return ApiResponseHelper::success('FCM token updated successfully.', [
            'fcm_token' => $request->fcm_token,
        ], 200);
    }

    /**
     * Get Firebase Custom Token
     */
    public function getFirebaseToken(Request $request): JsonResponse
    {
        $driver = $request->user();
        if (!$driver) {
            return ApiResponseHelper::error('Driver not authenticated.', null, 401);
        }

        $firebaseService = app(\App\Services\FirebaseService::class);
        $token = $firebaseService->createCustomToken("driver_{$driver->id}", ['role' => 'driver']);

        if (!$token) {
            return ApiResponseHelper::error('Failed to generate Firebase token.', null, 500);
        }

        return ApiResponseHelper::success('Firebase token generated.', ['token' => $token]);
    }
}
