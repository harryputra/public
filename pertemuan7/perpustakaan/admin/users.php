<?php
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

$db = getDB();

$q    = trim($_GET['q']    ?? '');
$role = trim($_GET['role'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$where  = "WHERE 1=1";
$params = [];
if ($q) {
    $where   .= " AND (full_name LIKE ? OR nim LIKE ? OR email LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($role) {
    $where   .= " AND role = ?";
    $params[] = $role;
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM users $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

$users = $db->prepare("SELECT * FROM users $where ORDER BY role, full_name LIMIT $perPage OFFSET $offset");
$users->execute($params);
$users = $users->fetchAll();

$paginationBase = BASE_URL . 'admin/users.php?q=' . urlencode($q) . '&role=' . urlencode($role);

$pageTitle = 'Manajemen Pengguna';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center mb-4">
  <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Manajemen Pengguna</h4>
  <a href="<?= BASE_URL ?>admin/user_add.php" class="btn btn-primary ms-auto">
    <i class="bi bi-person-plus me-2"></i>Tambah Pengguna
  </a>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" name="q" placeholder="Cari nama, NIM, email…" value="<?= e($q) ?>">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="role">
          <option value="">Semua Role</option>
          <option value="student"   <?= $role==='student'   ? 'selected':'' ?>>Mahasiswa</option>
          <option value="librarian" <?= $role==='librarian' ? 'selected':'' ?>>Pustakawan</option>
          <option value="admin"     <?= $role==='admin'     ? 'selected':'' ?>>Admin</option>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i> Cari</button></div>
      <div class="col-md-1"><a href="<?= BASE_URL ?>admin/users.php" class="btn btn-outline-secondary w-100"><i class="bi bi-x"></i></a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header text-muted small">Total: <strong><?= number_format($total) ?></strong> pengguna</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Nama</th><th>NIM/ID</th><th>Email</th><th>Role</th><th>Status</th><th>Tgl Daftar</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($users as $i => $u): ?>
      <tr>
        <td class="text-muted"><?= $offset + $i + 1 ?></td>
        <td class="fw-semibold"><?= e($u['full_name']) ?></td>
        <td class="font-monospace small"><?= e($u['nim'] ?? '—') ?></td>
        <td class="small"><?= e($u['email']) ?></td>
        <td>
          <?php $roleColors = ['admin'=>'danger','librarian'=>'primary','student'=>'secondary']; ?>
          <span class="badge bg-<?= $roleColors[$u['role']] ?? 'secondary' ?>">
            <?= ucfirst($u['role']) ?>
          </span>
        </td>
        <td><?= $u['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' ?></td>
        <td class="small"><?= formatDate($u['created_at']) ?></td>
        <td>
          <div class="d-flex gap-1">
            <a href="<?= BASE_URL ?>admin/user_edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <?php if ($u['id'] !== currentUser()['id']): ?>
            <a href="<?= BASE_URL ?>admin/user_delete.php?id=<?= $u['id'] ?>"
               class="btn btn-sm btn-outline-danger" title="Hapus"
               data-confirm="Hapus pengguna '<?= e(addslashes($u['full_name'])) ?>'?"
               data-confirm-type="error">
              <i class="bi bi-trash"></i>
            </a>

            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($users)): ?>
        <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada pengguna ditemukan.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($total > $perPage): ?>
  <div class="card-footer"><?= paginate($total, $perPage, $page, $paginationBase) ?></div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
