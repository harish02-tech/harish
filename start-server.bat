@echo off
title LearnSphere Local Development Server
color 0B
echo ======================================================================
echo    LearnSphere - Online Course Enrollment Portal
echo    Local Web Server Launcher
echo ======================================================================
echo.
echo [INFO] Starting PHP Server at http://localhost:8000 ...
echo [INFO] Opening webpage in your browser...
echo.
start http://localhost:8000/index.php
echo ======================================================================
echo  Server is ACTIVE! 
echo  Open your browser and visit: http://localhost:8000/
echo  (Do not close this window while using the website)
echo ======================================================================
echo.
"D:\php\php.exe" -S localhost:8000 -t "%~dp0"
pause
