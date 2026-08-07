<?php
// Analytics Event Logger
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['event_type'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing event_type']);
    exit;
}

$timestamp = date('Y-m-d H:i:s');
$eventType = isset($data['event_type']) ? htmlspecialchars($data['event_type'], ENT_QUOTES, 'UTF-8') : '';  // BUG FIX: Sanitize input
$pageUrl = isset($data['page_url']) ? htmlspecialchars($data['page_url'], ENT_QUOTES, 'UTF-8') : '-';  // BUG FIX: Sanitize input
$eventData = isset($data['data']) ? $data['data'] : '{}';
// BUG FIX: Sanitize eventData - strip tabs/newlines to prevent TSV corruption
if (is_string($eventData)) {
    $eventData = str_replace(["\t", "\n", "\r"], ['', '', ''], $eventData);
}
$sessionId = isset($data['session_id']) ? htmlspecialchars($data['session_id'], ENT_QUOTES, 'UTF-8') : '-';  // BUG FIX: Sanitize input

// Get device type from User-Agent
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$device = 'Desktop';
if (preg_match('/mobile|android|iphone/i', $userAgent)) {
    $device = 'Mobile';
} elseif (preg_match('/tablet|ipad/i', $userAgent)) {
    $device = 'Tablet';
}

// Get UTM parameters from page_url (sent from frontend)
$campaign = 'direct';
$source = 'direct';
$medium = 'none';
$utmContent = '-';
$placement = '-';
$utmTerm = '-';

if (isset($data['page_url'])) {
    $parsed = parse_url($data['page_url']);
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $params);
        $campaign = $params['utm_campaign'] ?? 'direct';
        $source = $params['utm_source'] ?? 'direct';
        $medium = $params['utm_medium'] ?? 'none';
        $utmContent = $params['utm_content'] ?? '-';
        $placement = isset($params['utm_placement']) ? trim($params['utm_placement']) : ($params['placement'] ?? '-');
        $utmTerm = isset($params['utm_term']) ? trim($params['utm_term']) : '-';
    }
}

// Get IP address - BUG FIX: Validate IP format to prevent spoofing
$ip = $_SERVER['REMOTE_ADDR'];
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $clientIp = $_SERVER['HTTP_CLIENT_IP'];
    // Validate IP format before using
    if (filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $ip = $clientIp;
    }
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $forwardedIp = trim($ipList[0]);
    // Validate IP format before using
    if (filter_var($forwardedIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $ip = $forwardedIp;
    }
}

// Get country from IP
$country = '-';
try {
    $geoData = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,countryCode", false, stream_context_create([
        'http' => ['timeout' => 3]  // BUG FIX: Increased from 2 to 3 seconds
    ]));
    if ($geoData !== false) {
        $geo = json_decode($geoData, true);
        if (isset($geo['status']) && $geo['status'] === 'success' && isset($geo['countryCode'])) {
            $country = $geo['countryCode'];
        }
    }
} catch (Exception $e) {
    // Keep country as -
}

// Format: 14 columns
// timestamp | event_type | page_url | data | device | session_id | campaign | source | medium | utm_content | placement | utm_term | ip | country
$logLine = implode("\t", [
    $timestamp,
    $eventType,
    $pageUrl,
    $eventData,
    $device,
    $sessionId,
    $campaign,
    $source,
    $medium,
    $utmContent,
    $placement,
    $utmTerm,
    $ip,
    $country
]) . "\n";

// Write to log file
$logFile = __DIR__ . '/page_analytics.log';
$success = @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

if ($success !== false) {
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to write log']);
}
exit;
?>