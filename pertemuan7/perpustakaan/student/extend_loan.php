<?php
require_once __DIR__ . '/../config/config.php';
requireRole('student');

$db     = getDB();
$loanId = (int)($_GET['id'] ?? 0);
$user   = currentUser();

// Ambil data loan milik user
$loan = $db->prepare("
    SELECT l.*, b.title FROM loans l
    JOIN books b ON l.book_id = b.id
    WHERE l.id = ? AND l.user_id = ?
");
$loan->execute([$loanId, $user['id']]);
$loan = $loan->fetch();

if (!$loan) {
    setFlash('danger', 'Data peminjaman tidak ditemukan.');
    redirect(BASE_URL . 'student/my_loans.php');
}

if ($loan['status'] !== 'active') {
    setFlash('warning', 'Hanya peminjaman aktif yang dapat diperpanjang.');
    redirect(BASE_URL . 'student/my_loans.php');
}

if ($loan['extension_count'] >= MAX_EXTENSIONS) {
    setFlash('warning', 'Peminjaman ini sudah mencapai batas maksimum perpanjangan (' . MAX_EXTENSIONS . 'x).');
    redirect(BASE_URL . 'student/my_loans.php');
}

// Cek reservasi – jika ada user lain yg reservasi, tidak bisa diperpanjang
$hasReservation = $db->prepare("SELECT id FROM reservations WHERE book_id=? AND status='waiting' AND user_id != ?");
$hasReservation->execute([$loan['book_id'], $user['id']]);
if ($hasReservation->fetch()) {
    setFlash('warning', 'Tidak dapat diperpanjang. Ada pengguna lain yang mereservasi buku ini.');
    redirect(BASE_URL . 'student/my_loans.php');
}

// Proses perpanjangan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $newDueDate = date('Y-m-d', strtotime($loan['due_date'] . ' +' . EXTENSION_DAYS . ' days'));
    $db->prepare("UPDATE loans SET due_date=?, extension_count=extension_count+1, updated_at=NOW() WHERE id=?")
       ->execute([$newDueDate, $loanId]);
    setFlash('success', 'Peminjaman berhasil diperpanjang hingga ' . formatDate($newDueDate) . '.');
    redirect(BASE_URL . 'student/my_loans.php');
}

$pageTitle = 'Perpanjang Peminjaman';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><h5 class="mb-0 fw-bold"><i class="bi bi-arrow-clockwise me-2 text-success"></i>Perpanjang Peminjaman</h5></div>
      <div class="card-body">
        <dl class="row">
          <dt class="col-5">Judul Buku</dt><dd class="col-7"><?= e($loan['title']) ?></dd>
          <dt class="col-5">Jatuh Tempo</dt><dd class="col-7"><?= formatDate($loan['due_date']) ?></dd>
          <dt class="col-5">Perpanjangan ke-</dt><dd class="col-7"><?= $loan['extension_count'] + 1 ?> dari <?= MAX_EXTENSIONS ?></dd>
          <dt class="col-5">Jatuh Tempo Baru</dt>
          <dd class="col-7 fw-bold text-success">
            <?= formatDate(date('Y-m-d', strtotime($loan['due_date'] . ' +' . EXTENSION_DAYS . ' days'))) ?>
          </dd>
        </dl>
        <form method="POST">
          <?php csrfField(); ?>
          <button class="btn btn-success w-100" type="submit">
            <i class="bi bi-check-circle me-2"></i>Konfirmasi Perpanjangan
          </button>
        </form>
        <a href="<?= BASE_URL ?>student/my_loans.php" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
