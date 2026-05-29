<?php
/**
 * /demography/index.php - Customer Demographics Dashboard v2.0
 * Reads data from /submit/data/submissions_log.json
 *
 * v2.0: Date range filter, trend line chart, CSV export, status breakdown,
 *       table column sorting, empty states, search filter
 */

session_start();
$PASSWORD = 'smartbuzzer2025';

if (isset($_POST['logout'])) { unset($_SESSION['demo_auth']); header('Location: ' . $_SERVER['PHP_SELF']); exit; }
if (isset($_POST['password'])) { if ($_POST['password'] === $PASSWORD) $_SESSION['demo_auth'] = true; }

if (!isset($_SESSION['demo_auth']) || $_SESSION['demo_auth'] !== true) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demographics - Login</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',sans-serif;background:#F2F2F7;min-height:100vh;display:flex;align-items:center;justify-content:center;-webkit-font-smoothing:antialiased}
.login-box{background:rgba(255,255,255,0.8);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(0,0,0,0.06);border-radius:20px;padding:48px 40px;width:100%;max-width:400px;text-align:center;box-shadow:0 4px 16px rgba(0,0,0,0.06),0 16px 48px rgba(0,0,0,0.06)}
h1{font-size:28px;font-weight:700;color:#1D1D1F;margin-bottom:6px;letter-spacing:-0.5px}
.sub{color:#86868B;font-size:15px;margin-bottom:32px}
input[type="password"]{width:100%;padding:16px 20px;background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.08);border-radius:14px;font-size:16px;color:#1D1D1F;outline:none;transition:all .3s;margin-bottom:16px}
input[type="password"]:focus{border-color:#FF6B35;background:#fff;box-shadow:0 0 0 4px rgba(255,107,53,0.1)}
button{width:100%;padding:16px;background:linear-gradient(135deg,#FF6B35,#FF8F5E);border:none;border-radius:14px;color:#fff;font-size:16px;font-weight:600;cursor:pointer;transition:all .3s}
button:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(255,107,53,0.3)}
</style>
</head>
<body>
<div class="login-box">
<h1>Demographics</h1>
<p class="sub">Smart Buzzer Analytics</p>
<form method="POST"><input type="password" name="password" placeholder="Enter password" required autofocus><button type="submit">Access Dashboard</button></form>
</div>
</body>
</html>
<?php exit; }

// ===== LOAD DATA =====
$logFile = __DIR__ . '/../submit/data/submissions_log.json';
$allLogs = [];
if (file_exists($logFile)) {
    $allLogs = json_decode(file_get_contents($logFile), true) ?? [];
}

// ===== CSV EXPORT =====
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="demographics_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Order ID','Timestamp','Type','Source','Business Name','Email','Country','State','Industry','Quantity','Platform','Status','Device']);
    foreach ($allLogs as $l) {
        fputcsv($out, [
            $l['orderId'] ?? '', $l['timestamp'] ?? '', $l['orderType'] ?? 'Review',
            $l['source'] ?? '', $l['businessName'] ?? '', $l['email'] ?? '',
            $l['country'] ?? '', $l['state'] ?? '', $l['businessIndustry'] ?? '',
            $l['quantity'] ?? '', $l['platform'] ?? '', $l['status'] ?? 'Pending',
            $l['device'] ?? ''
        ]);
    }
    fclose($out);
    exit;
}

// Pass all data to JS for client-side filtering
$allLogsJson = json_encode($allLogs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Demographics - Smart Buzzer</title>
<link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', sans-serif;
    background: #F2F2F7;
    color: #1D1D1F;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-in { opacity: 0; animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.d1 { animation-delay: .05s } .d2 { animation-delay: .1s } .d3 { animation-delay: .15s }
.d4 { animation-delay: .2s } .d5 { animation-delay: .25s } .d6 { animation-delay: .3s }

/* Header */
.header {
    background: rgba(255,255,255,0.72);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(0,0,0,0.06);
    padding: 16px 24px;
    position: sticky; top: 0; z-index: 100;
}
.header-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
.header-title { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
.header-title span { color: #FF6B35; }
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

.btn {
    padding: 10px 20px; border-radius: 12px; border: none; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.3s cubic-bezier(0.25,0.1,0.25,1); text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn:hover { transform: translateY(-1px); }
.btn:active { transform: scale(0.97); }
.btn-primary { background: linear-gradient(135deg,#FF6B35,#FF8F5E); color: #fff; }
.btn-primary:hover { box-shadow: 0 4px 16px rgba(255,107,53,0.3); }
.btn-secondary { background: rgba(0,0,0,0.05); color: #1D1D1F; }
.btn-secondary:hover { background: rgba(0,0,0,0.08); }
.btn-danger { background: rgba(255,59,48,0.1); color: #FF3B30; }
.btn-green { background: rgba(52,199,89,0.1); color: #34C759; }
.btn-green:hover { background: rgba(52,199,89,0.2); }

/* Preset Buttons */
.preset-group { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
.btn-preset {
    padding: 7px 13px; border-radius: 9px; border: 1px solid rgba(0,0,0,0.08);
    font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    background: rgba(0,0,0,0.03); color: #86868B; white-space: nowrap;
}
.btn-preset:hover { background: rgba(255,107,53,0.1); color: #FF6B35; border-color: rgba(255,107,53,0.25); }
.btn-preset.active { background: linear-gradient(135deg,#FF6B35,#FF8F5E); color: #fff; border-color: transparent; box-shadow: 0 2px 8px rgba(255,107,53,0.3); }

/* MoM Section */
.mom-mini-stats { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
.mom-mini-card { border-radius: 14px; padding: 14px 20px; min-width: 100px; }
.mom-mini-val { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
.mom-mini-label { font-size: 12px; color: #86868B; margin-top: 3px; }
.mom-bar-wrap { position: relative; width: 100%; height: 260px; }
.mom-bar-wrap canvas { width: 100% !important; height: 100% !important; }

.container { max-width: 1200px; margin: 0 auto; padding: 24px; }

/* Filter Bar */
.filter-bar {
    background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(0,0,0,0.06); border-radius: 20px; padding: 16px 20px;
    margin-bottom: 24px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.filter-bar label { font-size: 13px; font-weight: 600; color: #86868B; }
.filter-input {
    padding: 10px 14px; background: rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.08);
    border-radius: 12px; font-size: 14px; color: #1D1D1F; outline: none; transition: all .2s;
}
.filter-input:focus { border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
.filter-search { flex: 1; min-width: 180px; }
.filter-select { min-width: 140px; }

/* Stat Cards */
.stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 24px; }
.stat-card {
    background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(0,0,0,0.06); border-radius: 20px; padding: 22px;
    transition: all 0.3s cubic-bezier(0.25,0.1,0.25,1); box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
.stat-value { font-size: 30px; font-weight: 700; letter-spacing: -1px; margin-bottom: 2px; }
.stat-label { font-size: 12px; color: #86868B; font-weight: 500; }
.stat-sub { font-size: 11px; color: #AEAEB2; margin-top: 3px; }
.stat-card.orange .stat-value { color: #FF6B35; }
.stat-card.green .stat-value { color: #34C759; }
.stat-card.blue .stat-value { color: #007AFF; }
.stat-card.purple .stat-value { color: #AF52DE; }
.stat-card.teal .stat-value { color: #5AC8FA; }
.stat-card.pink .stat-value { color: #FF2D55; }

/* Section Cards */
.section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.section-card {
    background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(0,0,0,0.06); border-radius: 20px; padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: all 0.3s cubic-bezier(0.25,0.1,0.25,1);
}
.section-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
.section-card.full { grid-column: 1 / -1; }
.section-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; letter-spacing: -0.3px; display: flex; align-items: center; gap: 8px; }
.section-title .icon { width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
.icon-blue { background: rgba(0,122,255,0.1); }
.icon-purple { background: rgba(175,82,222,0.1); }
.icon-green { background: rgba(52,199,89,0.1); }
.icon-orange { background: rgba(255,107,53,0.1); }
.icon-teal { background: rgba(90,200,250,0.1); }
.icon-pink { background: rgba(255,45,85,0.1); }
.icon-yellow { background: rgba(255,214,10,0.15); }

/* Chart layouts */
.chart-row { display: flex; gap: 20px; align-items: flex-start; }
.chart-canvas-wrap { flex: 0 0 180px; width: 180px; height: 180px; position: relative; }
.chart-canvas-wrap canvas { position: absolute; top: 0; left: 0; width: 100% !important; height: 100% !important; }
.chart-table-wrap { flex: 1; min-width: 0; max-height: 420px; overflow-y: auto; }
.trend-wrap { position: relative; width: 100%; height: 220px; }
.trend-wrap canvas { width: 100% !important; height: 100% !important; }

/* Tables */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    text-align: left; padding: 7px 10px; font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.5px; color: #86868B;
    border-bottom: 1px solid rgba(0,0,0,0.06); position: sticky; top: 0;
    background: rgba(255,255,255,0.95); cursor: pointer; user-select: none;
}
.data-table th:hover { color: #1D1D1F; }
.data-table th.sorted-asc::after { content: ' ▲'; font-size: 9px; }
.data-table th.sorted-desc::after { content: ' ▼'; font-size: 9px; }
.data-table td { padding: 8px 10px; font-size: 13px; border-bottom: 1px solid rgba(0,0,0,0.04); }
.data-table tr:hover td { background: rgba(0,0,0,0.02); }
.data-table td:last-child, .data-table th:last-child { text-align: right; }

.pct-bar { display: inline-block; height: 5px; border-radius: 3px; background: #FF6B35; margin-right: 6px; vertical-align: middle; }
.rank-badge { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 6px; font-size: 10px; font-weight: 700; margin-right: 6px; }
.rank-1 { background: #FFD60A; color: #1D1D1F; }
.rank-2 { background: #D1D1D6; color: #1D1D1F; }
.rank-3 { background: #E4A574; color: #fff; }
.rank-other { background: rgba(0,0,0,0.04); color: #86868B; }

/* Horizontal Bars */
.hbar-list { list-style: none; }
.hbar-item { display: flex; align-items: center; margin-bottom: 8px; }
.hbar-label { width: 140px; font-size: 12px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex-shrink: 0; }
.hbar-track { flex: 1; height: 26px; background: rgba(0,0,0,0.03); border-radius: 8px; overflow: hidden; margin: 0 10px; }
.hbar-fill { height: 100%; border-radius: 8px; transition: width 0.8s cubic-bezier(0.16,1,0.3,1); display: flex; align-items: center; padding-left: 8px; font-size: 11px; font-weight: 600; color: #fff; min-width: 24px; }
.hbar-count { font-size: 13px; font-weight: 600; color: #1D1D1F; width: 36px; text-align: right; flex-shrink: 0; }

.fill-blue { background: linear-gradient(135deg,#007AFF,#5AC8FA); }
.fill-purple { background: linear-gradient(135deg,#AF52DE,#DA7FFF); }
.fill-green { background: linear-gradient(135deg,#34C759,#7AE28C); }
.fill-orange { background: linear-gradient(135deg,#FF6B35,#FF9F6B); }
.fill-teal { background: linear-gradient(135deg,#5AC8FA,#96DEFF); }
.fill-pink { background: linear-gradient(135deg,#FF2D55,#FF6B8A); }
.fill-yellow { background: linear-gradient(135deg,#FFD60A,#FFE066); }

/* Status badges */
.status-badge { padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; }
.status-pending { background: rgba(255,214,10,0.15); color: #B8860B; }
.status-processing { background: rgba(0,122,255,0.1); color: #007AFF; }
.status-completed { background: rgba(52,199,89,0.1); color: #34C759; }
.status-cancelled { background: rgba(255,59,48,0.1); color: #FF3B30; }
.status-onhold { background: rgba(0,0,0,0.05); color: #86868B; }

/* Empty state */
.empty-state { text-align: center; padding: 48px 20px; color: #AEAEB2; }
.empty-state .icon { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.empty-state p { font-size: 15px; }

/* Scrollbar */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 3px; }

/* Print */
@media print {
    .header, .filter-bar, .btn { display: none !important; }
    .stat-card, .section-card { break-inside: avoid; box-shadow: none; border: 1px solid #ddd; }
    body { background: #fff; }
}

@media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .section-grid { grid-template-columns: 1fr; }
    .chart-row { flex-direction: column; align-items: center; }
    .chart-canvas-wrap { flex: none; width: 180px; height: 180px; margin-bottom: 12px; }
    .hbar-label { width: 100px; }
    .header-inner { flex-direction: column; }
    .filter-bar { flex-direction: column; align-items: stretch; }
    .stat-card, .section-card { border-radius: 16px; padding: 18px; }
}
</style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div class="header-title">Customer <span>Demographics</span></div>
        <div class="header-actions">
            <a href="?export=csv" class="btn btn-green">Export CSV</a>
            <button class="btn btn-secondary" onclick="location.reload()">Refresh</button>
            <form method="POST" style="display:inline;"><button type="submit" name="logout" class="btn btn-danger">Logout</button></form>
        </div>
    </div>
</div>

<div class="container">

    <!-- Filter Bar -->
    <div class="filter-bar animate-in d1">
        <label>Date Range:</label>
        <input type="text" id="dateRange" class="filter-input" placeholder="All time" readonly style="width:200px;cursor:pointer;">
        <div class="preset-group">
            <button class="btn-preset" data-preset="today" onclick="setPreset('today')">Today</button>
            <button class="btn-preset" data-preset="7d" onclick="setPreset('7d')">7D</button>
            <button class="btn-preset" data-preset="30d" onclick="setPreset('30d')">30D</button>
            <button class="btn-preset" data-preset="month" onclick="setPreset('month')">This Month</button>
            <button class="btn-preset" data-preset="lastmonth" onclick="setPreset('lastmonth')">Last Month</button>
            <button class="btn-preset active" data-preset="all" onclick="setPreset('all')">All Time</button>
        </div>
        <label>Search:</label>
        <input type="text" id="searchInput" class="filter-input filter-search" placeholder="Search state, industry, source...">
        <label>Status:</label>
        <select id="statusFilter" class="filter-input filter-select">
            <option value="">All Statuses</option>
            <option value="Pending">Pending</option>
            <option value="Processing">Processing</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
            <option value="On Hold">On Hold</option>
        </select>
        <button class="btn btn-secondary" onclick="clearAllFilters()">Clear</button>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid" id="statsGrid"></div>

    <!-- Trend Chart -->
    <div class="section-grid">
        <div class="section-card full animate-in d2">
            <div class="section-title"><div class="icon icon-orange">📈</div> Submissions Trend</div>
            <div class="trend-wrap"><canvas id="trendChart"></canvas></div>
        </div>
    </div>

    <!-- Month over Month -->
    <div class="section-grid">
        <div class="section-card full animate-in d3" id="momSection">
            <div class="section-title"><div class="icon icon-purple">📊</div> Month over Month</div>
            <div class="mom-mini-stats" id="momStats"></div>
            <div class="mom-bar-wrap"><canvas id="momChart"></canvas></div>
            <div id="momTable" style="margin-top:16px;max-height:220px;overflow-y:auto;"></div>
        </div>
    </div>

    <!-- State + Industry -->
    <div class="section-grid">
        <div class="section-card animate-in d2" id="stateSection"></div>
        <div class="section-card animate-in d3" id="industrySection"></div>
    </div>

    <!-- Device + Source -->
    <div class="section-grid">
        <div class="section-card animate-in d4" id="deviceSection"></div>
        <div class="section-card animate-in d5" id="sourceSection"></div>
    </div>

    <!-- Status Breakdown + Package -->
    <div class="section-grid">
        <div class="section-card animate-in d4" id="statusSection"></div>
        <div class="section-card animate-in d5" id="packageSection"></div>
    </div>

    <!-- Platform + Country -->
    <div class="section-grid">
        <div class="section-card animate-in d2" id="platformSection"></div>
        <div class="section-card animate-in d3" id="countrySection"></div>
    </div>

    <div id="footerInfo" style="text-align:center;padding:24px 0;color:#AEAEB2;font-size:13px;"></div>
</div>

<script>
// ===== ALL DATA =====
var ALL_DATA = <?= $allLogsJson ?>;
var chartInstances = {};
var dateFrom = null, dateTo = null;

// ===== FLATPICKR =====
flatpickr('#dateRange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    onChange: function(dates, dateStr, instance) {
        if (dates.length === 2) {
            dateFrom = dates[0]; dateTo = dates[1];
            dateTo.setHours(23,59,59);
            // Clear preset active state on manual pick
            document.querySelectorAll('.btn-preset').forEach(function(b) { b.classList.remove('active'); });
            applyFilters();
        }
    }
});

document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 300));
document.getElementById('statusFilter').addEventListener('change', applyFilters);

function debounce(fn, ms) { var t; return function() { clearTimeout(t); t = setTimeout(fn, ms); }; }

// ===== PRESET DATE RANGES =====
function setPreset(preset) {
    var now = new Date();
    var fp = document.getElementById('dateRange')._flatpickr;
    var from, to;

    document.querySelectorAll('.btn-preset').forEach(function(b) { b.classList.remove('active'); });
    document.querySelector('.btn-preset[data-preset="'+preset+'"]').classList.add('active');

    if (preset === 'all') {
        dateFrom = null; dateTo = null;
        fp.clear();
        applyFilters();
        return;
    }
    if (preset === 'today') {
        from = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        to   = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
    } else if (preset === '7d') {
        from = new Date(now); from.setDate(from.getDate() - 6); from.setHours(0,0,0,0);
        to   = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
    } else if (preset === '30d') {
        from = new Date(now); from.setDate(from.getDate() - 29); from.setHours(0,0,0,0);
        to   = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
    } else if (preset === 'month') {
        from = new Date(now.getFullYear(), now.getMonth(), 1);
        to   = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
    } else if (preset === 'lastmonth') {
        var pm = now.getMonth() - 1, py = now.getFullYear();
        if (pm < 0) { pm = 11; py--; }
        from = new Date(py, pm, 1);
        to   = new Date(py, pm + 1, 0, 23, 59, 59);
    }
    dateFrom = from; dateTo = to;
    fp.setDate([from, to], false);
    applyFilters();
}

function clearAllFilters() {
    dateFrom = null; dateTo = null;
    document.getElementById('dateRange')._flatpickr.clear();
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.querySelectorAll('.btn-preset').forEach(function(b) { b.classList.remove('active'); });
    document.querySelector('.btn-preset[data-preset="all"]').classList.add('active');
    applyFilters();
}

// ===== MONTH OVER MONTH =====
function renderMoMChart(logs) {
    if (chartInstances.mom) { chartInstances.mom.destroy(); chartInstances.mom = null; }

    var byMonth = {};
    logs.forEach(function(l) {
        var ym = (l.timestamp || '').substring(0, 7);
        if (ym.length === 7) byMonth[ym] = (byMonth[ym] || 0) + 1;
    });

    var months = Object.keys(byMonth).sort();
    var values = months.map(function(m) { return byMonth[m]; });

    var deltas = values.map(function(v, i) {
        if (i === 0 || values[i-1] === 0) return null;
        return ((v - values[i-1]) / values[i-1] * 100).toFixed(1);
    });

    var labels = months.map(function(m) {
        var p = m.split('-');
        return new Date(+p[0], +p[1]-1, 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });

    // Mini stat cards
    var n = values.length;
    var cur = n > 0 ? values[n-1] : 0;
    var prev = n > 1 ? values[n-2] : null;
    var lastDelta = n > 1 ? deltas[n-1] : null;
    var isPos = lastDelta !== null && parseFloat(lastDelta) >= 0;
    var sign = lastDelta !== null ? (isPos ? '+' : '') : '';

    var mini = '';
    if (lastDelta !== null) {
        mini += '<div class="mom-mini-card" style="background:rgba('+(isPos?'52,199,89':'255,59,48')+',0.1);">' +
            '<div class="mom-mini-val" style="color:'+(isPos?'#34C759':'#FF3B30')+'">'+sign+lastDelta+'%</div>' +
            '<div class="mom-mini-label">'+(isPos?'&#9650;':'&#9660;')+' vs Last Month</div></div>';
    }
    mini += '<div class="mom-mini-card" style="background:rgba(0,122,255,0.08);">' +
        '<div class="mom-mini-val" style="color:#007AFF">'+cur+'</div>' +
        '<div class="mom-mini-label">'+labels[n-1]+'</div></div>';
    if (prev !== null) {
        mini += '<div class="mom-mini-card" style="background:rgba(0,0,0,0.04);">' +
            '<div class="mom-mini-val" style="color:#86868B">'+prev+'</div>' +
            '<div class="mom-mini-label">'+labels[n-2]+'</div></div>';
    }
    // Best month
    if (n > 0) {
        var maxVal = Math.max.apply(null, values);
        var maxIdx = values.indexOf(maxVal);
        mini += '<div class="mom-mini-card" style="background:rgba(255,214,10,0.12);">' +
            '<div class="mom-mini-val" style="color:#B8860B">'+maxVal+'</div>' +
            '<div class="mom-mini-label">&#127942; Best: '+labels[maxIdx]+'</div></div>';
    }
    document.getElementById('momStats').innerHTML = mini || '';

    if (months.length === 0) {
        document.getElementById('momChart').style.display = 'none';
        document.getElementById('momTable').innerHTML = '<div class="empty-state"><div class="icon">&#128205;</div><p>No monthly data available</p></div>';
        return;
    }
    document.getElementById('momChart').style.display = '';

    var barColors = values.map(function(v, i) {
        if (i === 0) return 'rgba(0,122,255,0.65)';
        var d = deltas[i];
        if (d === null) return 'rgba(0,122,255,0.65)';
        return parseFloat(d) >= 0 ? 'rgba(52,199,89,0.7)' : 'rgba(255,59,48,0.65)';
    });
    var borderColors = barColors.map(function(c) { return c.replace(/[\d.]+\)$/, '1)'); });

    chartInstances.mom = new Chart(document.getElementById('momChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Submissions',
                data: values,
                backgroundColor: barColors,
                borderColor: borderColors,
                borderWidth: 1.5,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.82)', cornerRadius: 10, padding: 12,
                    callbacks: {
                        title: function(ctx) { return ctx[0].label; },
                        label: function(ctx) { return ' Submissions: ' + ctx.raw; },
                        afterLabel: function(ctx) {
                            var d = deltas[ctx.dataIndex];
                            if (d === null) return ' First recorded month';
                            return ' vs prev: ' + (parseFloat(d) >= 0 ? '▲ +' : '▼ ') + d + '%';
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 45 } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, stepSize: 1 } }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });

    // MoM table (newest first)
    var rows = months.map(function(m, i) {
        var d = deltas[i];
        var diff = i === 0 ? '—' : (values[i] - values[i-1] >= 0 ? '+' + (values[i]-values[i-1]) : ''+(values[i]-values[i-1]));
        var deltaHtml = d === null ? '<span style="color:#AEAEB2">—</span>' :
            '<span style="font-weight:600;color:'+(parseFloat(d)>=0?'#34C759':'#FF3B30')+'">' +
            (parseFloat(d)>=0?'&#9650; +':'&#9660; ')+d+'%</span>';
        return '<tr><td><strong>'+labels[i]+'</strong></td><td>'+values[i]+'</td><td style="color:'+(i>0&&values[i]>=values[i-1]?'#34C759':'#FF3B30')+';font-weight:600">'+diff+'</td><td>'+deltaHtml+'</td></tr>';
    }).reverse().join('');

    document.getElementById('momTable').innerHTML =
        '<table class="data-table"><thead><tr>' +
        '<th>Month</th><th>Submissions</th><th>Change</th><th>% Change</th>' +
        '</tr></thead><tbody>'+rows+'</tbody></table>';
}

// ===== FILTER =====
function getFilteredData() {
    var search = document.getElementById('searchInput').value.toLowerCase();
    var status = document.getElementById('statusFilter').value;

    return ALL_DATA.filter(function(l) {
        if (dateFrom && dateTo) {
            var ts = new Date((l.timestamp || '').replace(' ', 'T'));
            if (isNaN(ts.getTime()) || ts < dateFrom || ts > dateTo) return false;
        }
        if (status && (l.status || 'Pending') !== status) return false;
        if (search) {
            var haystack = [l.state, l.businessIndustry, l.source, l.businessName, l.country, l.platform, l.email].join(' ').toLowerCase();
            if (haystack.indexOf(search) === -1) return false;
        }
        return true;
    });
}

// ===== AGGREGATE =====
function aggregate(logs) {
    var r = { states:{}, industries:{}, devices:{}, sources:{}, packages:{}, platforms:{}, countries:{}, statuses:{}, byDate:{} };
    logs.forEach(function(l) {
        var st = l.state || ''; var ind = l.businessIndustry || ''; var dev = l.device || 'Desktop';
        var src = l.source || 'Direct'; var qty = l.quantity || ''; var plat = l.platform || 'Google';
        var ctry = l.country || ''; var status = l.status || 'Pending';
        var date = (l.timestamp || '').substring(0, 10);

        if (st && st !== '-') r.states[st] = (r.states[st]||0)+1;
        if (ind && ind !== '-') r.industries[ind] = (r.industries[ind]||0)+1;
        r.devices[dev] = (r.devices[dev]||0)+1;
        if (src) r.sources[src] = (r.sources[src]||0)+1;
        if (qty) { var k = qty + ' reviews'; r.packages[k] = (r.packages[k]||0)+1; }
        if (plat) r.platforms[plat] = (r.platforms[plat]||0)+1;
        if (ctry && ctry !== '-') r.countries[ctry] = (r.countries[ctry]||0)+1;
        r.statuses[status] = (r.statuses[status]||0)+1;
        if (date && date.length === 10) r.byDate[date] = (r.byDate[date]||0)+1;
    });
    // Sort all
    ['states','industries','devices','sources','packages','platforms','countries','statuses'].forEach(function(k) {
        r[k] = sortObj(r[k]);
    });
    return r;
}

function sortObj(obj) {
    return Object.fromEntries(Object.entries(obj).sort(function(a,b) { return b[1]-a[1]; }));
}

function topKey(obj) { var k = Object.keys(obj); return k.length ? k[0] : '-'; }
function topVal(obj) { var v = Object.values(obj); return v.length ? v[0] : 0; }

// ===== RENDER =====
function applyFilters() {
    var logs = getFilteredData();
    var data = aggregate(logs);
    var total = logs.length;
    var mobilePct = total > 0 ? Math.round(((data.devices['Mobile']||0)/total)*100) : 0;

    // Stats
    document.getElementById('statsGrid').innerHTML = statCard('orange', total, 'Total Customers', '') +
        statCard('blue', topKey(data.devices), 'Top Device', topVal(data.devices)+' ('+mobilePct+'% mobile)') +
        statCard('green', topKey(data.states), 'Top State', topVal(data.states)+' customers') +
        statCard('purple', topKey(data.industries), 'Top Industry', topVal(data.industries)+' customers') +
        statCard('teal', topKey(data.packages), 'Top Package', topVal(data.packages)+' orders') +
        statCard('pink', topKey(data.sources), 'Top Source', topVal(data.sources)+' customers');

    // Sections
    renderDonutSection('stateSection', '📍', 'icon-blue', 'By State / Region', 'stateDonut', data.states, total, null);
    renderDonutSection('industrySection', '🏢', 'icon-purple', 'By Industry', 'industryDonut', data.industries, total, ['#AF52DE','#DA7FFF','#BF5AF2','#7A5AF8','#5E5CE6','#B4A0E5','#C7B8EA','#E0D6F5']);
    renderDonutSection('deviceSection', '📱', 'icon-green', 'By Device', 'deviceDonut', data.devices, total, ['#34C759','#007AFF','#FF9500']);
    renderDonutSection('sourceSection', '🔗', 'icon-orange', 'By Source', 'sourceDonut', data.sources, total, null);
    renderDonutSection('platformSection', '⭐', 'icon-teal', 'By Platform', 'platformDonut', data.platforms, total, ['#007AFF','#34C759','#FF2D55','#FF6B35','#5AC8FA']);
    renderDonutSection('countrySection', '🌍', 'icon-pink', 'By Country', 'countryDonut', data.countries, total, ['#FF2D55','#FF6B35','#FFD60A','#34C759','#007AFF','#AF52DE','#5AC8FA','#FF9500']);
    renderStatusSection(data.statuses, total);
    renderPackageSection(data.packages);
    renderTrendChart(data.byDate);
    renderMoMChart(logs);

    document.getElementById('footerInfo').textContent = total + ' submissions shown \u00b7 ' + new Date().toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric',hour:'2-digit',minute:'2-digit'});
}

function statCard(color, value, label, sub) {
    var fontSize = (typeof value === 'number' || String(value).length < 6) ? '30px' : (String(value).length < 12 ? '20px' : '16px');
    return '<div class="stat-card '+color+' animate-in"><div class="stat-value" style="font-size:'+fontSize+'">'+esc(String(value))+'</div><div class="stat-label">'+label+'</div>'+(sub?'<div class="stat-sub">'+sub+'</div>':'')+'</div>';
}

// ===== DONUT SECTION =====
function renderDonutSection(containerId, emoji, iconClass, title, canvasId, dataObj, total, colors) {
    var el = document.getElementById(containerId);
    var entries = Object.entries(dataObj).slice(0, 15);

    if (entries.length === 0) {
        el.innerHTML = '<div class="section-title"><div class="icon '+iconClass+'">'+emoji+'</div> '+title+'</div><div class="empty-state"><div class="icon">📭</div><p>No data for this filter</p></div>';
        return;
    }

    var tableRows = entries.map(function(e, i) {
        var pct = total > 0 ? ((e[1]/total)*100).toFixed(1) : 0;
        var rankCls = i < 3 ? 'rank-'+(i+1) : 'rank-other';
        return '<tr><td><span class="rank-badge '+rankCls+'">'+(i+1)+'</span>'+esc(e[0])+'</td><td><span class="pct-bar" style="width:'+Math.min(pct*2,50)+'px"></span>'+e[1]+'</td><td>'+pct+'%</td></tr>';
    }).join('');

    el.innerHTML = '<div class="section-title"><div class="icon '+iconClass+'">'+emoji+'</div> '+title+'</div>' +
        '<div class="chart-row"><div class="chart-canvas-wrap"><canvas id="'+canvasId+'"></canvas></div>' +
        '<div class="chart-table-wrap"><table class="data-table"><thead><tr><th>Name</th><th>Count</th><th>%</th></tr></thead><tbody>'+tableRows+'</tbody></table></div></div>';

    makeDonut(canvasId, dataObj, colors);
}

// ===== STATUS SECTION =====
function renderStatusSection(statuses, total) {
    var el = document.getElementById('statusSection');
    var entries = Object.entries(statuses);
    if (entries.length === 0) {
        el.innerHTML = '<div class="section-title"><div class="icon icon-yellow">🏷</div> By Status</div><div class="empty-state"><div class="icon">📭</div><p>No data</p></div>';
        return;
    }

    var statusColors = { 'Pending': '#FFD60A', 'Processing': '#007AFF', 'Completed': '#34C759', 'Cancelled': '#FF3B30', 'On Hold': '#86868B' };
    var rows = entries.map(function(e) {
        var pct = total > 0 ? ((e[1]/total)*100).toFixed(1) : 0;
        var cls = 'status-' + e[0].toLowerCase().replace(/\s/g, '');
        return '<tr><td><span class="status-badge '+cls+'">'+esc(e[0])+'</span></td><td>'+e[1]+'</td><td>'+pct+'%</td></tr>';
    }).join('');

    el.innerHTML = '<div class="section-title"><div class="icon icon-yellow">🏷</div> By Status</div>' +
        '<div class="chart-row"><div class="chart-canvas-wrap"><canvas id="statusDonut"></canvas></div>' +
        '<div class="chart-table-wrap"><table class="data-table"><thead><tr><th>Status</th><th>Count</th><th>%</th></tr></thead><tbody>'+rows+'</tbody></table></div></div>';

    var labels = Object.keys(statuses).slice(0,8);
    var values = labels.map(function(k) { return statuses[k]; });
    var cols = labels.map(function(k) { return statusColors[k] || '#86868B'; });
    makeDonut('statusDonut', statuses, cols);
}

// ===== PACKAGE SECTION =====
function renderPackageSection(packages) {
    var el = document.getElementById('packageSection');
    var entries = Object.entries(packages).slice(0, 10);
    if (entries.length === 0) {
        el.innerHTML = '<div class="section-title"><div class="icon icon-teal">📦</div> By Package</div><div class="empty-state"><div class="icon">📭</div><p>No data</p></div>';
        return;
    }
    var maxVal = Math.max.apply(null, entries.map(function(e){return e[1];}));
    var colors = ['fill-orange','fill-blue','fill-purple','fill-green','fill-teal','fill-pink','fill-yellow'];
    var bars = entries.map(function(e, i) {
        var pct = Math.round((e[1]/maxVal)*100);
        return '<li class="hbar-item"><span class="hbar-label">'+esc(e[0])+'</span><div class="hbar-track"><div class="hbar-fill '+colors[i%colors.length]+'" style="width:'+pct+'%">'+e[1]+'</div></div><span class="hbar-count">'+e[1]+'</span></li>';
    }).join('');

    el.innerHTML = '<div class="section-title"><div class="icon icon-teal">📦</div> By Package</div><ul class="hbar-list">'+bars+'</ul>';
}

// ===== TREND CHART =====
function renderTrendChart(byDate) {
    if (chartInstances.trend) chartInstances.trend.destroy();

    var dates = Object.keys(byDate).sort();
    var values = dates.map(function(d) { return byDate[d]; });

    if (dates.length === 0) {
        document.getElementById('trendChart').style.display = 'none';
        return;
    }
    document.getElementById('trendChart').style.display = '';

    chartInstances.trend = new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Submissions',
                data: values,
                borderColor: '#FF6B35',
                backgroundColor: 'rgba(255,107,53,0.08)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: dates.length > 30 ? 0 : 4,
                pointBackgroundColor: '#FF6B35',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', cornerRadius: 10, padding: 12, titleFont: { size: 13 }, bodyFont: { size: 12 } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, maxTicksLimit: 12 } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 } } }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });
}

// ===== DONUT HELPER =====
var appleColors = ['#FF6B35','#007AFF','#AF52DE','#34C759','#5AC8FA','#FF2D55','#FFD60A','#FF9500','#64D2FF','#BF5AF2','#30D158','#AC8E68','#FF6482','#8E8E93','#48484A'];

function makeDonut(id, dataObj, colors) {
    if (chartInstances[id]) chartInstances[id].destroy();
    var canvas = document.getElementById(id);
    if (!canvas) return;

    var labels = Object.keys(dataObj).slice(0, 8);
    var values = labels.map(function(k) { return dataObj[k]; });
    var total = values.reduce(function(a,b){return a+b;}, 0);

    chartInstances[id] = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{ data: values, backgroundColor: colors || appleColors, borderWidth: 2, borderColor: '#fff', borderRadius: 4, spacing: 2 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)', cornerRadius: 10, padding: 12,
                    callbacks: { label: function(ctx) { var p = total>0?((ctx.raw/total)*100).toFixed(1):0; return ctx.label+': '+ctx.raw+' ('+p+'%)'; } }
                }
            },
            animation: { animateRotate: true, duration: 800, easing: 'easeOutQuart' }
        },
        plugins: [{
            id: 'center',
            beforeDraw: function(chart) {
                var w = chart.width, h = chart.height, ctx = chart.ctx;
                ctx.save();
                ctx.font = '700 20px -apple-system, sans-serif'; ctx.fillStyle = '#1D1D1F';
                ctx.textBaseline = 'middle'; ctx.textAlign = 'center';
                ctx.fillText(total, w/2, h/2 - 7);
                ctx.font = '500 10px -apple-system, sans-serif'; ctx.fillStyle = '#86868B';
                ctx.fillText('Total', w/2, h/2 + 10);
                ctx.restore();
            }
        }]
    });
}

function esc(s) { if (!s) return '-'; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

// ===== INIT =====
applyFilters();
</script>
</body>
</html>
