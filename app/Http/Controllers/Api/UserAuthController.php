<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User login route is working.',
            'data' => [
                'login_type' => 'user',
                'identifier' => $validated['email'] ?? $validated['phone'] ?? null,
            ],
        ]);
    }
}
