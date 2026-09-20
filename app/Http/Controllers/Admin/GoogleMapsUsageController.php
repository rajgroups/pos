<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleMapsUsageService;
use Illuminate\Http\Request;

class GoogleMapsUsageController extends Controller
{
    public function __construct(protected GoogleMapsUsageService $usageService) {}

    /**
     * Main dashboard view.
     */
    public function index()
    {
        $configStatus = $this->usageService->getConfigStatus();
        return view('admin.google_maps.usage', compact('configStatus'));
    }

    /**
     * AJAX: Get summary data for cards.
     */
    public function summary()
    {
        return response()->json($this->usageService->getDashboardSummary());
    }

    /**
     * AJAX: Get daily chart data.
     */
    public function daily(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $days = in_array($days, [7, 30, 90]) ? $days : 30;

        return response()->json($this->usageService->getDailyUsage($days));
    }

    /**
     * AJAX: Get API breakdown table data.
     */
    public function apis()
    {
        return response()->json([
            'breakdown' => $this->usageService->getApiBreakdown(),
            'api_keys' => $this->usageService->getUsageByApiKey(),
        ]);
    }

    /**
     * POST: Refresh (clear cache and re-fetch).
     */
    public function refresh()
    {
        $this->usageService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared. Dashboard data will refresh on next load.',
        ]);
    }
}
