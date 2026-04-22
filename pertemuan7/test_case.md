# 📙 SmartLib QA Lab Manual: Automation Fundamentals
**Standard**: Antigravity Architect Academic Suite  
**Target**: Mahasiswa SQA (Software Quality Assurance)

---

## 🎯 1. Tujuan Praktikum
Setelah menyelesaikan modul ini, Mahasiswa diharapkan mampu:
1.  Melakukan pengujian otomasi menggunakan **Selenium IDE**.
2.  Membedakan antara **Positive Testing** (jalur sukses) dan **Negative Testing** (jalur gagal).
3.  Mendokumentasikan hasil pengujian secara profesional dan akurat.
4.  Menganalisis mekanisme validasi pada aplikasi web.

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

1.  **Preparation**: Jalankan aplikasi SmartLib di `http://localhost:8000/`.
2.  **Execution**: Jalankan setiap Test Case (TC) secara berurutan menggunakan Selenium IDE.
3.  **Logging**: Catat hasil setiap TC ke dalam **Tabel Laporan Hasil Pengujian** (lihat poin 4).
4.  **Evidence**: Ambil screenshot jika terjadi error atau kegagalan asersi. Beri nama: `[TC-ID]_[PASS/FAIL].png`.
5.  **Documentation**: Simpan project Selenium dalam format `.side`.

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

## 🛠️ 6. Modul B: Administrasi Pengguna (Admin)

> [!NOTE]
> Semua test dalam modul ini memerlukan login sebagai **Admin** (`admin@smartlib.id`) terlebih dahulu.

### [POSITIVE] TC-ADM-001 — Tambah Pengguna Baru
| Parameter | Detail |
| :--- | :--- |
| **Data** | Nama: `Tester SQA`, NIM: `2021999`, Email: `tester.sqa@polman.id`, Role: Mahasiswa, Password: `test1234` |
| **Ekspektasi** | Redirect ke `/admin/users.php`, muncul flash sukses "berhasil ditambahkan" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/user_add.php` | |
| 2 | `assertTitle` | | `Tambah Pengguna — SmartLib` |
| 3 | `execute script` | `document.querySelector('input[name="full_name"]').value='Tester SQA'` | |
| 4 | `execute script` | `document.querySelector('input[name="nim"]').value='2021999'` | |
| 5 | `execute script` | `document.querySelector('input[name="email"]').value='tester.sqa@polman.id'` | |
| 6 | `select` | `name=role` | `label=Mahasiswa (Akses Katalog)` |
| 7 | `execute script` | `document.querySelector('input[name="password"]').value='test1234'` | |
| 8 | `execute script` | `document.querySelector('input[name="password2"]').value='test1234'` | |
| 9 | `click` | `css=button[type="submit"]` | |
| 10 | `assertUrlContains` | | `admin/users.php` |
| 11 | `assertTextContains` | `css=.alert-success` | `berhasil ditambahkan` |

#### Yang Harus Dicatat Mahasiswa
- Tuliskan ID user baru yang muncul di tabel (untuk test Edit/Delete berikutnya).

---

### [NEGATIVE] TC-ADM-002 — Tambah Pengguna Gagal (Email Duplikat)
| Parameter | Detail |
| :--- | :--- |
| **Data** | Email yang sudah ada: `andi@student.polman.id` |
| **Ekspektasi** | Muncul error "Email sudah digunakan" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/user_add.php` | |
| 2 | `execute script` | `document.querySelector('input[name="full_name"]').value='User Duplikat'` | |
| 3 | `execute script` | `document.querySelector('input[name="email"]').value='andi@student.polman.id'` | |
| 4 | `execute script` | `document.querySelector('input[name="password"]').value='test1234'` | |
| 5 | `execute script` | `document.querySelector('input[name="password2"]').value='test1234'` | |
| 6 | `click` | `css=button[type="submit"]` | |
| 7 | `assertElementPresent` | `css=.alert-danger` | |
| 8 | `assertTextContains` | `css=.alert-danger` | `sudah digunakan` |

---

### [NEGATIVE] TC-ADM-003 — Tambah Pengguna Gagal (Password Tidak Cocok)
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Muncul error "Konfirmasi password tidak cocok" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/user_add.php` | |
| 2 | `execute script` | `document.querySelector('input[name="full_name"]').value='Test Konfirmasi'` | |
| 3 | `execute script` | `document.querySelector('input[name="email"]').value='konfirmasi@test.id'` | |
| 4 | `execute script` | `document.querySelector('input[name="password"]').value='password1'` | |
| 5 | `execute script` | `document.querySelector('input[name="password2"]').value='password2'` | |
| 6 | `click` | `css=button[type="submit"]` | |
| 7 | `assertTextContains` | `css=.alert-danger` | `tidak cocok` |

---

### [POSITIVE] TC-ADM-004 — Lihat Daftar Pengguna
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Tabel pengguna tampil, setidaknya ada 3 user (Admin, Pustakawan, Mahasiswa) |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/users.php` | |
| 2 | `assertElementPresent` | `css=table tbody tr` | |

---

### [POSITIVE] TC-ADM-005 — Edit Data Pengguna
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | User `tester.sqa@polman.id` sudah dibuat (TC-ADM-001) |
| **Data Baru** | Nama: `Tester SQA (Updated)`, Status: Aktif |
| **Ekspektasi** | Redirect ke daftar user, muncul pesan sukses "berhasil diperbarui" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/users.php` | |
| 2 | `click` | `css=a[href*="user_edit.php"]:last-of-type` | |
| 3 | `execute script` | `document.querySelector('input[name="full_name"]').value='Tester SQA (Updated)'` | |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertTextContains` | `css=.alert-success` | `berhasil diperbarui` |

---

### [NEGATIVE] TC-ADM-006 — Edit Gagal (Admin Nonaktifkan Diri Sendiri)
> [!CAUTION]
> Sistem harus memblokir Admin dari menonaktifkan akun mereka sendiri untuk mencegah *self-lockout*.

| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Muncul pesan "Anda tidak dapat menonaktifkan akun Anda sendiri" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/users.php` | |
| 2 | `click` | `css=a[href*="user_edit.php?id=1"]` | *(ID sesuai ID Admin)* |
| 3 | `uncheck` | `id=is_active` | |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertTextContains` | `css=.alert-danger` | `tidak dapat menonaktifkan` |

#### Yang Harus Dicatat Mahasiswa
- Ini adalah contoh **Business Rule Validation** — aturan bisnis yang diimplementasikan di server, bukan di browser.

---

### [POSITIVE] TC-ADM-007 — Hapus Pengguna
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | User dummy `tester.sqa@polman.id` ada di sistem |
| **Ekspektasi** | Data user hilang dari tabel, muncul flash sukses |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/users.php` | |
| 2 | `click` | `css=a[href*="user_delete.php"]:last-of-type` | |
| 3 | `chooseOkOnNextConfirmation` | | |
| 4 | `assertUrlContains` | | `admin/users.php` |
| 5 | `assertTextContains` | `css=.alert-success` | `berhasil dihapus` |

---

## 📚 7. Modul C: Manajemen Buku (Librarian)

> [!NOTE]
> Semua test dalam modul ini memerlukan login sebagai **Pustakawan** (`pustakawan@smartlib.id`).

### [POSITIVE] TC-BOOK-001 — Tambah Buku Baru
| Parameter | Detail |
| :--- | :--- |
| **Data** | Judul: `Rekayasa Perangkat Lunak`, Author: `Roger Pressman`, ISBN: `978-999-TEST-001`, Stok: `3`, Rak: `LT.2 B5` |
| **Ekspektasi** | Redirect ke `/librarian/books.php`, muncul flash sukses "berhasil ditambahkan" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/book_add.php` | |
| 2 | `execute script` | `document.querySelector('input[name="title"]').value='Rekayasa Perangkat Lunak'` | |
| 3 | `execute script` | `document.querySelector('input[name="author"]').value='Roger Pressman'` | |
| 4 | `execute script` | `document.querySelector('input[name="isbn"]').value='978-999-TEST-001'` | |
| 5 | `execute script` | `document.querySelector('input[name="publisher"]').value='McGraw-Hill'` | |
| 6 | `execute script` | `document.querySelector('input[name="year"]').value='2023'` | |
| 7 | `select` | `name=category` | `label=Pengujian Software` |
| 8 | `execute script` | `document.querySelector('input[name="total_copies"]').value='3'` | |
| 9 | `execute script` | `document.querySelector('input[name="rack_location"]').value='LT.2 B5'` | |
| 10 | `click` | `id=btnSave` | |
| 11 | `assertUrlContains` | | `librarian/books.php` |
| 12 | `assertTextContains` | `css=.alert-success` | `berhasil ditambahkan` |

#### Yang Harus Dicatat Mahasiswa
- Judul buku yang baru ditambahkan (untuk referensi TC-BOOK-005 dan TC-BOOK-006).

---

### [NEGATIVE] TC-BOOK-002 — Tambah Buku Gagal (ISBN Duplikat)
| Parameter | Detail |
| :--- | :--- |
| **Data** | ISBN: `978-0132350884` (ISBN Clean Code yang sudah ada) |
| **Ekspektasi** | Muncul error "ISBN sudah ada dalam sistem" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/book_add.php` | |
| 2 | `execute script` | `document.querySelector('input[name="title"]').value='Buku Test Duplikat'` | |
| 3 | `execute script` | `document.querySelector('input[name="author"]').value='Penulis Test'` | |
| 4 | `execute script` | `document.querySelector('input[name="isbn"]').value='978-0132350884'` | |
| 5 | `click` | `id=btnSave` | |
| 6 | `assertElementPresent` | `css=.alert-danger` | |
| 7 | `assertTextContains` | `css=.alert-danger` | `sudah ada dalam sistem` |

---

### [NEGATIVE] TC-BOOK-003 — Tambah Buku Gagal (Field Wajib Kosong)
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Muncul error "Judul buku wajib diisi" dan "Penulis wajib diisi" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/book_add.php` | |
| 2 | `click` | `id=btnSave` | |
| 3 | `assertElementPresent` | `css=.alert-danger` | |
| 4 | `assertTextContains` | `css=.alert-danger` | `wajib diisi` |

---

### [POSITIVE] TC-BOOK-004 — Lihat Daftar & Cari Buku
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Tabel buku tampil, fungsi pencarian memfilter hasil dengan benar |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/books.php` | |
| 2 | `assertElementPresent` | `css=table tbody tr` | |
| 3 | `execute script` | `document.querySelector('input[name="q"]').value='Clean Code'` | |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertElementPresent` | `css=table tbody tr` | |
| 6 | `assertTextContains` | `css=table tbody tr:first-child` | `Clean Code` |

---

### [POSITIVE] TC-BOOK-005 — Edit Metadata Buku
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Buku "Rekayasa Perangkat Lunak" sudah ditambahkan (TC-BOOK-001) |
| **Data Baru** | Judul: `Rekayasa Perangkat Lunak (Edisi 8)` |
| **Ekspektasi** | Muncul flash sukses "berhasil diperbarui" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/books.php` | |
| 2 | `execute script` | `document.querySelector('input[name="q"]').value='Rekayasa Perangkat Lunak'` | |
| 3 | `click` | `css=button[type="submit"]` | |
| 4 | `click` | `css=a[href*="book_edit.php"]:first-of-type` | |
| 5 | `execute script` | `document.querySelector('input[name="title"]').value='Rekayasa Perangkat Lunak (Edisi 8)'` | |
| 6 | `click` | `css=button[type="submit"]` | |
| 7 | `assertTextContains` | `css=.alert-success` | `berhasil diperbarui` |

---

### [POSITIVE] TC-BOOK-006 — Hapus Buku
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Buku "Rekayasa Perangkat Lunak" sudah ada dan belum ada transaksi pinjam |
| **Ekspektasi** | Buku terhapus (soft-delete), muncul flash sukses |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/books.php` | |
| 2 | `execute script` | `document.querySelector('input[name="q"]').value='Rekayasa Perangkat Lunak'` | |
| 3 | `click` | `css=button[type="submit"]` | |
| 4 | `click` | `css=a[href*="book_delete.php"]:first-of-type` | |
| 5 | `chooseOkOnNextConfirmation` | | |
| 6 | `assertTextContains` | `css=.alert-success` | `berhasil dihapus` |

---

## 📈 8. Modul D: Sirkulasi & Pengembalian (Librarian)

### [POSITIVE] TC-LOAN-001 — Proses Peminjaman Buku
> [!TIP]
> Dropdown **Mahasiswa** dan **Buku** menggunakan **Tom Select** (searchable dropdown). Untuk Firefox, kita perlu mensimulasikan interaksi klik pada komponen UI-nya, bukan pada `<select>` aslinya.

| Parameter | Detail |
| :--- | :--- |
| **Data Mahasiswa** | `Andi Pratama` (NIM: 2021001) |
| **Data Buku** | `Clean Code` |
| **Ekspektasi** | Redirect ke `/librarian/loans.php`, muncul flash sukses "berhasil diproses" |

#### Script Selenium — Interaksi Tom Select
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/loan_add.php` | |
| 2 | `click` | `css=#user_id + .ts-wrapper .ts-control` | *(Klik area dropdown Mahasiswa)* |
| 3 | `execute script` | `document.querySelector('#user_id + .ts-wrapper input').value='Andi'` | |
| 4 | `sendKeys` | `css=#user_id + .ts-wrapper input` | *(ketik: 'Andi' lanjut tekan Enter atau klik opsi)* |
| 5 | `waitForElementVisible` | `css=.ts-dropdown .option` | |
| 6 | `click` | `css=.ts-dropdown .option:first-child` | |
| 7 | `click` | `css=#book_id + .ts-wrapper .ts-control` | *(Klik area dropdown Buku)* |
| 8 | `execute script` | `document.querySelector('#book_id + .ts-wrapper input').value='Clean Code'` | |
| 9 | `waitForElementVisible` | `css=.ts-dropdown .option` | |
| 10 | `click` | `css=.ts-dropdown .option:first-child` | |
| 11 | `click` | `css=button[type="submit"]` | |
| 12 | `assertUrlContains` | | `librarian/loans.php` |
| 13 | `assertTextContains` | `css=.alert-success` | `berhasil diproses` |

> **Catatan**: Alternatif yang lebih andal untuk Tom Select di Firefox — gunakan JavaScript langsung:
> `execute script` | `document.getElementById('user_id').tomselect.setValue('1')` | *(ganti '1' dengan ID Mahasiswa)*

---

### [NEGATIVE] TC-LOAN-002 — Peminjaman Gagal (Kuota Maksimum)
> [!IMPORTANT]
> Aturan Bisnis: Mahasiswa tidak boleh meminjam lebih dari **3 buku** secara bersamaan (`MAX_ACTIVE_LOANS = 3`).

| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Mahasiswa target sudah meminjam 3 buku aktif |
| **Ekspektasi** | Muncul info "sudah mencapai batas maksimum peminjaman" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/loan_add.php` | |
| 2 | `execute script` | `document.getElementById('user_id').tomselect.setValue('2')` | *(ID Mhs yg sudah 3 pinjaman)* |
| 3 | `waitForElementVisible` | `id=loanInfo` | |
| 4 | `assertTextContains` | `id=loanInfo` | `batas` |

#### Yang Harus Dicatat Mahasiswa
- Apakah tombol Submit diblokir oleh frontend, atau validasi terjadi saat submit ke server?

---

### [POSITIVE] TC-LOAN-003 — Lihat Daftar Sirkulasi Aktif
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Tabel sirkulasi tampil, ada kolom Nama Mahasiswa, Judul Buku, Jatuh Tempo |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/loans.php` | |
| 2 | `assertElementPresent` | `css=table thead tr` | |
| 3 | `assertTextContains` | `css=table thead` | `Mahasiswa` |

---

### [POSITIVE] TC-RET-001 — Pengembalian Tepat Waktu (Tanpa Denda)
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Ada transaksi pinjam aktif yang belum melewati jatuh tempo |
| **Ekspektasi** | Muncul panel "Rp 0 — Tidak ada denda", redirect sukses ke daftar sirkulasi |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/loans.php` | |
| 2 | `click` | `css=a[href*="return_book.php"]:first-of-type` | |
| 3 | `assertTextContains` | `css=.h3.fw-bold` | `Rp 0` |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertUrlContains` | | `librarian/loans.php` |
| 6 | `assertTextContains` | `css=.alert-success` | `berhasil dikembalikan` |

---

### [POSITIVE] TC-RET-002 — Pengembalian Terlambat (Denda Lunas)
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Ada transaksi pinjam dengan status `overdue` (tanggal jatuh tempo sudah lewat) |
| **Ekspektasi** | Denda tampil (Rp > 0), centang "Lunas", proses berhasil dengan keterangan "(Lunas)" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/loans.php` | |
| 2 | `click` | `css=a[href*="return_book.php"]:first-of-type` | |
| 3 | `assertElementPresent` | `css=input#fine_paid` | *(Pastikan checkbox denda muncul)* |
| 4 | `check` | `id=fine_paid` | |
| 5 | `click` | `css=button[type="submit"]` | |
| 6 | `assertTextContains` | `css=.alert-success` | `Lunas` |

---

## 🎓 9. Modul E: Layanan Mahasiswa (Student)

> [!NOTE]
> Semua test dalam modul ini memerlukan login sebagai **Mahasiswa** (`andi@student.polman.id`).

### [POSITIVE] TC-STU-001 — Navigasi Katalog & Pencarian Buku
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Katalog tampil, pencarian "rekayasa" mengembalikan hasil yang relevan |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/student/catalog.php` | |
| 2 | `assertElementPresent` | `css=.book-card` | |
| 3 | `execute script` | `document.querySelector('input[name="q"]').value='rekayasa'` | |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertElementPresent` | `css=.book-card` | |

#### Yang Harus Dicatat Mahasiswa
- Berapa jumlah hasil yang muncul untuk kata kunci "rekayasa"?
- Apakah hasil sudah relevan?

---

### [POSITIVE] TC-STU-002 — Lihat Detail Buku
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Halaman detail menampilkan Judul, Author, ISBN, Penerbit, dan Status Ketersediaan |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/student/catalog.php` | |
| 2 | `click` | `css=.book-card a:first-of-type` | |
| 3 | `assertUrlContains` | | `student/book_detail.php` |
| 4 | `assertElementPresent` | `css=.display-6.fw-bold` | *(Judul buku)* |
| 5 | `assertElementPresent` | `css=.card.border-0` | *(Panel Status Ketersediaan)* |

---

### [POSITIVE] TC-STU-003 — Reservasi Buku yang Habis
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Ada buku dengan `available_copies = 0` (stok habis semua dipinjam) |
| **Ekspektasi** | Tombol "Reservasi Buku" tampil, setelah diklik muncul flash sukses "Reservasi berhasil" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/student/catalog.php` | |
| 2 | `click` | `css=a[href*="book_detail.php"]` | *(Pilih buku yang habis)* |
| 3 | `assertElementPresent` | `css=button[type="submit"]` | *(Tombol Reservasi Buku)* |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertTextContains` | `css=.alert-success` | `Reservasi berhasil` |

---

### [POSITIVE] TC-STU-004 — Perpanjangan Pinjaman Mandiri
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Mahasiswa memiliki pinjaman aktif yang masih bisa diperpanjang |
| **Ekspektasi** | Jatuh tempo bertambah, muncul flash sukses "berhasil diperpanjang" |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/student/my_loans.php` | |
| 2 | `assertElementPresent` | `css=table tbody tr` | |
| 3 | `click` | `css=a[href*="extend_loan.php"]:first-of-type` | |
| 4 | `click` | `css=button[type="submit"]` | |
| 5 | `assertTextContains` | `css=.alert-success` | `berhasil diperpanjang` |

#### Yang Harus Dicatat Mahasiswa
- Berapa hari jatuh tempo diperpanjang? (Catat tanggal sebelum dan sesudah)

---

### [POSITIVE] TC-STU-005 — Lihat Riwayat Pinjaman
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Tabel "Pinjaman Saya" tampil dengan kolom Buku, Tgl Pinjam, Jatuh Tempo, Status |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/student/my_loans.php` | |
| 2 | `assertElementPresent` | `css=table thead tr` | |
| 3 | `assertTextContains` | `css=table thead` | `Jatuh Tempo` |

---

## 🏛️ 10. Modul F: Manajemen Reservasi (Librarian)

### [POSITIVE] TC-RES-001 — Lihat Daftar Reservasi
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Halaman Manajemen Reservasi tampil dengan tab status (Menunggu, Diberitahu, dll.) |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/reservations.php` | |
| 2 | `assertTitle` | | `Manajemen Reservasi — SmartLib` |
| 3 | `assertElementPresent` | `css=.nav-tabs` | |

---

### [POSITIVE] TC-RES-002 — Batalkan Reservasi
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Ada reservasi dengan status "Menunggu" di daftar |
| **Ekspektasi** | Status reservasi berubah menjadi "Dibatalkan", muncul flash sukses |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/librarian/reservations.php?status=waiting` | |
| 2 | `assertElementPresent` | `css=table tbody tr` | |
| 3 | `click` | `css=button[data-confirm*="Batalkan"]` | |
| 4 | `chooseOkOnNextConfirmation` | | |
| 5 | `assertTextContains` | `css=.alert-success` | `dibatalkan` |

---

## 🔒 11. Modul G: Keamanan & Kontrol Akses (Security)

### [NEGATIVE] TC-SEC-001 — Proteksi Halaman Admin (oleh Mahasiswa)
> [!CAUTION]
> Mahasiswa **tidak boleh** mengakses halaman Admin meskipun tau URL-nya secara langsung.

| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Login sebagai Mahasiswa (`andi@student.polman.id`) |
| **Ekspektasi** | Redirect ke `/unauthorized.php` atau `/login.php` |

#### Script Selenium
| # | Command | Target | Value |
| :--- | :--- | :--- | :--- |
| 1 | `open` | `/admin/users.php` | |
| 2 | `assertFalse` | `css=table` | *(Tabel user tidak boleh tampil)* |

---

### [NEGATIVE] TC-SEC-002 — Proteksi Halaman Librarian (oleh Mahasiswa)
| Parameter | Detail |
| :--- | :--- |
| **Prasyarat** | Login sebagai Mahasiswa |
| **Ekspektasi** | Redirect ke `/unauthorized.php` (bukan halaman sirkulasi) |

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
