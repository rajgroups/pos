<?php

namespace App\Services;

use Google\Cloud\Monitoring\V3\Client\MetricServiceClient;
use Google\Cloud\Monitoring\V3\ListTimeSeriesRequest;
use Google\Cloud\Monitoring\V3\TimeInterval;
use Google\Cloud\Monitoring\V3\Aggregation;
use Google\Protobuf\Timestamp;
use Google\Protobuf\Duration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use App\Models\Booking;

class GoogleMapsUsageService
{
    protected ?string $projectId;
    protected ?string $billingAccountId;
    protected ?string $credentialsPath;
    protected array $alertThresholds;

    // Google Maps Platform service categories
    protected array $serviceCategories = [
        'places' => [
            'label' => 'Places API',
            'skuPrefixes' => ['Autocomplete', 'Place Details', 'Find Place', 'Nearby Search', 'Text Search', 'Place Photo', 'Places'],
        ],
        'directions' => [
            'label' => 'Directions / Routes',
            'skuPrefixes' => ['Directions', 'Routes', 'Distance Matrix'],
        ],
        'maps_sdk' => [
            'label' => 'Maps SDK',
            'skuPrefixes' => ['Dynamic Maps', 'Static Maps', 'Maps SDK', 'Map Loads', 'Street View'],
        ],
        'geocoding' => [
            'label' => 'Geocoding',
            'skuPrefixes' => ['Geocoding', 'Reverse Geocoding'],
        ],
    ];

    public function __construct()
    {
        $this->projectId = config('services.google_cloud.project_id');
        $this->billingAccountId = config('services.google_cloud.billing_account_id');
        $this->credentialsPath = config('services.google_cloud.credentials');
        $this->alertThresholds = [
            'daily_inr' => (float) config('services.google_cloud.alerts.daily_inr', 500),
            'monthly_inr' => (float) config('services.google_cloud.alerts.monthly_inr', 10000),
        ];
    }

    /**
     * Check if Google Cloud credentials are properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->projectId)
            && !empty($this->credentialsPath)
            && file_exists($this->credentialsPath);
    }

    /**
     * Check if BigQuery billing export is configured.
     */
    public function isBigQueryConfigured(): bool
    {
        return !empty(config('services.google_cloud.bigquery.project_id'))
            && !empty(config('services.google_cloud.bigquery.dataset'))
            && !empty(config('services.google_cloud.bigquery.billing_table'));
    }

    /**
     * Get configuration status details for the dashboard.
     */
    public function getConfigStatus(): array
    {
        $issues = [];

        if (empty($this->projectId)) {
            $issues[] = 'GOOGLE_CLOUD_PROJECT_ID is not set in .env';
        }
        if (empty($this->credentialsPath)) {
            $issues[] = 'GOOGLE_CLOUD_CREDENTIALS path is not set in .env';
        } elseif (!file_exists($this->credentialsPath)) {
            $issues[] = 'Service account JSON file not found at: ' . basename($this->credentialsPath);
        }
        if (empty($this->billingAccountId)) {
            $issues[] = 'GOOGLE_CLOUD_BILLING_ACCOUNT_ID is not set (billing data will be unavailable)';
        }

        return [
            'configured' => $this->isConfigured(),
            'bigquery_configured' => $this->isBigQueryConfigured(),
            'issues' => $issues,
        ];
    }

    /**
     * Get today's usage summary.
     */
    public function getTodayUsage(): array
    {
        return Cache::remember('gmaps_today_usage', 900, function () {
            if (!$this->isConfigured()) {
                return $this->emptyUsageSummary();
            }

            try {
                $now = Carbon::now();
                $startOfDay = $now->copy()->startOfDay();

                $metrics = $this->fetchMetrics(
                    $startOfDay,
                    $now,
                    'serviceruntime.googleapis.com/api/request_count',
                    Aggregation\Aligner::ALIGN_SUM,
                    3600 // 1 hour alignment
                );

                $totalRequests = 0;
                $errorRequests = 0;
                $breakdown = [];

                foreach ($metrics as $metric) {
                    $service = $metric['service'] ?? 'unknown';
                    $responseCode = $metric['response_code'] ?? '2xx';
                    $count = $metric['value'] ?? 0;

                    $totalRequests += $count;

                    if (str_starts_with($responseCode, '4') || str_starts_with($responseCode, '5')) {
                        $errorRequests += $count;
                    }

                    if (!isset($breakdown[$service])) {
                        $breakdown[$service] = 0;
                    }
                    $breakdown[$service] += $count;
                }

                $estimatedCost = $this->estimateCostFromRequests($totalRequests, $breakdown);

                return [
                    'total_requests' => $totalRequests,
                    'estimated_cost' => $estimatedCost,
                    'billable_requests' => $totalRequests, // All API requests are billable (after free tier)
                    'errors' => $errorRequests,
                    'breakdown' => $breakdown,
                    'last_updated' => $now->toIso8601String(),
                    'data_available' => true,
                ];

            } catch (\Exception $e) {
                Log::error('GoogleMapsUsageService::getTodayUsage failed', [
                    'error' => $e->getMessage(),
                ]);
                return $this->emptyUsageSummary('Unable to fetch data from Google Cloud. ' . $this->sanitizeError($e));
            }
        });
    }

    /**
     * Get current month usage summary.
     */
    public function getCurrentMonthUsage(): array
    {
        return Cache::remember('gmaps_month_usage', 21600, function () {
            if (!$this->isConfigured()) {
                return $this->emptyMonthSummary();
            }

            try {
                $now = Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth();
                $daysElapsed = $now->day;
                $daysInMonth = $now->daysInMonth;

                $metrics = $this->fetchMetrics(
                    $startOfMonth,
                    $now,
                    'serviceruntime.googleapis.com/api/request_count',
                    Aggregation\Aligner::ALIGN_SUM,
                    86400 // 1 day alignment
                );

                $totalRequests = 0;
                $dailyCosts = [];

                foreach ($metrics as $metric) {
                    $totalRequests += $metric['value'] ?? 0;
                }

                $estimatedCost = $this->estimateCostFromRequests($totalRequests);
                $avgDailyCost = $daysElapsed > 0 ? ($estimatedCost / $daysElapsed) : 0;
                $projectedCost = $avgDailyCost * $daysInMonth;

                return [
                    'total_requests' => $totalRequests,
                    'estimated_cost' => round($estimatedCost, 2),
                    'avg_daily_cost' => round($avgDailyCost, 2),
                    'projected_cost' => round($projectedCost, 2),
                    'days_elapsed' => $daysElapsed,
                    'days_in_month' => $daysInMonth,
                    'last_updated' => $now->toIso8601String(),
                    'data_available' => true,
                ];

            } catch (\Exception $e) {
                Log::error('GoogleMapsUsageService::getCurrentMonthUsage failed', [
                    'error' => $e->getMessage(),
                ]);
                return $this->emptyMonthSummary('Unable to fetch monthly data.');
            }
        });
    }

    /**
     * Get daily usage for chart data.
     */
    public function getDailyUsage(int $days = 30): array
    {
        $cacheKey = "gmaps_daily_usage_{$days}";

        return Cache::remember($cacheKey, 3600, function () use ($days) {
            if (!$this->isConfigured()) {
                return ['dates' => [], 'costs' => [], 'requests' => [], 'data_available' => false];
            }

            try {
                $now = Carbon::now();
                $start = $now->copy()->subDays($days)->startOfDay();

                $metrics = $this->fetchMetrics(
                    $start,
                    $now,
                    'serviceruntime.googleapis.com/api/request_count',
                    Aggregation\Aligner::ALIGN_SUM,
                    86400 // 1 day alignment
                );

                // Organize by date
                $dailyData = [];
                foreach ($metrics as $metric) {
                    $date = $metric['date'] ?? Carbon::now()->format('Y-m-d');
                    if (!isset($dailyData[$date])) {
                        $dailyData[$date] = ['requests' => 0, 'cost' => 0];
                    }
                    $dailyData[$date]['requests'] += $metric['value'] ?? 0;
                }

                // Fill in all dates and estimate costs
                $dates = [];
                $costs = [];
                $requests = [];

                for ($i = $days; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i)->format('Y-m-d');
                    $dates[] = $date;
                    $reqCount = $dailyData[$date]['requests'] ?? 0;
                    $requests[] = $reqCount;
                    $costs[] = round($this->estimateCostFromRequests($reqCount), 2);
                }

                return [
                    'dates' => $dates,
                    'costs' => $costs,
                    'requests' => $requests,
                    'data_available' => true,
                    'last_updated' => $now->toIso8601String(),
                ];

            } catch (\Exception $e) {
                Log::error('GoogleMapsUsageService::getDailyUsage failed', [
                    'error' => $e->getMessage(),
                ]);
                return ['dates' => [], 'costs' => [], 'requests' => [], 'data_available' => false];
            }
        });
    }

    /**
     * Get API/SKU breakdown.
     */
    public function getApiBreakdown(): array
    {
        return Cache::remember('gmaps_api_breakdown', 3600, function () {
            if (!$this->isConfigured()) {
                return ['breakdown' => [], 'data_available' => false];
            }

            try {
                $now = Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth();

                $metrics = $this->fetchMetrics(
                    $startOfMonth,
                    $now,
                    'serviceruntime.googleapis.com/api/request_count',
                    Aggregation\Aligner::ALIGN_SUM,
                    0, // No alignment period - sum all
                    true // Group by service/method
                );

                $breakdown = [];
                foreach ($this->serviceCategories as $key => $category) {
                    $breakdown[$key] = [
                        'label' => $category['label'],
                        'requests' => 0,
                        'billable' => 0,
                        'cost' => 0,
                    ];
                }
                $breakdown['other'] = [
                    'label' => 'Other',
                    'requests' => 0,
                    'billable' => 0,
                    'cost' => 0,
                ];

                foreach ($metrics as $metric) {
                    $service = $metric['service'] ?? '';
                    $method = $metric['method'] ?? '';
                    $count = $metric['value'] ?? 0;
                    $label = $service . ' ' . $method;

                    $matched = false;
                    foreach ($this->serviceCategories as $key => $category) {
                        foreach ($category['skuPrefixes'] as $prefix) {
                            if (stripos($label, $prefix) !== false) {
                                $breakdown[$key]['requests'] += $count;
                                $breakdown[$key]['billable'] += $count;
                                $matched = true;
                                break 2;
                            }
                        }
                    }

                    if (!$matched) {
                        $breakdown['other']['requests'] += $count;
                        $breakdown['other']['billable'] += $count;
                    }
                }

                // Estimate costs per category
                foreach ($breakdown as $key => &$data) {
                    $data['cost'] = round($this->estimateCostForCategory($key, $data['requests']), 2);
                }
                unset($data);

                return [
                    'breakdown' => $breakdown,
                    'data_available' => true,
                    'last_updated' => $now->toIso8601String(),
                ];

            } catch (\Exception $e) {
                Log::error('GoogleMapsUsageService::getApiBreakdown failed', [
                    'error' => $e->getMessage(),
                ]);
                return ['breakdown' => [], 'data_available' => false];
            }
        });
    }

    /**
     * Get usage by API key (masked).
     */
    public function getUsageByApiKey(): array
    {
        return Cache::remember('gmaps_api_key_usage', 3600, function () {
            if (!$this->isConfigured()) {
                return ['keys' => [], 'data_available' => false];
            }

            try {
                $now = Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth();

                $metrics = $this->fetchMetrics(
                    $startOfMonth,
                    $now,
                    'serviceruntime.googleapis.com/api/request_count',
                    Aggregation\Aligner::ALIGN_SUM,
                    0,
                    true
                );

                $keys = [];
                foreach ($metrics as $metric) {
                    $credential = $metric['credential_id'] ?? 'unknown';
                    $masked = $this->maskApiKey($credential);

                    if (!isset($keys[$masked])) {
                        $keys[$masked] = [
                            'key' => $masked,
                            'platform' => $this->detectPlatform($credential),
                            'requests' => 0,
                            'cost' => 0,
                        ];
                    }
                    $keys[$masked]['requests'] += $metric['value'] ?? 0;
                }

                // Estimate costs
                foreach ($keys as &$keyData) {
                    $keyData['cost'] = round($this->estimateCostFromRequests($keyData['requests']), 2);
                }
                unset($keyData);

                return [
                    'keys' => array_values($keys),
                    'data_available' => true,
                    'last_updated' => $now->toIso8601String(),
                ];

            } catch (\Exception $e) {
                Log::error('GoogleMapsUsageService::getUsageByApiKey failed', [
                    'error' => $e->getMessage(),
                ]);
                return ['keys' => [], 'data_available' => false];
            }
        });
    }

    /**
     * Get billing alerts based on configured thresholds.
     */
    public function getBillingAlerts(): array
    {
        $today = $this->getTodayUsage();
        $month = $this->getCurrentMonthUsage();

        $dailyCost = $today['estimated_cost'] ?? 0;
        $monthlyCost = $month['estimated_cost'] ?? 0;
        $dailyThreshold = $this->alertThresholds['daily_inr'];
        $monthlyThreshold = $this->alertThresholds['monthly_inr'];

        $alerts = [];

        if ($dailyCost > $dailyThreshold) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'ti-alert-triangle',
                'message' => "Today's estimated cost (₹" . number_format($dailyCost, 2) . ") exceeds daily threshold (₹" . number_format($dailyThreshold, 2) . ")",
            ];
        }

        if ($monthlyCost > $monthlyThreshold) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'ti-alert-triangle',
                'message' => "Monthly estimated cost (₹" . number_format($monthlyCost, 2) . ") exceeds monthly budget (₹" . number_format($monthlyThreshold, 2) . ")",
            ];
        }

        $budgetPercentage = $monthlyThreshold > 0 ? ($monthlyCost / $monthlyThreshold) * 100 : 0;
        if ($budgetPercentage > 80 && $budgetPercentage <= 100) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ti-alert-circle',
                'message' => "Monthly budget usage is at " . number_format($budgetPercentage, 1) . "%. Consider reviewing usage patterns.",
            ];
        }

        return [
            'daily_cost' => round($dailyCost, 2),
            'monthly_cost' => round($monthlyCost, 2),
            'daily_threshold' => $dailyThreshold,
            'monthly_threshold' => $monthlyThreshold,
            'budget_percentage' => round($budgetPercentage, 1),
            'alerts' => $alerts,
        ];
    }

    /**
     * Detect unusual usage (anomalies).
     */
    public function getAnomalies(): array
    {
        return Cache::remember('gmaps_anomalies', 900, function () {
            $today = $this->getTodayUsage();
            $daily = $this->getDailyUsage(7);

            if (!$today['data_available'] || !$daily['data_available']) {
                return ['anomalies' => [], 'data_available' => false];
            }

            $anomalies = [];

            // Compare today vs 7-day average
            $recentRequests = array_slice($daily['requests'], 0, -1); // Exclude today
            if (count($recentRequests) > 0) {
                $avgRequests = array_sum($recentRequests) / count($recentRequests);

                if ($avgRequests > 0 && $today['total_requests'] > 0) {
                    $ratio = $today['total_requests'] / $avgRequests;

                    if ($ratio > 2.0) {
                        $anomalies[] = [
                            'type' => 'warning',
                            'icon' => 'ti-trending-up',
                            'title' => 'Higher-than-usual usage detected',
                            'message' => "Today's API requests are " . number_format($ratio, 1) . "x higher than the recent 7-day daily average.",
                        ];
                    }
                }

                // Cost anomaly
                $recentCosts = array_slice($daily['costs'], 0, -1);
                $avgCost = count($recentCosts) > 0 ? array_sum($recentCosts) / count($recentCosts) : 0;
                $todayCost = $today['estimated_cost'] ?? 0;

                if ($avgCost > 0 && $todayCost > 0) {
                    $costRatio = $todayCost / $avgCost;
                    if ($costRatio > 2.0) {
                        $anomalies[] = [
                            'type' => 'warning',
                            'icon' => 'ti-currency-rupee',
                            'title' => 'Higher-than-usual cost detected',
                            'message' => "Today's estimated cost is " . number_format($costRatio, 1) . "x higher than the recent daily average.",
                        ];
                    }
                }
            }

            return ['anomalies' => $anomalies, 'data_available' => true];
        });
    }

    /**
     * Get Indicab-specific metrics (maps cost per ride, etc.)
     */
    public function getIndicabMetrics(): array
    {
        return Cache::remember('gmaps_indicab_metrics', 3600, function () {
            $month = $this->getCurrentMonthUsage();

            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();

            $completedRides = Booking::where('status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $now])
                ->count();

            $monthlyCost = $month['estimated_cost'] ?? 0;
            $totalRequests = $month['total_requests'] ?? 0;

            return [
                'completed_rides' => $completedRides,
                'maps_cost' => round($monthlyCost, 2),
                'cost_per_ride' => $completedRides > 0 ? round($monthlyCost / $completedRides, 2) : 0,
                'requests_per_ride' => $completedRides > 0 ? round($totalRequests / $completedRides, 1) : 0,
                'data_available' => $month['data_available'] ?? false,
            ];
        });
    }

    /**
     * Clear all cached data.
     */
    public function clearCache(): void
    {
        $keys = [
            'gmaps_today_usage',
            'gmaps_month_usage',
            'gmaps_daily_usage_7',
            'gmaps_daily_usage_30',
            'gmaps_daily_usage_90',
            'gmaps_api_breakdown',
            'gmaps_api_key_usage',
            'gmaps_anomalies',
            'gmaps_indicab_metrics',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get full dashboard summary (for the AJAX summary endpoint).
     */
    public function getDashboardSummary(): array
    {
        return [
            'config' => $this->getConfigStatus(),
            'today' => $this->getTodayUsage(),
            'month' => $this->getCurrentMonthUsage(),
            'alerts' => $this->getBillingAlerts(),
            'anomalies' => $this->getAnomalies(),
            'indicab' => $this->getIndicabMetrics(),
        ];
    }

    // ─── Private helpers ──────────────────────────────────

    /**
     * Fetch metrics from Google Cloud Monitoring API.
     */
    protected function fetchMetrics(
        Carbon $start,
        Carbon $end,
        string $metricType,
        int $aligner = Aggregation\Aligner::ALIGN_SUM,
        int $alignmentPeriodSeconds = 86400,
        bool $groupByMethod = false
    ): array {
        $client = new MetricServiceClient([
            'credentials' => $this->credentialsPath,
        ]);

        try {
            $projectName = "projects/{$this->projectId}";

            $startTimestamp = new Timestamp();
            $startTimestamp->setSeconds($start->timestamp);

            $endTimestamp = new Timestamp();
            $endTimestamp->setSeconds($end->timestamp);

            $interval = new TimeInterval();
            $interval->setStartTime($startTimestamp);
            $interval->setEndTime($endTimestamp);

            // Build the filter for Google Maps Platform services
            $filter = "metric.type = \"{$metricType}\" "
                . "AND resource.type = \"consumed_api\" "
                . "AND resource.labels.service = starts_with(\"maps\")";

            $request = new ListTimeSeriesRequest();
            $request->setName($projectName);
            $request->setFilter($filter);
            $request->setInterval($interval);
            $request->setView(ListTimeSeriesRequest\TimeSeriesView::FULL);

            if ($alignmentPeriodSeconds > 0) {
                $aggregation = new Aggregation();
                $aggregation->setAlignmentPeriod(new Duration(['seconds' => $alignmentPeriodSeconds]));
                $aggregation->setPerSeriesAligner($aligner);
                $request->setAggregation($aggregation);
            }

            $results = [];
            $response = $client->listTimeSeries($request);

            foreach ($response as $timeSeries) {
                $resource = $timeSeries->getResource();
                $metricLabels = $timeSeries->getMetric()->getLabels();
                $resourceLabels = $resource->getLabels();

                $service = $resourceLabels['service'] ?? 'unknown';
                $method = $resourceLabels['method'] ?? $metricLabels['method'] ?? '';
                $credentialId = $metricLabels['credential_id'] ?? '';
                $responseCode = $metricLabels['response_code_class'] ?? '2xx';

                foreach ($timeSeries->getPoints() as $point) {
                    $value = $point->getValue()->getInt64Value();
                    $pointTime = $point->getInterval()->getEndTime();
                    $date = Carbon::createFromTimestamp($pointTime->getSeconds())->format('Y-m-d');

                    $results[] = [
                        'service' => $service,
                        'method' => $method,
                        'credential_id' => $credentialId,
                        'response_code' => $responseCode,
                        'value' => $value,
                        'date' => $date,
                    ];
                }
            }

            return $results;

        } finally {
            $client->close();
        }
    }

    /**
     * Estimate cost from request count.
     * Google Maps Platform pricing (approximate INR equivalents).
     * Uses a blended average rate. Real billing uses SKU-specific rates.
     */
    protected function estimateCostFromRequests(int $requests, array $breakdown = []): float
    {
        if ($requests === 0) return 0.0;

        // Approximate Google Maps Platform pricing in INR per 1000 requests
        // (These are estimates; actual billing uses SKU-specific rates from Google)
        $blendedRatePer1000 = 5.0; // ~₹5 per 1000 requests (average across APIs)

        return ($requests / 1000) * $blendedRatePer1000;
    }

    /**
     * Estimate cost for a specific API category.
     */
    protected function estimateCostForCategory(string $category, int $requests): float
    {
        if ($requests === 0) return 0.0;

        // Approximate rates per 1000 requests in INR
        $rates = [
            'places' => 12.0,       // Places API is more expensive
            'directions' => 8.0,     // Directions API
            'maps_sdk' => 5.0,      // Maps SDK (relatively cheaper)
            'geocoding' => 4.0,      // Geocoding
            'other' => 5.0,          // Default
        ];

        $rate = $rates[$category] ?? $rates['other'];
        return ($requests / 1000) * $rate;
    }

    /**
     * Mask an API key for safe display.
     */
    protected function maskApiKey(string $key): string
    {
        if (strlen($key) <= 8) {
            return '****';
        }
        return substr($key, 0, 4) . '...' . substr($key, -4);
    }

    /**
     * Detect platform from API key or credential identifier.
     */
    protected function detectPlatform(string $credential): string
    {
        $lc = strtolower($credential);
        if (str_contains($lc, 'android')) return 'Android';
        if (str_contains($lc, 'ios')) return 'iOS';
        if (str_contains($lc, 'web')) return 'Web';
        if (str_contains($lc, 'server')) return 'Server';
        return 'Unknown';
    }

    /**
     * Sanitize error messages to never expose credentials.
     */
    protected function sanitizeError(\Exception $e): string
    {
        $message = $e->getMessage();
        // Strip any potential credential/token content
        $message = preg_replace('/[A-Za-z0-9_-]{20,}/', '[REDACTED]', $message);
        // Truncate for safety
        return substr($message, 0, 200);
    }

    /**
     * Return empty usage summary structure.
     */
    protected function emptyUsageSummary(string $error = ''): array
    {
        return [
            'total_requests' => 0,
            'estimated_cost' => 0,
            'billable_requests' => 0,
            'errors' => 0,
            'breakdown' => [],
            'last_updated' => Carbon::now()->toIso8601String(),
            'data_available' => false,
            'error' => $error,
        ];
    }

    /**
     * Return empty month summary structure.
     */
    protected function emptyMonthSummary(string $error = ''): array
    {
        return [
            'total_requests' => 0,
            'estimated_cost' => 0,
            'avg_daily_cost' => 0,
            'projected_cost' => 0,
            'days_elapsed' => 0,
            'days_in_month' => Carbon::now()->daysInMonth,
            'last_updated' => Carbon::now()->toIso8601String(),
            'data_available' => false,
            'error' => $error,
        ];
    }
}
