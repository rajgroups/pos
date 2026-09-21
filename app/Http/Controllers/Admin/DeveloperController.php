<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeveloperActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeveloperController extends Controller
{
    /**
     * Helper to log actions.
     */
    protected function logAction($action, $command, $status, $output, $errorOutput = null)
    {
        $admin = Auth::guard('admin')->user();
        DeveloperActionLog::create([
            'admin_user_id' => $admin ? $admin->id : null,
            'admin_name' => $admin ? $admin->name : 'System',
            'action' => $action,
            'command' => $command,
            'status' => $status,
            'output' => $output,
            'error_output' => $errorOutput,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'started_at' => now(),
            'completed_at' => now(),
        ]);
    }

    /**
     * System Overview
     */
    public function index()
    {
        $systemInfo = [
            'app_name' => config('app.name'),
            'laravel_version' => app()->version(),
            'php_version' => phpversion(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'app_url' => config('app.url'),
            'timezone' => config('app.timezone'),
        ];

        try {
            DB::connection()->getPdo();
            $dbStatus = 'Connected';
        } catch (\Exception $e) {
            $dbStatus = 'Disconnected';
        }

        $databaseInfo = [
            'driver' => config('database.default'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'status' => $dbStatus,
        ];

        $cacheInfo = [
            'driver' => config('cache.default'),
        ];

        $queueInfo = [
            'driver' => config('queue.default'),
        ];

        $redisConfigured = config('database.redis.default.host') !== null;
        $redisStatus = 'Not Configured';
        if ($redisConfigured) {
            try {
                \Illuminate\Support\Facades\Redis::ping();
                $redisStatus = 'Connected';
            } catch (\Exception $e) {
                $redisStatus = 'Disconnected';
            }
        }

        $serverInfo = [
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'sapi' => php_sapi_name(),
            'time' => now()->toDateTimeString(),
        ];

        $storageDisk = config('filesystems.default');
        $storageWritable = is_writable(storage_path()) ? 'Writable' : 'Not Writable';
        $diskFreeSpace = @disk_free_space(storage_path());
        $freeSpaceHuman = $diskFreeSpace ? number_format($diskFreeSpace / 1024 / 1024 / 1024, 2) . ' GB' : 'Unknown';

        $storageInfo = [
            'disk' => $storageDisk,
            'writable' => $storageWritable,
            'free_space' => $freeSpaceHuman,
        ];

        return view('admin.developer.index', compact(
            'systemInfo', 'databaseInfo', 'cacheInfo', 'queueInfo', 'redisConfigured', 'redisStatus', 'serverInfo', 'storageInfo'
        ));
    }

    /**
     * Migrations Page
     */
    public function migrations()
    {
        $migrations = DB::table('migrations')->orderBy('id', 'desc')->get();
        return view('admin.developer.migrations', compact('migrations'));
    }

    /**
     * Run Migrations
     */
    public function runMigration(Request $request)
    {
        $type = $request->input('type');
        $command = '';
        $params = [];

        if ($type === 'fresh' && $request->input('confirm') !== 'FRESH') {
            return response()->json([
                'status' => 'FAILED',
                'output' => 'Confirmation failed. Type FRESH to confirm.',
            ]);
        }
        if ($type === 'fresh_seed' && $request->input('confirm') !== 'FRESH') {
            return response()->json([
                'status' => 'FAILED',
                'output' => 'Confirmation failed. Type FRESH to confirm.',
            ]);
        }

        switch ($type) {
            case 'migrate':
                $command = 'migrate';
                $params = ['--force' => true];
                break;
            case 'rollback':
                $command = 'migrate:rollback';
                $params = ['--force' => true];
                break;
            case 'fresh':
                $command = 'migrate:fresh';
                $params = ['--force' => true];
                break;
            case 'fresh_seed':
                $command = 'migrate:fresh';
                $params = ['--force' => true, '--seed' => true];
                break;
            case 'seed':
                $command = 'db:seed';
                $params = ['--force' => true];
                break;
            default:
                return response()->json([
                    'status' => 'FAILED',
                    'output' => 'Invalid migration command.',
                ]);
        }

        try {
            Artisan::call($command, $params);
            $output = Artisan::output();
            $this->logAction('Migration', $command, 'SUCCESS', $output);
            return response()->json([
                'status' => 'SUCCESS',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            $this->logAction('Migration', $command, 'FAILED', null, $e->getMessage());
            return response()->json([
                'status' => 'FAILED',
                'output' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cache Page
     */
    public function cache()
    {
        return view('admin.developer.cache');
    }

    /**
     * Run Cache Clear
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type');
        $command = '';

        switch ($type) {
            case 'application':
                $command = 'cache:clear';
                break;
            case 'config':
                $command = 'config:clear';
                break;
            case 'route':
                $command = 'route:clear';
                break;
            case 'view':
                $command = 'view:clear';
                break;
            case 'compiled':
                $command = 'clear-compiled';
                break;
            case 'optimize':
                $command = 'optimize';
                break;
            default:
                return response()->json([
                    'status' => 'FAILED',
                    'output' => 'Invalid cache command.',
                ]);
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();
            $this->logAction('Cache', $command, 'SUCCESS', $output);
            return response()->json([
                'status' => 'SUCCESS',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            $this->logAction('Cache', $command, 'FAILED', null, $e->getMessage());
            return response()->json([
                'status' => 'FAILED',
                'output' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Logs Page
     */
    public function logs(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];
        
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $pattern = "/^\[(?P<date>.*)\] (?P<env>\w+)\.(?P<type>\w+): (?P<message>.*)/m";
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            
            $matches = array_slice(array_reverse($matches), 0, 500);
            $levelFilter = strtolower($request->query('level', 'all'));

            foreach ($matches as $match) {
                if ($levelFilter !== 'all' && strtolower($match['type']) !== $levelFilter) {
                    continue;
                }
                $logs[] = [
                    'timestamp' => $match['date'],
                    'env' => $match['env'],
                    'level' => $match['type'],
                    'message' => $match['message'],
                ];
            }
        }

        return view('admin.developer.logs', compact('logs'));
    }

    /**
     * Clear Log
     */
    public function clearLog()
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
            $this->logAction('Log', 'clear', 'SUCCESS', 'Laravel log cleared.');
        }
        return redirect()->route('admin.developer.logs')->with('success', 'Log cleared successfully.');
    }

    /**
     * Artisan Page
     */
    public function artisan()
    {
        $allowedCommands = [
            'about' => 'Display basic information about your application',
            'route:list' => 'List all registered routes',
            'migrate:status' => 'Show the status of each migration',
            'config:show' => 'Display all of the values for a given configuration file',
            'queue:monitor' => 'Monitor the sizes of the specified queues',
            'schedule:list' => 'List the scheduled commands',
            'optimize:status' => 'Show the cache status of the application',
        ];

        return view('admin.developer.artisan', compact('allowedCommands'));
    }

    /**
     * Run Artisan Command
     */
    public function runArtisan(Request $request)
    {
        $command = $request->input('command');
        $allowedCommands = [
            'about', 'route:list', 'migrate:status', 'config:show', 
            'queue:monitor', 'schedule:list', 'optimize:status'
        ];

        if (!in_array($command, $allowedCommands)) {
            return response()->json([
                'status' => 'FAILED',
                'output' => 'Command not allowed.',
            ]);
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();
            $this->logAction('Artisan', $command, 'SUCCESS', $output);
            return response()->json([
                'status' => 'SUCCESS',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            $this->logAction('Artisan', $command, 'FAILED', null, $e->getMessage());
            return response()->json([
                'status' => 'FAILED',
                'output' => $e->getMessage(),
            ]);
        }
    }
}
