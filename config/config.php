<?php
// Site configuration
define('SITE_NAME', 'Online Voting System');
define('SITE_URL', 'http://localhost/voting_system');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '97swain');
define('DB_NAME', 'voting_system');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('Asia/Manila');

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('UPLOAD_PATH', 'uploads/');

// Application Configuration
define('ADMIN_EMAIL', 'admin@example.com');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
?> 