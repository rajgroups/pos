<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Helpers\GeneralHelper;
use App\Helpers\KeywordHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    /**
     * Authenticate user using mobile number
     *
     * Validate mobile number,
     * verify user existence,
     * generate OTP,
     * and return API response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
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

        // Initialize user service
        $userService = app(UserService::class);

        // Find user by mobile number
        $userExist = $userService->findByMobile(
            $request->mobile
        );

        // Return error if user does not exist
        if (empty($userExist)) {

            return ApiResponseHelper::error(
                __('string.common.no_user_found'),
                [],
                404
            );
        }

        // Generate 4 digit OTP
        $otp = GeneralHelper::OtpGenerator(
            true,
            4
        );
        // Insert otp for the user
        $userService->updateUser(
            $userExist['id'],
            [
                'otp' => $otp
            ]
        );

        // Return success response with OTP
        return ApiResponseHelper::success(
            __('string.common.user_found'),
            [
                'otp' => $otp
            ],
            200
        );
    }

    /**
     * Verify user login OTP
     *
     * Validate request data,
     * verify user existence,
     * check OTP validity,
     * generate access token,
     * and return authenticated response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        /**
         * Validate request data
         */
        $validated = ValidationHelper::ValidateOtp(
            $request
        );

        /**
         * Return validation error response
         */
        if ($validated['status'] == KeywordHelper::ERROR) {

            return ApiResponseHelper::error(
                $validated['message'],
                $validated['errors']
            );
        }

        /**
         * Initialize user service
         */
        $userService = app(UserService::class);

        /**
         * Find user by mobile number
         */
        $user = $userService->findByMobile(
            $request->mobile
        );

        /**
         * Return error if user not found
         */
        if (empty($user)) {

            return ApiResponseHelper::error(
                __('string.common.no_user_found'),
                [],
                404
            );
        }

        /**
         * Verify OTP
         */
        if ($user->otp != $request->otp) {

            return ApiResponseHelper::error(
                __('string.common.invalid_otp'),
                [],
                422
            );
        }

        /**
         * Clear OTP after successful verification
         */
        $userService->updateUser(
            $user->id,
             [
                'otp' => null
            ]
        );

        /**
         * Generate Sanctum token
         */
        $token = $user->createToken('auth_token')
                    ->plainTextToken;

        /**
         * Compress the response
         */
        $formatData = [
            'id' => $user->id,
            'name' => $user->name ?? null,
            'email' => $user->email ?? null,
            'mobile' => $user->mobile ?? null,
        ];

        /**
         * Return authenticated response
         */
        return ApiResponseHelper::success(
            __('string.common.login_success'),
            [
                'token' => $token,
                'user'  => $formatData
            ],
            200
        );
    }

    /**
     * Update user device token for FCM push notifications.
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        $fcmToken = $request->input('fcm_token') ?? $request->input('device_token');

        if (empty($fcmToken)) {
            return ApiResponseHelper::error('FCM token is required.', null, 422);
        }

        $user->update(['device_token' => $fcmToken]);

        return ApiResponseHelper::success('Device FCM token updated successfully.', [
            'user_id' => $user->id,
            'device_token' => $user->device_token,
        ]);
    }

    /**
     * Logout user and revoke Sanctum access token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return ApiResponseHelper::success('Logged out successfully.', null, 200);
    }
}
