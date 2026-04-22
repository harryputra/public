# 📙 SmartLib QA Lab Manual: Automation Fundamentals

**Standard**: Antigravity Architect Academic Suite  
**Target**: Mahasiswa SQA (Software Quality Assurance)

---

## 🎯 1. Tujuan Praktikum

Setelah menyelesaikan modul ini, Mahasiswa diharapkan mampu:

1. Melakukan pengujian otomasi menggunakan **Selenium IDE**.
2. Membedakan antara **Positive Testing** (jalur sukses) dan **Negative Testing** (jalur gagal).
3. Mendokumentasikan hasil pengujian secara profesional dan akurat.
4. Menganalisis mekanisme validasi pada aplikasi web.

---

## ⚠️ 2. Protokol Khusus Firefox (WAJIB DIBACA!)

Selenium IDE pada **Firefox** memiliki limitasi teknis pada perintah `type` untuk form input. **SELALU** gunakan `execute script` sebagai pengganti untuk semua input teks.

| ❌ JANGAN (Gagal di Firefox) | ✅ GUNAKAN INI (Berhasil di Firefox) |
| :--- | :--- |
| `type` \| `id=email` \| `andi@...` | `execute script` \| `document.getElementById('email').value='andi@...'` \| |

> **Catatan**: Setelah `execute script` untuk mengisi nilai, **tambahkan** command `click` atau `fire event` pada input tersebut agar event `change` terpicu (penting untuk validasi JavaScript).

---

## 📝 3. Instruksi Pengerjaan (Wajib Dibaca!)

Mahasiswa diwajibkan mengikuti protokol berikut selama sesi praktikum:

1. **Preparation**: Jalankan aplikasi SmartLib di `http://localhost:8000/`.
2. **Execution**: Jalankan setiap Test Case (TC) secara berurutan menggunakan Selenium IDE.
3. **Logging**: Catat hasil setiap TC ke dalam **Tabel Laporan Hasil Pengujian** (lihat poin 4).
4. **Evidence**: Ambil screenshot jika terjadi error atau kegagalan asersi. Beri nama: `[TC-ID]_[PASS/FAIL].png`.
5. **Documentation**: Simpan project Selenium dalam format `.side`.

---

## 📑 4. Template Laporan Hasil Pengujian

Salin tabel ini ke dokumen laporan Anda untuk setiap sesi pengujian.

| ID Test Case | Nama Pengujian | Tipe | Status (PASS/FAIL) | Catatan / Error Log | Evidence |
| :--- | :--- | :--- | :--- | :--- | :--- |
| TC-AUTH-001 | Login Berhasil - Mahasiswa | Positive | | | |
| TC-AUTH-002 | Login Berhasil - Pustakawan | Positive | | | |
| TC-AUTH-003 | Login Berhasil - Admin | Positive | | | |
| TC-AUTH-004 | Login - Password Salah | Negative | | | |
| TC-AUTH-005 | Login - Email Tidak Terdaftar | Negative | | | |
| TC-AUTH-006 | Login - Form Kosong | Negative | | | |
| TC-AUTH-007 | Login - Akun Non-Aktif | Negative | | | |
| TC-AUTH-008 | Logout Berhasil | Positive | | | |
| TC-ADM-001 | Tambah Pengguna Baru | Positive | | | |
| TC-ADM-002 | Tambah Pengguna - Email Duplikat | Negative | | | |
| TC-ADM-003 | Tambah Pengguna - Password Tidak Cocok | Negative | | | |
| TC-ADM-004 | Lihat Daftar Pengguna | Positive | | | |
| TC-ADM-005 | Edit Data Pengguna | Positive | | | |
| TC-ADM-006 | Edit - Admin Nonaktifkan Diri Sendiri | Negative | | | |
| TC-ADM-007 | Hapus Pengguna | Positive | | | |
| TC-BOOK-001 | Tambah Buku Baru | Positive | | | |
| TC-BOOK-002 | Tambah Buku - ISBN Duplikat | Negative | | | |
| TC-BOOK-003 | Tambah Buku - Field Wajib Kosong | Negative | | | |
| TC-BOOK-004 | Lihat Daftar & Cari Buku | Positive | | | |
| TC-BOOK-005 | Edit Metadata Buku | Positive | | | |
| TC-BOOK-006 | Hapus Buku | Positive | | | |
| TC-LOAN-001 | Proses Peminjaman Buku | Positive | | | |
| TC-LOAN-002 | Peminjaman - Mahasiswa Melebihi Kuota | Negative | | | |
| TC-LOAN-003 | Lihat Daftar Sirkulasi Aktif | Positive | | | |
| TC-RET-001 | Pengembalian Tepat Waktu | Positive | | | |
| TC-RET-002 | Pengembalian dengan Denda (Lunas) | Positive | | | |
| TC-STU-001 | Navigasi Katalog & Pencarian Buku | Positive | | | |
| TC-STU-002 | Lihat Detail Buku | Positive | | | |
| TC-STU-003 | Reservasi Buku Habis | Positive | | | |
| TC-STU-004 | Perpanjangan Pinjaman Mandiri | Positive | | | |
| TC-STU-005 | Lihat Riwayat Pinjaman Saya | Positive | | | |
| TC-RES-001 | Lihat Daftar Reservasi (Librarian) | Positive | | | |
| TC-RES-002 | Batalkan Reservasi (Librarian) | Positive | | | |
| TC-SEC-001 | Proteksi Akses Halaman Admin (oleh Mahasiswa) | Negative | | | |
| TC-SEC-002 | Proteksi Akses Halaman Librarian (oleh Mahasiswa) | Negative | | | |

---

## 🔐 5. Modul A: Autentikasi

### [POSITIVE] TC-AUTH-001 — Login Berhasil (Mahasiswa)

| Parameter | Detail |
| :--- | :--- |
| **Role** | Mahasiswa |
| **Data** | Email: `andi@student.polman.id` \| Password: `smartlib123` |
| **Prasyarat** | Server aktif, database terkoneksi |
| **Ekspektasi** | Redirect ke `/student/dashboard.php`, muncul greeting "Selamat datang" |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `assertTitle` | | `Login — SmartLib` |
| 3 | `execute script` | `document.getElementById('email').value='andi@student.polman.id'` | |
| 4 | `execute script` | `document.getElementById('password').value='smartlib123'` | |
| 5 | `click` | `id=btnLogin` | |
| 6 | `waitForPageLoad` | | |
| 7 | `assertUrlContains` | | `student/dashboard.php` |

#### Yang Harus Dicatat Mahasiswa

- Status URL setelah redirect (copy-paste dari address bar).
- Screenshot halaman dashboard jika berhasil.

---

### [POSITIVE] TC-AUTH-002 — Login Berhasil (Pustakawan)

| Parameter | Detail |
| :--- | :--- |
| **Data** | Email: `pustakawan@smartlib.id` \| Password: `smartlib123` |
| **Ekspektasi** | Redirect ke `/librarian/dashboard.php`, menu sirkulasi tersedia |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `execute script` | `document.getElementById('email').value='pustakawan@smartlib.id'` | |
| 3 | `execute script` | `document.getElementById('password').value='smartlib123'` | |
| 4 | `click` | `id=btnLogin` | |
| 5 | `assertUrlContains` | | `librarian/dashboard.php` |
| 6 | `assertElementPresent` | `css=a[href*="loan_add.php"]` | |

---

### [POSITIVE] TC-AUTH-003 — Login Berhasil (Admin)

| Parameter | Detail |
| :--- | :--- |
| **Data** | Email: `admin@smartlib.id` \| Password: `smartlib123` |
| **Ekspektasi** | Redirect ke `/admin/dashboard.php` |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `execute script` | `document.getElementById('email').value='admin@smartlib.id'` | |
| 3 | `execute script` | `document.getElementById('password').value='smartlib123'` | |
| 4 | `click` | `id=btnLogin` | |
| 5 | `assertUrlContains` | | `admin/dashboard.php` |

---

### [NEGATIVE] TC-AUTH-004 — Login Gagal (Password Salah)

| Parameter | Detail |
| :--- | :--- |
| **Data** | Email: `andi@student.polman.id` \| Password: `salah123` |
| **Ekspektasi** | Halaman tetap di `/login.php`, muncul `.alert-danger` berisi "Email atau password salah" |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `execute script` | `document.getElementById('email').value='andi@student.polman.id'` | |
| 3 | `execute script` | `document.getElementById('password').value='salah123'` | |
| 4 | `click` | `id=btnLogin` | |
| 5 | `assertElementPresent` | `css=#login-error` | |
| 6 | `assertTextContains` | `css=#login-error` | `Email atau password salah` |

#### Yang Harus Dicatat Mahasiswa

- Apakah pesan error yang ditampilkan cukup informatif tanpa mengekspos terlalu banyak detail keamanan?

---

### [NEGATIVE] TC-AUTH-005 — Login Gagal (Email Tidak Terdaftar)

| Parameter | Detail |
| :--- | :--- |
| **Data** | Email: `hacker@evil.com` \| Password: `apapun` |
| **Ekspektasi** | Muncul pesan error. Sistem **TIDAK** boleh memberi tahu apakah email terdaftar atau tidak. |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `execute script` | `document.getElementById('email').value='hacker@evil.com'` | |
| 3 | `execute script` | `document.getElementById('password').value='apapun'` | |
| 4 | `click` | `id=btnLogin` | |
| 5 | `assertElementPresent` | `css=#login-error` | |

---

### [NEGATIVE] TC-AUTH-006 — Login Gagal (Form Kosong)

| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Form tidak terkirim. Browser HTML5 menampilkan tooltip validasi pada field kosong. |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `click` | `id=btnLogin` | |
| 3 | `assertValue` | `id=email` | *(kosong — verifikasi URL tidak berubah)* |
| 4 | `assertUrlContains` | | `login.php` |

---

### [NEGATIVE] TC-AUTH-007 — Login Gagal (Akun Non-Aktif)
>
> [!WARNING]
> Akun yang di-suspend oleh Admin harus diblokir masuk ke sistem, meskipun password benar.

| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Buat akun demo dengan `is_active = 0` melalui panel Admin terlebih dahulu, atau gunakan akun yang sudah ada yang statusnya non-aktif. |
| **Ekspektasi** | Muncul pesan: "Akun Anda tidak aktif. Hubungi administrator." |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/login.php` | |
| 2 | `execute script` | `document.getElementById('email').value='nonaktif@test.com'` | |
| 3 | `execute script` | `document.getElementById('password').value='smartlib123'` | |
| 4 | `click` | `id=btnLogin` | |
| 5 | `assertElementPresent` | `css=#login-error` | |
| 6 | `assertTextContains` | `css=#login-error` | `tidak aktif` |

---

### [POSITIVE] TC-AUTH-008 — Logout Berhasil

| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Sedang login dengan akun apapun |
| **Ekspektasi** | Sesi dihancurkan, redirect ke `/login.php`, form login tampil kembali |

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `click` | `css=.dropdown-toggle[data-bs-toggle="dropdown"]` | |
| 2 | `waitForElementVisible` | `css=form[action*="logout"]` | |
| 3 | `click` | `css=form[action*="logout"] button` | |
| 4 | `assertUrlContains` | | `login.php` |
| 5 | `assertElementPresent` | `id=btnLogin` | |

---

#### Script Selenium

| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/loan_add.php` | |
| 2 | `assertUrlContains` | | `unauthorized.php` |

---

## ✅ 12. Checklist Akhir Penyerahan

Sebelum mengumpulkan, pastikan Anda telah:

- [ ] Menjalankan **seluruh 35 Test Case** di atas menggunakan Selenium IDE.
- [ ] Mengisi **kolom Status** pada Template Laporan untuk setiap TC.
- [ ] Melampirkan **screenshot** (format: `TC-ID_PASS.png` atau `TC-ID_FAIL.png`) untuk setiap TC.
- [ ] Menulis **Catatan/Error Log** untuk TC yang berstatus **FAIL**.
- [ ] Mengekspor project Selenium dalam format **`.side`**.
- [ ] Menyimpan laporan dalam format Word atau PDF.

---

## 📊 Ringkasan Inventori Fungsi

| Modul | Jumlah Fungsi | TC Positive | TC Negative |
| :--- | :--- | :--- | :--- |
| **A. Autentikasi** | 3 (Login + Logout + Security) | 4 | 4 |
| **B. Manajemen User (Admin)** | 4 (CRUD) | 4 | 3 |
| **C. Manajemen Buku (Librarian)** | 4 (CRUD) | 3 | 3 |
| **D. Sirkulasi & Pengembalian** | 4 (Pinjam + Kembali + Denda) | 3 | 1 |
| **E. Layanan Mahasiswa** | 4 (Katalog + Detail + Reservasi + Extend) | 5 | 0 |
| **F. Manajemen Reservasi** | 2 (Lihat + Batalkan) | 2 | 0 |
| **G. Keamanan Akses** | 2 (RBAC) | 0 | 2 |
| **TOTAL** | **20+ Fungsi** | **21 TC** | **13 TC** |

---
*SmartLib QA Lab Manual — Supporting the next generation of Indonesian SQA Engineers.*
