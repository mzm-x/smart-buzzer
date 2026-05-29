<?php
/**
 * ============================================================================
 * File: /submit/delete-orders.php
 * Smart Buzzer Delete Orders API - v1.1
 * 
 * v1.1 FIX: Added CSRF token validation for security
 * 
 * Description:
 * API endpoint for bulk deleting orders from JSON database.
 * Called by manage.php when user selects orders and clicks Delete.
 * 
 * Method: POST
 * Content-Type: application/json
 * Body: { "orderIds": ["SB-20241216-001", "SB-20241216-002", ...] }
 * 
 * Response:
 * - Success: { "success": true, "message": "Deleted X order(s)", "deletedCount": X }
 * - Error: { "success": false, "message": "Error description" }
 * 
 * Security:
 * - Session authentication required
 * - File locking for concurrent writes
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

$orderIds = $input['orderIds'] ?? [];

if (empty($orderIds) || !is_array($orderIds)) {
    echo json_encode(['success' => false, 'message' => 'No orders selected']);
    exit;
}

$ordersFile = __DIR__ . '/data/orders.json';

if (!file_exists($ordersFile)) {
    echo json_encode(['success' => false, 'message' => 'Orders file not found']);
    exit;
}

$fp = fopen($ordersFile, 'r+');
if (!$fp) {
    echo json_encode(['success' => false, 'message' => 'Cannot open orders file']);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    echo json_encode(['success' => false, 'message' => 'Cannot lock file']);
    exit;
}

$content = stream_get_contents($fp);
$data = json_decode($content, true);

$originalCount = count($data['orders'] ?? []);
$data['orders'] = array_filter($data['orders'] ?? [], function($order) use ($orderIds) {
    return !in_array($order['orderId'], $orderIds);
});
$data['orders'] = array_values($data['orders']); // Re-index

$deletedCount = $originalCount - count($data['orders']);

ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode([
    'success' => true,
    'message' => "Deleted {$deletedCount} order(s)",
    'deletedCount' => $deletedCount
]);