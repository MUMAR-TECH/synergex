# 🔧 Session, Cookie, and Memory Management Guide

## Overview

The Synergex application now includes professional session management, secure cookie handling, and memory optimization through three core manager classes:

1. **SessionManager** - Secure session handling with fingerprinting and auto-regeneration
2. **CookieManager** - Professional cookie management with security defaults
3. **MemoryManager** - Memory optimization and resource cleanup
4. **CacheManager** - HTTP cache header management

## 📋 Features

### Session Management

#### Security Features
- ✅ **Session Fingerprinting** - Validates user agent, language, and encoding to prevent hijacking
- ✅ **Auto Regeneration** - Session IDs regenerate every 5 minutes
- ✅ **Timeout Handling** - Automatic timeout after 2 hours (configurable)
- ✅ **Secure Cookies** - HttpOnly, Secure, and SameSite attributes
- ✅ **Session Validation** - Continuous validation of session integrity

#### Usage Examples

```php
// Initialize session (automatically called in config.php)
SessionManager::init();

// Set session data
SessionManager::set('user_id', 123);
SessionManager::set('user_name', 'John Doe');

// Get session data
$userId = SessionManager::get('user_id');
$userName = SessionManager::get('user_name', 'Guest'); // with default

// Check if session variable exists
if (SessionManager::has('user_id')) {
    // User is logged in
}

// Remove session variable
SessionManager::remove('temp_data');

// Flash messages (one-time messages)
SessionManager::flash('success', 'Your changes have been saved!');
$message = SessionManager::flash('success'); // Gets and removes

// Regenerate session ID manually
SessionManager::regenerate();

// Destroy session completely
SessionManager::destroy();
```

### Cookie Management

#### Security Features
- ✅ **Auto-Secure Detection** - Automatically uses secure flag on HTTPS
- ✅ **HttpOnly by Default** - Prevents JavaScript access
- ✅ **SameSite Protection** - CSRF protection with Lax policy
- ✅ **JSON Support** - Easy storage of complex data

#### Usage Examples

```php
// Set a simple cookie (expires in 30 days)
CookieManager::set('user_preference', 'dark_mode', 30 * 24 * 60 * 60);

// Get cookie value
$preference = CookieManager::get('user_preference', 'light_mode');

// Check if cookie exists
if (CookieManager::has('user_preference')) {
    // Cookie exists
}

// Delete cookie
CookieManager::delete('user_preference');

// Store JSON data in cookie
CookieManager::setJson('user_settings', [
    'theme' => 'dark',
    'language' => 'en',
    'notifications' => true
], 30 * 24 * 60 * 60);

// Get JSON data from cookie
$settings = CookieManager::getJson('user_settings', []);
```

### Memory Management

#### Features
- ✅ **Memory Limit Configuration** - 256MB production, 512MB development
- ✅ **Output Buffering** - Automatic buffer management
- ✅ **Gzip Compression** - Reduces bandwidth usage by 60-80%
- ✅ **Automatic Cleanup** - Memory freed at script end
- ✅ **Garbage Collection** - Manual garbage collection support

#### Usage Examples

```php
// Initialize memory management (automatically called in config.php)
MemoryManager::init();

// Get current memory usage
$current = MemoryManager::getUsage();
$peak = MemoryManager::getPeakUsage();

// Format memory size for display
echo "Memory used: " . MemoryManager::formatBytes($current);
echo "Peak memory: " . MemoryManager::formatBytes($peak);

// Free large variables manually
$largeArray = range(1, 1000000);
MemoryManager::free($largeArray); // Frees and triggers garbage collection

// Manual cleanup (automatically called at script end)
MemoryManager::cleanup();
```

### Cache Management

#### Features
- ✅ **Static Resource Caching** - Long-term caching for assets
- ✅ **No-Cache for Dynamic Content** - Prevents stale data
- ✅ **ETag Support** - Efficient conditional requests

#### Usage Examples

```php
// Set cache headers for static assets (CSS, JS, images)
CacheManager::setHeaders('public', 86400); // Cache for 24 hours

// Disable caching for dynamic content (default in config)
CacheManager::noCache();
```

## 🔒 Security Best Practices

### Session Security

1. **Always use SessionManager methods** instead of direct `$_SESSION` access
2. **Regenerate session on privilege changes** (login, logout, role change)
3. **Set appropriate timeout values** based on sensitivity
4. **Monitor session logs** for suspicious activity

```php
// Good: Use SessionManager
SessionManager::set('user_role', 'admin');

// Bad: Direct session access
$_SESSION['user_role'] = 'admin';
```

### Cookie Security

1. **Never store sensitive data** in cookies
2. **Always encrypt sensitive cookie values**
3. **Use short expiration** for authentication tokens
4. **Clear cookies on logout**

```php
// Good: Short expiration for sensitive data
CookieManager::set('auth_token', $token, 3600); // 1 hour

// Bad: Long expiration for auth tokens
CookieManager::set('auth_token', $token, 365 * 24 * 60 * 60); // 1 year
```

## 📊 Performance Benefits

### Before Implementation
- Manual session handling
- No session validation
- Direct `$_SESSION` usage
- No memory optimization
- No output buffering
- No compression

### After Implementation
- ✅ **60-80% bandwidth reduction** (Gzip compression)
- ✅ **Session hijacking protection** (Fingerprinting)
- ✅ **Automatic session cleanup** (Regeneration)
- ✅ **Memory leak prevention** (Garbage collection)
- ✅ **Faster page loads** (Output buffering)
- ✅ **Better security** (Secure cookies)

## 🔧 Configuration

### Environment Variables (.env)

```env
# Session Configuration
SESSION_LIFETIME=7200          # 2 hours
SESSION_COOKIE_SECURE=true     # Enable for HTTPS
SESSION_COOKIE_HTTPONLY=true   # Prevent JS access

# Application Environment
APP_ENV=production             # production or development
APP_DEBUG=false               # Disable in production
```

### Custom Configuration

You can customize session and memory settings by modifying constants in `config.php`:

```php
// Custom session timeout
define('SESSION_LIFETIME', 3600); // 1 hour

// Custom memory limits in session_manager.php
private static $sessionTimeout = 3600;
private static $regenerateInterval = 300;
```

## 🐛 Debugging

### Enable Debug Mode

Set in `.env`:
```env
APP_DEBUG=true
```

This will:
- Log memory usage at script end
- Show detailed error messages
- Log session activity

### Check Memory Usage

```php
if (defined('APP_DEBUG') && APP_DEBUG) {
    echo "Current: " . MemoryManager::formatBytes(MemoryManager::getUsage());
    echo "Peak: " . MemoryManager::formatBytes(MemoryManager::getPeakUsage());
}
```

### Session Debugging

```php
if (defined('APP_DEBUG') && APP_DEBUG) {
    error_log("Session ID: " . SessionManager::getId());
    error_log("Session Active: " . (SessionManager::isActive() ? 'Yes' : 'No'));
    error_log("Admin logged in: " . (SessionManager::has('admin_id') ? 'Yes' : 'No'));
}
```

## 📁 File Structure

```
includes/
├── session_manager.php    # SessionManager, CookieManager, MemoryManager, CacheManager
├── functions.php          # Updated to use SessionManager
├── header.php            # Includes cleanup initialization
└── footer.php            # Memory cleanup and resource freeing

config.php                # Initializes all managers
admin/
├── index.php            # Updated login with SessionManager
├── logout.php           # Proper session destruction
└── includes/
    └── admin_header.php  # Uses SessionManager for admin data
```

## 🚀 Migration from Old Code

### Before (Direct Session)
```php
// Login
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_name'] = $admin['name'];

// Check login
if (isset($_SESSION['admin_id'])) {
    // Logged in
}

// Logout
session_destroy();
```

### After (SessionManager)
```php
// Login
SessionManager::regenerate();
SessionManager::set('admin_id', $admin['id']);
SessionManager::set('admin_name', $admin['name']);

// Check login
if (SessionManager::has('admin_id')) {
    // Logged in
}

// Logout
SessionManager::destroy();
CookieManager::delete('remember_token');
```

## ✅ Checklist for Production

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Enable `SESSION_COOKIE_SECURE=true` for HTTPS
- [ ] Set appropriate `SESSION_LIFETIME` value
- [ ] Test session timeout functionality
- [ ] Verify cookie security settings
- [ ] Monitor memory usage logs
- [ ] Test login/logout flow
- [ ] Verify session regeneration works
- [ ] Check flash messages work correctly

## 📚 Additional Resources

- [PHP Session Security](https://www.php.net/manual/en/session.security.php)
- [OWASP Session Management](https://owasp.org/www-community/vulnerabilities/Session_Management)
- [Cookie Security Best Practices](https://owasp.org/www-chapter-london/assets/slides/OWASPLondon20171130_Cookie_Security_Myths_Misconceptions_David_Johansson.pdf)

---

**Last Updated:** December 2025  
**Version:** 1.0.0
