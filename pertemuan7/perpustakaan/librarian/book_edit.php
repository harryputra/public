<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db     = getDB();
$bookId = (int)($_GET['id'] ?? 0);

$book = $db->prepare("SELECT * FROM books WHERE id=? AND is_deleted=0");
$book->execute([$bookId]);
$book = $book->fetch();

if (!$book) {
    setFlash('danger', 'Buku tidak ditemukan.');
    redirect(BASE_URL . 'librarian/books.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $data = [
        'title'         => trim($_POST['title']         ?? ''),
        'author'        => trim($_POST['author']        ?? ''),
        'isbn'          => trim($_POST['isbn']          ?? '') ?: null,
        'publisher'     => trim($_POST['publisher']     ?? '') ?: null,
        'year'          => (int)($_POST['year']         ?? 0) ?: null,
        'category'      => trim($_POST['category']      ?? '') ?: null,
        'description'   => trim($_POST['description']   ?? '') ?: null,
        'total_copies'  => max(1, (int)($_POST['total_copies'] ?? 1)),
        'rack_location' => trim($_POST['rack_location'] ?? '') ?: null,
    ];

    if (!$data['title'])  $errors[] = 'Judul buku wajib diisi.';
    if (!$data['author']) $errors[] = 'Penulis wajib diisi.';

    if ($data['isbn']) {
        $chk = $db->prepare("SELECT id FROM books WHERE isbn=? AND id != ? AND is_deleted=0");
        $chk->execute([$data['isbn'], $bookId]);
        if ($chk->fetch()) $errors[] = 'ISBN sudah digunakan buku lain.';
    }

    // Hitung selisih eksemplar
    $diff = $data['total_copies'] - $book['total_copies'];
    $newAvailable = max(0, $book['available_copies'] + $diff);

    if (empty($errors)) {
        // Handle upload cover baru
        $coverUrl = $book['cover_url'];
        if (!empty($_FILES['cover']['name'])) {
            $uploadedPath = uploadImage($_FILES['cover']);
            if ($uploadedPath) {
                // Hapus cover lama jika ada
                if ($book['cover_url'] && file_exists(__DIR__ . '/../' . $book['cover_url'])) {
                    unlink(__DIR__ . '/../' . $book['cover_url']);
                }
                $coverUrl = $uploadedPath;
            } else {
                $errors[] = 'Gagal mengunggah sampul baru. Pastikan format JPG/PNG/WebP dan ukuran maks 2MB.';
            }
        }

        if (empty($errors)) {
            $db->prepare("UPDATE books SET title=?,author=?,isbn=?,publisher=?,year=?,category=?,description=?,
                          total_copies=?,available_copies=?,rack_location=?,cover_url=?,updated_at=NOW() WHERE id=?")
               ->execute([
                   $data['title'], $data['author'], $data['isbn'], $data['publisher'],
                   $data['year'], $data['category'], $data['description'],
                   $data['total_copies'], $newAvailable, $data['rack_location'], $coverUrl, $bookId,
               ]);
            setFlash('success', 'Buku "' . $data['title'] . '" berhasil diperbarui.');
            redirect(BASE_URL . 'librarian/books.php');
        }
    }

    // Merge untuk re-display
    $book = array_merge($book, $data);
}

$categories = ['Teknik Informatika','Pemrograman','Algoritma','Basis Data','Jaringan Komputer',
               'Sistem Operasi','Kecerdasan Buatan','Pengembangan Web','Pengujian Software',
               'Metodologi','Arsitektur Software','Matematika','Fisika','Kimia','Umum'];

$pageTitle = 'Edit Buku';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
<div class="col-xl-10">
  <div class="d-flex align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Manajemen Koleksi: Edit Buku</h4>
      <p class="text-muted small mb-0">Perbarui informasi katalog atau stok inventaris buku</p>
    </div>
    <a href="<?= BASE_URL ?>librarian/books.php" class="btn btn-outline-secondary ms-auto">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i>Terjadi Kendala Pembaruan:</div>
      <ul class="mb-0 small">
        <?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate enctype="multipart/form-data">
    <?php csrfField(); ?>
    <div class="row g-4">
      <!-- ─── SISI KIRI: METADATA BUKU ─── -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Metadata Buku</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Judul Buku <span class="text-danger">*</span></label>
              <input type="text" class="form-control fw-bold" name="title" value="<?= e($book['title']) ?>" placeholder="Judul buku…" required>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Penulis <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-circle"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0" name="author" value="<?= e($book['author']) ?>" required>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">ISBN / ISSN</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-hash"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0 font-monospace" name="isbn" value="<?= e($book['isbn'] ?? '') ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-8 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Penerbit</label>
                <input type="text" class="form-control" name="publisher" value="<?= e($book['publisher'] ?? '') ?>">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Tahun</label>
                <input type="number" class="form-control text-center" name="year" value="<?= e($book['year'] ?? '') ?>" min="1900" max="<?= date('Y') ?>">
              </div>
            </div>

            <div class="mb-0">
              <label class="form-label small fw-bold text-uppercase text-muted">Deskripsi / Sinopsis</label>
              <textarea class="form-control" name="description" rows="5"><?= e($book['description'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── SISI KANAN: SAMPUL & STOK ─── -->
      <div class="col-lg-5">
        <!-- MANAJEMEN SAMPUL -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-info"><i class="bi bi-image me-2"></i>Sampul Buku</h6>
          </div>
          <div class="card-body p-4 text-center">
            <div class="mb-3">
              <div id="previewContainer" class="mx-auto border rounded d-flex align-items-center justify-content-center bg-light mb-3" style="width: 140px; height: 200px; overflow: hidden;">
                <?php if ($book['cover_url']): ?>
                  <img id="imagePreview" src="<?= ASSETS_URL . '../' . $book['cover_url'] ?>" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                  <i class="bi bi-image text-muted display-4" id="placeholderIcon"></i>
                  <img id="imagePreview" src="#" alt="Preview" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
              </div>
              <input type="file" name="cover" id="coverInput" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
              <div class="form-text small mt-2">Biarkan kosong jika tidak ingin mengubah sampul.</div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-box-seam me-2"></i>Stok & Lokasi</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Kategori Koleksi</label>
              <select class="form-select text-center" name="category">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= e($cat) ?>" <?= ($book['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Total Stok</label>
                <input type="number" class="form-control fw-bold text-center" name="total_copies" value="<?= (int)$book['total_copies'] ?>" min="1">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Lokasi Rak</label>
                <input type="text" class="form-control text-center" name="rack_location" value="<?= e($book['rack_location'] ?? '') ?>" placeholder="LT.1 A1">
              </div>
            </div>

            <div class="mb-0 pt-2 text-center">
              <div class="p-3 bg-light rounded border">
                <div class="small fw-bold text-muted text-uppercase mb-1">Tersedia Saat Ini</div>
                <div class="h4 fw-bold mb-0 text-primary"><?= $book['available_copies'] ?> <span class="small text-muted fw-normal">Buku</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── ACTION BUTTONS ─── -->
      <div class="col-12 mt-4 text-center">
        <button type="submit" class="btn btn-warning px-5 py-2 fw-bold shadow-sm">
          <i class="bi bi-save2 me-2"></i>Perbarui Data Buku
        </button>
        <a href="<?= BASE_URL ?>librarian/books.php" class="btn btn-light border px-4 py-2 ms-2">Batal</a>
      </div>
    </div>
  </form>
</div>
</div>

<script>
document.getElementById('coverInput').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        let preview = document.getElementById('imagePreview');
        let placeholder = document.getElementById('placeholderIcon');
        
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
        if (placeholder) placeholder.classList.add('d-none');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
