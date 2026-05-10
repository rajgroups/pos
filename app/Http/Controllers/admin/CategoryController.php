<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\KeywordHelper;
use App\Helpers\NotifyHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $categorys = $this->service->getAll();
        return view('admin.category.list', compact('categorys'));
    }

    public function create()
    {
        $categories = $this->service->getAll();
        return view('admin.category.create', compact('categories'));
    }

    public function store(Request $request)
    {

        $validate = ValidationHelper::validateCategory($request->all());
        // dd($validate);
        if ($validate['status'] === KeywordHelper::ERROR) {
            // show the actual validation message (already localized if set up)
            NotifyHelper::errorMessage($validate['message']);
            return back()->withInput(); // <-- This fixes old()
        }

        $this->service->create($request->all());

        NotifyHelper::success('category.created_success'); // key-based success
        return back();
    }

    public function edit($id)
    {
        $validate = ValidationHelper::validateCategoryExist($id);

        if($validate['status'] == KeywordHelper::ERROR){
            NotifyHelper::errorMessage($validate['message']);
            return redirect()->route('admin.category.index');
        }

        $category = $this->service->getById($id);
        $categories = $this->service->getAll();

        return view('admin.category.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = ValidationHelper::validateCategory($request->all(), true, $id);

        if ($validated['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validated['message']);
            return back()->withInput();
        }

        // Send only data array to service
        $updated = $this->service->update($id, $validated['data']);

        if ($updated) {
            NotifyHelper::successMessage(__('string.category.updated_success'));
            return redirect()->route('admin.category.index');
        }

        NotifyHelper::errorMessage(__('string.something_wrong'));
        return back();
    }

    public function destroy($id)
    {
        $validate = ValidationHelper::validateCategoryExist($id);

        if ($validate['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validate['message']);
            return back();
        }

        $this->service->delete($id);

        // notyf()->success(__('string.category.deleted_success'));
        NotifyHelper::success('category.deleted_success'); // key-based success
        return back();
    }

}
