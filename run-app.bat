@echo off
echo Starting Attendance System...
echo ============================

:: Check if .env exists
if not exist .env (
    echo [ERROR] .env file not found! Please run setup first.
    pause
    exit
)

:: Clear caches to avoid issues
call php artisan optimize:clear

:: Start Server (opens in a new window)
start "Laravel Server" php artisan serve

echo.
echo The system is creating the server...
echo You can access the system at: http://127.0.0.1:8000
echo.
echo Press any key to close this launcher (Server will keep running)...
pause
