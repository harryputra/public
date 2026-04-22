# Technical Specification Document - SmartLib Automation System

## Project Overview
**SmartLib** adalah sistem manajemen perpustakaan berbasis PHP/MySQL yang dirancang untuk kebutuhan pengajaran SQA. Dokumen ini merinci spesifikasi teknis dari sistem otomasi "One-Click Setup" dan "Live Server" yang diimplementasikan oleh Antigravity Architect.

## 1. Automation Architecture (setup.bat)
Sistem otomasi dirancang agar sepenuhnya *self-contained* dan portabel.
*   **Auto-Elevation (UAC)**: Menggunakan PowerShell wrapper untuk memastikan script berjalan dengan hak akses Administrator agar bisa mengontrol Windows Services (MySQL).
*   **Heuristic Path Discovery**: Mencari binari `php.exe` dan `mysql.exe` di berbagai lokasi standar (XAMPP, Laragon, WAMP) di drive C hingga F.
*   **Service Recovery**: Secara otomatis mencoba memulai service MySQL (`net start`) atau menjalankan daemon `mysqld.exe` jika service tidak ditemukan.
*   **Port Management**: Melakukan pengecekan port 8000 dan secara otomatis bergeser jika port tersebut sedang digunakan.

## 2. Live Server Implementation
Fitur Live Server diimplementasikan tanpa dependensi pihak ketiga (seperti Node.js atau BrowserSync) untuk menjaga portabilitas.
*   **Backend Watcher (`live-reload.php`)**: Script PHP yang memindai struktur direktori secara rekursif dan mengembalikan *timestamp* modifikasi terbaru dari file `.php`, `.css`, dan `.js`.
*   **Frontend Poller**: Injeksi JavaScript di `header.php` yang melakukan polling asinkron ke `live-reload.php` setiap 1.5 detik. Jika terdeteksi perubahan *timestamp*, halaman akan dimuat ulang otomatis.

## 3. Database Management
*   **Implicit Schema Sync**: Script akan memeriksa keberadaan database `smartlib`.
*   **Automated Seeding**: Jika database kosong, script akan imimpor `database/smartlib.sql` dan menjalankan `install.php` via CLI untuk mengisi data demo.

## 4. UI & UX Modernization
Transformasi antarmuka untuk mencapai standar estetika "Premium" dan "High-Professional":
*   **Executive Dashboard (v2.0)**: Desain ulang total modul Admin dengan layout berbasis kartu *gradient-glass*.
*   **Form Standardization**: Mengimplementasikan pola formulir dua kolom (Metadata/Inventory) pada modul manajemen user dan koleksi buku untuk efisiensi input.
*   **Quick Operations Grid**: Dashboard kini berfungsi sebagai pusat kendali dengan navigasi cepat ke fitur-fitur krusial.
*   **Real-time Indicators**: Penambahan elemen UI yang responsif terhadap status sistem (System Health Pulse).
*   **Accessibility Path**: Penyempurnaan warna teks navbar (`navbar-light`) untuk meningkatkan kontras dan keterbacaan sesuai arahan laporan verifikasi.
*   **Professional Notification System**: Migrasi total dari default browser `confirm()` dan alert Bootstrap ke **SweetAlert2**. Mengimplementasikan asinkron konfirmasi asinkron dan sistem Toast global untuk feedback operasi yang efisien.
*   **Book Cover Management**: Penambahan sistem pengunggahan sampul buku berbasis file di modul Tambah dan Edit Buku. Mencakup fungsi helper `uploadImage()` yang aman, sistem pratinjau instan (JS Preview), dan pembersihan otomatis file lama saat sampul diperbarui.
*   **Circulation UI Modernization**: Redesain halaman Pengembalian Buku menjadi layout dua kolom yang informatif, mengintegrasikan visual sampul buku dan panel ringkasan denda yang dinamis untuk efisiensi kerja pustakawan.
*   **Premium Student Experience**: Transformasi halaman Detail Buku mahasiswa menjadi antarmuka katalog modern dengan visualisasi sampul berbayangan, metadata berbasis ikon, dan panel aksi ketersediaan yang intuitif.
*   **Enhanced Login Accessibility**: Penambahan panel informasi akun demo pada halaman login yang terstruktur menggunakan grid Bootstrap. Fitur ini memudahkan penguji dan pengembang untuk berpindah antar role (Admin, Pustakawan, Mahasiswa) dengan cepat selama fase testing.
*   **Searchable Components (Tom Select)**: Implementasi komponen dropdown pencarian pada modul Sirkulasi (Peminjaman Baru). Menggantikan elemen `<select>` standar dengan Tom Select untuk memudahkan pencarian mahasiswa dan buku dalam dataset besar, lengkap dengan visual feedback sisa kuota pinjam yang responsif.
*   **Automated Testing Alignment**: Sinkronisasi dokumen `test_case.md` dengan skema database v1.0. Mencakup pembaruan 37 test case agar kompatibel dengan lingkungan Base URL `:8000` dan data riil dari seeder sistem.
*   **Cross-Browser Automation Standard**: Implementasi metode *Script Injection* (`execute script`) pada dokumen pengujian untuk melewati restriksi API keyboard pada browser Firefox modern, memastikan reliabilitas pengujian otomatis tanpa hambatan platform.
*   **Enterprise QA Documentation Overhaul**: Transformasi `test_case.md` menjadi spesifikasi pengujian standar industri yang mencakup executive summary, environment setup, dan modularisasi test cases untuk skalabilitas tim QA.
*   **Academic Testing Pedagogical Standard**: Implementasi panduan lab terstruktur dalam `test_case.md` yang mencakup instruksi pengerjaan berkas laporan, template log hasil pengujian, serta perluasan skenario *Negative Testing* untuk melatih logika analisis kegagalan mahasiswa.
*   **Playwright Automation Standard**: Penyusunan panduan `test_case2_with_playwright.md` sebagai standar modern untuk *code-based automation*. Mencakup framework Node.js, pemanfaatan auto-waiting, integrasi *Trace Viewer*, serta modul lanjutan untuk penanganan komponen UI kustom (Tom Select) dan pengenalan arsitektur Page Object Model (POM) bagi pemula.





## 5. Bug Fixes & Patch
*   **Hotfix #001 (Type Safety)**: Memperbaiki kegagalan fatal pada modul `librarian/loans.php` dan `return_book.php`. Masalah disebabkan oleh pengiriman tipe data `array` ke fungsi `calculateFine()` yang secara eksplisit mengharapkan `string`. Solusinya adalah mengirimkan parameter tanggal secara spesifik (`due_date` dan `return_date`).

## 5. Technical Summary (Antigravity Sync)
*   **Logic Change**: Memindahkan mekanisme "Web Server" statis menjadi dinamis dengan "Live Reload" dan otomasi service DB.
*   **Rule Compliance**: Mematuhi aturan "No Side Effects" dengan tidak mengubah folder `/config` secara destruktif, hanya memperluas `header.php`.
*   **Testing Method**: Manual integration testing (Simulation of cold-start and file modification triggers).

---
*Created by Antigravity Architect - 2026-04-21*
