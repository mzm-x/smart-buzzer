<?php
/**
 * ============================================================================
 * File: /submit/update-status.php
 * Smart Buzzer Order Status Update Handler - v1.0
 * 
 * Description:
 * AJAX endpoint for updating order status from the dashboard.
 * Supports inline status editing feature.
 * 
 * ✅ v1.0 FEATURES:
 * - AJAX POST request handling
 * - Authentication required
 * - CSRF token validation
 * - Status validation
 * - Atomic file update
 * - Activity logging
 * 
 * Request Method: POST (AJAX)
 * Content-Type: application/json
 * 
 * Request Body:
 * {
 *   "orderId": "SB-20241215-001",
 *   "status": "Processing",
 *   "csrf_token": "..."
 * }
 * 
 * Response Format:
 * {
 *   "success": true/false,
 *   "message": "Status updated successfully"
 * }
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

session_start();

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Load dependencies
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Check authentication
if (!checkAuth()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please login.'
    ]);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Read JSON input
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

// Validate JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request format'
    ]);
    exit;
}

// Validate CSRF token
$csrfToken = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Security validation failed'
    ]);
    exit;
}

// Validate required fields
if (empty($data['orderId']) || empty($data['status'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Order ID and status are required'
    ]);
    exit;
}

$orderId = $data['orderId'];
$newStatus = $data['status'];

// Validate status value
$validStatuses = ['Pending', 'Processing', 'Completed', 'Cancelled', 'On Hold'];
if (!in_array($newStatus, $validStatuses)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status value'
    ]);
    exit;
}

// Validate order ID format
if (!validateOrderId($orderId)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Order ID format'
    ]);
    exit;
}

// Update order
try {
    $result = updateOrderById($orderId, ['status' => $newStatus]);
    
    if ($result) {
        // Log the update
        logActivity("Status updated: $orderId -> $newStatus by {$_SESSION['am_username']}", 'info');
        
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Order not found or update failed'
        ]);
    }
} catch (Exception $e) {
    logActivity("Status update error: " . $e->getMessage(), 'error');
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating status'
    ]);
}