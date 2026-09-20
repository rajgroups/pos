@extends('layouts.admin.app')
@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
   <div class="mb-3">
      <h1 class="mb-1">App Settings Configuration</h1>
      <p class="fw-medium">Manage the user and driver app updates, urls, and system dispatch settings.</p>
   </div>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.settings.app.update') }}" method="POST">
            @csrf
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- User App Settings Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white"><i class="bi bi-phone me-2"></i>User App Settings</h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Latest Version</label>
                                    <input type="text" name="user_app_latest_version" class="form-control" value="{{ old('user_app_latest_version', $settings['user_app_latest_version'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Minimum Version</label>
                                    <input type="text" name="user_app_min_version" class="form-control" value="{{ old('user_app_min_version', $settings['user_app_min_version'] ?? '') }}" required>
                                </div>
                                
                                <div class="col-md-12 mt-3">
                                    <div class="form-check form-switch custom-switch-md">
                                        <input class="form-check-input" type="hidden" name="user_app_force_update" value="0">
                                        <input class="form-check-input" type="checkbox" id="user_app_force_update" name="user_app_force_update" value="1" {{ (old('user_app_force_update', $settings['user_app_force_update'] ?? 0) == 1) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-2" for="user_app_force_update">Force Update Required</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-semibold">Android Play Store URL</label>
                                    <input type="url" name="user_app_url_android" class="form-control" value="{{ old('user_app_url_android', $settings['user_app_url_android'] ?? '') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">iOS App Store URL</label>
                                    <input type="url" name="user_app_url_ios" class="form-control" value="{{ old('user_app_url_ios', $settings['user_app_url_ios'] ?? '') }}" required>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-semibold">Update Title</label>
                                    <input type="text" name="user_app_update_title" class="form-control" value="{{ old('user_app_update_title', $settings['user_app_update_title'] ?? '') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Update Message</label>
                                    <textarea name="user_app_update_message" class="form-control" rows="3" required>{{ old('user_app_update_message', $settings['user_app_update_message'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Driver App Settings Card -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0 text-white"><i class="bi bi-car-front me-2"></i>Driver App Settings</h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Latest Version</label>
                                    <input type="text" name="driver_app_latest_version" class="form-control" value="{{ old('driver_app_latest_version', $settings['driver_app_latest_version'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Minimum Version</label>
                                    <input type="text" name="driver_app_min_version" class="form-control" value="{{ old('driver_app_min_version', $settings['driver_app_min_version'] ?? '') }}" required>
                                </div>
                                
                                <div class="col-md-12 mt-3">
                                    <div class="form-check form-switch custom-switch-md">
                                        <input class="form-check-input" type="hidden" name="driver_app_force_update" value="0">
                                        <input class="form-check-input" type="checkbox" id="driver_app_force_update" name="driver_app_force_update" value="1" {{ (old('driver_app_force_update', $settings['driver_app_force_update'] ?? 0) == 1) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold ms-2" for="driver_app_force_update">Force Update Required</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-semibold">Android Play Store URL</label>
                                    <input type="url" name="driver_app_url_android" class="form-control" value="{{ old('driver_app_url_android', $settings['driver_app_url_android'] ?? '') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">iOS App Store URL</label>
                                    <input type="url" name="driver_app_url_ios" class="form-control" value="{{ old('driver_app_url_ios', $settings['driver_app_url_ios'] ?? '') }}" required>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="form-label fw-semibold">Update Title</label>
                                    <input type="text" name="driver_app_update_title" class="form-control" value="{{ old('driver_app_update_title', $settings['driver_app_update_title'] ?? '') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Update Message</label>
                                    <textarea name="driver_app_update_message" class="form-control" rows="3" required>{{ old('driver_app_update_message', $settings['driver_app_update_message'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dispatch Settings Card -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0 text-dark"><i class="bi bi-clock-history me-2"></i>Dispatch & System Settings</h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Driver Waiting Time (Minutes)</label>
                                    <div class="input-group">
                                        <input type="number" name="driver_waiting_time" class="form-control" min="1" value="{{ old('driver_waiting_time', $settings['driver_waiting_time'] ?? '') }}" required>
                                        <span class="input-group-text">Min</span>
                                    </div>
                                    <small class="text-muted">How long driver should wait for passenger.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 mb-5">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Save App Settings
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-switch-md .form-check-input {
        width: 3rem;
        height: 1.5rem;
    }
    .custom-switch-md .form-check-label {
        line-height: 1.5rem;
        padding-top: 0.2rem;
    }
</style>

@endsection
