<?php
require_once __DIR__ . '/../config/config.php';
requireRole('admin');
updateOverdueLoans();

$db = getDB();

// ─── Query Statistik ───────────────────────────────────────────────────────
$stats = [];
$stats['total_users']     = (int)$db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$stats['active_loans']    = (int)$db->query("SELECT COUNT(*) FROM loans WHERE status IN ('active','overdue')")->fetchColumn();
$stats['overdue']         = (int)$db->query("SELECT COUNT(*) FROM loans WHERE status='overdue'")->fetchColumn();
$stats['total_books']     = (int)$db->query("SELECT COUNT(*) FROM books WHERE is_deleted=0")->fetchColumn();
$stats['total_copies']    = (int)$db->query("SELECT SUM(total_copies) FROM books WHERE is_deleted=0")->fetchColumn();
$stats['available']       = (int)$db->query("SELECT SUM(available_copies) FROM books WHERE is_deleted=0")->fetchColumn();
$stats['unpaid_fines']    = (int)$db->query("SELECT COALESCE(SUM(fine_amount),0) FROM loans WHERE fine_paid=0 AND fine_amount>0")->fetchColumn();
$stats['waiting_res']     = (int)$db->query("SELECT COUNT(*) FROM reservations WHERE status='waiting'")->fetchColumn();

// Mahasiswa Terbaru
$recentUsers = $db->query("
    SELECT id, full_name, nim, email, is_active, created_at
    FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 5
")->fetchAll();

$pageTitle = 'Admin Executive Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ═══ HEADER SECTION ══════════════════════════════════════════════════════ -->
<div class="row align-items-center mb-4">
  <div class="col-md-auto">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
        <i class="bi bi-shield-lock-fill fs-3"></i>
      </div>
      <div>
        <h4 class="fw-bold mb-0">Control Center</h4>
        <div class="d-flex align-items-center gap-2 small text-muted">
          <span class="status-dot bg-success animate-pulse"></span>
          <span>Sistem Berjalan Normal</span>
          <span class="mx-2">&bull;</span>
          <span><?= formatDate(date('Y-m-d'), 'l, d F Y') ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md ms-auto text-md-end mt-3 mt-md-0">
    <div class="btn-group shadow-sm">
      <button class="btn btn-white border-end"><i class="bi bi-download me-2"></i>Export Report</button>
      <button class="btn btn-white"><i class="bi bi-printer"></i></button>
    </div>
  </div>
</div>

<!-- ═══ PRIMARY STATS (V2) ══════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
  <div class="col-md-6 col-xl-3">
    <div class="stat-card-v2 bg-grad-blue">
      <div class="stat-icon"><i class="bi bi-people"></i></div>
      <div class="stat-label">Total Mahasiswa</div>
      <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
      <div class="mt-2 small text-white-50"><i class="bi bi-arrow-up-short"></i> +2 minggu ini</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card-v2 bg-grad-purple">
      <div class="stat-icon"><i class="bi bi-book"></i></div>
      <div class="stat-label">Koleksi Buku</div>
      <div class="stat-value"><?= number_format($stats['total_books']) ?></div>
      <div class="mt-2 small text-white-50"><?= number_format($stats['total_copies']) ?> total eksemplar</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card-v2 bg-grad-orange">
      <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
      <div class="stat-label">Peminjaman Aktif</div>
      <div class="stat-value"><?= number_format($stats['active_loans']) ?></div>
      <div class="mt-2 small text-white-50"><?= $stats['waiting_res'] ?> reservasi menunggu</div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card-v2 bg-grad-red">
      <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
      <div class="stat-label">Keterlambatan</div>
      <div class="stat-value"><?= number_format($stats['overdue']) ?></div>
      <div class="mt-2 small text-white-50">Total denda: <?= formatRupiah($stats['unpaid_fines']) ?></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- ═══ LEFT COLUMN: QUICK ACTIONS ════════════════════════════════════════ -->
  <div class="col-lg-4">
    <div class="card mb-4 shadow-sm border-0">
      <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
        <h6 class="fw-bold mb-0">Quick Operations</h6>
      </div>
      <div class="card-body p-4">
        <div class="row g-3">
          <div class="col-6">
            <a href="<?= BASE_URL ?>admin/user_add.php" class="text-decoration-none">
              <div class="quick-action-card p-3 text-center">
                <i class="bi bi-person-plus fs-3 text-primary d-block mb-1"></i>
                <span class="small fw-semibold text-dark">Tambah User</span>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="<?= BASE_URL ?>librarian/books.php" class="text-decoration-none">
              <div class="quick-action-card p-3 text-center">
                <i class="bi bi-plus-square fs-3 text-success d-block mb-1"></i>
                <span class="small fw-semibold text-dark">Input Buku</span>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="<?= BASE_URL ?>librarian/loans.php" class="text-decoration-none">
              <div class="quick-action-card p-3 text-center">
                <i class="bi bi-arrow-left-right fs-3 text-info d-block mb-1"></i>
                <span class="small fw-semibold text-dark">Sirkulasi</span>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="#" class="text-decoration-none">
              <div class="quick-action-card p-3 text-center">
                <i class="bi bi-file-earmark-bar-graph fs-3 text-warning d-block mb-1"></i>
                <span class="small fw-semibold text-dark">Laporan</span>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- System Info Card -->
    <div class="card bg-grad-slate text-white border-0 shadow-sm">
      <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-hdd-network me-2"></i>System Parameter</h6>
        <div class="d-flex justify-content-between mb-2 small text-white-50">
          <span>Loan Duration:</span><span><?= LOAN_DURATION_DAYS ?> days</span>
        </div>
        <div class="d-flex justify-content-between mb-2 small text-white-50">
          <span>Max Extension:</span><span><?= MAX_EXTENSIONS ?>x</span>
        </div>
        <div class="d-flex justify-content-between mb-3 small text-white-50">
          <span>Fine Rate:</span><span><?= formatRupiah(FINE_PER_DAY) ?>/day</span>
        </div>
        <hr class="border-white opacity-20">
        <div class="small fw-semibold text-center mb-0">SmartLib v2.0-STABLE</div>
      </div>
    </div>
  </div>

  <!-- ═══ RIGHT COLUMN: RECENT DATA ═════════════════════════════════════════ -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent border-0 d-flex align-items-center pt-4 px-4">
        <h6 class="fw-bold mb-0">Mahasiswa Baru Terdaftar</h6>
        <div class="ms-auto">
          <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-sm btn-light">Lihat Semua</a>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="text-muted small border-bottom">
              <tr>
                <th class="border-0 px-0">Mahasiswa (NIM)</th>
                <th class="border-0">Email</th>
                <th class="border-0 text-center">Status</th>
                <th class="border-0 text-end">Terdaftar</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $u): ?>
              <tr>
                <td class="px-0">
                  <div class="fw-bold"><?= e($u['full_name']) ?></div>
                  <div class="small text-muted"><?= e($u['nim']) ?></div>
                </td>
                <td><small><?= e($u['email']) ?></small></td>
                <td class="text-center">
                  <?= $u['is_active'] ? '<span class="badge bg-success bg-opacity-10 text-success px-3">Aktif</span>' : '<span class="badge bg-danger bg-opacity-10 text-danger px-3">Nonaktif</span>' ?>
                </td>
                <td class="text-end small text-muted"><?= formatDate($u['created_at']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($recentUsers)): ?>
                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data mahasiswa.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
