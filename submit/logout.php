<?php
/**
 * ============================================================================
 * File: /submit/logout.php
 * Smart Buzzer Logout Handler - COMPLETE v1.1
 * 
 * v1.1 FIX: Removed unused logged_out parameter from redirect
 * 
 * Description:
 * Handles user logout by destroying session and redirecting to login page.
 * Simple, secure logout implementation.
 * 
 * v1.0 FEATURES:
 * - Session destruction
 * - Cookie removal (remember me)
 * - Session data cleanup
 * - Redirect to login page
 * - Activity logging
 * 
 * Security Features:
 * - Complete session cleanup
 * - Secure cookie removal
 * - No data persistence
 * 
 * Usage:
 * - Direct access: /submit/logout.php
 * - Link: <a href="logout.php">Logout</a>
 * 
 * Dependencies:
 * - auth.php (logout function)
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

session_start();

require_once 'auth.php';

// Perform logout
logout();

// Redirect to login page
header('Location: login');
exit;