<?php
/**
 * ============================================================================
 * File: /submit/save-tnc.php
 * Smart Buzzer Save TNC API - v2.1 (Points Structure)
 * 
 * v2.1 FIX: Added proper CSRF token validation
 * 
 * Description:
 * API endpoint for saving Terms & Conditions points.
 * Each point has a title and optional subtitle.
 * 
 * Method: POST
 * Content-Type: application/json
 * Body: { "points": [{"title": "...", "subtitle": "..."}, ...], "csrf_token": "..." }
 * 
 * Response:
 * - Success: { "success": true, "message": "TNC saved" }
 * - Error: { "success": false, "message": "Error description" }
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

ini_set('session.gc_maxlifetime', 7776000);
ini_set('session.cookie_lifetime', 7776000);
session_start();

header('Content-Type: application/json');

// Check auth
if (!isset($_SESSION['am_logged_in']) || $_SESSION['am_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// CSRF validation
$csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF validation failed']);
    exit;
}

$points = $input['points'] ?? [];

// Validate points
if (!is_array($points)) {
    echo json_encode(['success' => false, 'message' => 'Invalid points data']);
    exit;
}

// Sanitize points
$sanitizedPoints = [];
foreach ($points as $point) {
    if (!empty($point['title'])) {
        $sanitizedPoints[] = [
            'title' => trim($point['title']),
            'subtitle' => trim($point['subtitle'] ?? '')
        ];
    }
}

if (empty($sanitizedPoints)) {
    echo json_encode(['success' => false, 'message' => 'At least one point with title is required']);
    exit;
}

// Prepare data
$tncData = [
    'points' => $sanitizedPoints,
    'lastUpdated' => date('Y-m-d H:i:s'),
    'updatedBy' => 'admin'
];

// Save to file
$tncFile = __DIR__ . '/data/tnc.json';
$dataDir = __DIR__ . '/data';

// Ensure data directory exists
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Write with file locking
$fp = fopen($tncFile, 'w');
if (!$fp) {
    echo json_encode(['success' => false, 'message' => 'Cannot open TNC file']);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    echo json_encode(['success' => false, 'message' => 'Cannot lock file']);
    exit;
}

fwrite($fp, json_encode($tncData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode([
    'success' => true,
    'message' => 'TNC saved successfully',
    'pointsCount' => count($sanitizedPoints),
    'lastUpdated' => $tncData['lastUpdated']
]);