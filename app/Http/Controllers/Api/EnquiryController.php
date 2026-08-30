<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnquiryController extends Controller
{
    /**
     * Store a new partner enquiry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'mobile'       => 'required|string|digits:10',
            'contact_note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error(
                'Validation failed.',
                $validator->errors()->toArray(),
                422
            );
        }

        try {
            Enquiry::create([
                'name'         => $request->input('name'),
                'mobile'       => $request->input('mobile'),
                'contact_note' => $request->input('contact_note'),
            ]);

            return ApiResponseHelper::success(
                'Your enquiry has been submitted successfully. We will contact you soon!',
                [],
                201
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                'Something went wrong. Please try again.',
                [],
                500
            );
        }
    }
}
