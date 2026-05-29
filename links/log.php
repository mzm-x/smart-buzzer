<?php
/**
 * Smart Buzzer - Links Analytics Dashboard
 * Tracks visitors and link clicks from /links/ (bio IG & Facebook)
 *
 * Features:
 * - Traffic source detection (IG Bio, FB Bio, Ads, Direct)
 * - Ads vs Organic split
 * - Link performance ranking with CTR
 * - Daily views & clicks trend chart
 * - Ads detail table (UTM breakdown)
 * - Device & Country breakdown
 * - Recent clicks live log
 * - Date filtering
 * - CSV export
 * - Password protected
 */

session_start();

$PASSWORD = 'smartbuzzer2025';

// Auth
if (isset($_POST['logout'])) {
    unset($_SESSION['links_authenticated']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
if (isset($_POST['password'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['links_authenticated'] = true;
    } else {
        $loginError = 'Invalid password';
    }
}
$isAuthenticated = isset($_SESSION['links_authenticated']) && $_SESSION['links_authenticated'] === true;

// AJAX handler
if (isset($_GET['action']) && $_GET['action'] === 'fetch_data') {
    header('Content-Type: application/json');
    if (!$isAuthenticated) {
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : null;
    $dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : null;

    $pvFile = __DIR__ . '/page_views.log';
    $lcFile = __DIR__ . '/link_clicks.log';

    // Parse page_views.log
    $views = [];
    $uniqueIPs = [];
    $dailyViews = [];
    $sourceBreakdown = ['instagram' => 0, 'facebook' => 0, 'ads' => 0, 'direct' => 0];
    $deviceBreakdown = [];
    $countryBreakdown = [];

    if (file_exists($pvFile)) {
        $lines = file($pvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $p = explode("\t", $line);
            if (count($p) < 10) continue;

            $date = substr($p[0], 0, 10);
            if ($dateFrom && $date < $dateFrom) continue;
            if ($dateTo && $date > $dateTo) continue;

            $views[] = $p;
            $uniqueIPs[$p[7]] = true;

            // Daily views
            if (!isset($dailyViews[$date])) $dailyViews[$date] = 0;
            $dailyViews[$date]++;

            // Source detection
            $referrer = strtolower($p[1]);
            $utmSource = strtolower($p[2]);
            $utmMedium = strtolower($p[3]);

            if ($utmSource !== 'direct' && $utmSource !== '-' && $utmMedium !== 'none' && $utmMedium !== '-') {
                $sourceBreakdown['ads']++;
            } elseif (strpos($referrer, 'instagram') !== false || strpos($referrer, 'ref=ig') !== false) {
                $sourceBreakdown['instagram']++;
            } elseif (strpos($referrer, 'facebook') !== false || strpos($referrer, 'fb.') !== false || strpos($referrer, 'ref=fb') !== false) {
                $sourceBreakdown['facebook']++;
            } else {
                $sourceBreakdown['direct']++;
            }

            // Device
            $dev = $p[6];
            if (!isset($deviceBreakdown[$dev])) $deviceBreakdown[$dev] = 0;
            $deviceBreakdown[$dev]++;

            // Country
            $ctry = $p[8];
            if ($ctry !== '-') {
                if (!isset($countryBreakdown[$ctry])) $countryBreakdown[$ctry] = 0;
                $countryBreakdown[$ctry]++;
            }
        }
    }

    // Parse link_clicks.log
    $clicks = [];
    $dailyClicks = [];
    $linkPerformance = [];
    $adsDetail = [];
    $recentClicks = [];

    if (file_exists($lcFile)) {
        $lines = file($lcFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $p = explode("\t", $line);
            if (count($p) < 12) continue;

            $date = substr($p[0], 0, 10);
            if ($dateFrom && $date < $dateFrom) continue;
            if ($dateTo && $date > $dateTo) continue;

            $clicks[] = $p;

            // Daily clicks
            if (!isset($dailyClicks[$date])) $dailyClicks[$date] = 0;
            $dailyClicks[$date]++;

            // Link performance
            $linkName = $p[1];
            $linkUrl = $p[2];
            if (!isset($linkPerformance[$linkName])) {
                $linkPerformance[$linkName] = ['clicks' => 0, 'url' => $linkUrl];
            }
            $linkPerformance[$linkName]['clicks']++;

            // Ads detail (only UTM traffic)
            $utmSource = $p[4];
            $utmMedium = $p[5];
            $utmCampaign = $p[6];
            $utmContent = $p[7];

            if ($utmSource !== 'direct' && $utmSource !== '-' && $utmMedium !== 'none' && $utmMedium !== '-') {
                $adKey = $utmCampaign . '|' . $utmSource . '|' . $utmMedium;
                if (!isset($adsDetail[$adKey])) {
                    $adsDetail[$adKey] = [
                        'campaign' => $utmCampaign,
                        'source' => $utmSource,
                        'medium' => $utmMedium,
                        'clicks' => 0,
                        'links' => []
                    ];
                }
                $adsDetail[$adKey]['clicks']++;
                if (!in_array($linkName, $adsDetail[$adKey]['links'])) {
                    $adsDetail[$adKey]['links'][] = $linkName;
                }
            }

            // Recent clicks (last 50)
            $referrer = strtolower($p[3]);
            $utmSrc = strtolower($p[4]);
            $utmMed = strtolower($p[5]);

            if ($utmSrc !== 'direct' && $utmSrc !== '-' && $utmMed !== 'none' && $utmMed !== '-') {
                $src = 'Ads (UTM)';
            } elseif (strpos($referrer, 'instagram') !== false || strpos($referrer, 'ref=ig') !== false) {
                $src = 'IG Bio';
            } elseif (strpos($referrer, 'facebook') !== false || strpos($referrer, 'fb.') !== false || strpos($referrer, 'ref=fb') !== false) {
                $src = 'FB Bio';
            } else {
                $src = 'Direct';
            }

            $recentClicks[] = [
                'timestamp' => $p[0],
                'link_name' => $p[1],
                'source' => $src,
                'country' => $p[10],
                'device' => $p[8]
            ];
        }
    }

    // Sort
    ksort($dailyViews);
    ksort($dailyClicks);
    uasort($linkPerformance, function($a, $b) { return $b['clicks'] - $a['clicks']; });
    uasort($adsDetail, function($a, $b) { return $b['clicks'] - $a['clicks']; });
    arsort($deviceBreakdown);
    arsort($countryBreakdown);

    // Recent clicks - newest first, limit 50
    $recentClicks = array_reverse($recentClicks);
    $recentClicks = array_slice($recentClicks, 0, 50);

    $totalViews = count($views);
    $totalClicks = count($clicks);
    $uniqueVisitors = count($uniqueIPs);
    $ctr = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 1) : 0;

    $organic = $sourceBreakdown['instagram'] + $sourceBreakdown['facebook'] + $sourceBreakdown['direct'];
    $ads = $sourceBreakdown['ads'];

    echo json_encode([
        'total_views' => $totalViews,
        'total_clicks' => $totalClicks,
        'unique_visitors' => $uniqueVisitors,
        'ctr' => $ctr,
        'source_breakdown' => $sourceBreakdown,
        'organic' => $organic,
        'ads' => $ads,
        'daily_views' => $dailyViews,
        'daily_clicks' => $dailyClicks,
        'link_performance' => $linkPerformance,
        'ads_detail' => array_values($adsDetail),
        'device_breakdown' => $deviceBreakdown,
        'country_breakdown' => array_slice($countryBreakdown, 0, 10, true),
        'recent_clicks' => $recentClicks
    ]);
    exit;
}

// CSV export
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    if (!$isAuthenticated) { http_response_code(403); exit; }

    $type = isset($_GET['type']) ? $_GET['type'] : 'clicks';
    $file = $type === 'views' ? __DIR__ . '/page_views.log' : __DIR__ . '/link_clicks.log';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="links_' . $type . '_' . date('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');

    if ($type === 'views') {
        fputcsv($out, ['Timestamp','Referrer','UTM_Source','UTM_Medium','UTM_Campaign','UTM_Content','Device','IP','Country','Session']);
    } else {
        fputcsv($out, ['Timestamp','Link_Name','Link_URL','Referrer','UTM_Source','UTM_Medium','UTM_Campaign','UTM_Content','Device','IP','Country','Session']);
    }

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            fputcsv($out, explode("\t", $line));
        }
    }
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Links Analytics - Smart Buzzer</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <style>
        :root {
            --blue: #0066FF;
            --blue-hover: #0052CC;
            --blue-light: #E6F2FF;
            --green: #00875A;
            --green-light: #E3FCEF;
            --red: #DE350B;
            --orange: #FF991F;
            --orange-light: #FFF4E6;
            --purple: #6554C0;
            --purple-light: #EAE6FF;
            --pink: #E91E63;
            --bg: #F7F8FA;
            --card: #FFFFFF;
            --border: #DFE1E6;
            --text: #172B4D;
            --text2: #5E6C84;
            --text3: #8993A4;
            --shadow: 0 1px 3px rgba(9,30,66,0.12);
            --shadow2: 0 4px 8px rgba(9,30,66,0.15);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background:var(--bg); color:var(--text); line-height:1.5; }

        .login-container { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .login-box { background:var(--card); padding:40px; border-radius:12px; box-shadow:var(--shadow2); width:100%; max-width:400px; text-align:center; }
        .login-box h1 { font-size:24px; margin-bottom:8px; }
        .login-box p { color:var(--text2); margin-bottom:24px; }
        .login-box input[type="password"] { width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-size:16px; margin-bottom:16px; }
        .login-box input[type="password"]:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px var(--blue-light); }
        .login-box button { width:100%; padding:12px; background:var(--blue); color:#fff; border:none; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; }
        .login-box button:hover { background:var(--blue-hover); }
        .login-error { color:var(--red); margin-bottom:16px; padding:12px; background:#FFEBE6; border-radius:8px; }

        .dashboard { max-width:1400px; margin:0 auto; padding:24px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
        .header h1 { font-size:24px; display:flex; align-items:center; gap:12px; }
        .header-actions { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }

        .btn { padding:10px 20px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:8px; }
        .btn-primary { background:var(--blue); color:#fff; }
        .btn-primary:hover { background:var(--blue-hover); }
        .btn-secondary { background:var(--card); color:var(--text); border:1px solid var(--border); }
        .btn-secondary:hover { background:var(--bg); }
        .btn-sm { padding:6px 12px; font-size:12px; }

        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
        .stat-card { background:var(--card); border-radius:12px; padding:20px; box-shadow:var(--shadow); border-left:4px solid var(--blue); }
        .stat-card.green { border-left-color:var(--green); }
        .stat-card.orange { border-left-color:var(--orange); }
        .stat-card.purple { border-left-color:var(--purple); }
        .stat-card .value { font-size:32px; font-weight:700; }
        .stat-card .label { font-size:14px; color:var(--text2); margin-top:4px; }
        .stat-card .sub { font-size:12px; color:var(--text3); margin-top:2px; }

        .card { background:var(--card); border-radius:12px; box-shadow:var(--shadow); overflow:hidden; margin-bottom:24px; }
        .card-header { padding:16px 20px; border-bottom:1px solid var(--border); font-weight:600; font-size:16px; display:flex; align-items:center; gap:8px; justify-content:space-between; }
        .card-body { padding:20px; }

        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:24px; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:24px; margin-bottom:24px; }
        @media(max-width:992px) { .grid-2,.grid-3 { grid-template-columns:1fr; } }

        /* Source bars */
        .source-row { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f0f0f0; }
        .source-row:last-child { border-bottom:none; }
        .source-label { width:130px; font-size:14px; font-weight:500; }
        .source-bar-wrap { flex:1; height:24px; background:#f0f0f0; border-radius:12px; overflow:hidden; }
        .source-bar { height:100%; border-radius:12px; transition:width .6s ease; }
        .source-bar.ig { background:linear-gradient(90deg,#833AB4,#E1306C,#F77737); }
        .source-bar.fb { background:#1877F2; }
        .source-bar.ads { background:var(--orange); }
        .source-bar.direct { background:var(--text3); }
        .source-count { width:80px; text-align:right; font-weight:600; font-size:14px; }
        .source-pct { width:50px; text-align:right; font-size:13px; color:var(--text2); }

        /* Ads vs Organic */
        .split-bar { height:40px; border-radius:12px; overflow:hidden; display:flex; margin:16px 0; }
        .split-organic { background:var(--green); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; font-size:13px; transition:width .6s; }
        .split-ads { background:var(--orange); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; font-size:13px; transition:width .6s; }
        .split-legend { display:flex; gap:24px; justify-content:center; margin-top:8px; }
        .split-legend span { font-size:14px; color:var(--text2); display:flex; align-items:center; gap:6px; }
        .split-legend .dot { width:12px; height:12px; border-radius:50%; }

        /* Link performance */
        .link-row { display:flex; align-items:center; gap:12px; padding:14px 0; border-bottom:1px solid #f0f0f0; }
        .link-row:last-child { border-bottom:none; }
        .link-rank { width:30px; font-weight:700; font-size:18px; color:var(--text3); }
        .link-rank.r1 { color:#FFD700; }
        .link-rank.r2 { color:#C0C0C0; }
        .link-rank.r3 { color:#CD7F32; }
        .link-info { flex:1; }
        .link-name { font-size:15px; font-weight:600; }
        .link-url { font-size:12px; color:var(--text3); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:300px; }
        .link-clicks-val { font-weight:700; font-size:18px; color:var(--blue); }
        .link-pct { font-size:13px; color:var(--text2); width:60px; text-align:right; }
        .link-ctr { font-size:13px; color:var(--green); width:60px; text-align:right; }
        .link-bar-wrap { width:100%; height:8px; background:#f0f0f0; border-radius:4px; margin-top:6px; }
        .link-bar { height:100%; background:var(--blue); border-radius:4px; transition:width .6s; }

        /* Chart */
        .chart-wrap { position:relative; height:260px; }
        .chart-empty { display:flex; align-items:center; justify-content:center; height:200px; color:var(--text3); flex-direction:column; gap:8px; }

        /* Table */
        .tbl { width:100%; border-collapse:collapse; }
        .tbl th,.tbl td { padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); font-size:14px; }
        .tbl th { font-size:12px; font-weight:600; color:var(--text2); text-transform:uppercase; background:var(--bg); }
        .tbl tr:hover td { background:var(--bg); }
        .tbl tr:last-child td { border-bottom:none; }

        /* Breakdown small */
        .breakdown-item { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
        .breakdown-item:last-child { border-bottom:none; }
        .breakdown-item .pct { color:var(--text2); }

        /* Date picker */
        .dp-wrapper { position:relative; }
        .dp-trigger { white-space:nowrap; font-size:13px!important; padding:8px 14px!important; }
        .dp-dropdown { display:none; position:absolute; top:calc(100%+8px); right:0; background:#fff; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,.18); z-index:500; min-width:480px; border:1px solid var(--border); }
        .dp-dropdown.open { display:block; }
        .dp-layout { display:flex; }
        .dp-presets { width:180px; border-right:1px solid var(--border); padding:8px 0; max-height:380px; overflow-y:auto; }
        .dp-preset { padding:10px 20px; font-size:14px; cursor:pointer; transition:all .15s; }
        .dp-preset:hover { background:var(--bg); }
        .dp-preset.active { background:var(--blue-light); color:var(--blue); font-weight:600; }
        .dp-custom { flex:1; padding:20px; display:flex; flex-direction:column; justify-content:space-between; }
        .dp-row { display:flex; gap:12px; margin-bottom:16px; }
        .dp-field { flex:1; }
        .dp-field label { display:block; font-size:12px; font-weight:600; color:var(--text2); margin-bottom:6px; text-transform:uppercase; }
        .dp-field input[type="date"] { width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:14px; }
        .dp-field input[type="date"]:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px var(--blue-light); }
        .dp-actions { display:flex; justify-content:flex-end; gap:8px; }
        @media(max-width:600px) { .dp-dropdown { min-width:300px; right:-60px; } .dp-layout { flex-direction:column; } .dp-presets { width:100%; border-right:none; border-bottom:1px solid var(--border); max-height:200px; } }

        .loading { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,.9); display:flex; align-items:center; justify-content:center; z-index:1000; }
        .loading.hidden { display:none; }
        .spinner { width:48px; height:48px; border:4px solid var(--border); border-top-color:var(--blue); border-radius:50%; animation:spin 1s linear infinite; }
        @keyframes spin { to { transform:rotate(360deg); } }

        .toast { position:fixed; bottom:24px; right:24px; padding:16px 24px; background:var(--text); color:#fff; border-radius:8px; box-shadow:var(--shadow2); z-index:1001; transform:translateY(100px); opacity:0; transition:all .3s; }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast.success { background:var(--green); }
        .toast.error { background:var(--red); }

        .badge-src { padding:3px 10px; border-radius:12px; font-size:12px; font-weight:600; }
        .badge-ig { background:#F3E5F5; color:#8E24AA; }
        .badge-fb { background:#E3F2FD; color:#1565C0; }
        .badge-ads { background:var(--orange-light); color:#E65100; }
        .badge-direct { background:#f0f0f0; color:var(--text2); }
    </style>
</head>
<body>

<?php if (!$isAuthenticated): ?>
<div class="login-container">
    <div class="login-box">
        <h1>Links Analytics</h1>
        <p>Smart Buzzer Bio Link Dashboard</p>
        <?php if (isset($loginError)): ?>
        <div class="login-error"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter password" autofocus>
            <button type="submit">Login</button>
        </form>
    </div>
</div>

<?php else: ?>
<div class="loading hidden" id="loader"><div class="spinner"></div></div>
<div class="toast" id="toast"></div>

<div class="dashboard">
    <div class="header">
        <h1>Links Analytics</h1>
        <div class="header-actions">
            <div class="dp-wrapper">
                <button class="btn btn-secondary dp-trigger" onclick="toggleDP()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="dpLabel">Last 30 Days</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="dp-dropdown" id="dpDropdown">
                    <div class="dp-layout">
                        <div class="dp-presets">
                            <div class="dp-preset" data-tf="today">Today</div>
                            <div class="dp-preset" data-tf="yesterday">Yesterday</div>
                            <div class="dp-preset" data-tf="7days">Last 7 days</div>
                            <div class="dp-preset" data-tf="14days">Last 14 days</div>
                            <div class="dp-preset active" data-tf="30days">Last 30 days</div>
                            <div class="dp-preset" data-tf="thismonth">This month</div>
                            <div class="dp-preset" data-tf="lastmonth">Last month</div>
                            <div class="dp-preset" data-tf="all">All Time</div>
                        </div>
                        <div class="dp-custom">
                            <div class="dp-row">
                                <div class="dp-field"><label>From</label><input type="date" id="dateFrom"></div>
                                <div class="dp-field"><label>To</label><input type="date" id="dateTo"></div>
                            </div>
                            <div class="dp-actions">
                                <button class="btn btn-secondary btn-sm" onclick="closeDP()">Cancel</button>
                                <button class="btn btn-primary btn-sm" onclick="applyDP()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="refresh()">Refresh</button>
            <div class="btn-group" style="display:flex;gap:4px;">
                <button class="btn btn-secondary btn-sm" onclick="location.href='?action=export_csv&type=clicks'">Export Clicks</button>
                <button class="btn btn-secondary btn-sm" onclick="location.href='?action=export_csv&type=views'">Export Views</button>
            </div>
            <form method="POST" style="display:inline;"><button type="submit" name="logout" value="1" class="btn btn-secondary">Logout</button></form>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card"><div class="value" id="sViews">0</div><div class="label">Total Views</div><div class="sub">All page views</div></div>
        <div class="stat-card green"><div class="value" id="sUnique">0</div><div class="label">Unique Visitors</div><div class="sub">By unique IP</div></div>
        <div class="stat-card orange"><div class="value" id="sClicks">0</div><div class="label">Total Clicks</div><div class="sub">All link clicks</div></div>
        <div class="stat-card purple"><div class="value" id="sCTR">0%</div><div class="label">CTR</div><div class="sub">Clicks / Views</div></div>
    </div>

    <!-- Traffic Source + Ads vs Organic -->
    <div class="grid-2">
        <div class="card">
            <div class="card-header">Traffic Source</div>
            <div class="card-body" id="sourcePanel">
                <div class="chart-empty">No data</div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Ads vs Organic</div>
            <div class="card-body" id="splitPanel">
                <div class="chart-empty">No data</div>
            </div>
        </div>
    </div>

    <!-- Link Performance -->
    <div class="card">
        <div class="card-header">Link Performance</div>
        <div class="card-body" id="linkPanel">
            <div class="chart-empty">No data</div>
        </div>
    </div>

    <!-- Daily Trend Chart -->
    <div class="card">
        <div class="card-header">Daily Views & Clicks</div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="trendChart"></canvas>
                <div class="chart-empty" id="trendEmpty" style="display:none;">No data</div>
            </div>
        </div>
    </div>

    <!-- Ads Detail + Device + Country -->
    <div class="grid-3">
        <div class="card">
            <div class="card-header">Ads Detail (UTM)</div>
            <div class="card-body" style="padding:0; overflow-x:auto;" id="adsPanel">
                <div class="chart-empty" style="padding:20px;">No ad traffic</div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Device</div>
            <div class="card-body" id="devicePanel">
                <div class="chart-empty">No data</div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Top Countries</div>
            <div class="card-body" id="countryPanel">
                <div class="chart-empty">No data</div>
            </div>
        </div>
    </div>

    <!-- Recent Clicks -->
    <div class="card">
        <div class="card-header">Recent Clicks</div>
        <div class="card-body" style="padding:0; overflow-x:auto;" id="recentPanel">
            <div class="chart-empty" style="padding:20px;">No clicks yet</div>
        </div>
    </div>
</div>

<script>
let data = null;
let dateFrom = null, dateTo = null;

// Date picker
function fmtD(d){return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');}
function toggleDP(){document.getElementById('dpDropdown').classList.toggle('open');}
function closeDP(){document.getElementById('dpDropdown').classList.remove('open');}
function applyDP(){
    dateFrom=document.getElementById('dateFrom').value||null;
    dateTo=document.getElementById('dateTo').value||null;
    if(dateFrom&&dateTo){document.getElementById('dpLabel').textContent=fmtDisplay(dateFrom)+' - '+fmtDisplay(dateTo);}
    document.querySelectorAll('.dp-preset').forEach(p=>p.classList.remove('active'));
    closeDP(); refresh();
}
function fmtDisplay(s){if(!s)return'';var d=new Date(s+'T00:00:00');var m=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];return m[d.getMonth()]+' '+d.getDate()+', '+d.getFullYear();}

function setTF(tf){
    var t=new Date(),ts=fmtD(t);
    switch(tf){
        case'today':dateFrom=ts;dateTo=ts;break;
        case'yesterday':var d=new Date(t);d.setDate(d.getDate()-1);dateFrom=fmtD(d);dateTo=dateFrom;break;
        case'7days':var d=new Date(t);d.setDate(d.getDate()-7);dateFrom=fmtD(d);dateTo=ts;break;
        case'14days':var d=new Date(t);d.setDate(d.getDate()-14);dateFrom=fmtD(d);dateTo=ts;break;
        case'30days':var d=new Date(t);d.setDate(d.getDate()-30);dateFrom=fmtD(d);dateTo=ts;break;
        case'thismonth':dateFrom=ts.substring(0,8)+'01';dateTo=ts;break;
        case'lastmonth':var d=new Date(t.getFullYear(),t.getMonth()-1,1);dateFrom=fmtD(d);var e=new Date(t.getFullYear(),t.getMonth(),0);dateTo=fmtD(e);break;
        case'all':default:dateFrom=null;dateTo=null;break;
    }
    document.getElementById('dateFrom').value=dateFrom||'';
    document.getElementById('dateTo').value=dateTo||'';
    refresh();
}

document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('.dp-preset').forEach(el=>{
        el.addEventListener('click',function(){
            document.querySelectorAll('.dp-preset').forEach(p=>p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('dpLabel').textContent=this.textContent;
            closeDP(); setTF(this.dataset.tf);
        });
    });
    document.addEventListener('click',function(e){
        if(!document.querySelector('.dp-wrapper').contains(e.target))closeDP();
    });
    setTF('30days');
    document.getElementById('dpLabel').textContent='Last 30 days';
});

function showToast(m,t){var el=document.getElementById('toast');el.textContent=m;el.className='toast show '+(t||'');setTimeout(()=>{el.className='toast';},3000);}
function setLoading(s){document.getElementById('loader').classList.toggle('hidden',!s);}

async function refresh(){
    setLoading(true);
    try{
        var fd=new FormData();
        if(dateFrom)fd.append('date_from',dateFrom);
        if(dateTo)fd.append('date_to',dateTo);
        var r=await fetch('?action=fetch_data',{method:'POST',body:fd});
        data=await r.json();
        if(data.error){showToast(data.error,'error');setLoading(false);return;}
        render();
        showToast('Data refreshed','success');
    }catch(e){showToast('Error: '+e.message,'error');}
    setLoading(false);
}

function esc(t){if(!t)return'';var d=document.createElement('div');d.textContent=t;return d.innerHTML;}

function render(){
    if(!data)return;

    // Stats
    document.getElementById('sViews').textContent=data.total_views.toLocaleString();
    document.getElementById('sUnique').textContent=data.unique_visitors.toLocaleString();
    document.getElementById('sClicks').textContent=data.total_clicks.toLocaleString();
    document.getElementById('sCTR').textContent=data.ctr+'%';

    // Source breakdown
    var sb=data.source_breakdown;
    var total=sb.instagram+sb.facebook+sb.ads+sb.direct;
    var sp=document.getElementById('sourcePanel');
    if(total===0){sp.innerHTML='<div class="chart-empty">No data</div>';}
    else{
        var rows=[
            {label:'Instagram Bio',cls:'ig',val:sb.instagram},
            {label:'Facebook Bio',cls:'fb',val:sb.facebook},
            {label:'Paid Ads (UTM)',cls:'ads',val:sb.ads},
            {label:'Direct / Other',cls:'direct',val:sb.direct}
        ];
        var h='';
        rows.forEach(r=>{
            var pct=total>0?((r.val/total)*100).toFixed(1):0;
            h+='<div class="source-row"><span class="source-label">'+r.label+'</span><div class="source-bar-wrap"><div class="source-bar '+r.cls+'" style="width:'+pct+'%"></div></div><span class="source-count">'+r.val+'</span><span class="source-pct">'+pct+'%</span></div>';
        });
        sp.innerHTML=h;
    }

    // Ads vs Organic split
    var spPanel=document.getElementById('splitPanel');
    var org=data.organic, ads=data.ads, splitTotal=org+ads;
    if(splitTotal===0){spPanel.innerHTML='<div class="chart-empty">No data</div>';}
    else{
        var orgPct=((org/splitTotal)*100).toFixed(1);
        var adsPct=((ads/splitTotal)*100).toFixed(1);
        spPanel.innerHTML='<div style="text-align:center;margin-bottom:12px;font-size:14px;color:var(--text2);">'+splitTotal+' total visitors</div>'
            +'<div class="split-bar"><div class="split-organic" style="width:'+orgPct+'%">'+orgPct+'%</div><div class="split-ads" style="width:'+adsPct+'%">'+adsPct+'%</div></div>'
            +'<div class="split-legend"><span><span class="dot" style="background:var(--green)"></span> Organic: '+org+'</span><span><span class="dot" style="background:var(--orange)"></span> Ads: '+ads+'</span></div>';
    }

    // Link performance
    var lp=data.link_performance;
    var lpPanel=document.getElementById('linkPanel');
    var lpKeys=Object.keys(lp);
    if(lpKeys.length===0){lpPanel.innerHTML='<div class="chart-empty">No data</div>';}
    else{
        var maxClicks=0;
        lpKeys.forEach(k=>{if(lp[k].clicks>maxClicks)maxClicks=lp[k].clicks;});
        var totalClicks=data.total_clicks;
        var totalViews=data.total_views;
        var h='';var rank=1;
        lpKeys.forEach(k=>{
            var c=lp[k];
            var pct=totalClicks>0?((c.clicks/totalClicks)*100).toFixed(1):0;
            var ctr=totalViews>0?((c.clicks/totalViews)*100).toFixed(1):0;
            var barW=maxClicks>0?((c.clicks/maxClicks)*100).toFixed(0):0;
            var rc=rank<=3?(' r'+rank):'';
            h+='<div class="link-row"><span class="link-rank'+rc+'">'+rank+'</span><div class="link-info"><div class="link-name">'+esc(k)+'</div><div class="link-url">'+esc(c.url)+'</div><div class="link-bar-wrap"><div class="link-bar" style="width:'+barW+'%"></div></div></div><span class="link-clicks-val">'+c.clicks+'</span><span class="link-pct">'+pct+'%</span><span class="link-ctr">CTR '+ctr+'%</span></div>';
            rank++;
        });
        lpPanel.innerHTML=h;
    }

    // Daily trend chart
    renderTrendChart();

    // Ads detail
    var adPanel=document.getElementById('adsPanel');
    if(data.ads_detail.length===0){adPanel.innerHTML='<div class="chart-empty" style="padding:20px;">No ad traffic</div>';}
    else{
        var h='<table class="tbl"><thead><tr><th>Campaign</th><th>Source</th><th>Medium</th><th style="text-align:right">Clicks</th><th>Links Clicked</th></tr></thead><tbody>';
        data.ads_detail.forEach(a=>{
            h+='<tr><td>'+esc(a.campaign)+'</td><td>'+esc(a.source)+'</td><td>'+esc(a.medium)+'</td><td style="text-align:right;font-weight:600">'+a.clicks+'</td><td>'+esc(a.links.join(', '))+'</td></tr>';
        });
        h+='</tbody></table>';
        adPanel.innerHTML=h;
    }

    // Device
    var devPanel=document.getElementById('devicePanel');
    var devKeys=Object.keys(data.device_breakdown);
    if(devKeys.length===0){devPanel.innerHTML='<div class="chart-empty">No data</div>';}
    else{
        var devTotal=0;devKeys.forEach(k=>{devTotal+=data.device_breakdown[k];});
        var h='';
        devKeys.forEach(k=>{
            var v=data.device_breakdown[k];
            var pct=devTotal>0?((v/devTotal)*100).toFixed(1):0;
            h+='<div class="breakdown-item"><span>'+esc(k)+'</span><span><strong>'+v+'</strong> <span class="pct">('+pct+'%)</span></span></div>';
        });
        devPanel.innerHTML=h;
    }

    // Country
    var ctryPanel=document.getElementById('countryPanel');
    var ctryKeys=Object.keys(data.country_breakdown);
    if(ctryKeys.length===0){ctryPanel.innerHTML='<div class="chart-empty">No data</div>';}
    else{
        var ctryTotal=0;ctryKeys.forEach(k=>{ctryTotal+=data.country_breakdown[k];});
        var h='';
        ctryKeys.forEach(k=>{
            var v=data.country_breakdown[k];
            var pct=ctryTotal>0?((v/ctryTotal)*100).toFixed(1):0;
            h+='<div class="breakdown-item"><span>'+esc(k)+'</span><span><strong>'+v+'</strong> <span class="pct">('+pct+'%)</span></span></div>';
        });
        ctryPanel.innerHTML=h;
    }

    // Recent clicks
    var rcPanel=document.getElementById('recentPanel');
    if(data.recent_clicks.length===0){rcPanel.innerHTML='<div class="chart-empty" style="padding:20px;">No clicks yet</div>';}
    else{
        var h='<table class="tbl"><thead><tr><th>Time</th><th>Link Clicked</th><th>Source</th><th>Country</th><th>Device</th></tr></thead><tbody>';
        data.recent_clicks.forEach(c=>{
            var badgeCls='badge-direct';
            if(c.source==='IG Bio')badgeCls='badge-ig';
            else if(c.source==='FB Bio')badgeCls='badge-fb';
            else if(c.source==='Ads (UTM)')badgeCls='badge-ads';
            h+='<tr><td style="white-space:nowrap;font-size:13px;">'+esc(c.timestamp.substring(0,16))+'</td><td style="font-weight:600;">'+esc(c.link_name)+'</td><td><span class="badge-src '+badgeCls+'">'+esc(c.source)+'</span></td><td>'+esc(c.country)+'</td><td>'+esc(c.device)+'</td></tr>';
        });
        h+='</tbody></table>';
        rcPanel.innerHTML=h;
    }
}

// Trend chart (canvas)
function renderTrendChart(){
    var canvas=document.getElementById('trendChart');
    var empty=document.getElementById('trendEmpty');
    var ctx=canvas.getContext('2d');

    var dv=data.daily_views||{};
    var dc=data.daily_clicks||{};

    // Merge dates
    var allDates=new Set();
    Object.keys(dv).forEach(d=>allDates.add(d));
    Object.keys(dc).forEach(d=>allDates.add(d));
    var dates=Array.from(allDates).sort();

    if(dates.length===0){canvas.style.display='none';empty.style.display='flex';return;}
    canvas.style.display='block';empty.style.display='none';

    var views=dates.map(d=>dv[d]||0);
    var clicks=dates.map(d=>dc[d]||0);

    var container=canvas.parentElement;
    var dpr=window.devicePixelRatio||1;
    canvas.width=container.offsetWidth*dpr;
    canvas.height=260*dpr;
    canvas.style.width=container.offsetWidth+'px';
    canvas.style.height='260px';
    ctx.scale(dpr,dpr);

    var W=container.offsetWidth, H=260;
    var pad={top:20,right:20,bottom:50,left:50};
    var cW=W-pad.left-pad.right, cH=H-pad.top-pad.bottom;

    ctx.clearRect(0,0,W,H);

    var maxV=Math.max(...views,...clicks,1);
    var yStep=Math.ceil(maxV/5);
    var yMax=yStep*5;

    // Grid
    ctx.strokeStyle='#E8E8E8';ctx.lineWidth=1;
    for(var i=0;i<=5;i++){var y=pad.top+(cH/5)*i;ctx.beginPath();ctx.moveTo(pad.left,y);ctx.lineTo(W-pad.right,y);ctx.stroke();}

    // Y labels
    ctx.fillStyle='#8993A4';ctx.font='11px -apple-system,sans-serif';ctx.textAlign='right';ctx.textBaseline='middle';
    for(var i=0;i<=5;i++){var v=yMax-(yStep*i);var y=pad.top+(cH/5)*i;ctx.fillText(v,pad.left-8,y);}

    // X labels
    var xStep=cW/(dates.length-1||1);
    var labelStep=Math.ceil(dates.length/12);
    ctx.fillStyle='#8993A4';ctx.font='10px -apple-system,sans-serif';ctx.textAlign='center';ctx.textBaseline='top';
    dates.forEach(function(d,i){
        if(i%labelStep===0||i===dates.length-1){
            var x=pad.left+i*xStep;
            ctx.save();ctx.translate(x,H-pad.bottom+8);ctx.rotate(-45*Math.PI/180);
            var dd=new Date(d);var ms=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            ctx.fillText(dd.getDate()+' '+ms[dd.getMonth()],0,0);
            ctx.restore();
        }
    });

    // Draw line helper
    function drawLine(vals,color,dashed){
        ctx.strokeStyle=color;ctx.lineWidth=2.5;ctx.lineJoin='round';ctx.lineCap='round';
        if(dashed)ctx.setLineDash([6,4]);else ctx.setLineDash([]);
        ctx.beginPath();
        vals.forEach(function(v,i){
            var x=pad.left+i*xStep;
            var y=pad.top+cH-(v/yMax)*cH;
            if(i===0)ctx.moveTo(x,y);else ctx.lineTo(x,y);
        });
        ctx.stroke();ctx.setLineDash([]);

        // Dots
        ctx.fillStyle=color;
        vals.forEach(function(v,i){
            var x=pad.left+i*xStep;
            var y=pad.top+cH-(v/yMax)*cH;
            ctx.beginPath();ctx.arc(x,y,3,0,Math.PI*2);ctx.fill();
        });
    }

    drawLine(views,'#0066FF',false);
    drawLine(clicks,'#FF991F',true);

    // Legend
    ctx.font='12px -apple-system,sans-serif';ctx.textAlign='left';ctx.textBaseline='middle';
    var lx=pad.left+10,ly=pad.top+10;
    ctx.fillStyle='#0066FF';ctx.fillRect(lx,ly-2,16,4);ctx.fillStyle='#172B4D';ctx.fillText('Views',lx+22,ly);
    ctx.fillStyle='#FF991F';ctx.setLineDash([4,3]);ctx.beginPath();ctx.moveTo(lx+80,ly);ctx.lineTo(lx+96,ly);ctx.strokeStyle='#FF991F';ctx.lineWidth=2;ctx.stroke();ctx.setLineDash([]);
    ctx.fillStyle='#172B4D';ctx.fillText('Clicks',lx+102,ly);
}

window.addEventListener('resize',function(){
    clearTimeout(window._rz);
    window._rz=setTimeout(function(){if(data)renderTrendChart();},250);
});
</script>
<?php endif; ?>
</body>
</html>
