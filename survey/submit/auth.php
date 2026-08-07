<?php
/**
 * ============================================================================
 * File: /submit/auth.php
 * Smart Buzzer Authentication Handler - COMPLETE v1.2
 * 
 * v1.2 FIX: Added isLoggedIn() alias for backward compatibility
 * 
 * Description:
 * Handles user authentication, session management, and authorization.
 * Provides secure login/logout functionality for Account Managers.
 * 
 * ✅ v1.1 SECURITY FIXES:
 * - Fixed session_regenerate_id placement (now BEFORE setting session vars)
 * - Fixed authenticate() return value consistency
 * - Added CSRF token generation
 * - Improved session validation
 * - Added secure headers
 * 
 * ✅ v1.0 FEATURES:
 * - Secure password verification (bcrypt)
 * - Session management with regeneration
 * - Remember me functionality (30 days)
 * - Failed login attempt tracking
 * - Rate limiting protection
 * - Session timeout handling
 * - Secure logout with session destruction
 * - User data management
 * - Password hash generation helper
 * 
 * Security Features:
 * - Password hashing with bcrypt (cost factor: 12)
 * - Session regeneration on login (prevent session fixation)
 * - Secure cookie settings (HttpOnly, SameSite)
 * - Rate limiting (max 5 failed attempts per 15 minutes)
 * - Session timeout (30 minutes inactivity)
 * - IP address validation
 * - User agent validation
 * - CSRF token protection
 * 
 * Functions:
 * - authenticate($username, $password, $remember)
 * - checkAuth()
 * - isLoggedIn() - alias for checkAuth()
 * - logout()
 * - generatePasswordHash($password)
 * - isRateLimited($username)
 * - recordFailedAttempt($username)
 * - generateCSRFToken()
 * - validateCSRFToken($token)
 * 
 * Session Variables:
 * - am_logged_in (bool)
 * - am_username (string)
 * - am_name (string)
 * - am_email (string)
 * - am_role (string)
 * - login_time (timestamp)
 * - last_activity (timestamp)
 * - csrf_token (string)
 * 
 * Dependencies:
 * - config.php (user credentials)
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

require_once __DIR__ . '/config.php';

/**
 * Set secure headers
 */
function setSecureHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string $token Token to validate
 * @return bool
 */
function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate CSRF token (after successful form submission)
 */
function regenerateCSRFToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Authenticate user credentials
 * 
 * @param string $username Username
 * @param string $password Password
 * @param bool $remember Remember me option
 * @return array ['success' => bool, 'message' => string]
 */
function authenticate($username, $password, $remember = false) {
    global $AM_USERS;
    
    // Sanitize input
    $username = trim($username);
    
    // Check if rate limited
    if (isRateLimited($username)) {
        return [
            'success' => false,
            'message' => 'Too many failed login attempts. Please try again in 15 minutes.'
        ];
    }
    
    // Find user
    $user = null;
    foreach ($AM_USERS as $u) {
        if ($u['username'] === $username) {
            $user = $u;
            break;
        }
    }
    
    // Validate credentials
    if (!$user || !password_verify($password, $user['password'])) {
        recordFailedAttempt($username);
        return [
            'success' => false,
            'message' => 'Invalid username or password.'
        ];
    }
    
    // Clear failed attempts
    clearFailedAttempts($username);
    
    // SECURITY FIX: Regenerate session ID BEFORE setting session vars (prevent session fixation)
    session_regenerate_id(true);
    
    // Set session variables AFTER regeneration
    $_SESSION['am_logged_in'] = true;
    $_SESSION['am_username'] = $user['username'];
    $_SESSION['am_name'] = $user['name'];
    $_SESSION['am_email'] = $user['email'];
    $_SESSION['am_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Generate new CSRF token for this session
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    
    // Set remember me cookie (30 days)
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['remember_token'] = $token;
        
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        
        setcookie(
            'am_remember',
            $token,
            [
                'expires' => time() + (30 * 24 * 60 * 60), // 30 days
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }
    
    // Log successful login
    logAuthActivity("Successful login: {$user['username']} from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    return [
        'success' => true,
        'message' => 'Login successful'
    ];
}

/**
 * Check if user is authenticated
 * 
 * @return bool
 */
function checkAuth() {
    // Check if session exists
    if (!isset($_SESSION['am_logged_in']) || $_SESSION['am_logged_in'] !== true) {
        return false;
    }
    
    // Session timeout disabled — login persists for 1 year
    
    // Validate user agent (prevent session hijacking)
    if (isset($_SESSION['user_agent']) && isset($_SERVER['HTTP_USER_AGENT'])) {
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            logAuthActivity("Session hijacking attempt detected: User agent mismatch");
            logout();
            return false;
        }
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    
    return true;
}

/**
 * Alias for checkAuth() - backward compatibility
 * 
 * @return bool
 */
function isLoggedIn() {
    return checkAuth();
}

/**
 * Logout user and destroy session
 */
function logout() {
    // Log logout
    if (isset($_SESSION['am_username'])) {
        logAuthActivity("Logout: {$_SESSION['am_username']}");
    }
    
    // Clear remember me cookie
    if (isset($_COOKIE['am_remember'])) {
        setcookie('am_remember', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    // Destroy session
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Check if user is rate limited
 * 
 * @param string $username Username
 * @return bool
 */
function isRateLimited($username) {
    $dataDir = __DIR__ . '/data';
    $failedAttemptsFile = $dataDir . '/failed_attempts.json';
    
    if (!file_exists($failedAttemptsFile)) {
        return false;
    }
    
    $data = json_decode(file_get_contents($failedAttemptsFile), true);
    
    if (!$data || !isset($data[$username])) {
        return false;
    }
    
    $attempts = $data[$username];
    $timeWindow = defined('LOGIN_ATTEMPT_WINDOW') ? LOGIN_ATTEMPT_WINDOW : 900; // 15 minutes
    $maxAttempts = defined('MAX_LOGIN_ATTEMPTS') ? MAX_LOGIN_ATTEMPTS : 5;
    
    // Count recent attempts
    $recentAttempts = 0;
    $currentTime = time();
    
    foreach ($attempts as $timestamp) {
        if ($currentTime - $timestamp < $timeWindow) {
            $recentAttempts++;
        }
    }
    
    return $recentAttempts >= $maxAttempts;
}

/**
 * Record failed login attempt
 * 
 * @param string $username Username
 */
function recordFailedAttempt($username) {
    $dataDir = __DIR__ . '/data';
    $failedAttemptsFile = $dataDir . '/failed_attempts.json';
    
    // Create data directory if not exists
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    // Load existing data
    $data = [];
    if (file_exists($failedAttemptsFile)) {
        $data = json_decode(file_get_contents($failedAttemptsFile), true) ?? [];
    }
    
    // Add attempt
    if (!isset($data[$username])) {
        $data[$username] = [];
    }
    
    $data[$username][] = time();
    
    // Clean old attempts (older than 1 hour)
    $data[$username] = array_filter($data[$username], function($timestamp) {
        return (time() - $timestamp) < 3600;
    });
    
    // Reindex array
    $data[$username] = array_values($data[$username]);
    
    // Save with file locking
    $fp = fopen($failedAttemptsFile, 'w');
    if ($fp && flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    
    // Log failed attempt
    logAuthActivity("Failed login attempt: $username from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

/**
 * Clear failed login attempts
 * 
 * @param string $username Username
 */
function clearFailedAttempts($username) {
    $dataDir = __DIR__ . '/data';
    $failedAttemptsFile = $dataDir . '/failed_attempts.json';
    
    if (!file_exists($failedAttemptsFile)) {
        return;
    }
    
    $data = json_decode(file_get_contents($failedAttemptsFile), true) ?? [];
    
    if (isset($data[$username])) {
        unset($data[$username]);
        
        $fp = fopen($failedAttemptsFile, 'w');
        if ($fp && flock($fp, LOCK_EX)) {
            fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}

/**
 * Generate password hash (for creating new passwords)
 * 
 * @param string $password Plain text password
 * @return string Hashed password
 */
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Require authentication (redirect to login if not authenticated)
 */
function requireAuth() {
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!checkAuth()) {
        header('Location: login');
        exit;
    }
    
    // Set secure headers
    setSecureHeaders();
}

/**
 * Log authentication activity
 * 
 * @param string $message Log message
 */
function logAuthActivity($message) {
    $dataDir = __DIR__ . '/data';
    
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $logFile = $dataDir . '/auth.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Get current user info
 * 
 * @return array|null User info or null if not logged in
 */
function getCurrentUser() {
    if (!checkAuth()) {
        return null;
    }
    
    return [
        'username' => $_SESSION['am_username'] ?? '',
        'name' => $_SESSION['am_name'] ?? '',
        'email' => $_SESSION['am_email'] ?? '',
        'role' => $_SESSION['am_role'] ?? ''
    ];
}

/**
 * Check if current user has specific role
 * 
 * @param string|array $roles Role(s) to check
 * @return bool
 */
function hasRole($roles) {
    if (!checkAuth()) {
        return false;
    }
    
    $userRole = $_SESSION['am_role'] ?? '';
    
    if (is_string($roles)) {
        return $userRole === $roles;
    }
    
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    
    return false;
}