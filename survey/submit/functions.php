<?php
/**
 * ============================================================================
 * File: /submit/functions.php
 * Smart Buzzer Helper Functions - COMPLETE v1.2
 * 
 * v1.2 FIX: Cleaned UTF-8 characters in comments
 * 
 * Description:
 * Collection of reusable helper functions for the Smart Buzzer Order System.
 * Provides utilities for data manipulation, validation, formatting, and more.
 * 
 * v1.1 FIXES:
 * - Removed duplicate generateOrderId() (now only here, not in process.php)
 * - Fixed currency encoding (UTF-8)
 * - Added atomic file operations for race condition prevention
 * - Improved file locking mechanism
 * - Added transaction-like order saving
 * 
 * v1.0 FEATURES:
 * - Order ID generation and validation
 * - Data sanitization and validation
 * - Date/time formatting helpers
 * - Email validation and formatting
 * - Phone number validation and formatting
 * - JSON file operations (read/write with locking)
 * - Array manipulation utilities
 * - String utilities
 * - URL validation and formatting
 * - File size formatting
 * - Status badge generation
 * - Notification helpers (future use)
 * 
 * Function Categories:
 * 1. Order Management Functions
 * 2. Validation Functions
 * 3. Formatting Functions
 * 4. File Operations
 * 5. Utility Functions
 * 6. Data Processing Functions
 * 
 * Dependencies:
 * - config.php (constants and settings)
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

require_once __DIR__ . '/config.php';

// ============================================================================
// 1. ORDER MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Generate unique Order ID with atomic operation (RACE CONDITION FIX)
 * Format: SB-YYYYMMDD-XXX
 * 
 * Uses file locking to prevent duplicate IDs when multiple users submit simultaneously
 * 
 * @return array ['orderId' => string, 'orderNumber' => int, 'date' => string]
 */
function generateOrderId() {
    $dataDir = defined('DATA_DIR') ? DATA_DIR : __DIR__ . '/data';
    $counterFile = $dataDir . '/order_counter.json';
    
    // Create data directory if not exists
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $date = date('Ymd');
    $orderNumber = 1;
    $maxRetries = 5;
    $retryCount = 0;
    
    // Atomic operation with file locking
    while ($retryCount < $maxRetries) {
        $fp = fopen($counterFile, 'c+');
        
        if (!$fp) {
            $retryCount++;
            usleep(100000); // Wait 100ms
            continue;
        }
        
        // Acquire exclusive lock
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            $retryCount++;
            usleep(100000);
            continue;
        }
        
        // Read current counter
        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 8192);
        }
        
        $counterData = json_decode($content, true);
        if (!$counterData) {
            $counterData = [];
        }
        
        // Get or initialize counter for today
        if (isset($counterData[$date])) {
            $orderNumber = $counterData[$date] + 1;
        } else {
            $orderNumber = 1;
            // Clean up old dates (keep only last 7 days)
            $cutoffDate = date('Ymd', strtotime('-7 days'));
            foreach (array_keys($counterData) as $key) {
                if ($key < $cutoffDate) {
                    unset($counterData[$key]);
                }
            }
        }
        
        // Update counter
        $counterData[$date] = $orderNumber;
        
        // Write back
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($counterData, JSON_PRETTY_PRINT));
        fflush($fp);
        
        // Release lock
        flock($fp, LOCK_UN);
        fclose($fp);
        
        break;
    }
    
    if ($retryCount >= $maxRetries) {
        // Fallback: use timestamp-based unique ID
        $orderNumber = (int)(microtime(true) * 1000) % 1000;
    }
    
    $prefix = defined('ORDER_ID_PREFIX') ? ORDER_ID_PREFIX : 'SB';
    $orderId = sprintf('%s-%s-%03d', $prefix, $date, $orderNumber);
    
    return [
        'orderId' => $orderId,
        'orderNumber' => $orderNumber,
        'date' => $date
    ];
}

/**
 * Validate Order ID format
 * 
 * @param string $orderId Order ID to validate
 * @return bool
 */
function validateOrderId($orderId) {
    $prefix = defined('ORDER_ID_PREFIX') ? ORDER_ID_PREFIX : 'SB';
    $pattern = '/^' . preg_quote($prefix, '/') . '-\d{8}-\d{3}$/';
    return preg_match($pattern, $orderId) === 1;
}

/**
 * Load orders data from JSON with file locking
 * 
 * @return array Orders data
 */
function loadOrdersData() {
    $ordersFile = defined('ORDERS_DATABASE') ? ORDERS_DATABASE : __DIR__ . '/data/orders.json';
    
    if (!file_exists($ordersFile)) {
        return ['orders' => [], 'lastOrderNumber' => []];
    }
    
    $fp = fopen($ordersFile, 'r');
    if (!$fp) {
        return ['orders' => [], 'lastOrderNumber' => []];
    }
    
    // Shared lock for reading
    if (!flock($fp, LOCK_SH)) {
        fclose($fp);
        return ['orders' => [], 'lastOrderNumber' => []];
    }
    
    $content = '';
    while (!feof($fp)) {
        $content .= fread($fp, 8192);
    }
    
    flock($fp, LOCK_UN);
    fclose($fp);
    
    $data = json_decode($content, true);
    
    if (!$data) {
        return ['orders' => [], 'lastOrderNumber' => []];
    }
    
    return $data;
}

/**
 * Save orders data to JSON with atomic write
 * 
 * @param array $data Orders data
 * @return bool Success status
 */
function saveOrdersData($data) {
    $ordersFile = defined('ORDERS_DATABASE') ? ORDERS_DATABASE : __DIR__ . '/data/orders.json';
    return saveJsonFileAtomic($ordersFile, $data);
}

/**
 * Add new order with atomic operation (RACE CONDITION FIX)
 * 
 * @param array $orderData Order data to add
 * @return array ['success' => bool, 'orderId' => string, 'message' => string]
 */
function addOrder($orderData) {
    $ordersFile = defined('ORDERS_DATABASE') ? ORDERS_DATABASE : __DIR__ . '/data/orders.json';
    $dataDir = dirname($ordersFile);
    
    // Create data directory if not exists
    if (!is_dir($dataDir)) {
        if (!mkdir($dataDir, 0755, true)) {
            return [
                'success' => false,
                'orderId' => null,
                'message' => 'Failed to create data directory'
            ];
        }
    }
    
    $maxRetries = 5;
    $retryCount = 0;
    
    while ($retryCount < $maxRetries) {
        // Open file for read/write
        $fp = fopen($ordersFile, 'c+');
        
        if (!$fp) {
            $retryCount++;
            usleep(100000);
            continue;
        }
        
        // Acquire exclusive lock
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            $retryCount++;
            usleep(100000);
            continue;
        }
        
        // Read current data
        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 8192);
        }
        
        $ordersData = json_decode($content, true);
        if (!$ordersData) {
            $ordersData = ['orders' => [], 'lastOrderNumber' => []];
        }
        
        // Generate Order ID
        $orderInfo = generateOrderId();
        $orderData['orderId'] = $orderInfo['orderId'];
        
        // Add timestamps
        $orderData['timestamp'] = date('Y-m-d H:i:s');
        $orderData['lastUpdated'] = date('Y-m-d H:i:s');
        
        // Initialize spreadsheet fields
        $orderData['spreadsheetId'] = null;
        $orderData['spreadsheetUrl'] = null;
        
        // Set default status
        if (!isset($orderData['status'])) {
            $orderData['status'] = 'Pending';
        }
        
        if (!isset($orderData['paymentStatus'])) {
            $orderData['paymentStatus'] = 'Pending';
        }
        
        // Sanitize all data
        $orderData = sanitizeInput($orderData);
        
        // Add to orders array
        $ordersData['orders'][] = $orderData;
        
        // Update last order number
        $ordersData['lastOrderNumber'][$orderInfo['date']] = $orderInfo['orderNumber'];
        
        // Write back
        ftruncate($fp, 0);
        rewind($fp);
        $jsonOutput = json_encode($ordersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        fwrite($fp, $jsonOutput);
        fflush($fp);
        
        // Release lock
        flock($fp, LOCK_UN);
        fclose($fp);
        
        return [
            'success' => true,
            'orderId' => $orderData['orderId'],
            'message' => 'Order submitted successfully'
        ];
    }
    
    return [
        'success' => false,
        'orderId' => null,
        'message' => 'Failed to save order after multiple attempts. Please try again.'
    ];
}

/**
 * Find order by ID
 * 
 * @param string $orderId Order ID
 * @return array|null Order data or null if not found
 */
function findOrderById($orderId) {
    $orders = loadOrdersData();
    
    foreach ($orders['orders'] as $order) {
        if ($order['orderId'] === $orderId) {
            return $order;
        }
    }
    
    return null;
}

/**
 * Update order by ID with atomic operation
 * 
 * @param string $orderId Order ID
 * @param array $updates Fields to update
 * @return bool Success status
 */
function updateOrderById($orderId, $updates) {
    $ordersFile = defined('ORDERS_DATABASE') ? ORDERS_DATABASE : __DIR__ . '/data/orders.json';
    
    $fp = fopen($ordersFile, 'c+');
    if (!$fp) {
        return false;
    }
    
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return false;
    }
    
    $content = '';
    while (!feof($fp)) {
        $content .= fread($fp, 8192);
    }
    
    $orders = json_decode($content, true);
    if (!$orders) {
        flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }
    
    $found = false;
    foreach ($orders['orders'] as &$order) {
        if ($order['orderId'] === $orderId) {
            foreach ($updates as $key => $value) {
                $order[$key] = $value;
            }
            $order['lastUpdated'] = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }
    
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    
    flock($fp, LOCK_UN);
    fclose($fp);
    
    return true;
}

// ============================================================================
// 2. VALIDATION FUNCTIONS
// ============================================================================

/**
 * Validate email address
 * 
 * @param string $email Email to validate
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (international format)
 * 
 * @param string $phone Phone number
 * @return bool
 */
function validatePhone($phone) {
    // Remove spaces, dashes, parentheses
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    
    // Check if starts with + and has 10-15 digits
    return preg_match('/^\+\d{10,15}$/', $phone) === 1;
}

/**
 * Validate URL
 * 
 * @param string $url URL to validate
 * @return bool
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate Google Maps URL
 * 
 * @param string $url Google Maps URL
 * @return bool
 */
function validateGoogleMapsUrl($url) {
    $patterns = [
        'maps.app.goo.gl',
        'maps.google.com',
        'goo.gl',
        'g.page',
        'google.com/maps',
        'share.google',
        'google.com/local'
    ];

    foreach ($patterns as $pattern) {
        if (strpos($url, $pattern) !== false) {
            return true;
        }
    }

    return false;
}

// ============================================================================
// 3. FORMATTING FUNCTIONS (ENCODING FIXED)
// ============================================================================

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Format string
 * @return string Formatted date
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) {
        return 'N/A';
    }
    
    try {
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : $date;
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Format datetime for display
 * 
 * @param string $datetime Datetime string
 * @return string Formatted datetime
 */
function formatDateTime($datetime) {
    return formatDate($datetime, 'M d, Y H:i');
}

/**
 * Format phone number for display
 * 
 * @param string $phone Phone number
 * @return string Formatted phone
 */
function formatPhone($phone) {
    // Remove all non-digits except +
    $phone = preg_replace('/[^\d\+]/', '', $phone);
    return $phone;
}

/**
 * Format currency (ENCODING FIXED - proper UTF-8)
 * 
 * @param float $amount Amount
 * @param string $currency Currency code
 * @return string Formatted amount
 */
function formatCurrency($amount, $currency = 'USD') {
    // Currency symbols with proper UTF-8 encoding
    $symbols = [
        'USD' => '$',
        'CAD' => 'CAD $',
        'AUD' => 'AUD $',
        'GBP' => '£',      // British Pound - UTF-8: \xC2\xA3
        'EUR' => '€',      // Euro - UTF-8: \xE2\x82\xAC
        'SGD' => 'S$',
        'IDR' => 'Rp'
    ];
    
    $symbol = $symbols[$currency] ?? '$';
    return $symbol . number_format((float)$amount, 2);
}

/**
 * Get status badge HTML class
 * 
 * @param string $status Status value
 * @return string CSS class
 */
function getStatusClass($status) {
    $classes = [
        'Pending' => 'status-pending',
        'Processing' => 'status-processing',
        'Completed' => 'status-completed',
        'Cancelled' => 'status-cancelled',
        'On Hold' => 'status-hold'
    ];
    
    return $classes[$status] ?? 'status-pending';
}

/**
 * Get status badge color
 * 
 * @param string $status Status value
 * @return array ['bg' => string, 'text' => string]
 */
function getStatusColors($status) {
    $colors = [
        'Pending' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
        'Processing' => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
        'Completed' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'Cancelled' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
        'On Hold' => ['bg' => '#F3F4F6', 'text' => '#374151']
    ];
    
    return $colors[$status] ?? $colors['Pending'];
}

/**
 * Truncate text
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix (default: ...)
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

// ============================================================================
// 4. FILE OPERATIONS
// ============================================================================

/**
 * Save data to JSON file with atomic write (RACE CONDITION FIX)
 * Uses temporary file + rename for atomic operation
 * 
 * @param string $filepath File path
 * @param array $data Data to save
 * @return bool Success status
 */
function saveJsonFileAtomic($filepath, $data) {
    // Ensure directory exists
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Create temporary file in same directory (for atomic rename)
    $tempFile = $filepath . '.tmp.' . getmypid() . '.' . uniqid();
    
    // Write to temp file
    $jsonOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($tempFile, $jsonOutput, LOCK_EX) === false) {
        @unlink($tempFile);
        return false;
    }
    
    // Atomic rename
    if (!rename($tempFile, $filepath)) {
        @unlink($tempFile);
        return false;
    }
    
    return true;
}

/**
 * Save data to JSON file with file locking (legacy support)
 * 
 * @param string $filepath File path
 * @param array $data Data to save
 * @return bool Success status
 */
function saveJsonFile($filepath, $data) {
    return saveJsonFileAtomic($filepath, $data);
}

/**
 * Load data from JSON file
 * 
 * @param string $filepath File path
 * @return array|null Data or null on error
 */
function loadJsonFile($filepath) {
    if (!file_exists($filepath)) {
        return null;
    }
    
    $jsonData = file_get_contents($filepath);
    return json_decode($jsonData, true);
}

/**
 * Get file size in human readable format
 * 
 * @param int $bytes File size in bytes
 * @return string Formatted size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// ============================================================================
// 5. UTILITY FUNCTIONS
// ============================================================================

/**
 * Sanitize input data
 * 
 * @param mixed $data Data to sanitize
 * @return mixed Sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    if (is_string($data)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Generate random token
 * 
 * @param int $length Token length (in bytes, output will be 2x this)
 * @return string Random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Check if request is AJAX
 * 
 * @return bool
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get client IP address
 * 
 * @return string IP address
 */
function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Get first IP from comma-separated list
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Generate JSON response
 * 
 * @param array $data Response data
 * @param int $statusCode HTTP status code
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================================
// 6. DATA PROCESSING FUNCTIONS
// ============================================================================

/**
 * Calculate order statistics
 * 
 * @param array $orders Array of orders
 * @return array Statistics
 */
function calculateOrderStats($orders) {
    $stats = [
        'total' => count($orders),
        'pending' => 0,
        'processing' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'totalValue' => 0
    ];
    
    foreach ($orders as $order) {
        $status = strtolower($order['status'] ?? 'pending');
        
        if (isset($stats[$status])) {
            $stats[$status]++;
        }
        
        if (isset($order['orderValue'])) {
            $stats['totalValue'] += floatval($order['orderValue']);
        }
    }
    
    return $stats;
}

/**
 * Filter orders by criteria
 * 
 * @param array $orders Array of orders
 * @param array $filters Filter criteria
 * @return array Filtered orders
 */
function filterOrders($orders, $filters) {
    return array_filter($orders, function($order) use ($filters) {
        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            if ($order['status'] !== $filters['status']) {
                return false;
            }
        }
        
        // Filter by product type
        if (isset($filters['productType']) && !empty($filters['productType'])) {
            if ($order['productType'] !== $filters['productType']) {
                return false;
            }
        }
        
        // Filter by search term
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $searchable = strtolower(implode(' ', [
                $order['orderId'] ?? '',
                $order['email'] ?? '',
                $order['whatsapp'] ?? ''
            ]));
            
            if (strpos($searchable, $search) === false) {
                return false;
            }
        }
        
        return true;
    });
}

/**
 * Sort orders by field
 * 
 * @param array $orders Array of orders
 * @param string $field Field to sort by
 * @param string $direction Sort direction (asc/desc)
 * @return array Sorted orders
 */
function sortOrders($orders, $field = 'timestamp', $direction = 'desc') {
    usort($orders, function($a, $b) use ($field, $direction) {
        $aVal = $a[$field] ?? '';
        $bVal = $b[$field] ?? '';
        
        if ($direction === 'asc') {
            return $aVal <=> $bVal;
        } else {
            return $bVal <=> $aVal;
        }
    });
    
    return $orders;
}

/**
 * Log activity (for debugging and auditing)
 * 
 * @param string $message Log message
 * @param string $level Log level (info, warning, error)
 */
function logActivity($message, $level = 'info') {
    $verboseLogging = defined('VERBOSE_LOGGING') ? VERBOSE_LOGGING : false;
    
    if (!$verboseLogging && $level === 'info') {
        return;
    }
    
    $dataDir = defined('DATA_DIR') ? DATA_DIR : __DIR__ . '/data';
    
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    $logFile = $dataDir . '/activity.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// ============================================================================
// 7. CSRF FUNCTIONS
// ============================================================================

/**
 * Get CSRF token from session
 * 
 * @return string|null CSRF token or null
 */
function getCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['csrf_token'] ?? null;
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool
 */
function verifyCSRFToken($token) {
    $sessionToken = getCSRFToken();
    
    if (empty($sessionToken) || empty($token)) {
        return false;
    }
    
    return hash_equals($sessionToken, $token);
}

// ============================================================================
// END OF FUNCTIONS
// ============================================================================

// Note: No closing PHP tag to prevent accidental whitespace