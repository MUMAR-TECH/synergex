@echo off
echo ========================================
echo  Synergex Chatbot Installation
echo ========================================
echo.

echo Step 1: Checking MySQL...
"C:\xampp\mysql\bin\mysql" --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: MySQL not found. Is XAMPP installed?
    pause
    exit /b 1
)
echo MySQL found!
echo.

echo Step 2: Importing chatbot database...
echo Please enter your MySQL password when prompted.
echo (Usually blank for XAMPP - just press Enter)
echo.

"C:\xampp\mysql\bin\mysql" -u root -p synergex_db < database_chatbot.sql

if errorlevel 1 (
    echo.
    echo ERROR: Database import failed!
    echo.
    echo Possible reasons:
    echo 1. Wrong password
    echo 2. Database 'synergex_db' doesn't exist
    echo 3. MySQL service not running
    echo.
    echo Please check XAMPP Control Panel and try again.
    pause
    exit /b 1
)

echo.
echo ========================================
echo  Installation Complete!
echo ========================================
echo.
echo Chatbot tables created successfully.
echo.
echo Next steps:
echo 1. Open your browser
echo 2. Go to: http://localhost/synergex/
echo 3. Look for the green chat button (bottom-right)
echo 4. Click it and try chatting!
echo.
echo Test with these questions:
echo - What products do you offer?
echo - How do I get a quote?
echo - Tell me about your company
echo.
echo Admin Panel:
echo http://localhost/synergex/admin/chatbot.php
echo.
pause
