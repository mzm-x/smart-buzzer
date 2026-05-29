<?php
/**
 * ============================================================================
 * File: /submit/audit-list.php
 * Smart Buzzer Free Marketing Audit — NDA Agreement Viewer (v1.0)
 *
 * Read-only dashboard for AM team to see who agreed to the Free Marketing
 * Audit NDA (logged by audit-log.php into data/audit_requests.json).
 *
 * Features:
 * - Auth-gated (same requireAuth() as manage.php)
 * - Stats cards: Total / Today / This Week / This Month
 * - Searchable table by business name / email / WhatsApp / order ID
 * - WhatsApp + Email follow-up buttons per row
 * - CSV export of current view
 * ============================================================================
 */

ini_set('session.gc_maxlifetime', 31536000);
ini_set('session.cookie_lifetime', 31536000);
session_start();

require_once __DIR__ . '/auth.php';
requireAuth();

$csrfToken = generateCSRFToken();

$auditFile = __DIR__ . '/data/audit_requests.json';
$auditRequests = [];
if (file_exists($auditFile)) {
    $jsonRaw = file_get_contents($auditFile);
    $parsed = json_decode($jsonRaw, true);
    if (is_array($parsed)) {
        $auditRequests = $parsed;
    }
}

usort($auditRequests, function ($a, $b) {
    $ta = !empty($a['timestamp']) ? strtotime($a['timestamp']) : 0;
    $tb = !empty($b['timestamp']) ? strtotime($b['timestamp']) : 0;
    if ($ta === false) $ta = 0;
    if ($tb === false) $tb = 0;
    return $tb - $ta;
});

$nowTs = time();
$startOfToday = strtotime(date('Y-m-d 00:00:00', $nowTs));
$startOfWeek = strtotime('-' . (int)date('w', $nowTs) . ' days', $startOfToday);
$startOfMonth = strtotime(date('Y-m-01 00:00:00', $nowTs));

$totalAudits = count($auditRequests);
$todayCount = 0;
$weekCount = 0;
$monthCount = 0;
foreach ($auditRequests as $row) {
    $ts = !empty($row['timestamp']) ? strtotime($row['timestamp']) : 0;
    if ($ts >= $startOfMonth) $monthCount++;
    if ($ts >= $startOfWeek)  $weekCount++;
    if ($ts >= $startOfToday) $todayCount++;
}

function audit_safe_json($data) {
    return htmlspecialchars(
        json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE),
        ENT_QUOTES,
        'UTF-8'
    );
}

// --- Recent attempts (success + failure) read from audit_attempts.log -------
$attemptsFile = __DIR__ . '/data/audit_attempts.log';
$recentAttempts = [];
$attemptStats = ['success' => 0, 'fail' => 0, 'csrf_warn' => 0];
if (file_exists($attemptsFile)) {
    $lines = @file($attemptsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        $lines = array_slice($lines, -50);
        foreach ($lines as $ln) {
            $row = json_decode($ln, true);
            if (!is_array($row)) continue;
            $recentAttempts[] = $row;
            $st = $row['status'] ?? '';
            if ($st === 'success') $attemptStats['success']++;
            elseif ($st === 'csrf_warn') $attemptStats['csrf_warn']++;
            else $attemptStats['fail']++;
        }
        $recentAttempts = array_reverse($recentAttempts); // newest first
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Requests — Smart Buzzer Dashboard</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', sans-serif; }
        .sb-tab { padding: 12px 20px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .sb-tab:hover { color: #111827; }
        .sb-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
        .sb-table-row { transition: background-color 0.15s ease; }
        .sb-table-row:hover { background-color: #f0f9ff; }
        .sb-type-badge { font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }
        .sb-type-reviews { background: #dbeafe; color: #1e40af; }
        .sb-type-social { background: #ede9fe; color: #5b21b6; }
        .sb-type-unknown { background: #f3f4f6; color: #374151; }
        .sb-empty { color: #9ca3af; }
        .sb-attempt-badge { font-size: 0.65rem; padding: 0.15rem 0.5rem; border-radius: 9999px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .sb-attempt-success { background: #d1fae5; color: #065f46; }
        .sb-attempt-warn    { background: #fef3c7; color: #92400e; }
        .sb-attempt-fail    { background: #fee2e2; color: #991b1b; }
        details.sb-diag > summary { cursor: pointer; list-style: none; }
        details.sb-diag > summary::-webkit-details-marker { display: none; }
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
                <a href="manage.php?tab=orders" class="sb-tab">📦 Orders</a>
                <a href="manage.php?tab=tnc" class="sb-tab">📋 Terms &amp; Conditions</a>
                <a href="audit-list.php" class="sb-tab active">🎁 Audit Requests</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">

        <!-- Page heading -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">🎁 Free Marketing Audit Requests</h1>
            <p class="text-sm text-gray-500 mt-1">Loyal clients who agreed to the NDA and clicked the "Open WhatsApp" CTA on the Thank You screen.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">Total Agreed</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $totalAudits; ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">Today</p>
                <p class="text-2xl font-bold text-emerald-500"><?php echo $todayCount; ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">This Week</p>
                <p class="text-2xl font-bold text-blue-500"><?php echo $weekCount; ?></p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-200">
                <p class="text-sm text-gray-500 mb-1">This Month</p>
                <p class="text-2xl font-bold text-purple-500"><?php echo $monthCount; ?></p>
            </div>
        </div>

        <!-- Diagnostic Panel: Recent Attempts (success + failure) -->
        <?php if (!empty($recentAttempts)): ?>
        <details class="sb-diag bg-white rounded-2xl border border-gray-200 mb-6">
            <summary class="px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-base font-semibold text-gray-900">🩺 Recent Attempts (Diagnostics)</span>
                    <span class="text-xs text-gray-500">Last <?php echo count($recentAttempts); ?> POST attempts to audit-log.php</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="sb-attempt-badge sb-attempt-success">✓ <?php echo $attemptStats['success']; ?> success</span>
                    <?php if ($attemptStats['csrf_warn'] > 0): ?>
                        <span class="sb-attempt-badge sb-attempt-warn">⚠ <?php echo $attemptStats['csrf_warn']; ?> csrf-warn</span>
                    <?php endif; ?>
                    <?php if ($attemptStats['fail'] > 0): ?>
                        <span class="sb-attempt-badge sb-attempt-fail">✗ <?php echo $attemptStats['fail']; ?> fail</span>
                    <?php endif; ?>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </summary>
            <div class="border-t border-gray-100 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                            <th class="px-4 py-2">Time (UTC)</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Reason</th>
                            <th class="px-4 py-2">Business</th>
                            <th class="px-4 py-2">Order ID</th>
                            <th class="px-4 py-2">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentAttempts as $att):
                        $st = $att['status'] ?? '-';
                        $reason = $att['reason'] ?? '-';
                        $ip = $att['ip'] ?? '-';
                        $pay = $att['payload'] ?? [];
                        $biz = $pay['businessName'] ?? '';
                        $oid = $pay['orderId'] ?? '';

                        if ($st === 'success') { $stClass = 'sb-attempt-success'; $stLabel = '✓ Success'; }
                        elseif ($st === 'csrf_warn') { $stClass = 'sb-attempt-warn'; $stLabel = '⚠ CSRF Warn'; }
                        else { $stClass = 'sb-attempt-fail'; $stLabel = '✗ ' . ucfirst(str_replace('_', ' ', (string)$st)); }
                    ?>
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-2 text-xs text-gray-600 whitespace-nowrap font-mono"><?php echo htmlspecialchars($att['ts'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-2"><span class="sb-attempt-badge <?php echo $stClass; ?>"><?php echo htmlspecialchars($stLabel, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="px-4 py-2 text-xs text-gray-700"><?php echo htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-2 text-xs text-gray-700"><?php echo $biz ? htmlspecialchars($biz, ENT_QUOTES, 'UTF-8') : '<span class="text-gray-400">—</span>'; ?></td>
                            <td class="px-4 py-2 text-xs text-gray-500 font-mono"><?php echo $oid ? htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') : '<span class="text-gray-400">—</span>'; ?></td>
                            <td class="px-4 py-2 text-xs text-gray-500 font-mono"><?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </details>
        <?php endif; ?>

        <!-- Toolbar -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 mb-6 flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1">
                <input id="auditSearch" type="text" placeholder="🔍 Search business name, email, WhatsApp, order ID..."
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <select id="auditTypeFilter" class="px-4 py-2 border border-gray-200 rounded-xl text-sm bg-white">
                <option value="">All Types</option>
                <option value="reviews">Reviews</option>
                <option value="social_media">Social Media</option>
            </select>
            <button id="auditExportBtn" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-medium transition flex items-center gap-2">
                📥 Export CSV
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <?php if ($totalAudits === 0): ?>
                <div class="p-12 text-center sb-empty">
                    <div class="text-5xl mb-3">📭</div>
                    <p class="text-base font-medium text-gray-700">No audit requests yet</p>
                    <p class="text-sm text-gray-500 mt-1">Loyal clients who click "I AGREE — OPEN WHATSAPP" will appear here.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Business</th>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">WhatsApp</th>
                                <th class="px-4 py-3">IP / Referer</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="auditTableBody">
                        <?php foreach ($auditRequests as $row):
                            $ts = !empty($row['timestamp']) ? strtotime($row['timestamp']) : 0;
                            $timeStr = $ts ? date('M d, Y · H:i', $ts) : '-';
                            $biz = $row['businessName'] ?? '-';
                            $orderId = $row['orderId'] ?? '-';
                            $type = $row['orderType'] ?? 'unknown';
                            $email = $row['email'] ?? '';
                            $wa = $row['whatsapp'] ?? '';
                            $ip = $row['ip'] ?? '-';
                            $ref = $row['referer'] ?? '';
                            $refShort = $ref ? (strlen($ref) > 40 ? substr($ref, 0, 40) . '…' : $ref) : '';

                            $typeClass = $type === 'reviews' ? 'sb-type-reviews' : ($type === 'social_media' ? 'sb-type-social' : 'sb-type-unknown');
                            $typeLabel = $type === 'reviews' ? 'Reviews' : ($type === 'social_media' ? 'Social Media' : 'Unknown');

                            $waDigits = preg_replace('/[^0-9]/', '', $wa);
                            $searchBlob = strtolower(implode(' ', [$biz, $orderId, $email, $wa, $type]));
                        ?>
                            <tr class="sb-table-row border-b border-gray-100" data-search="<?php echo htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8'); ?>" data-type="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap"><?php echo htmlspecialchars($timeStr, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-4 py-3 font-medium text-gray-900"><?php echo htmlspecialchars($biz, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs"><?php echo htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-4 py-3"><span class="sb-type-badge <?php echo $typeClass; ?>"><?php echo htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td class="px-4 py-3 text-gray-700"><?php echo $email ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : '<span class="text-gray-400">—</span>'; ?></td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap"><?php echo $wa ? htmlspecialchars($wa, ENT_QUOTES, 'UTF-8') : '<span class="text-gray-400">—</span>'; ?></td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    <div><?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if ($refShort): ?>
                                        <div class="text-gray-400" title="<?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($refShort, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <?php if ($waDigits): ?>
                                            <a href="https://wa.me/<?php echo htmlspecialchars($waDigits, ENT_QUOTES, 'UTF-8'); ?>?text=<?php echo rawurlencode('Hi ' . $biz . ', following up on your Free Marketing Audit request — Smart Buzzer'); ?>" target="_blank" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-medium">📞 WA</a>
                                        <?php endif; ?>
                                        <?php if ($email): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>?subject=<?php echo rawurlencode('Your Free Marketing Audit — Smart Buzzer'); ?>" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium">📧 Mail</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="auditNoResults" class="p-8 text-center sb-empty hidden">
                    <p class="text-sm">No matching audit requests found.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script>
        (function () {
            var auditRows = Array.prototype.slice.call(document.querySelectorAll('#auditTableBody tr'));
            var searchInput = document.getElementById('auditSearch');
            var typeFilter = document.getElementById('auditTypeFilter');
            var noResults = document.getElementById('auditNoResults');
            var exportBtn = document.getElementById('auditExportBtn');

            function applyFilter() {
                if (!auditRows.length) return;
                var q = (searchInput && searchInput.value || '').toLowerCase().trim();
                var t = (typeFilter && typeFilter.value || '').trim();
                var visible = 0;
                auditRows.forEach(function (row) {
                    var blob = row.getAttribute('data-search') || '';
                    var rowType = row.getAttribute('data-type') || '';
                    var matchSearch = !q || blob.indexOf(q) !== -1;
                    var matchType = !t || rowType === t;
                    var show = matchSearch && matchType;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                if (noResults) {
                    noResults.classList.toggle('hidden', visible !== 0);
                }
            }

            if (searchInput) searchInput.addEventListener('input', applyFilter);
            if (typeFilter) typeFilter.addEventListener('change', applyFilter);

            function csvEscape(v) {
                if (v == null) return '';
                v = String(v).replace(/"/g, '""');
                return /[",\n\r]/.test(v) ? '"' + v + '"' : v;
            }

            if (exportBtn) {
                exportBtn.addEventListener('click', function () {
                    var visibleRows = auditRows.filter(function (r) { return r.style.display !== 'none'; });
                    var rowsOut = [['Time', 'Business', 'Order ID', 'Type', 'Email', 'WhatsApp', 'IP', 'Referer']];
                    visibleRows.forEach(function (r) {
                        var cells = r.querySelectorAll('td');
                        rowsOut.push([
                            cells[0] ? cells[0].textContent.trim() : '',
                            cells[1] ? cells[1].textContent.trim() : '',
                            cells[2] ? cells[2].textContent.trim() : '',
                            cells[3] ? cells[3].textContent.trim() : '',
                            cells[4] ? cells[4].textContent.trim() : '',
                            cells[5] ? cells[5].textContent.trim() : '',
                            cells[6] ? (cells[6].querySelector('div') ? cells[6].querySelector('div').textContent.trim() : cells[6].textContent.trim()) : '',
                            cells[6] && cells[6].querySelectorAll('div')[1] ? (cells[6].querySelectorAll('div')[1].getAttribute('title') || cells[6].querySelectorAll('div')[1].textContent.trim()) : ''
                        ]);
                    });
                    var csv = rowsOut.map(function (r) { return r.map(csvEscape).join(','); }).join('\r\n');
                    var blob = new Blob(["﻿" + csv], { type: 'text/csv;charset=utf-8;' });
                    var url = URL.createObjectURL(blob);
                    var stamp = new Date().toISOString().slice(0, 10);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'audit_requests_' + stamp + '.csv';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                });
            }
        })();
    </script>
</body>
</html>
