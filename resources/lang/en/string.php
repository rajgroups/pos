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
        'record_not_found' =>  'Record Not Found',
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

    'common' => [
        'not_found' => 'Record not found',
        'user_found' => 'User found',
        'no_user_found' => 'No user found with this mobile number'
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
    // BRAND MODULE
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


    // -------------------------------------------------
    // UNIT MODULE
    // -------------------------------------------------
    'unit' => [
        'created_success' => 'Unit created successfully.',
        'updated_success' => 'Unit updated successfully.',
        'deleted_success' => 'Unit deleted successfully.',
        'not_found'       => 'Unit not found.',

        'name_required' => 'Unit name is required.',
        'name_unique'   => 'This unit name already exists.',

        'shortname_required' => 'Short name is required.',
        'type_required' => 'Unit type is required.',
        'status_required' => 'Status is required.',

        'id_required' => 'Unit ID is required.',
        'id_not_found' => 'Unit not found.',
    ],


    // -------------------------------------------------
    // WARRANTY MODULE (New)
    // -------------------------------------------------
    'warranty' => [

        // Success messages
        'created_success' => 'Warranty created successfully.',
        'updated_success' => 'Warranty updated successfully.',
        'deleted_success' => 'Warranty deleted successfully.',
        'restored_success' => 'Warranty restored successfully.',
        'force_deleted_success' => 'Warranty permanently deleted.',

        // Errors
        'not_found' => 'Warranty not found.',
        'delete_failed' => 'Failed to delete warranty.',
        'restore_failed' => 'Failed to restore warranty.',
        'force_delete_failed' => 'Failed to permanently delete warranty.',

        // Validation Messages
        'warranty_required' => 'Warranty name is required.',
        'warranty_unique' => 'This warranty name already exists.',

        'type_required' => 'Warranty type is required.',

        'code_unique' => 'Warranty code must be unique.',

        'duration_required_unless' => 'Duration is required unless lifetime warranty is selected.',
        'period_required_unless' => 'Period is required unless lifetime warranty is selected.',

        'duration_invalid' => 'Duration must be a number greater than 0.',
        'period_invalid' => 'Invalid warranty period.',

        'start_after_invalid' => 'Start after must be 0 or a positive number.',

        'description_required' => 'Description is required.',
        'description_min' => 'Description must contain at least :min characters.',
        'description_max' => 'Description must not exceed :max characters.',

        'terms_max' => 'Terms and conditions must not exceed :max characters.',

        'max_claims_invalid' => 'Maximum claims must be at least 1.',

        // Form Helper Text
        'helper_name' => 'Enter a descriptive warranty name.',
        'helper_code' => 'Optional unique warranty reference code.',
        'helper_duration' => 'Enter numeric duration (e.g., 12, 24).',
        'helper_period' => 'Select a time period for warranty.',
        'helper_start_after' => 'Warranty will begin after these many days.',
        'helper_lifetime' => 'Enable for unlimited lifetime warranty.',
        'helper_max_claims' => 'Leave blank for unlimited claims.',
        'helper_replacement' => 'Enable if replacement under warranty is allowed.',
        'helper_description' => 'Describe warranty coverage details.',
        'helper_terms' => 'Add rules, terms, and limitations for this warranty.',
    ],

    // inside 'warehouses' or 'warehouse' group
        'warehouse' => [
            'created_success' => 'Warehouse created successfully.',
            'updated_success' => 'Warehouse updated successfully.',
            'deleted_success' => 'Warehouse deleted successfully.',
            'not_found' => 'Warehouse not found.',
            'delete_failed' => 'Failed to delete warehouse.',
            // validation helpers
            'name_required' => 'Warehouse name is required.',
            'code_required' => 'Warehouse code is required.',
            'code_unique' => 'Warehouse code must be unique.',
            'address_required' => 'Address is required.',
        ],

];
