<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db     = getDB();
$loanId = (int)($_GET['id'] ?? 0);

$loan = $db->prepare("
    SELECT l.*, 
           u.full_name, u.nim, u.phone,
           b.title AS book_title, b.author, b.cover_url, b.id AS book_id, b.rack_location
    FROM loans l
    JOIN users u ON l.user_id = u.id
    JOIN books b ON l.book_id = b.id
    WHERE l.id = ? AND l.status IN ('active','overdue')
");
$loan->execute([$loanId]);
$loan = $loan->fetch();


if (!$loan) {
    setFlash('danger', 'Data peminjaman tidak ditemukan atau sudah dikembalikan.');
    redirect(BASE_URL . 'librarian/loans.php');
}

$today      = date('Y-m-d');
$fine       = calculateFine($loan['due_date']);
$isOverdue  = $loan['status'] === 'overdue';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $finePaid = isset($_POST['fine_paid']) ? 1 : 0;

    $db->prepare("
        UPDATE loans
        SET status='returned', return_date=?, fine_amount=?, fine_paid=?, updated_at=NOW()
        WHERE id=?
    ")->execute([$today, $fine, $finePaid, $loanId]);

    $db->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id=?")
       ->execute([$loan['book_id']]);

    // Aktifkan reservasi berikutnya jika ada
    $nextRes = $db->prepare("
        SELECT * FROM reservations WHERE book_id=? AND status='waiting' ORDER BY created_at ASC LIMIT 1
    ");
    $nextRes->execute([$loan['book_id']]);
    $nextRes = $nextRes->fetch();
    if ($nextRes) {
        $db->prepare("UPDATE reservations SET status='notified', updated_at=NOW() WHERE id=?")
           ->execute([$nextRes['id']]);
    }

    $msg = 'Buku "' . $loan['book_title'] . '" berhasil dikembalikan oleh ' . $loan['full_name'] . '.';
    if ($fine > 0) {
        $msg .= ' Denda: ' . formatRupiah($fine) . ($finePaid ? ' (Lunas).' : ' (Belum dibayar).');
    }
    setFlash('success', $msg);
    redirect(BASE_URL . 'librarian/loans.php');
}

$pageTitle = 'Proses Pengembalian';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
<div class="col-xl-10">
  <div class="d-flex align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Sirkulasi: Pengembalian Buku</h4>
      <p class="text-muted small mb-0">Verifikasi kondisi buku dan penyelesaian administrasi peminjaman</p>
    </div>
    <a href="<?= BASE_URL ?>librarian/loans.php" class="btn btn-outline-secondary ms-auto">
      <i class="bi bi-arrow-left me-1"></i>Batal & Kembali
    </a>
  </div>

  <form method="POST" id="returnForm">
    <?php csrfField(); ?>
    <div class="row g-4">
      <!-- ─── KOLOM KIRI: INFO PEMINJAM & PEMINJAMAN ─── -->
      <div class="col-lg-7">
        <!-- Card Peminjam -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-person-badge-fill me-2"></i>Informasi Peminjam</h6>
          </div>
          <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
              <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-person-fill fs-4"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0"><?= e($loan['full_name']) ?></h5>
                <p class="text-muted small mb-0"><?= e($loan['nim']) ?> &nbsp;•&nbsp; <i class="bi bi-telephone ms-1 me-1"></i><?= e($loan['phone'] ?? '-') ?></p>
              </div>
            </div>
            
            <hr class="text-muted opacity-25">

            <div class="row g-3">
              <div class="col-6">
                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-1">Tanggal Pinjam</label>
                <div class="fw-semibold"><i class="bi bi-calendar-check me-2 text-primary"></i><?= formatDate($loan['loan_date']) ?></div>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-1">Jatuh Tempo</label>
                <div class="fw-semibold <?= $isOverdue ? 'text-danger' : '' ?>">
                  <i class="bi bi-calendar-x me-2"></i><?= formatDate($loan['due_date']) ?>
                </div>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-1">Tanggal Kembali</label>
                <div class="fw-semibold text-success"><i class="bi bi-calendar-plus me-2"></i><?= formatDate($today) ?></div>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-1">Status Durasi</label>
                <?php if ($isOverdue): ?>
                  <span class="badge bg-danger">Terlambat <?= max(0, (int)((strtotime($today) - strtotime($loan['due_date'])) / 86400)) ?> Hari</span>
                <?php else: ?>
                  <span class="badge bg-success">Tepat Waktu</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Panel Denda -->
        <div class="card border-0 shadow-sm border-start border-4 <?= $fine > 0 ? 'border-danger' : 'border-success' ?>">
          <div class="card-body p-4">
            <div class="row align-items-center">
              <div class="col-md-7">
                <h6 class="fw-bold mb-1">Penyelesaian Administrasi</h6>
                <p class="text-muted small mb-0">Total denda dihitung otomatis berdasarkan peraturan keterlambatan.</p>
              </div>
              <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <?php if ($fine > 0): ?>
                  <div class="h3 fw-bold text-danger mb-0"><?= formatRupiah($fine) ?></div>
                  <div class="form-check d-inline-block mt-2">
                    <input class="form-check-input" type="checkbox" name="fine_paid" id="fine_paid" value="1">
                    <label class="form-check-label small fw-bold text-muted" for="fine_paid">Denda Sudah Dibayar</label>
                  </div>
                <?php else: ?>
                  <div class="h3 fw-bold text-success mb-0">Rp 0</div>
                  <span class="small text-muted">Tidak ada denda</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── KOLOM KANAN: DETAIL BUKU & KONFIRMASI ─── -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 text-center">
            <h6 class="fw-bold mb-0 text-muted small text-uppercase">Buku yang Dikembalikan</h6>
          </div>
          <div class="card-body p-4 text-center">
             <div class="mx-auto rounded shadow-sm bg-light mb-3 d-flex align-items-center justify-content-center" style="width: 140px; height: 200px; overflow: hidden;">
                <?php if ($loan['cover_url']): ?>
                  <img src="<?= ASSETS_URL . '../' . $loan['cover_url'] ?>" alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                  <i class="bi bi-book text-muted display-4"></i>
                <?php endif; ?>
             </div>
             <h5 class="fw-bold mb-1"><?= e($bookTitle = $loan['book_title']) ?></h5>
             <p class="text-muted mb-0 small"><?= e($loan['author']) ?></p>
             <div class="mt-3">
               <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i><?= e($loan['rack_location'] ?? 'Lt.1 Rak A1') ?></span>
             </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden position-relative">
          <div class="card-body p-4 position-relative" style="z-index: 2;">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2"></i>Konfirmasi Akhir</h6>
            <p class="small opacity-75 mb-4">Pastikan kondisi fisik buku sudah diperiksa dengan teliti sebelum menekan tombol konfirmasi di bawah ini.</p>
            <button type="submit" class="btn btn-white w-100 fw-bold py-2 shadow-sm text-primary">
              <i class="bi bi-check-circle-fill me-2"></i>Selesaikan Pengembalian
            </button>
          </div>
          <!-- Background Decoration -->
          <i class="bi bi-check-circle position-absolute" style="right: -20px; bottom: -20px; font-size: 8rem; opacity: 0.1; transform: rotate(-15deg);"></i>
        </div>
      </div>
    </div>
  </form>
</div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
