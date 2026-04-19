# PANDUAN LAPORAN PRAKTIKUM
## AUTOMATION TESTING PADA PENGEMBANGAN PERANGKAT LUNAK

### Mata Kuliah: Pengujian Perangkat Lunak / Rekayasa Perangkat Lunak

---

## A. TUJUAN PRAKTIKUM

Setelah menyelesaikan praktikum ini, mahasiswa diharapkan mampu:
1. Memahami konsep dasar Automation Testing dan perbedaannya dengan Manual Testing.
2. Membangun aplikasi web sederhana (TaskFlow) dengan FastAPI (backend) dan PHP Native + Bootstrap (frontend).
3. Mengimplementasikan Automation Testing untuk API menggunakan Postman dan Newman.
4. Mengimplementasikan Automation Testing untuk UI menggunakan Selenium WebDriver dengan Python.
5. Menerapkan Page Object Model (POM) dalam pengujian UI.
6. Melakukan Performance Testing menggunakan Locust.
7. Mengintegrasikan automation test ke dalam pipeline CI/CD dengan GitHub Actions.
8. Menganalisis hasil pengujian dan membuat laporan pengujian yang komprehensif.

---

## B. ALAT DAN BAHAN

| No | Alat/Bahan | Spesifikasi/Keterangan |
|----|------------|------------------------|
| 1 | Laptop/PC | Windows 10/11, macOS, atau Linux |
| 2 | Python | Versi 3.8 atau lebih tinggi |
| 3 | PHP | Versi 7.4 atau lebih tinggi |
| 4 | Node.js | Versi 16 atau lebih tinggi (untuk npm) |
| 5 | Browser | Google Chrome terbaru |
| 6 | Text Editor/IDE | Visual Studio Code (direkomendasikan) |
| 7 | Postman | Versi desktop terbaru |
| 8 | Git | Terinstall dan terhubung ke GitHub |
| 9 | Koneksi Internet | Untuk mengunduh dependencies |

**Python Libraries:**
- fastapi
- uvicorn
- sqlalchemy
- pydantic
- pytest
- selenium
- webdriver-manager
- locust
- requests

**Node Packages:**
- newman
- newman-reporter-htmlextra

---

## C. DASAR TEORI

*(Mahasiswa wajib membuat resume dari materi teori yang telah diberikan pada modul. Resume minimal mencakup poin-poin berikut:)*

### C.1. Pengertian Automation Testing
Jelaskan definisi Automation Testing dan analogikan dengan situasi nyata. Sertakan perbandingan dengan Manual Testing dalam bentuk tabel.

### C.2. Pentingnya Automation Testing dalam Era Modern
Jelaskan konsep CI/CD dan bagaimana Automation Testing menjadi jantung dari pipeline tersebut. Ceritakan studi kasus kecelakaan pesawat tahun 1994 sebagai pelajaran pentingnya pengujian otomatis.

### C.3. Transformasi dari Manual ke Automated Testing
Buat tabel perbandingan mendalam antara Manual Testing dan Automated Testing (minimal 8 aspek). Jelaskan kapan waktu yang tepat untuk beralih ke automation.

### C.4. Jenis-Jenis Pengujian yang Harus Diotomatisasi
Jelaskan secara singkat:
- End-to-End (E2E) Tests
- Unit Tests
- Integration Tests
- Performance Tests

Berikan contoh kasus masing-masing.

### C.5. Jenis Pengujian yang Sebaiknya Tetap Manual
Jelaskan Exploratory Testing, Usability Testing, dan Visual Regression Testing (aspek subjektif).

### C.6. Automation Testing untuk Full-Stack Developer
Jelaskan fokus pengujian pada sisi Backend dan Frontend beserta tools yang digunakan.

### C.7. Best Practices dalam Automation Testing
Sebutkan prinsip FIRST, Test Pyramid, dan tips-tips penting dalam menulis Selenium dan API tests.

*(Resume ditulis dengan bahasa sendiri, bukan copy-paste mentah dari modul. Panjang resume minimal 2 halaman.)*

---

## D. LANGKAH-LANGKAH PRAKTIKUM

### D.1. Persiapan Lingkungan dan Proyek

**Instruksi:**
1. Buat struktur folder proyek sesuai dengan yang ditentukan dalam modul.
2. Setup virtual environment Python dan install dependencies.
3. Inisialisasi Git repository.

**Dokumentasi yang harus disertakan:**
- Screenshot terminal saat membuat virtual environment dan install dependencies.
- Screenshot struktur folder di file explorer atau output `tree` command.
- Screenshot hasil `git init` dan `git status`.

---

### D.2. Membangun Backend dengan FastAPI

**Instruksi:**
1. Buat file `backend/main.py` dengan kode yang telah disediakan dalam modul.
2. Jalankan server FastAPI dengan perintah `uvicorn main:app --reload`.
3. Buka `http://127.0.0.1:8000/docs` dan uji endpoint menggunakan Swagger UI.

**Dokumentasi yang harus disertakan:**
- Screenshot kode `main.py` di editor (beberapa bagian penting).
- Screenshot terminal saat server berjalan.
- Screenshot Swagger UI dengan beberapa endpoint.
- Screenshot hasil uji coba `POST /api/tasks` dan `GET /api/tasks`.

---

### D.3. Membangun Frontend dengan PHP Native dan Bootstrap

**Instruksi:**
1. Buat file `frontend/index.php`, `api_config.js`, `script.js`, dan `style.css` sesuai modul.
2. Jalankan PHP built-in server dengan `php -S 127.0.0.1:8080`.
3. Buka `http://127.0.0.1:8080` dan uji fungsionalitas (tambah, edit, hapus tugas).

**Dokumentasi yang harus disertakan:**
- Screenshot kode `index.php` (bagian form dan tabel).
- Screenshot kode `script.js` (fungsi API calls).
- Screenshot terminal saat PHP server berjalan.
- Screenshot halaman TaskFlow di browser (tampilan awal, form isian, dan daftar tugas).

---

### D.4. Automation Testing untuk Backend API dengan Postman & Newman

**Instruksi:**
1. Buat Collection di Postman bernama `TaskFlow API Tests`.
2. Tambahkan Environment `TaskFlow Local` dengan variable `base_url`.
3. Buat request untuk setiap endpoint (POST, GET all, GET by ID, PUT, DELETE, negative test) lengkap dengan test script pada tab **Tests**.
4. Jalankan Collection Runner dan pastikan semua test passed.
5. Export Collection dan Environment ke folder `tests/postman/`.
6. Install Newman dan jalankan collection via command line, hasilkan HTML report.

**Dokumentasi yang harus disertakan:**
- Screenshot Collection di Postman beserta daftar request.
- Screenshot Environment variables.
- Screenshot test script untuk minimal 3 request (POST, GET all, DELETE).
- Screenshot hasil Collection Runner (semua test passed).
- Screenshot terminal saat menjalankan Newman.
- Screenshot HTML report yang terbuka di browser.

---

### D.5. Automation Testing untuk Frontend dengan Selenium WebDriver

**Instruksi:**
1. Buat file `tests/selenium/conftest.py` untuk konfigurasi pytest dan WebDriver fixture.
2. Buat file `tests/selenium/pages/task_page.py` dengan menerapkan Page Object Model (POM).
3. Buat file `tests/selenium/test_frontend.py` berisi minimal 10 test cases (sesuai modul).
4. Jalankan test dengan `pytest -v` dan hasilkan HTML report.

**Dokumentasi yang harus disertakan:**
- Screenshot kode `conftest.py`.
- Screenshot kode `task_page.py` (class TaskPage dengan locators dan methods).
- Screenshot kode `test_frontend.py` (beberapa test case penting).
- Screenshot terminal saat menjalankan pytest (semua test passed).
- Screenshot HTML report pytest.
- Screenshot folder `reports/screenshots` jika ada test yang gagal (untuk simulasi bisa dibuat gagal satu test).

---

### D.6. Performance Testing dengan Locust

**Instruksi:**
1. Buat file `tests/performance/locustfile.py` sesuai modul.
2. Jalankan Locust web UI dan lakukan load test dengan parameter: 50 users, spawn rate 5, selama 1 menit.
3. Jalankan Locust headless dan hasilkan HTML report.

**Dokumentasi yang harus disertakan:**
- Screenshot kode `locustfile.py`.
- Screenshot Locust web UI (halaman utama, grafik RPS, response times).
- Screenshot terminal saat menjalankan Locust headless.
- Screenshot HTML report performance.

---

### D.7. Integrasi dengan GitHub Actions (CI/CD)

**Instruksi:**
1. Buat repository GitHub baru dan push seluruh kode proyek.
2. Buat file `.github/workflows/test.yml` sesuai modul.
3. Push workflow ke GitHub dan amati hasil eksekusi di tab **Actions**.

**Dokumentasi yang harus disertakan:**
- Screenshot repository GitHub.
- Screenshot file workflow `test.yml`.
- Screenshot tab Actions di GitHub yang menunjukkan workflow berjalan (semua job hijau/sukses).
- Screenshot artifact report yang dihasilkan.

---

### D.8. Menjalankan Seluruh Test Suite

**Instruksi:**
1. Buat script `run_all_tests.py` di root proyek.
2. Jalankan script tersebut dan pastikan semua test berjalan otomatis.

**Dokumentasi yang harus disertakan:**
- Screenshot kode `run_all_tests.py`.
- Screenshot terminal saat script dijalankan (output summary).

---

## E. TANTANGAN / CHALLENGE

Mahasiswa wajib mengerjakan **minimal 3 (tiga)** tantangan dari daftar berikut. Pilihan tantangan bebas, namun disarankan memilih yang bervariasi (backend, frontend, performance).

### Challenge 1: Tambahkan Fitur Prioritas Tugas
1. Modifikasi backend: tambahkan field `priority` (enum: `low`, `medium`, `high`) pada model Task.
2. Update schema Pydantic dan endpoint API.
3. Modifikasi frontend: tambahkan dropdown prioritas di form dan tampilkan di tabel.
4. **Testing:** Tulis unit test untuk validasi priority, dan integration test untuk CRUD dengan priority. Tambahkan test case baru di Postman collection.
5. Dokumentasikan perubahan kode dan hasil test.

### Challenge 2: Implementasi Filter dan Pencarian
1. Tambahkan endpoint `GET /api/tasks?status={status}&search={keyword}`.
2. Modifikasi frontend: tambahkan input pencarian dan dropdown filter status.
3. **Testing:** Tulis test untuk memverifikasi filter dan pencarian bekerja (Postman & Selenium).
4. Dokumentasikan.

### Challenge 3: Data-Driven Testing dengan File JSON
1. Buat file JSON berisi minimal 10 data uji untuk pembuatan task (kombinasi valid dan invalid).
2. Tulis script Python yang membaca file JSON dan menjalankan test API secara otomatis menggunakan library `requests` dan `pytest`.
3. Dokumentasikan struktur JSON dan hasil eksekusi test.

### Challenge 4: Visual Regression Testing dengan Percy atau BackstopJS
1. Setup Percy atau BackstopJS pada proyek frontend.
2. Ambil screenshot baseline dan lakukan pengujian visual.
3. **Testing:** Buat test case untuk membandingkan tampilan halaman utama dan form tambah tugas.
4. Dokumentasikan setup dan hasil perbandingan visual.

### Challenge 5: Security Testing Sederhana
1. Tambahkan security headers (CORS restrictive, X-Content-Type-Options, dll) pada backend FastAPI.
2. Tulis test (Python) untuk memeriksa keberadaan dan kebenaran security headers.
3. Lakukan simulasi SQL injection pada endpoint API dan verifikasi bahwa sistem aman.
4. Dokumentasikan kode dan hasil pengujian.

### Challenge 6: Contract Testing dengan Pact
1. Gunakan Pact untuk membuat kontrak antara frontend (consumer) dan backend (provider).
2. Tulis consumer test (JavaScript) yang menghasilkan pact file.
3. Verifikasi provider (Python) terhadap pact file tersebut.
4. Dokumentasikan langkah-langkah dan hasil verifikasi.

### Challenge 7: Parallel Test Execution
1. Konfigurasikan `pytest-xdist` untuk menjalankan Selenium test secara paralel (misal 4 worker).
2. Bandingkan waktu eksekusi sebelum dan sesudah paralelisasi.
3. Dokumentasikan perbedaan waktu dan kendala yang dihadapi.

### Challenge 8: Mengganti Selenium dengan Playwright
1. Ganti implementasi UI testing dari Selenium ke Playwright (Python).
2. Tulis ulang test cases yang ada (minimal 5) menggunakan Playwright.
3. Bandingkan kemudahan penulisan, kecepatan eksekusi, dan fitur debugging.
4. Dokumentasikan perbandingan dalam bentuk tabel.

### Challenge 9: Membuat Dashboard Monitoring Test dengan Allure
1. Integrasikan Allure reporting ke dalam proyek.
2. Tambahkan anotasi `@allure.feature`, `@allure.story`, dll pada test cases.
3. Generate Allure report dan screenshot dashboard-nya.
4. Dokumentasikan langkah integrasi dan tampilan report.

### Challenge 10: Chaos Engineering Sederhana
1. Gunakan Toxiproxy atau tools lain untuk mensimulasikan latency pada backend API.
2. Tulis test yang mengukur bagaimana frontend/Selenium merespons ketika API lambat (timeout).
3. Dokumentasikan skenario chaos dan hasil pengujian.

---

## F. FORMAT LAPORAN

Laporan praktikum disusun dalam format **Microsoft Word (.doc/.docx)** dengan struktur sebagai berikut:

### Halaman Judul
- Judul Praktikum: "Laporan Praktikum Automation Testing pada Pengembangan Perangkat Lunak"
- Nama Mahasiswa
- NIM
- Kelas
- Tanggal Pengumpulan
- Logo Institusi (opsional)

### Daftar Isi
*(Otomatis menggunakan fitur Word)*

### BAB I: PENDAHULUAN
1.1. Latar Belakang  
1.2. Tujuan Praktikum  
1.3. Manfaat Praktikum  

### BAB II: DASAR TEORI
*(Resume dari materi yang telah dipelajari, minimal 2 halaman, ditulis dengan bahasa sendiri.)*

### BAB III: METODOLOGI PRAKTIKUM
3.1. Alat dan Bahan  
3.2. Arsitektur Aplikasi (diagram TaskFlow)  
3.3. Tahapan Praktikum (flowchart atau deskripsi singkat)  

### BAB IV: HASIL DAN PEMBAHASAN
4.1. Setup Lingkungan dan Proyek  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot dan penjelasan singkat)*  
4.2. Implementasi Backend FastAPI  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot kode utama, Swagger UI, hasil uji coba)*  
4.3. Implementasi Frontend  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot kode dan tampilan browser)*  
4.4. Pengujian API dengan Postman & Newman  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot Collection, Environment, test script, hasil runner, Newman report)*  
4.5. Pengujian UI dengan Selenium WebDriver  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot POM, test cases, hasil pytest, HTML report)*  
4.6. Performance Testing dengan Locust  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot Locust UI, grafik, HTML report)*  
4.7. Integrasi CI/CD dengan GitHub Actions  
&nbsp;&nbsp;&nbsp;&nbsp;*(Screenshot workflow, hasil Actions, artifact)*  
4.8. Pembahasan  
&nbsp;&nbsp;&nbsp;&nbsp;*(Analisis hasil pengujian, temuan bug, evaluasi performa, dll.)*  

### BAB V: TANTANGAN / CHALLENGE
5.1. Challenge 1: [Judul Challenge]  
&nbsp;&nbsp;&nbsp;&nbsp;*(Penjelasan singkat challenge, langkah pengerjaan, screenshot kode/hasil)*  
5.2. Challenge 2: [Judul Challenge]  
&nbsp;&nbsp;&nbsp;&nbsp;*(Penjelasan singkat challenge, langkah pengerjaan, screenshot kode/hasil)*  
5.3. Challenge 3: [Judul Challenge]  
&nbsp;&nbsp;&nbsp;&nbsp;*(Penjelasan singkat challenge, langkah pengerjaan, screenshot kode/hasil)*  

### BAB VI: KESIMPULAN DAN SARAN
6.1. Kesimpulan  
6.2. Saran  

### DAFTAR PUSTAKA

### LAMPIRAN
- Lampiran 1: Link Repository GitHub
- Lampiran 2: Kode Program Lengkap (opsional, atau cukup link GitHub)

---

## G. KETENTUAN PENGUMPULAN

1. **Laporan** dikumpulkan dalam format **PDF** (hasil konversi dari Word) dengan nama file:  
   `Laporan_Praktikum_Automation_Testing_NIM_NamaLengkap.pdf`

2. **Kode Program** diunggah ke **GitHub** dengan repository **public**.  
   - Nama repository: `taskflow-automation-testing`  
   - Sertakan file `README.md` yang menjelaskan isi repository dan cara menjalankan proyek.  
   - Link repository dicantumkan di laporan.

3. **Batas Waktu Pengumpulan:** Hari ini pukul **16.00 WIB**.  
   Keterlambatan akan mengurangi nilai.

4. **Pengumpulan** dilakukan melalui platform yang ditentukan (LMS/Google Classroom/email).

5. **Tugas bersifat individu.** Plagiarisme akan dikenakan sanksi sesuai aturan akademik.

---

## H. RUBRIK PENILAIAN

| No | Komponen Penilaian | Bobot |
|----|--------------------|-------|
| 1 | Kelengkapan dan Kualitas Resume Teori | 15% |
| 2 | Kebenaran Implementasi Backend & Frontend | 15% |
| 3 | Kelengkapan Automation Test (API & UI) | 20% |
| 4 | Kualitas Kode (struktur, POM, readability) | 10% |
| 5 | Hasil Performance Testing & CI/CD | 10% |
| 6 | Penyelesaian Tantangan (min. 3) | 20% |
| 7 | Kualitas Laporan (sistematika, screenshot, analisis) | 10% |
| **Total** | | **100%** |

---

## I. TEMPLATE SEDERHANA UNTUK LAPORAN

### Halaman Judul

```
                    LAPORAN PRAKTIKUM
              AUTOMATION TESTING PADA PENGEMBANGAN
                      PERANGKAT LUNAK

            (Studi Kasus: Aplikasi TaskFlow)


                         Disusun oleh:
                        [Nama Mahasiswa]
                             [NIM]
                            [Kelas]


                         [Logo POLMAN]


                    PROGRAM STUDI TRIN
               POLITEKNIK MANUFAKTUR BANDUNG
                           2026
```

### Format Penulisan
- Font: Times New Roman 12pt (atau Arial 11pt)
- Spasi: 1.5
- Margin: Normal (2.5 cm kiri, 3 cm kanan? sesuaikan standar kampus)
- Kode program menggunakan font monospace (Courier New 10pt) dengan highlight atau shading.

---

## J. CONTOH TABEL PERBANDINGAN UNTUK RESUME

| **Aspek** | **Manual Testing** | **Automated Testing** |
|-----------|-------------------|----------------------|
| Keandalan | ... | ... |
| Kecepatan | ... | ... |
| ... | ... | ... |

---

## K. TIPS PENGERJAAN

1. **Kerjakan bertahap.** Mulai dari setup, backend, frontend, lalu testing.
2. **Commit secara berkala** ke Git untuk menghindari kehilangan data.
3. **Gunakan terminal/command line yang sama** agar path konsisten.
4. **Jika mengalami error**, catat error dan solusinya untuk dimasukkan dalam pembahasan laporan.
5. **Manfaatkan fitur screenshot area** (bukan full screen) agar gambar fokus.
6. **Untuk laporan**, gunakan heading style Word agar daftar isi otomatis.

---

**Selamat mengerjakan praktikum! Jika ada pertanyaan, segera hubungi asisten praktikum atau dosen pengampu.**