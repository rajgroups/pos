<?php

namespace App\Http\Controllers\admin;

use App\Helpers\KeywordHelper;
use App\Helpers\NotifyHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $service;

    public function __construct(BrandService $brandService) {
        $this->service = $brandService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = $this->service->all();
        return view('admin.brand.list',compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // create new Brand form
        return view('admin.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // create new brand record
        $validator = ValidationHelper::validateBrand($request->all());

        if($validator['status'] == KeywordHelper::ERROR){
            NotifyHelper::errorMessage($validator[KeywordHelper::MESSAGE]);
            return back()->withInput();
        };

        $this->service->create($request->all());

        NotifyHelper::success('brand.created_success');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $validate = ValidationHelper::validateBrandExist($id);

        if($validate['status'] == KeywordHelper::ERROR){
            NotifyHelper::errorMessage($validate['message']);
            return redirect()->route('admin.category.index');
        }

        $brand = $this->service->getById($id);
        return view('admin.brand.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Check Brand Exists
        $validate = ValidationHelper::validateBrandExist($id);

        if ($validate['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validate['message']);
            return redirect()->route('admin.brand.index');
        }

        // Validate Data
        $validator = ValidationHelper::validateBrand(
            $request->all(),
            isUpdate: true,
            id: $id
        );

        if ($validator['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validator[KeywordHelper::MESSAGE]);
            return back()->withInput();
        }

        // Update Brand
        $this->service->update($id, $request->all());

        NotifyHelper::success('brand.updated_success');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check Brand Exists
        $validate = ValidationHelper::validateBrandExist($id);

        if ($validate['status'] == KeywordHelper::ERROR) {
            NotifyHelper::errorMessage($validate['message']);
            return redirect()->route('admin.brand.index');
        }

        // Delete Brand
        $this->service->delete($id);

        NotifyHelper::successMessage('brand.deleted_success');
        return redirect()->route('admin.brand.index');
    }

}
