@extends('layouts.admin.app')
@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
   <div class="mb-3">
      <h1 class="mb-1">System Mode Configuration</h1>
      <p class="fw-medium">Manage the application communication strategy.</p>
   </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Communication Mode</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.mode.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label d-block mb-3">Select Operating Mode</label>
                        
                        <div class="form-check custom-radio mb-3 p-3 border rounded @if($currentMode === 'economy') border-primary bg-light @endif">
                            <input class="form-check-input mt-1" type="radio" name="indicab_mode" id="mode_economy" value="economy" @if($currentMode === 'economy') checked @endif>
                            <label class="form-check-label ms-2 d-block" for="mode_economy">
                                <span class="d-block fw-bold fs-16">Economy Mode</span>
                                <span class="d-block text-muted mt-1">Uses API + FCM for communication. Low infrastructure cost. Does not require WebSocket/Swoole to be running.</span>
                            </label>
                        </div>
                        
                        <div class="form-check custom-radio p-3 border rounded @if($currentMode === 'prime') border-primary bg-light @endif">
                            <input class="form-check-input mt-1" type="radio" name="indicab_mode" id="mode_prime" value="prime" @if($currentMode === 'prime') checked @endif>
                            <label class="form-check-label ms-2 d-block" for="mode_prime">
                                <span class="d-block fw-bold fs-16">Prime Mode</span>
                                <span class="d-block text-muted mt-1">Uses API + WebSocket + Swoole + Redis for real-time communication. Fast and responsive. FCM serves as a fallback.</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
