# 🔧 Chatbot Troubleshooting Guide

## Quick Fix Steps

### Step 1: Import Database Tables
The chatbot needs 4 database tables. Run this command:

```bash
# In Command Prompt or PowerShell
cd C:\xampp\htdocs\synergex
C:\xampp\mysql\bin\mysql -u root -p synergex_db < database_chatbot.sql
```

Or using phpMyAdmin:
1. Go to http://localhost/phpmyadmin
2. Select `synergex_db` database
3. Click "Import" tab
4. Choose `database_chatbot.sql`
5. Click "Go"

### Step 2: Test Database Connection
Visit: http://localhost/synergex/test_chatbot_db.php

You should see:
- ✅ Table 'chatbot_conversations' exists with 0 records
- ✅ Table 'chatbot_messages' exists with 0 records
- ✅ Table 'chatbot_knowledge' exists with 15 records
- ✅ Table 'chatbot_settings' exists with 7 records

### Step 3: Clear Browser Cache
1. Press Ctrl+Shift+Delete
2. Clear cached images and files
3. Refresh the page (Ctrl+F5)

### Step 4: Check Browser Console
1. Press F12 to open Developer Tools
2. Go to "Console" tab
3. Try chatting with the bot
4. Look for error messages

## Common Issues & Solutions

### Issue 1: "Sorry, I encountered an error"
**Cause:** Database tables not created
**Solution:** Run Step 1 above

### Issue 2: Chatbot doesn't appear
**Cause:** JavaScript not loading
**Solution:** 
- Clear cache (Ctrl+F5)
- Check console for errors (F12)
- Verify files exist:
  - assets/css/chatbot.css
  - assets/js/chatbot.js

### Issue 3: "Failed to fetch" error
**Cause:** API path incorrect
**Solution:** Already fixed! The API now uses dynamic URLs.

### Issue 4: Blank responses
**Cause:** Knowledge base empty
**Solution:** Database import will add 15+ pre-loaded Q&As

### Issue 5: Network error
**Cause:** Apache/MySQL not running
**Solution:**
1. Open XAMPP Control Panel
2. Start Apache (if not green)
3. Start MySQL (if not green)

## Verify Installation

Run these checks:

### 1. Files Exist
```
✓ api/chatbot.php
✓ assets/css/chatbot.css
✓ assets/js/chatbot.js
✓ admin/chatbot.php
✓ database_chatbot.sql
```

### 2. Database Tables
Run in phpMyAdmin SQL tab:
```sql
SHOW TABLES LIKE 'chatbot%';
```
Should show 4 tables.

### 3. Knowledge Base
Run in phpMyAdmin SQL tab:
```sql
SELECT COUNT(*) FROM chatbot_knowledge WHERE is_active = 1;
```
Should return 15 or more.

### 4. API Response Test
Open in browser:
http://localhost/synergex/api/chatbot.php

You should NOT see PHP errors. You might see:
```json
{"success":false,"message":"Invalid action"}
```
This is actually CORRECT! It means the API is working.

## Debug Mode

### Enable Error Display
Edit `api/chatbot.php`, add at top after `<?php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check PHP Error Log
Location: `C:\xampp\apache\logs\error.log`

Open and look for recent errors related to chatbot.

## Test Chatbot Manually

### 1. Test Init
Open browser console (F12), paste:
```javascript
fetch('/synergex/api/chatbot.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({action: 'init'})
})
.then(r => r.json())
.then(d => console.log(d));
```

Expected response:
```json
{
  "success": true,
  "session_id": "chat_...",
  "conversation_id": 1,
  "greeting": "Hello! 👋..."
}
```

### 2. Test Message
After init, paste:
```javascript
fetch('/synergex/api/chatbot.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        action: 'send_message',
        message: 'What products do you offer?',
        session_id: 'test_123'
    })
})
.then(r => r.json())
.then(d => console.log(d));
```

Expected response:
```json
{
  "success": true,
  "response": "We specialize in eco-friendly...",
  "intent": "products",
  "suggestions": [...]
}
```

## Still Having Issues?

### Check These:

1. **XAMPP Status**
   - Apache: Green/Running
   - MySQL: Green/Running

2. **PHP Version**
   Run: `php -v`
   Should be 7.4 or higher

3. **Database Connection**
   Visit: http://localhost/synergex/
   If main site works, database is fine.

4. **File Permissions**
   On Windows, usually not an issue.
   But ensure files are readable.

5. **Antivirus/Firewall**
   Sometimes blocks localhost.
   Temporarily disable to test.

## Quick Reset

If all else fails:

1. **Delete chatbot tables:**
```sql
DROP TABLE IF EXISTS chatbot_conversations;
DROP TABLE IF EXISTS chatbot_messages;
DROP TABLE IF EXISTS chatbot_knowledge;
DROP TABLE IF EXISTS chatbot_settings;
```

2. **Re-import:**
```bash
mysql -u root -p synergex_db < database_chatbot.sql
```

3. **Clear browser cache:**
   Ctrl+Shift+Delete → Clear all

4. **Hard refresh:**
   Ctrl+F5

## Success Indicators

When working correctly, you'll see:

1. **Green chat button** bottom-right of page
2. **Click opens chat window** with greeting
3. **Type "hello"** → Get welcome response
4. **Type "products"** → Get product info
5. **No console errors** in F12 Developer Tools

## Contact Support

If still not working:
1. Check test_chatbot_db.php results
2. Copy any console errors (F12)
3. Check Apache error log
4. Provide error details

---

## Most Likely Solution

**90% of issues are fixed by:**
```bash
mysql -u root -p synergex_db < database_chatbot.sql
```
Then refresh browser with Ctrl+F5.

**The database import creates all required tables with pre-loaded knowledge!**
