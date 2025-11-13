@echo off
echo ====================================
echo  Aplikasi Absensi Karyawan - Setup
echo ====================================
echo.

echo [1/3] Checking Laravel Installation...
php artisan --version
if %errorlevel% neq 0 (
    echo Error: Laravel not found!
    pause
    exit /b 1
)
echo.

echo [2/3] Running Migrations and Seeders...
echo.
php artisan migrate:fresh --seed
if %errorlevel% neq 0 (
    echo Error: Migration failed!
    echo.
    echo Make sure:
    echo 1. MySQL is running
    echo 2. Database 'absensi_karyawan' exists
    echo 3. .env file is configured correctly
    echo.
    pause
    exit /b 1
)
echo.

echo ====================================
echo  Setup Complete!
echo ====================================
echo.
echo Database seeded with:
echo - 3 Roles (Super Admin, Admin Cabang, Karyawan)
echo - 3 Branches (Jakarta, Bandung, Surabaya)
echo - 5 Positions (Manager, Supervisor, Staff, Operator, Admin)
echo - 5 Users (1 Super Admin, 2 Admin Cabang, 2 Karyawan)
echo.
echo Default login credentials:
echo - Super Admin: admin@absensi.com / password
echo - Admin Jakarta: admin.jakarta@absensi.com / password
echo - Karyawan: budi@absensi.com / password
echo.
echo [3/3] Starting Development Server...
echo.
echo Press Ctrl+C to stop the server
echo.
php artisan serve
