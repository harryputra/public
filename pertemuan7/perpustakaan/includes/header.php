<?php
defined('SMARTLIB') or die('Direct access not permitted');
$__user = isLoggedIn() ? currentUser() : null;
$__role = $__user['role'] ?? '';
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? APP_NAME) ?> — <?= APP_NAME ?></title>
<!-- Bootstrap 5.3 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Custom CSS -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/style.css">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Tom Select -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap5.min.css">

<!-- Live Reload (Antigravity) -->
<?php if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1'): ?>
<script>
(function() {
    let lastModified = 0;
    async function checkReload() {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 2000);
            const res = await fetch('<?= BASE_URL ?>live-reload.php', { signal: controller.signal });
            const data = await res.json();
            clearTimeout(timeoutId);
            if (lastModified && data.last_modified > lastModified) {
                console.log('%c[LiveReload]%c Perubahan terdeteksi, merefresh...', 'color: #0d6efd; font-weight: bold;', 'color: inherit;');
                location.reload();
            }
            lastModified = data.last_modified;
        } catch (e) {}
        setTimeout(checkReload, 1500);
    }
    checkReload();
})();
</script>
<?php endif; ?>
</head>
<body>

<!-- ═══ NAVBAR ═══════════════════════════════════════════════════════════════ -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm" id="mainNavbar">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>">
      <i class="bi bi-book-half fs-4"></i>
      <span class="fw-bold"><?= APP_NAME ?></span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <?php if ($__user): ?>
      <!-- ── Nav Links berdasarkan Role ── -->
      <ul class="navbar-nav me-auto">
        <?php if ($__role === 'student'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>student/dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>student/catalog.php"><i class="bi bi-search"></i> Katalog Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>student/my_loans.php"><i class="bi bi-journal-bookmark"></i> Peminjaman Saya</a></li>

        <?php elseif ($__role === 'librarian'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>librarian/dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-journals"></i> Koleksi Buku</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>librarian/books.php"><i class="bi bi-list-ul me-2"></i>Daftar Buku</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>librarian/book_add.php"><i class="bi bi-plus-circle me-2"></i>Tambah Buku</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-arrow-left-right"></i> Sirkulasi</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>librarian/loans.php"><i class="bi bi-clipboard-check me-2"></i>Daftar Peminjaman</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>librarian/loan_add.php"><i class="bi bi-plus-circle me-2"></i>Proses Peminjaman</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>librarian/return_book.php"><i class="bi bi-arrow-return-left me-2"></i>Proses Pengembalian</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>librarian/reservations.php"><i class="bi bi-bookmark-star"></i> Reservasi</a></li>

        <?php elseif ($__role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/users.php"><i class="bi bi-people"></i> Pengguna</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>librarian/books.php"><i class="bi bi-journals"></i> Koleksi Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>librarian/loans.php"><i class="bi bi-arrow-left-right"></i> Sirkulasi</a></li>
        <?php endif; ?>
      </ul>

      <!-- ── User Menu ── -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5"></i>
            <span><?= e($__user['name']) ?></span>
            <span class="badge bg-opacity-75 ms-1 role-badge role-<?= e($__role) ?>"><?= ucfirst($__role) ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-item-text small text-muted"><?= e($__user['email']) ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="<?= BASE_URL ?>logout.php" class="d-inline">
                <?php csrfField(); ?>
                <button type="submit" class="dropdown-item text-danger">
                  <i class="bi bi-box-arrow-right me-2"></i>Keluar
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
      <?php else: ?>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
      </ul>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- ═══ MAIN WRAPPER ═══════════════════════════════════════════════════════════ -->
<main class="main-content">
<div class="container-fluid py-4 px-4">
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfigurasi Toast Global
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});
</script>

<?php showFlash(); ?>

