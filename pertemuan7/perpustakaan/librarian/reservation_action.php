<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

verifyCsrf();

$db     = getDB();
$resId  = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

$res = $db->prepare("SELECT * FROM reservations WHERE id=? AND status IN ('waiting','notified')");
$res->execute([$resId]);
$res = $res->fetch();

if (!$res) {
    setFlash('danger', 'Reservasi tidak ditemukan atau sudah diproses.');
    redirect(BASE_URL . 'librarian/reservations.php');
}

if ($action === 'cancel') {
    $db->prepare("UPDATE reservations SET status='cancelled', updated_at=NOW() WHERE id=?")
       ->execute([$resId]);
    setFlash('success', 'Reservasi berhasil dibatalkan.');
}

redirect(BASE_URL . 'librarian/reservations.php');
