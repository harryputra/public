<?php
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

$db     = getDB();
$userId = (int)($_GET['id'] ?? 0);

$user = $db->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$userId]);
$user = $user->fetch();

if (!$user) {
    setFlash('danger', 'Pengguna tidak ditemukan.');
    redirect(BASE_URL . 'admin/users.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'nim'       => trim($_POST['nim']       ?? '') ?: null,
        'email'     => trim($_POST['email']     ?? ''),
        'role'      => in_array($_POST['role'] ?? '', ['student','librarian','admin']) ? $_POST['role'] : $user['role'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'password'  => $_POST['password']  ?? '',
        'password2' => $_POST['password2'] ?? '',
    ];

    if (!$data['full_name']) $errors[] = 'Nama lengkap wajib diisi.';
    if (!$data['email'])     $errors[] = 'Email wajib diisi.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

    if ($data['password'] !== '') {
        if (strlen($data['password']) < 6) $errors[] = 'Password minimal 6 karakter.';
        if ($data['password'] !== $data['password2']) $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // Cek email unik (kecuali diri sendiri)
    if ($data['email']) {
        $chk = $db->prepare("SELECT id FROM users WHERE email=? AND id != ?");
        $chk->execute([$data['email'], $userId]);
        if ($chk->fetch()) $errors[] = 'Email sudah digunakan pengguna lain.';
    }

    // Cegah admin menonaktifkan diri sendiri
    if ($userId === currentUser()['id'] && !$data['is_active']) {
        $errors[] = 'Anda tidak dapat menonaktifkan akun Anda sendiri.';
    }

    if (empty($errors)) {
        if ($data['password'] !== '') {
            $db->prepare("UPDATE users SET full_name=?,nim=?,email=?,role=?,is_active=?,password=?,updated_at=NOW() WHERE id=?")
               ->execute([$data['full_name'],$data['nim'],$data['email'],$data['role'],$data['is_active'],
                          password_hash($data['password'], PASSWORD_BCRYPT), $userId]);
        } else {
            $db->prepare("UPDATE users SET full_name=?,nim=?,email=?,role=?,is_active=?,updated_at=NOW() WHERE id=?")
               ->execute([$data['full_name'],$data['nim'],$data['email'],$data['role'],$data['is_active'],$userId]);
        }
        setFlash('success', 'Pengguna "' . $data['full_name'] . '" berhasil diperbarui.');
        redirect(BASE_URL . 'admin/users.php');
    }
    $user = array_merge($user, $data);
}

$pageTitle = 'Edit Pengguna';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
<div class="col-xl-7">
  <div class="d-flex align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-person-gear text-warning me-2"></i>Edit Pengguna</h4>
    <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-outline-secondary ms-auto">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e): ?><div>❌ <?= e($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST">
        <?php csrfField(); ?>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="full_name" value="<?= e($user['full_name']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">NIM</label>
            <input type="text" class="form-control font-monospace" name="nim" value="<?= e($user['nim'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" value="<?= e($user['email']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Role</label>
            <select class="form-select" name="role" <?= $userId === currentUser()['id'] ? 'disabled' : '' ?>>
              <option value="student"   <?= $user['role']==='student'   ? 'selected':'' ?>>Mahasiswa</option>
              <option value="librarian" <?= $user['role']==='librarian' ? 'selected':'' ?>>Pustakawan</option>
              <option value="admin"     <?= $user['role']==='admin'     ? 'selected':'' ?>>Admin</option>
            </select>
            <?php if ($userId === currentUser()['id']): ?>
              <input type="hidden" name="role" value="<?= e($user['role']) ?>">
            <?php endif; ?>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                     <?= $user['is_active'] ? 'checked' : '' ?>
                     <?= $userId === currentUser()['id'] ? 'disabled' : '' ?>>
              <label class="form-check-label" for="is_active">Akun Aktif</label>
            </div>
            <?php if ($userId === currentUser()['id']): ?>
              <input type="hidden" name="is_active" value="1">
            <?php endif; ?>
          </div>
          <div class="col-12">
            <hr><p class="text-muted small mb-2">Kosongkan jika tidak ingin mengubah password.</p>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Password Baru</label>
            <input type="password" class="form-control" name="password" minlength="6">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Konfirmasi Password</label>
            <input type="password" class="form-control" name="password2" minlength="6">
          </div>
          <div class="col-12"><hr>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-check-lg me-2"></i>Perbarui Pengguna
              </button>
              <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-outline-secondary">Batal</a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
