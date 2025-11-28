<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Models\category;
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

    public function create(){

        // Create Category
         $categories = category::all();
        return view('admin.category.create',compact('categories'));
    }

    public function store(Request $request)
    {
        // ✅ Validate request via Helper
        $validated = ValidationHelper::validateCategory($request->all());
        // dd('come');
        // If validation failed → it will already redirect, so continue only if array
        if ($validated instanceof \Illuminate\Http\RedirectResponse) {
            return $validated;
        }
        // dd($request->file('image'));

        // ✅ Proceed with service
        $response = $this->categoryService->storeCategory($validated, $request->file('image'));

        // dd($response);
        if ($response['success']) {
            return redirect()->back()->with('success', $response['message']);
        }

        return redirect()->back()->with('error', $response['message']);
    }
}
