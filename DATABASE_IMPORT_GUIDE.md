# 📦 Database Import Instructions for cPanel

## File Information

**Export File:** `synergex_cpanel_export.sql`  
**File Size:** ~67 KB  
**Total Tables:** 20  
**Total Records:** 250+  
**Character Encoding:** UTF-8 (utf8mb4)  
**Compatibility:** phpMyAdmin 4.x and 5.x on cPanel

## What's Included

This export contains the complete Synergex database with:

✅ **All Table Structures** - Complete CREATE TABLE statements  
✅ **All Data** - Every record from all tables  
✅ **Indexes & Keys** - Primary keys, foreign keys, unique keys  
✅ **AUTO_INCREMENT Values** - Proper sequence continuation  
✅ **Character Sets** - UTF-8 encoding for international characters  
✅ **Default Values** - Column defaults and NULL settings  
✅ **Timestamps** - Created/updated timestamps preserved  

### Tables Included (20 total):

| Table Name | Records | Purpose |
|------------|---------|---------|
| achievements | 8 | Company achievements and milestones |
| admin_users | 4 | Admin panel users |
| chatbot_conversations | 33 | Chatbot conversation history |
| chatbot_knowledge | 30 | Chatbot knowledge base |
| chatbot_messages | 41 | Individual chat messages |
| chatbot_settings | 7 | Chatbot configuration |
| contact_messages | 13 | Contact form submissions |
| dashboard_stats | 1 | Dashboard statistics cache |
| gallery | 11 | Image gallery items |
| hero_slider | 5 | Homepage slider images |
| impact_stats | 13 | Environmental impact statistics |
| page_content | 19 | CMS page content |
| partners | 6 | Partner organizations |
| product_audit_log | 0 | Product change history (empty) |
| products | 8 | Product catalog |
| quote_requests | 12 | Customer quote requests |
| recent_activity | 10 | System activity log |
| site_settings | 8 | Site configuration |
| subscribers | 6 | Newsletter subscribers |
| what_we_do | 3 | Services/offerings |

## Import to cPanel - Step by Step

### Step 1: Create Database (2 minutes)

1. Login to **cPanel**
2. Find and click **MySQL® Databases**
3. Under "Create New Database"
   - Enter name: `synergex_db` (it will become `username_synergex_db`)
   - Click **Create Database**
4. Note the full database name shown (e.g., `cpanel_synergex_db`)

### Step 2: Create Database User (2 minutes)

1. Scroll to **MySQL Users** section
2. Under "Add New User"
   - Username: `synergex_user`
   - Password: Click **Generate Password** (save this!)
   - Click **Create User**
3. Copy the generated password to a safe place

### Step 3: Add User to Database (1 minute)

1. Scroll to **Add User To Database** section
2. User: Select `synergex_user`
3. Database: Select your `synergex_db`
4. Click **Add**
5. Check **ALL PRIVILEGES**
6. Click **Make Changes**

### Step 4: Import via phpMyAdmin (5 minutes)

1. Go back to cPanel home
2. Find and click **phpMyAdmin**
3. In left sidebar, click your database (e.g., `cpanel_synergex_db`)
4. Click **Import** tab at the top
5. Click **Choose File**
6. Select `synergex_cpanel_export.sql` from your computer
7. Scroll down
8. Keep default settings:
   - Format: SQL
   - Character set: utf8mb4
   - Partial import: Disabled
9. Click **Go** at bottom
10. Wait for "Import has been successfully finished" message

### Step 5: Verify Import (2 minutes)

After import completes, verify:

1. Check left sidebar shows 20 tables
2. Click on `admin_users` table
3. Should see 4 admin accounts
4. Click on `products` table
5. Should see 8 products
6. Click on `site_settings` table
7. Should see 8 configuration rows

**✅ If you see all tables and data, import was successful!**

## Update Your Application

After importing the database, update your `.env` file on cPanel:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=cpanel_synergex_db        # ← Your actual database name from Step 1
DB_USER=cpanel_synergex_user      # ← Your database user from Step 2
DB_PASS=generated_password_here   # ← Password from Step 2

# Site Configuration
SITE_URL=https://synergexsol.com
APP_ENV=production
APP_DEBUG=false
SESSION_COOKIE_SECURE=true
```

## Troubleshooting

### Import Error: "Maximum execution time exceeded"

**Cause:** Large file or slow server  
**Solution:**
1. Edit import settings in phpMyAdmin
2. Enable "Partial import" option
3. Or split the file into smaller parts
4. Or ask host to increase PHP time limit

### Import Error: "Unknown collation: utf8mb4_unicode_ci"

**Cause:** Old MySQL version  
**Solution:**
1. Your host needs MySQL 5.7+ or MariaDB 10.0+
2. Contact host support to upgrade
3. Or edit the SQL file and replace `utf8mb4_unicode_ci` with `utf8_general_ci`

### Import Error: "Access denied"

**Cause:** User doesn't have privileges  
**Solution:**
1. Go back to cPanel → MySQL Databases
2. Find your user under "Current Users"
3. Click "Privileges" next to database
4. Make sure ALL PRIVILEGES are checked
5. Click "Make Changes"

### Import Error: "Table already exists"

**Cause:** Database already has tables  
**Solution:**
1. Either drop all existing tables first
2. Or create a fresh empty database
3. The export file includes `DROP TABLE IF EXISTS` so should work

### Warning: "Some data may have been lost"

**Cause:** Data type conversion issues  
**Solution:**
1. Usually safe to ignore for this export
2. Verify critical tables (products, admin_users) have data
3. If data is missing, try importing again with different character set

## Testing After Import

Verify your application works:

```bash
✅ Visit: https://yourdomain.com
   - Homepage should load with slider images

✅ Visit: https://yourdomain.com/admin
   - Should see login page
   - Try logging in with existing credentials

✅ Visit: https://yourdomain.com/products.php
   - Should see 8 products with images

✅ Visit: https://yourdomain.com/gallery.php
   - Should see gallery images

✅ Visit: https://yourdomain.com/achievements.php
   - Should see 8 achievements
```

## Important Notes

⚠️ **Before Import:**
- Make sure you have a backup of any existing data
- The export file will DROP existing tables (if any)
- All data in the export file will replace existing data

✅ **After Import:**
- Update `.env` file with new database credentials
- Delete `export-database-cpanel.php` from server (security)
- Test all functionality before going live
- Keep a backup copy of the SQL file

🔒 **Security:**
- Never commit the exported SQL file to Git
- Store backup securely (encrypted if possible)
- Delete SQL files from server after import
- Use strong database passwords

## Export Details

**Generated:** December 29, 2025  
**Source:** localhost/synergex  
**MySQL Version:** 10.4.32-MariaDB  
**PHP Version:** 8.2.12  
**Export Method:** Custom PHP script with full data preservation  

## Need Help?

If you encounter issues not covered here:

1. Check phpMyAdmin error log in cPanel
2. Verify MySQL version is 5.7+ or MariaDB 10.0+
3. Check PHP version is 7.4+
4. Contact your hosting provider support
5. Provide them with the error message from phpMyAdmin

---

**✨ Your database is ready for cPanel import!**

The export file `synergex_cpanel_export.sql` contains everything needed to run your Synergex application on cPanel with all data intact.
