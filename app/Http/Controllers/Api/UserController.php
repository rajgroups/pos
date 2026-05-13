<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'mobile'   => 'nullable|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->userService->createUser($validated);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'nullable|string|max:255',
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'mobile'   => 'nullable|string|max:20|unique:users,mobile,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $user = $this->userService->updateUser($id, $validated);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}
