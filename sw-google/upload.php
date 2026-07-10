<?php
/**
 * /sw-google/upload.php — Proof-of-payment receiver
 *
 * Receives the proof file + payer details from payment.php, validates it,
 * stores it under /proofs/ (HTTP-denied via .htaccess), and appends an order
 * record to proofs_log.tsv for the team to verify.
 *
 * This does NOT write customer_data.log — that FORM_SUBMIT row is written on
 * the index.php order submit (via log.php). This file only records the payment
 * proof tied to that order.
 *
 * Returns JSON: {status: 'success'|'error', message?, file?}
 */
header('Content-Type: application/json');
session_start();

function fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Invalid request method.', 405);
}

// ---- CSRF ----
$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    fail('Session expired. Please refresh the page and try again.', 403);
}

// ---- File presence ----
if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
    $errMap = [
        UPLOAD_ERR_INI_SIZE => 'File is too large (server limit).',
        UPLOAD_ERR_FORM_SIZE => 'File is too large.',
        UPLOAD_ERR_PARTIAL => 'Upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
    ];
    $e = $_FILES['proof']['error'] ?? UPLOAD_ERR_NO_FILE;
    fail($errMap[$e] ?? 'Upload failed. Please try again.');
}

$file = $_FILES['proof'];

// ---- Size (8 MB) ----
$MAX_BYTES = 8 * 1024 * 1024;
if ($file['size'] > $MAX_BYTES) {
    fail('File is too large. Max size is 8 MB.');
}
if ($file['size'] <= 0) {
    fail('The uploaded file is empty.');
}

// ---- MIME + extension whitelist ----
$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf',
];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowed[$mime])) {
    fail('Unsupported file type. Please upload a JPG, PNG, WEBP or PDF.');
}
$ext = $allowed[$mime];

// ---- Ensure proofs dir + .htaccess ----
$proofDir = __DIR__ . '/proofs';
if (!is_dir($proofDir)) {
    @mkdir($proofDir, 0755, true);
}
$htaccess = $proofDir . '/.htaccess';
if (!file_exists($htaccess)) {
    @file_put_contents($htaccess, "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
}

// ---- Sanitize meta fields ----
function clean($v, $max = 120) {
    $v = is_string($v) ? $v : '';
    $v = str_replace(["\t", "\n", "\r"], ' ', trim($v));
    if (strlen($v) > $max) $v = substr($v, 0, $max);
    return $v;
}
$pkg      = clean($_POST['pkg'] ?? '', 40);
$business = clean($_POST['business'] ?? '', 160);
$payer    = clean($_POST['payer_name'] ?? '', 120);
$email    = clean($_POST['payer_email'] ?? '', 160);
$contact  = clean($_POST['payer_contact'] ?? '', 60);

$validPkgs = ['starter', 'growth', 'performance'];
if (!in_array($pkg, $validPkgs, true)) { $pkg = 'unknown'; }

// ---- Build safe unique filename ----
$slugBiz = preg_replace('/[^A-Za-z0-9]+/', '-', $business);
$slugBiz = trim(substr($slugBiz, 0, 30), '-');
$slugBiz = $slugBiz !== '' ? $slugBiz : 'order';
$rand = bin2hex(random_bytes(4));
$fname = date('Ymd-His') . '_' . $pkg . '_' . $slugBiz . '_' . $rand . '.' . $ext;
$dest = $proofDir . '/' . $fname;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    fail('Could not save your file. Please try again or send it via WhatsApp.', 500);
}
@chmod($dest, 0644);

// ---- IP (validated) ----
$ip = $_SERVER['REMOTE_ADDR'] ?? '-';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $first = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    if (filter_var($first, FILTER_VALIDATE_IP)) { $ip = $first; }
}

// ---- Append order record (TSV) ----
// timestamp | pkg | business | payer_name | email | contact | filename | ip
$row = implode("\t", [
    date('Y-m-d H:i:s'), $pkg, $business, $payer, $email, $contact, $fname, $ip
]) . "\n";
@file_put_contents(__DIR__ . '/proofs_log.tsv', $row, FILE_APPEND | LOCK_EX);

echo json_encode(['status' => 'success', 'file' => $fname]);
exit;
