<?php
defined('SMARTLIB') or die('Direct access not permitted');

// ─── Cek Login ───────────────────────────────────────────────────────────────
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        setFlash('warning', 'Silakan login terlebih dahulu.');
        redirect(BASE_URL . 'login.php');
    }
}

// ─── Cek Role ────────────────────────────────────────────────────────────────
function requireRole(string|array $roles): void {
    requireLogin();
    $allowed = is_array($roles) ? $roles : [$roles];
    if (!in_array($_SESSION['user_role'] ?? '', $allowed, true)) {
        setFlash('danger', 'Anda tidak memiliki akses ke halaman tersebut.');
        redirect(BASE_URL . 'unauthorized.php');
    }
}

// ─── Helpers session ─────────────────────────────────────────────────────────
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentUser(): array {
    return [
        'id'    => $_SESSION['user_id']   ?? null,
        'name'  => $_SESSION['user_name'] ?? '',
        'role'  => $_SESSION['user_role'] ?? '',
        'email' => $_SESSION['user_email']?? '',
        'nim'   => $_SESSION['user_nim']  ?? '',
    ];
}

function isRole(string $role): bool {
    return ($_SESSION['user_role'] ?? '') === $role;
}

function dashboardUrl(): string {
    return match ($_SESSION['user_role'] ?? '') {
        'admin'     => BASE_URL . 'admin/dashboard.php',
        'librarian' => BASE_URL . 'librarian/dashboard.php',
        default     => BASE_URL . 'student/dashboard.php',
    };
}
