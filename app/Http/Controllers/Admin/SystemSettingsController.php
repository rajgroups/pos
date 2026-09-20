<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\IndicabModeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SystemSettingsController extends Controller
{
    public function __construct(protected IndicabModeService $modeService) {}

    public function mode()
    {
        $currentMode = $this->modeService->getMode();
        return view('admin.settings.mode', compact('currentMode'));
    }

    public function updateMode(Request $request)
    {
        $validated = $request->validate([
            'indicab_mode' => ['required', 'in:economy,prime'],
        ]);

        $mode = $validated['indicab_mode'];

        // If switching to prime, check if socket server is available
        if ($mode === 'prime') {
            $socketUrl = rtrim(config('services.socket.url', 'http://127.0.0.1:9502'), '/');
            try {
                // Perform a quick ping or check
                $response = Http::timeout(3)->get($socketUrl);
                // We expect a 404 or something because / doesn't exist, but it shouldn't throw connection refused
            } catch (\Exception $e) {
                Log::warning('Admin tried to switch to prime but socket server is unreachable.', [
                    'error' => $e->getMessage()
                ]);
                return back()->with('error', 'Cannot switch to Prime mode: Socket server is unreachable or not running.');
            }
        }

        $this->modeService->setMode($mode);

        return back()->with('success', 'System mode updated successfully.');
    }

    public function appSettings()
    {
        $settings = \App\Models\AppSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.app_settings', compact('settings'));
    }

    public function updateAppSettings(Request $request)
    {
        $validated = $request->validate([
            'user_app_latest_version' => 'required|string',
            'user_app_min_version' => 'required|string',
            'user_app_force_update' => 'required|boolean',
            'user_app_url_android' => 'required|url',
            'user_app_url_ios' => 'required|url',
            'user_app_update_title' => 'required|string',
            'user_app_update_message' => 'required|string',

            'driver_app_latest_version' => 'required|string',
            'driver_app_min_version' => 'required|string',
            'driver_app_force_update' => 'required|boolean',
            'driver_app_url_android' => 'required|url',
            'driver_app_url_ios' => 'required|url',
            'driver_app_update_title' => 'required|string',
            'driver_app_update_message' => 'required|string',

            'driver_waiting_time' => 'required|integer|min:1',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\AppSetting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'App settings updated successfully.');
    }
}
