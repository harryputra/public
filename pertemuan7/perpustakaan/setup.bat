@echo off
setlocal enabledelayedexpansion
title SmartLib - One Click Setup

:: STEP 0: Auto-Elevation (Administrator)
:check_privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Memerlukan izin Administrator...
    powershell -Command "Start-Process -FilePath '%~f0' -Verb RunAs -WorkingDirectory '%~dp0'" >nul 2>&1
    if %errorLevel% neq 0 (
        echo [GAGAL] Jalankan manual: Klik Kanan setup.bat - Run as Administrator
        pause
        exit /b
    )
    exit /b
)

:: Pindah ke directory script
cd /d "%~dp0"

echo ================================================
echo   SmartLib - One Click Setup
echo ================================================

:: LANGKAH 1: Mencari PHP
echo [1/4] Mencari PHP...
set "PHP_BIN="

:: Cek path
where php >nul 2>&1
if !errorLevel! equ 0 (
    set "PHP_BIN=php"
)

:: Cek folder standar jika path tidak ada
if "!PHP_BIN!"=="" (
    for %%D in (C D E F) do (
        if exist "%%D:\xampp\php\php.exe" set "PHP_BIN=%%D:\xampp\php\php.exe"
    )
)

if "!PHP_BIN!"=="" (
    echo [GAGAL] php.exe tidak ditemukan.
    pause
    exit /b
)
echo [OK] Menggunakan PHP: !PHP_BIN!

:: LANGKAH 2: Mencari MySQL
echo [2/4] Mencari MySQL...
set "MYSQL_BIN="

for %%D in (C D E F) do (
    if exist "%%D:\xampp\mysql\bin\mysql.exe" set "MYSQL_BIN=%%D:\xampp\mysql\bin\mysql.exe"
)

if "!MYSQL_BIN!"=="" (
    where mysql >nul 2>&1
    if !errorLevel! equ 0 set "MYSQL_BIN=mysql"
)

if "!MYSQL_BIN!"=="" (
    echo [GAGAL] mysql.exe tidak ditemukan.
    pause
    exit /b
)

:: Pastikan MySQL Aktif
echo [INFO] Memeriksa status MySQL...
"!MYSQL_BIN!" -u root -e "SELECT 1;" >nul 2>&1
if !errorLevel! neq 0 (
    echo [INFO] MySQL belum aktif, mencoba menjalankan service...
    net start mysql >nul 2>&1
    net start MySQL80 >nul 2>&1
    timeout /t 3 >nul
)

"!MYSQL_BIN!" -u root -e "SELECT 1;" >nul 2>&1
if !errorLevel! neq 0 (
    echo [WARNING] MySQL gagal dijalankan otomatis. Silakan Start via XAMPP.
) else (
    echo [OK] MySQL aktif.
)

:: LANGKAH 3: Sinkronisasi Database
echo [3/4] Menyiapkan Database...
"!MYSQL_BIN!" -u root -e "CREATE DATABASE IF NOT EXISTS smartlib;"
"!MYSQL_BIN!" -u root smartlib < "database\smartlib.sql"
"!PHP_BIN!" install.php --cli >nul 2>&1
echo [OK] Database smartlib berhasil di-sinkronisasi.

:: LANGKAH 4: Jalankan Server
echo [4/4] Menjalankan Web Server ^& Live Reload...
set "PORT=8000"
netstat -ano | find ":!PORT! " >nul && set "PORT=8001"

:: Jalankan server di background
start /min "SmartLib_Server" "!PHP_BIN!" -S localhost:!PORT! -t "%~dp0"

echo [OK] Server berjalan di http://localhost:!PORT!
echo [OK] Fitur Live Reload AKTIF.

:: Buka Browser
timeout /t 2 >nul
start http://localhost:!PORT!

echo ================================================
echo   SETUP SELESAI!
echo ================================================
echo   Aplikasi siap digunakan. Jangan tutup terminal ini.
echo ================================================
pause
