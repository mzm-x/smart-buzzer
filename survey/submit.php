<?php
// survey/submit.php v1.0 — Save to JSON (no database)
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Invalid method']));
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body || empty($body['answers'])) {
    die(json_encode(['success' => false, 'error' => 'Invalid data']));
}

$answers = $body['answers'];

// Validate required fields
$required = ['channels_used', 'monthly_spend', 'main_goals', 'lead_sources', 'challenges', 'expected_results'];
foreach ($required as $k) {
    if (empty($answers[$k])) {
        die(json_encode(['success' => false, 'error' => 'Incomplete survey']));
    }
}

// Paths
$data_dir  = __DIR__ . '/data';
$data_file = $data_dir . '/responses.json';

// Create data dir + protect from direct access
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
    file_put_contents($data_dir . '/.htaccess', "Deny from all\nOptions -Indexes\n");
}

// Read existing responses (with file lock)
$fp = fopen($data_file, 'c+');
if (!$fp) {
    die(json_encode(['success' => false, 'error' => 'Could not write data. Check folder permissions.']));
}

flock($fp, LOCK_EX);

$size     = filesize($data_file);
$existing = [];
if ($size > 2) {
    rewind($fp);
    $content  = fread($fp, $size);
    $existing = json_decode($content, true) ?: [];
}

// Build entry
$callRequested = !empty($body['call_requested']) && $body['call_requested'] === true;
$entry = [
    'id'                  => count($existing) + 1,
    'submitted_at'        => date('Y-m-d H:i:s'),
    'email'               => substr(strip_tags((string)($answers['email'] ?? '')), 0, 255),
    'name'                => substr(strip_tags((string)($answers['name']  ?? '')), 0, 200),
    'time_spent_seconds'  => max(0, (int)($body['time_spent_seconds'] ?? 0)),
    'ip_address'          => substr((string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
    'user_agent'          => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200),
    'call_requested'      => $callRequested,
    'call_phone'          => substr(strip_tags((string)($body['call_phone'] ?? '')), 0, 50),
    'call_preferred_time' => substr(strip_tags((string)($body['call_preferred_time'] ?? '')), 0, 50),
    'call_status'         => $callRequested ? 'pending' : 'na',
    'answers'             => [
        'email'            => substr(strip_tags((string)($answers['email'] ?? '')), 0, 255),
        'name'             => substr(strip_tags((string)($answers['name']  ?? '')), 0, 200),
        'phone'            => substr(strip_tags((string)($answers['phone'] ?? '')), 0, 50),
        'channels_used'    => (array)($answers['channels_used']    ?? []),
        'channels_other'   => substr(strip_tags((string)($answers['channels_other']  ?? '')), 0, 300),
        'fb_ig_detail'     => (array)($answers['fb_ig_detail']     ?? []),
        'fb_ig_other'      => substr(strip_tags((string)($answers['fb_ig_other']     ?? '')), 0, 300),
        'monthly_spend'    => (array)($answers['monthly_spend']    ?? []),
        'main_goals'       => (array)($answers['main_goals']       ?? []),
        'lead_sources'     => (array)($answers['lead_sources']     ?? []),
        'challenges'       => (array)($answers['challenges']       ?? []),
        'challenges_other' => substr(strip_tags((string)($answers['challenges_other']?? '')), 0, 300),
        'expected_results' => (array)($answers['expected_results'] ?? []),
    ],
];

$existing[] = $entry;

// Write back
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
fflush($fp);

flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['success' => true, 'message' => '10 bonus reviews added!']);
