@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-sm-0">System Logs</h4>
            <div class="page-title-right">
                <form method="POST" action="{{ route('admin.developer.logs.clear') }}" onsubmit="return confirm('Are you sure you want to clear the Laravel log? This cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-2"></i>Clear Log</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.developer.logs') }}" method="GET" class="d-flex align-items-center gap-3">
                    <label class="fw-bold">Filter by Level:</label>
                    <select name="level" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="all" {{ request('level') == 'all' ? 'selected' : '' }}>All Levels</option>
                        <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                        <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>Debug</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-file-text me-2"></i>Laravel Log (Latest 500 lines)</h5>
            </div>
            <div class="card-body p-0">
                <pre style="color: #0f0; background: #000; padding: 15px; border-radius: 5px; min-height: 500px; max-height: 800px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; font-size: 14px;">@forelse($logs as $log)
[{{ $log['timestamp'] }}] {{ $log['env'] }}.{{ strtoupper($log['level']) }}: {{ $log['message'] }}
@empty
No logs found matching criteria.
@endforelse</pre>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Optional: Auto-scroll to bottom of logs if desired
    // $(document).ready(function() {
    //     var pre = $('pre');
    //     pre.scrollTop(pre.prop('scrollHeight'));
    // });
</script>
@endpush
