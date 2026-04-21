Berikut adalah file `README.md` yang lengkap untuk studi kasus API Testing dengan Postman. Simpan file ini di folder proyek yang sama dengan `main.py`.

```markdown
# 📚 Sistem Perpustakaan API - Studi Kasus API Testing dengan Postman

Proyek ini adalah API Sistem Perpustakaan yang dibangun dengan **FastAPI (Python)**. API memiliki **23 endpoint** lengkap dengan autentikasi JWT, manajemen user, buku, peminjaman, pengembalian, denda, dan laporan.

Tujuan proyek ini adalah sebagai bahan praktikum **Software Quality Assurance (SQA)** untuk menguji API menggunakan **Postman**. Data dummy sudah disediakan secara otomatis (minimal 20 record per tabel) sehingga mahasiswa dapat langsung melakukan pengujian tanpa perlu memasukkan data manual.

---

## 🚀 Fitur Utama

- ✅ Autentikasi JWT (Login & Register)
- ✅ Role-based access control (Anggota & Petugas)
- ✅ CRUD User, Buku, Peminjaman, Denda
- ✅ Proses peminjaman, perpanjangan, pembatalan, dan pengembalian
- ✅ Perhitungan denda otomatis (Rp 1.000/hari keterlambatan)
- ✅ Laporan peminjaman dan denda (filter by user, tanggal, status bayar)
- ✅ Database SQLite dengan **seeder data dummy otomatis**
- ✅ Dokumentasi interaktif Swagger UI (`/docs`)

---

## 🛠️ Teknologi yang Digunakan

| Komponen          | Teknologi                          |
|-------------------|------------------------------------|
| Backend Framework | FastAPI (Python 3.9+)              |
| Database          | SQLite (dengan SQLAlchemy ORM)     |
| Autentikasi       | JWT (JSON Web Token)               |
| Hashing Password  | Bcrypt                             |
| Server            | Uvicorn                            |
| Testing Tool      | Postman                            |

---

## 📦 Persiapan Lingkungan

### Prasyarat

- **Python 3.9 atau lebih tinggi**  
  Unduh di [python.org](https://www.python.org/downloads/)
- **Postman** (versi terbaru)  
  Unduh di [postman.com](https://www.postman.com/downloads/)
- **Git** (opsional, untuk clone repositori)

### 1. Clone atau Download Proyek

```bash
git clone <url-repositori-anda>
cd perpustakaan-api
```

Atau cukup download ZIP dan ekstrak ke folder pilihan Anda.

### 2. Buat Virtual Environment

Buka terminal (Command Prompt / PowerShell / Terminal) di folder proyek, lalu jalankan:

**Windows:**
```bash
python -m venv venv
venv\Scripts\activate
```

**Linux / macOS:**
```bash
python3 -m venv venv
source venv/bin/activate
```

Pastikan terminal menampilkan `(venv)` di awal baris.

### 3. Install Dependensi

```bash
pip install fastapi uvicorn sqlalchemy "python-jose[cryptography]" "passlib[bcrypt]" python-multipart
```

> **Catatan untuk pengguna Windows:**  
> Jika instalasi `bcrypt` gagal dengan error `Microsoft Visual C++ 14.0 is required`, gunakan perintah alternatif:
> ```bash
> pip install bcrypt==3.2.0
> ```

### 4. Jalankan Server FastAPI

```bash
uvicorn main:app --reload
```

Tunggu hingga muncul pesan:

```
INFO:     Uvicorn running on http://127.0.0.1:8000
🌱 Menjalankan seeder data dummy...
✅ 22 user berhasil ditambahkan.
✅ 24 buku berhasil ditambahkan.
✅ 30 peminjaman berhasil ditambahkan.
✅ 20 denda berhasil ditambahkan.
🎉 Seeder selesai! Data dummy siap digunakan.
```

Server akan berjalan di `http://localhost:8000`.

> **Jika port 8000 sudah terpakai**, jalankan dengan port lain:
> ```bash
> uvicorn main:app --reload --port 8001
> ```
> Kemudian sesuaikan `base_url` di Postman Environment.

### 5. Buka Dokumentasi API

Kunjungi [http://localhost:8000/docs](http://localhost:8000/docs) untuk melihat dokumentasi interaktif Swagger UI. Anda dapat mencoba semua endpoint langsung dari sini.

---

## 🗃️ Data Dummy yang Dihasilkan

Seeder akan otomatis membuat data berikut saat server pertama kali dijalankan:

| Tabel         | Jumlah | Keterangan                                                                 |
|---------------|--------|-----------------------------------------------------------------------------|
| **users**     | 22     | 2 petugas + 20 anggota. Username `petugas1`/`petugas2` dengan password `admin123`. Anggota: `anggota1` s.d. `anggota20` dengan password `rahasia`. |
| **buku**      | 24     | Judul bervariasi, stok 1-5, tahun terbit 2015-2023.                         |
| **peminjaman**| 30     | Status bervariasi: `dipinjam`, `kembali`, `terlambat`.                       |
| **denda**     | 20+    | Denda Rp 1.000/hari keterlambatan, sebagian sudah dibayar.                   |

Data ini cukup untuk menguji semua skenario pengujian tanpa perlu input manual.

---

## 🧪 Pengujian API dengan Postman

### 1. Import Environment dan Collection

Proyek ini menyediakan file pendukung Postman:

- `Perpustakaan-Environment.postman_environment.json` – Environment variables.
- `Perpustakaan-API.postman_collection.json` – Collection berisi 25 test case.

**Cara import:**
1. Buka Postman.
2. Klik tombol **Import** (pojok kiri atas).
3. Pilih kedua file `.json` tersebut.
4. Setelah import, pilih environment **Perpustakaan API Environment** dari dropdown di pojok kanan atas.

### 2. Struktur Environment Variables

| Variable          | Initial Value             | Keterangan                              |
|-------------------|---------------------------|-----------------------------------------|
| `base_url`        | http://localhost:8000     | URL API                                 |
| `token_petugas`   | (kosong)                  | Akan terisi otomatis setelah login      |
| `token_anggota`   | (kosong)                  | Akan terisi otomatis setelah login      |
| `user_id_anggota` | (kosong)                  | ID user anggota (diisi manual/dari test)|
| `buku_id`         | (kosong)                  | ID buku (diisi dari test create buku)   |
| `pinjam_id`       | (kosong)                  | ID peminjaman (diisi dari test pinjam)  |
| `denda_id`        | (kosong)                  | ID denda (diisi dari test GET denda)    |

### 3. Menjalankan Collection Runner

1. Klik ikon **▶️** (Run) di samping nama collection **Perpustakaan API**.
2. Pilih **Run**.
3. Atur **Iterations = 1**, **Delay = 500 ms**.
4. Klik **Run Perpustakaan API**.
5. Amati hasil: semua 25 test case seharusnya **PASS**.

> **Catatan:** Beberapa test case (misal perpanjangan dan pengembalian) bergantung pada data yang dibuat di test sebelumnya. Pastikan test case dijalankan secara berurutan sesuai nomor.

### 4. Daftar Test Case (25 Skenario)

| No | Nama Test Case                     | Endpoint & Method          | Expected Result                                 |
|----|------------------------------------|----------------------------|-------------------------------------------------|
| 1  | Register Anggota Baru              | POST /auth/register        | 201 Created                                     |
| 2  | Register Gagal (Duplikat Username) | POST /auth/register        | 400 Bad Request                                 |
| 3  | Login Petugas                      | POST /auth/login           | 200 OK, dapat token                             |
| 4  | Login Anggota                      | POST /auth/login           | 200 OK, dapat token                             |
| 5  | Get All Users (Petugas)            | GET /users                 | 200 OK, list user                               |
| 6  | Get All Users (Anggota) - Gagal    | GET /users                 | 403 Forbidden                                   |
| 7  | Get User Sendiri (Anggota)         | GET /users/{id}            | 200 OK                                          |
| 8  | Update Profil Anggota              | PUT /users/{id}            | 200 OK                                          |
| 9  | Create Buku (Petugas)              | POST /buku                 | 201 Created                                     |
| 10 | Create Buku (Anggota) - Gagal      | POST /buku                 | 403 Forbidden                                   |
| 11 | Get All Buku (Anggota)             | GET /buku                  | 200 OK, list buku                               |
| 12 | Update Buku (Petugas)              | PUT /buku/{id}             | 200 OK                                          |
| 13 | Delete Buku (Petugas)              | DELETE /buku/{id}          | 200 OK                                          |
| 14 | Pinjam Buku (Anggota)              | POST /peminjaman           | 201 Created                                     |
| 15 | Pinjam Buku Gagal (Stok 0)         | POST /peminjaman           | 400 Bad Request                                 |
| 16 | Get Peminjaman Milik Anggota       | GET /peminjaman            | 200 OK                                          |
| 17 | Perpanjang Peminjaman              | PUT /peminjaman/{id}       | 200 OK                                          |
| 18 | Batalkan Peminjaman                | DELETE /peminjaman/{id}    | 200 OK                                          |
| 19 | Pengembalian Tepat Waktu           | POST /pengembalian         | 200 OK, denda 0                                 |
| 20 | Pengembalian Terlambat             | POST /pengembalian         | 200 OK, denda > 0                               |
| 21 | Get Denda (Anggota)                | GET /denda                 | 200 OK                                          |
| 22 | Bayar Denda                        | PUT /denda/{id}            | 200 OK                                          |
| 23 | Laporan Peminjaman (Petugas)       | GET /laporan/peminjaman    | 200 OK                                          |
| 24 | Laporan Denda (Petugas)            | GET /laporan/denda         | 200 OK                                          |
| 25 | Delete User (Petugas)              | DELETE /users/{id}         | 200 OK                                          |

Detail lengkap setiap test case (termasuk assertion) tersedia di dalam collection Postman.

---

## 🔧 Troubleshooting Umum

### 1. `ModuleNotFoundError: No module named '...'`
- Pastikan virtual environment sudah aktif (`(venv)` terlihat di terminal).
- Jalankan ulang `pip install -r requirements.txt` (jika ada) atau perintah instalasi lengkap di atas.

### 2. `Error loading ASGI app. Could not import module "main".`
- Pastikan file `main.py` berada di folder yang sama dengan terminal.
- Periksa apakah nama file benar `main.py` (bukan `main.py.txt`).
- Jalankan `python -c "import main"` untuk melihat error spesifik.

### 3. `Address already in use` (Port 8000 sudah terpakai)
- Ganti port saat menjalankan server: `uvicorn main:app --reload --port 8001`
- Sesuaikan `base_url` di Postman Environment menjadi `http://localhost:8001`

### 4. Error terkait `bcrypt` di Windows
- Gunakan `pip install bcrypt==3.2.0`
- Atau install **Microsoft C++ Build Tools** dari [sini](https://visualstudio.microsoft.com/visual-cpp-build-tools/)

### 5. Data dummy tidak muncul
- Hapus file `perpustakaan.db` (jika ada) lalu restart server. Seeder akan membuat ulang data.

### 6. Test case di Postman gagal karena token expired
- Token JWT berlaku 30 menit. Jalankan ulang test case **Login** untuk mendapatkan token baru.

---

## 📁 Struktur Proyek

```
pertemuan6/
│
├── main.py                     # Source code FastAPI (dengan seeder)
├── perpustakaan.db             # Database SQLite (akan dibuat otomatis)
├── README.md                   # Dokumentasi ini
├── Perpustakaan-Environment.postman_environment.json  # Environment Postman
└── Perpustakaan-API.postman_collection.json           # Collection Postman
```

---

## 📝 Lisensi

Proyek ini dibuat untuk keperluan edukasi (Mata Kuliah Software Quality Assurance). Bebas digunakan dan dimodifikasi untuk pembelajaran.

---

## 🤝 Kontribusi

Jika menemukan bug atau ingin menambahkan fitur, silakan buka *issue* atau kirim *pull request*.

---

**Selamat belajar API Testing dengan Postman!** 🚀  
Jika ada pertanyaan, tanyakan langsung kepada dosen pengampu atau asisten praktikum.
```
