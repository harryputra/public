<?php
require_once __DIR__ . '/../config/config.php';
requireRole('student');
updateOverdueLoans();

$user = currentUser();
$db   = getDB();

// ─── Statistik peminjaman ─────────────────────────────────────────────────
$stats = $db->prepare("
    SELECT
        COUNT(CASE WHEN status='active'   THEN 1 END) AS active,
        COUNT(CASE WHEN status='overdue'  THEN 1 END) AS overdue,
        COUNT(CASE WHEN status='returned' THEN 1 END) AS returned,
        COALESCE(SUM(CASE WHEN status='overdue' AND fine_paid=0 THEN fine_amount END),0) AS total_fine
    FROM loans WHERE user_id = ?
");
$stats->execute([$user['id']]);
$stat = $stats->fetch();

// Hitung ulang denda overdue yang belum dibayar
$overdueFine = 0;
$overdueRows = $db->prepare("SELECT fine_amount, due_date FROM loans WHERE user_id=? AND status='overdue' AND fine_paid=0");
$overdueRows->execute([$user['id']]);
foreach ($overdueRows->fetchAll() as $row) {
    $overdueFine += max($row['fine_amount'], calculateFine($row['due_date']));
}

// ─── Peminjaman aktif (5 terbaru) ─────────────────────────────────────────
$activeLoans = $db->prepare("
    SELECT l.*, b.title, b.author, b.rack_location
    FROM loans l JOIN books b ON l.book_id = b.id
    WHERE l.user_id = ? AND l.status IN ('active','overdue')
    ORDER BY l.due_date ASC LIMIT 5
");
$activeLoans->execute([$user['id']]);
$loans = $activeLoans->fetchAll();

// ─── Reservasi aktif ──────────────────────────────────────────────────────
$myReservations = $db->prepare("
    SELECT r.*, b.title, b.author
    FROM reservations r JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ? AND r.status IN ('waiting','ready')
    ORDER BY r.reserved_at DESC LIMIT 3
");
$myReservations->execute([$user['id']]);
$reservations = $myReservations->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div class="d-flex align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">👋 Halo, <?= e($user['name']) ?>!</h4>
    <p class="text-muted mb-0">NIM: <?= e($user['nim']) ?> &nbsp;|&nbsp; <?= formatDate(date('Y-m-d'), 'd F Y') ?></p>
  </div>
  <div class="ms-auto">
    <a href="<?= BASE_URL ?>student/catalog.php" class="btn btn-primary">
      <i class="bi bi-search me-2"></i>Cari Buku
    </a>
  </div>
</div>

<!-- Statistik Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-blue">
      <div class="stat-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
      <div class="stat-value"><?= $stat['active'] ?></div>
      <div class="stat-label">Sedang Dipinjam</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-red">
      <div class="stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
      <div class="stat-value"><?= $stat['overdue'] ?></div>
      <div class="stat-label">Terlambat</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card stat-green">
      <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
      <div class="stat-value"><?= $stat['returned'] ?></div>
      <div class="stat-label">Sudah Dikembalikan</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card <?= $overdueFine > 0 ? 'stat-orange' : 'stat-green' ?>">
      <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
      <div class="stat-value"><?= $overdueFine > 0 ? formatRupiah($overdueFine) : 'Rp 0' ?></div>
      <div class="stat-label">Denda Belum Bayar</div>
    </div>
  </div>
</div>

<!-- Denda Alert -->
<?php if ($overdueFine > 0): ?>
<div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
  <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
  <div>
    <strong>Perhatian!</strong> Anda memiliki denda sebesar <strong><?= formatRupiah($overdueFine) ?></strong>
    yang belum dibayar. Silakan hubungi pustakawan untuk pembayaran.
  </div>
</div>
<?php endif; ?>

<div class="row g-4">
  <!-- Peminjaman Aktif -->
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Peminjaman Aktif</h6>
        <a href="<?= BASE_URL ?>student/my_loans.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <?php if (empty($loans)): ?>
          <div class="text-center py-5 text-muted">
            <i class="bi bi-journal-x display-4"></i>
            <p class="mt-2">Tidak ada peminjaman aktif.</p>
            <a href="<?= BASE_URL ?>student/catalog.php" class="btn btn-outline-primary btn-sm">Cari Buku Sekarang</a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light"><tr>
                <th>Judul Buku</th><th>Jatuh Tempo</th><th>Status</th><th>Denda</th><th></th>
              </tr></thead>
              <tbody>
              <?php foreach ($loans as $loan):
                $fine   = calculateFine($loan['due_date']);
                $isLate = $loan['status'] === 'overdue';
                $daysLeft = (int)((strtotime($loan['due_date']) - time()) / 86400);
              ?>
              <tr class="<?= $isLate ? 'table-danger' : ($daysLeft <= 2 ? 'table-warning' : '') ?>">
                <td>
                  <div class="fw-semibold"><?= e($loan['title']) ?></div>
                  <small class="text-muted"><?= e($loan['author']) ?></small>
                </td>
                <td>
                  <?= formatDate($loan['due_date']) ?>
                  <?php if ($isLate): ?>
                    <br><small class="text-danger"><i class="bi bi-clock-history"></i> Terlambat <?= abs($daysLeft) ?> hari</small>
                  <?php elseif ($daysLeft <= 2): ?>
                    <br><small class="text-warning"><i class="bi bi-alarm"></i> <?= $daysLeft ?> hari lagi</small>
                  <?php endif; ?>
                </td>
                <td><?= loanStatusBadge($loan['status']) ?></td>
                <td><?= $fine > 0 ? '<span class="text-danger fw-semibold">' . formatRupiah($fine) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                <td>
                  <?php if ($loan['extension_count'] < MAX_EXTENSIONS && !$isLate): ?>
                  <a href="<?= BASE_URL ?>student/extend_loan.php?id=<?= $loan['id'] ?>"
                     class="btn btn-sm btn-outline-success"
                     data-confirm="Perpanjang peminjaman buku ini?"
                     data-confirm-type="question">
                    <i class="bi bi-arrow-clockwise"></i> Perpanjang
                  </a>

                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Reservasi & Info -->
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header">
        <h6 class="mb-0 fw-bold"><i class="bi bi-bookmark-star me-2 text-warning"></i>Reservasi Saya</h6>
      </div>
      <div class="card-body p-0">
        <?php if (empty($reservations)): ?>
          <p class="text-center text-muted py-3 small mb-0">Tidak ada reservasi aktif.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush">
          <?php foreach ($reservations as $r): ?>
            <li class="list-group-item">
              <div class="fw-semibold small"><?= e($r['title']) ?></div>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="text-muted"><?= formatDate($r['reserved_at']) ?></small>
                <?= reservationStatusBadge($r['status']) ?>
              </div>
            </li>
          <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <!-- Info Box -->
    <div class="card bg-light border-0">
      <div class="card-body small">
        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Peminjaman</h6>
        <ul class="ps-3 mb-0 text-muted">
          <li>Maks. <strong><?= MAX_ACTIVE_LOANS ?> buku</strong> dipinjam sekaligus</li>
          <li>Durasi <strong><?= LOAN_DURATION_DAYS ?> hari</strong> per peminjaman</li>
          <li>Perpanjangan maks. <strong><?= MAX_EXTENSIONS ?>x</strong> (+<?= EXTENSION_DAYS ?> hari)</li>
          <li>Denda terlambat <strong><?= formatRupiah(FINE_PER_DAY) ?>/hari</strong></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
