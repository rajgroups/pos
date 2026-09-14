@extends('layouts.admin.app')
@section('content')
<!-- content @s
   -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
   <div class="mb-3">
      <h1 class="mb-1">Welcome, Admin</h1>
      <p class="fw-medium">You have <span class="text-primary fw-bold">200+</span> Orders, Today</p>
   </div>
   <div class="input-icon-start position-relative mb-3">
      <span class="input-icon-addon fs-16 text-gray-9">
      <i class="ti ti-calendar"></i>
      </span>
      <input type="text" class="form-control date-range bookingrange" placeholder="Search Product">
   </div>
</div>
<div class="alert bg-success-transparent alert-dismissible fade show mb-4">
   <div>
      <span>
      <i class="ti ti-info-circle fs-14 text-success me-2"></i>
      Today's Commission
      </span>
      <span class="text-success fw-semibold"> ₹3,358.69 </span>
      earned from 16 rides today.
   </div>
   <button type="button" class="btn-close text-gray-9 fs-14" data-bs-dismiss="alert" aria-label="Close">
   <i class="ti ti-x"></i>
   </button>
</div>
<div class="row">
   <!-- Today's Commission -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card bg-primary sale-widget flex-fill">
         <div class="card-body d-flex align-items-center">
            <span class="sale-icon bg-white text-primary">
            <i class="ti ti-currency-rupee fs-24"></i>
            </span>
            <div class="ms-2">
               <p class="text-white mb-1">Today's Commission</p>
               <div class="d-inline-flex align-items-center flex-wrap gap-2">
                  <h4 class="text-white">₹3,358.69</h4>
                  <span class="badge badge-soft-primary">16 Rides</span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Driver Earnings -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card bg-secondary sale-widget flex-fill">
         <div class="card-body d-flex align-items-center">
            <span class="sale-icon bg-white text-secondary">
            <i class="ti ti-wallet fs-24"></i>
            </span>
            <div class="ms-2">
               <p class="text-white mb-1">Driver Earnings</p>
               <div class="d-inline-flex align-items-center flex-wrap gap-2">
                  <h4 class="text-white">₹13,549.75</h4>
                  <span class="badge badge-soft-success">After Tax</span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Pending Payouts -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card bg-warning sale-widget flex-fill">
         <div class="card-body d-flex align-items-center">
            <span class="sale-icon bg-white text-warning">
            <i class="ti ti-clock-dollar fs-24"></i>
            </span>
            <div class="ms-2">
               <p class="text-white mb-1">Pending Payouts</p>
               <div class="d-inline-flex align-items-center flex-wrap gap-2">
                  <h4 class="text-white">₹0.00</h4>
                  <span class="badge badge-soft-light">0 Pending</span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Promo Uses -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card bg-info sale-widget flex-fill">
         <div class="card-body d-flex align-items-center">
            <span class="sale-icon bg-white text-info">
            <i class="ti ti-ticket fs-24"></i>
            </span>
            <div class="ms-2">
               <p class="text-white mb-1">Today's Promo Uses</p>
               <div class="d-inline-flex align-items-center flex-wrap gap-2">
                  <h4 class="text-white">0</h4>
                  <span class="badge badge-soft-light">₹0 Discount</span>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <!-- Active Promo Codes -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">1</h4>
                  <p>Active Promo Codes</p>
               </div>
               <span class="revenue-icon bg-success-transparent text-success">
               <i class="ti ti-discount-2 fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Currently valid codes</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">View All</a>
            </div>
         </div>
      </div>
   </div>
   <!-- Referral Bonuses -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">₹0.00</h4>
                  <p>Pending Referral Bonuses</p>
               </div>
               <span class="revenue-icon bg-danger-transparent text-danger">
               <i class="ti ti-users-plus fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">0 Pending Bonuses</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">View All</a>
            </div>
         </div>
      </div>
   </div>
   <!-- Total Conversations -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">3</h4>
                  <p>Total Conversations</p>
               </div>
               <span class="revenue-icon bg-primary-transparent text-primary">
               <i class="ti ti-message-circle fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Booking conversations</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">View All</a>
            </div>
         </div>
      </div>
   </div>
   <!-- Open Conversations -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">2</h4>
                  <p>Open Conversations</p>
               </div>
               <span class="revenue-icon bg-warning-transparent text-warning">
               <i class="ti ti-message-dots fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Active booking chats</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">Open</a>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <!-- Unread Messages -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">3</h4>
                  <p>Unread Messages</p>
               </div>
               <span class="revenue-icon bg-info-transparent text-info">
               <i class="ti ti-mail fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Waiting for response</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">Reply</a>
            </div>
         </div>
      </div>
   </div>
   <!-- Urgent Messages -->
<div class="col-xl-3 col-sm-6 col-12 d-flex">
    <div class="card revenue-widget flex-fill">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h4 class="mb-1">0</h4>
                    <p>Urgent Messages</p>
                </div>

                <span class="revenue-icon bg-danger-transparent text-danger">
                    <i class="ti ti-alert-circle fs-16"></i>
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0">
                    High priority messages
                </p>

                <a href="#" class="text-decoration-underline fs-13 fw-medium">
                    View All
                </a>
            </div>
        </div>
    </div>
</div>
   <!-- Today's Messages -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">1</h4>
                  <p>Today's Messages</p>
               </div>
               <span class="revenue-icon bg-teal-transparent text-teal">
               <i class="ti ti-message-2 fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Received today</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">View</a>
            </div>
         </div>
      </div>
   </div>
   <!-- This Week -->
   <div class="col-xl-3 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">2</h4>
                  <p>This Week</p>
               </div>
               <span class="revenue-icon bg-orange-transparent text-orange">
               <i class="ti ti-calendar-week fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Messages this week</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">Details</a>
            </div>
         </div>
      </div>
   </div>
   <!-- This Month -->
   <div class="col-xl-4 col-sm-6 col-12 d-flex">
      <div class="card revenue-widget flex-fill">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
               <div>
                  <h4 class="mb-1">4</h4>
                  <p>This Month</p>
               </div>
               <span class="revenue-icon bg-indigo-transparent text-indigo">
               <i class="ti ti-calendar-stats fs-16"></i>
               </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
               <p class="mb-0">Monthly messages</p>
               <a href="#" class="text-decoration-underline fs-13 fw-medium">View</a>
            </div>
         </div>
      </div>
   </div>
   <!-- Avg Response Time -->
<div class="col-xl-4 col-sm-6 col-12 d-flex">
    <div class="card revenue-widget flex-fill">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h4 class="mb-1">N/A</h4>
                    <p>Avg Response Time</p>
                </div>

                <span class="revenue-icon bg-info-transparent text-info">
                    <i class="ti ti-clock-hour-4 fs-16"></i>
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0">
                    Average admin response time
                </p>

                <a href="#" class="text-decoration-underline fs-13 fw-medium">
                    Details
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Open Tickets -->
<div class="col-xl-4 col-sm-6 col-12 d-flex">
    <div class="card revenue-widget flex-fill">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h4 class="mb-1">0</h4>
                    <p>Open Tickets</p>
                </div>

                <span class="revenue-icon bg-warning-transparent text-warning">
                    <i class="ti ti-ticket fs-16"></i>
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0">
                    Tickets requiring attention
                </p>

                <a href="#" class="text-decoration-underline fs-13 fw-medium">
                    View All
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Needs Attention -->
<div class="col-xl-3 col-sm-6 col-12 d-flex">
    <div class="card revenue-widget flex-fill">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h4 class="mb-1">0</h4>
                    <p>Needs Attention</p>
                </div>

                <span class="revenue-icon bg-danger-transparent text-danger">
                    <i class="ti ti-alert-triangle fs-16"></i>
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0">
                    No response in 24 hours
                </p>

                <a href="#" class="text-decoration-underline fs-13 fw-medium">
                    Review
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Unassigned -->
<div class="col-xl-3 col-sm-6 col-12 d-flex">
    <div class="card revenue-widget flex-fill">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div>
                    <h4 class="mb-1">0</h4>
                    <p>Unassigned</p>
                </div>

                <span class="revenue-icon bg-secondary-transparent text-secondary">
                    <i class="ti ti-user-off fs-16"></i>
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <p class="mb-0">
                    Tickets without agent
                </p>

                <a href="#" class="text-decoration-underline fs-13 fw-medium">
                    Assign
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Ticket Overview</h5>
    </div>

    <div class="card-body">
        <canvas id="ticketChart" height="120"></canvas>
    </div>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('ticketChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                'May 14',
                'May 15',
                'May 16',
                'May 17',
                'May 18',
                'May 19',
                'May 20',
                'May 21',
                'May 22',
                'May 23',
                'May 24',
                'May 25',
                'May 26',
                'May 27'
            ],
            datasets: [
                {
                    label: 'New Tickets',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 4
                },
                {
                    label: 'Resolved Tickets',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    tension: 0.4,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'top'
                }
            },

            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Transaction Trends</h5>
    </div>

    <div class="card-body">
        <div style="height: 350px;">
            <canvas id="transactionChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const transactionCtx = document.getElementById('transactionChart').getContext('2d');

    new Chart(transactionCtx, {
        type: 'line',

        data: {
            labels: [
                'May 14',
                'May 15',
                'May 16',
                'May 17',
                'May 18',
                'May 19',
                'May 20',
                'May 21',
                'May 22',
                'May 23',
                'May 24',
                'May 25',
                'May 26',
                'May 27'
            ],

            datasets: [

                {
                    label: 'Credits',
                    data: [
                        0, 0, 0, 0, 0, 0, 0, 0,
                        734.26,
                        122.44,
                        0,
                        1123.43,
                        234.50,
                        14999.53
                    ],
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 4
                },

                {
                    label: 'Debits',
                    data: [
                        0, 0, 0, 0, 0, 0, 0, 0,
                        746.92,
                        108.55,
                        0,
                        1322.87,
                        194.50,
                        701.34
                    ],
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 4
                },

                {
                    label: 'Active Wallets',
                    data: [
                        0, 0, 0, 0, 0, 0, 0, 0,
                        7,
                        3,
                        0,
                        4,
                        4,
                        7
                    ],
                    borderColor: '#6366f1',
                    backgroundColor: '#6366f1',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 4,
                    yAxisID: 'wallets'
                }

            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    position: 'top'
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {

                            let label = context.dataset.label || '';
                            let value = context.parsed.y;

                            if (label === 'Active Wallets') {
                                return label + ': ' + value;
                            }

                            return label + ': ₹' + value.toFixed(2);
                        }
                    }
                }
            },

            scales: {

                y: {
                    beginAtZero: true,
                    position: 'left',

                    title: {
                        display: true,
                        text: 'Amount (₹)'
                    }
                },

                wallets: {
                    beginAtZero: true,
                    position: 'right',

                    title: {
                        display: true,
                        text: 'Active Wallets'
                    },

                    grid: {
                        drawOnChartArea: false
                    }
                }

            }
        }
    });
</script>

<div class="row">

<!-- Wallet Overview -->
<div class="col-xxl-8 col-xl-7 col-sm-12 col-12 d-flex">
    <div class="card flex-fill border-0 shadow-sm overflow-hidden">

        <!-- Card Header -->
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                    style="width:50px;height:50px;">

                    <i class="ti ti-wallet text-primary fs-24"></i>
                </div>

                <div>
                    <h5 class="card-title mb-1 fw-bold">
                        Wallet Overview
                    </h5>

                    <p class="text-muted mb-0 fs-13">
                        Daily wallet transactions & balance summary
                    </p>
                </div>
            </div>

            <!-- Filter Buttons -->
            <ul class="nav btn-group custom-btn-group">
                <a class="btn btn-light btn-sm" href="javascript:void(0);">1D</a>
                <a class="btn btn-light btn-sm" href="javascript:void(0);">1W</a>
                <a class="btn btn-light btn-sm" href="javascript:void(0);">1M</a>
                <a class="btn btn-primary btn-sm active" href="javascript:void(0);">1Y</a>
            </ul>

        </div>

        <!-- Card Body -->
        <div class="card-body pt-2 px-4 pb-4">

            <!-- Stats -->
            <div class="row g-3 mb-4">

                <!-- Credits -->
                <div class="col-lg-4 col-md-6">
                    <div class="border rounded-4 p-3 h-100 bg-success bg-opacity-10 position-relative overflow-hidden">

                        <div class="d-flex align-items-center justify-content-between mb-3">

                            <div>
                                <p class="mb-1 text-muted fw-medium">
                                    Today's Credits
                                </p>

                                <h3 class="fw-bold text-success mb-1">
                                    ₹14,999.53
                                </h3>

                                <small class="text-muted">
                                    From 22 transactions
                                </small>
                            </div>

                            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;">

                                <i class="ti ti-arrow-down-left text-success fs-22"></i>
                            </div>

                        </div>

                        <span class="badge bg-success">
                            +18.5%
                        </span>

                    </div>
                </div>

                <!-- Debits -->
                <div class="col-lg-4 col-md-6">
                    <div class="border rounded-4 p-3 h-100 bg-warning bg-opacity-10 position-relative overflow-hidden">

                        <div class="d-flex align-items-center justify-content-between mb-3">

                            <div>
                                <p class="mb-1 text-muted fw-medium">
                                    Today's Debits
                                </p>

                                <h3 class="fw-bold text-warning mb-1">
                                    ₹660.77
                                </h3>

                                <small class="text-muted">
                                    From 15 transactions
                                </small>
                            </div>

                            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;">

                                <i class="ti ti-arrow-up-right text-warning fs-22"></i>
                            </div>

                        </div>

                        <span class="badge bg-warning text-dark">
                            -4.3%
                        </span>

                    </div>
                </div>

                <!-- Balance -->
                <div class="col-lg-4 col-md-12">
                    <div class="border rounded-4 p-3 h-100 bg-primary bg-opacity-10 position-relative overflow-hidden">

                        <div class="d-flex align-items-center justify-content-between mb-3">

                            <div>
                                <p class="mb-1 text-white fw-medium ">
                                    Total Balance
                                </p>

                                <h3 class="fw-bold text-white mb-1">
                                    ₹14,139.98
                                </h3>

                                <small class="text-white">
                                    Current available balance
                                </small>
                            </div>

                            <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;">

                                <i class="ti ti-wallet text-primary fs-22"></i>
                            </div>

                        </div>

                        <span class="badge bg-primary">
                            Active Wallet
                        </span>

                    </div>
                </div>

            </div>

            <!-- Chart Section -->
            <div class="border rounded-4 p-3 bg-light">

                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">

                    <div>
                        <h6 class="fw-semibold mb-1">
                            Transaction Analytics
                        </h6>

                        <p class="text-muted mb-0 fs-13">
                            Credit and debit comparison overview
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-3">

                        <span class="d-flex align-items-center fs-13">
                            <span class="bg-success rounded-circle me-2"
                                style="width:10px;height:10px;"></span>
                            Credits
                        </span>

                        <span class="d-flex align-items-center fs-13">
                            <span class="bg-warning rounded-circle me-2"
                                style="width:10px;height:10px;"></span>
                            Debits
                        </span>

                    </div>

                </div>

                <!-- Chart -->
                <div id="wallet-chart" style="height:320px;"></div>

            </div>

        </div>
    </div>
</div>
    <!-- Overall Information -->
    <div class="col-xxl-4 col-xl-5 d-flex">
        <div class="card flex-fill">

            <div class="card-header">
                <div class="d-inline-flex align-items-center">
                    <span class="title-icon bg-soft-info fs-16 me-2">
                        <i class="ti ti-info-circle"></i>
                    </span>

                    <h5 class="card-title mb-0">Platform Overview</h5>
                </div>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <!-- Total Users -->
                    <div class="col-md-6">
                        <div class="info-item border bg-light p-3 text-center">

                            <div class="mb-2 text-primary fs-24">
                                <i class="ti ti-users"></i>
                            </div>

                            <p class="mb-1">Total Users</p>

                            <h5>56</h5>

                            <small class="text-muted">
                                Active platform users
                            </small>

                        </div>
                    </div>

                    <!-- Active Drivers -->
                    <div class="col-md-6">
                        <div class="info-item border bg-light p-3 text-center">

                            <div class="mb-2 text-success fs-24">
                                <i class="ti ti-steering-wheel"></i>
                            </div>

                            <p class="mb-1">Active Drivers</p>

                            <h5>77</h5>

                            <small class="text-muted">
                                Registered vehicle drivers
                            </small>

                        </div>
                    </div>

                    <!-- Today's Bookings -->
                    <div class="col-md-6">
                        <div class="info-item border bg-light p-3 text-center">

                            <div class="mb-2 text-warning fs-24">
                                <i class="ti ti-calendar-check"></i>
                            </div>

                            <p class="mb-1">Today's Bookings</p>

                            <h5>56</h5>

                            <small class="text-muted">
                                Bookings made today
                            </small>

                        </div>
                    </div>

                    <!-- Active Vehicles -->
                    <div class="col-md-6">
                        <div class="info-item border bg-light p-3 text-center">

                            <div class="mb-2 text-info fs-24">
                                <i class="ti ti-car"></i>
                            </div>

                            <p class="mb-1">Active Vehicles</p>

                            <h5>76</h5>

                            <small class="text-muted">
                                Vehicles currently active
                            </small>

                        </div>
                    </div>

                </div>

            </div>

            <div class="card-footer">

                <div class="row text-center">

                    <div class="col-6 border-end">
                        <h2 class="mb-1 text-success">₹14,999</h2>

                        <p class="text-success mb-2">
                            Credits
                        </p>

                        <span class="badge badge-success badge-xs d-inline-flex align-items-center">
                            <i class="ti ti-arrow-up-left me-1"></i>
                            +22%
                        </span>
                    </div>

                    <div class="col-6">
                        <h2 class="mb-1 text-warning">₹660</h2>

                        <p class="text-warning mb-2">
                            Debits
                        </p>

                        <span class="badge badge-danger badge-xs d-inline-flex align-items-center">
                            <i class="ti ti-arrow-down-right me-1"></i>
                            -5%
                        </span>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<div class="row">

    <!-- Latest Bookings -->
    <div class="col-xxl-8 col-xl-7 d-flex">
        <div class="card flex-fill">
            
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-inline-flex align-items-center">
                    <span class="title-icon bg-soft-success fs-16 me-2">
                        <i class="ti ti-wallet"></i>
                    </span>
                    <h5 class="card-title mb-0">Recent Wallet Transactions</h5>
                </div>

                <a href="javascript:void(0);" class="fs-13 fw-medium text-decoration-underline">
                    View All
                </a>
            </div>

            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between border-bottom mb-3 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-soft-success rounded-circle">
                            <i class="ti ti-arrow-down text-success fs-20"></i>
                        </div>

                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Wallet Credit</h6>
                            <p class="mb-0 text-muted">TXN#845621</p>
                        </div>
                    </div>

                    <div class="text-end">
                        <h6 class="text-success mb-1">+ ₹4,500</h6>
                        <small class="text-muted">Today</small>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between border-bottom mb-3 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-soft-warning rounded-circle">
                            <i class="ti ti-arrow-up text-warning fs-20"></i>
                        </div>

                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Driver Payout</h6>
                            <p class="mb-0 text-muted">TXN#845622</p>
                        </div>
                    </div>

                    <div class="text-end">
                        <h6 class="text-warning mb-1">- ₹1,250</h6>
                        <small class="text-muted">Today</small>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between border-bottom mb-3 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-soft-info rounded-circle">
                            <i class="ti ti-car text-info fs-20"></i>
                        </div>

                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Booking Payment</h6>
                            <p class="mb-0 text-muted">TXN#845623</p>
                        </div>
                    </div>

                    <div class="text-end">
                        <h6 class="text-success mb-1">+ ₹3,890</h6>
                        <small class="text-muted">Yesterday</small>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between border-bottom mb-3 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-soft-danger rounded-circle">
                            <i class="ti ti-discount text-danger fs-20"></i>
                        </div>

                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Refund Amount</h6>
                            <p class="mb-0 text-muted">TXN#845624</p>
                        </div>
                    </div>

                    <div class="text-end">
                        <h6 class="text-danger mb-1">- ₹560</h6>
                        <small class="text-muted">Yesterday</small>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-soft-primary rounded-circle">
                            <i class="ti ti-credit-card text-primary fs-20"></i>
                        </div>

                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Wallet Recharge</h6>
                            <p class="mb-0 text-muted">TXN#845625</p>
                        </div>
                    </div>

                    <div class="text-end">
                        <h6 class="text-success mb-1">+ ₹5,000</h6>
                        <small class="text-muted">2 Days Ago</small>
                    </div>
                </div>

            </div>
        </div>
    
    </div>
    <!-- /Latest Bookings -->


    <!-- Overview Cards -->
    <div class="col-xxl-4 col-xl-5 d-flex">
        <div class="card flex-fill">

            <div class="card-header">
                <div class="d-inline-flex align-items-center">
                    <span class="title-icon bg-soft-success fs-16 me-2">
                        <i class="ti ti-chart-bar"></i>
                    </span>

                    <h5 class="card-title mb-0">
                        Booking & Revenue Overview
                    </h5>
                </div>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-6">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fs-13 text-muted">
                                    Total Bookings
                                </span>

                                <i class="ti ti-car text-primary fs-20"></i>
                            </div>

                            <h3 class="mb-1">150</h3>

                            <span class="badge bg-success-subtle text-success">
                                +12%
                            </span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fs-13 text-muted">
                                    Revenue
                                </span>

                                <i class="ti ti-currency-rupee text-success fs-20"></i>
                            </div>

                            <h3 class="mb-1">₹14.9K</h3>

                            <span class="badge bg-success-subtle text-success">
                                +18%
                            </span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fs-13 text-muted">
                                    Completed
                                </span>

                                <i class="ti ti-circle-check text-success fs-20"></i>
                            </div>

                            <h3 class="mb-1">89</h3>

                            <span class="badge bg-success-subtle text-success">
                                Active
                            </span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fs-13 text-muted">
                                    Cancelled
                                </span>

                                <i class="ti ti-circle-x text-danger fs-20"></i>
                            </div>

                            <h3 class="mb-1">34</h3>

                            <span class="badge bg-danger-subtle text-danger">
                                Issue
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Chart -->
                <div class="mt-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Revenue Overview</h6>

                        <select class="form-select form-select-sm" style="width:120px;">
                            <option>Today</option>
                            <option>Weekly</option>
                            <option>Monthly</option>
                        </select>
                    </div>

                    <div id="booking-revenue-chart" style="height: 280px;"></div>

                </div>

            </div>

        </div>
    </div>
    <!-- /Overview Cards -->

</div>

<div class="row">
     <div class="col-xxl-12 col-md-12">
                <div class="card flex-fill">

            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-inline-flex align-items-center">
                    <span class="title-icon bg-soft-primary fs-16 me-2">
                        <i class="ti ti-car"></i>
                    </span>
                    <h5 class="card-title mb-0">Latest Bookings</h5>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search bookings...">
                    <button class="btn btn-primary btn-sm">
                        <i class="ti ti-search"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table custom-table table-hover mb-0">

                        <thead class="thead-light">
                            <tr>
                                <th>Booking Code</th>
                                <th>User</th>
                                <th>Driver</th>
                                <th>Pickup</th>
                                <th>Dropoff</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        BK2605278B71
                                    </span>
                                </td>

                                <td>Dhruv</td>

                                <td>
                                    <span class="text-muted">Not Assigned</span>
                                </td>

                                <td>219 Green City Road, Bhatha...</td>

                                <td>Surat International Airport...</td>

                                <td>--</td>

                                <td>
                                    <span class="badge bg-danger-subtle text-danger">
                                        Cancelled
                                    </span>
                                </td>

                                <td>May 27, 2026 17:26</td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        BK260527A74D
                                    </span>
                                </td>

                                <td>Asgar WRTeam</td>

                                <td>
                                    <span class="text-muted">Not Assigned</span>
                                </td>

                                <td>Hotel KBN, Station Road...</td>

                                <td>Bhuj, Gujarat, India</td>

                                <td>--</td>

                                <td>
                                    <span class="badge bg-warning-subtle text-warning">
                                        Expired
                                    </span>
                                </td>

                                <td>May 27, 2026 17:22</td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        BK2605272C9F
                                    </span>
                                </td>

                                <td>Dhruv</td>

                                <td>Dhruv Netsofters</td>

                                <td>219 Green City Road...</td>

                                <td>Vegetable Market...</td>

                                <td class="fw-semibold text-success">
                                    ₹360.70
                                </td>

                                <td>
                                    <span class="badge bg-success-subtle text-success">
                                        Completed
                                    </span>
                                </td>

                                <td>May 27, 2026 17:02</td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        BK260527AC77
                                    </span>
                                </td>

                                <td>Dhruv</td>

                                <td>Dhruv Netsofters</td>

                                <td>219 Green City Road...</td>

                                <td>Surat International Airport...</td>

                                <td class="fw-semibold text-success">
                                    ₹375.30
                                </td>

                                <td>
                                    <span class="badge bg-success-subtle text-success">
                                        Completed
                                    </span>
                                </td>

                                <td>May 27, 2026 17:00</td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        BK260527D039
                                    </span>
                                </td>

                                <td>Binal WRTeam</td>

                                <td>
                                    <span class="text-muted">Not Assigned</span>
                                </td>

                                <td>Uma Nagar, Bhuj...</td>

                                <td>Jubilee Circle...</td>

                                <td>--</td>

                                <td>
                                    <span class="badge bg-danger-subtle text-danger">
                                        Cancelled
                                    </span>
                                </td>

                                <td>May 27, 2026 16:57</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-3">

                <p class="mb-0 text-muted">
                    Showing 1 to 10 of 150 results
                </p>

                <div class="d-flex align-items-center gap-2">

                    <select class="form-select form-select-sm" style="width: 80px;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>

                    <ul class="pagination pagination-sm mb-0">

                        <li class="page-item active">
                            <a class="page-link" href="javascript:void(0);">1</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);">2</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);">3</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);">4</a>
                        </li>

                        <li class="page-item disabled">
                            <a class="page-link" href="javascript:void(0);">...</a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);">15</a>
                        </li>

                    </ul>

                </div>

            </div>

        </div>
     </div>
    <!-- Recent Wallet Transactions -->

    <!-- /Recent Wallet Transactions -->



</div>
<div class="row">

    <!-- Bookings Overview -->
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-inline-flex align-items-center">
                    <span class="title-icon bg-soft-primary fs-16 me-2">
                        <i class="ti ti-car"></i>
                    </span>

                    <h5 class="card-title mb-0">
                        Bookings Overview
                    </h5>
                </div>

                <div class="dropdown">
                    <a href="javascript:void(0);"
                       class="dropdown-toggle btn btn-sm btn-white"
                       data-bs-toggle="dropdown">
                        <i class="ti ti-calendar me-1"></i>Weekly
                    </a>

                    <ul class="dropdown-menu p-2">
                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">Weekly</a></li>
                        <li><a class="dropdown-item" href="#">Monthly</a></li>
                    </ul>
                </div>
            </div>

            <div class="card-body">

                <div class="row g-3 mb-4">

                    <div class="col-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <h3 class="text-primary mb-1">150</h3>
                            <p class="mb-0 text-muted">Total</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <h3 class="text-success mb-1">89</h3>
                            <p class="mb-0 text-muted">Completed</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <h3 class="text-danger mb-1">34</h3>
                            <p class="mb-0 text-muted">Cancelled</p>
                        </div>
                    </div>

                </div>

                <!-- Flow Chart -->
                <div id="bookings-overview-chart"></div>

            </div>

        </div>
    </div>
    <!-- /Bookings Overview -->


    <!-- Revenue Overview -->
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div class="d-inline-flex align-items-center">
                    <span class="title-icon bg-soft-success fs-16 me-2">
                        <i class="ti ti-currency-rupee"></i>
                    </span>

                    <h5 class="card-title mb-0">
                        Revenue Overview
                    </h5>
                </div>

                <div class="dropdown">
                    <a href="javascript:void(0);"
                       class="dropdown-toggle btn btn-sm btn-white"
                       data-bs-toggle="dropdown">
                        <i class="ti ti-calendar me-1"></i>Monthly
                    </a>

                    <ul class="dropdown-menu p-2">
                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">Weekly</a></li>
                        <li><a class="dropdown-item" href="#">Monthly</a></li>
                    </ul>
                </div>

            </div>

            <div class="card-body">

                <div class="row g-3 mb-4">

                    <div class="col-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <h3 class="text-success mb-1">₹14.9K</h3>
                            <p class="mb-0 text-muted">Revenue</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <h3 class="text-warning mb-1">₹660</h3>
                            <p class="mb-0 text-muted">Debits</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="border rounded p-3 text-center bg-light">
                            <h3 class="text-primary mb-1">₹14.1K</h3>
                            <p class="mb-0 text-muted">Balance</p>
                        </div>
                    </div>

                </div>

                <!-- Revenue Chart -->
                <div id="revenue-overview-chart"></div>

            </div>

        </div>
    </div>
    <!-- /Revenue Overview -->

</div>


<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>

    // Bookings Overview Chart
    var bookingsOptions = {
        series: [{
            name: 'Bookings',
            data: [20, 35, 28, 45, 32, 50, 40]
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            }
        },
        colors: ['#0d6efd'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            opacity: 0.2
        },
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        },
        grid: {
            borderColor: '#f1f1f1'
        }
    };

    var bookingsChart = new ApexCharts(
        document.querySelector("#bookings-overview-chart"),
        bookingsOptions
    );

    bookingsChart.render();



    // Revenue Overview Chart
    var revenueOptions = {
        series: [{
            name: 'Revenue',
            data: [2500, 3200, 2800, 4500, 3900, 5200, 4900]
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        colors: ['#198754'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '45%'
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        },
        grid: {
            borderColor: '#f1f1f1'
        }
    };

    var revenueChart = new ApexCharts(
        document.querySelector("#revenue-overview-chart"),
        revenueOptions
    );

    revenueChart.render();

</script>

@endsection
