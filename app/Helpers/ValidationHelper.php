<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;

class ValidationHelper
{
    /**
     * GENERIC VALIDATION
     * Returns:
     *  - status: success/error
     *  - message: first error
     *  - errors: all errors (optional)
     */
    public static function validate(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return [
                KeywordHelper::STATUS  => KeywordHelper::ERROR,
                KeywordHelper::MESSAGE => $validator->errors()->first(),
                KeywordHelper::ERRORS  => $validator->errors()->messages(),
            ];
        }

        return [
            KeywordHelper::STATUS => KeywordHelper::SUCCESS,
            KeywordHelper::DATA   => $validator->validated(),
        ];
    }


    /**
     * CATEGORY CREATE/UPDATE VALIDATION
     */
    public static function validateCategory(array $data, $isUpdate = false, $id = null)
    {
        $rules = [
            'name'      => 'required|unique:category,name' . ($isUpdate ? ',' . $id : ''),
            'slug'      => 'required|unique:category,slug' . ($isUpdate ? ',' . $id : ''),
            'status'    => 'required|in:0,1',
            'parent_id' => 'required'

            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'icon'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        $messages = [
            // Name
            'name.required' => __('string.category.name_required'),
            'name.unique'   => __('string.category.name_unique'),

            // Slug
            'slug.required' => __('string.category.slug_required'),
            'slug.unique'   => __('string.category.slug_unique'),

            // Status
            'status.required' => __('string.category.status_required'),

            // Image messages
            'image.image'  => __('string.category.image_invalid'),
            'image.mimes'  => __('string.category.image_extension'),
            'image.max'    => __('string.category.max_file_size'),

            // Icon messages
            'icon.image'  => __('string.category.image_invalid'),
            'icon.mimes'  => __('string.category.image_extension'),
            'icon.max'    => __('string.category.max_file_size'),
        ];

        return self::validate($data, $rules, $messages);
    }



    /**
     * CHECK CATEGORY EXIST
     */
    public static function validateCategoryExist($id)
    {
        $rules = [
            'id' => 'required|exists:category,id',
        ];

        $messages = [
            'id.required' => __('string.category.id_required'),
            'id.exists'   => __('string.category.id_not_found'),
        ];

        return self::validate(['id' => $id], $rules, $messages);
    }

    /*
    * Brand Validation
    */

    public static function validateBrand(array $data, $isUpdate = false, $id = null){

        $rules = [
            'name'      => 'required|unique:brand,name'.($isUpdate ? ',' .$id : ''),
            'slug'      => 'required|unique:brand,slug'.($isUpdate ? ','.$id : ''),

            'status'    => 'required|in:0,1',

            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'icon'      => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        $messages = [
            // Name
            'name.required' => __('string.brand.name_required'),
            'name.unique'   => __('string.brand.name_unique'),

            // Slug
            'slug.required' => __('string.brand.slug_required'),
            'slug.unique'   => __('string.brand.slug_unique'),

            // Status
            'status.required' => __('string.brand.status_required'),

            // Image
            'image.required' => __('string.brand.image_required'),
            'image.image'    => __('string.brand.image_invalid'),
            'image.mimes'    => __('string.brand.image_extension'),
            'image.max'      => __('string.brand.image_max'),

            // Icon
            'icon.required' => __('string.brand.icon_required'),
            'icon.image'    => __('string.brand.icon_invalid'),
            'icon.mimes'    => __('string.brand.icon_extension'),
            'icon.max'      => __('string.brand.icon_max'),
        ];

        return self::validate($data, $rules ,$messages);
    }

    /**
     * CHECK CATEGORY EXIST
     */
    public static function validateBrandExist($id)
    {
        $rules = [
            'id' => 'required|exists:brand,id',
        ];

        $messages = [
            'id.required' => __('string.brand.id_required'),
            'id.exists'   => __('string.category.id_not_found'),
        ];

        return self::validate(['id' => $id], $rules, $messages);
    }
}
