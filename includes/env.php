<?php
/**
 * ============================================================================
 * Environment Variable Loader
 * ============================================================================
 * Loads environment variables from .env file
 * This provides a secure way to manage sensitive configuration
 */

class EnvLoader {
    private static $loaded = false;
    private static $variables = [];

    /**
     * Load environment variables from .env file
     */
    public static function load($filePath = null) {
        if (self::$loaded) {
            return;
        }

        if ($filePath === null) {
            $filePath = dirname(__DIR__) . '/.env';
        }

        if (!file_exists($filePath)) {
            // If .env doesn't exist, try to use .env.example or throw error
            $examplePath = dirname(__DIR__) . '/.env.example';
            if (file_exists($examplePath)) {
                error_log("WARNING: .env file not found. Using .env.example. Please create .env file!");
                $filePath = $examplePath;
            } else {
                throw new Exception(".env file not found at: $filePath");
            }
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse the line
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                // Store in class variable
                self::$variables[$name] = $value;

                // Set as environment variable
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }

                // Also set using putenv for compatibility
                putenv("$name=$value");
            }
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable value
     * 
     * @param string $key The environment variable name
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get($key, $default = null) {
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Check if an environment variable exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key) {
        return isset(self::$variables[$key]) || 
               isset($_ENV[$key]) || 
               getenv($key) !== false;
    }

    /**
     * Get a boolean environment variable
     * 
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function getBool($key, $default = false) {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get an integer environment variable
     * 
     * @param string $key
     * @param int $default
     * @return int
     */
    public static function getInt($key, $default = 0) {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }

        return (int) $value;
    }

    /**
     * Get all loaded variables (for debugging only)
     * 
     * @return array
     */
    public static function getAll() {
        return self::$variables;
    }
}

// Helper function for easier access
if (!function_exists('env')) {
    /**
     * Get an environment variable value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env($key, $default = null) {
        return EnvLoader::get($key, $default);
    }
}
?>
