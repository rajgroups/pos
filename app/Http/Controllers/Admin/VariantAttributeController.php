<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\KeywordHelper;
use App\Http\Controllers\Controller;
use App\Models\VariantAttribute;
use App\Services\VariantAttributeService;
use App\Helpers\ValidationHelper;
use App\Helpers\NotifyHelper;
use Illuminate\Http\Request;

class VariantAttributeController extends Controller
{
    protected $service;

    public function __construct(VariantAttributeService $service)
    {
        $this->service = $service;
    }

    /**
     * List all variant attributes
     */
    public function index()
    {
        $variants = $this->service->all();
        return view('admin.variantattributes.index', compact('variants'));
    }

    /**
     * Show the form for creating
     */
    public function create()
    {
        return view('admin.variantattributes.create');
    }

    /**
     * Store new variant
     */
    public function store(Request $request)
    {
        $validate = ValidationHelper::validate(
            $request->all(),
            VariantAttribute::validationRules()
        );

        if ($validate['status'] === KeywordHelper::ERROR) {

            // Toast for the first validation message
            NotifyHelper::errorMessage($validate['message']);

            return back()->withErrors($validate['errors'])->withInput();
        }

        $this->service->store($validate['data']);

        NotifyHelper::success('variant_attribute.created_success');

        return redirect()->route('admin.variant-attributes.index');
    }


    /**
     * Edit
     */
    public function edit($id)
    {
        $variant = $this->service->find($id);

        if (!$variant) {
            NotifyHelper::errorMessage("Variant Attribute not found!");
            return back();
        }

        return view('admin.variantattributes.edit', compact('variant'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $validate = ValidationHelper::validate(
            $request->all(),
            VariantAttribute::validationRules($id)
        );

        if ($validate['status'] === KeywordHelper::ERROR) {

            // 🔥 Toast for first validation error
            NotifyHelper::errorMessage($validate['message']);

            return back()->withErrors($validate['errors'])->withInput();
        }

        $this->service->update($id, $validate['data']);

        // 🔥 Success toast (localized)
        NotifyHelper::success('variant_attribute.updated_success');

        return redirect()->route('admin.variant-attributes.index');
    }


    /**
     * Delete
     */
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            NotifyHelper::errorMessage("Failed to delete Variant Attribute!");
            return back();
        }

        NotifyHelper::successMessage("Variant Attribute deleted successfully!");
        return back();
    }
}
