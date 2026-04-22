<?php
require_once __DIR__ . '/../config/config.php';
requireRole('admin');

verifyCsrf();

$db     = getDB();
$userId = (int)($_GET['id'] ?? 0);

if ($userId === currentUser()['id']) {
    setFlash('danger', 'Anda tidak dapat menghapus akun Anda sendiri.');
    redirect(BASE_URL . 'admin/users.php');
}

$user = $db->prepare("SELECT id, full_name FROM users WHERE id=?");
$user->execute([$userId]);
$user = $user->fetch();

if (!$user) {
    setFlash('danger', 'Pengguna tidak ditemukan.');
    redirect(BASE_URL . 'admin/users.php');
}

// Cek peminjaman aktif
$activeLoans = $db->prepare("SELECT COUNT(*) FROM loans WHERE user_id=? AND status IN ('active','overdue')");
$activeLoans->execute([$userId]);
if ((int)$activeLoans->fetchColumn() > 0) {
    setFlash('danger', 'Pengguna "' . $user['full_name'] . '" tidak dapat dihapus karena masih memiliki peminjaman aktif.');
    redirect(BASE_URL . 'admin/users.php');
}

$db->prepare("DELETE FROM users WHERE id=?")->execute([$userId]);
setFlash('success', 'Pengguna "' . $user['full_name'] . '" berhasil dihapus.');
redirect(BASE_URL . 'admin/users.php');
