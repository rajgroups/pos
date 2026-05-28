<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
   protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('admin.driver.index', compact('users'));
    }

    public function create()
    {
        return view('admin.driver.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'mobile'   => 'nullable|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8',
        ]);

        $this->userService->createUser($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return view('admin.driver.show', compact('user'));
    }

    public function edit($id)
    {
        $user = $this->userService->getUserById($id);
        return view('admin.driver.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'     => 'nullable|string|max:255',
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'mobile'   => 'nullable|string|max:20|unique:users,mobile,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $this->userService->updateUser($id, $validated);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}
