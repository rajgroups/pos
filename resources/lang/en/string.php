<?php

return [
    // -------------------------------------------------
    // GENERAL WORDS (Common text)
    // -------------------------------------------------
    'general' => [
        'save'          => 'Save',
        'update'        => 'Update',
        'delete'        => 'Delete',
        'status'        => 'Status',
        'active'        => 'Active',
        'inactive'      => 'Inactive',
        'record_not_found' =>  'Record Found',
    ],
    // -------------------------------------------------
    // CATEGORY MODULE
    // -------------------------------------------------
    'category' => [
        'created_success' => 'Category created successfully.',
        'updated_success' => 'Category updated successfully.',
        'deleted_success' => 'Category deleted successfully.',

        'name_required' => 'Category name is required.',
        'name_unique'   => 'This category name already exists.',

        'slug_required' => 'Slug is required.',
        'slug_unique'   => 'This slug already exists.',

        'status_required' => 'Status is required.',

        'image_invalid'     => 'Please upload a valid image file.',
        'image_extension'   => 'Only JPG, JPEG, PNG, and WEBP formats are allowed.',
        'max_file_size'     => 'Image size must not exceed 2MB.',

        'id_required' => 'Category ID is required.',
        'id_not_found' => 'Category not found.',
    ],



    // -------------------------------------------------
    // PRODUCT MODULE
    // -------------------------------------------------
    'product' => [
        'name_required' => 'Product name is required.',
        'price_required' => 'Price is required.',
        'created_success' => 'Product created successfully.',
    ],


    // -------------------------------------------------
    // AUTH / LOGIN / USERS
    // -------------------------------------------------
    'auth' => [
        'login_failed' => 'Invalid email or password.',
        'logout_success' => 'Logged out successfully.',
    ],

    // -------------------------------------------------
    // Brand
    // -------------------------------------------------

    'brand' => [
        'created_success' => 'Brand created successfully.',
        'updated_success' => 'Brand updated successfully.',
        'deleted_success' => 'Brand deleted successfully.',

        'name_required' => 'Brand name is required.',
        'name_unique'   => 'This brand name already exists.',

        'slug_required' => 'Slug is required.',
        'slug_unique'   => 'This slug already exists.',

        'status_required' => 'Status is required.',

        'image_required'  => 'Brand image is required.',
        'image_invalid'   => 'Please upload a valid image.',
        'image_extension' => 'Only JPG, JPEG, PNG, and WEBP formats are allowed.',
        'image_max'       => 'Image size must not exceed 5MB.',

        'icon_required'  => 'Brand icon is required.',
        'icon_invalid'   => 'Please upload a valid icon.',
        'icon_extension' => 'Only JPG, JPEG, PNG, and WEBP formats are allowed.',
        'icon_max'       => 'Icon size must not exceed 5MB.',

        'id_required' => 'Brand ID is required.',
        'id_not_found' => 'Brand not found.',
    ],


];
