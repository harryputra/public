<?php
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

$db     = getDB();
$errors = [];
$data   = ['full_name'=>'','nim'=>'','email'=>'','role'=>'student','is_active'=>1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'nim'       => trim($_POST['nim']       ?? '') ?: null,
        'email'     => trim($_POST['email']     ?? ''),
        'role'      => in_array($_POST['role'] ?? '', ['student','librarian','admin']) ? $_POST['role'] : 'student',
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'password'  => $_POST['password']  ?? '',
        'password2' => $_POST['password2'] ?? '',
    ];

    if (!$data['full_name']) $errors[] = 'Nama lengkap wajib diisi.';
    if (!$data['email'])     $errors[] = 'Email wajib diisi.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($data['password']) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($data['password'] !== $data['password2']) $errors[] = 'Konfirmasi password tidak cocok.';

    // Cek email unik
    if ($data['email']) {
        $chk = $db->prepare("SELECT id FROM users WHERE email=?");
        $chk->execute([$data['email']]);
        if ($chk->fetch()) $errors[] = 'Email sudah digunakan.';
    }

    if (empty($errors)) {
        $db->prepare("INSERT INTO users (full_name, nim, email, password, role, is_active) VALUES (?,?,?,?,?,?)")
           ->execute([
               $data['full_name'], $data['nim'], $data['email'],
               password_hash($data['password'], PASSWORD_BCRYPT),
               $data['role'], $data['is_active'],
           ]);
        setFlash('success', 'Pengguna "' . $data['full_name'] . '" berhasil ditambahkan.');
        redirect(BASE_URL . 'admin/users.php');
    }
}

$pageTitle = 'Tambah Pengguna';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
<div class="col-xl-9">
  <div class="d-flex align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Manajemen Pengguna</h4>
      <p class="text-muted small mb-0">Daftarkan akun Mahasiswa, Pustakawan, atau Admin baru</p>
    </div>
    <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-outline-secondary ms-auto">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i>Terjadi Kendala:</div>
      <ul class="mb-0 small">
        <?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST">
    <?php csrfField(); ?>
    <div class="row g-4">
      <!-- ─── SISI KIRI: DATA PERSONAL ─── -->
      <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i>Informasi Personal</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Nama Lengkap <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="full_name" value="<?= e($data['full_name']) ?>" placeholder="Contoh: Andi Pratama" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">NIM (Opsional)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0 font-monospace" name="nim" value="<?= e($data['nim'] ?? '') ?>" placeholder="2021xxxx">
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Email <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                  <input type="email" class="form-control border-start-0 ps-0" name="email" value="<?= e($data['email']) ?>" placeholder="user@domain.com" required>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── SISI KANAN: PENGATURAN AKUN ─── -->
      <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock text-success me-2"></i>Pengaturan Akun</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Level Akses (Role)</label>
              <select class="form-select" name="role">
                <option value="student"   <?= $data['role']==='student'   ? 'selected':'' ?>>Mahasiswa (Akses Katalog)</option>
                <option value="librarian" <?= $data['role']==='librarian' ? 'selected':'' ?>>Pustakawan (Olah Data)</option>
                <option value="admin"     <?= $data['role']==='admin'     ? 'selected':'' ?>>Administrator (Sistem)</option>
              </select>
            </div>

            <div class="mb-3">
              <div class="form-check form-switch p-0 ms-4">
                <input class="form-check-input ms-n4" type="checkbox" name="is_active" id="is_active" value="1" <?= $data['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="is_active">Status Akun Aktif</label>
                <div class="form-text small">Akun nonaktif tidak bisa login.</div>
              </div>
            </div>

            <hr class="my-4 opacity-10">

            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="password" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="mb-0">
              <label class="form-label small fw-bold text-uppercase text-muted">Ulangi Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="password2" placeholder="Konfirmasi password" required minlength="6">
            </div>
          </div>
        </div>
      </div>

      <!-- ─── TOMBOL AKSI ─── -->
      <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm bg-light">
          <div class="card-body d-flex gap-2 justify-content-center py-3">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
              <i class="bi bi-check-circle me-2"></i>Simpan Pengguna Baru
            </button>
            <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-white border px-4 py-2">Batal</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
