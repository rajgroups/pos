<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile details.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        return ApiResponseHelper::success('Profile fetched successfully.', [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'country_code' => $user->country_code,
            'gender' => $user->gender,
            'date_of_birth' => $user->date_of_birth,
            'profile_image' => $user->profile_image,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'postal_code' => $user->postal_code,
            'emergency_contact_name' => $user->emergency_contact_name,
            'emergency_contact_mobile' => $user->emergency_contact_mobile,
            'emergency_contact_relation' => $user->emergency_contact_relation,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'rating' => 4.85, // Default/calculated user rating
            'created_at' => $user->created_at?->toIso8601String(),
        ]);
    }

    /**
     * Update user profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:191',
            'first_name' => 'nullable|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191|unique:users,email,' . $user->id,
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:191',
            'emergency_contact_mobile' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed.', $validator->errors()->toArray(), 422);
        }

        $data = array_filter($validator->validated(), fn ($val) => $val !== null);

        // If name is provided but first_name/last_name not provided, auto split
        if (isset($data['name']) && empty($data['first_name'])) {
            $parts = explode(' ', trim($data['name']), 2);
            $data['first_name'] = $parts[0] ?? null;
            $data['last_name'] = $parts[1] ?? null;
        }

        $user->update($data);

        return ApiResponseHelper::success('Profile updated successfully.', [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'country_code' => $user->country_code,
            'gender' => $user->gender,
            'date_of_birth' => $user->date_of_birth,
            'profile_image' => $user->profile_image,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'postal_code' => $user->postal_code,
            'emergency_contact_name' => $user->emergency_contact_name,
            'emergency_contact_mobile' => $user->emergency_contact_mobile,
            'emergency_contact_relation' => $user->emergency_contact_relation,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'rating' => 4.85,
            'updated_at' => $user->updated_at?->toIso8601String(),
        ]);
    }
}
