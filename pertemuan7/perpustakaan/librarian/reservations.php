<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db = getDB();

$status = trim($_GET['status'] ?? 'waiting');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$allowedStatus = ['waiting','notified','fulfilled','cancelled','expired'];
if (!in_array($status, $allowedStatus)) $status = 'waiting';

$countStmt = $db->prepare("SELECT COUNT(*) FROM reservations r JOIN users u ON r.user_id=u.id JOIN books b ON r.book_id=b.id WHERE r.status=?");
$countStmt->execute([$status]);
$total = (int)$countStmt->fetchColumn();

$reservations = $db->prepare("
    SELECT r.*, u.full_name, u.nim, b.title AS book_title, b.available_copies
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    WHERE r.status = ?
    ORDER BY r.created_at ASC
    LIMIT $perPage OFFSET $offset
");
$reservations->execute([$status]);
$reservations = $reservations->fetchAll();

$paginationBase = BASE_URL . 'librarian/reservations.php?status=' . urlencode($status);

$tabs = [
    'waiting'   => ['Menunggu',      'warning'],
    'notified'  => ['Diberitahu',    'info'],
    'fulfilled' => ['Terpenuhi',     'success'],
    'cancelled' => ['Dibatalkan',    'secondary'],
    'expired'   => ['Kedaluwarsa',   'dark'],
];

// Hitung badge per status
$counts = [];
foreach (array_keys($tabs) as $s) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE status=?");
    $stmt->execute([$s]);
    $counts[$s] = (int)$stmt->fetchColumn();
}

$pageTitle = 'Manajemen Reservasi';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <h4 class="fw-bold mb-0"><i class="bi bi-bookmark-star text-warning me-2"></i>Manajemen Reservasi</h4>
</div>

<!-- Status Tabs -->
<ul class="nav nav-tabs mb-3">
  <?php foreach ($tabs as $s => [$label, $color]): ?>
  <li class="nav-item">
    <a class="nav-link <?= $status === $s ? 'active' : '' ?>"
       href="<?= BASE_URL ?>librarian/reservations.php?status=<?= $s ?>">
      <?= $label ?>
      <?php if ($counts[$s] > 0): ?>
        <span class="badge bg-<?= $color ?> ms-1"><?= $counts[$s] ?></span>
      <?php endif; ?>
    </a>
  </li>
  <?php endforeach; ?>
</ul>

<div class="card">
  <div class="card-header text-muted small">
    Total: <strong><?= number_format($total) ?></strong> reservasi
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Mahasiswa</th>
          <th>Judul Buku</th>
          <th>Tgl Reservasi</th>
          <th>Ketersediaan</th>
          <th>Status</th>
          <?php if ($status === 'waiting' || $status === 'notified'): ?><th>Aksi</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($reservations as $i => $res): ?>
      <tr>
        <td class="text-muted"><?= $offset + $i + 1 ?></td>
        <td>
          <div class="fw-semibold"><?= e($res['full_name']) ?></div>
          <small class="text-muted"><?= e($res['nim']) ?></small>
        </td>
        <td><?= e($res['book_title']) ?></td>
        <td><?= formatDate($res['created_at']) ?></td>
        <td>
          <?php if ($res['available_copies'] > 0): ?>
            <span class="badge bg-success">Tersedia (<?= $res['available_copies'] ?>)</span>
          <?php else: ?>
            <span class="badge bg-danger">Habis</span>
          <?php endif; ?>
        </td>
        <td><?= reservationStatusBadge($res['status']) ?></td>
        <?php if ($status === 'waiting' || $status === 'notified'): ?>
        <td>
          <form method="POST" action="<?= BASE_URL ?>librarian/reservation_action.php" class="d-inline">
            <?php csrfField(); ?>
            <input type="hidden" name="id" value="<?= $res['id'] ?>">
            <input type="hidden" name="action" value="cancel">
            <button type="submit" class="btn btn-sm btn-outline-danger"
                    data-confirm="Batalkan reservasi ini?"
                    data-confirm-type="warning">
              <i class="bi bi-x-circle"></i> Batalkan
            </button>

          </form>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($reservations)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada reservasi.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($total > $perPage): ?>
  <div class="card-footer"><?= paginate($total, $perPage, $page, $paginationBase) ?></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
