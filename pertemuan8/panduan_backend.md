# Panduan Instalasi dan Konfigurasi Backend - Untuk Pemula

Dokumen ini akan memandu Anda step-by-step dalam menginstal dan mengkonfigurasi backend Sistem Penjualan dari awal.

## 📋 Daftar Isi
1. [Persyaratan Sistem](#persyaratan-sistem)
2. [Pemahaman Singkat Teknologi](#pemahaman-singkat-teknologi)
3. [Step 1: Install Python](#step-1-install-python)
4. [Step 2: Install dan Setup MySQL](#step-2-install-dan-setup-mysql)
5. [Step 3: Persiapan Folder Proyek](#step-3-persiapan-folder-proyek)
6. [Step 4: Setup Virtual Environment](#step-4-setup-virtual-environment)
7. [Step 5: Instalasi Dependencies](#step-5-instalasi-dependencies)
8. [Step 6: Konfigurasi Database](#step-6-konfigurasi-database)
9. [Step 7: Menjalankan Backend](#step-7-menjalankan-backend)
10. [Step 8: Testing dan Verifikasi](#step-8-testing-dan-verifikasi)
11. [Troubleshooting](#troubleshooting)

---

## 📌 Persyaratan Sistem

Sebelum memulai, pastikan komputer Anda memiliki:

- **OS**: Windows, macOS, atau Linux
- **RAM**: Minimal 4GB (8GB disarankan)
- **Disk Space**: Minimal 2GB untuk instalasi
- **Internet**: Untuk download tools dan packages

---

## 💡 Pemahaman Singkat Teknologi

Backend aplikasi ini dibangun menggunakan:

- **FastAPI**: Framework Python modern untuk membuat REST API
- **SQLAlchemy**: ORM (Object-Relational Mapping) untuk mengelola database
- **MySQL**: Database relasional untuk menyimpan data
- **Uvicorn**: Server web untuk menjalankan FastAPI
- **JWT**: Sistem autentikasi untuk keamanan API

Tidak perlu memahami semua detail teknologi ini, tapi ini akan membantu Anda mengerti step-step berikutnya.

---

## Step 1: Install Python

### Untuk Windows:

1. **Download Python**
   - Buka browser, kunjungi: [python.org](https://www.python.org/downloads/)
   - Klik tombol "Download Python 3.x.x" (pilih versi 3.10 atau terbaru)

2. **Jalankan Installer**
   - Buka file `.exe` yang sudah diunduh
   - **PENTING**: Centang ✓ "Add Python to PATH" di bagian bawah
   - Klik "Install Now"
   - Tunggu proses selesai

3. **Verifikasi Instalasi**
   - Buka Command Prompt (tekan `Win + R`, ketik `cmd`, tekan Enter)
   - Ketik perintah berikut:
   ```bash
   python --version
   ```
   - Anda seharusnya melihat versi Python yang terinstall (contoh: `Python 3.11.0`)

> **Jika melihat error**: Restart komputer, lalu coba lagi

### Untuk macOS:

```bash
# Install Homebrew terlebih dahulu jika belum punya
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Python
brew install python3

# Verifikasi
python3 --version
```

### Untuk Linux (Ubuntu/Debian):

```bash
# Update package manager
sudo apt update

# Install Python
sudo apt install python3 python3-pip

# Verifikasi
python3 --version
```

---

## Step 2: Install dan Setup MySQL

### Untuk Windows:

1. **Download MySQL Community Server**
   - Kunjungi: [mysql.com/downloads](https://www.mysql.com/downloads/mysql/)
   - Pilih "MySQL Community Server"
   - Download versi terbaru (8.x atau 5.7)

2. **Jalankan MySQL Installer**
   - Buka file `.msi` yang sudah diunduh
   - Pilih "Setup Type" → "Developer Default" atau "Server Only"
   - Klik "Next" hingga selesai
   - Pada konfigurasi, gunakan default settings

3. **Start MySQL Service**
   - Tekan `Win + R`, ketik `services.msc`
   - Cari "MySQL80" (atau versi Anda)
   - Pastikan status adalah "Running"

4. **Test Koneksi**
   - Buka Command Prompt
   - Ketik:
   ```bash
   mysql -u root
   ```
   - Jika muncul prompt `mysql>`, MySQL sudah berhasil diinstall

### Untuk macOS:

```bash
# Install MySQL menggunakan Homebrew
brew install mysql

# Start MySQL service
brew services start mysql

# Set root password
mysql -u root "-psomething"
```

### Untuk Linux (Ubuntu/Debian):

```bash
# Install MySQL Server
sudo apt install mysql-server

# Jalankan MySQL Secure Installation
sudo mysql_secure_installation

# Verifikasi koneksi
mysql -u root -p
```

---

## Step 3: Persiapan Folder Proyek

1. **Buka File Explorer** (atau Terminal)

2. **Navigasi ke folder proyek**
   - Arahkan ke folder: `pertemuan8/backend`
   - Contoh path: `E:\__POLMAN\2026-2027\Ganjil\SQA\Materi Ajar\Minggu 2\projek\pertemuan8\backend`

3. **Pastikan file berikut sudah ada di folder `backend/`**:
   - `main.py`
   - `database.py`
   - `models.py`
   - `schemas.py`
   - `auth.py`
   - `crud.py`
   - `requirements.txt`
   - Folder `routers/` dengan file-file di dalamnya

---

## Step 4: Setup Virtual Environment

Virtual Environment adalah folder terpisah yang berisi instalasi Python spesifik proyek. Ini memastikan dependencies tidak conflict dengan proyek lain.

### Untuk Windows:

1. **Buka Command Prompt di folder backend**
   - Buka File Explorer
   - Di address bar, ketik: `cmd` dan tekan Enter
   - Anda akan berada di folder tersebut dalam Command Prompt

2. **Buat Virtual Environment**
   ```bash
   python -m venv venv
   ```
   - Tunggu proses selesai (biasanya 1-2 menit)
   - Anda akan melihat folder baru bernama `venv` muncul

3. **Aktivasi Virtual Environment**
   ```bash
   venv\Scripts\activate
   ```
   - Jika berhasil, Anda akan melihat `(venv)` di awal command prompt
   - Contoh: `(venv) C:\path\to\backend>`

### Untuk macOS/Linux:

```bash
# Buat Virtual Environment
python3 -m venv venv

# Aktivasi Virtual Environment
source venv/bin/activate

# Anda akan melihat (venv) di awal prompt
```

> **Catatan**: Setelah ini, pastikan selalu virtual environment aktif sebelum menjalankan perintah Python. Jika virtual environment tidak aktif, kembali ke langkah 3 di atas.

---

## Step 5: Instalasi Dependencies

Dependencies adalah library/paket yang diperlukan oleh Backend API. Semua dependencies tercatat di file `requirements.txt`.

### Langkah-langkah:

1. **Pastikan virtual environment aktif**
   - Di Command Prompt, Anda harus melihat `(venv)` di awal

2. **Install semua dependencies**
   ```bash
   pip install -r requirements.txt
   ```
   - Tunggu proses hingga selesai (bisa memakan waktu 2-5 menit)
   - Anda akan melihat banyak output instalasi

3. **Verifikasi instalasi**
   ```bash
   pip list
   ```
   - Anda akan melihat daftar semua package yang terinstall
   - Pastikan ada: `fastapi`, `uvicorn`, `sqlalchemy`, `pymysql`

> **Jika ada error**: Pastikan Anda memiliki koneksi internet yang stabil, dan virtual environment sudah aktif

---

## Step 6: Konfigurasi Database

### 6.1 Buat Database di MySQL

1. **Buka MySQL di Command Prompt**
   ```bash
   mysql -u root -p
   ```
   - Tekan Enter ketika diminta password (jika tidak ada password)

2. **Copy & Paste script database**
   - Buka file `database.sql` yang ada di root proyek
   - Copy seluruh isi file
   - Paste di MySQL prompt dan tekan Enter

3. **Verifikasi database sudah dibuat**
   ```bash
   SHOW DATABASES;
   USE db_penjualan;
   SHOW TABLES;
   ```
   - Anda seharusnya melihat 4 tabel: `kategori`, `produk`, `pelanggan`, `transaksi_header`, `transaksi_detail`

4. **Keluar dari MySQL**
   ```bash
   EXIT;
   ```

### 6.2 Konfigurasi File `database.py`

File `database.py` berisi konfigurasi koneksi ke database. Jika Anda menggunakan setup default, tidak perlu diubah. Tapi jika berbeda, ikuti langkah berikut:

1. **Buka file `backend/database.py`** menggunakan text editor (notepad, VS Code, dll)

2. **Cek baris DATABASE_URL**:
   ```python
   DATABASE_URL = "mysql+pymysql://root:@localhost:3306/db_penjualan"
   ```

3. **Jika ada yang perlu disesuaikan**:
   - `root` = username MySQL Anda
   - `:` = pemisah username dan password
   - (kosong) = password (jika Anda punya password, masukkan di sini)
   - `localhost` = host MySQL (biasanya localhost)
   - `3306` = port MySQL default
   - `db_penjualan` = nama database

4. **Contoh jika Anda punya password MySQL `password123`**:
   ```python
   DATABASE_URL = "mysql+pymysql://root:password123@localhost:3306/db_penjualan"
   ```

5. **Simpan file** (Ctrl + S)

---

## Step 7: Menjalankan Backend

### Jalankan Aplikasi FastAPI:

1. **Pastikan di folder `backend` dan virtual environment aktif**
   - Anda seharusnya melihat `(venv) C:\...\backend>`

2. **Jalankan aplikasi**
   ```bash
   uvicorn main:app --reload
   ```

3. **Tunggu hingga melihat output seperti ini**:
   ```
   INFO:     Uvicorn running on http://127.0.0.1:8000 (Press CTRL+C to quit)
   INFO:     Started server process [1234]
   INFO:     Waiting for application startup.
   INFO:     Application startup complete
   ```

4. **Aplikasi sudah berjalan!**
   - Server berjalan di: `http://localhost:8000`
   - Dokumentasi API (Swagger UI) di: `http://localhost:8000/docs`

> **Tips**: 
> - Jangan tutup Command Prompt ini, karena server akan berhenti
> - Jika ingin stop server, tekan `CTRL + C`
> - Jika ada perubahan code, server otomatis restart (karena `--reload`)

---

## Step 8: Testing dan Verifikasi

### Cara 1: Menggunakan Swagger UI (Rekomendasi untuk Pemula)

1. **Buka browser**
   - Kunjungi: `http://localhost:8000/docs`

2. **Anda akan melihat halaman Swagger UI dengan semua endpoint API**

3. **Test endpoint root**
   - Klik pada endpoint `GET /`
   - Klik tombol "Try it out"
   - Klik tombol "Execute"
   - Anda seharusnya melihat response:
   ```json
   {
     "message": "API Sistem Penjualan Sederhana - Protected by JWT"
   }
   ```

### Cara 2: Menggunakan Postman (Alternatif)

1. **Download Postman** dari [postman.com](https://www.postman.com/downloads/)

2. **Buka Postman dan buat request baru**:
   - Method: `GET`
   - URL: `http://localhost:8000/`
   - Klik "Send"

3. **Verifikasi response** seperti di atas

### Cara 3: Menggunakan curl (Di Command Prompt)

1. **Buka Command Prompt baru** (jangan tutup yang menjalankan server)

2. **Jalankan perintah**:
   ```bash
   curl http://localhost:8000/
   ```

3. **Anda seharusnya melihat response JSON**

### Test Endpoint Kategori (Contoh):

1. **Di Swagger UI, cari endpoint `GET /api/v1/kategori`**

2. **Klik dan jalankan**

3. **Anda seharusnya melihat daftar kategori yang sudah di-insert ke database**

---

## 📝 Struktur File Backend Penjelasan

Mari memahami setiap file:

```
backend/
├── main.py              # Entry point aplikasi, definisi FastAPI app
├── database.py          # Konfigurasi koneksi database
├── models.py            # Definisi model/struktur tabel database
├── schemas.py           # Definisi struktur request/response data
├── auth.py              # Logika autentikasi JWT
├── crud.py              # Create, Read, Update, Delete operations
├── requirements.txt     # Daftar semua dependencies yang diperlukan
├── routers/
│   ├── auth.py         # Endpoint autentikasi
│   ├── kategori.py     # Endpoint CRUD kategori
│   ├── produk.py       # Endpoint CRUD produk
│   ├── pelanggan.py    # Endpoint CRUD pelanggan
│   └── transaksi.py    # Endpoint transaksi
└── __pycache__/         # Cache Python (auto-generated)
```

---

## 🔧 Troubleshooting

### Problem 1: "python command not found"

**Solusi**:
- Python belum di-install atau tidak di PATH
- Restart komputer setelah install Python
- Verifikasi: buka cmd baru dan ketik `python --version`

### Problem 2: "No module named 'fastapi'"

**Solusi**:
- Virtual environment tidak aktif
- Jalankan: `venv\Scripts\activate` (Windows) atau `source venv/bin/activate` (Mac/Linux)
- Install ulang: `pip install -r requirements.txt`

### Problem 3: "Can't connect to MySQL server"

**Solusi**:
- MySQL service belum running
  - Windows: Buka `services.msc` dan start MySQL80
  - Mac: Ketik `brew services start mysql`
  - Linux: Ketik `sudo service mysql start`
- Cek username/password di `database.py`
- Pastikan database `db_penjualan` sudah dibuat

### Problem 4: "Port 8000 already in use"

**Solusi 1 - Gunakan port lain**:
```bash
uvicorn main:app --reload --port 8001
```

**Solusi 2 - Matikan proses yang memakai port**:
- Windows: Di Task Manager, cari proses Python dan kill
- Mac/Linux: Ketik `lsof -i :8000` kemudian `kill -9 <PID>`

### Problem 5: Database sudah ada tapi error "table already exists"

**Solusi**:
```bash
# Di MySQL prompt, drop database dan buat ulang
DROP DATABASE db_penjualan;
# Kemudian jalankan ulang script database.sql
```

### Problem 6: Error "ModuleNotFoundError" saat jalankan app

**Solusi**:
- Pastikan di folder `backend`
- Pastikan virtual environment aktif
- Cek apakah ada file `__init__.py` di folder `routers/`
- Jika belum ada, buat file kosong bernama `__init__.py` di folder `routers/`

---

## ✅ Checklist Setup Berhasil

Tandai semua item berikut jika sudah selesai:

- [ ] Python terinstall dan bisa dijalankan di command line
- [ ] MySQL terinstall dan sudah running
- [ ] Database `db_penjualan` berhasil dibuat
- [ ] Folder `backend` sudah ada
- [ ] Virtual environment sudah dibuat di `backend/venv`
- [ ] Virtual environment sudah diaktifkan (melihat `(venv)`)
- [ ] Dependencies sudah diinstall (`pip install -r requirements.txt`)
- [ ] File `database.py` sudah dikonfigurasi dengan benar
- [ ] Server FastAPI sudah running (melihat pesan "Application startup complete")
- [ ] Bisa akses `http://localhost:8000/docs` di browser
- [ ] Endpoint root (`GET /`) mengembalikan response yang benar

Jika semua checklist sudah dicentang, **selamat! Backend Anda sudah siap digunakan!** 🎉

---

## 🚀 Langkah Berikutnya

Setelah backend setup selesai, Anda bisa:

1. **Eksplorasi API di Swagger UI** - coba berbagai endpoint
2. **Setup Frontend** - untuk berkomunikasi dengan backend
3. **Learn FastAPI** - untuk memahami dan mengembangkan API lebih lanjut
4. **Setup Production** - untuk deploy aplikasi ke server

---

## 📚 Referensi Berguna

- FastAPI Documentation: [fastapi.tiangolo.com](https://fastapi.tiangolo.com/)
- SQLAlchemy Documentation: [sqlalchemy.org](https://www.sqlalchemy.org/)
- MySQL Documentation: [mysql.com/doc](https://www.mysql.com/doc/)
- Python Documentation: [python.org/doc](https://www.python.org/doc/)

---

## 💬 Catatan Penting

- Jangan share file `database.py` dengan password yang sebenarnya
- Untuk production, gunakan environment variables untuk konfigurasi sensitif
- Pastikan selalu backup database sebelum melakukan perubahan besar
- Gunakan virtual environment untuk setiap proyek Python

---

**Dibuat untuk pemula. Jika ada pertanyaan, coba bagian Troubleshooting atau konsultasikan dengan instruktur.**

Selamat belajar! 📚
