<?php
// tp-survey/submit.php v1.0 — Upsert survey response (autosave + final submit)
// 1 record = 1 respondent (keyed by client-generated rid).
// status: in_progress (autosave tiap Continue) -> completed (setelah Q21).
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Invalid method']));
}

$raw  = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body || empty($body['rid']) || !is_string($body['rid'])) {
    die(json_encode(['success' => false, 'error' => 'Invalid data']));
}

$rid = substr(preg_replace('/[^a-zA-Z0-9_-]/', '', $body['rid']), 0, 40);
if ($rid === '') die(json_encode(['success' => false, 'error' => 'Invalid rid']));

// ── sanitize helpers ──────────────────────────────────────
function clean_str($v, $max = 500) {
    return substr(trim(strip_tags((string)$v)), 0, $max);
}
function clean_answers($answers) {
    if (!is_array($answers)) return [];
    $out = [];
    foreach ($answers as $qid => $a) {
        if (!preg_match('/^q([1-9]|1[0-9]|2[0-4])$/', $qid) || !is_array($a)) continue;
        $c = [];
        if (isset($a['value']))  $c['value']  = clean_str($a['value'], 2000);
        if (isset($a['note']))   $c['note']   = clean_str($a['note'], 500);
        if (isset($a['values']) && is_array($a['values'])) {
            // checkbox list OR price map {a,b,c,d}
            $isAssoc = array_keys($a['values']) !== range(0, count($a['values']) - 1);
            if ($isAssoc) {
                $m = [];
                foreach (['a','b','c','d'] as $k) {
                    if (isset($a['values'][$k]) && $a['values'][$k] !== null && $a['values'][$k] !== '') {
                        $m[$k] = (float)$a['values'][$k];
                    }
                }
                $c['values'] = $m;
            } else {
                $c['values'] = array_slice(array_map(fn($x) => clean_str($x, 120), $a['values']), 0, 15);
            }
        }
        if (isset($a['reveals']) && is_array($a['reveals'])) {
            $r = [];
            foreach ($a['reveals'] as $k => $v) { $r[clean_str($k, 60)] = clean_str($v, 300); }
            $c['reveals'] = $r;
        }
        if (isset($a['contact']) && is_array($a['contact'])) {
            $c['contact'] = [
                'name'    => clean_str($a['contact']['name']    ?? '', 150),
                'contact' => clean_str($a['contact']['contact'] ?? '', 200),
            ];
        }
        $out[$qid] = $c;
    }
    return $out;
}

// ── paths + lock ──────────────────────────────────────────
$data_dir  = __DIR__ . '/data';
$data_file = $data_dir . '/responses.json';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
    file_put_contents($data_dir . '/.htaccess', "Deny from all\nOptions -Indexes\n");
}

$fp = fopen($data_file, 'c+');
if (!$fp) die(json_encode(['success' => false, 'error' => 'Cannot write data']));
flock($fp, LOCK_EX);

$size = filesize($data_file);
$existing = [];
if ($size > 2) {
    rewind($fp);
    $existing = json_decode(fread($fp, $size), true) ?: [];
}

// ── build/merge entry ─────────────────────────────────────
$status = ($body['status'] ?? '') === 'completed' ? 'completed' : 'in_progress';
$entry = [
    'rid'                => $rid,
    'seg'                => clean_str($body['seg']     ?? '', 20),
    'type'               => clean_str($body['type']    ?? '', 40),
    'channel'            => clean_str($body['channel'] ?? '', 40),
    'reward'             => clean_str($body['reward']         ?? '', 40),
    'reward_contact'     => clean_str($body['reward_contact'] ?? '', 200),
    'status'             => $status,
    'last_q'             => max(0, min(21, (int)($body['last_q'] ?? 0))),
    'started_at'         => clean_str($body['started_at'] ?? '', 30),
    'time_spent_seconds' => max(0, (int)($body['time_spent_seconds'] ?? 0)),
    'updated_at'         => date('Y-m-d H:i:s'),
    'ip_address'         => substr((string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
    'user_agent'         => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200),
    'answers'            => clean_answers($body['answers'] ?? []),
];

$found = false;
foreach ($existing as $i => $r) {
    if (($r['rid'] ?? '') === $rid) {
        // never downgrade completed -> in_progress (late/dup autosave)
        if (($r['status'] ?? '') === 'completed') $entry['status'] = 'completed';
        $entry['id'] = $r['id'];
        $entry['submitted_at'] = $r['submitted_at'] ?? '';
        $entry['call_status']  = $r['call_status'] ?? '';
        $existing[$i] = array_merge($r, $entry);
        if ($entry['status'] === 'completed' && empty($existing[$i]['submitted_at'])) {
            $existing[$i]['submitted_at'] = date('Y-m-d H:i:s');
            $existing[$i]['call_status']  = tp_call_status($entry['answers']);
        }
        $found = true;
        break;
    }
}
if (!$found) {
    $maxId = 0;
    foreach ($existing as $r) $maxId = max($maxId, (int)($r['id'] ?? 0));
    $entry['id'] = $maxId + 1;
    $entry['created_at'] = date('Y-m-d H:i:s');
    $entry['submitted_at'] = $status === 'completed' ? date('Y-m-d H:i:s') : '';
    $entry['call_status']  = $status === 'completed' ? tp_call_status($entry['answers']) : '';
    $existing[] = $entry;
}

function tp_call_status($answers) {
    return (($answers['q24']['value'] ?? '') === 'Yes') ? 'pending' : 'na';
}

ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['success' => true]);
