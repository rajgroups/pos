<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('admin.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'phone'    => 'nullable|string|max:20|unique:admins,phone',
            'password' => 'required|string|min:8',
            'status'   => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Admin::create($validated);

        return redirect()->route('admin.admin.index')
                         ->with('success', 'Admin created successfully.');
    }

    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admin.show', compact('admin'));
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admin.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email,' . $id,
            'phone'    => 'nullable|string|max:20|unique:admins,phone,' . $id,
            'password' => 'nullable|string|min:8',
            'status'   => 'required|in:active,inactive',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.admin.index')
                         ->with('success', 'Admin updated successfully.');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.admin.index')
                         ->with('success', 'Admin deleted successfully.');
    }
}
