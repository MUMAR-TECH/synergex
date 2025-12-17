<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'synergex_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_URL', 'http://localhost/synergex');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/assets/images/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/images/uploads/');

// WhatsApp Configuration
define('WHATSAPP_NUMBER', '260770377471');
define('WHATSAPP_MESSAGE', 'Hello Synergex! I would like to know more about your products.');

// Email Configuration (for future use)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'synergexsolutions25@gmail.com');
define('SMTP_PASS', ''); // Add password when ready

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Timezone
date_default_timezone_set('Africa/Lusaka');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>