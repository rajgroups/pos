<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverAuthController extends Controller
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
            'message' => 'Driver login route is working.',
            'data' => [
                'login_type' => 'driver',
                'identifier' => $validated['email'] ?? $validated['phone'] ?? null,
            ],
        ]);
    }
}
