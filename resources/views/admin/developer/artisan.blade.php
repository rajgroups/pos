@extends('layouts.admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-sm-0">Artisan Tools</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-terminal me-2"></i>Allowed Commands</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="30%">Command</th>
                                <th width="50%">Description</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allowedCommands as $cmd => $desc)
                            <tr>
                                <td><code class="text-dark fw-bold">php artisan {{ $cmd }}</code></td>
                                <td>{{ $desc }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary w-100" onclick="runArtisan('{{ $cmd }}')"><i class="ti ti-player-play me-1"></i>Run</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card bg-dark text-light">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-terminal me-2"></i>Terminal Output</h5>
                <button class="btn btn-sm btn-outline-light" onclick="$('#terminal-output').text('Awaiting command...');"><i class="ti ti-trash"></i></button>
            </div>
            <div class="card-body">
                <pre id="terminal-output" style="color: #0f0; background: #000; padding: 15px; border-radius: 5px; min-height: 250px; max-height: 500px; overflow-y: auto;">Awaiting command...</pre>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    function runArtisan(command) {
        const terminal = $('#terminal-output');
        terminal.text('$ php artisan ' + command + '\nRunning...\n');
        
        $.ajax({
            url: '{{ route('admin.developer.artisan.run') }}',
            type: 'POST',
            data: {
                _token: csrfToken,
                command: command
            },
            success: function(response) {
                if (response.status === 'SUCCESS') {
                    terminal.text('$ php artisan ' + command + '\n\n' + response.output + '\n\n✓ Command completed successfully.');
                } else {
                    terminal.text('$ php artisan ' + command + '\n\n✕ Error:\n' + response.output);
                }
            },
            error: function(xhr) {
                terminal.text('Error executing command:\n' + xhr.responseText);
            }
        });
    }
</script>
@endpush
