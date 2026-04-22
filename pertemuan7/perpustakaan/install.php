<?php
/**
 * SmartLib - Installer & Database Seeder
 * Jalankan sekali di browser: http://localhost/pertemuan7/perpustakaan/install.php
 * HAPUS file ini setelah instalasi selesai!
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartlib');

$errors = [];
$success = [];

try {
    // Connect tanpa database dulu
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Buat database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    $success[] = "Database '" . DB_NAME . "' berhasil dibuat/terhubung.";

    // Baca dan jalankan schema SQL
    $sql = file_get_contents(__DIR__ . '/database/smartlib.sql');
    // Hapus komentar SQL dan jalankan statement satu per satu
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt) && !preg_match('/^--/', $stmt)) {
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                // Abaikan error minor (index sudah ada, dll)
            }
        }
    }
    $success[] = "Skema tabel berhasil dibuat.";

    // =====================================================
    // SEED DATA - Pengguna
    // Default password semua user: smartlib123
    // =====================================================
    $password = password_hash('smartlib123', PASSWORD_BCRYPT);

    $users = [
        ['ADMIN001',  'Administrator',      'admin@smartlib.id',         $password, 'admin',     '081200000001'],
        ['PUST001',   'Budi Santoso',       'pustakawan@smartlib.id',    $password, 'librarian', '081200000002'],
        ['2021001',   'Andi Pratama',       'andi@student.polman.id',    $password, 'student',   '081200000003'],
        ['2021002',   'Siti Rahayu',        'siti@student.polman.id',    $password, 'student',   '081200000004'],
        ['2021003',   'Rizky Firmansyah',   'rizky@student.polman.id',   $password, 'student',   '081200000005'],
        ['2021004',   'Diana Kusuma',       'diana@student.polman.id',   $password, 'student',   '081200000006'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO users (nim,full_name,email,password,role,phone) VALUES (?,?,?,?,?,?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    $success[] = "Data pengguna berhasil ditambahkan (6 akun).";

    // =====================================================
    // SEED DATA - Buku
    // =====================================================
    $books = [
        ['Rekayasa Perangkat Lunak', 'Roger S. Pressman', '978-0-07-337597-7', 'McGraw-Hill', 2014, 'Teknik Informatika', 'Buku teks komprehensif tentang rekayasa perangkat lunak modern, mencakup metodologi, desain, pengujian, dan pemeliharaan sistem.', 3, 3, 'Lt.2 Rak A1'],
        ['Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', '978-0-13-235088-4', 'Prentice Hall', 2008, 'Pemrograman', 'Panduan praktis menulis kode yang bersih, readable, dan maintainable untuk developer profesional.', 2, 2, 'Lt.2 Rak A2'],
        ['The Pragmatic Programmer', 'David Thomas & Andrew Hunt', '978-0-13-595705-9', 'Addison-Wesley', 2019, 'Pemrograman', 'Panduan esensial untuk menjadi programmer yang lebih efektif dan pragmatis dalam pengembangan software.', 2, 2, 'Lt.2 Rak A2'],
        ['Design Patterns: Elements of Reusable Object-Oriented Software', 'Gang of Four', '978-0-20-163361-5', 'Addison-Wesley', 1994, 'Arsitektur Software', 'Buku klasik tentang 23 pola desain yang menjadi fondasi arsitektur software modern.', 1, 1, 'Lt.2 Rak B1'],
        ['Introduction to Algorithms', 'Thomas H. Cormen', '978-0-26-204630-5', 'MIT Press', 2009, 'Algoritma', 'Textbook algoritma dan struktur data yang paling komprehensif, wajib untuk mahasiswa ilmu komputer.', 3, 3, 'Lt.2 Rak B2'],
        ['Database System Concepts', 'Silberschatz, Korth, Sudarshan', '978-0-07-352332-3', 'McGraw-Hill', 2011, 'Basis Data', 'Konsep dasar dan lanjutan sistem basis data, mencakup SQL, normalisasi, transaksi, dan optimasi query.', 2, 1, 'Lt.2 Rak C1'],
        ['Computer Networks', 'Andrew S. Tanenbaum', '978-0-13-212695-3', 'Prentice Hall', 2011, 'Jaringan Komputer', 'Buku teks jaringan komputer dari lapisan fisik hingga aplikasi, dilengkapi contoh nyata dan protokol terkini.', 2, 2, 'Lt.2 Rak C2'],
        ['Artificial Intelligence: A Modern Approach', 'Stuart Russell & Peter Norvig', '978-0-13-604259-4', 'Prentice Hall', 2020, 'Kecerdasan Buatan', 'Buku referensi utama kecerdasan buatan yang mencakup machine learning, NLP, robotika, dan AI modern.', 2, 2, 'Lt.3 Rak A1'],
        ['Web Development with PHP & MySQL', 'Luke Welling & Laura Thomson', '978-0-67-233398-3', 'Addison-Wesley', 2017, 'Pengembangan Web', 'Panduan lengkap pengembangan web dinamis menggunakan PHP dan MySQL dari dasar hingga mahir.', 3, 3, 'Lt.3 Rak A2'],
        ['Python Crash Course', 'Eric Matthes', '978-1-59-327603-4', 'No Starch Press', 2019, 'Pemrograman', 'Pengenalan Python yang praktis dengan proyek-proyek menarik termasuk game, visualisasi data, dan web app.', 4, 4, 'Lt.3 Rak B1'],
        ['Matematika Diskrit', 'Kenneth H. Rosen', '978-0-07-288008-3', 'McGraw-Hill', 2012, 'Matematika', 'Fondasi matematika untuk ilmu komputer: logika, himpunan, kombinatorika, teori graf, dan bilangan.', 2, 2, 'Lt.1 Rak D1'],
        ['Kalkulus', 'James Stewart', '978-0-53-849787-9', 'Brooks/Cole', 2016, 'Matematika', 'Buku kalkulus komprehensif untuk mahasiswa teknik dan sains, dilengkapi latihan soal dan solusi.', 3, 3, 'Lt.1 Rak D2'],
        ['Operating System Concepts', 'Silberschatz, Galvin, Gagne', '978-1-11-856419-2', 'Wiley', 2018, 'Sistem Operasi', 'Penjelasan mendalam tentang konsep sistem operasi: proses, memori, sistem file, dan keamanan.', 2, 0, 'Lt.2 Rak D1'],
        ['Software Testing: A Craftsman Approach', 'Paul C. Jorgensen', '978-1-46-657808-8', 'CRC Press', 2014, 'Pengujian Software', 'Pendekatan komprehensif terhadap pengujian perangkat lunak, mencakup unit testing, integrasi, dan acceptance testing.', 2, 2, 'Lt.2 Rak E1'],
        ['Agile Software Development', 'Robert C. Martin', '978-0-13-597444-5', 'Prentice Hall', 2003, 'Metodologi', 'Prinsip, pola, dan praktik pengembangan software agile dengan fokus pada clean design dan TDD.', 1, 1, 'Lt.2 Rak E2'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO books (title,author,isbn,publisher,year,category,description,total_copies,available_copies,rack_location) VALUES (?,?,?,?,?,?,?,?,?,?)");
    foreach ($books as $b) {
        $stmt->execute($b);
    }
    $success[] = "Data buku berhasil ditambahkan (15 judul buku).";

    // =====================================================
    // SEED DATA - Peminjaman Demo
    // =====================================================
    $today = date('Y-m-d');
    $overdueDate = date('Y-m-d', strtotime('-10 days'));
    $dueSoon = date('Y-m-d', strtotime('+2 days'));
    $normalDue = date('Y-m-d', strtotime('+5 days'));

    // Get IDs
    $andiBM  = $pdo->query("SELECT id FROM users WHERE nim='2021001' LIMIT 1")->fetch();
    $sitiBM  = $pdo->query("SELECT id FROM users WHERE nim='2021002' LIMIT 1")->fetch();
    $pustBM  = $pdo->query("SELECT id FROM users WHERE nim='PUST001' LIMIT 1")->fetch();
    $book13  = $pdo->query("SELECT id FROM books WHERE isbn='978-1-11-856419-2' LIMIT 1")->fetch();
    $book6   = $pdo->query("SELECT id FROM books WHERE isbn='978-0-07-352332-3' LIMIT 1")->fetch();

    if ($andiBM && $sitiBM && $pustBM && $book13 && $book6) {
        // Andi meminjam OS Concepts (sudah dipinjam, stok 0)
        $pdo->prepare("INSERT IGNORE INTO loans (user_id,book_id,loan_date,due_date,status,processed_by) VALUES (?,?,?,?,?,?)")
            ->execute([$andiBM['id'], $book13['id'], date('Y-m-d', strtotime('-3 days')), $normalDue, 'active', $pustBM['id']]);

        // Siti meminjam Database Concepts (sudah dipinjam, stok 1)
        $pdo->prepare("INSERT IGNORE INTO loans (user_id,book_id,loan_date,due_date,status,processed_by) VALUES (?,?,?,?,?,?)")
            ->execute([$sitiBM['id'], $book6['id'], date('Y-m-d', strtotime('-12 days')), $overdueDate, 'overdue', $pustBM['id']]);

        // Update stok DB Concepts menjadi 1 (sudah dipinjam 1 dari 2)
        $pdo->prepare("UPDATE books SET available_copies=1 WHERE isbn='978-0-07-352332-3'")->execute();
        $success[] = "Data peminjaman demo berhasil dibuat.";
    }

    echo renderPage($success, $errors);

} catch (PDOException $e) {
    $errors[] = "Error database: " . $e->getMessage();
    echo renderPage($success, $errors);
}

function renderPage($success, $errors) {
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>SmartLib - Installer</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:680px">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📚 SmartLib — Installer</h4>
        </div>
        <div class="card-body">
            <?php foreach ($errors as $e): ?>
                <div class="alert alert-danger"><strong>Error:</strong> <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
            <?php foreach ($success as $s): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($s) ?></div>
            <?php endforeach; ?>

            <?php if (empty($errors)): ?>
            <hr>
            <h5>Akun Demo (password: <code>smartlib123</code>)</h5>
            <table class="table table-sm table-bordered">
                <thead class="table-dark"><tr><th>Role</th><th>Email</th><th>NIM/NIP</th></tr></thead>
                <tbody>
                    <tr><td><span class="badge bg-danger">Admin</span></td><td>admin@smartlib.id</td><td>ADMIN001</td></tr>
                    <tr><td><span class="badge bg-warning text-dark">Pustakawan</span></td><td>pustakawan@smartlib.id</td><td>PUST001</td></tr>
                    <tr><td><span class="badge bg-success">Mahasiswa</span></td><td>andi@student.polman.id</td><td>2021001</td></tr>
                    <tr><td><span class="badge bg-success">Mahasiswa</span></td><td>siti@student.polman.id</td><td>2021002</td></tr>
                    <tr><td><span class="badge bg-success">Mahasiswa</span></td><td>rizky@student.polman.id</td><td>2021003</td></tr>
                </tbody>
            </table>
            <div class="alert alert-warning">
                ⚠️ <strong>Penting:</strong> Hapus file <code>install.php</code> setelah instalasi selesai!
            </div>
            <a href="login.php" class="btn btn-primary btn-lg w-100">Lanjut ke Halaman Login →</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
    <?php
    return ob_get_clean();
}
