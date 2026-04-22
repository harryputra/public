<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db     = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $userId = (int)($_POST['user_id'] ?? 0);
    $bookId = (int)($_POST['book_id'] ?? 0);

    // Validasi user
    $user = $db->prepare("SELECT * FROM users WHERE id=? AND role='student' AND is_active=1");
    $user->execute([$userId]);
    $user = $user->fetch();
    if (!$user) $errors[] = 'Mahasiswa tidak ditemukan atau tidak aktif.';

    // Validasi buku
    $book = $db->prepare("SELECT * FROM books WHERE id=? AND is_deleted=0 AND available_copies > 0");
    $book->execute([$bookId]);
    $book = $book->fetch();
    if (!$book) $errors[] = 'Buku tidak ditemukan atau tidak tersedia.';

    // Cek batas pinjam aktif
    if ($user && countActiveLoans($userId) >= MAX_ACTIVE_LOANS) {
        $errors[] = 'Mahasiswa sudah mencapai batas maksimum peminjaman aktif (' . MAX_ACTIVE_LOANS . ' buku).';
    }

    // Cek duplikat – user sudah pinjam buku ini
    if ($user && $book) {
        $dup = $db->prepare("SELECT id FROM loans WHERE user_id=? AND book_id=? AND status IN ('active','overdue')");
        $dup->execute([$userId, $bookId]);
        if ($dup->fetch()) $errors[] = 'Mahasiswa sudah meminjam buku ini dan belum mengembalikannya.';
    }

    if (empty($errors)) {
        $loanDate = date('Y-m-d');
        $dueDate  = date('Y-m-d', strtotime('+' . LOAN_DURATION_DAYS . ' days'));

        $db->prepare("INSERT INTO loans (user_id, book_id, loan_date, due_date, status) VALUES (?,?,?,?,'active')")
           ->execute([$userId, $bookId, $loanDate, $dueDate]);

        $db->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id=?")
           ->execute([$bookId]);

        // Hapus reservasi jika ada
        $db->prepare("UPDATE reservations SET status='fulfilled', updated_at=NOW() WHERE user_id=? AND book_id=? AND status='waiting'")
           ->execute([$userId, $bookId]);

        setFlash('success', 'Peminjaman buku "' . $book['title'] . '" untuk ' . $user['full_name'] . ' berhasil diproses. Jatuh tempo: ' . formatDate($dueDate) . '.');
        redirect(BASE_URL . 'librarian/loans.php');
    }
}

// Data untuk form
$students = $db->query("SELECT id, full_name, nim FROM users WHERE role='student' AND is_active=1 ORDER BY full_name")->fetchAll();
$books    = $db->query("SELECT id, title, author, available_copies FROM books WHERE is_deleted=0 AND available_copies > 0 ORDER BY title")->fetchAll();

$pageTitle = 'Proses Peminjaman';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
<div class="col-xl-10">
  <div class="d-flex align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Sirkulasi: Peminjaman Baru</h4>
      <p class="text-muted small mb-0">Catat transaksi peminjaman buku untuk mahasiswa</p>
    </div>
    <a href="<?= BASE_URL ?>librarian/loans.php" class="btn btn-outline-secondary ms-auto">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i>Kendala Validasi:</div>
      <ul class="mb-0 small">
        <?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" id="loanForm">
    <?php csrfField(); ?>
    <div class="row g-4">
      <!-- ─── SISI KIRI: SELEKSI ENTITAS ─── -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-right text-primary me-2"></i>Detail Transaksi</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Mahasiswa (Peminjam) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-fill"></i></span>
                <select class="form-select border-start-0 ps-0" name="user_id" id="user_id" required>
                  <option value="">-- Cari Nama atau NIM --</option>
                  <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ((int)($_POST['user_id'] ?? 0) === $s['id']) ? 'selected' : '' ?>>
                      <?= e($s['full_name']) ?> (<?= e($s['nim']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mt-2" id="loanInfo"></div>
            </div>

            <div class="mb-0">
              <label class="form-label small fw-bold text-uppercase text-muted">Buku yang Dipinjam <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-book-half"></i></span>
                <select class="form-select border-start-0 ps-0" name="book_id" id="book_id" required>
                  <option value="">-- Pilih Judul Buku --</option>
                  <?php foreach ($books as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ((int)($_POST['book_id'] ?? 0) === $b['id']) ? 'selected' : '' ?>>
                      <?= e($b['title']) ?> (Tersedia: <?= $b['available_copies'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-text small mt-1">Hanya buku dengan stok tersedia yang muncul di daftar.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── SISI KANAN: RINGKASAN & JADWAL ─── -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-calendar-check text-success me-2"></i>Jadwal Peminjaman</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Tanggal Pinjam</label>
              <div class="p-2 bg-light border rounded text-center fw-bold">
                <?= formatDate(date('Y-m-d')) ?>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Batas Pengembalian</label>
              <div class="p-2 bg-primary bg-opacity-10 border border-primary border-opacity-20 rounded text-center text-primary fw-bold">
                <?= formatDate(date('Y-m-d', strtotime('+' . LOAN_DURATION_DAYS . ' days'))) ?>
              </div>
              <div class="form-text text-center small text-muted mt-1">Durasi peminjaman: <?= LOAN_DURATION_DAYS ?> hari</div>
            </div>

            <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning border-opacity-20">
              <h6 class="small fw-bold mb-2"><i class="bi bi-shield-exclamation me-1"></i>Kebijakan SmartLib</h6>
              <ul class="mb-0 x-small text-muted ps-3">
                <li>Maksimal <?= MAX_ACTIVE_LOANS ?> buku aktif per mahasiswa.</li>
                <li>Denda <?= formatRupiah(FINE_PER_DAY) ?>/hari keterlambatan.</li>
                <li>Perpanjangan maksimal <?= MAX_EXTENSIONS ?> kali.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── TOMBOL AKSI ─── -->
      <div class="col-12 mt-4 text-center">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
          <i class="bi bi-check-circle-fill me-2"></i>Konfirmasi Peminjaman
        </button>
        <a href="<?= BASE_URL ?>librarian/loans.php" class="btn btn-white border px-4 py-2 ms-2">Batal</a>
      </div>
    </div>
  </form>
</div>
</div>

<script>
const loanCounts = <?= json_encode(array_column(
    $db->query("SELECT user_id, COUNT(*) AS c FROM loans WHERE status IN ('active','overdue') GROUP BY user_id")->fetchAll(),
    'c', 'user_id'
)) ?>;
const maxLoans = <?= MAX_ACTIVE_LOANS ?>;

// Initialize Tom Select
const tsConfig = {
    create: false,
    sortField: { field: "text", direction: "asc" },
    render: {
        no_results: function(data, escape) {
            return '<div class="no-results">Data tidak ditemukan untuk "' + escape(data.input) + '"</div>';
        }
    }
};

const tsUser = new TomSelect('#user_id', tsConfig);
const tsBook = new TomSelect('#book_id', tsConfig);

// Sync logic for loan limits
tsUser.on('change', function(value) {
    const uid = parseInt(value);
    const info = document.getElementById('loanInfo');
    if (!uid) { info.innerHTML = ''; return; }
    
    const count = loanCounts[uid] || 0;
    const remaining = maxLoans - count;
    
    if (remaining <= 0) {
        info.innerHTML = '<div class="alert alert-danger py-1 px-2 x-small mb-0 animate__animated animate__shakeX"><i class="bi bi-x-circle me-1"></i>Batas peminjaman (' + maxLoans + ') tercapai</div>';
    } else {
        info.innerHTML = '<div class="alert alert-success py-1 px-2 x-small mb-0 animate__animated animate__fadeIn"><i class="bi bi-check-circle me-1"></i>Sisa kuota: ' + remaining + ' buku</div>';
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
