<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\KeywordHelper;
use App\Helpers\NotifyHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\Models\VehicleCategory;

class CategoryController extends Controller
{
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    private function getCategoryOptions($excludeId = null)
    {
        $tree = VehicleCategory::whereNull('parent_id')->orWhere('parent_id', 0)->with('childrenRecursive')->orderBy('sort_order')->get();
        return $this->buildCategoryOptions($tree, '', $excludeId);
    }

    private function buildCategoryOptions($categories, $prefix = '', $excludeId = null)
    {
        $options = [];
        foreach ($categories as $category) {
            if ($excludeId && $category->id == $excludeId) {
                continue;
            }
            $options[$category->id] = $prefix . $category->name;
            if ($category->childrenRecursive->isNotEmpty()) {
                $options += $this->buildCategoryOptions($category->childrenRecursive, $prefix . '— ', $excludeId);
            }
        }
        return $options;
    }

    private function buildCategoryTreeList($categories, $level = 0)
    {
        $list = collect();
        foreach ($categories as $category) {
            $category->level = $level;
            $category->prefix = str_repeat('— ', $level);
            $list->push($category);
            if ($category->childrenRecursive->isNotEmpty()) {
                $list = $list->merge($this->buildCategoryTreeList($category->childrenRecursive, $level + 1));
            }
        }
        return $list;
    }

    public function index()
    {
        $treeCategories = VehicleCategory::whereNull('parent_id')->orWhere('parent_id', 0)->with('childrenRecursive')->orderBy('sort_order')->get();
        $categorys = $this->buildCategoryTreeList($treeCategories);
        return view('admin.category.list', compact('categorys', 'treeCategories'));
    }

    public function create()
    {
        $categories = $this->getCategoryOptions();
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

        $this->service->create($validate['data']);

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
        $categories = $this->getCategoryOptions($id);

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
