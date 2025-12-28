# 🚀 Production Deployment Guide for Synergex Solutions

## Prerequisites
- cPanel hosting account with PHP 7.4 or higher
- MySQL database access
- Domain name configured

## 📋 Deployment Steps

### 1️⃣ Prepare Your Files

1. **Download/Export your project files** from your local development environment
2. **Compress the files** into a ZIP archive (if not already done)
   - Exclude: `.git`, `node_modules`, `error_log`, `.env`

### 2️⃣ Upload to cPanel

1. **Login to your cPanel account**
2. **Navigate to File Manager**
3. **Go to public_html** (or your domain's root directory)
4. **Upload the project ZIP file**
5. **Extract the ZIP file** using cPanel's Extract feature
6. **Move all files** from the extracted folder to public_html root
   - Final structure should be: `public_html/index.php`, `public_html/admin/`, etc.

### 3️⃣ Create MySQL Database

1. **Navigate to MySQL Databases** in cPanel
2. **Create a new database**
   - Example: `username_synergex`
3. **Create a database user**
   - Example: `username_synergex_user`
   - Set a strong password
4. **Add user to database** with ALL PRIVILEGES
5. **Note down:**
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### 4️⃣ Import Database

1. **Navigate to phpMyAdmin** in cPanel
2. **Select your database** from the left sidebar
3. **Click on "Import" tab**
4. **Choose file:** `synergex_db.sql`
5. **Click "Go"** to import
6. **Verify** that all tables were created successfully

### 5️⃣ Configure Environment Variables

1. **Rename `.env.example` to `.env`**
   ```bash
   # In File Manager, rename the file
   ```

2. **Edit the .env file** with your production values:
   ```env
   # Database Configuration
   DB_HOST=localhost
   DB_NAME=your_database_name
   DB_USER=your_database_user
   DB_PASS=your_database_password

   # Site Configuration (IMPORTANT - use your actual domain)
   SITE_URL=https://yourdomain.com
   APP_ENV=production
   APP_DEBUG=false

   # WhatsApp Configuration
   WHATSAPP_NUMBER=260770377471
   WHATSAPP_MESSAGE="Hello Synergex! I would like to know more about your products."

   # Email/SMTP Configuration (configure your email service)
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_USER=your-email@gmail.com
   SMTP_PASS=your-app-password
   SMTP_FROM_EMAIL=your-email@gmail.com
   SMTP_FROM_NAME="Synergex Solutions"

   # Security Settings
   SESSION_LIFETIME=7200
   SESSION_COOKIE_SECURE=true
   SESSION_COOKIE_HTTPONLY=true

   # Timezone
   TIMEZONE=Africa/Lusaka
   ```

### 6️⃣ Set File Permissions

Using File Manager or FTP:
- **Directories:** `755`
  - `admin/`, `api/`, `assets/`, `includes/`, `error_pages/`
- **PHP Files:** `644`
  - `*.php`
- **Uploads Directory:** `755` (needs write permission)
  - `assets/images/uploads/`
- **Config Files:** `644`
  - `.env`, `.htaccess`, `config.php`

### 7️⃣ Setup Admin Account

1. **Access phpMyAdmin**
2. **Go to `admin_users` table**
3. **Insert admin user or update existing:**
   ```sql
   INSERT INTO admin_users (name, email, password, created_at) 
   VALUES ('Admin', 'admin@yourdomain.com', '$2y$10$HASHED_PASSWORD', NOW());
   ```
   
   Or generate password hash using PHP:
   ```php
   <?php echo password_hash('YourSecurePassword123!', PASSWORD_DEFAULT); ?>
   ```

4. **Remove the password generation script** after use

### 8️⃣ SSL Certificate Configuration

1. **Navigate to SSL/TLS** in cPanel
2. **Install SSL certificate** (Let's Encrypt is free)
3. **Force HTTPS** by uncommenting in `.htaccess`:
   ```apache
   # Uncomment these lines:
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### 9️⃣ Test Your Deployment

1. **Visit your website:** `https://yourdomain.com`
2. **Test admin login:** `https://yourdomain.com/admin`
3. **Check all pages:**
   - Home, About, Products, Gallery, Contact
   - Achievement details, Product details
4. **Test forms:**
   - Contact form
   - Quote request
   - Newsletter subscription
5. **Test file uploads** in admin panel
6. **Verify database connections**

### 🔟 Post-Deployment Checklist

- [ ] ✅ Site loads without errors
- [ ] ✅ All images and assets load correctly
- [ ] ✅ Admin panel is accessible and functional
- [ ] ✅ Database connections work
- [ ] ✅ Forms submit successfully
- [ ] ✅ Email notifications work
- [ ] ✅ File uploads work in admin
- [ ] ✅ SSL certificate is active (HTTPS)
- [ ] ✅ Error pages display correctly (404, 500)
- [ ] ✅ WhatsApp integration works

## 🔒 Security Checklist

- [ ] ✅ `.env` file is NOT accessible via browser
- [ ] ✅ `APP_DEBUG` is set to `false`
- [ ] ✅ Database credentials are secure
- [ ] ✅ Admin password is strong
- [ ] ✅ File permissions are correct
- [ ] ✅ SSL certificate is installed
- [ ] ✅ Security headers are active
- [ ] ✅ Directory browsing is disabled

## 🐛 Troubleshooting

### Database Connection Errors
1. Verify database credentials in `.env`
2. Check database user has proper privileges
3. Confirm database host is correct (usually `localhost`)

### 500 Internal Server Error
1. Check `error_log` file in root directory
2. Verify PHP version compatibility (7.4+)
3. Check file permissions
4. Enable debug mode temporarily: `APP_DEBUG=true`

### Assets Not Loading (CSS/JS/Images)
1. Verify `SITE_URL` in `.env` matches your actual domain
2. Check `.htaccess` file is present
3. Clear browser cache
4. Check file paths are correct

### Email Not Sending
1. Configure SMTP settings correctly
2. For Gmail: Enable 2FA and create App Password
3. Check spam folder
4. Verify SMTP port is open (587 or 465)

### Admin Login Not Working
1. Verify admin user exists in database
2. Check session settings
3. Clear browser cookies
4. Verify password hash in database

## 📝 Important Notes

1. **NEVER commit `.env` to version control**
2. **Always use HTTPS in production**
3. **Keep regular database backups**
4. **Monitor error logs regularly**
5. **Update dependencies periodically**

## 📧 Support

For deployment issues, check:
- Error log: `public_html/error_log`
- PHP error log in cPanel
- Database connection in phpMyAdmin

## 🔄 Updating Your Site

1. **Backup database and files**
2. **Upload new files via File Manager**
3. **Run any new SQL migrations**
4. **Clear cache if applicable**
5. **Test functionality**

---

**Deployed Successfully?** 🎉
Access your admin panel at: `https://yourdomain.com/admin`

---

**Last Updated:** December 2025
**Version:** 1.0.0
