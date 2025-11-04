<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
        protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(Request $request)
    {
        // ✅ Validate request via Helper
        $validated = ValidationHelper::validateCategory($request);

        // If validation failed → it will already redirect, so continue only if array
        if ($validated instanceof \Illuminate\Http\RedirectResponse) {
            return $validated;
        }

        // ✅ Proceed with service
        $response = $this->categoryService->storeCategory($validated, $request->file('image'));

        if ($response['success']) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->with('error', $response['message']);
    }
}
