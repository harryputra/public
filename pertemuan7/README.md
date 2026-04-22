# Pertemuan 7 – Studi Kasus Automation Testing dengan Selenium IDE

> **Mata Kuliah:** Software Quality Assurance (SQA)  
> **Program Studi:** Teknik Informatika – POLMAN Bandung  
> **Topik:** Automation Testing menggunakan Selenium IDE pada Aplikasi Web

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Prasyarat](#2-prasyarat)
3. [Cara Menjalankan Aplikasi (Otomatis)](#3-cara-menjalankan-aplikasi-otomatis)
4. [Setup Manual (Jika .bat Gagal)](#4-setup-manual-jika-bat-gagal)
5. [Akun Demo](#5-akun-demo)
6. [Struktur Folder](#6-struktur-folder)
7. [Tugas Mahasiswa](#7-tugas-mahasiswa)
8. [Panduan Selenium IDE](#8-panduan-selenium-ide)
9. [Format Laporan](#9-format-laporan)
10. [Rubrik Penilaian](#10-rubrik-penilaian)

---

## 1. Gambaran Umum

Pada pertemuan ini, kamu akan mempelajari dan mempraktikkan **automation testing** menggunakan **Selenium IDE** pada aplikasi web nyata.

Aplikasi yang digunakan adalah **SmartLib** – sistem manajemen perpustakaan kampus berbasis PHP Native + MySQL dengan tiga role pengguna: Admin, Pustakawan, dan Mahasiswa.

### Apa yang akan kamu pelajari?

- Konsep dan manfaat automation testing
- Cara merekam dan memutar test menggunakan Selenium IDE
- Menulis test case dengan assertions yang tepat
- Mengelompokkan test ke dalam test suite
- Menghasilkan laporan pengujian

---

## 2. Prasyarat

Pastikan software berikut sudah terinstall sebelum memulai:

### Web Server Stack (pilih salah satu)

| Software | Versi Minimum | Download |
|----------|---------------|----------|
| **XAMPP** *(rekomendasi)* | 8.0+ | [apachefriends.org](https://www.apachefriends.org/) |
| Laragon | 6.0+ | [laragon.org](https://laragon.org/) |
| WAMP64 | 3.2+ | [wampserver.com](https://www.wampserver.com/) |

> **Penting:** PHP minimal versi **8.0**, MySQL/MariaDB minimal **5.7 / 10.4**

### Browser & Extension

| Software | Keterangan |
|----------|------------|
| **Firefox** atau **Chrome** | Browser terbaru |
| **Selenium IDE** | Extension browser – install dari [selenium.dev/selenium-ide](https://www.selenium.dev/selenium-ide/) |

### Git

```
git --version   # minimal 2.x
```

---

## 3. Cara Menjalankan Aplikasi (Otomatis)

### Langkah 1 – Clone Repositori

```bash
git clone <URL_REPOSITORI>
cd <nama-folder>
```

### Langkah 2 – Pindahkan ke htdocs / www

Salin seluruh folder `public/` ke dalam folder web server kamu:

| Web Server | Folder Tujuan |
|------------|---------------|
| XAMPP | `C:\xampp\htdocs\` |
| Laragon | `C:\laragon\www\` |
| WAMP64 | `C:\wamp64\www\` |

Sehingga strukturnya menjadi:
```
htdocs/
└── pertemuan7/
    ├── perpustakaan/   ← folder aplikasi
    ├── test_case.md
    └── README.md
```

### Langkah 3 – Jalankan setup.bat

Navigasi ke folder `perpustakaan/` lalu **double-click** file `setup.bat`:

```
htdocs/pertemuan7/perpustakaan/setup.bat
```

Script **Antigravity One-Click Setup** ini akan otomatis:

1. 🛡️ **Auto-Elevation**: Meminta izin Administrator secara otomatis (UAC) jika diperlukan.
2. 🔍 **Smart Discovery**: Mencari lokasi PHP dan MySQL secara otomatis (XAMPP/Laragon).
3. ⚡ **Service Manager**: Menjalankan service MySQL jika belum aktif di latar belakang.
4. 📦 **Database Sync**: Membuat database `smartlib` dan mengisi data demo secara instan.
5. 🌐 **Live Server & Browser**: Menjalankan server lokal dan membuka browser secara otomatis.

> [!TIP]
> **Fitur Live Reload Aktif!** Browser akan otomatis memuat ulang halaman (refresh) setiap kali kamu melakukan perubahan pada file `.php`, `.css`, atau `.js`. Kamu bisa fokus melakukan pengujian tanpa perlu refresh manual.

> **Jika setup.bat gagal**, lihat bagian [Setup Manual](#4-setup-manual-jika-bat-gagal).

---

## 4. Setup Manual (Jika .bat Gagal)

### Langkah A – Jalankan XAMPP

1. Buka **XAMPP Control Panel**
2. Klik **Start** pada baris **Apache**
3. Klik **Start** pada baris **MySQL**

### Langkah B – Buat Database

**Opsi 1 – phpMyAdmin:**
1. Buka `http://localhost/phpmyadmin`
2. Klik **Import** di menu atas
3. Pilih file: `pertemuan7/perpustakaan/database/smartlib.sql`
4. Klik **Go**

**Opsi 2 – MySQL CLI:**
```bash
mysql -u root -p < C:\xampp\htdocs\pertemuan7\perpustakaan\database\smartlib.sql
```

### Langkah C – Seed Data

Buka browser dan akses:
```
http://localhost/pertemuan7/perpustakaan/install.php
```

Halaman install akan membuat akun demo dan data buku secara otomatis.

> **Hapus `install.php` setelah selesai** di lingkungan produksi (tidak diperlukan untuk tugas ini).

### Langkah D – Akses Aplikasi

```
http://localhost/pertemuan7/perpustakaan/
```

---

## 5. Akun Demo

Semua akun menggunakan satu password standar: **`smartlib123`**

| Role | Email | Nama Pengguna | NIM/NIP |
|------|-------|---------------|---------|
| 🔴 **Admin** | `admin@smartlib.id` | Administrator | ADMIN001 |
| 🟠 **Pustakawan** | `pustakawan@smartlib.id` | Budi Santoso | PUST001 |
| 🔵 **Mahasiswa 1** | `andi@student.polman.id` | Andi Pratama | 2021001 |
| 🔵 **Mahasiswa 2** | `siti@student.polman.id` | Siti Rahayu | 2021002 |
| 🔵 **Mahasiswa 3** | `rizky@student.polman.id` | Rizky Firmansyah | 2021003 |
| 🔵 **Mahasiswa 4** | `diana@student.polman.id` | Diana Kusuma | 2021004 |

---

## 6. Struktur Folder

```
pertemuan7/
├── README.md                    ← Panduan ini
├── test_case.md                 ← Dokumen test case (37 skenario)
│
└── perpustakaan/                ← Root aplikasi SmartLib
    ├── setup.bat                ← Script setup otomatis
    ├── install.php              ← Installer web (seed data)
    ├── index.php                ← Entry point
    ├── login.php
    ├── logout.php
    ├── unauthorized.php
    │
    ├── config/
    │   ├── config.php           ← Konstanta & konfigurasi aplikasi
    │   └── database.php         ← Konfigurasi PDO (host, user, pass, dbname)
    │
    ├── includes/
    │   ├── auth.php             ← Helper autentikasi & otorisasi
    │   ├── functions.php        ← Helper umum (flash, CSRF, format, dll.)
    │   ├── header.php           ← Template header + navigasi
    │   └── footer.php           ← Template footer + JS
    │
    ├── student/                 ← Halaman role Mahasiswa
    │   ├── dashboard.php
    │   ├── catalog.php
    │   ├── book_detail.php
    │   ├── my_loans.php
    │   └── extend_loan.php
    │
    ├── librarian/               ← Halaman role Pustakawan
    │   ├── dashboard.php
    │   ├── books.php
    │   ├── book_add.php
    │   ├── book_edit.php
    │   ├── book_delete.php
    │   ├── loans.php
    │   ├── loan_add.php
    │   ├── return_book.php
    │   ├── reservations.php
    │   └── reservation_action.php
    │
    ├── admin/                   ← Halaman role Admin
    │   ├── dashboard.php
    │   ├── users.php
    │   ├── user_add.php
    │   ├── user_edit.php
    │   └── user_delete.php
    │
    ├── database/
    │   └── smartlib.sql         ← Schema database (tanpa seed data)
    │
    └── assets/
        ├── css/style.css        ← Stylesheet kustom
        └── js/main.js           ← JavaScript kustom
```

---

## 7. Tugas Mahasiswa

### Deskripsi Tugas

Kamu diminta untuk melakukan **automation testing** pada aplikasi SmartLib menggunakan **Selenium IDE**. Gunakan file `test_case.md` sebagai panduan test case yang harus diimplementasikan.

### Yang Harus Dikerjakan

#### A. Implementasi Test Case (Wajib)

Buat dan jalankan **minimal 20 test case** dari daftar di `test_case.md`, mencakup:

| Modul | Jumlah Min. |
|-------|-------------|
| Autentikasi (login/logout) | 4 TC |
| Manajemen Buku (CRUD) | 4 TC |
| Proses Peminjaman & Pengembalian | 4 TC |
| Fitur Mahasiswa (katalog, pinjaman, perpanjangan) | 4 TC |
| Kontrol Akses (role-based) | 4 TC |

#### B. Organisasi Test Suite

Kelompokkan test case ke dalam **Test Suite** berdasarkan modul di Selenium IDE.

#### C. Dokumentasi Hasil

Isi kolom **Status** di tabel ringkasan `test_case.md` untuk setiap TC yang kamu jalankan:

| Status | Arti |
|--------|------|
| `Pass` | Test berjalan sesuai hasil yang diharapkan |
| `Fail` | Test gagal – catat penyebabnya |
| `Skip` | Tidak dijalankan – jelaskan alasannya |

#### D. Laporan Pengujian

Buat file `laporan_testing.md` (lihat [Format Laporan](#9-format-laporan)).

### Pengumpulan

Kumpulkan dalam satu folder ZIP berisi:
1. File project Selenium IDE (`.side`)
2. File `test_case.md` yang sudah diperbarui (kolom status terisi)
3. File `laporan_testing.md`
4. Screenshot hasil eksekusi (minimal 5 screenshot)

---

## 8. Panduan Selenium IDE

### Instalasi

1. Buka browser Firefox atau Chrome
2. Cari **"Selenium IDE"** di extension store browser
3. Install ekstensi tersebut
4. Ikon Selenium IDE akan muncul di toolbar browser

### Memulai Project Baru

1. Klik ikon Selenium IDE di toolbar
2. Pilih **"Create a new project"**
3. Beri nama: `SmartLib Testing`
4. Set **Base URL**: `http://localhost/pertemuan7/perpustakaan/`

### Merekam Test Case

1. Di panel kiri, klik **"+"** untuk membuat test baru
2. Beri nama test sesuai ID (contoh: `TC-AUTH-001`)
3. Klik tombol **Record** (lingkaran merah)
4. Browser baru akan terbuka – lakukan aksi yang ingin direkam
5. Klik **Stop Recording** setelah selesai
6. Tambahkan **assertions** yang diperlukan

### Menambahkan Assertions

Klik kanan pada elemen di halaman saat recording → pilih:
- `Assert text` – verifikasi teks pada elemen
- `Assert element present` – verifikasi elemen ada
- `Assert URL` – verifikasi URL halaman

### Command Penting

| Command | Fungsi |
|---------|--------|
| `open` | Buka URL (relatif terhadap Base URL) |
| `click` | Klik elemen |
| `type` | Isi input field |
| `select` | Pilih opsi dropdown |
| `assertText` | Verifikasi teks elemen |
| `assertElementPresent` | Verifikasi elemen ada di halaman |
| `assertUrlContains` | Verifikasi URL mengandung teks |
| `waitForElementPresent` | Tunggu sampai elemen muncul |
| `storeText` | Simpan teks ke variabel |

### Menjalankan Test

- **Satu test:** Klik tombol ▶ (play) di samping nama test
- **Seluruh suite:** Klik ▶▶ (run all) di toolbar
- **Lihat hasil:** Panel bawah menampilkan PASS / FAIL per command

### Menyimpan Project

- **File → Save project** → simpan sebagai `smartlib_testing.side`
- File `.side` ini yang dikumpulkan sebagai tugas

---

## 9. Format Laporan

Buat file `laporan_testing.md` dengan struktur berikut:

```markdown
# Laporan Automation Testing – SmartLib
- **Nama     :** [Nama Lengkap]
- **NIM      :** [NIM]
- **Tanggal  :** [Tanggal Pengujian]
- **Tools    :** Selenium IDE [versi] – Firefox/Chrome [versi]

---

## Ringkasan Eksekusi

| Total TC Direncanakan | TC Pass | TC Fail | TC Skip | Pass Rate |
|-----------------------|---------|---------|---------|-----------|
| 20                    | ?       | ?       | ?       | ?%        |

---

## Hasil per Modul

### Modul Autentikasi
| ID | Deskripsi | Status | Catatan |
|----|-----------|--------|---------|
| TC-AUTH-001 | Login mahasiswa | Pass | - |
| ...         | ...             | ...  | ... |

(ulangi untuk setiap modul)

---

## Bug / Temuan

### BUG-001: [Judul Bug]
- **TC terkait:** TC-XXX-XXX
- **Langkah reproduksi:** ...
- **Hasil aktual:** ...
- **Hasil diharapkan:** ...
- **Severity:** Minor / Major / Critical

---

## Kesimpulan

[Tuliskan kesimpulan singkat tentang kualitas aplikasi berdasarkan hasil pengujian]
```

---

## 10. Rubrik Penilaian

| Kriteria | Bobot | Keterangan |
|----------|-------|------------|
| Jumlah TC dijalankan (min. 20) | 25% | Setiap TC terdokumentasi dengan benar |
| Kualitas assertions | 25% | Assertions relevan, bukan sekadar merekam klik |
| Organisasi Test Suite | 15% | TC dikelompokkan per modul dengan penamaan konsisten |
| Dokumentasi hasil | 20% | Kolom status terisi, bug terdokumentasi |
| Laporan (analisis & kesimpulan) | 15% | Analisis bermakna, bukan hanya rekap tabel |

### Kriteria PASS / FAIL

Sebuah test case dinyatakan **PASS** jika:
- Semua command berjalan tanpa error
- Semua assertions mengembalikan nilai `true`
- Behavior aplikasi sesuai kolom "Hasil Diharapkan" di `test_case.md`

Sebuah test case dinyatakan **FAIL** jika:
- Ada command yang error / timeout
- Ada assertion yang gagal
- Aplikasi menampilkan output berbeda dari yang diharapkan

> **Catatan:** TC yang FAIL bukan berarti nilai berkurang – justru menunjukkan bahwa kamu menemukan bug. Dokumentasikan dengan baik!

---

## Bantuan & Referensi

| Sumber | Link |
|--------|------|
| Dokumentasi Selenium IDE | [selenium.dev/selenium-ide/docs](https://www.selenium.dev/selenium-ide/docs/en/introduction/getting-started) |
| Tutorial Selenium (Bahasa Indonesia) | Lihat folder `tutorial_selenium/SeleniumTutorial_ID.md` |
| Test Case Lengkap | `pertemuan7/test_case.md` |
| PRD Aplikasi | `pertemuan7/prd_perpustakaan.md` |

---

*Selamat mengerjakan! Jika ada kendala teknis, hubungi asisten dosen atau tanyakan di forum kelas.*
