@extends('layouts.admin.app')
@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
    <div class="mb-3">
        <h1 class="mb-1">Google Maps Usage</h1>
        <p class="fw-medium" id="last-updated-text">Monitor Google Maps Platform usage and billing</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary" id="btn-refresh" onclick="refreshData()">
            <i class="ti ti-refresh me-1"></i> Refresh Data
        </button>
    </div>
</div>

@if(!$configStatus['configured'])
{{-- ─── NOT CONFIGURED STATE ─── --}}
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-warning">
            <div class="card-body text-center py-5">
                <i class="ti ti-cloud-off fs-48 text-warning mb-3 d-block"></i>
                <h4 class="fw-bold mb-3">Google Cloud Not Configured</h4>
                <p class="text-muted mb-4">To monitor Google Maps Platform usage and billing, configure the following environment variables in your <code>.env</code> file:</p>
                <div class="text-start mx-auto" style="max-width: 500px;">
                    <div class="bg-light rounded p-3 mb-3">
                        <code class="d-block mb-1">GOOGLE_CLOUD_PROJECT_ID=your-project-id</code>
                        <code class="d-block mb-1">GOOGLE_CLOUD_BILLING_ACCOUNT_ID=your-billing-account</code>
                        <code class="d-block">GOOGLE_CLOUD_CREDENTIALS=/path/to/service-account.json</code>
                    </div>
                    @if(count($configStatus['issues']) > 0)
                        <div class="alert alert-warning">
                            <ul class="mb-0">
                                @foreach($configStatus['issues'] as $issue)
                                    <li>{{ $issue }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <p class="text-muted mt-3 small">
                    <i class="ti ti-shield-lock me-1"></i>
                    Credentials are stored server-side only. Never expose them to browsers or Flutter apps.
                </p>
            </div>
        </div>
    </div>
</div>
@else
{{-- ─── CONFIGURED: FULL DASHBOARD ─── --}}

{{-- ─── ANOMALY ALERTS ─── --}}
<div id="anomaly-alerts"></div>

{{-- ─── BILLING ALERTS ─── --}}
<div id="billing-alert-banners"></div>

{{-- ─── TODAY'S USAGE CARDS ─── --}}
<div class="row" id="today-cards">
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-primary sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-primary"><i class="ti ti-api fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1">Today's Requests</p>
                    <h4 class="text-white" id="today-requests"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-success sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-success"><i class="ti ti-currency-rupee fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1">Estimated Cost</p>
                    <h4 class="text-white" id="today-cost"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-info sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-info"><i class="ti ti-receipt fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1">Billable Requests</p>
                    <h4 class="text-white" id="today-billable"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-danger sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-danger"><i class="ti ti-alert-circle fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1">Errors</p>
                    <h4 class="text-white" id="today-errors"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── CURRENT MONTH CARDS ─── --}}
<div class="row" id="month-cards">
    <div class="col-12 mb-2"><h5 class="fw-bold text-muted">Current Month</h5></div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Total Requests</span>
                    <span class="badge bg-light text-dark"><i class="ti ti-calendar me-1"></i>MTD</span>
                </div>
                <h4 class="fw-bold" id="month-requests"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Estimated Cost</span>
                    <span class="badge bg-light text-dark"><i class="ti ti-currency-rupee me-1"></i>INR</span>
                </div>
                <h4 class="fw-bold" id="month-cost"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card flex-fill">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Average Daily Cost</span>
                </div>
                <h4 class="fw-bold" id="month-avg"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card flex-fill border-start border-warning border-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted">Projected Monthly Cost</span>
                    <span class="badge bg-warning text-dark">Projected</span>
                </div>
                <h4 class="fw-bold" id="month-projected"><span class="placeholder-glow"><span class="placeholder col-6"></span></span></h4>
            </div>
        </div>
    </div>
</div>

{{-- ─── CHARTS ROW ─── --}}
<div class="row">
    {{-- Daily Cost Chart --}}
    <div class="col-lg-6 d-flex">
        <div class="card flex-fill">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Daily Cost</h5>
                <div class="btn-group btn-group-sm" role="group" id="cost-chart-filter">
                    <button type="button" class="btn btn-outline-primary" onclick="loadDailyChart(7)">7D</button>
                    <button type="button" class="btn btn-primary active" onclick="loadDailyChart(30)">30D</button>
                    <button type="button" class="btn btn-outline-primary" onclick="loadDailyChart(90)">90D</button>
                </div>
            </div>
            <div class="card-body">
                <div id="daily-cost-chart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    {{-- Request Volume Chart --}}
    <div class="col-lg-6 d-flex">
        <div class="card flex-fill">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Request Volume</h5>
                <div class="btn-group btn-group-sm" role="group" id="req-chart-filter">
                    <button type="button" class="btn btn-outline-info" onclick="loadRequestChart(7)">7D</button>
                    <button type="button" class="btn btn-info active" onclick="loadRequestChart(30)">30D</button>
                    <button type="button" class="btn btn-outline-info" onclick="loadRequestChart(90)">90D</button>
                </div>
            </div>
            <div class="card-body">
                <div id="request-volume-chart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── API BREAKDOWN TABLE ─── --}}
<div class="row">
    <div class="col-lg-7 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0">API / SKU Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="api-breakdown-table">
                        <thead class="thead-light">
                            <tr>
                                <th>API / SKU</th>
                                <th class="text-end">Requests</th>
                                <th class="text-end">Billable</th>
                                <th class="text-end">Cost (₹)</th>
                            </tr>
                        </thead>
                        <tbody id="api-breakdown-body">
                            <tr><td colspan="4" class="text-center py-4 text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- API Key / Platform Breakdown --}}
    <div class="col-lg-5 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0">API Key / Platform Usage</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="api-key-table">
                        <thead class="thead-light">
                            <tr>
                                <th>API Key</th>
                                <th>Platform</th>
                                <th class="text-end">Requests</th>
                                <th class="text-end">Cost (₹)</th>
                            </tr>
                        </thead>
                        <tbody id="api-key-body">
                            <tr><td colspan="4" class="text-center py-4 text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── BILLING ALERTS & INDICAB METRICS ─── --}}
<div class="row">
    {{-- Billing Alerts --}}
    <div class="col-lg-6 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ti ti-bell me-2"></i>Billing Alerts</h5>
            </div>
            <div class="card-body" id="billing-alerts-section">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">Daily Budget</span>
                        <span id="alert-daily-text" class="text-muted">Loading...</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" id="alert-daily-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">Monthly Budget</span>
                        <span id="alert-monthly-text" class="text-muted">Loading...</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" id="alert-monthly-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Indicab-Specific Metrics --}}
    <div class="col-lg-6 d-flex">
        <div class="card flex-fill">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ti ti-chart-bar me-2"></i>Indicab Metrics (This Month)</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <p class="text-muted mb-1 small">Completed Rides</p>
                            <h4 class="fw-bold mb-0" id="indicab-rides"><span class="placeholder-glow"><span class="placeholder col-4"></span></span></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <p class="text-muted mb-1 small">Maps Cost</p>
                            <h4 class="fw-bold mb-0" id="indicab-maps-cost"><span class="placeholder-glow"><span class="placeholder col-4"></span></span></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center bg-light">
                            <p class="text-muted mb-1 small">Cost / Ride</p>
                            <h4 class="fw-bold mb-0 text-primary" id="indicab-cost-per-ride"><span class="placeholder-glow"><span class="placeholder col-4"></span></span></h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center bg-light">
                            <p class="text-muted mb-1 small">Requests / Ride</p>
                            <h4 class="fw-bold mb-0 text-info" id="indicab-req-per-ride"><span class="placeholder-glow"><span class="placeholder col-4"></span></span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BigQuery notice --}}
@if(!$configStatus['bigquery_configured'])
<div class="row">
    <div class="col-12">
        <div class="alert alert-light border">
            <i class="ti ti-database me-2"></i>
            <strong>Detailed historical billing is not configured.</strong>
            Configure Google Cloud Billing export to BigQuery for detailed historical cost analysis.
        </div>
    </div>
</div>
@endif

@endif {{-- end configured check --}}

@push('scripts')
<script>
const ROUTES = {
    summary: "{{ route('admin.google-maps.usage.summary') }}",
    daily: "{{ route('admin.google-maps.usage.daily') }}",
    apis: "{{ route('admin.google-maps.usage.apis') }}",
    refresh: "{{ route('admin.google-maps.usage.refresh') }}"
};

let costChart = null;
let requestChart = null;

$(document).ready(function() {
    @if($configStatus['configured'])
    loadSummary();
    loadDailyChart(30);
    loadApiBreakdown();
    @endif
});

function formatNumber(num) {
    if (num === null || num === undefined) return '—';
    return Number(num).toLocaleString('en-IN');
}

function formatCurrency(num) {
    if (num === null || num === undefined) return '—';
    return '₹' + Number(num).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function loadSummary() {
    $.getJSON(ROUTES.summary, function(data) {
        // Today's cards
        let today = data.today || {};
        $('#today-requests').text(formatNumber(today.total_requests));
        $('#today-cost').text(formatCurrency(today.estimated_cost));
        $('#today-billable').text(formatNumber(today.billable_requests));
        $('#today-errors').text(formatNumber(today.errors));

        // Month cards
        let month = data.month || {};
        $('#month-requests').text(formatNumber(month.total_requests));
        $('#month-cost').text(formatCurrency(month.estimated_cost));
        $('#month-avg').text(formatCurrency(month.avg_daily_cost));
        $('#month-projected').text(formatCurrency(month.projected_cost));

        // Last updated
        let updated = today.last_updated || month.last_updated;
        if (updated) {
            let d = new Date(updated);
            $('#last-updated-text').html('Last updated: <strong>' + d.toLocaleString('en-IN') + '</strong>');
        }

        // Billing alerts
        let alerts = data.alerts || {};
        renderBillingAlerts(alerts);

        // Alert banners
        renderAlertBanners(alerts.alerts || []);

        // Anomalies
        let anomalies = data.anomalies || {};
        renderAnomalies(anomalies.anomalies || []);

        // Indicab metrics
        let indicab = data.indicab || {};
        $('#indicab-rides').text(formatNumber(indicab.completed_rides));
        $('#indicab-maps-cost').text(formatCurrency(indicab.maps_cost));
        $('#indicab-cost-per-ride').text(formatCurrency(indicab.cost_per_ride));
        $('#indicab-req-per-ride').text(indicab.requests_per_ride || '—');

        // Show unavailable notice
        if (!today.data_available && today.error) {
            showDataError(today.error);
        }
    }).fail(function() {
        showDataError('Failed to load dashboard data. Please try refreshing.');
    });
}

function renderBillingAlerts(alerts) {
    let dailyPct = alerts.daily_threshold > 0 ? Math.min((alerts.daily_cost / alerts.daily_threshold) * 100, 100) : 0;
    let monthlyPct = alerts.budget_percentage || 0;

    $('#alert-daily-text').text(formatCurrency(alerts.daily_cost) + ' / ' + formatCurrency(alerts.daily_threshold));
    $('#alert-daily-bar').css('width', dailyPct + '%').removeClass('bg-primary bg-warning bg-danger');
    if (dailyPct > 100) $('#alert-daily-bar').addClass('bg-danger');
    else if (dailyPct > 80) $('#alert-daily-bar').addClass('bg-warning');
    else $('#alert-daily-bar').addClass('bg-primary');

    $('#alert-monthly-text').text(formatCurrency(alerts.monthly_cost) + ' / ' + formatCurrency(alerts.monthly_threshold) + ' (' + monthlyPct.toFixed(1) + '%)');
    $('#alert-monthly-bar').css('width', Math.min(monthlyPct, 100) + '%').removeClass('bg-success bg-warning bg-danger');
    if (monthlyPct > 100) $('#alert-monthly-bar').addClass('bg-danger');
    else if (monthlyPct > 80) $('#alert-monthly-bar').addClass('bg-warning');
    else $('#alert-monthly-bar').addClass('bg-success');
}

function renderAlertBanners(alerts) {
    let html = '';
    alerts.forEach(function(alert) {
        let cls = alert.type === 'danger' ? 'alert-danger' : 'alert-warning';
        html += '<div class="alert ' + cls + ' alert-dismissible fade show mb-2">';
        html += '<i class="ti ' + alert.icon + ' me-2"></i>' + alert.message;
        html += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    });
    $('#billing-alert-banners').html(html);
}

function renderAnomalies(anomalies) {
    let html = '';
    anomalies.forEach(function(a) {
        html += '<div class="alert alert-warning alert-dismissible fade show mb-2">';
        html += '<i class="ti ' + a.icon + ' me-2 fs-18"></i>';
        html += '<strong>' + a.title + '</strong><br>' + a.message;
        html += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    });
    $('#anomaly-alerts').html(html);
}

function showDataError(msg) {
    let html = '<div class="alert alert-info mb-3"><i class="ti ti-info-circle me-2"></i>' + msg + '</div>';
    $('#anomaly-alerts').prepend(html);
}

function loadDailyChart(days) {
    // Update button states
    $('#cost-chart-filter .btn').removeClass('btn-primary active').addClass('btn-outline-primary');
    $('#cost-chart-filter .btn').each(function() {
        if ($(this).text() === days + 'D') {
            $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        }
    });

    $.getJSON(ROUTES.daily + '?days=' + days, function(data) {
        if (!data.data_available || data.dates.length === 0) {
            $('#daily-cost-chart').html('<p class="text-center text-muted py-5">No data available</p>');
            return;
        }
        renderCostChart(data.dates, data.costs);
        // Also update request chart if same period
        renderRequestChart(data.dates, data.requests);
    });
}

function loadRequestChart(days) {
    // Update button states
    $('#req-chart-filter .btn').removeClass('btn-info active').addClass('btn-outline-info');
    $('#req-chart-filter .btn').each(function() {
        if ($(this).text() === days + 'D') {
            $(this).removeClass('btn-outline-info').addClass('btn-info active');
        }
    });

    $.getJSON(ROUTES.daily + '?days=' + days, function(data) {
        if (!data.data_available || data.dates.length === 0) {
            $('#request-volume-chart').html('<p class="text-center text-muted py-5">No data available</p>');
            return;
        }
        renderRequestChart(data.dates, data.requests);
    });
}

function renderCostChart(dates, costs) {
    if (costChart) costChart.destroy();

    var options = {
        chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Cost (₹)', data: costs }],
        xaxis: {
            categories: dates.map(function(d) { return new Date(d).toLocaleDateString('en-IN', {day:'numeric', month:'short'}); }),
            labels: { rotate: -45 }
        },
        yaxis: { labels: { formatter: function(v) { return '₹' + v.toFixed(0); } } },
        colors: ['#4361ee'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: function(v) { return '₹' + v.toFixed(2); } } },
        grid: { borderColor: '#f1f1f1' }
    };

    costChart = new ApexCharts(document.querySelector('#daily-cost-chart'), options);
    costChart.render();
}

function renderRequestChart(dates, requests) {
    if (requestChart) requestChart.destroy();

    var options = {
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [{ name: 'Requests', data: requests }],
        xaxis: {
            categories: dates.map(function(d) { return new Date(d).toLocaleDateString('en-IN', {day:'numeric', month:'short'}); }),
            labels: { rotate: -45 }
        },
        yaxis: { labels: { formatter: function(v) { return formatNumber(v); } } },
        colors: ['#0dcaf0'],
        plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: function(v) { return formatNumber(v) + ' requests'; } } },
        grid: { borderColor: '#f1f1f1' }
    };

    requestChart = new ApexCharts(document.querySelector('#request-volume-chart'), options);
    requestChart.render();
}

function loadApiBreakdown() {
    $.getJSON(ROUTES.apis, function(data) {
        // API Breakdown table
        let breakdown = (data.breakdown && data.breakdown.breakdown) ? data.breakdown.breakdown : {};
        let html = '';
        let totalReq = 0, totalBillable = 0, totalCost = 0;

        Object.keys(breakdown).forEach(function(key) {
            let item = breakdown[key];
            html += '<tr>';
            html += '<td class="fw-medium">' + item.label + '</td>';
            html += '<td class="text-end">' + formatNumber(item.requests) + '</td>';
            html += '<td class="text-end">' + formatNumber(item.billable) + '</td>';
            html += '<td class="text-end">' + formatCurrency(item.cost) + '</td>';
            html += '</tr>';
            totalReq += item.requests;
            totalBillable += item.billable;
            totalCost += item.cost;
        });

        if (html === '') {
            html = '<tr><td colspan="4" class="text-center py-3 text-muted">No API breakdown data available</td></tr>';
        } else {
            html += '<tr class="fw-bold table-light">';
            html += '<td>Total</td>';
            html += '<td class="text-end">' + formatNumber(totalReq) + '</td>';
            html += '<td class="text-end">' + formatNumber(totalBillable) + '</td>';
            html += '<td class="text-end">' + formatCurrency(totalCost) + '</td>';
            html += '</tr>';
        }
        $('#api-breakdown-body').html(html);

        // API Key table
        let keys = (data.api_keys && data.api_keys.keys) ? data.api_keys.keys : [];
        let keyHtml = '';
        keys.forEach(function(item) {
            keyHtml += '<tr>';
            keyHtml += '<td><code>' + item.key + '</code></td>';
            keyHtml += '<td><span class="badge bg-light text-dark">' + item.platform + '</span></td>';
            keyHtml += '<td class="text-end">' + formatNumber(item.requests) + '</td>';
            keyHtml += '<td class="text-end">' + formatCurrency(item.cost) + '</td>';
            keyHtml += '</tr>';
        });

        if (keyHtml === '') {
            keyHtml = '<tr><td colspan="4" class="text-center py-3 text-muted">No API key usage data available</td></tr>';
        }
        $('#api-key-body').html(keyHtml);
    });
}

function refreshData() {
    var $btn = $('#btn-refresh');
    $btn.prop('disabled', true).html('<i class="ti ti-loader me-1 spin-icon"></i> Refreshing...');

    $.ajax({
        url: ROUTES.refresh,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function() {
            // Reload all data
            loadSummary();
            loadDailyChart(30);
            loadApiBreakdown();
            $btn.prop('disabled', false).html('<i class="ti ti-refresh me-1"></i> Refresh Data');
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="ti ti-refresh me-1"></i> Refresh Data');
            alert('Failed to refresh data. Please try again.');
        }
    });
}
</script>
<style>
.spin-icon {
    display: inline-block;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush

@endsection
