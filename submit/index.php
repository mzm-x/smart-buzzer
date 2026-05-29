<?php
/**
 * ============================================================================
 * File: /submit/index.php
 * Smart Buzzer Order System Index - v1.1
 * 
 * Description:
 * Entry point for the order system. Redirects users to appropriate pages
 * based on their authentication status.
 * 
 * ✅ v1.1 FEATURES:
 * - Proper redirect logic
 * - Clean URL support
 * - Authentication check
 * - Session handling
 * 
 * Redirect Logic:
 * - Public users → /submit (client order form)
 * - Authenticated AMs → /submit/manage (dashboard)
 * 
 * Author: Smart Buzzer Development Team
 * Last Updated: December 2024
 * ============================================================================
 */

// Start session
session_start();

// Check if user is authenticated AM
$isAuthenticated = isset($_SESSION['am_logged_in']) && $_SESSION['am_logged_in'] === true;

// Redirect based on authentication status
if ($isAuthenticated) {
    // Authenticated AM → Dashboard
    header('Location: manage');
    exit;
} else {
    // Public user → Order Form
    header('Location: submit');
    exit;
}