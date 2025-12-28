<?php
/**
 * ============================================================================
 * SYNERGEX CONFIGURATION FILE
 * ============================================================================
 * Main configuration file that loads environment variables
 * This file is safe to commit to version control
 */

// Load environment variables
require_once __DIR__ . '/includes/env.php';
require_once __DIR__ . '/includes/session_manager.php';

try {
    EnvLoader::load(__DIR__ . '/.env');
} catch (Exception $e) {
    die('Configuration Error: ' . $e->getMessage() . '<br>Please create a .env file based on .env.example');
}

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'synergex_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// Site Configuration
define('SITE_URL', env('SITE_URL', 'http://localhost/synergex'));
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/assets/images/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/images/uploads/');

// Application Settings
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', EnvLoader::getBool('APP_DEBUG', false));

// WhatsApp Configuration
define('WHATSAPP_NUMBER', env('WHATSAPP_NUMBER', '260770377471'));
define('WHATSAPP_MESSAGE', env('WHATSAPP_MESSAGE', 'Hello Synergex! I would like to know more about your products.'));

// Email Configuration
define('SMTP_HOST', env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', EnvLoader::getInt('SMTP_PORT', 587));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('SMTP_FROM_EMAIL', env('SMTP_FROM_EMAIL', env('SMTP_USER', '')));
define('SMTP_FROM_NAME', env('SMTP_FROM_NAME', 'Synergex Solutions'));

// Session Configuration
define('SESSION_LIFETIME', EnvLoader::getInt('SESSION_LIFETIME', 7200));
ini_set('session.cookie_httponly', EnvLoader::getBool('SESSION_COOKIE_HTTPONLY', true) ? 1 : 0);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', EnvLoader::getBool('SESSION_COOKIE_SECURE', false) ? 1 : 0);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Upload Configuration
define('MAX_UPLOAD_SIZE', EnvLoader::getInt('MAX_UPLOAD_SIZE', 52428800)); // 50MB default
define('ALLOWED_IMAGE_TYPES', explode(',', env('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif,webp')));
define('ALLOWED_VIDEO_TYPES', explode(',', env('ALLOWED_VIDEO_TYPES', 'mp4,webm,ogg,avi,mov')));

// Timezone
date_default_timezone_set(env('TIMEZONE', 'Africa/Lusaka'));

// Error Reporting - based on environment
if (APP_ENV === 'development' || APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error_log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error_log');
}

// Initialize Session Manager (handles secure session and cookie management)
SessionManager::init();

// Initialize Memory Manager (optimizes memory usage and output buffering)
MemoryManager::init();

// Set cache headers for dynamic content
CacheManager::noCache();

// Security Headers (for production)
if (APP_ENV === 'production') {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
?>