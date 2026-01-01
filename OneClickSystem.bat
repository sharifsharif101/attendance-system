@echo off
setlocal
title Attendance System Launcher

:: ==========================================
:: CONFIGURATION
:: ==========================================
:: Path to XAMPP's mysql.exe (Adjust if installed elsewhere)
set MYSQL_PATH=C:\xampp\mysql\bin\mysqld.exe
:: Port for the Laravel App
set APP_PORT=8000
:: App URL
set APP_URL=http://localhost:%APP_PORT%

echo.
echo    [ ATTENDANCE SYSTEM LAUNCHER ]
echo.

:: 1. CHECK & START MYSQL
echo [*] Checking Database Service...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo     - Database is already running.
) else (
    echo     - Starting Database...
    if exist "%MYSQL_PATH%" (
        start /B "" "%MYSQL_PATH%"
        echo     - Database started independently.
    ) else (
        echo [ERROR] MySQL not found at %MYSQL_PATH%
        echo Please make sure XAMPP is installed or update the path in this script.
        pause
        exit
    )
)

:: 2. START LARAVEL SERVER
echo [*] Starting Application Server...
:: We start this in a separate window so it stays alive
start "Attendance System Server" php artisan serve --port=%APP_PORT%

:: 3. OPEN BROWSER
echo [*] Opening System in Browser...
timeout /t 3 >nul
start "" "%APP_URL%"

echo.
echo ========================================================
echo   SYSTEM IS RUNNING.
echo   - Only close the "Attendance System Server" window 
echo     to stop the application.
echo   - You can minimize this window.
echo ========================================================
echo.
pause
