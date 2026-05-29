<?php
/**
 * /social/log.php
 * 
 * Smart Buzzer Dashboard - Social Media Landing Page
 * Version: 1.0
 * 
 * Tabs: Customers, Analytics, Campaign Breakdown
 * Reads: customer_data.log (17 cols), page_analytics.log (13 cols)
 */

session_start();

// ===== PASSWORD PROTECTION =====
$PASSWORD = 'smartbuzzer2025';

if (isset($_POST['logout'])) {
    unset($_SESSION['sb_dashboard_auth']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['password'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['sb_dashboard_auth'] = true;
    }
}

if (!isset($_SESSION['sb_dashboard_auth']) || $_SESSION['sb_dashboard_auth'] !== true) {
    showLoginPage();
    exit;
}

function showLoginPage() {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login - Smart Buzzer Social</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo { font-size: 48px; margin-bottom: 20px; }
        h1 { color: #fff; font-size: 24px; margin-bottom: 8px; font-weight: 600; }
        .subtitle { color: rgba(255,255,255,0.6); font-size: 14px; margin-bottom: 30px; }
        input[type="password"] {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            margin-bottom: 20px;
            outline: none;
            transition: all 0.3s;
        }
        input[type="password"]:focus {
            border-color: #FF6B35;
            background: rgba(255,255,255,0.12);
        }
        input[type="password"]::placeholder { color: rgba(255,255,255,0.4); }
        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #FF6B35 0%, #f8a100 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(255,107,53,0.3); }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">📊</div>
        <h1>Social Dashboard</h1>
        <p class="subtitle">Smart Buzzer Analytics</p>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter password" required autofocus>
            <button type="submit">Access Dashboard</button>
        </form>
    </div>
</body>
</html>
<?php
}

// ===== HANDLE AJAX REQUESTS =====
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'];
    
    if ($action === 'get_customers') {
        echo json_encode(getCustomerData());
        exit;
    }
    
    if ($action === 'get_analytics') {
        echo json_encode(getAnalyticsData());
        exit;
    }
    
    if ($action === 'delete_customer') {
        $timestamp = $_POST['timestamp'] ?? '';
        echo json_encode(deleteCustomer($timestamp));
        exit;
    }
    
    if ($action === 'toggle_followup') {
        $timestamp = $_POST['timestamp'] ?? '';
        echo json_encode(toggleFollowup($timestamp));
        exit;
    }
    
    if ($action === 'export_csv') {
        exportCSV();
        exit;
    }
    
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

// ===== DATA FUNCTIONS =====
function getCustomerData() {
    $file = __DIR__ . '/customer_data.log';
    $customers = [];
    
    if (!file_exists($file)) {
        return ['customers' => [], 'stats' => getEmptyStats()];
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $cols = explode("\t", $line);
        if (count($cols) < 17) continue;
        
        // Skip Indonesia traffic
        if (isset($cols[15]) && $cols[15] === 'ID') continue;
        
        $customers[] = [
            'timestamp' => $cols[0] ?? '-',
            'username' => $cols[1] ?? '-',
            'platform' => $cols[2] ?? '-',
            'email' => $cols[3] ?? '-',
            'whatsapp' => $cols[4] ?? '-',
            'package' => $cols[5] ?? '-',
            'page_url' => $cols[6] ?? '-',
            'qty' => $cols[7] ?? '-',
            'utm_source' => $cols[8] ?? '-',
            'utm_medium' => $cols[9] ?? '-',
            'utm_campaign' => $cols[10] ?? '-',
            'utm_content' => $cols[11] ?? '-',
            'placement' => $cols[12] ?? '-',
            'state' => $cols[13] ?? '-',
            'zip' => $cols[14] ?? '-',
            'country' => $cols[15] ?? '-',
            'status' => $cols[16] ?? 'FORM_SUBMIT'
        ];
    }
    
    // Reverse for newest first
    $customers = array_reverse($customers);
    
    // Calculate stats
    $stats = calculateCustomerStats($customers);
    
    return ['customers' => $customers, 'stats' => $stats];
}

function calculateCustomerStats($customers) {
    $stats = [
        'total_clicks' => count($customers),
        'today_clicks' => 0,
        'platforms' => [],
        'countries' => [],
        'by_date' => []
    ];
    
    $today = date('Y-m-d');
    
    foreach ($customers as $c) {
        $date = substr($c['timestamp'], 0, 10);
        
        if ($date === $today) {
            $stats['today_clicks']++;
        }
        
        // Platform breakdown
        $platform = $c['platform'];
        if (!isset($stats['platforms'][$platform])) {
            $stats['platforms'][$platform] = 0;
        }
        $stats['platforms'][$platform]++;
        
        // Country breakdown
        $country = $c['country'];
        if (!isset($stats['countries'][$country])) {
            $stats['countries'][$country] = 0;
        }
        $stats['countries'][$country]++;
        
        // By date
        if (!isset($stats['by_date'][$date])) {
            $stats['by_date'][$date] = 0;
        }
        $stats['by_date'][$date]++;
    }
    
    arsort($stats['platforms']);
    arsort($stats['countries']);
    
    return $stats;
}

function getAnalyticsData() {
    $file = __DIR__ . '/page_analytics.log';
    $events = [];
    $metrics = [
        'page_views' => 0,
        'unique_sessions' => [],
        'scroll_25' => 0,
        'scroll_50' => 0,
        'scroll_75' => 0,
        'scroll_100' => 0,
        'category_selects' => 0,
        'platform_selects' => 0,
        'modal_opens' => 0,
        'order_clicks' => 0,
        'devices' => ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0],
        'countries' => [],
        'platforms_selected' => [],
        'categories_selected' => [],
        'time_spent' => [],
        'by_hour' => array_fill(0, 24, 0),
        'by_date' => []
    ];
    
    if (!file_exists($file)) {
        return ['events' => [], 'metrics' => $metrics];
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $cols = explode("\t", $line);
        if (count($cols) < 13) continue;
        
        // Skip Indonesia
        if (isset($cols[12]) && $cols[12] === 'ID') continue;
        
        $event = [
            'timestamp' => $cols[0] ?? '-',
            'event_type' => $cols[1] ?? '-',
            'page_url' => $cols[2] ?? '-',
            'data' => $cols[3] ?? '{}',
            'device' => $cols[4] ?? '-',
            'session_id' => $cols[5] ?? '-',
            'utm_campaign' => $cols[6] ?? '-',
            'utm_source' => $cols[7] ?? '-',
            'utm_medium' => $cols[8] ?? '-',
            'utm_content' => $cols[9] ?? '-',
            'placement' => $cols[10] ?? '-',
            'ip' => $cols[11] ?? '-',
            'country' => $cols[12] ?? '-'
        ];
        
        $events[] = $event;
        
        // Parse event data
        $eventData = json_decode($event['data'], true) ?: [];
        $eventType = $event['event_type'];
        $date = substr($event['timestamp'], 0, 10);
        $hour = (int)substr($event['timestamp'], 11, 2);
        
        // Count metrics
        switch ($eventType) {
            case 'PAGE_VIEW':
                $metrics['page_views']++;
                $metrics['unique_sessions'][$event['session_id']] = true;
                break;
            case 'SCROLL_DEPTH_25':
                $metrics['scroll_25']++;
                break;
            case 'SCROLL_DEPTH_50':
                $metrics['scroll_50']++;
                break;
            case 'SCROLL_DEPTH_75':
                $metrics['scroll_75']++;
                break;
            case 'SCROLL_DEPTH_100':
                $metrics['scroll_100']++;
                break;
            case 'CATEGORY_SELECT':
                $metrics['category_selects']++;
                $cat = $eventData['category'] ?? 'unknown';
                if (!isset($metrics['categories_selected'][$cat])) {
                    $metrics['categories_selected'][$cat] = 0;
                }
                $metrics['categories_selected'][$cat]++;
                break;
            case 'PLATFORM_SELECT':
                $metrics['platform_selects']++;
                $plat = $eventData['platform'] ?? 'unknown';
                if (!isset($metrics['platforms_selected'][$plat])) {
                    $metrics['platforms_selected'][$plat] = 0;
                }
                $metrics['platforms_selected'][$plat]++;
                break;
            case 'MODAL_OPEN':
                $metrics['modal_opens']++;
                break;
            case 'ORDER_CLICK':
                $metrics['order_clicks']++;
                break;
            case 'EXIT_PAGE':
                if (isset($eventData['time_spent'])) {
                    $metrics['time_spent'][] = (int)$eventData['time_spent'];
                }
                break;
        }
        
        // Device breakdown
        $device = $event['device'];
        if (isset($metrics['devices'][$device])) {
            $metrics['devices'][$device]++;
        }
        
        // Country breakdown
        $country = $event['country'];
        if (!isset($metrics['countries'][$country])) {
            $metrics['countries'][$country] = 0;
        }
        $metrics['countries'][$country]++;
        
        // By hour
        $metrics['by_hour'][$hour]++;
        
        // By date
        if (!isset($metrics['by_date'][$date])) {
            $metrics['by_date'][$date] = 0;
        }
        $metrics['by_date'][$date]++;
    }
    
    // Calculate derived metrics
    $metrics['unique_visitors'] = count($metrics['unique_sessions']);
    unset($metrics['unique_sessions']);
    
    $metrics['avg_time_spent'] = !empty($metrics['time_spent']) 
        ? round(array_sum($metrics['time_spent']) / count($metrics['time_spent'])) 
        : 0;
    unset($metrics['time_spent']);
    
    // Conversion funnel
    $metrics['funnel'] = [
        'page_views' => $metrics['page_views'],
        'scroll_50' => $metrics['scroll_50'],
        'platform_selects' => $metrics['platform_selects'],
        'modal_opens' => $metrics['modal_opens'],
        'order_clicks' => $metrics['order_clicks']
    ];
    
    arsort($metrics['countries']);
    arsort($metrics['platforms_selected']);
    arsort($metrics['categories_selected']);
    
    return ['events' => array_reverse(array_slice($events, -500)), 'metrics' => $metrics];
}

function getCampaignBreakdown() {
    $file = __DIR__ . '/customer_data.log';
    $campaigns = [];
    
    if (!file_exists($file)) {
        return ['campaigns' => []];
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $cols = explode("\t", $line);
        if (count($cols) < 17) continue;
        
        // Skip Indonesia
        if (isset($cols[15]) && $cols[15] === 'ID') continue;
        
        $medium = $cols[9] ?? '-';   // Campaign (utm_medium)
        $campaign = $cols[10] ?? '-'; // Adset (utm_campaign)
        $content = $cols[11] ?? '-';  // Ad (utm_content)
        
        if ($medium === '-') $medium = 'Direct';
        if ($campaign === '-') $campaign = 'No Adset';
        if ($content === '-') $content = 'No Ad';
        
        // Initialize campaign
        if (!isset($campaigns[$medium])) {
            $campaigns[$medium] = [
                'name' => $medium,
                'clicks' => 0,
                'adsets' => []
            ];
        }
        $campaigns[$medium]['clicks']++;
        
        // Initialize adset
        if (!isset($campaigns[$medium]['adsets'][$campaign])) {
            $campaigns[$medium]['adsets'][$campaign] = [
                'name' => $campaign,
                'clicks' => 0,
                'ads' => []
            ];
        }
        $campaigns[$medium]['adsets'][$campaign]['clicks']++;
        
        // Initialize ad
        if (!isset($campaigns[$medium]['adsets'][$campaign]['ads'][$content])) {
            $campaigns[$medium]['adsets'][$campaign]['ads'][$content] = [
                'name' => $content,
                'clicks' => 0
            ];
        }
        $campaigns[$medium]['adsets'][$campaign]['ads'][$content]['clicks']++;
    }
    
    // Sort by clicks
    uasort($campaigns, function($a, $b) {
        return $b['clicks'] - $a['clicks'];
    });
    
    return ['campaigns' => $campaigns];
}

function deleteCustomer($timestamp) {
    $file = __DIR__ . '/customer_data.log';
    if (!file_exists($file)) {
        return ['success' => false, 'message' => 'File not found'];
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    $newLines = [];
    $found = false;
    
    foreach ($lines as $line) {
        if (strpos($line, $timestamp) === 0) {
            $found = true;
            continue;
        }
        $newLines[] = $line;
    }
    
    if ($found) {
        file_put_contents($file, implode("\n", $newLines) . "\n", LOCK_EX);
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Entry not found'];
}

function toggleFollowup($timestamp) {
    $file = __DIR__ . '/customer_data.log';
    if (!file_exists($file)) {
        return ['success' => false, 'message' => 'File not found'];
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    $newLines = [];
    $found = false;
    $newStatus = '';
    
    foreach ($lines as $line) {
        if (strpos($line, $timestamp) === 0) {
            $found = true;
            $cols = explode("\t", $line);
            if (count($cols) >= 17) {
                $currentStatus = $cols[16];
                $newStatus = ($currentStatus === 'FOLLOWED_UP') ? 'FORM_SUBMIT' : 'FOLLOWED_UP';
                $cols[16] = $newStatus;
                $line = implode("\t", $cols);
            }
        }
        $newLines[] = $line;
    }
    
    if ($found) {
        file_put_contents($file, implode("\n", $newLines) . "\n", LOCK_EX);
        return ['success' => true, 'new_status' => $newStatus];
    }
    
    return ['success' => false, 'message' => 'Entry not found'];
}

function exportCSV() {
    $data = getCustomerData();
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="social_customers_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, [
        'Timestamp', 'Username', 'Platform', 'Email', 'WhatsApp', 'Package', 'Qty',
        'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content', 'Placement',
        'Country', 'Status'
    ]);

    // Data
    foreach ($data['customers'] as $c) {
        fputcsv($output, [
            $c['timestamp'], $c['username'], $c['platform'], $c['email'], $c['whatsapp'],
            $c['package'], $c['qty'], $c['utm_source'], $c['utm_medium'],
            $c['utm_campaign'], $c['utm_content'], $c['placement'],
            $c['country'], $c['status']
        ]);
    }
    
    fclose($output);
    exit;
}

function getEmptyStats() {
    return [
        'total_clicks' => 0,
        'today_clicks' => 0,
        'platforms' => [],
        'countries' => [],
        'by_date' => []
    ];
}

// Get campaign data for initial load
$campaignData = getCampaignBreakdown();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Buzzer Social</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --bg-dark: #0f1419;
            --bg-card: #1a1f2e;
            --bg-hover: #252b3d;
            --border-color: #2d3548;
            --text-primary: #e7e9ea;
            --text-secondary: #71767b;
            --orange: #FF6B35;
            --orange-light: rgba(255, 107, 53, 0.15);
            --green: #00c853;
            --green-light: rgba(0, 200, 83, 0.15);
            --blue: #1d9bf0;
            --blue-light: rgba(29, 155, 240, 0.15);
            --purple: #a855f7;
            --purple-light: rgba(168, 85, 247, 0.15);
            --red: #f4212e;
            --red-light: rgba(244, 33, 46, 0.15);
            --yellow: #ffd700;
            --yellow-light: rgba(255, 215, 0, 0.15);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
        }
        
        /* Header */
        .header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .header-logo {
            font-size: 24px;
        }
        
        .header-title {
            font-size: 20px;
            font-weight: 700;
        }
        
        .header-title span {
            color: var(--orange);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--orange);
            color: white;
        }
        
        .btn-primary:hover {
            background: #e55a2b;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: var(--bg-hover);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: var(--border-color);
        }
        
        .btn-danger {
            background: var(--red-light);
            color: var(--red);
        }
        
        .btn-danger:hover {
            background: var(--red);
            color: white;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 0 24px;
        }
        
        .tab {
            padding: 16px 24px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .tab:hover {
            color: var(--text-primary);
            background: var(--bg-hover);
        }
        
        .tab.active {
            color: var(--orange);
            border-bottom-color: var(--orange);
        }
        
        .tab i {
            margin-right: 8px;
        }
        
        /* Main Content */
        .main-content {
            padding: 24px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
        }
        
        .stat-card.orange { border-left: 4px solid var(--orange); }
        .stat-card.green { border-left: 4px solid var(--green); }
        .stat-card.blue { border-left: 4px solid var(--blue); }
        .stat-card.purple { border-left: 4px solid var(--purple); }
        .stat-card.yellow { border-left: 4px solid var(--yellow); }
        
        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
        }
        
        .stat-card.orange .stat-value { color: var(--orange); }
        .stat-card.green .stat-value { color: var(--green); }
        .stat-card.blue .stat-value { color: var(--blue); }
        .stat-card.purple .stat-value { color: var(--purple); }
        .stat-card.yellow .stat-value { color: var(--yellow); }
        
        .stat-sub {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }
        
        /* Filter Bar */
        .filter-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-label {
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .filter-input {
            padding: 10px 14px;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        
        .filter-input:focus {
            border-color: var(--orange);
        }
        
        .search-input {
            flex: 1;
            min-width: 200px;
        }
        
        /* Table */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-scroll {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            background: var(--bg-hover);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            white-space: nowrap;
        }
        
        tr:hover {
            background: var(--bg-hover);
        }
        
        td {
            font-size: 14px;
            white-space: nowrap;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-orange { background: var(--orange-light); color: var(--orange); }
        .badge-green { background: var(--green-light); color: var(--green); }
        .badge-blue { background: var(--blue-light); color: var(--blue); }
        .badge-purple { background: var(--purple-light); color: var(--purple); }
        .badge-red { background: var(--red-light); color: var(--red); }
        .badge-yellow { background: var(--yellow-light); color: #b8860b; }
        
        .platform-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .platform-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: white; }
        .platform-tiktok { background: #000; color: white; }
        .platform-youtube { background: #FF0000; color: white; }
        .platform-facebook { background: #1877F2; color: white; }
        .platform-twitter, .platform-x { background: #000; color: white; }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .followup-btn {
            background: var(--green-light);
            color: var(--green);
        }
        
        .followup-btn.active {
            background: var(--green);
            color: white;
        }
        
        .followup-btn:hover {
            transform: scale(1.05);
        }
        
        .delete-btn {
            background: var(--red-light);
            color: var(--red);
            margin-left: 8px;
        }
        
        .delete-btn:hover {
            background: var(--red);
            color: white;
        }
        
        /* Funnel Chart */
        .funnel-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .funnel-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .funnel-step {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .funnel-label {
            width: 140px;
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .funnel-bar-container {
            flex: 1;
            height: 32px;
            background: var(--bg-hover);
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }
        
        .funnel-bar {
            height: 100%;
            border-radius: 6px;
            transition: width 0.5s ease;
        }
        
        .funnel-bar.orange { background: var(--orange); }
        .funnel-bar.green { background: var(--green); }
        .funnel-bar.blue { background: var(--blue); }
        .funnel-bar.purple { background: var(--purple); }
        .funnel-bar.yellow { background: var(--yellow); }
        
        .funnel-value {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            font-weight: 600;
        }
        
        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        
        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }
        
        .chart-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .breakdown-item:last-child {
            border-bottom: none;
        }
        
        .breakdown-label {
            font-size: 14px;
        }
        
        .breakdown-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--orange);
        }
        
        /* Campaign Breakdown */
        .campaign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 1200px) {
            .campaign-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .campaign-column {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .column-header {
            padding: 16px 20px;
            background: var(--bg-hover);
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }
        
        .campaign-list {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .campaign-item {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .campaign-item:hover {
            background: var(--bg-hover);
        }
        
        .campaign-item.selected {
            background: var(--orange-light);
            border-left: 3px solid var(--orange);
        }
        
        .campaign-name {
            font-size: 14px;
            font-weight: 500;
            word-break: break-word;
        }
        
        .campaign-clicks {
            font-size: 14px;
            font-weight: 600;
            color: var(--orange);
            white-space: nowrap;
            margin-left: 12px;
        }
        
        /* Loading */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color);
            border-top-color: var(--orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .empty-state p {
            font-size: 16px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 16px;
            }
            
            .tabs {
                overflow-x: auto;
            }
            
            .tab {
                padding: 12px 16px;
                font-size: 14px;
            }
            
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="header-logo">📊</div>
            <div class="header-title">Smart Buzzer <span>Social</span></div>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-primary" onclick="exportData()">
                <i class="fas fa-download"></i> Export CSV
            </button>
            <form method="POST" style="display:inline;">
                <button type="submit" name="logout" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="tabs">
        <div class="tab active" data-tab="customers">
            <i class="fas fa-users"></i> Customers
        </div>
        <div class="tab" data-tab="analytics">
            <i class="fas fa-chart-line"></i> Analytics
        </div>
        <div class="tab" data-tab="campaigns">
            <i class="fas fa-bullhorn"></i> Campaign Breakdown
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Customers Tab -->
        <div class="tab-content active" id="customers-tab">
            <div class="stats-grid" id="customer-stats">
                <div class="stat-card orange">
                    <div class="stat-label">Total Submissions</div>
                    <div class="stat-value" id="stat-total-clicks">-</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Today's Submissions</div>
                    <div class="stat-value" id="stat-today-clicks">-</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Top Platform</div>
                    <div class="stat-value" id="stat-top-platform" style="font-size: 20px;">-</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-label">Top Country</div>
                    <div class="stat-value" id="stat-top-country" style="font-size: 20px;">-</div>
                </div>
            </div>
            
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label">From:</label>
                    <input type="date" class="filter-input" id="filter-date-from">
                </div>
                <div class="filter-group">
                    <label class="filter-label">To:</label>
                    <input type="date" class="filter-input" id="filter-date-to">
                </div>
                <input type="text" class="filter-input search-input" id="filter-search" placeholder="Search by username, platform, country...">
                <button class="btn btn-secondary" onclick="applyFilters()">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <button class="btn btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
            
            <div class="table-container">
                <div class="table-scroll">
                    <table id="customers-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Platform</th>
                                <th>Username</th>
                                <th>Package</th>
                                <th>Qty</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Campaign</th>
                                <th>Adset</th>
                                <th>Ad</th>
                                <th>Country</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customers-tbody">
                            <tr>
                                <td colspan="12">
                                    <div class="loading"><div class="spinner"></div></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Analytics Tab -->
        <div class="tab-content" id="analytics-tab">
            <div class="stats-grid" id="analytics-stats">
                <div class="stat-card orange">
                    <div class="stat-label">Page Views</div>
                    <div class="stat-value" id="stat-page-views">-</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Unique Visitors</div>
                    <div class="stat-value" id="stat-unique-visitors">-</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Modal Opens</div>
                    <div class="stat-value" id="stat-modal-opens">-</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-label">Order Clicks</div>
                    <div class="stat-value" id="stat-order-clicks">-</div>
                </div>
                <div class="stat-card yellow">
                    <div class="stat-label">Avg Time (sec)</div>
                    <div class="stat-value" id="stat-avg-time">-</div>
                </div>
            </div>
            
            <div class="funnel-container">
                <div class="funnel-title"><i class="fas fa-filter"></i> Conversion Funnel</div>
                <div id="funnel-chart"></div>
            </div>
            
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-mobile-alt"></i> Device Breakdown</div>
                    <div id="device-breakdown"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-globe"></i> Top Countries</div>
                    <div id="country-breakdown"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-hashtag"></i> Platform Selections</div>
                    <div id="platform-breakdown"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-title"><i class="fas fa-heart"></i> Category Selections</div>
                    <div id="category-breakdown"></div>
                </div>
            </div>
        </div>
        
        <!-- Campaigns Tab -->
        <div class="tab-content" id="campaigns-tab">
            <div class="campaign-grid">
                <div class="campaign-column">
                    <div class="column-header">
                        <i class="fas fa-bullhorn"></i> Campaigns (UTM Medium)
                    </div>
                    <div class="campaign-list" id="campaign-list"></div>
                </div>
                <div class="campaign-column">
                    <div class="column-header">
                        <i class="fas fa-layer-group"></i> Adsets (UTM Campaign)
                    </div>
                    <div class="campaign-list" id="adset-list">
                        <div class="empty-state">
                            <i class="fas fa-mouse-pointer"></i>
                            <p>Select a campaign</p>
                        </div>
                    </div>
                </div>
                <div class="campaign-column">
                    <div class="column-header">
                        <i class="fas fa-ad"></i> Ads (UTM Content)
                    </div>
                    <div class="campaign-list" id="ad-list">
                        <div class="empty-state">
                            <i class="fas fa-mouse-pointer"></i>
                            <p>Select an adset</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Data storage
        let customersData = { customers: [], stats: {} };
        let analyticsData = { events: [], metrics: {} };
        let campaignData = <?php echo json_encode($campaignData); ?>;
        let selectedCampaign = null;
        let selectedAdset = null;
        
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
                
                // Load data on first view
                if (tabId === 'analytics' && analyticsData.events.length === 0) {
                    loadAnalytics();
                }
            });
        });
        
        // Load customers data
        async function loadCustomers() {
            try {
                const response = await fetch('?action=get_customers');
                customersData = await response.json();
                renderCustomers(customersData.customers);
                updateCustomerStats(customersData.stats);
            } catch (error) {
                console.error('Error loading customers:', error);
            }
        }
        
        // Load analytics data
        async function loadAnalytics() {
            try {
                const response = await fetch('?action=get_analytics');
                analyticsData = await response.json();
                renderAnalytics(analyticsData.metrics);
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }
        
        // Render customers table
        function renderCustomers(customers) {
            const tbody = document.getElementById('customers-tbody');
            
            if (customers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="12">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No customer data yet</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = customers.map(c => `
                <tr data-timestamp="${c.timestamp}">
                    <td>${c.timestamp}</td>
                    <td>${getPlatformBadge(c.platform)}</td>
                    <td>${escapeHtml(c.username)}</td>
                    <td>${escapeHtml(c.package)}</td>
                    <td><span class="badge badge-orange">${c.qty}</span></td>
                    <td>${c.email && c.email !== '-' ? escapeHtml(c.email) : '-'}</td>
                    <td>${c.whatsapp !== '-' ? `<a href="https://wa.me/${c.whatsapp.replace(/[^0-9]/g, '')}" target="_blank" style="color: var(--green);">${escapeHtml(c.whatsapp)}</a>` : '-'}</td>
                    <td><span class="badge badge-blue">${escapeHtml(c.utm_medium)}</span></td>
                    <td>${escapeHtml(c.utm_campaign)}</td>
                    <td>${escapeHtml(c.utm_content)}</td>
                    <td><span class="badge badge-purple">${c.country}</span></td>
                    <td>
                        <button class="action-btn followup-btn ${c.status === 'FOLLOWED_UP' ? 'active' : ''}" onclick="toggleFollowup('${c.timestamp}')">
                            <i class="fas fa-check"></i> ${c.status === 'FOLLOWED_UP' ? 'Done' : 'Follow Up'}
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteCustomer('${c.timestamp}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
        
        // Get platform badge
        function getPlatformBadge(platform) {
            const p = platform.toLowerCase();
            const icons = {
                instagram: 'fab fa-instagram',
                tiktok: 'fab fa-tiktok',
                youtube: 'fab fa-youtube',
                facebook: 'fab fa-facebook-f',
                twitter: 'fab fa-twitter',
                x: 'fab fa-x-twitter'
            };
            const icon = icons[p] || 'fas fa-globe';
            return `<span class="platform-badge platform-${p}"><i class="${icon}"></i> ${platform}</span>`;
        }
        
        // Update customer stats
        function updateCustomerStats(stats) {
            document.getElementById('stat-total-clicks').textContent = stats.total_clicks || 0;
            document.getElementById('stat-today-clicks').textContent = stats.today_clicks || 0;
            
            const topPlatform = Object.keys(stats.platforms || {})[0];
            document.getElementById('stat-top-platform').textContent = topPlatform || '-';
            
            const topCountry = Object.keys(stats.countries || {})[0];
            document.getElementById('stat-top-country').textContent = topCountry || '-';
        }
        
        // Render analytics
        function renderAnalytics(metrics) {
            document.getElementById('stat-page-views').textContent = metrics.page_views || 0;
            document.getElementById('stat-unique-visitors').textContent = metrics.unique_visitors || 0;
            document.getElementById('stat-modal-opens').textContent = metrics.modal_opens || 0;
            document.getElementById('stat-order-clicks').textContent = metrics.order_clicks || 0;
            document.getElementById('stat-avg-time').textContent = metrics.avg_time_spent || 0;
            
            // Funnel
            renderFunnel(metrics.funnel || {});
            
            // Breakdowns
            renderBreakdown('device-breakdown', metrics.devices || {});
            renderBreakdown('country-breakdown', Object.fromEntries(Object.entries(metrics.countries || {}).slice(0, 5)));
            renderBreakdown('platform-breakdown', metrics.platforms_selected || {});
            renderBreakdown('category-breakdown', metrics.categories_selected || {});
        }
        
        // Render funnel
        function renderFunnel(funnel) {
            const container = document.getElementById('funnel-chart');
            const maxVal = Math.max(...Object.values(funnel), 1);
            
            const steps = [
                { label: 'Page Views', key: 'page_views', color: 'orange' },
                { label: 'Scroll 50%', key: 'scroll_50', color: 'blue' },
                { label: 'Platform Select', key: 'platform_selects', color: 'purple' },
                { label: 'Modal Opens', key: 'modal_opens', color: 'yellow' },
                { label: 'Order Clicks', key: 'order_clicks', color: 'green' }
            ];
            
            container.innerHTML = steps.map(step => {
                const value = funnel[step.key] || 0;
                const width = (value / maxVal * 100) || 0;
                return `
                    <div class="funnel-step">
                        <div class="funnel-label">${step.label}</div>
                        <div class="funnel-bar-container">
                            <div class="funnel-bar ${step.color}" style="width: ${width}%"></div>
                            <span class="funnel-value">${value}</span>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Render breakdown
        function renderBreakdown(containerId, data) {
            const container = document.getElementById(containerId);
            const entries = Object.entries(data);
            
            if (entries.length === 0) {
                container.innerHTML = '<div class="empty-state" style="padding:20px;"><p>No data</p></div>';
                return;
            }
            
            container.innerHTML = entries.map(([key, value]) => `
                <div class="breakdown-item">
                    <span class="breakdown-label">${escapeHtml(key)}</span>
                    <span class="breakdown-value">${value}</span>
                </div>
            `).join('');
        }
        
        // Render campaign breakdown
        function renderCampaigns() {
            const campaigns = campaignData.campaigns || {};
            const container = document.getElementById('campaign-list');
            
            if (Object.keys(campaigns).length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No campaign data</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = Object.values(campaigns).map(c => `
                <div class="campaign-item" data-campaign="${escapeHtml(c.name)}" onclick="selectCampaign('${escapeHtml(c.name)}')">
                    <span class="campaign-name">${escapeHtml(c.name)}</span>
                    <span class="campaign-clicks">${c.clicks} clicks</span>
                </div>
            `).join('');
        }
        
        // Select campaign
        function selectCampaign(campaignName) {
            selectedCampaign = campaignName;
            selectedAdset = null;
            
            // Highlight selected
            document.querySelectorAll('#campaign-list .campaign-item').forEach(el => {
                el.classList.toggle('selected', el.dataset.campaign === campaignName);
            });
            
            // Render adsets
            const campaign = campaignData.campaigns[campaignName];
            const adsets = campaign ? campaign.adsets : {};
            const container = document.getElementById('adset-list');
            
            if (Object.keys(adsets).length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No adsets</p>
                    </div>
                `;
            } else {
                container.innerHTML = Object.values(adsets).map(a => `
                    <div class="campaign-item" data-adset="${escapeHtml(a.name)}" onclick="selectAdset('${escapeHtml(a.name)}')">
                        <span class="campaign-name">${escapeHtml(a.name)}</span>
                        <span class="campaign-clicks">${a.clicks} clicks</span>
                    </div>
                `).join('');
            }
            
            // Clear ads
            document.getElementById('ad-list').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-mouse-pointer"></i>
                    <p>Select an adset</p>
                </div>
            `;
        }
        
        // Select adset
        function selectAdset(adsetName) {
            selectedAdset = adsetName;
            
            // Highlight selected
            document.querySelectorAll('#adset-list .campaign-item').forEach(el => {
                el.classList.toggle('selected', el.dataset.adset === adsetName);
            });
            
            // Render ads
            const campaign = campaignData.campaigns[selectedCampaign];
            const adset = campaign ? campaign.adsets[adsetName] : null;
            const ads = adset ? adset.ads : {};
            const container = document.getElementById('ad-list');
            
            if (Object.keys(ads).length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No ads</p>
                    </div>
                `;
            } else {
                container.innerHTML = Object.values(ads).map(a => `
                    <div class="campaign-item">
                        <span class="campaign-name">${escapeHtml(a.name)}</span>
                        <span class="campaign-clicks">${a.clicks} clicks</span>
                    </div>
                `).join('');
            }
        }
        
        // Toggle followup
        async function toggleFollowup(timestamp) {
            try {
                const formData = new FormData();
                formData.append('timestamp', timestamp);
                
                const response = await fetch('?action=toggle_followup', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    loadCustomers();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        // Delete customer
        async function deleteCustomer(timestamp) {
            if (!confirm('Are you sure you want to delete this entry?')) return;
            
            try {
                const formData = new FormData();
                formData.append('timestamp', timestamp);
                
                const response = await fetch('?action=delete_customer', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    loadCustomers();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        // Apply filters
        function applyFilters() {
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;
            const search = document.getElementById('filter-search').value.toLowerCase();
            
            let filtered = customersData.customers.filter(c => {
                const date = c.timestamp.substring(0, 10);
                
                if (dateFrom && date < dateFrom) return false;
                if (dateTo && date > dateTo) return false;
                
                if (search) {
                    const searchable = [
                        c.username, c.platform, c.package, c.whatsapp,
                        c.utm_medium, c.utm_campaign, c.utm_content, c.country
                    ].join(' ').toLowerCase();
                    if (!searchable.includes(search)) return false;
                }
                
                return true;
            });
            
            renderCustomers(filtered);
        }
        
        // Clear filters
        function clearFilters() {
            document.getElementById('filter-date-from').value = '';
            document.getElementById('filter-date-to').value = '';
            document.getElementById('filter-search').value = '';
            renderCustomers(customersData.customers);
        }
        
        // Refresh data
        function refreshData() {
            loadCustomers();
            loadAnalytics();
        }
        
        // Export CSV
        function exportData() {
            window.location.href = '?action=export_csv';
        }
        
        // Escape HTML
        function escapeHtml(text) {
            if (!text || text === '-') return text;
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Initialize
        loadCustomers();
        renderCampaigns();
    </script>
</body>
</html>