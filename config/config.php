<?php
/**
 * Database Configuration
 * 
 * IMPORTANT: Change these values for production
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'college_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration - IMPORTANT: Update this to match your setup
define('SITE_URL', 'http://localhost/website');  // No trailing slash
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/faculty/');  // Fixed path
define('UPLOAD_URL', SITE_URL . '/uploads/faculty/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Admin Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_NAME', 'college_admin_session');

// Security Settings
define('ENABLE_CSRF', true);
define('ENABLE_RATE_LIMIT', true);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// Error Reporting - Set to 0 in production
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    if (defined('SESSION_NAME')) {
        session_name(SESSION_NAME);
    }
    session_start();
}
?>


