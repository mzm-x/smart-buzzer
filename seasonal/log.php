<?php
/**
 * Smart Buzzer - Valentine Landing Page Dashboard
 * Version: 2.8
 * 
 * Features:
 * - Customer Tab (pricing clicks with UTM attribution)
 * - Analytics Tab (page views, scroll depth, device breakdown)
 * - Campaign Breakdown Tab (Campaign -> Adset -> Ad hierarchy)
 */

session_start();

// Password Protection
$password = 'smartbuzzer2024';
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

if (isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['logged_in'] = true;
        $isLoggedIn = true;
    } else {
        $loginError = 'Invalid password';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: log.php');
    exit;
}

// File paths
$customerDataFile = __DIR__ . '/customer_data.log';
$analyticsFile = __DIR__ . '/page_analytics.log';

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if (!$isLoggedIn) {
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    
    $action = $_GET['action'];
    
    // Get customers data
    if ($action === 'get_customers') {
        $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
        $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
        $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
        
        $customers = [];
        
        if (file_exists($customerDataFile)) {
            $lines = file($customerDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $cols = explode("\t", $line);
                if (count($cols) >= 7) {
                    $timestamp = $cols[0];
                    $date = substr($timestamp, 0, 10);
                    
                    // Date filter
                    if ($dateFrom && $date < $dateFrom) continue;
                    if ($dateTo && $date > $dateTo) continue;
                    
                    $customer = [
                        'timestamp' => $cols[0] ?? '',
                        'business' => $cols[1] ?? '-',
                        'location' => $cols[2] ?? '-',
                        'email' => $cols[3] ?? '-',
                        'whatsapp' => $cols[4] ?? '-',
                        'package' => $cols[5] ?? '-',
                        'page_url' => $cols[6] ?? '',
                        'reviews_qty' => $cols[7] ?? '-',
                        'utm_source' => $cols[8] ?? '',
                        'utm_medium' => $cols[9] ?? '',
                        'utm_campaign' => $cols[10] ?? '',
                        'utm_content' => $cols[11] ?? '',
                        'placement' => $cols[12] ?? '',
                        'state' => $cols[13] ?? '',
                        'zip' => $cols[14] ?? '',
                        'country' => $cols[15] ?? '',
                        'status' => $cols[16] ?? 'CLICK_ONLY',
                        'utm_term' => (count($cols) >= 18) ? ($cols[17] ?? '-') : '-'
                    ];
                    
                    // Search filter
                    if ($search) {
                        $searchStr = strtolower(implode(' ', $customer));
                        if (strpos($searchStr, $search) === false) continue;
                    }
                    
                    $customers[] = $customer;
                }
            }
        }
        
        // Sort by timestamp descending
        usort($customers, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });
        
        echo json_encode(['customers' => $customers, 'total' => count($customers)]);
        exit;
    }
    
    // Get analytics data
    if ($action === 'get_analytics') {
        $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
        $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
        
        $analytics = [
            'page_views' => 0,
            'unique_sessions' => [],
            'scroll_depths' => ['25' => 0, '50' => 0, '75' => 0, '100' => 0],
            'devices' => ['Desktop' => 0, 'Mobile' => 0, 'Tablet' => 0],
            'countries' => [],
            'hourly' => array_fill(0, 24, 0),
            'daily' => [],
            'events' => []
        ];
        
        if (file_exists($analyticsFile)) {
            $lines = file($analyticsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $cols = explode("\t", $line);
                if (count($cols) >= 5) {
                    $timestamp = $cols[0];
                    $date = substr($timestamp, 0, 10);
                    $hour = (int)substr($timestamp, 11, 2);
                    $event = $cols[1];
                    $device = $cols[4] ?? 'Desktop';
                    $session = $cols[5] ?? '';
                    $country = $cols[12] ?? 'unknown';
                    
                    // Date filter
                    if ($dateFrom && $date < $dateFrom) continue;
                    if ($dateTo && $date > $dateTo) continue;
                    
                    // Count events
                    if ($event === 'PAGE_VIEW') {
                        $analytics['page_views']++;
                        $analytics['unique_sessions'][$session] = true;
                        $analytics['hourly'][$hour]++;
                        
                        if (!isset($analytics['daily'][$date])) {
                            $analytics['daily'][$date] = 0;
                        }
                        $analytics['daily'][$date]++;
                        
                        // Device
                        if (isset($analytics['devices'][$device])) {
                            $analytics['devices'][$device]++;
                        }
                        
                        // Country
                        if (!isset($analytics['countries'][$country])) {
                            $analytics['countries'][$country] = 0;
                        }
                        $analytics['countries'][$country]++;
                    }
                    
                    // Scroll depths
                    if (strpos($event, 'SCROLL_DEPTH_') === 0) {
                        $depth = str_replace('SCROLL_DEPTH_', '', $event);
                        if (isset($analytics['scroll_depths'][$depth])) {
                            $analytics['scroll_depths'][$depth]++;
                        }
                    }
                    
                    // Track all events
                    if (!isset($analytics['events'][$event])) {
                        $analytics['events'][$event] = 0;
                    }
                    $analytics['events'][$event]++;
                }
            }
        }
        
        $analytics['unique_visitors'] = count($analytics['unique_sessions']);
        unset($analytics['unique_sessions']);
        
        // Sort countries by count
        arsort($analytics['countries']);
        $analytics['countries'] = array_slice($analytics['countries'], 0, 10, true);
        
        // Sort daily
        ksort($analytics['daily']);
        
        echo json_encode($analytics);
        exit;
    }
    
    // Get campaign breakdown
    if ($action === 'get_campaigns') {
        $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
        $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
        
        $campaigns = []; // utm_medium = Campaign
        $adsets = [];    // utm_campaign = Adset
        $ads = [];       // utm_content = Ad
        
        if (file_exists($customerDataFile)) {
            $lines = file($customerDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $cols = explode("\t", $line);
                if (count($cols) >= 12) {
                    $timestamp = $cols[0];
                    $date = substr($timestamp, 0, 10);
                    
                    // Date filter
                    if ($dateFrom && $date < $dateFrom) continue;
                    if ($dateTo && $date > $dateTo) continue;
                    
                    $campaign = $cols[9] ?? '(direct)';  // utm_medium
                    $adset = $cols[10] ?? '(none)';      // utm_campaign
                    $ad = $cols[11] ?? '(none)';         // utm_content
                    
                    if (empty($campaign)) $campaign = '(direct)';
                    if (empty($adset)) $adset = '(none)';
                    if (empty($ad)) $ad = '(none)';
                    
                    // Count campaigns
                    if (!isset($campaigns[$campaign])) {
                        $campaigns[$campaign] = ['clicks' => 0, 'adsets' => []];
                    }
                    $campaigns[$campaign]['clicks']++;
                    
                    // Count adsets per campaign
                    if (!isset($campaigns[$campaign]['adsets'][$adset])) {
                        $campaigns[$campaign]['adsets'][$adset] = ['clicks' => 0, 'ads' => []];
                    }
                    $campaigns[$campaign]['adsets'][$adset]['clicks']++;
                    
                    // Count ads per adset
                    if (!isset($campaigns[$campaign]['adsets'][$adset]['ads'][$ad])) {
                        $campaigns[$campaign]['adsets'][$adset]['ads'][$ad] = 0;
                    }
                    $campaigns[$campaign]['adsets'][$adset]['ads'][$ad]++;
                }
            }
        }
        
        // Sort by clicks
        uasort($campaigns, function($a, $b) {
            return $b['clicks'] - $a['clicks'];
        });
        
        echo json_encode(['campaigns' => $campaigns]);
        exit;
    }
    
    // Delete customer entry
    if ($action === 'delete_customer') {
        $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        
        if ($timestamp && file_exists($customerDataFile)) {
            $lines = file($customerDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $newLines = [];
            
            foreach ($lines as $line) {
                if (strpos($line, $timestamp) !== 0) {
                    $newLines[] = $line;
                }
            }
            
            file_put_contents($customerDataFile, implode("\n", $newLines) . "\n");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Entry not found']);
        }
        exit;
    }
    
    // Toggle follow-up status
    if ($action === 'toggle_followup') {
        $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        
        if ($timestamp && file_exists($customerDataFile)) {
            $lines = file($customerDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $newLines = [];
            $toggled = false;
            
            foreach ($lines as $line) {
                if (strpos($line, $timestamp) === 0 && !$toggled) {
                    $cols = explode("\t", $line);
                    if (count($cols) >= 17) {
                        $current = trim($cols[16]);
                        $cols[16] = ($current === 'FOLLOWED_UP') ? 'CLICK_ONLY' : 'FOLLOWED_UP';
                    } elseif (count($cols) >= 7) {
                        while (count($cols) < 17) { $cols[] = ''; }
                        $cols[16] = 'FOLLOWED_UP';
                    }
                    $newLines[] = implode("\t", $cols);
                    $toggled = true;
                } else {
                    $newLines[] = $line;
                }
            }
            
            file_put_contents($customerDataFile, implode("\n", $newLines) . "\n");
            echo json_encode(['success' => true, 'toggled' => $toggled]);
        } else {
            echo json_encode(['error' => 'Entry not found']);
        }
        exit;
    }
    
    // Export CSV
    if ($action === 'export_csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="customers_export_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, [
            'Timestamp', 'Business', 'Location', 'Email', 'WhatsApp', 'Package',
            'Reviews Qty', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content',
            'Placement', 'State', 'Zip', 'Country', 'Status', 'UTM Term'
        ]);
        
        if (file_exists($customerDataFile)) {
            $lines = file($customerDataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $cols = explode("\t", $line);
                if (count($cols) >= 7) {
                    fputcsv($output, [
                        $cols[0] ?? '',
                        $cols[1] ?? '-',
                        $cols[2] ?? '-',
                        $cols[3] ?? '-',
                        $cols[4] ?? '-',
                        $cols[5] ?? '-',
                        $cols[7] ?? '-',
                        $cols[8] ?? '',
                        $cols[9] ?? '',
                        $cols[10] ?? '',
                        $cols[11] ?? '',
                        $cols[12] ?? '',
                        $cols[13] ?? '',
                        $cols[14] ?? '',
                        $cols[15] ?? '',
                        $cols[16] ?? 'CLICK_ONLY',
                        (count($cols) >= 18) ? ($cols[17] ?? '-') : '-'
                    ]);
                }
            }
        }
        
        fclose($output);
        exit;
    }
    
    echo json_encode(['error' => 'Invalid action']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentine Dashboard - Smart Buzzer</title>
    <link rel="icon" type="image/x-icon" href="https://smart-buzzer.com/tracker/sb.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --rose-primary: #E63946;
            --rose-dark: #9E2A2B;
            --rose-light: #F8B4B4;
            --rose-pale: #FFF0F0;
            --bg-primary: #0f0f0f;
            --bg-secondary: #1a1a1a;
            --bg-tertiary: #252525;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --text-muted: #666666;
            --border-color: #333333;
            --success: #22c55e;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Login Page */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-box {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .login-box h1 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 24px;
        }

        .login-box .subtitle {
            text-align: center;
            color: var(--rose-primary);
            margin-bottom: 32px;
            font-size: 14px;
        }

        .login-box input {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            margin-bottom: 16px;
        }

        .login-box input:focus {
            outline: none;
            border-color: var(--rose-primary);
        }

        .login-box button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--rose-primary), var(--rose-dark));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.4);
        }

        .login-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
            font-size: 14px;
        }

        /* Dashboard Layout */
        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .sidebar-logo img {
            height: 36px;
        }

        .sidebar-logo span {
            font-weight: 600;
            font-size: 14px;
            color: var(--rose-primary);
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 4px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .sidebar-nav a.active {
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.2), rgba(158, 42, 43, 0.2));
            color: var(--rose-primary);
        }

        .sidebar-nav .icon {
            width: 20px;
            text-align: center;
        }

        .sidebar-bottom {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }

        .sidebar-bottom a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .sidebar-bottom a:hover {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 24px;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .page-header .badge {
            background: var(--rose-pale);
            color: var(--rose-primary);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 12px;
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            padding: 20px;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 14px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 13px;
            min-width: 150px;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--rose-primary);
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--rose-primary), var(--rose-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
        }

        .stat-card .label {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-card .change {
            font-size: 12px;
            margin-top: 8px;
        }

        .stat-card .change.positive {
            color: var(--success);
        }

        .stat-card .change.negative {
            color: #ef4444;
        }

        /* Tables */
        .table-container {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .table-wrapper {
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
            font-size: 13px;
        }

        th {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-weight: 600;
            white-space: nowrap;
        }

        td {
            color: var(--text-primary);
        }

        tr:hover {
            background: var(--bg-tertiary);
        }

        .badge-package {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-starter {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
        }

        .badge-growth {
            background: rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
        }

        .badge-performance {
            background: rgba(230, 57, 70, 0.15);
            color: var(--rose-primary);
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-click {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .badge-converted {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
        }

        .badge-followed {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
        }

        .action-btn.followup {
            border-color: var(--success);
            color: var(--success);
            text-decoration: none;
            display: inline-block;
        }

        .action-btn.followup:hover {
            background: rgba(34, 197, 94, 0.1);
        }

        .utm-cell {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .action-btn {
            padding: 6px 10px;
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .action-btn.delete:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #ef4444;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 24px;
            background: var(--bg-secondary);
            padding: 6px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab:hover {
            color: var(--text-primary);
            background: var(--bg-tertiary);
        }

        .tab.active {
            background: linear-gradient(135deg, var(--rose-primary), var(--rose-dark));
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Charts placeholder */
        .chart-container {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .chart-placeholder {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-tertiary);
            border-radius: 8px;
            color: var(--text-muted);
        }

        /* Campaign Breakdown */
        .campaign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        .campaign-column {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .campaign-column-header {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .campaign-column-header .count {
            background: var(--bg-tertiary);
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .campaign-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .campaign-item {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .campaign-item:hover {
            background: var(--bg-tertiary);
        }

        .campaign-item.active {
            background: rgba(230, 57, 70, 0.1);
            border-left: 3px solid var(--rose-primary);
        }

        .campaign-item .name {
            font-size: 13px;
            color: var(--text-primary);
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .campaign-item .clicks {
            background: var(--bg-tertiary);
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: var(--rose-primary);
        }

        /* Scroll Depth Bars */
        .scroll-bars {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .scroll-bar-item {
            flex: 1;
            min-width: 150px;
        }

        .scroll-bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .scroll-bar-label span:first-child {
            color: var(--text-secondary);
        }

        .scroll-bar-label span:last-child {
            color: var(--text-primary);
            font-weight: 600;
        }

        .scroll-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 4px;
            overflow: hidden;
        }

        .scroll-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--rose-primary), var(--rose-dark));
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* Loading */
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            color: var(--text-muted);
        }

        .loading-spinner {
            width: 24px;
            height: 24px;
            border: 2px solid var(--border-color);
            border-top-color: var(--rose-primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 12px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .campaign-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters {
                flex-direction: column;
            }

            .filter-group input,
            .filter-group select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
    <!-- Login Page -->
    <div class="login-container">
        <div class="login-box">
            <h1>Dashboard Login</h1>
            <p class="subtitle">Valentine Landing Page</p>
            
            <?php if (isset($loginError)): ?>
            <div class="login-error"><?php echo $loginError; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" required autofocus>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Dashboard -->
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="https://smart-buzzer.com/wp-content/uploads/2021/10/REV-COLOR-Smart-Buzzer-10.png.webp" alt="Smart Buzzer">
                <span>Valentine</span>
            </div>
            
            <ul class="sidebar-nav">
                <li>
                    <a href="#" class="active" data-tab="customers">
                        <span class="icon">&#128101;</span>
                        Customers
                    </a>
                </li>
                <li>
                    <a href="#" data-tab="analytics">
                        <span class="icon">&#128200;</span>
                        Analytics
                    </a>
                </li>
                <li>
                    <a href="#" data-tab="campaigns">
                        <span class="icon">&#127919;</span>
                        Campaign Breakdown
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-bottom">
                <a href="index.php" target="_blank">
                    <span>&#128279;</span> View Landing Page
                </a>
                <a href="?logout=1">
                    <span>&#128682;</span> Logout
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Filters -->
            <div class="filters">
                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" id="dateFrom">
                </div>
                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" id="dateTo">
                </div>
                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" id="searchInput" placeholder="Search...">
                </div>
                <div class="filter-group" style="justify-content: flex-end;">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" onclick="applyFilters()">
                        Apply Filters
                    </button>
                </div>
                <div class="filter-group" style="justify-content: flex-end;">
                    <label>&nbsp;</label>
                    <button class="btn btn-secondary" onclick="exportCSV()">
                        Export CSV
                    </button>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" data-tab="customers">Customers</button>
                <button class="tab" data-tab="analytics">Analytics</button>
                <button class="tab" data-tab="campaigns">Campaign Breakdown</button>
            </div>
            
            <!-- Customers Tab -->
            <div class="tab-content active" id="tab-customers">
                <div class="page-header">
                    <h1>Customer Clicks <span class="badge" id="totalCustomers">0</span></h1>
                </div>
                
                <div class="stats-grid" id="customerStats">
                    <div class="stat-card">
                        <div class="label">Total Clicks</div>
                        <div class="value" id="statTotalClicks">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Today's Clicks</div>
                        <div class="value" id="statTodayClicks">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Top Package</div>
                        <div class="value" id="statTopPackage">-</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Top Country</div>
                        <div class="value" id="statTopCountry">-</div>
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="table-header">
                        <h3>Recent Clicks</h3>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Package</th>
                                    <th>Reviews</th>
                                    <th>UTM Source</th>
                                    <th>UTM Medium</th>
                                    <th>UTM Campaign</th>
                                    <th>UTM Term</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customersTable">
                                <tr>
                                    <td colspan="10" class="loading">
                                        <div class="loading-spinner"></div>
                                        Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Analytics Tab -->
            <div class="tab-content" id="tab-analytics">
                <div class="page-header">
                    <h1>Page Analytics</h1>
                </div>
                
                <div class="stats-grid" id="analyticsStats">
                    <div class="stat-card">
                        <div class="label">Page Views</div>
                        <div class="value" id="statPageViews">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Unique Visitors</div>
                        <div class="value" id="statUniqueVisitors">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Desktop</div>
                        <div class="value" id="statDesktop">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Mobile</div>
                        <div class="value" id="statMobile">0</div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Scroll Depth</h3>
                    </div>
                    <div class="scroll-bars" id="scrollBars">
                        <div class="scroll-bar-item">
                            <div class="scroll-bar-label">
                                <span>25%</span>
                                <span id="scroll25">0</span>
                            </div>
                            <div class="scroll-bar">
                                <div class="scroll-bar-fill" id="scrollBar25" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="scroll-bar-item">
                            <div class="scroll-bar-label">
                                <span>50%</span>
                                <span id="scroll50">0</span>
                            </div>
                            <div class="scroll-bar">
                                <div class="scroll-bar-fill" id="scrollBar50" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="scroll-bar-item">
                            <div class="scroll-bar-label">
                                <span>75%</span>
                                <span id="scroll75">0</span>
                            </div>
                            <div class="scroll-bar">
                                <div class="scroll-bar-fill" id="scrollBar75" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="scroll-bar-item">
                            <div class="scroll-bar-label">
                                <span>100%</span>
                                <span id="scroll100">0</span>
                            </div>
                            <div class="scroll-bar">
                                <div class="scroll-bar-fill" id="scrollBar100" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Events Breakdown</h3>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody id="eventsTable">
                                <tr>
                                    <td colspan="2" class="loading">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Campaigns Tab -->
            <div class="tab-content" id="tab-campaigns">
                <div class="page-header">
                    <h1>Campaign Breakdown</h1>
                </div>
                
                <div class="campaign-grid">
                    <div class="campaign-column">
                        <div class="campaign-column-header">
                            Campaign (UTM Campaign)
                            <span class="count" id="campaignCount">0</span>
                        </div>
                        <div class="campaign-list" id="campaignList">
                            <div class="loading">Loading...</div>
                        </div>
                    </div>

                    <div class="campaign-column">
                        <div class="campaign-column-header">
                            Adset
                            <span class="count" id="adsetCount">0</span>
                        </div>
                        <div class="campaign-list" id="adsetList">
                            <div class="empty-state">Select a campaign</div>
                        </div>
                    </div>
                    
                    <div class="campaign-column">
                        <div class="campaign-column-header">
                            Ad (UTM Content)
                            <span class="count" id="adCount">0</span>
                        </div>
                        <div class="campaign-list" id="adList">
                            <div class="empty-state">Select an adset</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Global data
        let campaignsData = {};
        let selectedCampaign = null;
        let selectedAdset = null;
        
        // XSS Prevention helpers
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }
        
        // Tab switching
        document.querySelectorAll('.tab, .sidebar-nav a').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const tabName = this.getAttribute('data-tab');
                
                // Update active states
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                document.querySelector(`.tab[data-tab="${tabName}"]`).classList.add('active');
                document.querySelector(`.sidebar-nav a[data-tab="${tabName}"]`).classList.add('active');
                document.getElementById(`tab-${tabName}`).classList.add('active');
                
                // Load data for tab
                if (tabName === 'customers') loadCustomers();
                if (tabName === 'analytics') loadAnalytics();
                if (tabName === 'campaigns') loadCampaigns();
            });
        });
        
        // Apply filters
        function applyFilters() {
            const activeTab = document.querySelector('.tab.active').getAttribute('data-tab');
            if (activeTab === 'customers') loadCustomers();
            if (activeTab === 'analytics') loadAnalytics();
            if (activeTab === 'campaigns') loadCampaigns();
        }
        
        // Get filter params
        function getFilterParams() {
            const params = new URLSearchParams();
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const search = document.getElementById('searchInput').value;
            
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (search) params.append('search', search);
            
            return params.toString();
        }
        
        // Load customers
        function loadCustomers() {
            const params = getFilterParams();
            
            fetch(`?action=get_customers&${params}`)
                .then(res => res.json())
                .then(data => {
                    const customers = data.customers || [];
                    document.getElementById('totalCustomers').textContent = customers.length;
                    document.getElementById('statTotalClicks').textContent = customers.length;
                    
                    // Today's clicks
                    const today = new Date().toISOString().split('T')[0];
                    const todayClicks = customers.filter(c => c.timestamp.startsWith(today)).length;
                    document.getElementById('statTodayClicks').textContent = todayClicks;
                    
                    // Top package
                    const packages = {};
                    customers.forEach(c => {
                        packages[c.package] = (packages[c.package] || 0) + 1;
                    });
                    const topPackage = Object.entries(packages).sort((a, b) => b[1] - a[1])[0];
                    document.getElementById('statTopPackage').textContent = topPackage ? topPackage[0] : '-';
                    
                    // Top country
                    const countries = {};
                    customers.forEach(c => {
                        if (c.country) countries[c.country] = (countries[c.country] || 0) + 1;
                    });
                    const topCountry = Object.entries(countries).sort((a, b) => b[1] - a[1])[0];
                    document.getElementById('statTopCountry').textContent = topCountry ? topCountry[0] : '-';
                    
                    // Render table
                    const tbody = document.getElementById('customersTable');
                    if (customers.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="10" class="empty-state"><div class="icon">&#128203;</div>No data found</td></tr>';
                        return;
                    }
                    
                    tbody.innerHTML = customers.map(c => {
                        var isFU = c.status === 'FOLLOWED_UP';
                        var badgeCls = isFU ? 'badge-followed' : 'badge-click';
                        var safeTs = c.timestamp.replace(/'/g, "\\'");
                        var escapedBiz = (c.business || '-').replace(/"/g, '&quot;');
                        var escapedEmail = (c.email || '-').replace(/"/g, '&quot;');
                        var escapedPkg = (c.package || '-').replace(/"/g, '&quot;');
                        var escapedReviews = (c.reviews_qty || '-').toString().replace(/"/g, '&quot;');
                        var escapedWa = (c.whatsapp || '').replace(/"/g, '&quot;');
                        return `
                        <tr>
                            <td>${c.timestamp}</td>
                            <td><span class="badge-package badge-${c.package.toLowerCase()}">${c.package}</span></td>
                            <td>${c.reviews_qty}</td>
                            <td class="utm-cell" title="${c.utm_source}">${c.utm_source || '-'}</td>
                            <td class="utm-cell" title="${c.utm_medium}">${c.utm_medium || '-'}</td>
                            <td class="utm-cell" title="${c.utm_campaign}">${c.utm_campaign || '-'}</td>
                            <td class="utm-cell" title="${c.utm_term}">${c.utm_term || '-'}</td>
                            <td>${c.country || '-'}</td>
                            <td><span class="badge-status ${badgeCls}">${c.status}</span></td>
                            <td>
                                <a href="#" class="action-btn followup" onclick="openFollowupWA(this); return false;" data-biz="${escapedBiz}" data-email="${escapedEmail}" data-pkg="${escapedPkg}" data-reviews="${escapedReviews}" data-wa="${escapedWa}"><span class="text">Follow Up</span></a>
                                <button class="action-btn delete" onclick="deleteCustomer('${safeTs}')">Delete</button>
                            </td>
                        </tr>`;
                    }).join('');
                })
                .catch(err => {
                    console.error('Error loading customers:', err);
                    document.getElementById('customersTable').innerHTML = '<tr><td colspan="10" class="empty-state">Error loading data</td></tr>';
                });
        }
        
        // Load analytics
        function loadAnalytics() {
            const params = getFilterParams();
            
            fetch(`?action=get_analytics&${params}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('statPageViews').textContent = data.page_views || 0;
                    document.getElementById('statUniqueVisitors').textContent = data.unique_visitors || 0;
                    document.getElementById('statDesktop').textContent = data.devices?.Desktop || 0;
                    document.getElementById('statMobile').textContent = data.devices?.Mobile || 0;
                    
                    // Scroll depths
                    const maxScroll = Math.max(...Object.values(data.scroll_depths || {}), 1);
                    ['25', '50', '75', '100'].forEach(depth => {
                        const count = data.scroll_depths?.[depth] || 0;
                        document.getElementById(`scroll${depth}`).textContent = count;
                        document.getElementById(`scrollBar${depth}`).style.width = `${(count / maxScroll) * 100}%`;
                    });
                    
                    // Events table
                    const events = data.events || {};
                    const eventsHtml = Object.entries(events)
                        .sort((a, b) => b[1] - a[1])
                        .map(([event, count]) => `
                            <tr>
                                <td>${event}</td>
                                <td>${count}</td>
                            </tr>
                        `).join('');
                    
                    document.getElementById('eventsTable').innerHTML = eventsHtml || '<tr><td colspan="2">No events recorded</td></tr>';
                })
                .catch(err => {
                    console.error('Error loading analytics:', err);
                });
        }
        
        // Load campaigns
        function loadCampaigns() {
            const params = getFilterParams();
            
            fetch(`?action=get_campaigns&${params}`)
                .then(res => res.json())
                .then(data => {
                    campaignsData = data.campaigns || {};
                    const campaignNames = Object.keys(campaignsData);
                    
                    document.getElementById('campaignCount').textContent = campaignNames.length;
                    
                    if (campaignNames.length === 0) {
                        document.getElementById('campaignList').innerHTML = '<div class="empty-state"><div class="icon">&#128203;</div>No campaigns found</div>';
                        return;
                    }
                    
                    document.getElementById('campaignList').innerHTML = campaignNames.map((name, idx) => `
                        <div class="campaign-item" data-campaign-idx="${idx}">
                            <span class="name" title="${escapeHtml(name)}">${escapeHtml(name)}</span>
                            <span class="clicks">${campaignsData[name].clicks}</span>
                        </div>
                    `).join('');
                    
                    // Bind click events for campaigns
                    document.querySelectorAll('#campaignList .campaign-item[data-campaign-idx]').forEach(el => {
                        el.addEventListener('click', function() {
                            const idx = parseInt(this.getAttribute('data-campaign-idx'));
                            selectCampaign(campaignNames[idx]);
                        });
                    });
                    
                    // Auto-select first campaign
                    selectCampaign(campaignNames[0]);
                })
                .catch(err => {
                    console.error('Error loading campaigns:', err);
                    document.getElementById('campaignList').innerHTML = '<div class="empty-state">Error loading data</div>';
                });
        }
        
        // Select campaign
        function selectCampaign(name) {
            selectedCampaign = name;
            selectedAdset = null;
            
            // Update active state
            document.querySelectorAll('#campaignList .campaign-item').forEach(item => {
                item.classList.toggle('active', item.querySelector('.name').textContent === name);
            });
            
            // Show adsets
            const campaign = campaignsData[name];
            const adsets = campaign?.adsets || {};
            const adsetNames = Object.keys(adsets);
            
            document.getElementById('adsetCount').textContent = adsetNames.length;
            
            if (adsetNames.length === 0) {
                document.getElementById('adsetList').innerHTML = '<div class="empty-state">No adsets</div>';
                document.getElementById('adList').innerHTML = '<div class="empty-state">Select an adset</div>';
                document.getElementById('adCount').textContent = '0';
                return;
            }
            
            document.getElementById('adsetList').innerHTML = adsetNames.map((adsetName, idx) => `
                <div class="campaign-item" data-adset-idx="${idx}">
                    <span class="name" title="${escapeHtml(adsetName)}">${escapeHtml(adsetName)}</span>
                    <span class="clicks">${adsets[adsetName].clicks}</span>
                </div>
            `).join('');
            
            // Bind click events for adsets
            document.querySelectorAll('#adsetList .campaign-item[data-adset-idx]').forEach(el => {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-adset-idx'));
                    selectAdset(name, adsetNames[idx]);
                });
            });
            
            // Auto-select first adset
            selectAdset(name, adsetNames[0]);
        }
        
        // Select adset
        function selectAdset(campaignName, adsetName) {
            selectedAdset = adsetName;
            
            // Update active state
            document.querySelectorAll('#adsetList .campaign-item').forEach(item => {
                item.classList.toggle('active', item.querySelector('.name').textContent === adsetName);
            });
            
            // Show ads
            const ads = campaignsData[campaignName]?.adsets[adsetName]?.ads || {};
            const adNames = Object.keys(ads);
            
            document.getElementById('adCount').textContent = adNames.length;
            
            if (adNames.length === 0) {
                document.getElementById('adList').innerHTML = '<div class="empty-state">No ads</div>';
                return;
            }
            
            document.getElementById('adList').innerHTML = adNames.map(adName => `
                <div class="campaign-item">
                    <span class="name" title="${escapeHtml(adName)}">${escapeHtml(adName)}</span>
                    <span class="clicks">${ads[adName]}</span>
                </div>
            `).join('');
        }
        
        // Delete customer
        function deleteCustomer(timestamp) {
            if (!confirm('Are you sure you want to delete this entry?')) return;
            
            fetch(`?action=delete_customer&timestamp=${encodeURIComponent(timestamp)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadCustomers();
                    } else {
                        alert('Error deleting entry');
                    }
                });
        }
        
        // Open WhatsApp follow-up
        function openFollowupWA(el) {
            var biz = el.getAttribute('data-biz') || '-';
            var email = el.getAttribute('data-email') || '-';
            var pkg = el.getAttribute('data-pkg') || '-';
            var reviews = el.getAttribute('data-reviews') || '-';
            var wa = el.getAttribute('data-wa') || '';
            var phone = wa.replace(/[^0-9+]/g, '').replace(/^\+/, '');
            if (!phone) { alert('No WhatsApp number for this customer.'); return; }
            var msg = 'Hi ' + biz + ',\n\n'
                + 'Thank you for your interest in our GGL Rvws service!\n\n'
                + 'We noticed you selected:\n'
                + '- Package: ' + pkg + '\n'
                + '- Reviews: ' + reviews + '\n'
                + '- Email: ' + email + '\n\n'
                + 'Would you like to proceed with the payment?';
            var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(msg);
            window.open(url, '_blank');
        }
        
        // Export CSV
        function exportCSV() {
            const params = getFilterParams();
            window.location.href = `?action=export_csv&${params}`;
        }
        
        // Set default date range (last 30 days)
        function setDefaultDateRange() {
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            document.getElementById('dateTo').value = today.toISOString().split('T')[0];
            document.getElementById('dateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            setDefaultDateRange();
            loadCustomers();
            loadAnalytics();
            loadCampaigns();
        });
    </script>
    <?php endif; ?>
</body>
</html>