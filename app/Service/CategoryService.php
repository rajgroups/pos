<?php

namespace App\Services;

use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryService
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function storeCategory(array $validated, $image = null)
    {
        try {
            DB::beginTransaction();

            if ($image) {
                $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/category'), $imageName);
                $validated['image'] = $imageName;
            }

            $this->categoryRepo->create($validated);

            DB::commit();
            return ['success' => true, 'message' => 'Category Added Successfully'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Category Add Failed: '.$e->getMessage()];
        }
    }
}
