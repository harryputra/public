-- =====================================================
-- SmartLib - Sistem Manajemen Perpustakaan Kampus
-- Database Schema v1.0
--
-- Jalankan file ini di phpMyAdmin atau MySQL CLI:
--   mysql -u root -p < smartlib.sql
-- Atau gunakan install.php untuk setup otomatis
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS smartlib
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smartlib;

-- =====================================================
-- Tabel: users
-- =====================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nim         VARCHAR(20) UNIQUE COMMENT 'NIM mahasiswa / NIP dosen/pustakawan',
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(100) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('student','librarian','admin') NOT NULL DEFAULT 'student',
    phone       VARCHAR(20),
    is_active   TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel: books
-- =====================================================
DROP TABLE IF EXISTS books;
CREATE TABLE books (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    author           VARCHAR(255) NOT NULL,
    isbn             VARCHAR(20) UNIQUE,
    publisher        VARCHAR(100),
    year             YEAR,
    category         VARCHAR(50),
    description      TEXT,
    total_copies     INT UNSIGNED NOT NULL DEFAULT 1,
    available_copies INT UNSIGNED NOT NULL DEFAULT 1,
    rack_location    VARCHAR(30) COMMENT 'Contoh: Lantai 2, Rak A3',
    cover_url        VARCHAR(255),
    is_deleted       TINYINT(1) NOT NULL DEFAULT 0,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel: loans
-- =====================================================
DROP TABLE IF EXISTS loans;
CREATE TABLE loans (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    book_id         INT UNSIGNED NOT NULL,
    loan_date       DATE NOT NULL,
    due_date        DATE NOT NULL,
    return_date     DATE DEFAULT NULL,
    fine_amount     INT UNSIGNED DEFAULT 0 COMMENT 'Denda dalam Rupiah',
    fine_paid       TINYINT(1) NOT NULL DEFAULT 0,
    extension_count TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Maks 2x perpanjangan',
    status          ENUM('active','returned','overdue') NOT NULL DEFAULT 'active',
    processed_by    INT UNSIGNED DEFAULT NULL COMMENT 'Pustakawan yang memproses',
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_loans_user     FOREIGN KEY (user_id)       REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_loans_book     FOREIGN KEY (book_id)       REFERENCES books(id) ON DELETE RESTRICT,
    CONSTRAINT fk_loans_staff    FOREIGN KEY (processed_by)  REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel: reservations
-- =====================================================
DROP TABLE IF EXISTS reservations;
CREATE TABLE reservations (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    book_id     INT UNSIGNED NOT NULL,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_at   TIMESTAMP NULL DEFAULT NULL COMMENT 'Batas ambil 2x24 jam saat ready',
    status      ENUM('waiting','ready','cancelled','taken') NOT NULL DEFAULT 'waiting',
    CONSTRAINT fk_res_user  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_res_book  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INDEX untuk performa query
-- =====================================================
CREATE INDEX idx_loans_user_status    ON loans(user_id, status);
CREATE INDEX idx_loans_book_status    ON loans(book_id, status);
CREATE INDEX idx_loans_due_date       ON loans(due_date, status);
CREATE INDEX idx_books_title          ON books(title);
CREATE INDEX idx_books_author         ON books(author);
CREATE INDEX idx_books_isbn           ON books(isbn);
CREATE INDEX idx_reservations_user    ON reservations(user_id, status);
CREATE INDEX idx_reservations_book    ON reservations(book_id, status);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- Catatan: Data seed (pengguna & buku demo)
-- diinsert melalui install.php agar password ter-hash
-- dengan bcrypt secara aman.
-- =====================================================
