<?php
require_once __DIR__ . '/config/config.php';

// Sudah login → ke dashboard
if (isLoggedIn()) redirect(dashboardUrl());

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = getDB()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['is_active'] && password_verify($password, $user['password'])) {
            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);

            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['full_name'];
            $_SESSION['user_role']  = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nim']   = $user['nim'];

            setFlash('success', 'Selamat datang, ' . $user['full_name'] . '!');
            redirect(dashboardUrl());
        } elseif ($user && !$user['is_active']) {
            $error = 'Akun Anda tidak aktif. Hubungi administrator.';
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

$pageTitle = 'Login';
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/style.css">
<style>
  body { background: linear-gradient(135deg, #1a3a6b 0%, #2563eb 100%); min-height: 100vh; }
  .login-card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
  .login-brand { color: #2563eb; }
</style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
<div class="container" style="max-width:420px">
  <div class="card login-card">
    <div class="card-body p-5">
      <!-- Brand -->
      <div class="text-center mb-4">
        <i class="bi bi-book-half display-4 login-brand"></i>
        <h2 class="fw-bold mt-2 login-brand"><?= APP_NAME ?></h2>
        <p class="text-muted mb-0">Sistem Manajemen Perpustakaan Kampus</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger" id="login-error" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" novalidate id="loginForm">
        <?php csrfField(); ?>

        <div class="mb-3">
          <label class="form-label fw-semibold" for="email">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="nama@domain.com"
                   value="<?= e($_POST['email'] ?? '') ?>"
                   required autofocus>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold" for="password">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="••••••••" required>
            <button type="button" class="btn btn-outline-secondary" id="togglePwd" tabindex="-1">
              <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg fw-semibold" id="btnLogin">
          <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
      </form>

      <hr class="my-4">
      <div class="demo-accounts">
        <h6 class="text-muted small fw-bold text-uppercase mb-3">
          <i class="bi bi-person-badge me-1"></i> Akun Demo (Password: <code>smartlib123</code>)
        </h6>
        <div class="row g-2">
          <div class="col-6">
            <div class="p-2 border rounded bg-light small">
              <span class="badge bg-danger mb-1">Admin</span><br>
              <code>admin@smartlib.id</code>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 border rounded bg-light small">
              <span class="badge bg-warning text-dark mb-1">Pustakawan</span><br>
              <code>pustakawan@smartlib.id</code>
            </div>
          </div>
          <div class="col-12">
            <div class="p-2 border rounded bg-light small">
              <span class="badge bg-success mb-1">Mahasiswa</span><br>
              <code>andi@student.polman.id</code>, <code>siti@...</code>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <p class="text-center text-white-50 mt-3 small">&copy; <?= date('Y') ?> <?= APP_NAME ?></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('togglePwd').addEventListener('click', function () {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') { pwd.type = 'text';     icon.className = 'bi bi-eye-slash'; }
    else                          { pwd.type = 'password'; icon.className = 'bi bi-eye'; }
});
</script>
</body>
</html>
