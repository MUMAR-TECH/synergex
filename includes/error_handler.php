<?php
/**
 * ============================================================================
 * Production Error Handler Configuration
 * ============================================================================
 * Include this file at the top of your config.php for production
 */

// Set error reporting based on environment
if (defined('APP_DEBUG') && APP_DEBUG === true) {
    // Development mode - show all errors
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    // Production mode - hide errors from users, log them
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    
    // Set custom error log location (relative to project root)
    ini_set('error_log', __DIR__ . '/../error_log');
}

// Custom error handler function
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Don't log if error reporting is disabled
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorTypes = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER DEPRECATED'
    ];
    
    $errorType = $errorTypes[$errno] ?? 'UNKNOWN ERROR';
    
    // Log the error
    error_log(sprintf(
        "[%s] %s: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $errorType,
        $errstr,
        $errfile,
        $errline
    ));
    
    // In production, show generic error page
    if (!APP_DEBUG && in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
        }
        include __DIR__ . '/../error_pages/500.php';
        exit();
    }
    
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    error_log(sprintf(
        "[%s] Uncaught Exception: %s in %s on line %d\nStack trace:\n%s",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));
    
    // In production, show generic error page
    if (!APP_DEBUG) {
        if (!headers_sent()) {
            http_response_code(500);
        }
        if (file_exists(__DIR__ . '/../error_pages/500.php')) {
            include __DIR__ . '/../error_pages/500.php';
        } else {
            echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
            echo '<h1>An error occurred</h1>';
            echo '<p>We apologize for the inconvenience. Please try again later.</p>';
            echo '</body></html>';
        }
        exit();
    } else {
        // In debug mode, show the actual error
        echo '<pre>Uncaught Exception: ' . $exception->getMessage() . "\n";
        echo 'File: ' . $exception->getFile() . ' Line: ' . $exception->getLine() . "\n";
        echo 'Stack trace:' . "\n" . $exception->getTraceAsString() . '</pre>';
        exit();
    }
}

// Custom fatal error handler
function customShutdownHandler() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        error_log(sprintf(
            "[%s] FATAL ERROR: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        ));
        
        if (!APP_DEBUG) {
            if (!headers_sent()) {
                http_response_code(500);
            }
            if (file_exists(__DIR__ . '/../error_pages/500.php')) {
                include __DIR__ . '/../error_pages/500.php';
            } else {
                echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
                echo '<h1>An error occurred</h1>';
                echo '<p>We apologize for the inconvenience. Please try again later.</p>';
                echo '</body></html>';
            }
        }
    }
}

// Register error handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
register_shutdown_function('customShutdownHandler');
