<?php
/**
 * ============================================================================
 * File: /submit/manage.php
 * Smart Buzzer AM Dashboard - v5.1 (Country/State/Industry in Modal)
 * 
 * v5.1 NEW: Display Country, State/Region, Business Industry in order details modal
 * v5.0 NEW: Order Type column, filter, and modal display for social media
 * v4.4 NEW: Display Rating Option in order details modal
 * v4.3 NEW: Display Business Location in order details modal
 * v4.2 NEW: Display custom platform name when platform is "Other"
 * v4.1 FIX: Uses unique function names (getClientIpM, isIndonesianIPM) to avoid conflicts
 * 
 * Features:
 * - Tab navigation (Orders + TNC)
 * - Order Type filter (Reviews / Social Media)
 * - Indonesia IP restriction
 * - Checkbox select & bulk delete
 * - Date range filter
 * - Business Names column
 * - Status dropdown
 * - Google Sheet integration
 * - TNC Editor for form
 * - Unique SB namespace (no JS conflicts)
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: January 2025
 * ============================================================================
 */

ini_set('session.gc_maxlifetime', 31536000);
ini_set('session.cookie_lifetime', 31536000);
session_start();

// Require authentication
require_once __DIR__ . '/auth.php';
requireAuth();

// Generate CSRF token
$csrfToken = generateCSRFToken();

// Current tab
$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';

// Load orders from JSON
$ordersFile = __DIR__ . '/data/orders.json';
$orders = [];
if (file_exists($ordersFile)) {
    $jsonData = file_get_contents($ordersFile);
    $data = json_decode($jsonData, true);
    $orders = $data['orders'] ?? [];
}

// Sort orders by timestamp (newest first)
usort($orders, function($a, $b) {
    $timeA = !empty($a['timestamp']) ? strtotime($a['timestamp']) : 0;
    $timeB = !empty($b['timestamp']) ? strtotime($b['timestamp']) : 0;
    if ($timeA === false) $timeA = 0;
    if ($timeB === false) $timeB = 0;
    return $timeB - $timeA;
});

// Pagination
$itemsPerPage = 50;
$totalOrders = count($orders);
$totalPages = ceil($totalOrders / $itemsPerPage);
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedOrders = array_slice($orders, $offset, $itemsPerPage);

// Stats
$pendingOrders = count(array_filter($orders, function($o) { return ($o['status'] ?? 'Pending') === 'Pending'; }));
$processingOrders = count(array_filter($orders, function($o) { return ($o['status'] ?? '') === 'Processing'; }));
$completedOrders = count(array_filter($orders, function($o) { return ($o['status'] ?? '') === 'Completed'; }));
$socialMediaOrders = count(array_filter($orders, function($o) { return ($o['orderType'] ?? 'reviews') === 'social_media'; }));
$reviewsOrders = count(array_filter($orders, function($o) { return ($o['orderType'] ?? 'reviews') === 'reviews'; }));

// Load TNC
$tncFile = __DIR__ . '/data/tnc.json';
$tncData = ['content' => '', 'lastUpdated' => null];
if (file_exists($tncFile)) {
    $tncData = json_decode(file_get_contents($tncFile), true) ?? $tncData;
}

function safeJsonEncode($data) {
    return htmlspecialchars(json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; }
        .sb-status-badge { font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .sb-status-badge:hover { transform: scale(1.05); }
        .sb-status-pending { background: #fef3c7; color: #92400e; }
        .sb-status-processing { background: #dbeafe; color: #1e40af; }
        .sb-status-completed { background: #d1fae5; color: #065f46; }
        .sb-status-cancelled { background: #fee2e2; color: #991b1b; }
        .sb-status-hold { background: #f3f4f6; color: #374151; }
        .sb-table-row { transition: background-color 0.15s ease; }
        .sb-table-row:hover { background-color: #f0f9ff; }
        @keyframes sb-spin { to { transform: rotate(360deg); } }
        .sb-spinner { animation: sb-spin 1s linear infinite; }
        .sb-dropdown { position: absolute; top: 100%; left: 0; z-index: 50; min-width: 140px; background: white; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .sb-dropdown-hidden { display: none; }
        .sb-dropdown-item { padding: 8px 16px; cursor: pointer; transition: background 0.15s; font-size: 14px; }
        .sb-dropdown-item:hover { background: #f0f9ff; }
        .sb-tab { padding: 12px 20px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .sb-tab:hover { color: #111827; }
        .sb-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-6 py-3">
            <div class="flex items-center justify-between">
                <img src="https://smart-buzzer.com/wp-content/uploads/2024/12/REV-COLOR-Smart-Buzzer-11.png" alt="Smart Buzzer" class="h-10">
                <div class="flex items-center space-x-3">
                    <a href="https://docs.google.com/spreadsheets/d/1WzOBE3ReJCjwjfqAqWEqw50wLkJdggQ0xQWr0U5dg9o" target="_blank" 
                       class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-medium transition">
                        📑 Master Sheet
                    </a>
                    <a href="logout.php" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex space-x-1">
                <a href="?tab=orders" class="sb-tab <?php echo $currentTab === 'orders' ? 'active' : ''; ?>">
                    📦 Orders
                </a>
                <a href="?tab=tnc" class="sb-tab <?php echo $currentTab === 'tnc' ? 'active' : ''; ?>">
                    📋 Terms & Conditions
                </a>
                <a href="audit-list.php" class="sb-tab">
                    🎁 Audit Requests
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <?php if ($currentTab === 'orders'): ?>
        <!-- ============================================ -->
        <!-- ORDERS TAB -->
        <!-- ============================================ -->
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">Total</p>
                <p id="statTotal" class="text-2xl font-bold text-gray-900"><?php echo $totalOrders; ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">Pending</p>
                <p id="statPending" class="text-2xl font-bold text-amber-500"><?php echo $pendingOrders; ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">Processing</p>
                <p id="statProcessing" class="text-2xl font-bold text-blue-500"><?php echo $processingOrders; ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">Completed</p>
                <p id="statCompleted" class="text-2xl font-bold text-emerald-500"><?php echo $completedOrders; ?></p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 mb-6">
            <div class="flex flex-wrap gap-3">
                <input type="text" id="sbSearchInput" placeholder="🔍 Search..." class="flex-1 min-w-[200px] px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                
                <select id="sbStatusFilter" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                
                <select id="sbOrderTypeFilter" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">All Types</option>
                    <option value="reviews">Reviews</option>
                    <option value="social_media">Social Media</option>
                </select>
                
                <input type="date" id="sbDateFrom" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200" title="From Date">
                <input type="date" id="sbDateTo" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200" title="To Date">
                
                <div class="flex items-center space-x-3">
                    <a href="log.php" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-xl text-sm font-medium transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Analytics
                    </a>
                    <button onclick="SB.refresh()" id="sbRefreshBtn" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-medium transition flex items-center">
                        <svg id="sbRefreshIcon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Refresh
                    </button>
                </div>
                
                <button onclick="SB.deleteSelected()" id="sbDeleteBtn" disabled class="px-4 py-2 bg-red-500 hover:bg-red-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-xl text-sm font-medium transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete (<span id="sbSelectedCount">0</span>)
                </button>
            </div>
        </div>

        <!-- Notification -->
        <div id="sbNotification" class="hidden mb-6 p-4 rounded-2xl border">
            <div class="flex justify-between items-start">
                <div>
                    <p id="sbNotificationTitle" class="font-semibold mb-1"></p>
                    <p id="sbNotificationMsg" class="text-sm"></p>
                </div>
                <button onclick="SB.hideNotification()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <?php if (empty($orders)): ?>
                <div class="p-12 text-center">
                    <p class="text-gray-500">No orders yet</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left"><input type="checkbox" id="sbSelectAll" class="w-4 h-4 rounded"></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Business</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Platform</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sbOrdersTable" class="divide-y divide-gray-100">
                            <?php foreach ($paginatedOrders as $order): 
                                $status = $order['status'] ?? 'Pending';
                                $statusClass = 'sb-status-pending';
                                if ($status === 'Processing') $statusClass = 'sb-status-processing';
                                elseif ($status === 'Completed') $statusClass = 'sb-status-completed';
                                elseif ($status === 'Cancelled') $statusClass = 'sb-status-cancelled';
                                elseif ($status === 'On Hold') $statusClass = 'sb-status-hold';
                                
                                $orderType = $order['orderType'] ?? 'reviews';
                                $orderTypeLabel = $orderType === 'social_media' ? 'SM' : 'Reviews';
                                $orderTypeBg = $orderType === 'social_media' ? 'bg-purple-100 text-purple-700' : 'bg-yellow-100 text-yellow-700';
                                
                                $ts = $order['timestamp'] ?? '';
                                $dateStr = $ts ? date('M d, Y', strtotime($ts)) : '-';
                                $timeStr = $ts ? date('H:i', strtotime($ts)) : '';
                                $dateISO = $ts ? date('Y-m-d', strtotime($ts)) : '';
                                $platform = $order['platform'] ?? 'Google';
                                if ($platform === 'Other' && !empty($order['customPlatform'])) {
                                    $platform = $order['customPlatform'];
                                }
                                $businessDisplay = $orderType === 'social_media' ? ($order['smLink'] ?? '-') : ($order['businessNames'] ?? '-');
                                if (strlen($businessDisplay) > 30) {
                                    $businessDisplay = substr($businessDisplay, 0, 30) . '...';
                                }
                            ?>
                            <tr class="sb-table-row" data-order='<?php echo safeJsonEncode($order); ?>' data-date="<?php echo $dateISO; ?>" data-status="<?php echo htmlspecialchars($status); ?>" data-ordertype="<?php echo htmlspecialchars($orderType); ?>">
                                <td class="px-4 py-3"><input type="checkbox" class="sb-checkbox w-4 h-4 rounded" value="<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>"></td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900"><?php echo htmlspecialchars($order['orderId'] ?? '-'); ?></td>
                                <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $orderTypeBg; ?>"><?php echo $orderTypeLabel; ?></span></td>
                                <td class="px-4 py-3"><p class="text-sm text-gray-600"><?php echo $dateStr; ?></p><p class="text-xs text-gray-400"><?php echo $timeStr; ?></p></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($businessDisplay); ?></td>
                                <td class="px-4 py-3"><p class="text-sm text-gray-900"><?php echo htmlspecialchars($order['email'] ?? '-'); ?></p><p class="text-xs text-gray-400"><?php echo htmlspecialchars($order['whatsapp'] ?? ''); ?></p></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($platform); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($order['quantity'] ?? '-'); ?></td>
                                <td class="px-4 py-3">
                                    <div class="relative">
                                        <span class="sb-status-badge <?php echo $statusClass; ?>" onclick="SB.toggleDropdown('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>')"><?php echo htmlspecialchars($status); ?></span>
                                        <div id="sbDrop_<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>" class="sb-dropdown sb-dropdown-hidden">
                                            <div class="sb-dropdown-item" onclick="SB.updateStatus('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>', 'Pending')">Pending</div>
                                            <div class="sb-dropdown-item" onclick="SB.updateStatus('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>', 'Processing')">Processing</div>
                                            <div class="sb-dropdown-item" onclick="SB.updateStatus('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>', 'Completed')">Completed</div>
                                            <div class="sb-dropdown-item" onclick="SB.updateStatus('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>', 'Cancelled')">Cancelled</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button onclick="SB.viewDetails('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>')" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50">View</button>
                                        <button onclick="SB.openSheet('<?php echo htmlspecialchars($order['orderId'] ?? ''); ?>')" class="px-3 py-1 text-sm text-white bg-gray-800 hover:bg-gray-700 rounded-lg">Open File</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-500">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></p>
                    <div class="flex space-x-2">
                        <?php if ($currentPage > 1): ?>
                        <a href="?tab=orders&page=<?php echo $currentPage - 1; ?>" class="px-3 py-1 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">Previous</a>
                        <?php endif; ?>
                        <?php if ($currentPage < $totalPages): ?>
                        <a href="?tab=orders&page=<?php echo $currentPage + 1; ?>" class="px-3 py-1 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php else: ?>
        <!-- ============================================ -->
        <!-- TNC TAB -->
        <!-- ============================================ -->
        
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Terms & Conditions</h2>
                    <p class="text-sm text-gray-500 mt-1">Add points that customers must agree to (each point will be a checklist item)</p>
                </div>
                <?php if ($tncData['lastUpdated']): ?>
                <p class="text-sm text-gray-400">Last updated: <?php echo date('M d, Y H:i', strtotime($tncData['lastUpdated'])); ?></p>
                <?php endif; ?>
            </div>
            
            <div id="sbTncNotification" class="hidden mb-4 p-4 rounded-xl"></div>
            
            <!-- TNC Points Container -->
            <div id="tncPointsContainer" class="space-y-4 mb-6">
                <?php 
                $points = $tncData['points'] ?? [];
                if (empty($points)) {
                    // Default empty point
                    $points = [['title' => '', 'subtitle' => '']];
                }
                foreach ($points as $index => $point): 
                ?>
                <div class="tnc-point bg-gray-50 rounded-xl p-4 border border-gray-200" data-index="<?php echo $index; ?>">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-sm font-semibold text-gray-500">Point <?php echo $index + 1; ?></span>
                        <button type="button" onclick="SB.removeTncPoint(this)" class="text-red-500 hover:text-red-700 text-sm <?php echo count($points) <= 1 ? 'hidden' : ''; ?>">
                            ✕ Remove
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" class="tnc-title w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200" 
                                   value="<?php echo htmlspecialchars($point['title'] ?? ''); ?>" 
                                   placeholder="Example: Payment Terms">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea class="tnc-subtitle w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 resize-y" 
                                      rows="2" 
                                      placeholder="Example: Full payment is required before service begins. No refunds after reviews are posted."><?php echo htmlspecialchars($point['subtitle'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Add Point Button -->
            <button type="button" onclick="SB.addTncPoint()" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:border-blue-400 hover:text-blue-500 transition flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Another Point
            </button>
            
            <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Each point will appear as a checklist item that customers must agree to
                </p>
                <button onclick="SB.saveTnc()" id="sbSaveTncBtn" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-medium transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save TNC
                </button>
            </div>
        </div>
        
        <?php endif; ?>
    </main>

    <!-- Modal -->
    <div id="sbModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                <h2 id="sbModalTitle" class="text-lg font-semibold text-gray-900">Order Details</h2>
                <button onclick="SB.closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div id="sbModalContent" class="p-6 overflow-y-auto flex-1"></div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <button onclick="SB.closeModal()" class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Close</button>
            </div>
        </div>
    </div>

    <input type="hidden" id="sbCsrfToken" value="<?php echo htmlspecialchars($csrfToken); ?>">

    <script>
    // ============================================================================
    // SMART BUZZER DASHBOARD - UNIQUE NAMESPACE
    // ============================================================================
    const SB = {
        orders: <?php echo json_encode($orders, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE); ?>,
        csrf: '',

        // Initialize
        init: function() {
            // Get CSRF token safely
            var csrfEl = document.getElementById('sbCsrfToken');
            this.csrf = csrfEl ? csrfEl.value : '';
            console.log('[SB] Init, csrf:', this.csrf ? 'OK' : 'MISSING');
            
            // Only init orders tab if elements exist
            if (document.getElementById('sbSearchInput')) {
                document.getElementById('sbSearchInput').addEventListener('input', () => this.applyFilters());
                document.getElementById('sbStatusFilter').addEventListener('change', () => this.applyFilters());
                document.getElementById('sbOrderTypeFilter').addEventListener('change', () => this.applyFilters());
                document.getElementById('sbDateFrom').addEventListener('change', () => this.applyFilters());
                document.getElementById('sbDateTo').addEventListener('change', () => this.applyFilters());
                
                document.getElementById('sbSelectAll').addEventListener('change', (e) => {
                    document.querySelectorAll('.sb-checkbox').forEach(cb => {
                        if (cb.closest('tr').style.display !== 'none') {
                            cb.checked = e.target.checked;
                        }
                    });
                    this.updateDeleteBtn();
                });
                
                document.getElementById('sbOrdersTable').addEventListener('change', (e) => {
                    if (e.target.classList.contains('sb-checkbox')) {
                        this.updateDeleteBtn();
                        this.updateSelectAll();
                    }
                });
            }
            
            // Close dropdowns on outside click
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.sb-status-badge') && !e.target.closest('.sb-dropdown')) {
                    document.querySelectorAll('.sb-dropdown').forEach(d => d.classList.add('sb-dropdown-hidden'));
                }
            });
            
            // ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal();
                    document.querySelectorAll('.sb-dropdown').forEach(d => d.classList.add('sb-dropdown-hidden'));
                }
            });
            
            // Modal outside click
            const modal = document.getElementById('sbModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target.id === 'sbModal') this.closeModal();
                });
            }
        },

        // Apply all filters
        applyFilters: function() {
            const search = document.getElementById('sbSearchInput').value.toLowerCase();
            const status = document.getElementById('sbStatusFilter').value;
            const orderTypeFilter = document.getElementById('sbOrderTypeFilter').value;
            const dateFrom = document.getElementById('sbDateFrom').value;
            const dateTo = document.getElementById('sbDateTo').value;
            
            document.querySelectorAll('#sbOrdersTable tr').forEach(row => {
                const orderData = JSON.parse(row.dataset.order || '{}');
                const rowDate = row.dataset.date || '';
                const rowStatus = row.dataset.status || '';
                const rowOrderType = row.dataset.ordertype || 'reviews';
                
                const orderId = (orderData.orderId || '').toLowerCase();
                const email = (orderData.email || '').toLowerCase();
                const whatsapp = (orderData.whatsapp || '').toLowerCase();
                const business = (orderData.businessNames || orderData.smLink || '').toLowerCase();
                
                let show = true;
                if (search && !orderId.includes(search) && !email.includes(search) && !whatsapp.includes(search) && !business.includes(search)) show = false;
                if (status && rowStatus !== status) show = false;
                if (orderTypeFilter && rowOrderType !== orderTypeFilter) show = false;
                if (dateFrom && rowDate < dateFrom) show = false;
                if (dateTo && rowDate > dateTo) show = false;
                
                row.style.display = show ? '' : 'none';
            });
            
            this.updateSelectAll();
        },

        updateDeleteBtn: function() {
            const count = document.querySelectorAll('.sb-checkbox:checked').length;
            document.getElementById('sbSelectedCount').textContent = count;
            document.getElementById('sbDeleteBtn').disabled = count === 0;
        },

        updateSelectAll: function() {
            const visible = document.querySelectorAll('#sbOrdersTable tr:not([style*="display: none"]) .sb-checkbox');
            const checked = document.querySelectorAll('#sbOrdersTable tr:not([style*="display: none"]) .sb-checkbox:checked');
            const selectAll = document.getElementById('sbSelectAll');
            if (!selectAll) return;
            
            if (visible.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (checked.length === visible.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (checked.length > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        },

        refresh: async function() {
            const icon = document.getElementById('sbRefreshIcon');
            icon.classList.add('sb-spinner');
            
            try {
                const res = await fetch('get-orders.php');
                const data = await res.json();
                
                if (data.success) {
                    this.orders = data.orders;
                    this.renderOrders();
                    this.updateStats();
                    this.showNotification('success', 'Refreshed', 'Orders updated');
                }
            } catch (err) {
                this.showNotification('error', 'Error', 'Failed to refresh');
            }
            
            icon.classList.remove('sb-spinner');
        },
        
        updateStats: function() {
            const total = this.orders.length;
            const pending = this.orders.filter(o => (o.status || 'Pending') === 'Pending').length;
            const processing = this.orders.filter(o => o.status === 'Processing').length;
            const completed = this.orders.filter(o => o.status === 'Completed').length;
            
            // Animate stats update
            const animateNumber = (el, value) => {
                el.style.transition = 'transform 0.2s';
                el.style.transform = 'scale(1.1)';
                el.textContent = value;
                setTimeout(() => el.style.transform = 'scale(1)', 200);
            };
            
            const statTotal = document.getElementById('statTotal');
            const statPending = document.getElementById('statPending');
            const statProcessing = document.getElementById('statProcessing');
            const statCompleted = document.getElementById('statCompleted');
            
            if (statTotal) animateNumber(statTotal, total);
            if (statPending) animateNumber(statPending, pending);
            if (statProcessing) animateNumber(statProcessing, processing);
            if (statCompleted) animateNumber(statCompleted, completed);
        },
        
        renderOrders: function() {
            const tbody = document.getElementById('sbOrdersTable');
            if (!tbody) return;
            
            // Sort orders by timestamp (newest first)
            this.orders.sort((a, b) => {
                const timeA = a.timestamp ? new Date(a.timestamp).getTime() : 0;
                const timeB = b.timestamp ? new Date(b.timestamp).getTime() : 0;
                return timeB - timeA;
            });
            
            tbody.innerHTML = this.orders.map(order => {
                const status = order.status || 'Pending';
                let statusClass = 'sb-status-pending';
                if (status === 'Processing') statusClass = 'sb-status-processing';
                else if (status === 'Completed') statusClass = 'sb-status-completed';
                else if (status === 'Cancelled') statusClass = 'sb-status-cancelled';
                else if (status === 'On Hold') statusClass = 'sb-status-hold';
                
                const orderType = order.orderType || 'reviews';
                const orderTypeLabel = orderType === 'social_media' ? 'SM' : 'Reviews';
                const orderTypeBg = orderType === 'social_media' ? 'bg-purple-100 text-purple-700' : 'bg-yellow-100 text-yellow-700';
                
                const ts = order.timestamp || '';
                const dateStr = ts ? new Date(ts).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : '-';
                const timeStr = ts ? new Date(ts).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'}) : '';
                const dateISO = ts ? ts.split(' ')[0] : '';
                const platform = (order.platform === 'Other' && order.customPlatform) ? order.customPlatform : (order.platform || 'Google');
                
                let businessDisplay = orderType === 'social_media' ? (order.smLink || '-') : (order.businessNames || '-');
                if (businessDisplay.length > 30) businessDisplay = businessDisplay.substring(0, 30) + '...';
                
                const esc = (t) => t ? String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : '-';
                const orderJson = JSON.stringify(order).replace(/'/g, '&#39;').replace(/"/g, '&quot;');
                
                return `
                <tr class="sb-table-row" data-order="${orderJson}" data-date="${dateISO}" data-status="${esc(status)}" data-ordertype="${orderType}">
                    <td class="px-4 py-3"><input type="checkbox" class="sb-checkbox w-4 h-4 rounded" value="${esc(order.orderId)}"></td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-900">${esc(order.orderId)}</td>
                    <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full ${orderTypeBg}">${orderTypeLabel}</span></td>
                    <td class="px-4 py-3"><p class="text-sm text-gray-600">${dateStr}</p><p class="text-xs text-gray-400">${timeStr}</p></td>
                    <td class="px-4 py-3 text-sm text-gray-900">${esc(businessDisplay)}</td>
                    <td class="px-4 py-3"><p class="text-sm text-gray-900">${esc(order.email)}</p><p class="text-xs text-gray-400">${esc(order.whatsapp)}</p></td>
                    <td class="px-4 py-3 text-sm text-gray-900">${esc(platform)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${esc(order.quantity)}</td>
                    <td class="px-4 py-3">
                        <div class="relative">
                            <span class="sb-status-badge ${statusClass}" onclick="SB.toggleDropdown('${esc(order.orderId)}')">${esc(status)}</span>
                            <div id="sbDrop_${esc(order.orderId)}" class="sb-dropdown sb-dropdown-hidden">
                                <div class="sb-dropdown-item" onclick="SB.updateStatus('${esc(order.orderId)}', 'Pending')">Pending</div>
                                <div class="sb-dropdown-item" onclick="SB.updateStatus('${esc(order.orderId)}', 'Processing')">Processing</div>
                                <div class="sb-dropdown-item" onclick="SB.updateStatus('${esc(order.orderId)}', 'Completed')">Completed</div>
                                <div class="sb-dropdown-item" onclick="SB.updateStatus('${esc(order.orderId)}', 'Cancelled')">Cancelled</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <button onclick="SB.viewDetails('${esc(order.orderId)}')" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50">View</button>
                            <button onclick="SB.openSheet('${esc(order.orderId)}')" class="px-3 py-1 text-sm text-white bg-gray-800 hover:bg-gray-700 rounded-lg">Open File</button>
                        </div>
                    </td>
                </tr>
                `;
            }).join('');
            
            this.applyFilters();
        },

        deleteSelected: async function() {
            const checked = document.querySelectorAll('.sb-checkbox:checked');
            const ids = Array.from(checked).map(cb => cb.value);
            
            if (ids.length === 0) return;
            if (!confirm(`Delete ${ids.length} order(s)? This cannot be undone.`)) return;
            
            const btn = document.getElementById('sbDeleteBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Deleting...';
            
            try {
                const res = await fetch('delete-orders.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orderIds: ids })
                });
                const data = await res.json();
                if (data.success) {
                    // Remove rows from DOM
                    ids.forEach(id => {
                        const row = document.querySelector(`tr[data-order*='"orderId":"${id}"']`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s, transform 0.3s';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';
                            setTimeout(() => row.remove(), 300);
                        }
                        // Remove from local orders array
                        const idx = this.orders.findIndex(o => o.orderId === id);
                        if (idx > -1) this.orders.splice(idx, 1);
                    });
                    
                    // Update stats
                    this.updateStats();
                    this.showNotification('success', 'Deleted', data.message);
                } else {
                    this.showNotification('error', 'Error', data.message);
                }
            } catch (err) {
                this.showNotification('error', 'Error', err.message);
            }
            
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete (<span id="sbSelectedCount">0</span>)';
            this.updateDeleteBtn();
            this.updateSelectAll();
        },

        toggleDropdown: function(orderId) {
            document.querySelectorAll('.sb-dropdown').forEach(d => d.classList.add('sb-dropdown-hidden'));
            const dd = document.getElementById('sbDrop_' + orderId);
            if (dd) dd.classList.toggle('sb-dropdown-hidden');
        },

        updateStatus: async function(orderId, newStatus) {
            document.querySelectorAll('.sb-dropdown').forEach(d => d.classList.add('sb-dropdown-hidden'));
            
            // Find the status badge
            const row = document.querySelector(`tr[data-order*='"orderId":"${orderId}"']`);
            const badge = row ? row.querySelector('.sb-status-badge') : null;
            
            if (badge) {
                badge.innerHTML = '<svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
            }
            
            try {
                const res = await fetch('update-status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orderId, status: newStatus, csrf_token: this.csrf })
                });
                const data = await res.json();
                if (data.success) {
                    // Update badge
                    if (badge) {
                        badge.textContent = newStatus;
                        badge.className = 'sb-status-badge sb-status-' + newStatus.toLowerCase().replace(' ', '');
                    }
                    
                    // Update row data attribute
                    if (row) {
                        const orderData = JSON.parse(row.dataset.order);
                        orderData.status = newStatus;
                        row.dataset.order = JSON.stringify(orderData);
                        row.dataset.status = newStatus;
                    }
                    
                    // Update local orders array
                    const order = this.orders.find(o => o.orderId === orderId);
                    if (order) order.status = newStatus;
                    
                    // Update stats
                    this.updateStats();
                    
                    // Flash effect
                    if (row) {
                        row.style.transition = 'background-color 0.3s';
                        row.style.backgroundColor = '#D1FAE5';
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                        }, 500);
                    }
                    
                    this.showNotification('success', 'Updated', `Status changed to ${newStatus}`);
                } else {
                    if (badge) badge.textContent = 'Error';
                    this.showNotification('error', 'Error', data.message);
                }
            } catch (err) {
                if (badge) badge.textContent = 'Error';
                this.showNotification('error', 'Error', err.message);
            }
        },

        viewDetails: function(orderId) {
            const order = this.orders.find(o => o.orderId === orderId);
            if (!order) return;
            
            const esc = (t) => t ? String(t).replace(/</g, '&lt;').replace(/>/g, '&gt;') : '-';
            const date = order.timestamp ? new Date(order.timestamp).toLocaleString() : '-';
            const orderType = order.orderType || 'reviews';
            
            document.getElementById('sbModalTitle').textContent = 'Order: ' + orderId;
            
            // Check if social media order
            if (orderType === 'social_media') {
                const stayRatePercent = ((order.stayRate || 0.8944) * 100).toFixed(2) + '%';
                
                document.getElementById('sbModalContent').innerHTML = `
                    <div class="mb-4 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg text-center font-medium">
                        Social Media Order
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><p class="text-gray-500">Order ID</p><p class="font-medium">${esc(order.orderId)}</p></div>
                        <div><p class="text-gray-500">Date</p><p class="font-medium">${date}</p></div>
                        <div><p class="text-gray-500">Platform</p><p class="font-medium">${esc(order.platform)}</p></div>
                        <div><p class="text-gray-500">Service Type</p><p class="font-medium">${esc(order.serviceType)}</p></div>
                        <div><p class="text-gray-500">Email</p><p class="font-medium">${esc(order.email)}</p></div>
                        ${order.paymentEmail ? `<div class="col-span-2"><p class="text-gray-500">Payment Email (Fanbasis/Tazapay)</p><p class="font-medium">${esc(order.paymentEmail)}</p></div>` : ''}
                        ${order.telegram ? `<div><p class="text-gray-500">Telegram</p><p class="font-medium">${esc(order.telegram)}</p></div>` : ''}
                        <div><p class="text-gray-500">WhatsApp</p><p class="font-medium">${esc(order.whatsapp)}</p></div>
                        <div class="col-span-2"><p class="text-gray-500">Profile/Post Link</p><p class="font-medium break-all">${esc(order.smLink)}</p></div>
                        <div class="col-span-2 pt-4 border-t mt-4"><p class="font-semibold text-gray-900 mb-3">Delivery Details</p></div>
                        <div><p class="text-gray-500">Target Quantity</p><p class="font-medium">${esc(order.quantity)}</p></div>
                        <div><p class="text-gray-500">Fulfill Quantity</p><p class="font-medium font-bold text-purple-600">${esc(order.fulfillQuantity)}</p></div>
                        <div><p class="text-gray-500">Stay Rate</p><p class="font-medium">${stayRatePercent}</p></div>
                        <div><p class="text-gray-500">Guaranteed</p><p class="font-medium text-green-600">${esc(order.quantity)} (after drop)</p></div>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="https://wa.me/${(order.whatsapp || '').replace(/[^0-9]/g, '')}" target="_blank" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-center rounded-lg text-sm font-medium">WhatsApp</a>
                        ${order.telegram ? `<a href="https://t.me/${order.telegram.replace(/^@/, '')}" target="_blank" class="flex-1 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-center rounded-lg text-sm font-medium">Telegram</a>` : ''}
                        <a href="mailto:${esc(order.email)}" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-center rounded-lg text-sm font-medium">Email</a>
                        <button onclick="SB.openSheet('${esc(order.orderId)}')" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm font-medium">Open File</button>
                    </div>
                `;
                document.getElementById('sbModal').classList.remove('hidden');
                return;
            }
            
            // Reviews order (existing flow)
            // Calculate target rating
            const currentRating = parseInt(order.currentRating) || 0;
            const quantity = parseInt(order.quantity) || 0;
            const targetRating = currentRating + quantity;
            
            document.getElementById('sbModalContent').innerHTML = `
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Order ID</p><p class="font-medium">${esc(order.orderId)}</p></div>
                    <div><p class="text-gray-500">Date</p><p class="font-medium">${date}</p></div>
                    <div><p class="text-gray-500">Business</p><p class="font-medium">${esc(order.businessNames)}</p></div>
                    <div><p class="text-gray-500">Platform</p><p class="font-medium">${esc((order.platform === 'Other' && order.customPlatform) ? order.customPlatform : (order.platform || 'Google'))}</p></div>
                    <div><p class="text-gray-500">Email</p><p class="font-medium">${esc(order.email)}</p></div>
                    ${order.paymentEmail ? `<div class="col-span-2"><p class="text-gray-500">Payment Email (Fanbasis/Tazapay)</p><p class="font-medium">${esc(order.paymentEmail)}</p></div>` : ''}
                    ${order.telegram ? `<div><p class="text-gray-500">Telegram</p><p class="font-medium">${esc(order.telegram)}</p></div>` : ''}
                    <div><p class="text-gray-500">WhatsApp</p><p class="font-medium">${esc(order.whatsapp)}</p></div>
                    <div><p class="text-gray-500">Business Location</p><p class="font-medium">${esc(order.businessLocation || '-')}</p></div>
                    <div><p class="text-gray-500">Country</p><p class="font-medium">${esc(order.country || '-')}</p></div>
                    <div><p class="text-gray-500">State / Region</p><p class="font-medium">${esc(order.state || '-')}</p></div>
                    <div><p class="text-gray-500">Business Industry</p><p class="font-medium">${esc(order.businessIndustry || '-')}</p></div>
                    <div><p class="text-gray-500">Product</p><p class="font-medium">${esc(order.productType)}</p></div>
                    <div><p class="text-gray-500">Rating Mix</p><p class="font-medium">${esc(order.ratingMix)}</p></div>
                    <div><p class="text-gray-500">Rating Option</p><p class="font-medium">${order.ratingOption === 'mix_80_20' ? 'Mix (80% 5-Star + 20% 4-Star)' : (order.ratingOption === '5_star_only' ? '5 Stars Only' : esc(order.ratingOption || order.ratingMix || '-'))}</p></div>
                    <div><p class="text-gray-500">Quantity</p><p class="font-medium">${esc(order.quantity)}</p></div>
                    <div><p class="text-gray-500">Num Businesses</p><p class="font-medium">${esc(order.numBusinesses || '1')}</p></div>
                    <div><p class="text-gray-500">Current Rating Qty</p><p class="font-medium">${esc(order.currentRating || '-')}</p></div>
                    <div><p class="text-gray-500">Delivery Speed</p><p class="font-medium">${esc(order.reviewPhaseW1 || '-')}</p></div>
                    <div><p class="text-gray-500">Target Rating Total</p><p class="font-medium font-bold text-green-600">${targetRating > 0 ? targetRating : esc(order.targetRatingTotal || '-')}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Business Link</p><p class="font-medium break-all">${esc(order.mapsLink)}</p></div>
                    ${order.productType === 'Rating & Review' ? `
                    <div class="col-span-2 pt-4 border-t mt-4"><p class="font-semibold text-gray-900 mb-3">Review Details</p></div>
                    <div><p class="text-gray-500">Business Type</p><p class="font-medium">${esc(order.businessType)}</p></div>
                    <div><p class="text-gray-500">Review Tone</p><p class="font-medium">${esc(order.reviewTone)}</p></div>
                    <div><p class="text-gray-500">Review Length</p><p class="font-medium">${esc(order.reviewLength)}</p></div>
                    <div><p class="text-gray-500">Keyword Flex</p><p class="font-medium">${esc(order.keywordFlex)}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Business USP</p><p class="font-medium">${esc(order.businessUSP)}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Business Details</p><p class="font-medium">${esc(order.businessDetails)}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Keywords</p><p class="font-medium">${esc(order.keywords)}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Specific Names</p><p class="font-medium">${esc(order.specificNames)}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Example Reviews</p><p class="font-medium whitespace-pre-wrap">${esc(order.exampleReviews)}</p></div>
                    <div class="col-span-2"><p class="text-gray-500">Additional Notes</p><p class="font-medium whitespace-pre-wrap">${esc(order.additionalNotes || '-')}</p></div>
                    ` : ''}
                </div>
                <div class="mt-6 flex space-x-3">
                    <a href="https://wa.me/${(order.whatsapp || '').replace(/[^0-9]/g, '')}" target="_blank" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-center rounded-lg text-sm font-medium">WhatsApp</a>
                    ${order.telegram ? `<a href="https://t.me/${order.telegram.replace(/^@/, '')}" target="_blank" class="flex-1 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-center rounded-lg text-sm font-medium">Telegram</a>` : ''}
                    <a href="mailto:${esc(order.email)}" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-center rounded-lg text-sm font-medium">Email</a>
                    <button onclick="SB.openSheet('${esc(order.orderId)}')" class="flex-1 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm font-medium">Open File</button>
                </div>
            `;
            document.getElementById('sbModal').classList.remove('hidden');
        },

        closeModal: function() {
            document.getElementById('sbModal').classList.add('hidden');
        },

        openSheet: async function(orderId) {
            const order = this.orders.find(o => o.orderId === orderId);
            if (!order) return;
            
            if (order.spreadsheetUrl) {
                window.open(order.spreadsheetUrl, '_blank');
                return;
            }
            
            if (!confirm('Create Google Sheet for this order?')) return;
            
            // Find the button and show loading
            const row = document.querySelector(`tr[data-order*='"orderId":"${orderId}"']`);
            const btn = row ? row.querySelector('button:last-child') : null;
            const originalText = btn ? btn.innerHTML : '';
            
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
            }
            
            try {
                const res = await fetch('open-sheet.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orderId, csrf_token: this.csrf })
                });
                const data = await res.json();
                if (data.success) {
                    // Update order in local array
                    order.spreadsheetUrl = data.spreadsheetUrl;
                    
                    // Update row data attribute
                    if (row) {
                        const orderData = JSON.parse(row.dataset.order);
                        orderData.spreadsheetUrl = data.spreadsheetUrl;
                        row.dataset.order = JSON.stringify(orderData);
                    }
                    
                    window.open(data.spreadsheetUrl, '_blank');
                    this.showNotification('success', 'Created', 'Google Sheet created successfully');
                } else {
                    this.showNotification('error', 'Error', data.message);
                }
            } catch (err) {
                this.showNotification('error', 'Error', err.message);
            }
            
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        },

        showNotification: function(type, title, msg) {
            const el = document.getElementById('sbNotification');
            if (!el) return;
            el.className = 'mb-6 p-4 rounded-2xl border ' + (type === 'error' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200');
            document.getElementById('sbNotificationTitle').textContent = title;
            document.getElementById('sbNotificationTitle').className = 'font-semibold mb-1 ' + (type === 'error' ? 'text-red-800' : 'text-green-800');
            document.getElementById('sbNotificationMsg').textContent = msg;
            document.getElementById('sbNotificationMsg').className = 'text-sm ' + (type === 'error' ? 'text-red-700' : 'text-green-700');
            el.classList.remove('hidden');
            
            // Auto hide after 3 seconds for success
            if (type === 'success') {
                setTimeout(() => this.hideNotification(), 3000);
            }
        },

        hideNotification: function() {
            const el = document.getElementById('sbNotification');
            if (el) el.classList.add('hidden');
        },

        // ============================================
        // TNC FUNCTIONS
        // ============================================
        
        addTncPoint: function() {
            const container = document.getElementById('tncPointsContainer');
            const points = container.querySelectorAll('.tnc-point');
            const newIndex = points.length;
            
            const pointHtml = `
                <div class="tnc-point bg-gray-50 rounded-xl p-4 border border-gray-200" data-index="${newIndex}">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-sm font-semibold text-gray-500">Point ${newIndex + 1}</span>
                        <button type="button" onclick="SB.removeTncPoint(this)" class="text-red-500 hover:text-red-700 text-sm">
                            ✕ Remove
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" class="tnc-title w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200" 
                                   placeholder="Example: Payment Terms">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea class="tnc-subtitle w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 resize-y" 
                                      rows="2" 
                                      placeholder="Example: Full payment is required before service begins."></textarea>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', pointHtml);
            this.updatePointNumbers();
            this.updateRemoveButtons();
        },
        
        removeTncPoint: function(btn) {
            const point = btn.closest('.tnc-point');
            point.remove();
            this.updatePointNumbers();
            this.updateRemoveButtons();
        },
        
        updatePointNumbers: function() {
            const points = document.querySelectorAll('.tnc-point');
            points.forEach((point, index) => {
                point.dataset.index = index;
                point.querySelector('.text-gray-500').textContent = `Point ${index + 1}`;
            });
        },
        
        updateRemoveButtons: function() {
            const points = document.querySelectorAll('.tnc-point');
            const removeButtons = document.querySelectorAll('.tnc-point button');
            removeButtons.forEach(btn => {
                btn.classList.toggle('hidden', points.length <= 1);
            });
        },
        
        saveTnc: async function() {
            console.log('[SB] saveTnc called');
            const points = [];
            document.querySelectorAll('.tnc-point').forEach(point => {
                const titleEl = point.querySelector('.tnc-title');
                const subtitleEl = point.querySelector('.tnc-subtitle');
                const title = titleEl ? titleEl.value.trim() : '';
                const subtitle = subtitleEl ? subtitleEl.value.trim() : '';
                if (title) { // Only save if title is not empty
                    points.push({ title, subtitle });
                }
            });
            
            console.log('[SB] Points:', points.length);
            
            if (points.length === 0) {
                alert('Please add at least one point with a title');
                return;
            }
            
            const btn = document.getElementById('sbSaveTncBtn');
            const notif = document.getElementById('sbTncNotification');
            
            if (!btn) {
                console.error('[SB] Save button not found!');
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
            
            try {
                console.log('[SB] Sending to save-tnc.php');
                const res = await fetch('save-tnc.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ points, csrf_token: this.csrf })
                });
                console.log('[SB] Response status:', res.status);
                const data = await res.json();
                console.log('[SB] Response data:', data);
                
                if (data.success) {
                    if (notif) {
                        notif.className = 'mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800';
                        notif.innerHTML = '✅ TNC saved successfully! (' + points.length + ' points)';
                        notif.classList.remove('hidden');
                        setTimeout(() => notif.classList.add('hidden'), 3000);
                    }
                } else {
                    if (notif) {
                        notif.className = 'mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800';
                        notif.innerHTML = '❌ ' + data.message;
                        notif.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error('[SB] Error:', err);
                if (notif) {
                    notif.className = 'mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800';
                    notif.innerHTML = '❌ ' + err.message;
                    notif.classList.remove('hidden');
                }
            }
            
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Save TNC';
        }
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => SB.init());
    </script>
</body>
</html>