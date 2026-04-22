<?php
require_once __DIR__ . '/../config/config.php';
requireRole('student');

$db = getDB();

// ─── Parameter pencarian & filter ─────────────────────────────────────────
$q        = trim($_GET['q']        ?? '');
$category = trim($_GET['category'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 12;
$offset   = ($page - 1) * $perPage;

// ─── Query buku ───────────────────────────────────────────────────────────
$where  = "WHERE b.is_deleted = 0";
$params = [];
if ($q) {
    $where  .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
    $params  = array_merge($params, ["%$q%", "%$q%", "%$q%"]);
}
if ($category) {
    $where  .= " AND b.category = ?";
    $params[] = $category;
}

// Count total
$countStmt = $db->prepare("SELECT COUNT(*) FROM books b $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

// Fetch data
$stmt = $db->prepare("SELECT * FROM books b $where ORDER BY b.title ASC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$books = $stmt->fetchAll();

// Daftar kategori untuk filter
$categories = $db->query("SELECT DISTINCT category FROM books WHERE is_deleted=0 AND category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Base URL untuk pagination
$paginationBase = BASE_URL . 'student/catalog.php?q=' . urlencode($q) . '&category=' . urlencode($category);

$pageTitle = 'Katalog Buku';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <h4 class="fw-bold mb-0"><i class="bi bi-search text-primary me-2"></i>Katalog Buku</h4>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end" id="searchForm">
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Cari Buku</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" name="q"
                 id="searchInput"
                 placeholder="Judul, penulis, atau ISBN…"
                 value="<?= e($q) ?>">
          <?php if ($q): ?>
            <a href="<?= BASE_URL ?>student/catalog.php" class="btn btn-outline-secondary">
              <i class="bi bi-x"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Kategori</label>
        <select class="form-select" name="category">
          <option value="">Semua Kategori</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
              <?= e($cat) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-search me-1"></i>Cari
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Result Info -->
<div class="d-flex align-items-center justify-content-between mb-3">
  <p class="text-muted mb-0">
    Ditemukan <strong><?= number_format($total) ?></strong> buku
    <?= $q ? 'untuk "<strong>' . e($q) . '</strong>"' : '' ?>
    <?= $category ? 'kategori "<strong>' . e($category) . '</strong>"' : '' ?>
  </p>
</div>

<!-- Book Grid -->
<?php if (empty($books)): ?>
<div class="text-center py-5">
  <i class="bi bi-book-x display-3 text-muted"></i>
  <h5 class="mt-3 text-muted">Buku tidak ditemukan</h5>
  <a href="<?= BASE_URL ?>student/catalog.php" class="btn btn-outline-primary mt-2">Lihat Semua Buku</a>
</div>
<?php else: ?>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4 mb-4">
  <?php foreach ($books as $book): ?>
  <div class="col">
    <div class="card h-100 book-card">
      <!-- Cover placeholder -->
      <div class="book-cover-wrap">
        <?php if ($book['cover_url']): ?>
          <img src="<?= e($book['cover_url']) ?>" class="book-cover-img" alt="<?= e($book['title']) ?>">
        <?php else: ?>
          <div class="book-cover-placeholder">
            <i class="bi bi-book"></i>
          </div>
        <?php endif; ?>
        <!-- Availability Badge -->
        <?php if ($book['available_copies'] > 0): ?>
          <span class="availability-badge available"><i class="bi bi-check-circle-fill me-1"></i>Tersedia</span>
        <?php else: ?>
          <span class="availability-badge unavailable"><i class="bi bi-x-circle-fill me-1"></i>Dipinjam</span>
        <?php endif; ?>
      </div>

      <div class="card-body d-flex flex-column">
        <span class="badge bg-secondary-subtle text-secondary mb-1 align-self-start small"><?= e($book['category'] ?? 'Umum') ?></span>
        <h6 class="card-title fw-bold mb-1 lh-sm"><?= e($book['title']) ?></h6>
        <p class="card-text text-muted small mb-2"><?= e($book['author']) ?></p>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <small class="text-muted">
            <i class="bi bi-archive me-1"></i><?= $book['available_copies'] ?>/<?= $book['total_copies'] ?> tersedia
          </small>
          <a href="<?= BASE_URL ?>student/book_detail.php?id=<?= $book['id'] ?>"
             class="btn btn-sm btn-primary">
            Detail
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?= paginate($total, $perPage, $page, $paginationBase) ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
