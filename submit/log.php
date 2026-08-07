<?php
/**
 * ============================================================================
 * File: /submit/log.php
 * Smart Buzzer Submission Analytics Dashboard - v1.3
 * 
 * v1.3 NEW: Expanded table columns (State, Industry, Platform, Status, Actions),
 *           detail modal, platform/status filters, review phase fields in CSV
 * v1.2 NEW: Added Country, State, Business Industry to Business column & CSV export
 * v1.1 FIX: Unique function names to avoid conflicts
 * 
 * Features:
 * - View all form submissions with expanded columns
 * - Track referrer sources
 * - Filter by date, source, device, platform, status
 * - Stats cards with key metrics
 * - Detail modal per submission
 * - Export to CSV
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

ini_set('session.gc_maxlifetime', 31536000);
ini_set('session.cookie_lifetime', 31536000);
session_start();

// Require authentication
require_once __DIR__ . '/auth.php';
requireAuth();

// Load logs
$logFile = __DIR__ . '/data/submissions_log.json';
$logs = [];
if (file_exists($logFile)) {
    $logs = json_decode(file_get_contents($logFile), true) ?? [];
}

// Sort by timestamp (newest first)
usort($logs, function($a, $b) {
    return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
});

// Calculate stats
$totalSubmissions = count($logs);

// Source breakdown
$sources = [];
foreach ($logs as $log) {
    $source = $log['source'] ?? 'Unknown';
    $sources[$source] = ($sources[$source] ?? 0) + 1;
}
arsort($sources);

// Device breakdown
$devices = ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0];
foreach ($logs as $log) {
    $device = $log['device'] ?? 'Desktop';
    $devices[$device] = ($devices[$device] ?? 0) + 1;
}

// Today's submissions
$today = date('Y-m-d');
$todayCount = 0;
foreach ($logs as $log) {
    if (strpos($log['timestamp'] ?? '', $today) === 0) {
        $todayCount++;
    }
}

// This week's submissions
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekCount = 0;
foreach ($logs as $log) {
    $logDate = substr($log['timestamp'] ?? '', 0, 10);
    if ($logDate >= $weekStart) {
        $weekCount++;
    }
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="submissions_log_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'Timestamp', 'Type', 'Source', 'Referrer', 'Business Name', 'Email', 'Payment Email (Fanbasis/Tazapay)', 'Telegram', 'Country', 'State', 'Industry', 'Quantity', 'Platform', 'Status', 'Review Phase W1', 'Review Phase W2', 'Review Phase W3', 'Device', 'IP']);
    
    foreach ($logs as $log) {
        fputcsv($output, [
            $log['orderId'] ?? '',
            $log['timestamp'] ?? '',
            $log['orderType'] ?? 'Review',
            $log['source'] ?? '',
            $log['referrer'] ?? '',
            $log['businessName'] ?? '',
            $log['email'] ?? '',
            $log['paymentEmail'] ?? '',
            $log['telegram'] ?? '',
            $log['country'] ?? '',
            $log['state'] ?? '',
            $log['businessIndustry'] ?? '',
            $log['quantity'] ?? '',
            $log['platform'] ?? '',
            $log['status'] ?? 'Pending',
            $log['reviewPhaseW1'] ?? '',
            $log['reviewPhaseW2'] ?? '',
            $log['reviewPhaseW3'] ?? '',
            $log['device'] ?? '',
            $log['ip'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Analytics - Smart Buzzer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .source-badge { padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 500; }
        .source-promo { background: #DCFCE7; color: #166534; }
        .source-google { background: #DBEAFE; color: #1E40AF; }
        .source-direct { background: #F3F4F6; color: #374151; }
        .source-social { background: #FEF3C7; color: #92400E; }
        .source-other { background: #E5E7EB; color: #4B5563; }
        .device-desktop { background: #E0E7FF; color: #3730A3; }
        .device-mobile { background: #FCE7F3; color: #9D174D; }
        .device-tablet { background: #CFFAFE; color: #0E7490; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-processing { background: #DBEAFE; color: #1E40AF; }
        .status-completed { background: #DCFCE7; color: #166534; }
        .status-cancelled { background: #FEE2E2; color: #991B1B; }
        .status-onhold { background: #F3F4F6; color: #374151; }
        .platform-google { background: #DBEAFE; color: #1E40AF; }
        .platform-facebook { background: #E0E7FF; color: #3730A3; }
        .platform-yelp { background: #FEE2E2; color: #991B1B; }
        .platform-other { background: #F3F4F6; color: #374151; }
        .type-review { background: #DCFCE7; color: #166534; }
        .type-social { background: #F3E8FF; color: #6B21A8; }
        .sb-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:50; align-items:center; justify-content:center; }
        .sb-modal-overlay.active { display:flex; }
        .sb-modal { background:#fff; border-radius:16px; max-width:560px; width:92%; max-height:85vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.15); }
        .sb-modal-header { padding:20px 24px; border-bottom:1px solid #E5E7EB; display:flex; align-items:center; justify-content:space-between; }
        .sb-modal-body { padding:24px; }
        .sb-modal-row { display:flex; padding:10px 0; border-bottom:1px solid #F3F4F6; }
        .sb-modal-row:last-child { border-bottom:none; }
        .sb-modal-label { width:140px; flex-shrink:0; font-size:13px; color:#6B7280; font-weight:500; }
        .sb-modal-value { flex:1; font-size:13px; color:#111827; word-break:break-word; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="manage.php" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">📊 Submission Analytics</h1>
                        <p class="text-sm text-gray-500">Track where your leads come from</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="?export=csv" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export CSV
                    </a>
                    <button onclick="location.reload()" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total Submissions</p>
                        <p class="text-3xl font-bold text-gray-900"><?php echo $totalSubmissions; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Today</p>
                        <p class="text-3xl font-bold text-emerald-500"><?php echo $todayCount; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">This Week</p>
                        <p class="text-3xl font-bold text-amber-500"><?php echo $weekCount; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Top Source</p>
                        <p class="text-xl font-bold text-purple-600"><?php echo array_key_first($sources) ?: 'N/A'; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Source & Device Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Source Breakdown -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 Traffic Sources</h3>
                <div class="space-y-3">
                    <?php 
                    $topSources = array_slice($sources, 0, 6, true);
                    foreach ($topSources as $source => $count): 
                        $percentage = $totalSubmissions > 0 ? round(($count / $totalSubmissions) * 100) : 0;
                        $barColor = 'bg-blue-500';
                        if (strpos($source, 'Promo') !== false) $barColor = 'bg-emerald-500';
                        elseif (strpos($source, 'Google') !== false) $barColor = 'bg-blue-500';
                        elseif ($source === 'Direct') $barColor = 'bg-gray-400';
                        elseif (in_array($source, ['Facebook', 'Instagram', 'LinkedIn', 'Twitter/X'])) $barColor = 'bg-amber-500';
                    ?>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($source); ?></span>
                            <span class="text-sm text-gray-500"><?php echo $count; ?> (<?php echo $percentage; ?>%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="<?php echo $barColor; ?> h-2 rounded-full transition-all" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($topSources)): ?>
                    <p class="text-gray-500 text-sm">No data yet</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Device Breakdown -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📱 Devices</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-indigo-50 rounded-xl">
                        <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <p class="text-2xl font-bold text-indigo-600"><?php echo $devices['Desktop']; ?></p>
                        <p class="text-xs text-gray-500">Desktop</p>
                    </div>
                    <div class="text-center p-4 bg-pink-50 rounded-xl">
                        <svg class="w-8 h-8 mx-auto mb-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <p class="text-2xl font-bold text-pink-600"><?php echo $devices['Mobile']; ?></p>
                        <p class="text-xs text-gray-500">Mobile</p>
                    </div>
                    <div class="text-center p-4 bg-cyan-50 rounded-xl">
                        <svg class="w-8 h-8 mx-auto mb-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <p class="text-2xl font-bold text-cyan-600"><?php echo $devices['Tablet']; ?></p>
                        <p class="text-xs text-gray-500">Tablet</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" id="searchInput" placeholder="Search by business, email, order ID..." class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <select id="sourceFilter" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">All Sources</option>
                    <?php foreach (array_keys($sources) as $source): ?>
                    <option value="<?php echo htmlspecialchars($source); ?>"><?php echo htmlspecialchars($source); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="deviceFilter" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">All Devices</option>
                    <option value="Desktop">Desktop</option>
                    <option value="Mobile">Mobile</option>
                    <option value="Tablet">Tablet</option>
                </select>
                <select id="platformFilter" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">All Platforms</option>
                    <option value="Google">Google</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Yelp">Yelp</option>
                    <option value="Trustpilot">Trustpilot</option>
                </select>
                <select id="statusFilter" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="On Hold">On Hold</option>
                </select>
                <input type="date" id="dateFilter" class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Business</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">State</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Industry</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Platform</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="logsTable" class="divide-y divide-gray-100">
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                No submissions yet. Logs will appear here when customers submit orders.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($logs as $idx => $log): 
                            $sourceClass = 'source-other';
                            $source = $log['source'] ?? 'Unknown';
                            if (strpos($source, 'Promo') !== false || strpos($source, 'Landing') !== false) $sourceClass = 'source-promo';
                            elseif (strpos($source, 'Google') !== false) $sourceClass = 'source-google';
                            elseif ($source === 'Direct') $sourceClass = 'source-direct';
                            elseif (in_array($source, ['Facebook', 'Instagram', 'LinkedIn', 'Twitter/X'])) $sourceClass = 'source-social';
                            
                            $deviceClass = 'device-desktop';
                            $device = $log['device'] ?? 'Desktop';
                            if ($device === 'Mobile') $deviceClass = 'device-mobile';
                            elseif ($device === 'Tablet') $deviceClass = 'device-tablet';
                            
                            $timestamp = $log['timestamp'] ?? '';
                            $date = $timestamp ? date('M d', strtotime($timestamp)) : '-';
                            $time = $timestamp ? date('H:i', strtotime($timestamp)) : '';
                            $dateISO = $timestamp ? date('Y-m-d', strtotime($timestamp)) : '';

                            $platform = $log['platform'] ?? 'Google';
                            $platformLower = strtolower($platform);
                            $platformClass = 'platform-other';
                            if ($platformLower === 'google') $platformClass = 'platform-google';
                            elseif ($platformLower === 'facebook') $platformClass = 'platform-facebook';
                            elseif ($platformLower === 'yelp') $platformClass = 'platform-yelp';

                            $status = $log['status'] ?? 'Pending';
                            $statusLower = strtolower(str_replace(' ', '', $status));
                            $statusClass = 'status-pending';
                            if ($statusLower === 'processing') $statusClass = 'status-processing';
                            elseif ($statusLower === 'completed') $statusClass = 'status-completed';
                            elseif ($statusLower === 'cancelled') $statusClass = 'status-cancelled';
                            elseif ($statusLower === 'onhold') $statusClass = 'status-onhold';

                            $orderType = $log['orderType'] ?? 'Review';
                            $typeClass = strtolower($orderType) === 'social media' ? 'type-social' : 'type-review';
                        ?>
                        <tr class="log-row hover:bg-gray-50" 
                            data-search="<?php echo htmlspecialchars(strtolower(($log['businessName'] ?? '') . ' ' . ($log['email'] ?? '') . ' ' . ($log['orderId'] ?? '') . ' ' . ($log['country'] ?? '') . ' ' . ($log['state'] ?? '') . ' ' . ($log['businessIndustry'] ?? ''))); ?>"
                            data-source="<?php echo htmlspecialchars($source); ?>"
                            data-device="<?php echo htmlspecialchars($device); ?>"
                            data-platform="<?php echo htmlspecialchars($platform); ?>"
                            data-status="<?php echo htmlspecialchars($status); ?>"
                            data-date="<?php echo $dateISO; ?>">
                            <td class="px-3 py-3">
                                <span class="text-sm font-mono text-gray-600"><?php echo htmlspecialchars($log['orderId'] ?? '-'); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="source-badge <?php echo $typeClass; ?>"><?php echo htmlspecialchars($orderType); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <p class="text-sm font-medium text-gray-900"><?php echo $date; ?></p>
                                <p class="text-xs text-gray-400"><?php echo $time; ?></p>
                            </td>
                            <td class="px-3 py-3">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($log['businessName'] ?? '-'); ?></p>
                                <p class="text-xs text-gray-400"><?php echo htmlspecialchars($log['email'] ?? ''); ?></p>
                            </td>
                            <td class="px-3 py-3">
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($log['state'] ?? '-'); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($log['businessIndustry'] ?? '-'); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="source-badge <?php echo $platformClass; ?>"><?php echo htmlspecialchars($platform); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($log['quantity'] ?? '-'); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="source-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                            </td>
                            <td class="px-3 py-3">
                                <button onclick="openLogDetail(<?php echo $idx; ?>)" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-400">
            Showing <?php echo count($logs); ?> submissions • Last updated: <?php echo date('M d, Y H:i'); ?>
        </div>
    </main>

    <!-- Detail Modal -->
    <div id="logDetailModal" class="sb-modal-overlay" onclick="if(event.target===this)closeLogDetail()">
        <div class="sb-modal">
            <div class="sb-modal-header">
                <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Submission Details</h3>
                <button onclick="closeLogDetail()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="sb-modal-body" id="modalBody"></div>
        </div>
    </div>

    <script>
    // Log data for modal
    var logData = <?php echo json_encode(array_values($logs), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    function openLogDetail(idx) {
        var d = logData[idx];
        if (!d) return;
        document.getElementById('modalTitle').textContent = d.orderId || 'Submission Details';
        var fields = [
            ['Order ID', d.orderId || '-'],
            ['Type', d.orderType || 'Review'],
            ['Timestamp', d.timestamp || '-'],
            ['Business', d.businessName || '-'],
            ['Email', d.email || '-'],
            ['Payment Email (Fanbasis/Tazapay)', d.paymentEmail || '-'],
            ['Telegram', d.telegram || '-'],
            ['Country', d.country || '-'],
            ['State', d.state || '-'],
            ['Industry', d.businessIndustry || '-'],
            ['Platform', d.platform || '-'],
            ['Quantity', d.quantity || '-'],
            ['Status', d.status || 'Pending'],
            ['Source', d.source || '-'],
            ['Device', d.device || '-'],
            ['Referrer', d.referrer || 'Direct'],
            ['Review Phase W1', d.reviewPhaseW1 || '-'],
            ['Review Phase W2', d.reviewPhaseW2 || '-'],
            ['Review Phase W3+', d.reviewPhaseW3 || '-'],
            ['IP', d.ip || '-']
        ];
        var html = '';
        for (var i = 0; i < fields.length; i++) {
            html += '<div class="sb-modal-row"><div class="sb-modal-label">' + escapeLogHtml(fields[i][0]) + '</div><div class="sb-modal-value">' + escapeLogHtml(fields[i][1]) + '</div></div>';
        }
        document.getElementById('modalBody').innerHTML = html;
        document.getElementById('logDetailModal').classList.add('active');
    }

    function closeLogDetail() {
        document.getElementById('logDetailModal').classList.remove('active');
    }

    function escapeLogHtml(t) {
        if (t === null || t === undefined) return '-';
        var s = String(t);
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(s));
        return d.innerHTML;
    }

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLogDetail();
    });

    // Filter functionality
    var searchInput = document.getElementById('searchInput');
    var sourceFilter = document.getElementById('sourceFilter');
    var deviceFilter = document.getElementById('deviceFilter');
    var platformFilter = document.getElementById('platformFilter');
    var statusFilter = document.getElementById('statusFilter');
    var dateFilter = document.getElementById('dateFilter');

    function applyFilters() {
        var search = searchInput.value.toLowerCase();
        var source = sourceFilter.value;
        var device = deviceFilter.value;
        var platform = platformFilter.value;
        var status = statusFilter.value;
        var date = dateFilter.value;
        
        var rows = document.querySelectorAll('.log-row');
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var rowSearch = row.dataset.search || '';
            var rowSource = row.dataset.source || '';
            var rowDevice = row.dataset.device || '';
            var rowPlatform = row.dataset.platform || '';
            var rowStatus = row.dataset.status || '';
            var rowDate = row.dataset.date || '';
            
            var show = true;
            if (search && rowSearch.indexOf(search) === -1) show = false;
            if (source && rowSource !== source) show = false;
            if (device && rowDevice !== device) show = false;
            if (platform && rowPlatform !== platform) show = false;
            if (status && rowStatus !== status) show = false;
            if (date && rowDate !== date) show = false;
            
            row.style.display = show ? '' : 'none';
        }
    }

    searchInput.addEventListener('input', applyFilters);
    sourceFilter.addEventListener('change', applyFilters);
    deviceFilter.addEventListener('change', applyFilters);
    platformFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    </script>
</body>
</html>