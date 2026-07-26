<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppUpdateController extends Controller
{
    /**
     * Check update status for User App.
     *
     * GET /api/user/check-update?app_version=1.0.0&platform=android
     */
    public function checkUserApp(Request $request): JsonResponse
    {
        return $this->evaluateAppUpdate($request, 'user_app');
    }

    /**
     * Check update status for Driver App.
     *
     * GET /api/driver/check-update?app_version=1.0.0&platform=android
     */
    public function checkDriverApp(Request $request): JsonResponse
    {
        return $this->evaluateAppUpdate($request, 'driver_app');
    }

    /**
     * Evaluate version comparison and return update config payload.
     */
    protected function evaluateAppUpdate(Request $request, string $appGroup): JsonResponse
    {
        $currentVersion = $request->query('app_version', $request->query('version', '1.0.0'));
        $platform = strtolower($request->query('platform', 'android'));

        $latestVersion = AppSetting::get("{$appGroup}_latest_version", '1.0.0');
        $minVersion = AppSetting::get("{$appGroup}_min_version", '1.0.0');
        $forceUpdateFlag = AppSetting::get("{$appGroup}_force_update", '0');

        $urlAndroid = AppSetting::get("{$appGroup}_url_android", '');
        $urlIos = AppSetting::get("{$appGroup}_url_ios", '');
        $title = AppSetting::get("{$appGroup}_update_title", 'Update Available');
        $message = AppSetting::get("{$appGroup}_update_message", 'A new version of the app is available. Please update to continue.');

        $updateUrl = ($platform === 'ios') ? $urlIos : $urlAndroid;

        // Version comparisons
        $updateAvailable = version_compare($currentVersion, $latestVersion, '<');
        $belowMinVersion = version_compare($currentVersion, $minVersion, '<');

        // Force update is true if explicitly enabled OR current version is below mandatory minimum version
        $forceUpdate = ($forceUpdateFlag === '1' || strtolower($forceUpdateFlag) === 'true') || $belowMinVersion;

        return ApiResponseHelper::success('App update status checked.', [
            'update_available' => (bool) $updateAvailable,
            'force_update'     => (bool) $forceUpdate,
            'current_version'  => $currentVersion,
            'latest_version'   => $latestVersion,
            'min_version'      => $minVersion,
            'update_url'       => $updateUrl,
            'title'            => $title,
            'message'          => $message,
        ]);
    }
}
