<?php
/**
 * Smart Buzzer - Links Analytics Backend
 * Receives page_view and link_click events from /links/index.php
 *
 * Log files:
 * - page_views.log (10 cols): Timestamp|Referrer|UTM_Source|UTM_Medium|UTM_Campaign|UTM_Content|Device|IP|Country|Session
 * - link_clicks.log (12 cols): Timestamp|Link_Name|Link_URL|Referrer|UTM_Source|UTM_Medium|UTM_Campaign|UTM_Content|Device|IP|Country|Session
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST only']);
    exit;
}

// Read JSON body
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['event_type'])) {
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$eventType = $input['event_type']; // page_view or link_click

// Sanitize function - remove tabs/newlines
function sanitize($val) {
    if (!$val) return '-';
    return str_replace(["\t", "\n", "\r"], '', trim($val));
}

// Detect device
function detectDevice() {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (preg_match('/tablet|ipad|playbook|silk/i', $ua)) return 'Tablet';
    if (preg_match('/mobile|android|iphone|ipod|opera mini|iemobile|wpdesktop/i', $ua)) return 'Mobile';
    return 'Desktop';
}

// Get real IP
function getRealIP() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = explode(',', $_SERVER[$h])[0];
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

// Get country via ip-api.com (with timeout)
function getCountry($ip) {
    if ($ip === '127.0.0.1' || $ip === '0.0.0.0') return '-';
    $ctx = stream_context_create(['http' => ['timeout' => 3]]);
    $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, $ctx);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['countryCode'])) return $data['countryCode'];
    }
    return '-';
}

$timestamp = date('Y-m-d H:i:s');
$device = detectDevice();
$ip = getRealIP();
$country = getCountry($ip);

// Common fields
$referrer = sanitize($input['referrer'] ?? '-');
$utmSource = sanitize($input['utm_source'] ?? 'direct');
$utmMedium = sanitize($input['utm_medium'] ?? 'none');
$utmCampaign = sanitize($input['utm_campaign'] ?? 'direct');
$utmContent = sanitize($input['utm_content'] ?? '-');
$session = sanitize($input['session_id'] ?? '-');

if ($eventType === 'page_view') {
    // page_views.log: 10 columns
    $line = implode("\t", [
        $timestamp,
        $referrer,
        $utmSource,
        $utmMedium,
        $utmCampaign,
        $utmContent,
        $device,
        $ip,
        $country,
        $session
    ]);
    file_put_contents(__DIR__ . '/page_views.log', $line . "\n", FILE_APPEND | LOCK_EX);
    echo json_encode(['success' => true]);

} elseif ($eventType === 'link_click') {
    // link_clicks.log: 12 columns
    $linkName = sanitize($input['link_name'] ?? '-');
    $linkUrl = sanitize($input['link_url'] ?? '-');

    $line = implode("\t", [
        $timestamp,
        $linkName,
        $linkUrl,
        $referrer,
        $utmSource,
        $utmMedium,
        $utmCampaign,
        $utmContent,
        $device,
        $ip,
        $country,
        $session
    ]);
    file_put_contents(__DIR__ . '/link_clicks.log', $line . "\n", FILE_APPEND | LOCK_EX);
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['error' => 'Unknown event_type']);
}
