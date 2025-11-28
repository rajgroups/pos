<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;

class ValidationHelper
{
        /**
     * ----------------------------------
     * GENERIC VALIDATION (Reusable)
     * ----------------------------------
     * Use this for other modules like Product, Blog, etc.
     */
    public static function validate($request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request, $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return $validator->validated();
    }
    /**
     * ----------------------------------
     * CATEGORY VALIDATION
     * ----------------------------------
     */
    public static function validateCategory($request, $isUpdate = false, $id = null)
    {
        $rules = [
            'name'   => 'required|unique:tbl_category,name' . ($isUpdate && $id ? ',' . $id : ''),
            'slug'   => 'required|unique:tbl_category,slug' . ($isUpdate && $id ? ',' . $id : ''),
            'status' => 'required|in:active,inactive',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $messages = [
            'name.required'   => 'Please enter category name.',
            'slug.required'   => 'Please enter category slug.',
            'status.required' => 'Please select a category status.',
            'status.in'       => 'Invalid status selected.',
            'image.image'     => 'The uploaded file must be an image.',
        ];

        return self::validate($request, $rules, $messages);
    }

    /**
     * ----------------------------------
     * USER VALIDATION
     * ----------------------------------
     */
    public static function validateUser($request, $isUpdate = false, $id = null)
    {
        $rules = [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:tbl_users,email' . ($isUpdate && $id ? ',' . $id : ''),
            'password' => $isUpdate ? 'nullable|min:6' : 'required|min:6',
        ];

        $messages = [
            'name.required'     => 'User name is required.',
            'email.required'    => 'Email address is required.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 6 characters.',
        ];

        return self::validate($request, $rules, $messages);
    }
}
