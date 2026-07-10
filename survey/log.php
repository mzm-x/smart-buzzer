<?php
// survey/log.php v3.0 — Smart Buzzer Survey Admin Log
// v1.0: Initial JSON log with bar charts
// v2.0: Donut charts + ranked list tables (Chart.js)
// v3.0: Call Requests tab + status management
session_start();

$PASS      = 'smartbuzzer2025';
$DATA_FILE = __DIR__ . '/data/responses.json';

// ── AJAX: Delete entries ──────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete_entries' && !empty($_SESSION['sb_auth'])) {
    header('Content-Type: application/json');
    $body = json_decode(file_get_contents('php://input'), true);
    $ids  = array_map('intval', (array)($body['ids'] ?? []));
    if (!empty($ids) && file_exists($DATA_FILE)) {
        $fp = fopen($DATA_FILE, 'c+');
        flock($fp, LOCK_EX);
        $sz   = filesize($DATA_FILE);
        $data = [];
        if ($sz > 2) { rewind($fp); $data = json_decode(fread($fp, $sz), true) ?: []; }
        $before = count($data);
        $data   = array_values(array_filter($data, fn($r) => !in_array((int)$r['id'], $ids)));
        ftruncate($fp, 0); rewind($fp);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fflush($fp);
        flock($fp, LOCK_UN); fclose($fp);
        echo json_encode(['success' => true, 'deleted' => $before - count($data)]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ── AJAX: Update call status ──────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'update_call_status' && !empty($_SESSION['sb_auth'])) {
    header('Content-Type: application/json');
    $id        = (int)($_POST['id'] ?? 0);
    $newStatus = in_array($_POST['status'] ?? '', ['pending','called','skipped']) ? $_POST['status'] : null;
    if ($id > 0 && $newStatus && file_exists($DATA_FILE)) {
        $fp = fopen($DATA_FILE, 'c+');
        flock($fp, LOCK_EX);
        $sz = filesize($DATA_FILE);
        $data = [];
        if ($sz > 2) { rewind($fp); $data = json_decode(fread($fp, $sz), true) ?: []; }
        $updated = false;
        foreach ($data as &$r) {
            if ((int)$r['id'] === $id) { $r['call_status'] = $newStatus; $updated = true; break; }
        }
        unset($r);
        if ($updated) {
            ftruncate($fp, 0); rewind($fp);
            fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            fflush($fp);
        }
        flock($fp, LOCK_UN); fclose($fp);
        echo json_encode(['success' => $updated]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ── AUTH ──────────────────────────────────────────────────
if (isset($_GET['logout'])) { unset($_SESSION['sb_auth']); header('Location: log.php'); exit; }
if (!empty($_POST['pass'])) {
    if ($_POST['pass'] === $PASS) { $_SESSION['sb_auth'] = true; header('Location: log.php'); exit; }
}
if (empty($_SESSION['sb_auth'])) { ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#F5F5F5;display:flex;align-items:center;justify-content:center;min-height:100vh}
.box{background:white;padding:40px 32px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.1);width:100%;max-width:340px;text-align:center}
h2{font-size:20px;color:#1A1A1A;margin-bottom:6px}p{font-size:14px;color:#666;margin-bottom:24px}
input{width:100%;padding:12px 16px;border:1.5px solid #E0E0E0;border-radius:8px;font-size:15px;margin-bottom:12px;outline:none;font-family:inherit}
input:focus{border-color:#2E7D32}
button{width:100%;padding:13px;background:#2E7D32;color:white;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer}
button:hover{background:#1B5E20}
.err{color:#D32F2F;font-size:13px;margin-bottom:12px}
</style></head><body>
<div class="box">
    <h2>&#128274; Survey Log</h2>
    <p>Smart Buzzer Admin</p>
    <?php if (!empty($_POST)): ?><div class="err">Wrong password.</div><?php endif; ?>
    <form method="POST">
        <input type="password" name="pass" placeholder="Password" autofocus required>
        <button type="submit">Login</button>
    </form>
</div>
</body></html>
<?php exit; }

// ── LOAD DATA ─────────────────────────────────────────────
$responses = [];
if (file_exists($DATA_FILE)) {
    $raw = @file_get_contents($DATA_FILE);
    if ($raw) $responses = json_decode($raw, true) ?: [];
}
usort($responses, fn($a,$b) => strcmp($b['submitted_at'], $a['submitted_at']));

// ── CSV EXPORT ────────────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] === 'callrequests') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="survey-call-requests-'.date('Y-m-d').'.csv"');
    $timeLabels = ['morning'=>'Morning (9-12pm)','afternoon'=>'Afternoon (12-3pm)','late_pm'=>'Late PM (3-6pm)','flexible'=>'Flexible / Anytime'];
    $callReqs = array_values(array_filter($responses, fn($r) => !empty($r['call_requested'])));
    $fp = fopen('php://output', 'w');
    fputcsv($fp, ['ID','Submitted At','Name','Email','Phone','Best Time','Status']);
    foreach ($callReqs as $r) {
        $pt = $r['call_preferred_time'] ?? '';
        fputcsv($fp, [
            $r['id'], $r['submitted_at'],
            $r['name'] ?: ($r['answers']['name'] ?? ''),
            $r['email'] ?? '',
            $r['call_phone'] ?? '',
            $timeLabels[$pt] ?? $pt,
            $r['call_status'] ?? 'pending',
        ]);
    }
    fclose($fp);
    exit;
}
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="survey-responses-'.date('Y-m-d').'.csv"');
    $fp = fopen('php://output', 'w');
    fputcsv($fp, ['ID','Submitted At','Email','Name','Time (sec)','IP','Channels','FB/IG Detail','Monthly Spend','Goals','Lead Sources','Challenges','Expected Results']);
    foreach ($responses as $r) {
        $a = $r['answers'];
        fputcsv($fp, [
            $r['id'], $r['submitted_at'], $r['email']??'', $r['name']??'', $r['time_spent_seconds'], $r['ip_address'],
            implode(', ', $a['channels_used'] ?? []),
            implode(', ', $a['fb_ig_detail']  ?? []),
            json_encode($a['monthly_spend']   ?? []),
            implode(', ', $a['main_goals']    ?? []),
            json_encode($a['lead_sources']    ?? []),
            implode(', ', $a['challenges']    ?? []),
            implode(', ', $a['expected_results'] ?? []),
        ]);
    }
    fclose($fp);
    exit;
}

// ── LABELS ────────────────────────────────────────────────
$CHAN = ['google_reviews'=>'Google Reviews','google_seo'=>'Google SEO','google_ads'=>'Google Ads / SEM',
    'google_pmax'=>'Google PMax','google_maps_ads'=>'Google Maps Ads','fb_ig_ads'=>'FB / IG Ads',
    'fb_marketplace'=>'FB Marketplace','yelp'=>'Yelp','website'=>'Website','tiktok_youtube'=>'TikTok / YouTube','other'=>'Other'];
$GOAL = ['get_more_leads'=>'More leads','increase_revenue'=>'More revenue','build_trust'=>'Build trust',
    'beat_competitors'=>'Beat competitors','reduce_cpl'=>'Reduce CPL','brand_awareness'=>'Brand awareness'];
$CHAL = ['not_enough_reviews'=>'Low reviews','high_ad_cost'=>'High ad cost','low_maps_rank'=>'Low Maps rank',
    'dont_know'=>'Dont know what works','competitors'=>'Competitors outrank','too_many_channels'=>'Too many channels',
    'not_enough_leads'=>'Not enough leads','other'=>'Other'];
$LEAD = ['google_maps'=>'Google Maps','google_search'=>'Google Search','google_ads'=>'Google Ads',
    'fb_organic'=>'FB/IG Organic','fb_ads'=>'FB/IG Ads','yelp'=>'Yelp','website'=>'Website',
    'referrals'=>'Referrals','email'=>'Email','tiktok_youtube'=>'TikTok/YouTube','other'=>'Other'];
$ERES = ['more_calls'=>'More calls','higher_maps_rank'=>'Maps ranking','more_trust'=>'More trust',
    'beat_competitors'=>'Beat competitors','more_traffic'=>'More traffic','higher_conversion'=>'Higher conversion'];

function lbl($map, $k) { return $map[$k] ?? ucwords(str_replace('_',' ',$k)); }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function fmtT($s) { if (!$s) return '&mdash;'; $m=floor($s/60);$sec=$s%60; return $m.'m '.str_pad($sec,2,'0',STR_PAD_LEFT).'s'; }

// ── INSIGHTS ──────────────────────────────────────────────
$cnt_chan=[]; $cnt_goal=[]; $cnt_lead=[]; $cnt_chal=[]; $cnt_eres=[];
$total_time = 0; $time_count = 0;
foreach ($responses as $r) {
    $a = $r['answers'];
    foreach ((array)($a['channels_used']    ?? []) as $v)  $cnt_chan[$v] = ($cnt_chan[$v]??0)+1;
    foreach ((array)($a['main_goals']       ?? []) as $v)  $cnt_goal[$v] = ($cnt_goal[$v]??0)+1;
    foreach ((array)($a['challenges']       ?? []) as $v)  $cnt_chal[$v] = ($cnt_chal[$v]??0)+1;
    foreach ((array)($a['expected_results'] ?? []) as $v)  $cnt_eres[$v] = ($cnt_eres[$v]??0)+1;
    $ls = (array)($a['lead_sources'] ?? []);
    if (!empty($ls[1])) $cnt_lead[$ls[1]] = ($cnt_lead[$ls[1]]??0)+1;
    if ($r['time_spent_seconds'] > 0) { $total_time += $r['time_spent_seconds']; $time_count++; }
}
arsort($cnt_chan); arsort($cnt_goal); arsort($cnt_lead); arsort($cnt_chal); arsort($cnt_eres);
$avg_time = $time_count ? round($total_time/$time_count) : 0;

// ── CALL REQUESTS ─────────────────────────────────────────
$call_requests = array_values(array_filter($responses, fn($r) => !empty($r['call_requested'])));
$pending_calls  = count(array_filter($call_requests, fn($r) => ($r['call_status']??'') === 'pending'));
$TIME_LABELS = ['morning'=>'Morning (9–12pm)','afternoon'=>'Afternoon (12–3pm)','late_pm'=>'Late PM (3–6pm)','flexible'=>'Flexible / Anytime'];

// ── CHART CONFIGS ─────────────────────────────────────────
$COLORS = ['#F97316','#3B82F6','#8B5CF6','#22C55E','#F59E0B','#EF4444','#14B8A6','#EC4899'];
$charts = [
    ['id'=>'chart-chan', 'title'=>'Top Channels Used',   'data'=>$cnt_chan, 'map'=>$CHAN],
    ['id'=>'chart-goal', 'title'=>'Top Goals',           'data'=>$cnt_goal, 'map'=>$GOAL],
    ['id'=>'chart-lead', 'title'=>'Top #1 Lead Sources', 'data'=>$cnt_lead, 'map'=>$LEAD],
    ['id'=>'chart-chal', 'title'=>'Top Challenges',      'data'=>$cnt_chal, 'map'=>$CHAL],
    ['id'=>'chart-eres', 'title'=>'Expected Results',    'data'=>$cnt_eres, 'map'=>$ERES],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Survey Log &mdash; Smart Buzzer</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;font-size:14px;background:#F5F5F5;color:#1A1A1A}
.hdr{background:#2E7D32;color:white;padding:13px 24px;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
.hdr h1{font-size:17px;font-weight:600}
.hdr a{color:white;opacity:.8;font-size:13px;text-decoration:none}.hdr a:hover{opacity:1}
.wrap{max-width:1280px;margin:0 auto;padding:20px 16px}

/* Stat cards */
.stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px}
.stat{background:white;border-radius:10px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.stat-n{font-size:28px;font-weight:700;color:#2E7D32}
.stat-l{font-size:12px;color:#666;margin-top:3px}

/* Chart grid */
.chart-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:14px;margin-bottom:20px}
.chart-card{background:white;border-radius:12px;padding:18px 20px;box-shadow:0 1px 4px rgba(0,0,0,.09)}
.chart-card-head{font-size:11px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px}
.chart-card-body{display:flex;align-items:center;gap:20px}
.donut-wrap{position:relative;flex-shrink:0;width:120px;height:120px}
.donut-wrap canvas{width:120px!important;height:120px!important}
.donut-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;white-space:nowrap}
.donut-total{display:block;font-size:24px;font-weight:800;color:#1A1A1A;line-height:1.1}
.donut-lbl{display:block;font-size:10px;color:#AAA;margin-top:1px}
.rank-list{flex:1;min-width:0}
.rank-row{display:flex;align-items:center;gap:7px;margin-bottom:8px}
.rank-row:last-child{margin-bottom:0}
.rank-badge{flex-shrink:0;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:white;line-height:1}
.rank-name{flex:1;min-width:0;font-size:12px;color:#333;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.rank-bar-wrap{width:56px;height:5px;background:#F0F0F0;border-radius:3px;flex-shrink:0}
.rank-bar{height:100%;border-radius:3px}
.rank-pct{flex-shrink:0;text-align:right;font-size:11px;font-weight:600;color:#444;min-width:52px}
.rank-pct .pct{color:#BBB;font-weight:400}
.no-data{font-size:12px;color:#BBB;padding:16px 0;text-align:center}

/* Table */
.tbl-box{background:white;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden;margin-bottom:20px}
.tbl-head{padding:13px 16px;border-bottom:1px solid #F0F0F0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.tbl-head h3{font-size:14px;font-weight:600}
.btn-csv{padding:7px 16px;background:#E8F5E9;color:#2E7D32;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;border:none;cursor:pointer}
.btn-csv:hover{background:#C8E6C9}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:#FAFAFA}
th,td{padding:10px 14px;text-align:left;border-bottom:1px solid #F0F0F0;vertical-align:top}
th{font-weight:600;color:#666;font-size:11px;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap}
tr:last-child>td{border-bottom:none}
tr:hover>td{background:#FAFAFA}
.expand{color:#2E7D32;cursor:pointer;font-size:12px;white-space:nowrap;user-select:none}
.detail-row{display:none;background:#FAFAFA}
.detail-row.open{display:table-row}
.detail-row td{padding:12px 14px 16px}
.dgrid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px}
.dblock{background:white;border-radius:8px;padding:12px 14px;border:1px solid #E0E0E0}
.dblock h4{font-size:11px;font-weight:600;color:#999;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px}
.tag{display:inline-block;padding:3px 8px;background:#E8F5E9;color:#2E7D32;border-radius:4px;font-size:12px;margin:2px 2px 2px 0}
.tag.sp{background:#F3E5F5;color:#6A1B9A}
.empty{text-align:center;padding:48px;color:#999;font-size:15px}
@media(max-width:600px){th:nth-child(4),td:nth-child(4){display:none}.chart-grid{grid-template-columns:1fr}}

/* Tabs */
.tabs{display:flex;gap:4px;padding:16px 0 0;border-bottom:2px solid #E0E0E0;margin-bottom:20px}
.tab-btn{padding:10px 20px;border:none;background:none;font-size:14px;font-weight:600;color:#999;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s;display:flex;align-items:center;gap:8px;font-family:inherit}
.tab-btn.active{color:#2E7D32;border-bottom-color:#2E7D32}
.tab-btn:hover:not(.active){color:#555}
.tbadge{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border-radius:10px;font-size:11px;font-weight:700;background:#E0E0E0;color:#666}
.tab-btn.active .tbadge{background:#E8F5E9;color:#2E7D32}
.tbadge-orange{background:#FFF3CD!important;color:#856404!important}

/* Call status badges + action buttons */
.sbadge{display:inline-block;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap}
.sbadge-pending{background:#FFF3CD;color:#856404}
.sbadge-called{background:#D1E7DD;color:#155724}
.sbadge-skipped{background:#F8F9FA;color:#6C757D}
.act-btn{padding:5px 11px;border-radius:5px;font-size:12px;font-weight:600;border:none;cursor:pointer;margin-right:4px;transition:background .15s;font-family:inherit}
.act-called{background:#D1E7DD;color:#155724}.act-called:hover{background:#A3CFBB}
.act-skip{background:#F8F9FA;color:#6C757D;border:1px solid #DEE2E6}.act-skip:hover{background:#E9ECEF}
.act-undo{background:#FFF3CD;color:#856404}.act-undo:hover{background:#FFE69C}
.call-empty{text-align:center;padding:48px;color:#999;font-size:15px}

/* Checkbox + delete */
input[type=checkbox]{width:15px;height:15px;cursor:pointer;accent-color:#2E7D32;flex-shrink:0}
.btn-del-sel{padding:7px 14px;background:#FEECEC;color:#D32F2F;border-radius:6px;font-size:13px;font-weight:600;border:1px solid #FFCDD2;cursor:pointer;transition:all .15s;font-family:inherit}
.btn-del-sel:hover{background:#FFCDD2}
.th-chk{width:36px;padding-left:14px!important}
.td-chk{width:36px;padding-left:14px!important;vertical-align:middle}
</style>
</head>
<body>

<div class="hdr">
    <h1>&#128202; Survey Log &mdash; Smart Buzzer</h1>
    <div style="display:flex;gap:16px;align-items:center">
        <span style="font-size:13px;opacity:.75"><?= date('d M Y') ?></span>
        <a href="?logout=1">Logout</a>
    </div>
</div>

<div class="wrap">

<!-- TABS -->
<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('responses',this)">
        Responses <span class="tbadge"><?= count($responses) ?></span>
    </button>
    <button class="tab-btn" onclick="switchTab('callrequests',this)">
        &#128222; Call Requests
        <?php if ($pending_calls > 0): ?>
            <span class="tbadge tbadge-orange"><?= $pending_calls ?> pending</span>
        <?php elseif(count($call_requests) > 0): ?>
            <span class="tbadge"><?= count($call_requests) ?></span>
        <?php endif; ?>
    </button>
</div>

<!-- TAB: RESPONSES -->
<div id="tab-responses">

<!-- STATS -->
<div class="stat-row">
    <div class="stat"><div class="stat-n"><?= count($responses) ?></div><div class="stat-l">Total Responses</div></div>
    <div class="stat"><div class="stat-n"><?= fmtT($avg_time) ?></div><div class="stat-l">Avg Completion Time</div></div>
    <div class="stat"><div class="stat-n"><?= count($responses) * 10 ?></div><div class="stat-l">Bonus Reviews Granted</div></div>
    <div class="stat"><div class="stat-n"><?= $cnt_chan ? e(lbl($CHAN, array_key_first($cnt_chan))) : '&mdash;' ?></div><div class="stat-l">Top Channel</div></div>
</div>

<!-- CHART GRID -->
<div class="chart-grid">
<?php foreach ($charts as $ch):
    $top   = array_slice($ch['data'], 0, 6, true);
    $grand = array_sum($ch['data']);
?>
<div class="chart-card">
    <div class="chart-card-head"><?= e($ch['title']) ?></div>
    <div class="chart-card-body">
        <div class="donut-wrap">
            <canvas id="<?= $ch['id'] ?>"></canvas>
            <div class="donut-center">
                <span class="donut-total"><?= $grand ?: '0' ?></span>
                <span class="donut-lbl">total</span>
            </div>
        </div>
        <div class="rank-list">
            <?php if (!$top): ?>
                <p class="no-data">No data yet</p>
            <?php else: $i=0; foreach ($top as $k=>$n):
                $col = $COLORS[$i % count($COLORS)];
                $pct = $grand > 0 ? round($n / $grand * 100) : 0;
            ?>
            <div class="rank-row">
                <span class="rank-badge" style="background:<?= $col ?>"><?= $i+1 ?></span>
                <span class="rank-name" title="<?= e(lbl($ch['map'], $k)) ?>"><?= e(lbl($ch['map'], $k)) ?></span>
                <div class="rank-bar-wrap"><div class="rank-bar" style="width:<?= $pct ?>%;background:<?= $col ?>"></div></div>
                <span class="rank-pct"><?= $n ?> <span class="pct">&middot; <?= $pct ?>%</span></span>
            </div>
            <?php $i++; endforeach; endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- TABLE -->
<div class="tbl-box" id="responses-tbl-box">
    <div class="tbl-head">
        <h3 id="resp-count-lbl">All Responses (<?= count($responses) ?>)</h3>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <button id="btn-delete-sel" class="btn-del-sel" onclick="deleteSelected()" style="display:none">&#128465; Delete (<span id="del-count">0</span>)</button>
            <?php if($responses): ?>
            <a href="?export=csv" class="btn-csv">&#8595; Export CSV</a>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!$responses): ?>
        <div class="empty">No responses yet.<br><small>Share <code>survey/index.php</code> with your clients.</small></div>
    <?php else: ?>
    <table>
        <thead><tr>
            <th class="th-chk"><input type="checkbox" id="chk-all" onclick="toggleAll(this)" title="Select all"></th>
            <th>#</th><th>Date &amp; Time</th><th>Email / Name</th><th>Channels</th><th>Time</th><th>Top Goal</th><th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($responses as $r):
            $a   = $r['answers'];
            $rid = 'r'.$r['id'];
            $chans   = (array)($a['channels_used']    ?? []);
            $goals   = (array)($a['main_goals']       ?? []);
            $chals   = (array)($a['challenges']        ?? []);
            $results = (array)($a['expected_results'] ?? []);
            $spend   = (array)($a['monthly_spend']    ?? []);
            $leads   = (array)($a['lead_sources']     ?? []);
            $fbig    = (array)($a['fb_ig_detail']     ?? []);
        ?>
        <tr id="row-<?= $r['id'] ?>">
            <td class="td-chk"><input type="checkbox" class="row-chk" value="<?= $r['id'] ?>" onchange="updateDeleteBtn()"></td>
            <td style="color:#999;font-size:12px"><?= $r['id'] ?></td>
            <td style="white-space:nowrap"><?= e(date('d M Y', strtotime($r['submitted_at']))) ?><br><span style="color:#999;font-size:12px"><?= e(date('H:i', strtotime($r['submitted_at']))) ?></span></td>
            <td>
                <div style="font-weight:500;font-size:13px"><?= e($r['email'] ?? $a['email'] ?? '&mdash;') ?></div>
                <?php $nm=$r['name']??$a['name']??''; if($nm): ?><div style="color:#999;font-size:12px"><?= e($nm) ?></div><?php endif; ?>
            </td>
            <td><?= count($chans) ?> channels</td>
            <td style="white-space:nowrap"><?= fmtT($r['time_spent_seconds']) ?></td>
            <td style="font-size:13px"><?= $goals ? e(lbl($GOAL,$goals[0])) : '&mdash;' ?></td>
            <td><span class="expand" onclick="exp('<?= $rid ?>')">Details &#9662;</span></td>
        </tr>
        <tr id="<?= $rid ?>" class="detail-row">
            <td colspan="8">
                <div class="dgrid">
                    <div class="dblock">
                        <h4>Channels Used</h4>
                        <?php foreach($chans as $v): ?><span class="tag"><?= e(lbl($CHAN,$v)) ?></span><?php endforeach; ?>
                        <?php if(!empty($a['channels_other'])): ?><span class="tag"><?= e($a['channels_other']) ?></span><?php endif; ?>
                    </div>
                    <div class="dblock">
                        <h4>Monthly Spend</h4>
                        <?php foreach($spend as $ch=>$amt): ?><span class="tag sp"><?= e($ch) ?>: <?= e($amt) ?></span><?php endforeach; ?>
                    </div>
                    <?php if($fbig): ?>
                    <div class="dblock">
                        <h4>FB / IG Activities</h4>
                        <?php foreach($fbig as $v): ?><span class="tag"><?= e($v) ?></span><?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="dblock">
                        <h4>Goals</h4>
                        <?php foreach($goals as $v): ?><span class="tag"><?= e(lbl($GOAL,$v)) ?></span><?php endforeach; ?>
                    </div>
                    <div class="dblock">
                        <h4>Top Lead Sources</h4>
                        <?php foreach([1,2,3] as $n): if(!empty($leads[$n])): ?>
                            <div style="font-size:13px;margin-bottom:4px"><strong>#<?= $n ?></strong> <?= e(lbl($LEAD,$leads[$n])) ?></div>
                        <?php endif; endforeach; ?>
                        <?php if(!array_filter($leads)): ?><span style="color:#999">—</span><?php endif; ?>
                    </div>
                    <div class="dblock">
                        <h4>Challenges</h4>
                        <?php foreach($chals as $v): ?><span class="tag"><?= e(lbl($CHAL,$v)) ?></span><?php endforeach; ?>
                        <?php if(!empty($a['challenges_other'])): ?><span class="tag"><?= e($a['challenges_other']) ?></span><?php endif; ?>
                    </div>
                    <div class="dblock">
                        <h4>Expected Results</h4>
                        <?php foreach($results as $v): ?><span class="tag"><?= e(lbl($ERES,$v)) ?></span><?php endforeach; ?>
                    </div>
                    <div class="dblock">
                        <h4>Meta</h4>
                        <div style="font-size:12px;color:#666;line-height:1.8">
                            <div>Time: <?= fmtT($r['time_spent_seconds']) ?></div>
                            <div>IP: <?= e($r['ip_address']??'—') ?></div>
                            <div>Submitted: <?= e($r['submitted_at']) ?></div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</div><!-- /tab-responses -->

<!-- TAB: CALL REQUESTS -->
<div id="tab-callrequests" style="display:none">
<div class="tbl-box">
    <div class="tbl-head">
        <h3>Call Requests (<?= count($call_requests) ?>)</h3>
        <div style="display:flex;align-items:center;gap:14px">
            <?php if ($pending_calls > 0): ?>
                <span style="font-size:13px;color:#856404;font-weight:600">&#9203; <?= $pending_calls ?> pending</span>
            <?php endif; ?>
            <?php if ($call_requests): ?>
                <a href="?export=callrequests" class="btn-csv">&#8595; Export CSV</a>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!$call_requests): ?>
        <div class="call-empty">No call requests yet.<br><small>Respondents who click "Yes, call me!" will appear here.</small></div>
    <?php else: ?>
    <table>
        <thead><tr>
            <th>#</th>
            <th>Submitted</th>
            <th>Name / Email</th>
            <th>Phone</th>
            <th>Best Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr></thead>
        <tbody>
        <?php foreach ($call_requests as $r):
            $status   = $r['call_status'] ?? 'pending';
            $dispTime = $TIME_LABELS[$r['call_preferred_time'] ?? ''] ?? ($r['call_preferred_time'] ?: '&mdash;');
            $nm = e($r['name'] ?: ($r['answers']['name'] ?? ''));
        ?>
        <tr id="cr-<?= $r['id'] ?>">
            <td style="color:#999;font-size:12px"><?= $r['id'] ?></td>
            <td style="white-space:nowrap">
                <?= e(date('d M Y', strtotime($r['submitted_at']))) ?>
                <br><span style="color:#999;font-size:12px"><?= e(date('H:i', strtotime($r['submitted_at']))) ?></span>
            </td>
            <td>
                <?php if($nm): ?><div style="font-weight:500;font-size:13px"><?= $nm ?></div><?php endif; ?>
                <div style="color:#999;font-size:12px"><?= e($r['email'] ?: '&mdash;') ?></div>
            </td>
            <td style="font-weight:500;font-size:13px"><?= e($r['call_phone'] ?: '&mdash;') ?></td>
            <td style="font-size:13px"><?= $dispTime ?></td>
            <td>
                <?php
                $badgeClass = ['pending'=>'sbadge-pending','called'=>'sbadge-called','skipped'=>'sbadge-skipped'][$status] ?? 'sbadge-pending';
                $badgeText  = ['pending'=>'&#9203; Pending','called'=>'&#10003; Called','skipped'=>'&#8594; Skipped'][$status] ?? 'Pending';
                ?>
                <span class="sbadge <?= $badgeClass ?>" id="sb-<?= $r['id'] ?>"><?= $badgeText ?></span>
            </td>
            <td id="act-<?= $r['id'] ?>">
                <?php if($status === 'pending'): ?>
                    <button class="act-btn act-called" onclick="updateStatus(<?= $r['id'] ?>,'called')">Mark Called</button>
                    <button class="act-btn act-skip" onclick="updateStatus(<?= $r['id'] ?>,'skipped')">Skip</button>
                <?php else: ?>
                    <button class="act-btn act-undo" onclick="updateStatus(<?= $r['id'] ?>,'pending')">Undo</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</div><!-- /tab-callrequests -->

</div><!-- /wrap -->

<script>
function exp(id) {
    const row = document.getElementById(id);
    if (!row) return;
    const isOpen = row.classList.contains('open');
    document.querySelectorAll('.detail-row.open').forEach(r => r.classList.remove('open'));
    document.querySelectorAll('.expand').forEach(el => { if(el.textContent.includes('\u25b4')) el.textContent='Details \u25be'; });
    if (!isOpen) {
        row.classList.add('open');
        const tog = row.previousElementSibling?.querySelector('.expand');
        if (tog) tog.textContent = 'Details \u25b4';
    }
}

// ── Checkbox + delete ────────────────────────────────────
function toggleAll(cb) {
    document.querySelectorAll('.row-chk').forEach(c => c.checked = cb.checked);
    updateDeleteBtn();
}

function updateDeleteBtn() {
    const checked = document.querySelectorAll('.row-chk:checked');
    const btn = document.getElementById('btn-delete-sel');
    const cnt = document.getElementById('del-count');
    if (btn) btn.style.display = checked.length > 0 ? '' : 'none';
    if (cnt) cnt.textContent = checked.length;
    const all = document.querySelectorAll('.row-chk');
    const chkAll = document.getElementById('chk-all');
    if (chkAll) {
        chkAll.indeterminate = checked.length > 0 && checked.length < all.length;
        chkAll.checked = all.length > 0 && checked.length === all.length;
    }
}

async function deleteSelected() {
    const checked = document.querySelectorAll('.row-chk:checked');
    if (!checked.length) return;
    const ids = Array.from(checked).map(c => parseInt(c.value));
    const noun = ids.length === 1 ? '1 response' : ids.length + ' responses';
    if (!confirm('Delete ' + noun + '? This cannot be undone.')) return;
    try {
        const res = await fetch('log.php?action=delete_entries', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ids})
        });
        const data = await res.json();
        if (!data.success) { alert('Delete failed'); return; }
        ids.forEach(id => {
            const dataRow   = document.getElementById('row-' + id);
            const detailRow = document.getElementById('r' + id);
            if (dataRow)   dataRow.remove();
            if (detailRow) detailRow.remove();
        });
        const remaining = document.querySelectorAll('.row-chk').length;
        const lbl = document.getElementById('resp-count-lbl');
        if (lbl) lbl.textContent = 'All Responses (' + remaining + ')';
        updateDeleteBtn();
    } catch(e) { alert('Network error'); }
}

// ── Tab switching ─────────────────────────────────────────
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-responses').style.display    = tab === 'responses'    ? '' : 'none';
    document.getElementById('tab-callrequests').style.display = tab === 'callrequests' ? '' : 'none';
}

// ── Call status update ────────────────────────────────────
async function updateStatus(id, status) {
    try {
        const res = await fetch('log.php?action=update_call_status', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&status=' + status
        });
        const data = await res.json();
        if (!data.success) { alert('Failed to update'); return; }
        const badge = document.getElementById('sb-' + id);
        const acts  = document.getElementById('act-' + id);
        const labels = {pending:'&#9203; Pending', called:'&#10003; Called', skipped:'&#8594; Skipped'};
        const classes = {pending:'sbadge-pending', called:'sbadge-called', skipped:'sbadge-skipped'};
        badge.innerHTML  = labels[status];
        badge.className  = 'sbadge ' + classes[status];
        if (status === 'pending') {
            acts.innerHTML = '<button class="act-btn act-called" onclick="updateStatus('+id+',\'called\')">Mark Called</button>'
                           + '<button class="act-btn act-skip" onclick="updateStatus('+id+',\'skipped\')">Skip</button>';
        } else {
            acts.innerHTML = '<button class="act-btn act-undo" onclick="updateStatus('+id+',\'pending\')">Undo</button>';
        }
    } catch(e) { alert('Network error'); }
}

// ── Donut charts ──────────────────────────────────────────
<?php foreach ($charts as $ch):
    $top   = array_slice($ch['data'], 0, 6, true);
    $grand = array_sum($ch['data']);
    $vals  = array_values($top);
    $others = $grand - array_sum($vals);
    $num   = count($vals);
    $clrs  = array_slice($COLORS, 0, $num);
    if ($others > 0 && $num < 8) { $vals[] = $others; $clrs[] = '#E5E7EB'; }
    if (!$vals) { $vals = [1]; $clrs = ['#E5E7EB']; }
?>
(function(){
    var ctx = document.getElementById('<?= $ch['id'] ?>');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: <?= json_encode($vals) ?>,
                backgroundColor: <?= json_encode($clrs) ?>,
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 5
            }]
        },
        options: {
            cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            events: [],
            animation: { duration: 700, easing: 'easeInOutQuart' }
        }
    });
})();
<?php endforeach; ?>
</script>
</body>
</html>
