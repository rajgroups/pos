@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-sm-0">Cache & Optimization</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-box me-2"></i>Application Cache</h5>
            </div>
            <div class="card-body">
                <p>Remove cached application data.</p>
                <button class="btn btn-primary w-100" onclick="clearCache('application')">Clear App Cache</button>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-settings me-2"></i>Configuration Cache</h5>
            </div>
            <div class="card-body">
                <p>Clear the configuration cache.</p>
                <button class="btn btn-success w-100" onclick="clearCache('config')">Clear Config Cache</button>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-route me-2"></i>Route Cache</h5>
            </div>
            <div class="card-body">
                <p>Clear the route cache.</p>
                <button class="btn btn-info w-100 text-white" onclick="clearCache('route')">Clear Route Cache</button>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-photo me-2"></i>View Cache</h5>
            </div>
            <div class="card-body">
                <p>Clear compiled view files.</p>
                <button class="btn btn-warning w-100 text-white" onclick="clearCache('view')">Clear View Cache</button>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-code me-2"></i>Compiled Files</h5>
            </div>
            <div class="card-body">
                <p>Remove compiled class files.</p>
                <button class="btn btn-secondary w-100" onclick="clearCache('compiled')">Clear Compiled</button>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-rocket me-2"></i>Optimize</h5>
            </div>
            <div class="card-body">
                <p>Cache config, routes, and views for performance.</p>
                <button class="btn btn-dark w-100" onclick="clearCache('optimize')">Optimize System</button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card bg-dark text-light">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-terminal me-2"></i>Terminal Output</h5>
            </div>
            <div class="card-body">
                <pre id="terminal-output" style="color: #0f0; background: #000; padding: 15px; border-radius: 5px; min-height: 150px; max-height: 300px; overflow-y: auto;">Awaiting command...</pre>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    function clearCache(type) {
        const terminal = $('#terminal-output');
        terminal.text('Running command...\n');
        
        $.ajax({
            url: '{{ route('admin.developer.cache.clear') }}',
            type: 'POST',
            data: {
                _token: csrfToken,
                type: type
            },
            success: function(response) {
                if (response.status === 'SUCCESS') {
                    terminal.text('✓ Operation completed successfully.\n\n' + response.output);
                } else {
                    terminal.text('✕ Operation failed.\n\n' + response.output);
                }
            },
            error: function(xhr) {
                terminal.text('Error executing command:\n' + xhr.responseText);
            }
        });
    }
</script>
@endpush
