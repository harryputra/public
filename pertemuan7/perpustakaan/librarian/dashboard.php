<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);
updateOverdueLoans();

$db = getDB();

// ─── Statistik hari ini ───────────────────────────────────────────────────
$today = date('Y-m-d');

$stats = [];
$stats['loans_today']   = (int)$db->query("SELECT COUNT(*) FROM loans WHERE DATE(loan_date)='$today'")->fetchColumn();
$stats['returns_today'] = (int)$db->query("SELECT COUNT(*) FROM loans WHERE DATE(return_date)='$today'")->fetchColumn();
$stats['overdue']       = (int)$db->query("SELECT COUNT(*) FROM loans WHERE status='overdue'")->fetchColumn();
$stats['total_books']   = (int)$db->query("SELECT COUNT(*) FROM books WHERE is_deleted=0")->fetchColumn();
$stats['available']     = (int)$db->query("SELECT SUM(available_copies) FROM books WHERE is_deleted=0")->fetchColumn();
$stats['reservations']  = (int)$db->query("SELECT COUNT(*) FROM reservations WHERE status='waiting'")->fetchColumn();
$stats['fine_today']    = (int)$db->query("SELECT COALESCE(SUM(fine_amount),0) FROM loans WHERE DATE(return_date)='$today'")->fetchColumn();

// Buku paling sering dipinjam (top 5)
$topBooks = $db->query("
    SELECT b.title, b.author, COUNT(l.id) AS loan_count
    FROM loans l JOIN books b ON l.book_id = b.id
    GROUP BY l.book_id ORDER BY loan_count DESC LIMIT 5
")->fetchAll();

// Peminjaman yang jatuh tempo hari ini & besok
$dueSoon = $db->query("
    SELECT l.id, u.full_name, u.nim, b.title, l.due_date, l.status
    FROM loans l
    JOIN users u ON l.user_id = u.id
    JOIN books b ON l.book_id = b.id
    WHERE l.status IN ('active','overdue') AND l.due_date <= DATE_ADD('$today', INTERVAL 2 DAY)
    ORDER BY l.due_date ASC LIMIT 10
")->fetchAll();

// Peminjaman aktif terbaru
$recentLoans = $db->query("
    SELECT l.id, u.full_name, b.title, l.loan_date, l.due_date, l.status
    FROM loans l
    JOIN users u ON l.user_id = u.id
    JOIN books b ON l.book_id = b.id
    ORDER BY l.created_at DESC LIMIT 8
")->fetchAll();

// Data chart: peminjaman 7 hari terakhir
$chartData = $db->query("
    SELECT DATE(loan_date) AS d, COUNT(*) AS c
    FROM loans
    WHERE loan_date >= DATE_SUB('$today', INTERVAL 6 DAY)
    GROUP BY DATE(loan_date) ORDER BY d
")->fetchAll();

$chartLabels = json_encode(array_column($chartData, 'd'));
$chartValues = json_encode(array_column($chartData, 'c'));

$pageTitle = 'Dashboard Pustakawan';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Dashboard Pustakawan</h4>
    <p class="text-muted mb-0"><?= formatDate($today, 'l, d F Y') ?></p>
  </div>
  <div class="ms-auto d-flex gap-2">
    <a href="<?= BASE_URL ?>librarian/loan_add.php" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>Proses Peminjaman
    </a>
    <a href="<?= BASE_URL ?>librarian/return_book.php" class="btn btn-success">
      <i class="bi bi-arrow-return-left me-2"></i>Proses Pengembalian
    </a>
  </div>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3"><div class="stat-card stat-blue">
    <div class="stat-icon"><i class="bi bi-journal-arrow-up"></i></div>
    <div class="stat-value"><?= $stats['loans_today'] ?></div>
    <div class="stat-label">Peminjaman Hari Ini</div>
  </div></div>
  <div class="col-6 col-xl-3"><div class="stat-card stat-green">
    <div class="stat-icon"><i class="bi bi-arrow-return-left"></i></div>
    <div class="stat-value"><?= $stats['returns_today'] ?></div>
    <div class="stat-label">Pengembalian Hari Ini</div>
  </div></div>
  <div class="col-6 col-xl-3"><div class="stat-card stat-red">
    <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <div class="stat-value"><?= $stats['overdue'] ?></div>
    <div class="stat-label">Buku Terlambat</div>
  </div></div>
  <div class="col-6 col-xl-3"><div class="stat-card stat-orange">
    <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
    <div class="stat-value"><?= formatRupiah($stats['fine_today']) ?></div>
    <div class="stat-label">Denda Dikumpulkan Hari Ini</div>
  </div></div>
</div>

<div class="row g-3 mb-4">
  <div class="col-4"><div class="stat-card stat-purple">
    <div class="stat-icon"><i class="bi bi-journals"></i></div>
    <div class="stat-value"><?= $stats['total_books'] ?></div>
    <div class="stat-label">Total Judul Buku</div>
  </div></div>
  <div class="col-4"><div class="stat-card stat-teal">
    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
    <div class="stat-value"><?= $stats['available'] ?></div>
    <div class="stat-label">Eksemplar Tersedia</div>
  </div></div>
  <div class="col-4"><div class="stat-card stat-yellow">
    <div class="stat-icon"><i class="bi bi-bookmark-star"></i></div>
    <div class="stat-value"><?= $stats['reservations'] ?></div>
    <div class="stat-label">Antrian Reservasi</div>
  </div></div>
</div>

<!-- Charts & Tables Row -->
<div class="row g-4">
  <!-- Chart -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header fw-bold"><i class="bi bi-bar-chart me-2 text-primary"></i>Peminjaman 7 Hari Terakhir</div>
      <div class="card-body">
        <canvas id="loanChart" height="220"></canvas>
      </div>
    </div>
  </div>

  <!-- Buku Terpopuler -->
  <div class="col-lg-3">
    <div class="card h-100">
      <div class="card-header fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Buku Terpopuler</div>
      <div class="card-body p-0">
        <ol class="list-group list-group-flush list-group-numbered">
          <?php foreach ($topBooks as $b): ?>
          <li class="list-group-item">
            <div class="fw-semibold small"><?= e($b['title']) ?></div>
            <small class="text-muted"><?= $b['loan_count'] ?>x dipinjam</small>
          </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </div>

  <!-- Jatuh Tempo Dekat -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header fw-bold">
        <i class="bi bi-alarm me-2 text-danger"></i>Jatuh Tempo Dekat
        <span class="badge bg-danger ms-1"><?= count($dueSoon) ?></span>
      </div>
      <div class="card-body p-0">
        <?php if (empty($dueSoon)): ?>
          <p class="text-center text-muted py-3 small mb-0">Tidak ada yang jatuh tempo dekat.</p>
        <?php else: ?>
        <ul class="list-group list-group-flush">
        <?php foreach ($dueSoon as $d): ?>
          <li class="list-group-item py-2">
            <div class="fw-semibold small"><?= e($d['title']) ?></div>
            <div class="d-flex justify-content-between">
              <small class="text-muted"><?= e($d['full_name']) ?> (<?= e($d['nim']) ?>)</small>
              <?= loanStatusBadge($d['status']) ?>
            </div>
            <small class="text-danger"><i class="bi bi-calendar3 me-1"></i><?= formatDate($d['due_date']) ?></small>
          </li>
        <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Peminjaman Terbaru -->
<div class="card mt-4">
  <div class="card-header d-flex align-items-center">
    <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Peminjaman Terbaru</span>
    <a href="<?= BASE_URL ?>librarian/loans.php" class="btn btn-sm btn-outline-primary ms-auto">Lihat Semua</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr>
        <th>Peminjam</th><th>Buku</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Status</th>
      </tr></thead>
      <tbody>
      <?php foreach ($recentLoans as $loan): ?>
      <tr>
        <td><div class="fw-semibold"><?= e($loan['full_name']) ?></div></td>
        <td><?= e($loan['title']) ?></td>
        <td><?= formatDate($loan['loan_date']) ?></td>
        <td><?= formatDate($loan['due_date']) ?></td>
        <td><?= loanStatusBadge($loan['status']) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
new Chart(document.getElementById('loanChart'), {
    type: 'bar',
    data: {
        labels: <?= $chartLabels ?>,
        datasets: [{
            label: 'Peminjaman',
            data: <?= $chartValues ?>,
            backgroundColor: 'rgba(37,99,235,0.7)',
            borderRadius: 6,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
