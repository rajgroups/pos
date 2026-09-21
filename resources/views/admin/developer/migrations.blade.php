@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-sm-0">Migrations Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-database me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="runMigration('migrate')">Run Migrations (migrate)</button>
                <button class="btn btn-warning" onclick="runMigration('rollback')">Rollback (migrate:rollback)</button>
                <button class="btn btn-danger" onclick="confirmFresh('fresh')">Fresh (migrate:fresh)</button>
                <button class="btn btn-danger" onclick="confirmFresh('fresh_seed')">Fresh + Seed</button>
                <button class="btn btn-info" onclick="runMigration('seed')">Run Seeders (db:seed)</button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card bg-dark text-light">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-terminal me-2"></i>Terminal Output</h5>
            </div>
            <div class="card-body">
                <pre id="terminal-output" style="color: #0f0; background: #000; padding: 15px; border-radius: 5px; min-height: 200px; max-height: 400px; overflow-y: auto;">Awaiting command...</pre>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Migration History</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Migration</th>
                            <th>Batch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($migrations as $migration)
                        <tr>
                            <td>{{ $migration->id }}</td>
                            <td>{{ $migration->migration }}</td>
                            <td>{{ $migration->batch }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Fresh Confirmation Modal -->
<div class="modal fade" id="freshConfirmModal" tabindex="-1" aria-labelledby="freshConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="freshConfirmModalLabel"><i class="ti ti-alert-triangle me-2"></i>WARNING: Destructive Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger fw-bold">This will DROP all database tables and recreate the database schema.</p>
                <p>Database: <strong>{{ config('database.connections.' . config('database.default') . '.database') }}</strong></p>
                <div class="mb-3">
                    <label for="freshConfirmInput" class="form-label">Type <strong>FRESH</strong> to confirm</label>
                    <input type="text" class="form-control" id="freshConfirmInput" autocomplete="off">
                </div>
                <input type="hidden" id="freshType" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="executeFreshBtn" disabled>Execute</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    function confirmFresh(type) {
        $('#freshType').val(type);
        $('#freshConfirmInput').val('');
        $('#executeFreshBtn').prop('disabled', true);
        $('#freshConfirmModal').modal('show');
    }

    $('#freshConfirmInput').on('input', function() {
        if ($(this).val() === 'FRESH') {
            $('#executeFreshBtn').prop('disabled', false);
        } else {
            $('#executeFreshBtn').prop('disabled', true);
        }
    });

    $('#executeFreshBtn').on('click', function() {
        $('#freshConfirmModal').modal('hide');
        const type = $('#freshType').val();
        runMigration(type, 'FRESH');
    });

    function runMigration(type, confirm = null) {
        const terminal = $('#terminal-output');
        terminal.text('Running command...\n');
        
        $.ajax({
            url: '{{ route('admin.developer.migrations.run') }}',
            type: 'POST',
            data: {
                _token: csrfToken,
                type: type,
                confirm: confirm
            },
            success: function(response) {
                terminal.text(response.output || response.status);
            },
            error: function(xhr) {
                terminal.text('Error executing command:\n' + xhr.responseText);
            }
        });
    }
</script>
@endpush
