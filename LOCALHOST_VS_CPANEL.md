# 📋 Synergex Solutions - cPanel vs Localhost Configuration

## Key Differences Between Environments

### Localhost Configuration (.env)
```env
# Database
DB_HOST=localhost
DB_NAME=synergex_db
DB_USER=root
DB_PASS=

# Site
SITE_URL=http://localhost/synergex
APP_ENV=development
APP_DEBUG=true
SESSION_COOKIE_SECURE=false
```

### cPanel Production (.env)
```env
# Database - MUST MATCH YOUR CPANEL DATABASE!
DB_HOST=localhost
DB_NAME=username_synergex_db        # ← Prefixed with cPanel username
DB_USER=username_dbuser             # ← Your database user
DB_PASS=strong_secure_password123   # ← Strong password

# Site - MUST MATCH YOUR DOMAIN!
SITE_URL=https://synergexsol.com   # ← Your actual domain with HTTPS
APP_ENV=production
APP_DEBUG=false
SESSION_COOKIE_SECURE=true
```

## Why Your Application Works on Localhost but Not cPanel

### Common Causes:

#### 1. Missing .env File
**Problem:** .env exists on localhost but not uploaded to cPanel (correctly - it should never be committed to Git)

**Solution:** Create .env manually on cPanel with production values

**Symptoms:**
- HTTP 500 Error
- "Configuration Error" message
- Blank white page

#### 2. Wrong Database Credentials
**Problem:** cPanel databases have prefixed names (username_dbname) and different passwords

**Solution:** Use exact database name from cPanel → MySQL Databases

**Symptoms:**
- "Could not connect to database"
- "Access denied for user"
- HTTP 500 Error

#### 3. Database Not Imported
**Problem:** Files uploaded but database not imported from .sql file

**Solution:** Import synergex_db.sql via phpMyAdmin

**Symptoms:**
- "Table doesn't exist"
- "No admin accounts found"
- Can't login even with correct password

#### 4. File Permissions
**Problem:** Localhost doesn't care about permissions, cPanel does

**Solution:** Set correct permissions:
- .env → 600 (read/write owner only)
- uploads/ → 755 (writable)
- Other files → 644
- Directories → 755

**Symptoms:**
- Can't upload images
- "Permission denied" errors
- Can't write to session files

#### 5. HTTPS/SSL Configuration
**Problem:** Localhost uses HTTP, production should use HTTPS

**Solution:** 
1. Install SSL certificate in cPanel
2. Set SITE_URL to https://
3. Set SESSION_COOKIE_SECURE=true

**Symptoms:**
- Redirect loops
- Session not persisting
- "Site not secure" warnings

#### 6. PHP Version Mismatch
**Problem:** Different PHP versions between localhost and cPanel

**Solution:** Check cPanel → Select PHP Version, use 7.4 or higher

**Symptoms:**
- Syntax errors
- Function not found
- HTTP 500 Error

## What's Already Fixed for cPanel Compatibility

Your application includes these cPanel-specific fixes:

### ✅ Session Management (includes/session_manager.php)
```php
// Empty domain for subdomain compatibility
'domain' => '',  // Was causing redirect loops

// HTTPS detection with proxy support
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
          (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
           $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// Fingerprint validation only in debug mode
if (defined('APP_DEBUG') && APP_DEBUG) {
    // Check fingerprint
}
```

### ✅ Error Handling (includes/db.php)
```php
// Production-safe error messages
if (APP_DEBUG) {
    die("Database Error: " . $e->getMessage());
} else {
    error_log("Database Error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}
```

### ✅ Security Headers (.htaccess)
```apache
# All security headers already configured
# XSS Protection, Clickjacking prevention, etc.
```

### ✅ AUTO_INCREMENT Fix (admin/setup.php)
```sql
CREATE TABLE admin_users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    -- Fixed the ID=0 issue
)
```

## Step-by-Step: Making It Work on cPanel

### Phase 1: Database Setup
```bash
1. Login to cPanel
2. MySQL Databases → Create Database
   Name: synergex_db (it becomes: username_synergex_db)
3. Create User → Strong password
4. Add User to Database → ALL PRIVILEGES
5. Note full database name: "username_synergex_db"
```

### Phase 2: Import Database
```bash
1. cPanel → phpMyAdmin
2. Select: username_synergex_db
3. Import Tab
4. Choose file: synergex_db.sql
5. Execute
6. Verify tables created (admin_users, products, etc.)
```

### Phase 3: Upload Files
```bash
METHOD 1 - Git (Recommended):
1. cPanel → Git Version Control
2. Clone Repository
3. Path: /home/username/public_html
4. Branch: main

METHOD 2 - File Manager:
1. cPanel → File Manager
2. Upload ZIP of your project
3. Extract to public_html
4. Delete .git folder if present
```

### Phase 4: Configure Environment
```bash
1. File Manager → public_html
2. Create file: .env
3. Copy from .env.cpanel template
4. Replace ALL "YOUR_*" placeholders:
   - Database name (with prefix!)
   - Database user
   - Database password
   - Site URL (with https://)
   - SMTP password (Gmail app password)
5. Save file
6. Permissions → 600
```

### Phase 5: SSL Certificate
```bash
1. cPanel → SSL/TLS Status
2. Click "Run AutoSSL"
3. Wait 5 minutes for certificate
4. Test: https://yourdomain.com
5. If working, force HTTPS in .htaccess:
   Uncomment these lines:
   # RewriteCond %{HTTPS} off
   # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Phase 6: Create Admin Account
```bash
1. Visit: https://yourdomain.com/admin/setup.php
2. Fill form:
   - Name: Admin
   - Email: admin@synergexsol.com
   - Password: Strong password!
3. Create Account
4. IMMEDIATELY delete setup.php via File Manager!
```

### Phase 7: Verify Everything
```bash
1. Upload verify-deployment.php
2. Visit: https://yourdomain.com/verify-deployment.php
3. Check all green checkmarks
4. Fix any warnings/errors
5. DELETE verify-deployment.php
```

## Troubleshooting Guide

### Error: "HTTP 500"
```bash
Cause: Missing/invalid .env or PHP error
Fix:
1. Check .env exists: ls -la .env
2. Check .env syntax: cat .env
3. Check error log: cPanel → Errors
4. Temporarily enable debug in .env:
   APP_DEBUG=true
```

### Error: "Database Connection Failed"
```bash
Cause: Wrong credentials in .env
Fix:
1. Go to cPanel → MySQL Databases
2. Verify database name (includes prefix!)
3. Verify user has access
4. Test in phpMyAdmin
5. Update .env with exact values
```

### Error: "Redirect Loop"
```bash
Cause: Session domain or HTTPS mismatch
Fix:
✅ Already fixed in session_manager.php
If still occurs:
1. Clear browser cookies
2. Check SITE_URL in .env matches actual URL
3. Verify SSL certificate is active
```

### Error: "Admin ID is 0"
```bash
Cause: AUTO_INCREMENT not set
Fix:
1. phpMyAdmin → admin_users table
2. Structure Tab
3. Check ID field: should be AUTO_INCREMENT
4. If not, run SQL:
   ALTER TABLE admin_users 
   MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT;
```

### Error: "Invalid Email or Password"
```bash
Cause: No admin account or wrong password
Fix:
1. Verify account exists in phpMyAdmin
2. If exists, password might be wrong
3. Create new account via setup.php
4. Or reset password via reset-password.php
```

### Error: "Can't Upload Images"
```bash
Cause: Directory not writable
Fix:
1. File Manager → assets/images/uploads
2. Permissions → 755 or 775
3. Check parent folders also 755
```

## Environment Comparison Table

| Feature | Localhost | cPanel Production |
|---------|-----------|-------------------|
| Database Name | `synergex_db` | `username_synergex_db` |
| Database Password | Empty or simple | Strong password required |
| Site URL | `http://localhost/synergex` | `https://synergexsol.com` |
| SSL/HTTPS | Not required | Required (recommended) |
| File Permissions | Not enforced | Strictly enforced |
| Error Display | Enabled (debug) | Disabled (production) |
| Session Security | Relaxed | Strict |
| PHP Version | XAMPP (8.2.12) | Host-provided (7.4+) |
| .env File | In project folder | Created manually on server |
| Debug Mode | ON (true) | OFF (false) |
| Cookie Secure | false (HTTP) | true (HTTPS) |

## Critical Files Checklist

Files that MUST exist on cPanel:
```
✅ .env (created manually, not uploaded)
✅ .htaccess (uploaded from repository)
✅ config.php (uploaded from repository)
✅ includes/session_manager.php (uploaded)
✅ includes/db.php (uploaded)
✅ includes/env.php (uploaded)
✅ admin/index.php (uploaded)
```

Files that should NOT exist on cPanel:
```
❌ .env.example (info only, not needed)
❌ .git/ (Git folder, not needed)
❌ *.sql (database dumps, not needed after import)
❌ reset-password.php (security risk!)
❌ check-database.php (debug tool)
❌ test-login.php (debug tool)
❌ verify-deployment.php (delete after use)
❌ admin/setup.php (delete after first admin)
```

## Final Verification Checklist

Before marking deployment complete:

- [ ] .env file created with correct values
- [ ] Database imported successfully
- [ ] SSL certificate installed and working
- [ ] Homepage loads: https://yourdomain.com
- [ ] Admin login works: https://yourdomain.com/admin
- [ ] Can upload images in admin panel
- [ ] Contact form sends emails
- [ ] Quote form works
- [ ] Newsletter subscription works
- [ ] Mobile menu functions correctly
- [ ] All pages load without errors
- [ ] setup.php deleted
- [ ] verify-deployment.php deleted
- [ ] Debug scripts deleted
- [ ] APP_DEBUG=false in .env
- [ ] File permissions correct (600 for .env, 755 for uploads)

## Support Resources

- **Full Deployment Guide:** CPANEL_DEPLOYMENT.md
- **Quick Checklist:** QUICK_DEPLOY.md  
- **Session Management:** SESSION_MANAGEMENT.md
- **cPanel Documentation:** https://docs.cpanel.net/
- **Error Logs:** cPanel → Errors → Error Log

---

## Summary

**The main difference between localhost and cPanel is:**

1. **Database credentials** - cPanel uses prefixed names and requires passwords
2. **Environment file** - Must be created manually on cPanel with production values
3. **HTTPS/SSL** - Production requires SSL and secure cookies
4. **File permissions** - cPanel enforces strict permissions
5. **Debug mode** - Must be OFF in production

**Your code is already cPanel-compatible!** The session management, error handling, and security features have been designed to work on shared hosting. You just need to:

1. Create .env with correct cPanel database credentials
2. Import the database
3. Install SSL certificate
4. Set proper file permissions

Follow QUICK_DEPLOY.md for fastest deployment (35 minutes total).

🚀 Your application will work perfectly on cPanel once the environment is properly configured!
