<?php
defined('SMARTLIB') or die('Direct access not permitted');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartlib');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die('<div style="font-family:sans-serif;padding:2rem;color:#c00">
                <h2>Koneksi Database Gagal</h2>
                <p>Pastikan MySQL berjalan dan konfigurasi database sudah benar di <code>config/database.php</code>.</p>
                <p>Jalankan <a href="../install.php">install.php</a> untuk setup database.</p>
                <pre style="background:#fee;padding:1rem">' . htmlspecialchars($e->getMessage()) . '</pre>
            </div>');
        }
    }
    return $pdo;
}
