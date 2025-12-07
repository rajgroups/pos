<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ValidationHelper;
use App\Helpers\NotifyHelper;
use App\Models\Store;
use App\Services\StoreService;

class StoreController extends Controller
{
    protected $service;

    public function __construct(StoreService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'search'  => $request->search,
            'status'  => $request->status,
            'trashed' => $request->trashed,
        ];

        $stores = $this->service->getAll(15, $filters);

        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        // 1) Validate using ValidationHelper + Model rules
        $validate = ValidationHelper::validate(
            $request->all(),
            Store::validationRules()
        );

        if ($validate['status'] === 'error') {
            NotifyHelper::errorMessage($validate['message']);
            return back()->withInput()->withErrors($validate['errors']);
        }

        $data = $validate['data'];

        // 2) Attach uploaded files (service handles uploading)
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo');
        }

        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner');
        }

        if ($request->hasFile('gallery')) {
            $data['gallery'] = $request->file('gallery');
        }

        // 3) Create store via service
        $this->service->create($data);

        NotifyHelper::successMessage('Store created successfully!');
        return redirect()->route('admin.stores.index');
    }


    public function edit($id)
    {
        $store = $this->service->getById($id);

        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, $id)
    {
        // Validate using shared Model rules
        $validate = ValidationHelper::validate(
            $request->all(),
            Store::validationRules($id)
        );

        if ($validate['status'] === 'error') {
            NotifyHelper::errorMessage($validate['message']);
            return back()->withInput()->withErrors($validate['errors']);
        }

        $data = $validate['data'];

        // Handle new logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner');
        }

        /**
         * GALLERY HANDLING
         * - User can upload NEW images
         * - User can keep OLD images (e.g. hidden input: gallery_keep[])
         */
        $gallery = [];

        // keep old gallery images
        if ($request->has('gallery_keep')) {
            $gallery = array_filter($request->gallery_keep);
        }

        // new uploads
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file; // service will upload
            }
        }

        // if gallery exists, pass to service
        if (!empty($gallery)) {
            $data['gallery'] = $gallery;
        }

        // Update store via service
        $this->service->update($id, $data);

        NotifyHelper::successMessage('Store updated successfully!');
        return redirect()->route('admin.stores.index');
    }


    public function destroy($id)
    {
        $this->service->delete($id);
        NotifyHelper::successMessage('Store deleted successfully!');
        return redirect()->route('admin.stores.index');
    }

    public function restore($id)
    {
        $this->service->restore($id);
        NotifyHelper::successMessage('Store restored successfully!');
        return redirect()->route('admin.stores.index');
    }

    public function forceDelete($id)
    {
        $this->service->forceDelete($id);
        NotifyHelper::successMessage('Store permanently deleted!');
        return redirect()->route('admin.stores.index');
    }
}
