<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db = getDB();
$q  = trim($_GET['q'] ?? '');
$cat = trim($_GET['cat'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;

$where  = "WHERE is_deleted = 0";
$params = [];
if ($q) {
    $where  .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $params  = ["%$q%", "%$q%", "%$q%"];
}
if ($cat) {
    $where  .= " AND category = ?";
    $params[] = $cat;
}

$total = (int)$db->prepare("SELECT COUNT(*) FROM books $where")->execute($params) ?
         $db->prepare("SELECT COUNT(*) FROM books $where")->execute($params) | 0 : 0;

// re-execute for count
$countStmt = $db->prepare("SELECT COUNT(*) FROM books $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$books = $db->prepare("SELECT * FROM books $where ORDER BY title ASC LIMIT $perPage OFFSET $offset");
$books->execute($params);
$books = $books->fetchAll();

$categories = $db->query("SELECT DISTINCT category FROM books WHERE is_deleted=0 AND category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$paginationBase = BASE_URL . 'librarian/books.php?q=' . urlencode($q) . '&cat=' . urlencode($cat);

$pageTitle = 'Manajemen Buku';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <h4 class="fw-bold mb-0"><i class="bi bi-journals text-primary me-2"></i>Manajemen Buku</h4>
  <a href="<?= BASE_URL ?>librarian/book_add.php" class="btn btn-primary ms-auto">
    <i class="bi bi-plus-lg me-2"></i>Tambah Buku
  </a>
</div>

<!-- Search -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" name="q" placeholder="Cari judul, penulis, ISBN…" value="<?= e($q) ?>">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="cat">
          <option value="">Semua Kategori</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= e($c) ?>" <?= $cat === $c ? 'selected' : '' ?>><?= e($c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i> Cari</button></div>
      <div class="col-md-1"><a href="<?= BASE_URL ?>librarian/books.php" class="btn btn-outline-secondary w-100"><i class="bi bi-x"></i></a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex align-items-center">
    <span class="text-muted small">Total: <strong><?= number_format($total) ?></strong> buku</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="booksTable">
      <thead class="table-light">
        <tr><th>#</th><th>Sampul</th><th>Judul & Penulis</th><th>ISBN</th><th>Kategori</th><th>Tahun</th><th class="text-center">Tersedia</th><th class="text-center">Total</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($books as $i => $b): ?>
      <tr>
        <td class="text-muted"><?= $offset + $i + 1 ?></td>
        <td>
          <div class="rounded bg-light d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 60px; overflow: hidden;">
            <?php if ($b['cover_url']): ?>
              <img src="<?= ASSETS_URL . '../' . $b['cover_url'] ?>" alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <i class="bi bi-book text-muted small"></i>
            <?php endif; ?>
          </div>
        </td>
        <td>
          <div class="fw-semibold text-truncate" style="max-width: 250px;"><?= e($b['title']) ?></div>
          <small class="text-muted"><?= e($b['author']) ?></small>
        </td>
        <td><small class="font-monospace"><?= e($b['isbn'] ?? '-') ?></small></td>
        <td><span class="badge bg-secondary-subtle text-secondary small"><?= e($b['category'] ?? '-') ?></span></td>

        <td><?= e($b['year'] ?? '-') ?></td>
        <td class="text-center">
          <?php if ($b['available_copies'] == 0): ?>
            <span class="badge bg-danger"><?= $b['available_copies'] ?></span>
          <?php elseif ($b['available_copies'] < $b['total_copies']): ?>
            <span class="badge bg-warning text-dark"><?= $b['available_copies'] ?></span>
          <?php else: ?>
            <span class="badge bg-success"><?= $b['available_copies'] ?></span>
          <?php endif; ?>
        </td>
        <td class="text-center"><?= $b['total_copies'] ?></td>
        <td><small><?= e($b['rack_location'] ?? '-') ?></small></td>
        <td>
          <div class="d-flex gap-1">
            <a href="<?= BASE_URL ?>librarian/book_edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="<?= BASE_URL ?>librarian/book_delete.php?id=<?= $b['id'] ?>"
               class="btn btn-sm btn-outline-danger" title="Hapus"
               data-confirm="Hapus buku '<?= e(addslashes($b['title'])) ?>'?"
               data-confirm-type="error">
              <i class="bi bi-trash"></i>
            </a>

          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($books)): ?>
        <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada buku ditemukan.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($total > $perPage): ?>
  <div class="card-footer"><?= paginate($total, $perPage, $page, $paginationBase) ?></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
