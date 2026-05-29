<?php
/**
 * ============================================================================
 * File: /submit/get-orders.php
 * Smart Buzzer Get Orders API - v1.1
 * 
 * v1.1 FIX: Added CSRF token validation for security
 * 
 * Description:
 * API endpoint for fetching orders data (AJAX refresh).
 * 
 * Method: GET
 * Response: { "success": true, "orders": [...] }
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

// CSRF validation (via header or query param)
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_GET['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF validation failed']);
    exit;
}

// Load orders
$ordersFile = __DIR__ . '/data/orders.json';
$orders = [];

if (file_exists($ordersFile)) {
    $jsonData = file_get_contents($ordersFile);
    $data = json_decode($jsonData, true);
    $orders = $data['orders'] ?? [];
}

// Sort by timestamp (newest first)
usort($orders, function($a, $b) {
    $timeA = !empty($a['timestamp']) ? strtotime($a['timestamp']) : 0;
    $timeB = !empty($b['timestamp']) ? strtotime($b['timestamp']) : 0;
    return $timeB - $timeA;
});

echo json_encode([
    'success' => true,
    'orders' => $orders,
    'total' => count($orders)
]);