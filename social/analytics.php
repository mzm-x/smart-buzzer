<?php
/**
 * Smart Buzzer Analytics Backend - Social Landing Page
 * Version: 1.0
 * 
 * Handles: PAGE_VIEW, SCROLL_DEPTH, ORDER_CLICK, MODAL events
 * Logs to: page_analytics.log (13 columns), customer_data.log (17 columns)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'POST required']);
    exit;
}

// Get POST data
$eventType = isset($_POST['event_type']) ? trim($_POST['event_type']) : '';
$pageUrl = isset($_POST['page_url']) ? trim($_POST['page_url']) : '';
$data = isset($_POST['data']) ? trim($_POST['data']) : '{}';
$sessionId = isset($_POST['session_id']) ? trim($_POST['session_id']) : '';

if (empty($eventType)) {
    echo json_encode(['status' => 'error', 'message' => 'event_type required']);
    exit;
}

// Sanitize inputs
$eventType = preg_replace('/[^A-Z0-9_]/', '', strtoupper($eventType));
$pageUrl = filter_var($pageUrl, FILTER_SANITIZE_URL);
$sessionId = preg_replace('/[^a-zA-Z0-9_]/', '', $sessionId);

// Get real IP
function getRealIP() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = explode(',', $_SERVER[$header])[0];
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

$ip = getRealIP();

// Detect device
function detectDevice() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $ua)) {
        return 'Mobile';
    } elseif (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
        return 'Tablet';
    }
    return 'Desktop';
}

$device = detectDevice();

// Get country from IP (with caching)
function getCountry($ip) {
    $cacheFile = sys_get_temp_dir() . '/geo_' . md5($ip) . '.txt';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
        return file_get_contents($cacheFile);
    }
    
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, $ctx);
    
    if ($response) {
        $geo = json_decode($response, true);
        $country = $geo['countryCode'] ?? 'XX';
        file_put_contents($cacheFile, $country);
        return $country;
    }
    return 'XX';
}

$country = getCountry($ip);

// Block Indonesia traffic (admin testing)
if ($country === 'ID') {
    echo json_encode(['status' => 'success']);
    exit;
}

// Parse UTM from page URL
function parseUTM($url) {
    $params = [];
    $query = parse_url($url, PHP_URL_QUERY);
    if ($query) {
        parse_str($query, $params);
    }
    return [
        'source' => $params['utm_source'] ?? '-',
        'medium' => $params['utm_medium'] ?? '-',
        'campaign' => $params['utm_campaign'] ?? '-',
        'content' => $params['utm_content'] ?? '-',
        'placement' => $params['placement'] ?? '-'
    ];
}

$utm = parseUTM($pageUrl);
$timestamp = date('Y-m-d H:i:s');

// Log to page_analytics.log (13 columns)
$analyticsLine = implode("\t", [
    $timestamp,           // 0
    $eventType,           // 1
    $pageUrl,             // 2
    $data,                // 3
    $device,              // 4
    $sessionId,           // 5
    $utm['campaign'],     // 6
    $utm['source'],       // 7
    $utm['medium'],       // 8
    $utm['content'],      // 9
    $utm['placement'],    // 10
    $ip,                  // 11
    $country              // 12
]) . "\n";

file_put_contents(__DIR__ . '/page_analytics.log', $analyticsLine, FILE_APPEND | LOCK_EX);

// If ORDER_CLICK, also log to customer_data.log (17 columns)
if ($eventType === 'ORDER_CLICK') {
    $eventData = json_decode($data, true) ?: [];
    
    // Extract customer info from event data
    $platform = $eventData['platform'] ?? '-';
    $category = $eventData['category'] ?? '-';
    $qty = $eventData['qty'] ?? '-';
    $price = $eventData['price'] ?? '-';
    $username = $eventData['username'] ?? '-';
    $postLink = $eventData['post_link'] ?? '-';
    $email = $eventData['email'] ?? '-';
    $whatsapp = $eventData['whatsapp'] ?? '-';
    $package = $eventData['package'] ?? '-';
    
    // Get state/zip from IP (optional enhancement)
    $state = '-';
    $zip = '-';
    
    $customerLine = implode("\t", [
        $timestamp,           // 0  Timestamp
        $username,            // 1  Business/Username
        $platform,            // 2  Location/Platform
        $email,               // 3  Email
        $whatsapp,            // 4  WhatsApp
        $package,             // 5  Package
        $pageUrl,             // 6  Page URL
        $qty,                 // 7  Reviews Qty
        $utm['source'],       // 8  UTM_Source
        $utm['medium'],       // 9  UTM_Medium
        $utm['campaign'],     // 10 UTM_Campaign
        $utm['content'],      // 11 UTM_Content
        $utm['placement'],    // 12 Placement
        $state,               // 13 State
        $zip,                 // 14 Zip
        $country,             // 15 Country
        'FORM_SUBMIT'         // 16 Status
    ]) . "\n";
    
    file_put_contents(__DIR__ . '/customer_data.log', $customerLine, FILE_APPEND | LOCK_EX);
}

echo json_encode(['status' => 'success']);