# 🎯 Quick Reference - Session & Memory Management

## Common Tasks

### Session Operations

```php
// Get admin info
$adminName = SessionManager::get('admin_name', 'Guest');
$adminId = SessionManager::get('admin_id');

// Check if logged in
if (SessionManager::has('admin_id')) {
    // User is logged in
}

// Set session data
SessionManager::set('user_preference', 'dark_mode');

// Remove session data
SessionManager::remove('temp_data');
```

### Flash Messages

```php
// Set flash message (in form handler)
SessionManager::flash('success', 'Changes saved!');
SessionManager::flash('error', 'Something went wrong');

// Display flash message (in template)
<?php if ($msg = SessionManager::flash('success')): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
```

### Cookie Operations

```php
// Set cookie (30 days)
CookieManager::set('theme', 'dark', 30 * 24 * 60 * 60);

// Get cookie
$theme = CookieManager::get('theme', 'light');

// Check cookie
if (CookieManager::has('theme')) {
    // Cookie exists
}

// Delete cookie
CookieManager::delete('theme');

// Store JSON
CookieManager::setJson('settings', ['theme' => 'dark', 'lang' => 'en'], 30 * 24 * 60 * 60);
$settings = CookieManager::getJson('settings', []);
```

### Memory Monitoring

```php
// Check memory usage
$current = MemoryManager::getUsage();
$peak = MemoryManager::getPeakUsage();

// Display formatted
echo MemoryManager::formatBytes($current); // "25.3 MB"

// Free large variables
MemoryManager::free($largeArray, $bigData);
```

## Security Patterns

### Login
```php
if ($validUser) {
    SessionManager::regenerate();
    SessionManager::set('admin_id', $user['id']);
    SessionManager::set('admin_name', $user['name']);
}
```

### Logout
```php
SessionManager::destroy();
CookieManager::delete('remember_token');
header('Location: index.php');
exit;
```

### Protected Page
```php
requireLogin(); // Checks session + timeout
```

### CSRF Protection
```php
// Generate token
<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

// Verify token
if (!verifyCSRFToken($_POST['csrf_token'])) {
    die('Invalid request');
}
```

## Configuration

### .env Settings
```env
SESSION_LIFETIME=7200          # 2 hours
SESSION_COOKIE_SECURE=true     # HTTPS only
SESSION_COOKIE_HTTPONLY=true   # No JS access
APP_DEBUG=false                # Production
```

## Testing URLs

```
System Health: /system-health.php?verify=health-check-2024
Security Check: /security-check.php?verify=security-check-2024
```

**⚠️ DELETE BOTH FILES AFTER TESTING!**

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Session expires too fast | Increase `SESSION_LIFETIME` in .env |
| Cookies not working | Enable HTTPS, set `SESSION_COOKIE_SECURE=true` |
| High memory usage | Check for memory leaks, use `MemoryManager::free()` |
| Login not persisting | Check session permissions, verify fingerprinting |

## DO's and DON'Ts

### ✅ DO
- Use SessionManager methods
- Regenerate session on login
- Clear cookies on logout
- Monitor memory in debug mode
- Use flash messages for notifications

### ❌ DON'T
- Access `$_SESSION` directly
- Store passwords in sessions/cookies
- Ignore session timeouts
- Skip session regeneration
- Forget to cleanup resources

---

**Remember:** Always use the Manager classes instead of PHP's native functions!
