<?php
require_once __DIR__ . '/../config/config.php';
requireRole('student');
updateOverdueLoans();

$user = currentUser();
$db   = getDB();
$tab  = $_GET['tab'] ?? 'active';

// ─── Query peminjaman ──────────────────────────────────────────────────────
$statusMap = [
    'active'   => "status IN ('active','overdue')",
    'returned' => "status = 'returned'",
    'all'      => "1=1",
];
$statusSql = $statusMap[$tab] ?? $statusMap['active'];

$loans = $db->prepare("
    SELECT l.*, b.title, b.author, b.isbn, b.rack_location, b.id AS book_id
    FROM loans l
    JOIN books b ON l.book_id = b.id
    WHERE l.user_id = ? AND $statusSql
    ORDER BY l.created_at DESC
");
$loans->execute([$user['id']]);
$loans = $loans->fetchAll();

// Hitung total denda belum bayar
$fineStmt = $db->prepare("SELECT COALESCE(SUM(fine_amount),0) FROM loans WHERE user_id=? AND fine_paid=0 AND status IN ('overdue','returned')");
$fineStmt->execute([$user['id']]);
$totalFine = (int)$fineStmt->fetchColumn();

$pageTitle = 'Peminjaman Saya';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <h4 class="fw-bold mb-0"><i class="bi bi-journal-bookmark text-primary me-2"></i>Peminjaman Saya</h4>
</div>

<?php if ($totalFine > 0): ?>
<div class="alert alert-danger mb-4">
  <i class="bi bi-cash me-2"></i>Total denda Anda: <strong><?= formatRupiah($totalFine) ?></strong>. Silakan bayar ke pustakawan.
</div>
<?php endif; ?>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="loanTabs">
  <li class="nav-item">
    <a class="nav-link <?= $tab === 'active' ? 'active' : '' ?>" href="?tab=active">
      <i class="bi bi-journal-arrow-up me-1"></i>Aktif & Terlambat
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= $tab === 'returned' ? 'active' : '' ?>" href="?tab=returned">
      <i class="bi bi-check-circle me-1"></i>Sudah Dikembalikan
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= $tab === 'all' ? 'active' : '' ?>" href="?tab=all">
      <i class="bi bi-list-ul me-1"></i>Semua Histori
    </a>
  </li>
</ul>

<div class="card">
  <div class="card-body p-0">
    <?php if (empty($loans)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-journal-x display-4"></i>
        <p class="mt-2">Tidak ada data peminjaman.</p>
        <?php if ($tab === 'active'): ?>
        <a href="<?= BASE_URL ?>student/catalog.php" class="btn btn-primary btn-sm">Cari Buku</a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="loansTable">
          <thead class="table-light">
            <tr>
              <th>Buku</th>
              <th>Tanggal Pinjam</th>
              <th>Jatuh Tempo</th>
              <th>Tanggal Kembali</th>
              <th>Status</th>
              <th>Denda</th>
              <th>Perpanjangan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($loans as $loan):
            $fine    = $loan['status'] === 'returned'
                       ? $loan['fine_amount']
                       : calculateFine($loan['due_date']);
            $daysLeft = (int)((strtotime($loan['due_date']) - time()) / 86400);
            $rowClass = match($loan['status']) {
                'overdue'  => 'table-danger',
                'active'   => $daysLeft <= 2 ? 'table-warning' : '',
                default    => '',
            };
          ?>
          <tr class="<?= $rowClass ?>">
            <td>
              <a href="<?= BASE_URL ?>student/book_detail.php?id=<?= $loan['book_id'] ?>" class="fw-semibold text-decoration-none">
                <?= e($loan['title']) ?>
              </a>
              <div class="small text-muted"><?= e($loan['author']) ?></div>
            </td>
            <td><?= formatDate($loan['loan_date']) ?></td>
            <td>
              <?= formatDate($loan['due_date']) ?>
              <?php if ($loan['status'] === 'overdue'): ?>
                <br><small class="text-danger">Terlambat <?= abs($daysLeft) ?> hari</small>
              <?php elseif ($loan['status'] === 'active' && $daysLeft <= 2): ?>
                <br><small class="text-warning"><?= $daysLeft ?> hari lagi</small>
              <?php endif; ?>
            </td>
            <td><?= $loan['return_date'] ? formatDate($loan['return_date']) : '<span class="text-muted">Belum</span>' ?></td>
            <td><?= loanStatusBadge($loan['status']) ?></td>
            <td>
              <?php if ($fine > 0): ?>
                <span class="fw-semibold text-danger"><?= formatRupiah($fine) ?></span>
                <?php if ($loan['fine_paid']): ?>
                  <br><span class="badge bg-success">Lunas</span>
                <?php else: ?>
                  <br><span class="badge bg-warning text-dark">Belum Bayar</span>
                <?php endif; ?>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?= $loan['extension_count'] ?>/<?= MAX_EXTENSIONS ?>
            </td>
            <td>
              <?php if (in_array($loan['status'], ['active']) && $loan['extension_count'] < MAX_EXTENSIONS): ?>
              <a href="<?= BASE_URL ?>student/extend_loan.php?id=<?= $loan['id'] ?>"
                 class="btn btn-sm btn-outline-success"
                 data-confirm="Perpanjang peminjaman buku '<?= e(addslashes($loan['title'])) ?>'?"
                 data-confirm-type="question">
                <i class="bi bi-arrow-clockwise"></i> Perpanjang
              </a>

              <?php else: ?>
                <span class="text-muted small">—</span>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
