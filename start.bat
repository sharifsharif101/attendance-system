@echo off
title Attendance System
echo ================================
echo    Starting Attendance System
echo ================================
echo.

:: Start Laravel Server
start "Laravel Server" php artisan serve

:: Start Vite Dev Server
start "Vite Dev" npm run dev

echo.
echo [OK] Both servers are running!
echo.
echo Laravel: http://127.0.0.1:8000
echo Vite:    http://127.0.0.1:5173
echo.
echo Close the server windows to stop.
pause
