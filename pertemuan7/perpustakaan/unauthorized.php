<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Akses Ditolak';
require_once __DIR__ . '/includes/header.php';
?>
<div class="text-center py-5">
  <i class="bi bi-shield-lock display-1 text-danger"></i>
  <h2 class="mt-3">Akses Ditolak</h2>
  <p class="text-muted">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
  <a href="<?= BASE_URL ?>" class="btn btn-primary">
    <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
  </a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
