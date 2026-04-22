<?php
define('SMARTLIB', true);

// ─── Konfigurasi Aplikasi ───────────────────────────────────────────────────
define('APP_NAME',    'SmartLib');
define('APP_VERSION', '1.0.0');

// Base URL – deteksi otomatis berdasarkan posisi folder di web root
(function () {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Hitung path relatif dari DOCUMENT_ROOT ke folder aplikasi
    $docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $appDir  = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/');
    $relPath = str_replace($docRoot, '', $appDir);

    define('BASE_URL', $protocol . '://' . $host . $relPath . '/');
    define('ASSETS_URL', BASE_URL . 'assets/');
})();

// ─── Aturan Bisnis Perpustakaan ──────────────────────────────────────────────
define('FINE_PER_DAY',       1000);  // Rp 1.000 per hari keterlambatan
define('LOAN_DURATION_DAYS',    7);  // Durasi peminjaman standar
define('MAX_ACTIVE_LOANS',      3);  // Maks buku dipinjam sekaligus
define('MAX_EXTENSIONS',        2);  // Maks perpanjangan
define('EXTENSION_DAYS',        7);  // Tambahan hari tiap perpanjangan
define('RESERVATION_EXPIRY_H', 48);  // Jam batas ambil reservasi

// ─── Konfigurasi Session ─────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// ─── Load dependency ─────────────────────────────────────────────────────────
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
