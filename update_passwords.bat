@echo off
REM ============================================================
REM TechTrack - Quick Password Update Script for Windows
REM ============================================================

echo.
echo ================================================
echo    TechTrack Password Security Update
echo ================================================
echo.
echo This script will guide you through updating
echo your default passwords to secure ones.
echo.
echo Press any key to continue or CTRL+C to exit...
pause > nul

echo.
echo [Step 1] Opening phpMyAdmin in your browser...
echo.
start http://localhost/phpmyadmin

echo.
echo [Step 2] Opening credentials file...
echo.
start notepad NEW_SECURE_CREDENTIALS.txt

echo.
echo ================================================
echo    INSTRUCTIONS
echo ================================================
echo.
echo 1. In phpMyAdmin:
echo    - Click on 'techtrack_db' database
echo    - Click on 'SQL' tab
echo    - Copy the SQL script from NEW_SECURE_CREDENTIALS.txt
echo    - Paste and click 'Go'
echo.
echo 2. Save the credentials:
echo    - Copy passwords to your password manager
echo    - NEVER save in plain text permanently
echo.
echo 3. Test the login:
echo    - Try logging in with new credentials
echo.
echo 4. Clean up:
echo    - Delete NEW_SECURE_CREDENTIALS.txt
echo    - Delete this script (update_passwords.bat)
echo    - Delete change_passwords_secure.php
echo.
echo ================================================
echo.
echo Ready to open the login page? (Y/N)
set /p openlogin="Open login page? (Y/N): "

if /i "%openlogin%"=="Y" (
    echo.
    echo Opening admin login page...
    start http://localhost:8080/TECH%%20TRACK%%20LAVALUST%%201.2/admin-login
)

echo.
echo ================================================
echo    CLEANUP REMINDER
echo ================================================
echo.
echo After successfully updating passwords, delete:
echo   [X] NEW_SECURE_CREDENTIALS.txt
echo   [X] update_passwords.bat (this file)
echo   [X] change_passwords_secure.php
echo   [X] generate_password_hash.php
echo.
echo Press any key to exit...
pause > nul
