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
}
