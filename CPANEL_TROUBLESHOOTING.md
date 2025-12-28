# 🔧 cPanel Deployment Troubleshooting Guide

## Common Issues and Solutions

### Issue 1: Redirect Loop on Login ✅ FIXED

**Symptoms:**
- "This page isn't working - redirected you too many times"
- Can't login to admin panel
- Browser shows ERR_TOO_MANY_REDIRECTS

**Root Causes:**
1. Session cookie domain mismatch
2. Session fingerprinting too strict on shared hosting
3. HTTPS detection issues on cPanel

**Solution Applied:**
- ✅ Fixed session cookie domain (set to empty for subdomain compatibility)
- ✅ Added X-Forwarded-Proto header check for HTTPS detection
- ✅ Relaxed session fingerprinting in production (debug mode only)
- ✅ Added redirect loop prevention in admin/index.php
- ✅ Enhanced session validation logic

**Files Modified:**
- `includes/session_manager.php` - Session configuration
- `admin/index.php` - Login logic with loop prevention

---

### Issue 2: User ID Shows 0 ✅ FIXED

**Symptoms:**
- User created in database but ID = 0
- Cannot login after creating account
- Admin account exists but ID is invalid

**Root Causes:**
1. AUTO_INCREMENT not properly set in table
2. Table created without explicit AUTO_INCREMENT start value
3. cPanel MySQL version differences

**Solution Applied:**
- ✅ Updated table schema to use INT UNSIGNED with explicit AUTO_INCREMENT=1
- ✅ Created fix-database.php script to repair existing tables
- ✅ Enhanced login validation to check for ID > 0
- ✅ Added session destruction if invalid ID detected

**Files Modified:**
- `admin/setup.php` - Updated table creation
- `admin/index.php` - Added ID validation
- `includes/functions.php` - Added ID check in requireLogin()

**Fix Script Created:**
- `fix-database.php` - Run this to repair your database

---

## 🚀 Quick Fix Steps

### Step 1: Run Database Fix Script
```
https://yourdomain.com/fix-database.php?verify=fix-db-2024
```

This will:
- ✅ Check and repair admin_users table
- ✅ Delete users with ID = 0
- ✅ Fix AUTO_INCREMENT
- ✅ Show current admin accounts
- ⚠️ **DELETE the file after running!**

### Step 2: Clear Browser Data
1. Open browser DevTools (F12)
2. Go to Application/Storage tab
3. Clear all cookies for your domain
4. Clear Local Storage and Session Storage
5. Close DevTools and refresh page

### Step 3: Recreate Admin Account (if needed)
If no valid admin exists:
```
https://yourdomain.com/admin/setup.php
```

Create a new admin account with:
- Valid email address
- Strong password (min 8 characters)
- Your name

### Step 4: Try Logging In
```
https://yourdomain.com/admin/
```

If still having issues, continue to advanced troubleshooting below.

---

## 🔍 Advanced Troubleshooting

### Check Session Configuration

1. **Verify .env settings:**
```env
# Make sure these are set correctly
APP_ENV=production
APP_DEBUG=false
SESSION_COOKIE_SECURE=true  # Only if using HTTPS
SESSION_LIFETIME=7200
SITE_URL=https://yourdomain.com  # YOUR actual domain
```

2. **Check session storage:**
- In cPanel, sessions are stored in `/tmp` by default
- Verify your account has write permissions
- Check session files: `ls -la /tmp/sess_*`

### Database Connection Issues

1. **Verify database credentials in .env:**
```env
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
```

2. **Test connection:**
Access: `https://yourdomain.com/system-health.php?verify=health-check-2024`

3. **Common cPanel database issues:**
- Database name usually has prefix (e.g., `username_synergex`)
- User must have ALL PRIVILEGES
- Verify from phpMyAdmin that tables exist

### Session Cookie Issues

1. **Check HTTPS:**
- If site uses HTTPS, ensure `SESSION_COOKIE_SECURE=true`
- If site uses HTTP, set `SESSION_COOKIE_SECURE=false`

2. **Clear cookies manually:**
- Chrome: chrome://settings/siteData
- Firefox: about:preferences#privacy
- Search for your domain and remove all cookies

3. **Test in Incognito/Private mode:**
- This ensures no old cookies interfere

### PHP Version Issues

1. **Check PHP version in cPanel:**
- Should be PHP 7.4 or higher
- Use MultiPHP Manager in cPanel
- Select PHP 8.0 or 8.1 for best compatibility

2. **Check PHP extensions:**
Required extensions:
- ✅ PDO
- ✅ PDO_MySQL
- ✅ mbstring
- ✅ session
- ✅ json

### File Permission Issues

1. **Check file permissions:**
```bash
# Directories should be 755
find . -type d -exec chmod 755 {} \;

# PHP files should be 644
find . -type f -name "*.php" -exec chmod 644 {} \;

# Make uploads writable
chmod 755 assets/images/uploads/
```

2. **Check ownership:**
- Files should be owned by your cPanel user
- Run: `chown -R username:username *`

---

## 🐛 Debug Mode

### Enable Debug Temporarily

1. **Edit .env:**
```env
APP_DEBUG=true
```

2. **Check error logs:**
- cPanel: Error Log viewer
- File: `public_html/error_log`
- Look for session or database errors

3. **Add debug output in admin/index.php:**
```php
// After login attempt
if (APP_DEBUG) {
    error_log('Login attempt: ' . $email);
    error_log('Admin found: ' . ($admin ? 'Yes' : 'No'));
    error_log('Admin ID: ' . ($admin['id'] ?? 'null'));
    error_log('Session ID after login: ' . SessionManager::getId());
    error_log('Admin ID in session: ' . SessionManager::get('admin_id'));
}
```

4. **Disable debug after fixing:**
```env
APP_DEBUG=false
```

---

## 📋 Checklist: Fresh Installation

If starting fresh on cPanel:

- [ ] Upload all files to public_html
- [ ] Create MySQL database in cPanel
- [ ] Create database user with ALL PRIVILEGES
- [ ] Add user to database
- [ ] Copy .env.example to .env
- [ ] Edit .env with database credentials
- [ ] Set SITE_URL to your actual domain
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Set SESSION_COOKIE_SECURE=true (if HTTPS)
- [ ] Run fix-database.php
- [ ] Create admin account via setup.php
- [ ] Test login
- [ ] Delete fix-database.php
- [ ] Delete security-check.php
- [ ] Delete system-health.php

---

## 🆘 Still Having Issues?

### Check These Files

1. **Session Manager Configuration:**
```php
// File: includes/session_manager.php
// Line ~29: Check domain setting
'domain' => '', // Should be empty string
```

2. **Login Logic:**
```php
// File: admin/index.php
// Check for proper validation:
if ($admin && isset($admin['id']) && $admin['id'] > 0)
```

3. **Database Connection:**
```php
// File: includes/db.php
// Verify DSN string:
"mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4"
```

### Common Error Messages

| Error | Solution |
|-------|----------|
| "Session initialization failed" | Run fix-database.php, check session permissions |
| "Invalid email or password" | Verify password, check user exists in DB |
| "Database connection failed" | Check .env credentials, test in phpMyAdmin |
| "redirected too many times" | Clear cookies, check session configuration |
| "Access denied" | Check file permissions, verify .htaccess |

---

## 🔒 Security After Fixing

Once everything works:

1. **Delete test files:**
```bash
rm fix-database.php
rm security-check.php
rm system-health.php
```

2. **Verify production settings:**
```env
APP_ENV=production
APP_DEBUG=false
SESSION_COOKIE_SECURE=true
```

3. **Check .htaccess is active:**
- Security headers should be present
- Directory browsing disabled
- Sensitive files protected

4. **Test security:**
- Try accessing .env (should be blocked)
- Try accessing includes/ directly (should be blocked)
- Verify HTTPS redirect works

---

## 📞 Quick Reference

### Important URLs
```
Admin Login:     /admin/
Admin Setup:     /admin/setup.php
Database Fix:    /fix-database.php?verify=fix-db-2024
Health Check:    /system-health.php?verify=health-check-2024
Security Check:  /security-check.php?verify=security-check-2024
```

### Database Queries

**Check users:**
```sql
SELECT id, email, name, created_at FROM admin_users;
```

**Delete invalid users:**
```sql
DELETE FROM admin_users WHERE id = 0;
```

**Reset auto_increment:**
```sql
ALTER TABLE admin_users AUTO_INCREMENT = 1;
```

**Create admin manually (replace values):**
```sql
INSERT INTO admin_users (email, password, name) 
VALUES ('admin@example.com', '$2y$10$HASHED_PASSWORD', 'Admin Name');
```

---

## ✅ Success Indicators

You'll know everything works when:
- ✅ Can access admin login page without redirect
- ✅ Can login with credentials
- ✅ Dashboard loads after login
- ✅ User ID in database is > 0
- ✅ Session persists between pages
- ✅ Can logout successfully
- ✅ All admin features work

---

**Last Updated:** December 28, 2025  
**Issue:** Redirect loop and ID=0 on cPanel  
**Status:** ✅ Fixed
