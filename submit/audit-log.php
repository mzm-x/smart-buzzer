<?php
/**
 * ============================================================================
 * File: /submit/audit-log.php
 * Smart Buzzer Free Marketing Audit — NDA Agreement Log Endpoint (v1.1)
 *
 * v1.1 (2026-05-26): Added attempt logging to data/audit_attempts.log (JSON
 * Lines) so every POST is observable from the dashboard — success and failure.
 * Lenient CSRF: missing/expired token is logged as a soft warning, not 403,
 * so loyal-client agreements never get lost to session timeouts.
 *
 * Purpose: Record each loyal-client agreement to the Free Marketing Audit NDA.
 * Triggered when a user clicks "I AGREE — OPEN WHATSAPP" inside the bonus
 * audit modal on the Thank You screens (Reviews flow + Social Media flow).
 *
 * Storage:
 *   - data/audit_requests.json   (successful agreements, JSON array)
 *   - data/audit_attempts.log    (every POST attempt, JSON Lines)
 * ============================================================================
 */

session_start();

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$dataDir = defined('DATA_DIR') ? DATA_DIR : (__DIR__ . '/data');
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}
$attemptsLog = $dataDir . '/audit_attempts.log';

function sb_audit_record_attempt($status, $reason, $payload = [], $extras = []) {
    global $attemptsLog;
    $line = [
        'ts'      => gmdate('c'),
        'status'  => $status, // success | csrf_warn | bad_json | method | not_agreed | write_fail
        'reason'  => $reason,
        'ip'      => function_exists('getClientIp') ? getClientIp() : ($_SERVER['REMOTE_ADDR'] ?? ''),
        'ua'      => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200),
        'referer' => substr($_SERVER['HTTP_REFERER'] ?? '', 0, 200),
        'payload' => [
            'orderId'      => isset($payload['orderId'])      ? substr((string)$payload['orderId'], 0, 60)      : '',
            'businessName' => isset($payload['businessName']) ? substr((string)$payload['businessName'], 0, 200) : '',
            'email'        => isset($payload['email'])        ? substr((string)$payload['email'], 0, 120)        : '',
            'whatsapp'     => isset($payload['whatsapp'])     ? substr((string)$payload['whatsapp'], 0, 40)      : '',
            'orderType'    => isset($payload['orderType'])    ? substr((string)$payload['orderType'], 0, 30)     : '',
        ],
        'extras'  => $extras,
    ];
    @file_put_contents($attemptsLog, json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
}

// ----------------------------------------------------------------------------
// Method guard
// ----------------------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    sb_audit_record_attempt('method', 'Method not allowed: ' . ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed', 'error_code' => 'METHOD']);
    exit;
}

// ----------------------------------------------------------------------------
// Parse JSON body
// ----------------------------------------------------------------------------
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    sb_audit_record_attempt('bad_json', 'Invalid JSON payload', [], ['raw_len' => strlen((string)$raw)]);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload', 'error_code' => 'BAD_JSON']);
    exit;
}

// ----------------------------------------------------------------------------
// Lenient CSRF: warn-only (we don't want to lose an agreement to a stale token)
// ----------------------------------------------------------------------------
$csrfToken = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
$csrfStatus = 'ok';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($csrfToken === null || $csrfToken === '') {
    $csrfStatus = 'missing';
} elseif (!hash_equals($_SESSION['csrf_token'], (string)$csrfToken)) {
    $csrfStatus = 'mismatch';
}

if ($csrfStatus !== 'ok') {
    sb_audit_record_attempt('csrf_warn', 'CSRF ' . $csrfStatus, $data, ['csrf_status' => $csrfStatus]);
    if (function_exists('logActivity')) {
        logActivity('Audit-log CSRF ' . $csrfStatus . ' (proceeding) from IP: ' . (function_exists('getClientIp') ? getClientIp() : ''), 'warning');
    }
    // Do NOT exit — proceed to log the agreement anyway (loyal client UX > strict CSRF here).
}

// ----------------------------------------------------------------------------
// Sanitize + extract
// ----------------------------------------------------------------------------
function sb_clean($v, $max = 500) {
    $v = is_string($v) ? trim($v) : '';
    $v = str_replace(["\r", "\n", "\t"], ' ', $v);
    if (strlen($v) > $max) { $v = substr($v, 0, $max); }
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$entry = [
    'timestamp'    => gmdate('c'),
    'orderId'      => sb_clean($data['orderId']      ?? '', 60),
    'businessName' => sb_clean($data['businessName'] ?? '', 200),
    'email'        => filter_var(trim((string)($data['email'] ?? '')), FILTER_SANITIZE_EMAIL) ?: '',
    'whatsapp'     => sb_clean($data['whatsapp']     ?? '', 40),
    'orderType'    => sb_clean($data['orderType']    ?? 'unknown', 30),
    'agreed'       => !empty($data['agreed']),
    'ndaVersion'   => sb_clean($data['ndaVersion']   ?? '1.0', 10),
    'ip'           => function_exists('getClientIp') ? getClientIp() : ($_SERVER['REMOTE_ADDR'] ?? ''),
    'userAgent'    => sb_clean($_SERVER['HTTP_USER_AGENT'] ?? '', 300),
    'referer'      => sb_clean($_SERVER['HTTP_REFERER'] ?? '', 300),
    'csrfStatus'   => $csrfStatus,
];

if (!$entry['agreed']) {
    sb_audit_record_attempt('not_agreed', 'Agreement flag missing', $data);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Agreement flag missing', 'error_code' => 'NOT_AGREED']);
    exit;
}

// ----------------------------------------------------------------------------
// Append to data/audit_requests.json with file lock
// ----------------------------------------------------------------------------
$logFile = $dataDir . '/audit_requests.json';

if (!file_exists($logFile)) {
    @file_put_contents($logFile, "[]\n");
    @chmod($logFile, 0644);
}

$fp = @fopen($logFile, 'c+');
if (!$fp) {
    sb_audit_record_attempt('write_fail', 'fopen failed', $data, ['logFile' => $logFile]);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot open log file', 'error_code' => 'OPEN_FAIL']);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    sb_audit_record_attempt('write_fail', 'flock failed', $data);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot lock log file', 'error_code' => 'LOCK_FAIL']);
    exit;
}

$contents = stream_get_contents($fp);
$arr = json_decode($contents, true);
if (!is_array($arr)) { $arr = []; }

$arr[] = $entry;

ftruncate($fp, 0);
rewind($fp);
$writeOk = fwrite($fp, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if ($writeOk === false) {
    sb_audit_record_attempt('write_fail', 'fwrite returned false', $data);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to write log', 'error_code' => 'WRITE_FAIL']);
    exit;
}

sb_audit_record_attempt('success', 'Logged', $data, ['csrf_status' => $csrfStatus, 'total_entries' => count($arr)]);

if (function_exists('logActivity')) {
    logActivity('Audit NDA agreement logged: ' . $entry['orderId'] . ' / ' . $entry['businessName'], 'info');
}

echo json_encode([
    'success'    => true,
    'message'    => 'Audit request logged',
    'timestamp'  => $entry['timestamp'],
    'csrfStatus' => $csrfStatus,
]);
