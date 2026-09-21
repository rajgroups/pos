@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-sm-0">System Overview</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-app-window me-2"></i>Application</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th>App Name</th><td>{{ $systemInfo['app_name'] }}</td></tr>
                    <tr><th>Laravel Version</th><td>{{ $systemInfo['laravel_version'] }}</td></tr>
                    <tr><th>PHP Version</th><td>{{ $systemInfo['php_version'] }}</td></tr>
                    <tr><th>Environment</th><td><span class="badge bg-{{ $systemInfo['environment'] == 'production' ? 'success' : 'warning' }}">{{ $systemInfo['environment'] }}</span></td></tr>
                    <tr><th>Debug Mode</th><td><span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">{{ $systemInfo['debug_mode'] }}</span></td></tr>
                    <tr><th>Timezone</th><td>{{ $systemInfo['timezone'] }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-database me-2"></i>Database</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th>Driver</th><td>{{ $databaseInfo['driver'] }}</td></tr>
                    <tr><th>Database Name</th><td>{{ $databaseInfo['database'] }}</td></tr>
                    <tr><th>Connection Status</th><td>
                        <span class="badge bg-{{ $databaseInfo['status'] == 'Connected' ? 'success' : 'danger' }}">{{ $databaseInfo['status'] }}</span>
                    </td></tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-server me-2"></i>Server & Storage</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th>OS</th><td>{{ $serverInfo['os'] }}</td></tr>
                    <tr><th>Software</th><td>{{ $serverInfo['software'] }}</td></tr>
                    <tr><th>Time</th><td>{{ $serverInfo['time'] }}</td></tr>
                    <tr><th>Storage Disk</th><td>{{ $storageInfo['disk'] }}</td></tr>
                    <tr><th>Storage Writable</th><td><span class="badge bg-{{ $storageInfo['writable'] == 'Writable' ? 'success' : 'danger' }}">{{ $storageInfo['writable'] }}</span></td></tr>
                    <tr><th>Free Space</th><td>{{ $storageInfo['free_space'] }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-bolt me-2"></i>Cache & Queue</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th>Cache Driver</th><td>{{ $cacheInfo['driver'] }}</td></tr>
                    <tr><th>Queue Driver</th><td>{{ $queueInfo['driver'] }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-brand-redis me-2"></i>Redis</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th>Configured</th><td>{{ $redisConfigured ? 'Yes' : 'No' }}</td></tr>
                    <tr><th>Status</th><td>
                        <span class="badge bg-{{ $redisStatus == 'Connected' ? 'success' : ($redisStatus == 'Not Configured' ? 'secondary' : 'danger') }}">{{ $redisStatus }}</span>
                    </td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
