<?php
/**
 * ============================================================================
 * Session Manager - Professional Session and Memory Handling
 * ============================================================================
 * Provides secure session management, cookie handling, and memory optimization
 */

class SessionManager {
    private static $initialized = false;
    private static $sessionTimeout = 7200; // 2 hours default
    private static $regenerateInterval = 300; // 5 minutes
    
    /**
     * Initialize session with secure configuration
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        // Set custom session timeout
        if (defined('SESSION_LIFETIME')) {
            self::$sessionTimeout = SESSION_LIFETIME;
        }
        
        // Configure session settings before starting
        if (session_status() === PHP_SESSION_NONE) {
            // Session cookie parameters
            // Check for HTTPS - cPanel may use different headers
            $secure = (defined('APP_ENV') && APP_ENV === 'production') || 
                      (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                      (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            
            session_set_cookie_params([
                'lifetime' => self::$sessionTimeout,
                'path' => '/',
                'domain' => '', // Empty for subdomain compatibility
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            // Session configuration
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.gc_maxlifetime', self::$sessionTimeout);
            ini_set('session.gc_probability', '1');
            ini_set('session.gc_divisor', '100');
            
            // Start session
            session_start();
            
            // Validate and regenerate session
            self::validateSession();
            self::regenerateIfNeeded();
        }
        
        self::$initialized = true;
    }
    
    /**
     * Validate session security
     */
    private static function validateSession() {
        // Check if session has expired
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            if (time() - $_SESSION['LAST_ACTIVITY'] > self::$sessionTimeout) {
                self::destroy();
                return false;
            }
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Validate session fingerprint (only in development to avoid issues on shared hosting)
        if (defined('APP_DEBUG') && APP_DEBUG) {
            if (!isset($_SESSION['USER_FINGERPRINT'])) {
                $_SESSION['USER_FINGERPRINT'] = self::generateFingerprint();
            } else {
                if ($_SESSION['USER_FINGERPRINT'] !== self::generateFingerprint()) {
                    // Log potential issue but don't destroy in production
                    error_log('Session fingerprint mismatch - possible hijacking attempt');
                }
            }
        }
        
        // Set session created time if not exists
        if (!isset($_SESSION['CREATED_AT'])) {
            $_SESSION['CREATED_AT'] = time();
        }
        
        return true;
    }
    
    /**
     * Generate session fingerprint for security
     */
    private static function generateFingerprint() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'unknown';
        $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? 'unknown';
        
        return hash('sha256', $userAgent . $acceptLanguage . $acceptEncoding);
    }
    
    /**
     * Regenerate session ID periodically for security
     */
    private static function regenerateIfNeeded() {
        if (!isset($_SESSION['LAST_REGENERATION'])) {
            $_SESSION['LAST_REGENERATION'] = time();
        } else {
            if (time() - $_SESSION['LAST_REGENERATION'] > self::$regenerateInterval) {
                self::regenerate();
            }
        }
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerate() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['LAST_REGENERATION'] = time();
            $_SESSION['CREATED_AT'] = time();
        }
    }
    
    /**
     * Destroy session completely
     */
    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            
            // Delete session cookie
            if (isset($_COOKIE[session_name()])) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            session_destroy();
        }
    }
    
    /**
     * Set a session variable with optional encryption
     */
    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session variable
     */
    public static function get($key, $default = null) {
        self::init();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session variable exists
     */
    public static function has($key) {
        self::init();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session variable
     */
    public static function remove($key) {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Flash message - store for next request only
     */
    public static function flash($key, $value = null) {
        self::init();
        
        if ($value === null) {
            // Get flash message
            $message = self::get('_flash_' . $key);
            self::remove('_flash_' . $key);
            return $message;
        } else {
            // Set flash message
            self::set('_flash_' . $key, $value);
        }
    }
    
    /**
     * Clean up old flash messages
     */
    public static function cleanFlashMessages() {
        self::init();
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, '_flash_') === 0) {
                unset($_SESSION[$key]);
            }
        }
    }
    
    /**
     * Get session ID
     */
    public static function getId() {
        return session_id();
    }
    
    /**
     * Check if session is active
     */
    public static function isActive() {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}

/**
 * ============================================================================
 * Cookie Manager - Professional Cookie Handling
 * ============================================================================
 */
class CookieManager {
    /**
     * Set a secure cookie
     */
    public static function set($name, $value, $expire = 0, $path = '/', $secure = null, $httponly = true) {
        // Auto-detect secure flag
        if ($secure === null) {
            $secure = (defined('APP_ENV') && APP_ENV === 'production') || 
                      (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        }
        
        $options = [
            'expires' => $expire === 0 ? 0 : time() + $expire,
            'path' => $path,
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => 'Lax'
        ];
        
        return setcookie($name, $value, $options);
    }
    
    /**
     * Get a cookie value
     */
    public static function get($name, $default = null) {
        return $_COOKIE[$name] ?? $default;
    }
    
    /**
     * Check if cookie exists
     */
    public static function has($name) {
        return isset($_COOKIE[$name]);
    }
    
    /**
     * Delete a cookie
     */
    public static function delete($name, $path = '/') {
        if (self::has($name)) {
            self::set($name, '', -3600, $path);
            unset($_COOKIE[$name]);
        }
    }
    
    /**
     * Set a JSON cookie
     */
    public static function setJson($name, $data, $expire = 0, $path = '/') {
        return self::set($name, json_encode($data), $expire, $path);
    }
    
    /**
     * Get a JSON cookie
     */
    public static function getJson($name, $default = null) {
        $value = self::get($name);
        if ($value) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $default;
        }
        return $default;
    }
}

/**
 * ============================================================================
 * Memory Manager - Optimize Memory Usage
 * ============================================================================
 */
class MemoryManager {
    private static $initialized = false;
    
    /**
     * Initialize memory management
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        // Set memory limit for production
        if (defined('APP_ENV') && APP_ENV === 'production') {
            ini_set('memory_limit', '256M');
        } else {
            ini_set('memory_limit', '512M');
        }
        
        // Enable output buffering for better performance
        if (!ob_get_level()) {
            ob_start();
        }
        
        // Enable gzip compression if available
        if (!ini_get('zlib.output_compression') && extension_loaded('zlib')) {
            ini_set('zlib.output_compression', '1');
            ini_set('zlib.output_compression_level', '6');
        }
        
        // Register shutdown function to clean up
        register_shutdown_function([self::class, 'cleanup']);
        
        self::$initialized = true;
    }
    
    /**
     * Get current memory usage
     */
    public static function getUsage($realUsage = false) {
        return memory_get_usage($realUsage);
    }
    
    /**
     * Get peak memory usage
     */
    public static function getPeakUsage($realUsage = false) {
        return memory_get_peak_usage($realUsage);
    }
    
    /**
     * Format memory size
     */
    public static function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Clean up and flush output buffer
     */
    public static function cleanup() {
        // Flush output buffer
        if (ob_get_level()) {
            ob_end_flush();
        }
        
        // Log memory usage in debug mode
        if (defined('APP_DEBUG') && APP_DEBUG) {
            error_log(sprintf(
                "Memory Usage - Current: %s, Peak: %s",
                self::formatBytes(self::getUsage()),
                self::formatBytes(self::getPeakUsage())
            ));
        }
    }
    
    /**
     * Free up memory by clearing large variables
     */
    public static function free(&...$vars) {
        foreach ($vars as &$var) {
            $var = null;
            unset($var);
        }
        
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}

/**
 * ============================================================================
 * Cache Manager - Simple Cache Management
 * ============================================================================
 */
class CacheManager {
    /**
     * Set cache headers for static resources
     */
    public static function setHeaders($type = 'public', $maxAge = 3600) {
        $expires = gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT';
        
        header("Cache-Control: $type, max-age=$maxAge");
        header("Expires: $expires");
        header('Pragma: public');
        
        // ETag support
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    }
    
    /**
     * Set no-cache headers for dynamic content
     */
    public static function noCache() {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}
?>
