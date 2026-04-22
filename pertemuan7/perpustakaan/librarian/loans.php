<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db = getDB();

$q      = trim($_GET['q']      ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$where  = "WHERE 1=1";
$params = [];

if ($q) {
    $where   .= " AND (u.full_name LIKE ? OR u.nim LIKE ? OR b.title LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($status) {
    $where   .= " AND l.status = ?";
    $params[] = $status;
}

$countStmt = $db->prepare("
    SELECT COUNT(*) FROM loans l
    JOIN users u ON l.user_id = u.id
    JOIN books b ON l.book_id = b.id
    $where
");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$loans = $db->prepare("
    SELECT l.*, u.full_name, u.nim, b.title AS book_title
    FROM loans l
    JOIN users u ON l.user_id = u.id
    JOIN books b ON l.book_id = b.id
    $where
    ORDER BY l.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$loans->execute($params);
$loans = $loans->fetchAll();

$paginationBase = BASE_URL . 'librarian/loans.php?q=' . urlencode($q) . '&status=' . urlencode($status);

$pageTitle = 'Daftar Peminjaman';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <h4 class="fw-bold mb-0"><i class="bi bi-journal-text text-primary me-2"></i>Daftar Peminjaman</h4>
  <a href="<?= BASE_URL ?>librarian/loan_add.php" class="btn btn-primary ms-auto">
    <i class="bi bi-plus-circle me-2"></i>Proses Peminjaman Baru
  </a>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" name="q" placeholder="Cari nama, NIM, atau judul buku…" value="<?= e($q) ?>">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="status">
          <option value="">Semua Status</option>
          <option value="active"   <?= $status==='active'   ? 'selected':'' ?>>Aktif</option>
          <option value="overdue"  <?= $status==='overdue'  ? 'selected':'' ?>>Terlambat</option>
          <option value="returned" <?= $status==='returned' ? 'selected':'' ?>>Dikembalikan</option>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i> Cari</button></div>
      <div class="col-md-1"><a href="<?= BASE_URL ?>librarian/loans.php" class="btn btn-outline-secondary w-100"><i class="bi bi-x"></i></a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header text-muted small">
    Total: <strong><?= number_format($total) ?></strong> peminjaman
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Peminjam</th>
          <th>Buku</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Tgl Kembali</th>
          <th>Status</th>
          <th>Denda</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($loans as $i => $loan): ?>
      <tr>
        <td class="text-muted"><?= $offset + $i + 1 ?></td>
        <td>
          <div class="fw-semibold"><?= e($loan['full_name']) ?></div>
          <small class="text-muted"><?= e($loan['nim']) ?></small>
        </td>
        <td><?= e($loan['book_title']) ?></td>
        <td><?= formatDate($loan['loan_date']) ?></td>
        <td class="<?= $loan['status']==='overdue' ? 'text-danger fw-semibold' : '' ?>">
          <?= formatDate($loan['due_date']) ?>
        </td>
        <td><?= $loan['return_date'] ? formatDate($loan['return_date']) : '<span class="text-muted">—</span>' ?></td>
        <td><?= loanStatusBadge($loan['status']) ?></td>
        <td>
          <?php $fine = calculateFine($loan['due_date'], $loan['return_date'] ?? null); ?>
          <?php if ($fine > 0): ?>
            <span class="text-danger fw-semibold"><?= formatRupiah($fine) ?></span>
            <?php if ($loan['fine_paid']): ?>
              <span class="badge bg-success ms-1">Lunas</span>
            <?php endif; ?>
          <?php else: ?>
            <span class="text-muted">—</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($loan['status'] !== 'returned'): ?>
          <a href="<?= BASE_URL ?>librarian/return_book.php?id=<?= $loan['id'] ?>"
             class="btn btn-sm btn-outline-success" title="Proses Pengembalian">
            <i class="bi bi-arrow-return-left"></i>
          </a>
          <?php else: ?>
          <span class="text-muted small">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($loans)): ?>
        <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data peminjaman.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($total > $perPage): ?>
  <div class="card-footer"><?= paginate($total, $perPage, $page, $paginationBase) ?></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
