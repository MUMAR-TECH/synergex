# 🚀 cPanel Deployment Guide - Synergex Solutions

## Pre-Deployment Checklist

### 1. Database Setup on cPanel

1. **Create MySQL Database**
   - Log into cPanel → MySQL Databases
   - Create database: `username_synergex_db` (or your preferred name)
   - Create database user with a strong password
   - Add user to database with ALL PRIVILEGES

2. **Import Database**
   - Go to phpMyAdmin
   - Select your database
   - Import `synergex_db.sql` or `database.sql`
   - Verify tables are created successfully

3. **Note Your Database Credentials**
   ```
   DB_HOST: localhost (or specific hostname provided by host)
   DB_NAME: username_synergex_db
   DB_USER: username_dbuser
   DB_PASS: your_secure_password
   ```

### 2. File Upload to cPanel

#### Option A: File Manager Upload
1. Log into cPanel → File Manager
2. Navigate to `public_html` (or your domain's root)
3. Upload all files except:
   - `.git/` folder
   - `.env` file (create new on server)
   - `*.sql` files
   - Test/debug scripts

#### Option B: Git Deployment (Recommended)
1. Log into cPanel → Git Version Control
2. Clone your repository
3. Set deployment path to `public_html` or domain root
4. Pull latest code from main branch

### 3. Create .env File on cPanel

**IMPORTANT:** Create this file directly on the server, never upload it!

1. Go to File Manager → your site root
2. Create new file: `.env`
3. Copy content from `.env.example` and update values:

```env
# Database Configuration (UPDATE THESE!)
DB_HOST=localhost
DB_NAME=username_synergex_db
DB_USER=username_dbuser
DB_PASS=your_secure_database_password

# Site Configuration (UPDATE YOUR DOMAIN!)
SITE_URL=https://synergexsol.com
APP_ENV=production
APP_DEBUG=false

# WhatsApp Configuration
WHATSAPP_NUMBER=260770377471
WHATSAPP_MESSAGE="Hello Synergex! I would like to know more about your products."

# Email/SMTP Configuration (UPDATE WITH REAL CREDENTIALS)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=synergexsolutions25@gmail.com
SMTP_PASS=your_smtp_app_password
SMTP_FROM_EMAIL=synergexsolutions25@gmail.com
SMTP_FROM_NAME="Synergex Solutions"

# Security Settings
SESSION_LIFETIME=7200
SESSION_COOKIE_SECURE=true
SESSION_COOKIE_HTTPONLY=true

# Timezone
TIMEZONE=Africa/Lusaka

# Upload Configuration
MAX_UPLOAD_SIZE=52428800
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
ALLOWED_VIDEO_TYPES=mp4,webm,ogg,avi,mov
```

### 4. File Permissions

Set proper permissions via File Manager:

```
.env                    → 600 (read/write owner only)
config.php              → 644
includes/               → 755
admin/                  → 755
assets/images/uploads/  → 755 (writable for uploads)
.htaccess               → 644
```

### 5. SSL Certificate Setup

1. Go to cPanel → SSL/TLS Status
2. Install Let's Encrypt certificate (usually free and automatic)
3. Verify HTTPS is working: visit https://yourdomain.com
4. Once SSL is active, uncomment HTTPS redirect in `.htaccess`:
   ```apache
   # Force HTTPS
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### 6. Create Admin Account

1. Navigate to: `https://yourdomain.com/admin/setup.php`
2. Create your first admin account
3. **IMPORTANT:** Delete `admin/setup.php` immediately after creating account

### 7. Post-Deployment Testing

✅ Test these features:

- [ ] Homepage loads (https://yourdomain.com)
- [ ] Admin login (https://yourdomain.com/admin/)
- [ ] Upload images in admin panel
- [ ] Contact form submission
- [ ] Quote request form
- [ ] Newsletter subscription
- [ ] All navigation links work
- [ ] Mobile menu functions
- [ ] Gallery loads properly

## Common cPanel Issues & Solutions

### Issue 1: HTTP 500 Error
**Cause:** Missing or invalid .env file

**Solution:**
1. Check if `.env` file exists in site root
2. Verify all required variables are set
3. Check file permissions (should be 600 or 644)
4. Enable error display temporarily:
   ```php
   // In config.php, temporarily add:
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

### Issue 2: Database Connection Error
**Cause:** Wrong database credentials or hostname

**Solution:**
1. Verify credentials in `.env` match cPanel database
2. Check database user has ALL PRIVILEGES
3. Test connection with phpMyAdmin
4. Some hosts use different hostname (check cPanel documentation)

### Issue 3: Redirect Loop / Session Issues
**Cause:** Session cookie configuration incompatible with shared hosting

**Solution:**
✅ Already fixed in code:
- Session domain set to empty string (subdomain compatible)
- HTTPS detection includes `X-Forwarded-Proto` header
- Fingerprint validation disabled in production mode

### Issue 4: "Admin ID is 0" Error
**Cause:** AUTO_INCREMENT not set correctly in database

**Solution:**
1. Go to phpMyAdmin → select your database
2. Run this SQL:
   ```sql
   ALTER TABLE admin_users MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT;
   ALTER TABLE admin_users AUTO_INCREMENT = 1;
   ```

### Issue 5: File Upload Fails
**Cause:** Directory not writable or PHP limits too low

**Solution:**
1. Set `assets/images/uploads/` to 755 or 775
2. Check `.htaccess` upload limits (currently 50MB)
3. Ask host to increase limits if needed
4. Verify disk space quota in cPanel

### Issue 6: Email Not Sending
**Cause:** SMTP credentials invalid or port blocked

**Solution:**
1. Use Gmail App Password (not regular password)
2. Some hosts block port 587, try port 465 with SSL
3. Verify `SMTP_PASS` in `.env` is correct
4. Check host doesn't require mail() function instead

## Security Checklist

Before going live, verify:

- [x] `.env` file permissions set to 600
- [ ] Delete all test/debug scripts:
  - `reset-password.php`
  - `check-database.php`  
  - `test-login.php`
  - `fix-database.php`
  - `security-check.php`
  - `system-health.php`
- [ ] `admin/setup.php` deleted after first use
- [ ] SSL certificate installed and HTTPS working
- [ ] Strong database password set
- [ ] Strong admin password set
- [ ] `APP_DEBUG=false` in production `.env`
- [ ] Error logging enabled, display disabled
- [ ] Regular database backups scheduled
- [ ] File permissions properly set
- [ ] `.htaccess` protects sensitive files

## Maintenance Commands

### Backup Database
```bash
# Via cPanel phpMyAdmin → Export
# Or via SSH:
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

### Update from Git
```bash
# cPanel → Git Version Control → Pull/Deploy
# Or via SSH:
cd public_html
git pull origin main
```

### Clear Session Data
```bash
# Via File Manager, delete session files in:
# /tmp or session.save_path location
```

## Performance Optimization

### Enable OPcache (if available)
Add to `.htaccess` or ask host to enable:
```apache
php_value opcache.enable 1
php_value opcache.memory_consumption 128
```

### Database Optimization
Run periodically in phpMyAdmin:
```sql
OPTIMIZE TABLE admin_users, products, achievements, gallery, messages;
```

### Image Optimization
- Compress images before upload
- Use WebP format when possible
- Enable browser caching (already in `.htaccess`)

## Support Resources

- **cPanel Documentation:** https://docs.cpanel.net/
- **PHP Version:** Recommended 8.0+ (check in cPanel → Select PHP Version)
- **Error Logs:** cPanel → Errors → Error Log
- **File Manager:** Access all files via cPanel → File Manager

## Quick Reference: Important Paths

```
Site Root:           /home/username/public_html/
.env file:           /home/username/public_html/.env
Uploads:             /home/username/public_html/assets/images/uploads/
Error Log:           /home/username/public_html/error_log
Admin Panel:         https://yourdomain.com/admin/
```

---

**🎉 Deployment Complete!**

Your Synergex Solutions application should now be running smoothly on cPanel hosting.

If you encounter any issues not covered here, check the error log in cPanel and refer to the troubleshooting section above.
