<?php
defined('SMARTLIB') or die('Direct access not permitted');

// ─── Redirect ─────────────────────────────────────────────────────────────────
function redirect(string $url): never {
    header("Location: $url");
    exit;
}

// ─── Flash Message ────────────────────────────────────────────────────────────
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function showFlash(): void {
    $f = getFlash();
    if (!$f) return;
    
    $type = $f['type']; // success, danger, warning, info
    if ($type === 'danger') $type = 'error'; // SwAL uses 'error' not 'danger'

    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Toast.fire({
                icon: '" . e($type) . "',
                title: '" . e($f['message']) . "'
            });
        });
    </script>";
}


// ─── CSRF ─────────────────────────────────────────────────────────────────────
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): void {
    echo '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        setFlash('danger', 'Token keamanan tidak valid. Coba lagi.');
        redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL);
    }
}

// ─── Format Uang ──────────────────────────────────────────────────────────────
function formatRupiah(int $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// ─── Format Tanggal ──────────────────────────────────────────────────────────
function formatDate(?string $date, string $format = 'd M Y'): string {
    if (!$date) return '-';
    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
    $d = date_create($date);
    if (!$d) return $date;
    $result = date_format($d, $format);
    // Ganti nama bulan English → Indonesia
    $en = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return str_replace($en, $months, $result);
}

// ─── Hitung Denda ─────────────────────────────────────────────────────────────
function calculateFine(string $dueDate, ?string $returnDate = null): int {
    $due    = new DateTime($dueDate);
    $check  = $returnDate ? new DateTime($returnDate) : new DateTime('today');
    $diff   = $check->diff($due);
    if ($diff->invert === 0) return 0; // Belum lewat jatuh tempo
    return $diff->days * FINE_PER_DAY;
}

// ─── Status Badge ─────────────────────────────────────────────────────────────
function loanStatusBadge(string $status): string {
    return match ($status) {
        'active'   => '<span class="badge bg-primary">Aktif</span>',
        'returned' => '<span class="badge bg-success">Dikembalikan</span>',
        'overdue'  => '<span class="badge bg-danger">Terlambat</span>',
        default    => '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>',
    };
}

function reservationStatusBadge(string $status): string {
    return match ($status) {
        'waiting'   => '<span class="badge bg-warning text-dark">Menunggu</span>',
        'ready'     => '<span class="badge bg-info text-dark">Siap Diambil</span>',
        'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
        'taken'     => '<span class="badge bg-success">Sudah Diambil</span>',
        default     => '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>',
    };
}

// ─── Escape HTML ─────────────────────────────────────────────────────────────
function e(mixed $val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ─── Pagination ──────────────────────────────────────────────────────────────
function paginate(int $total, int $perPage, int $current, string $baseUrl): string {
    if ($total <= $perPage) return '';
    $pages = (int)ceil($total / $perPage);
    $html  = '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i === $current ? 'active' : '';
        $url    = $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . $i;
        $html  .= "<li class=\"page-item $active\"><a class=\"page-link\" href=\"$url\">$i</a></li>";
    }
    $html .= '</ul></nav>';
    return $html;
}

// ─── Ambil jumlah peminjaman aktif user ──────────────────────────────────────
function countActiveLoans(int $userId): int {
    $stmt = getDB()->prepare("SELECT COUNT(*) FROM loans WHERE user_id=? AND status IN ('active','overdue')");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

// ─── Update status overdue secara otomatis ────────────────────────────────────
function updateOverdueLoans(): void {
    getDB()->exec("UPDATE loans SET status='overdue' WHERE status='active' AND due_date < CURDATE()");
}

// ─── Upload Gambar Aman ───────────────────────────────────────────────────────
function uploadImage(array $file, string $targetDir = 'assets/uploads/covers/'): ?string {
    // Cek error upload
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    // Validasi tipe file
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedMimes)) return null;

    // Validasi ukuran (maks 2MB)
    if ($file['size'] > 2 * 1024 * 1024) return null;

    // Generate nama file unik
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ['jpg','jpeg','png','webp'])) return null;

    $filename = bin2hex(random_bytes(10)) . '.' . $ext;
    $relPath  = $targetDir . $filename;
    $fullPath = __DIR__ . '/../' . $relPath;

    // Pastikan direktori tujuan ada
    if (!is_dir(dirname($fullPath))) {
        mkdir(dirname($fullPath), 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return $relPath;
    }

    return null;
}

