<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;

class CmsController extends Controller
{
    /**
     * Get a CMS page by its slug.
     */
    public function show($slug): JsonResponse
    {
        $page = CmsPage::where('slug', $slug)->where('status', true)->first();

        if (!$page) {
            return ApiResponseHelper::error('Page not found', [], 404);
        }

        return ApiResponseHelper::success('Page loaded successfully', [
            'title' => $page->title,
            'slug' => $page->slug,
            'content' => $page->content,
            'updated_at' => $page->updated_at->toIso8601String(),
        ]);
    }
}
