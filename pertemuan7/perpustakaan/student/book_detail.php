<?php
require_once __DIR__ . '/../config/config.php';
requireRole('student');

$db     = getDB();
$bookId = (int)($_GET['id'] ?? 0);
$user   = currentUser();

// Ambil data buku
$book = $db->prepare("SELECT * FROM books WHERE id=? AND is_deleted=0");
$book->execute([$bookId]);
$book = $book->fetch();

if (!$book) {
    setFlash('danger', 'Buku tidak ditemukan.');
    redirect(BASE_URL . 'student/catalog.php');
}

// Cek apakah user sudah meminjam buku ini
$alreadyBorrowed = $db->prepare("SELECT id FROM loans WHERE user_id=? AND book_id=? AND status IN ('active','overdue')");
$alreadyBorrowed->execute([$user['id'], $bookId]);
$hasBorrowed = (bool)$alreadyBorrowed->fetch();

// Cek reservasi aktif user untuk buku ini
$hasReservation = $db->prepare("SELECT id FROM reservations WHERE user_id=? AND book_id=? AND status IN ('waiting','ready')");
$hasReservation->execute([$user['id'], $bookId]);
$hasReserved = (bool)$hasReservation->fetch();

// Jumlah peminjaman aktif user
$activeCount = countActiveLoans($user['id']);

// Proses reservasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    if ($_POST['action'] === 'reserve' && !$hasReserved && $book['available_copies'] == 0) {
        $ins = $db->prepare("INSERT INTO reservations (user_id,book_id,status) VALUES (?,?,'waiting')");
        $ins->execute([$user['id'], $bookId]);
        setFlash('success', 'Reservasi berhasil! Anda akan diberitahu saat buku tersedia.');
        redirect(BASE_URL . 'student/book_detail.php?id=' . $bookId);
    }
}

$pageTitle = $book['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-light p-3 rounded-3 shadow-sm border-0">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>student/catalog.php" class="text-decoration-none"><i class="bi bi-house-door me-1"></i>Katalog</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= e(mb_strimwidth($book['title'], 0, 40, '…')) ?></li>
  </ol>
</nav>

<div class="row g-5">
  <!-- SISI KIRI: COVER & INTERACTION -->
  <div class="col-lg-4 col-xl-3">
    <div class="sticky-top" style="top: 2rem;">
      <div class="book-cover-card mb-4">
        <div class="card border-0 shadow-lg overflow-hidden rounded-4">
          <?php if ($book['cover_url']): ?>
            <img src="<?= ASSETS_URL . '../' . $book['cover_url'] ?>" class="img-fluid" alt="<?= e($book['title']) ?>" style="width: 100%; transition: transform 0.5s ease;">
          <?php else: ?>
            <div class="bg-primary-subtle d-flex flex-column align-items-center justify-content-center text-primary py-5" style="min-height: 350px;">
              <i class="bi bi-book display-1 opacity-25"></i>
              <span class="mt-2 fw-bold small text-uppercase">No Cover Available</span>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Action Box Card -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3 text-muted small text-uppercase ls-1">Status Ketersediaan</h6>
          
          <?php if ($hasBorrowed): ?>
             <div class="d-flex align-items-center p-3 rounded-3 bg-info-subtle text-info">
               <i class="bi bi-info-circle-fill fs-4 me-3"></i>
               <div class="small fw-semibold">Anda sedang meminjam buku ini.</div>
             </div>
          <?php elseif ($book['available_copies'] > 0): ?>
             <?php if ($activeCount < MAX_ACTIVE_LOANS): ?>
               <div class="text-center p-3 rounded-3 bg-success-subtle mb-3">
                 <div class="h2 fw-bold text-success mb-0"><?= $book['available_copies'] ?></div>
                 <div class="x-small fw-bold text-success text-uppercase">Tersedia Sekarang</div>
               </div>
               <div class="alert alert-light border small mb-0">
                 <i class="bi bi-info-circle me-1"></i> Silakan hubungi pustakawan dengan membawa kartu mahasiswa.
               </div>
             <?php else: ?>
               <div class="alert alert-warning border-0 small mb-0">
                 <i class="bi bi-exclamation-triangle-fill me-2"></i> Kuota pinjam penuh (Maks <?= MAX_ACTIVE_LOANS ?> buku).
               </div>
             <?php endif; ?>
          <?php else: ?>
            <!-- STOCK HABIS -->
            <?php if ($hasReserved): ?>
              <div class="p-3 rounded-3 bg-info-subtle text-info text-center">
                <i class="bi bi-bookmark-check-fill fs-3 mb-2 d-block"></i>
                <div class="fw-bold small px-2">Anda sudah dalam antrean reservasi.</div>
              </div>
            <?php else: ?>
               <div class="text-center mb-3">
                 <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill small">Semua Dipinjam</span>
               </div>
               <form method="POST" data-confirm="Lanjutkan reservasi untuk buku ini?" data-confirm-type="question">
                 <?php csrfField(); ?>
                 <input type="hidden" name="action" value="reserve">
                 <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm rounded-3">
                   <i class="bi bi-bookmark-plus-fill me-2"></i>Reservasi Buku
                 </button>
               </form>
               <p class="x-small text-muted mt-2 text-center">Kami akan memberi tahu Anda via email saat buku kembali.</p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- SISI KANAN: DETAIL METADATA -->
  <div class="col-lg-8 col-xl-9">
    <div class="ps-lg-4">
      <div class="mb-4">
        <a href="<?= BASE_URL ?>student/catalog.php?category=<?= urlencode($book['category'] ?? 'Umum') ?>" class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill text-decoration-none mb-3">
          <i class="bi bi-tag-fill me-1"></i><?= e($book['category'] ?? 'Umum') ?>
        </a>
        <h2 class="display-6 fw-bold mb-2"><?= e($book['title']) ?></h2>
        <p class="fs-4 text-muted mb-0"><?= e($book['author']) ?></p>
      </div>

      <div class="row g-3 mb-5">
        <?php 
          $metaIcons = [
            'ISBN'      => 'bi bi-hash',
            'Penerbit'  => 'bi bi-building',
            'Tahun'     => 'bi bi-calendar3',
            'Lokasi Rak'=> 'bi bi-geo-alt-fill'
          ];
          $meta = [
            ['ISBN',      $book['isbn']         ?? '-'],
            ['Penerbit',  $book['publisher']    ?? '-'],
            ['Tahun',     $book['year']         ?? '-'],
            ['Lokasi Rak',$book['rack_location']?? '-'],
          ]; 
        ?>
        <?php foreach ($meta as [$label, $val]): ?>
        <div class="col-sm-6 col-md-3">
          <div class="card border-0 bg-light rounded-4 h-100">
            <div class="card-body p-3 text-center">
              <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">
                <i class="<?= $metaIcons[$label] ?> text-primary"></i>
              </div>
              <div class="x-small text-muted text-uppercase fw-bold"><?= e($label) ?></div>
              <div class="fw-bold mt-1 text-dark"><?= e($val) ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Deskripsi -->
      <?php if ($book['description']): ?>
      <div class="mb-5">
        <h5 class="fw-bold mb-3 d-flex align-items-center">
          <span class="bg-primary rounded-pill me-2" style="width: 4px; height: 20px; display: inline-block;"></span>
          Sinopsis & Deskripsi
        </h5>
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-4 text-muted lh-lg" style="text-align: justify;">
             <?= nl2br(e($book['description'])) ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Info Tambahan / Policy -->
      <div class="alert alert-secondary border-0 rounded-4 p-4 shadow-sm">
        <div class="d-flex align-items-start">
          <i class="bi bi-info-square-fill fs-3 text-secondary me-3"></i>
          <div>
            <h6 class="fw-bold mb-1">Kebijakan Peminjaman</h6>
            <p class="small mb-0 opacity-75">Buku ini dapat dipinjam selama <?= LOAN_DURATION_DAYS ?> hari. Jika Anda terlambat mengembalikan, akan dikenakan denda sebesar <?= formatRupiah(FINE_PER_DAY) ?> per hari.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
