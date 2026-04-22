<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['librarian','admin']);

$db     = getDB();
$bookId = (int)($_GET['id'] ?? 0);

$book = $db->prepare("SELECT id, title FROM books WHERE id=? AND is_deleted=0");
$book->execute([$bookId]);
$book = $book->fetch();

if (!$book) {
    setFlash('danger', 'Buku tidak ditemukan.');
    redirect(BASE_URL . 'librarian/books.php');
}

// Cek apakah ada peminjaman aktif
$activeLoans = $db->prepare("SELECT COUNT(*) FROM loans WHERE book_id=? AND status IN ('active','overdue')");
$activeLoans->execute([$bookId]);
if ((int)$activeLoans->fetchColumn() > 0) {
    setFlash('danger', 'Buku "' . $book['title'] . '" tidak dapat dihapus karena masih ada peminjaman aktif.');
    redirect(BASE_URL . 'librarian/books.php');
}

verifyCsrf();

$db->prepare("UPDATE books SET is_deleted=1, updated_at=NOW() WHERE id=?")
   ->execute([$bookId]);

// Batalkan reservasi yang masih waiting
$db->prepare("UPDATE reservations SET status='cancelled', updated_at=NOW() WHERE book_id=? AND status='waiting'")
   ->execute([$bookId]);

setFlash('success', 'Buku "' . $book['title'] . '" berhasil dihapus.');
redirect(BASE_URL . 'librarian/books.php');
