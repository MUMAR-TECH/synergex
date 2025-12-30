# 🚀 Quick cPanel Deployment Checklist

## Before You Deploy

- [ ] Test application works perfectly on localhost
- [ ] Commit all changes to Git repository
- [ ] Create `.gitignore` file (already created)
- [ ] Remove test/debug scripts from repository

## On cPanel Server

### 1. Database Setup (5 minutes)
```
1. cPanel → MySQL Databases
2. Create database: username_synergex_db
3. Create user with strong password
4. Add user to database (ALL PRIVILEGES)
5. Note down: host, database name, username, password
```

### 2. Import Database (3 minutes)
```
1. cPanel → phpMyAdmin
2. Select your database
3. Import → Choose synergex_db.sql
4. Verify tables created
```

### 3. Upload Files (10 minutes)

**Option A - Git (Recommended)**
```
1. cPanel → Git Version Control
2. Clone: https://github.com/MUMAR-TECH/synergex.git
3. Set path: public_html (or your domain root)
4. Pull latest code
```

**Option B - File Manager**
```
1. cPanel → File Manager
2. Upload all files to public_html
3. Exclude: .git, .env, *.sql files
```

### 4. Create .env File (5 minutes)
```
1. File Manager → public_html
2. Create new file: .env
3. Copy from .env.cpanel template
4. Update these values:
   - DB_NAME (your database name)
   - DB_USER (your database user)
   - DB_PASS (your database password)
   - SITE_URL (https://yourdomain.com)
   - SMTP_PASS (Gmail app password)
5. Set permissions: 600
```

**Critical .env Settings:**
```env
DB_HOST=localhost
DB_NAME=username_synergex_db       # ← Update
DB_USER=username_dbuser            # ← Update
DB_PASS=your_password              # ← Update
SITE_URL=https://synergexsol.com   # ← Update
APP_ENV=production
APP_DEBUG=false
SESSION_COOKIE_SECURE=true
```

### 5. Set File Permissions (2 minutes)
```
.env                          → 600
assets/images/uploads/        → 755
All other files               → 644
All directories               → 755
```

### 6. SSL Certificate (5 minutes)
```
1. cPanel → SSL/TLS Status
2. Run AutoSSL (Let's Encrypt - Free)
3. Wait for installation
4. Test: https://yourdomain.com
5. If works, edit .htaccess to force HTTPS
```

### 7. Create First Admin (2 minutes)
```
1. Visit: https://yourdomain.com/admin/setup.php
2. Fill in admin details
3. Create account
4. DELETE setup.php immediately after!
```

### 8. Verify Deployment (3 minutes)
```
1. Upload verify-deployment.php to site root
2. Visit: https://yourdomain.com/verify-deployment.php
3. Check all items pass
4. DELETE verify-deployment.php after verification!
```

## Test Everything

- [ ] Homepage loads: https://yourdomain.com
- [ ] Admin login works: https://yourdomain.com/admin
- [ ] Upload image in admin panel
- [ ] Submit contact form
- [ ] Request quote
- [ ] Subscribe to newsletter
- [ ] Check mobile menu

## Security - IMPORTANT!

Delete these files after deployment:
```bash
admin/setup.php              # Delete after creating admin
verify-deployment.php        # Delete after verification
reset-password.php           # Never upload
check-database.php          # Never upload
test-login.php              # Never upload
fix-database.php            # Never upload
```

## Common Issues & Quick Fixes

### "HTTP 500 Error"
→ Missing .env file or wrong syntax
→ Check error_log in cPanel

### "Database Connection Error"  
→ Wrong credentials in .env
→ Check database name prefix (username_)

### "Redirect Loop"
→ Already fixed in code (session domain)
→ Should not occur with current version

### "Admin ID is 0"
→ Database not imported correctly
→ Re-import synergex_db.sql

### "Can't Upload Images"
→ Permissions on uploads folder
→ Set to 755: assets/images/uploads/

## Need Help?

1. Check error log: cPanel → Errors
2. Enable debug: Set APP_DEBUG=true in .env (temporarily)
3. Read full guide: CPANEL_DEPLOYMENT.md
4. Contact host support for PHP/MySQL issues

---

**Total Time: ~35 minutes**

✅ Application should now be live on cPanel!

🎉 Visit: https://synergexsol.com
🔐 Admin: https://synergexsol.com/admin
