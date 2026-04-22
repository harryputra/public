<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$errors = [];
$data   = ['title'=>'','author'=>'','isbn'=>'','publisher'=>'','year'=>'',
           'category'=>'','description'=>'','total_copies'=>1,'rack_location'=>''];

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

    // Cek ISBN unik
    if ($data['isbn']) {
        $chk = getDB()->prepare("SELECT id FROM books WHERE isbn=? AND is_deleted=0");
        $chk->execute([$data['isbn']]);
        if ($chk->fetch()) $errors[] = 'ISBN sudah ada dalam sistem.';
    }

    if (empty($errors)) {
        $db  = getDB();
        
        // Handle upload cover
        $coverUrl = null;
        if (!empty($_FILES['cover']['name'])) {
            $uploadedPath = uploadImage($_FILES['cover']);
            if ($uploadedPath) {
                $coverUrl = $uploadedPath;
            } else {
                $errors[] = 'Gagal mengunggah sampul. Pastikan format JPG/PNG/WebP dan ukuran maks 2MB.';
            }
        }

        if (empty($errors)) {
            $sql = "INSERT INTO books (title,author,isbn,publisher,year,category,description,total_copies,available_copies,rack_location,cover_url)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $db->prepare($sql)->execute([
                $data['title'], $data['author'], $data['isbn'], $data['publisher'],
                $data['year'], $data['category'], $data['description'],
                $data['total_copies'], $data['total_copies'], $data['rack_location'],
                $coverUrl
            ]);
            setFlash('success', 'Buku "' . $data['title'] . '" berhasil ditambahkan.');
            redirect(BASE_URL . 'librarian/books.php');
        }
    }

}

$categories = ['Teknik Informatika','Pemrograman','Algoritma','Basis Data','Jaringan Komputer',
               'Sistem Operasi','Kecerdasan Buatan','Pengembangan Web','Pengujian Software',
               'Metodologi','Arsitektur Software','Matematika','Fisika','Kimia','Umum'];

$pageTitle = 'Tambah Buku';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
<div class="col-xl-10">
  <div class="d-flex align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">Manajemen Koleksi</h4>
      <p class="text-muted small mb-0">Input data buku baru ke dalam katalog perpustakaan</p>
    </div>
    <a href="<?= BASE_URL ?>librarian/books.php" class="btn btn-outline-secondary ms-auto">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kendala Input:</div>
      <ul class="mb-0 small">
        <?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate id="bookForm" enctype="multipart/form-data">
    <?php csrfField(); ?>
    <div class="row g-4">
      <!-- ─── SISI KIRI: METADATA BUKU ─── -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-info-circle-fill me-2"></i>Informasi Utama</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Judul Buku <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-book"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" name="title" value="<?= e($data['title']) ?>" placeholder="Masukkan judul lengkap buku…" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Penulis <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-lines-fill"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0" name="author" value="<?= e($data['author']) ?>" placeholder="Nama penulis…" required>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">ISBN / ISSN</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-hash"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0 font-monospace" name="isbn" value="<?= e($data['isbn'] ?? '') ?>" placeholder="978-xxx-...">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-7 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Penerbit</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-building"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0" name="publisher" value="<?= e($data['publisher'] ?? '') ?>" placeholder="Nama penerbit…">
                </div>
              </div>
              <div class="col-md-5 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Tahun Terbit</label>
                <input type="number" class="form-control text-center" name="year" value="<?= e($data['year'] ?? '') ?>" min="1900" max="<?= date('Y') ?>" placeholder="<?= date('Y') ?>">
              </div>
            </div>

            <div class="mb-0">
              <label class="form-label small fw-bold text-uppercase text-muted">Sinopsis / Keterangan</label>
              <textarea class="form-control" name="description" rows="4" placeholder="Ringkasan isi buku…"><?= e($data['description'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── SISI KANAN: SAMPUL & INVENTARIS ─── -->
      <div class="col-lg-5">
        <!-- ZONA UNGGAH SAMPUL -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-info"><i class="bi bi-image me-2"></i>Sampul Buku</h6>
          </div>
          <div class="card-body p-4 text-center">
            <div class="mb-3">
              <div id="previewContainer" class="mx-auto border rounded d-flex align-items-center justify-content-center bg-light mb-3" style="width: 140px; height: 200px; overflow: hidden;">
                <i class="bi bi-image text-muted display-4" id="placeholderIcon"></i>
                <img id="imagePreview" src="#" alt="Preview" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
              <input type="file" name="cover" id="coverInput" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
              <div class="form-text small mt-2">Format: JPG, PNG, WebP. Maks: 2MB.</div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-box-seam-fill me-2"></i>Inventaris & Rak</h6>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Kategori Koleksi</label>
              <select class="form-select text-center" name="category">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= e($cat) ?>" <?= ($data['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row">
              <div class="col-md-5 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Total Stok</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-stack"></i></span>
                  <input type="number" class="form-control text-center" name="total_copies" value="<?= (int)($data['total_copies'] ?? 1) ?>" min="1" max="99">
                </div>
              </div>
              <div class="col-md-7 mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Lokasi Rak</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0 text-center" name="rack_location" value="<?= e($data['rack_location'] ?? '') ?>" placeholder="LT.1 A1">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── ACTION BUTTONS ─── -->
      <div class="col-12 mt-4 text-center">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" id="btnSave">
          <i class="bi bi-check-circle-fill me-2"></i>Daftarkan Buku Baru
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
        document.getElementById('imagePreview').src = URL.createObjectURL(file);
        document.getElementById('imagePreview').classList.remove('d-none');
        document.getElementById('placeholderIcon').classList.add('d-none');
    } else {
        document.getElementById('imagePreview').classList.add('d-none');
        document.getElementById('placeholderIcon').classList.remove('d-none');
    }
}
</script>

</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
