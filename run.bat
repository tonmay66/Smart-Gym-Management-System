@echo off
echo ========================================
echo Gym Management System - Startup Script
echo ========================================
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP or XAMPP first
    pause
    exit /b 1
)

echo PHP is installed!
echo.

REM Check if database is set up
echo Checking database setup...
php -r "try { $pdo = new PDO('mysql:host=localhost;dbname=smart_gym', 'root', ''); echo 'Database exists!'; } catch (Exception $e) { echo 'Database not found. Please run setup first.'; exit(1); }" 2>nul
if %errorlevel% neq 0 (
    echo.
    echo Database not set up yet.
    echo Would you like to set up the database now? (Y/N)
    set /p setup_db=
    if /i "%setup_db%"=="Y" (
        echo.
        echo Running database setup...
        php setup_database.php
        if %errorlevel% neq 0 (
            echo.
            echo Database setup failed!
            pause
            exit /b 1
        )
    ) else (
        echo.
        echo Please run: php setup_database.php
        echo Then run this script again.
        pause
        exit /b 1
    )
)

echo.
echo ========================================
echo Starting PHP Development Server...
echo ========================================
echo.
echo Server running at: http://localhost:8000
echo Login page: http://localhost:8000/login
echo.
echo Press Ctrl+C to stop the server
echo.

cd public
php -S localhost:8000
