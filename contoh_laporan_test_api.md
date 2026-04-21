# LAPORAN PENGUJIAN API  
## Sistem Manajemen Tugas (TaskFlow API)  
**Versi 1.0.0**

---

| **Nomor Dokumen** | TFL-API-TR-001 |
|-------------------|----------------|
| **Tanggal**       | 21 April 2026  |
| **Penulis**       | Tim QA         |
| **Status**        | Final          |

---

## 1. Ringkasan Eksekutif

Pengujian API untuk sistem **TaskFlow** telah dilaksanakan pada tanggal 20–21 April 2026. Fokus pengujian adalah memvalidasi fungsionalitas 18 endpoint utama yang mencakup autentikasi, manajemen proyek, tugas, komentar, dan laporan.

**Hasil Utama:**
- Total test case: **28** (22 positif, 6 negatif)
- **25** test case berhasil (lulus)
- **3** test case gagal
- **Tingkat keberhasilan: 89,3%**

**Temuan Kritis:**
- **1 defect kritis** terkait otorisasi akses komentar antar pengguna.
- **2 defect minor** terkait validasi input dan format respons.

**Kesimpulan:** API belum memenuhi kriteria *production-ready* karena adanya celah keamanan pada endpoint komentar. Rilis ditunda hingga perbaikan selesai.

---

## 2. Pendahuluan

### 2.1 Tujuan Pengujian
- Memverifikasi bahwa seluruh endpoint API berfungsi sesuai spesifikasi.
- Mengidentifikasi cacat fungsional, keamanan, dan performa dasar.
- Memastikan mekanisme autentikasi JWT dan kontrol akses berbasis peran berjalan benar.

### 2.2 Lingkup Pengujian
| **Termasuk**                                                                 | **Tidak Termasuk**                                                              |
|-------------------------------------------------------------------------------|---------------------------------------------------------------------------------|
| Seluruh endpoint REST (18 endpoint)                                           | Pengujian performa (load/stress testing)                                        |
| Autentikasi JWT dengan peran `user` dan `admin`                               | Pengujian keamanan tingkat lanjut (penetration testing)                         |
| Validasi input dan skenario positif/negatif                                   | Pengujian antarmuka pengguna (UI)                                               |
| Integrasi dengan database PostgreSQL                                          | Pengujian pada environment production (hanya staging)                           |

### 2.3 Referensi
| Dokumen                 | Versi | Tanggal       |
|-------------------------|-------|---------------|
| Spesifikasi API TaskFlow| 2.1   | 15 April 2026 |
| Postman Collection      | 1.0   | 19 April 2026 |

---

## 3. Lingkungan Pengujian

### 3.1 Perangkat Lunak dan Konfigurasi
| Komponen               | Versi / Spesifikasi                     |
|------------------------|-----------------------------------------|
| **API Server**         | FastAPI 0.100.0 (Python 3.11)           |
| **Database**           | PostgreSQL 15.2                         |
| **Sistem Operasi**     | Ubuntu 22.04 LTS (Server)               |
| **Alat Pengujian**     | Postman 11.0.0, Newman 6.0.0 (CLI)      |
| **URL Endpoint**       | `https://staging-api.taskflow.id/v1`    |

### 3.2 Data Uji
- **User Admin**: `admin@taskflow.id` / `Admin#2024`
- **User Biasa**: `budi@email.com` / `User#1234`
- Data dummy proyek dan tugas telah di-*seed* menggunakan skrip migrasi (total: 3 proyek, 15 tugas).

---

## 4. Ringkasan Eksekusi Pengujian

### 4.1 Statistik Keseluruhan
| Metrik                        | Jumlah |
|-------------------------------|--------|
| Total Test Case               | 28     |
| Passed                        | 25     |
| Failed                        | 3      |
| Blocked                       | 0      |
| Execution Time (total)        | 45 menit |

### 4.2 Distribusi Berdasarkan Modul
| Modul          | Test Case | Pass | Fail |
|----------------|-----------|------|------|
| Autentikasi    | 4         | 4    | 0    |
| Manajemen User | 4         | 4    | 0    |
| Proyek         | 5         | 5    | 0    |
| Tugas          | 7         | 6    | 1    |
| Komentar       | 4         | 2    | 2    |
| Laporan        | 4         | 4    | 0    |
| **Total**      | **28**    | **25** | **3** |

---

## 5. Hasil Pengujian Terperinci

### 5.1 Modul Autentikasi & User
| ID     | Nama Test Case                      | Metode | Endpoint            | Hasil | Catatan                                 |
|--------|-------------------------------------|--------|---------------------|-------|-----------------------------------------|
| AUTH-01| Login dengan kredensial valid       | POST   | `/auth/login`       | ✅ Pass | Token JWT diterima, expires 30 menit    |
| AUTH-02| Login dengan password salah         | POST   | `/auth/login`       | ✅ Pass | 401 Unauthorized                        |
| AUTH-03| Register user baru (data valid)     | POST   | `/auth/register`    | ✅ Pass | 201 Created, password ter-hash          |
| AUTH-04| Register dengan email sudah terdaftar| POST  | `/auth/register`    | ✅ Pass | 400 Bad Request, pesan jelas            |
| USER-01| Get profil sendiri                  | GET    | `/users/me`         | ✅ Pass | Data sesuai user yang login             |
| USER-02| Update profil (nama)                | PATCH  | `/users/me`         | ✅ Pass | 200 OK, perubahan tersimpan             |
| USER-03| Admin dapat melihat semua user      | GET    | `/users`            | ✅ Pass | 200 OK, hanya role admin                |
| USER-04| User biasa tidak bisa akses `/users`| GET    | `/users`            | ✅ Pass | 403 Forbidden                           |

### 5.2 Modul Proyek
| ID     | Nama Test Case                      | Metode | Endpoint            | Hasil | Catatan                                 |
|--------|-------------------------------------|--------|---------------------|-------|-----------------------------------------|
| PRJ-01 | Buat proyek baru (user)             | POST   | `/projects`         | ✅ Pass | 201 Created, owner sesuai user login    |
| PRJ-02 | Buat proyek dengan nama kosong      | POST   | `/projects`         | ✅ Pass | 422 Validation Error                    |
| PRJ-03 | List proyek milik user              | GET    | `/projects`         | ✅ Pass | Hanya proyek user tersebut              |
| PRJ-04 | Admin dapat melihat semua proyek    | GET    | `/projects?all=true`| ✅ Pass | Filter berfungsi                        |
| PRJ-05 | Hapus proyek (hanya owner/admin)    | DELETE | `/projects/{id}`    | ✅ Pass | 204 No Content, proyek terhapus         |

### 5.3 Modul Tugas
| ID     | Nama Test Case                      | Metode | Endpoint                     | Hasil | Catatan                                 |
|--------|-------------------------------------|--------|------------------------------|-------|-----------------------------------------|
| TSK-01 | Tambah tugas ke proyek              | POST   | `/projects/{id}/tasks`       | ✅ Pass | 201 Created                             |
| TSK-02 | Tambah tugas tanpa judul            | POST   | `/projects/{id}/tasks`       | ✅ Pass | 422 Validation Error                    |
| TSK-03 | Ubah status tugas (selesai)         | PATCH  | `/tasks/{id}`                | ✅ Pass | 200 OK, completed_at terisi             |
| TSK-04 | Assign tugas ke user lain           | PUT    | `/tasks/{id}/assign`         | ✅ Pass | Hanya admin/project owner               |
| TSK-05 | Filter tugas berdasarkan status     | GET    | `/tasks?status=done`         | ✅ Pass | Hanya tugas selesai                     |
| TSK-06 | Hapus tugas (non-owner)             | DELETE | `/tasks/{id}`                | ✅ Pass | 403 Forbidden                           |
| TSK-07 | **Assign tugas dengan user tidak ada**| PUT  | `/tasks/{id}/assign`         | ❌ **Fail** | Seharusnya 404, tapi dapat 500 (lihat defect D-02) |

### 5.4 Modul Komentar
| ID     | Nama Test Case                      | Metode | Endpoint                     | Hasil | Catatan                                 |
|--------|-------------------------------------|--------|------------------------------|-------|-----------------------------------------|
| CMT-01 | Tambah komentar di tugas            | POST   | `/tasks/{id}/comments`       | ✅ Pass | 201 Created                             |
| CMT-02 | Lihat komentar tugas                | GET    | `/tasks/{id}/comments`       | ✅ Pass | 200 OK, daftar komentar                 |
| CMT-03 | **Hapus komentar milik orang lain** | DELETE | `/comments/{id}`             | ❌ **Fail** | **CRITICAL** User biasa bisa hapus komentar siapa pun (lihat D-01) |
| CMT-04 | **Tambah komentar dengan teks kosong**| POST  | `/tasks/{id}/comments`       | ❌ **Fail** | Seharusnya 422, tapi 201 dengan teks kosong (lihat D-03) |

### 5.5 Modul Laporan
| ID     | Nama Test Case                      | Metode | Endpoint                     | Hasil | Catatan                                 |
|--------|-------------------------------------|--------|------------------------------|-------|-----------------------------------------|
| RPT-01 | Laporan ringkasan proyek            | GET    | `/reports/projects/summary`  | ✅ Pass | 200 OK, format PDF                      |
| RPT-02 | Laporan tugas per user (CSV)        | GET    | `/reports/tasks?format=csv`  | ✅ Pass | Header Content-Disposition benar        |
| RPT-03 | Akses laporan tanpa login           | GET    | `/reports/projects/summary`  | ✅ Pass | 401 Unauthorized                        |
| RPT-04 | Laporan dengan filter tanggal       | GET    | `/reports/tasks?from=2026-01-01` | ✅ Pass | Data sesuai rentang                     |

---

## 6. Laporan Cacat (Defect)

### D-01: Broken Access Control pada Penghapusan Komentar
| **Atribut**        | **Deskripsi**                                                                 |
|---------------------|-------------------------------------------------------------------------------|
| **ID**              | D-01                                                                          |
| **Ringkasan**       | User biasa dapat menghapus komentar milik user lain tanpa izin.               |
| **Severity**        | 🔴 **Critical** (Keamanan)                                                     |
| **Prioritas**       | High                                                                          |
| **Langkah Reproduksi** | 1. Login sebagai user A.<br>2. Buat komentar di tugas milik proyek user B.<br>3. Login sebagai user B.<br>4. Kirim `DELETE /comments/{id_komentar_A}` dengan token user B. |
| **Hasil Aktual**    | 204 No Content (komentar terhapus).                                           |
| **Hasil Harapan**   | 403 Forbidden.                                                                |
| **Root Cause**      | Tidak ada pengecekan kepemilikan komentar di handler endpoint DELETE.         |
| **Status**          | **Open** (Menunggu perbaikan)                                                 |

### D-02: Internal Server Error saat Assign User Tidak Valid
| **Atribut**        | **Deskripsi**                                                                 |
|---------------------|-------------------------------------------------------------------------------|
| **ID**              | D-02                                                                          |
| **Ringkasan**       | Endpoint `PUT /tasks/{id}/assign` melempar error 500 ketika user_id tidak ditemukan. |
| **Severity**        | 🟡 **Minor**                                                                   |
| **Prioritas**       | Medium                                                                        |
| **Langkah Reproduksi** | 1. Kirim request dengan body `{"user_id": 9999}` (ID tidak ada di database). |
| **Hasil Aktual**    | 500 Internal Server Error dengan traceback SQLAlchemy.                        |
| **Hasil Harapan**   | 404 Not Found dengan pesan "User tidak ditemukan".                            |
| **Root Cause**      | Tidak ada penanganan exception `NoResultFound` sebelum `db.commit()`.          |
| **Status**          | **Open**                                                                      |

### D-03: Komentar Kosong Diterima
| **Atribut**        | **Deskripsi**                                                                 |
|---------------------|-------------------------------------------------------------------------------|
| **ID**              | D-03                                                                          |
| **Ringkasan**       | Validasi field `content` tidak diterapkan, komentar dengan string kosong tersimpan. |
| **Severity**        | 🟡 **Minor**                                                                   |
| **Prioritas**       | Low                                                                           |
| **Langkah Reproduksi** | 1. POST `/tasks/{id}/comments` dengan body `{"content": ""}`.                |
| **Hasil Aktual**    | 201 Created, data tersimpan di database dengan teks kosong.                   |
| **Hasil Harapan**   | 422 Validation Error (minimal 1 karakter).                                    |
| **Root Cause**      | Pydantic schema `CommentCreate` tidak menggunakan `min_length=1`.             |
| **Status**          | **Open**                                                                      |

---

## 7. Rekomendasi

1. **Perbaikan Segera (Critical):**
   - Implementasikan pemeriksaan kepemilikan pada endpoint DELETE `/comments/{id}`. Tambahkan dekorator atau dependency `get_current_user` yang membandingkan `comment.user_id` dengan `current_user.id`.

2. **Perbaikan Minor:**
   - Tambahkan `try-except` pada query pencarian user di endpoint assign tugas, kembalikan 404 jika user tidak ditemukan.
   - Perbarui schema `CommentCreate` dengan `Field(..., min_length=1)`.

3. **Peningkatan Proses QA:**
   - Tambahkan test case otomatis untuk semua skenario kontrol akses (RBAC) di pipeline CI/CD.
   - Gunakan tool keamanan API (misal OWASP ZAP) untuk pemindaian kerentanan dasar.

4. **Dokumentasi:**
   - Perjelas respons error 403 vs 404 pada dokumentasi OpenAPI untuk mencegah ambiguitas.

---

## 8. Kesimpulan

Berdasarkan hasil pengujian, API TaskFlow versi 1.0.0 **belum memenuhi kriteria kelulusan** untuk rilis produksi. Meskipun 89,3% test case lulus, ditemukan satu celah keamanan kritis (**D-01**) yang memungkinkan eskalasi hak akses. Tim pengembang harus memprioritaskan perbaikan defect tersebut sebelum melakukan pengujian ulang (retest). Dua defect minor lainnya tidak menghalangi fungsionalitas inti namun sebaiknya diperbaiki untuk meningkatkan kualitas pengalaman pengembang.

Pengujian selanjutnya akan difokuskan pada:
- Verifikasi perbaikan defect.
- Pengujian regresi menyeluruh.
- Pengujian performa untuk endpoint laporan.

---

## Lampiran

### A. Postman Collection & Environment
- File koleksi: `TaskFlow_API_Test.postman_collection.json`
- File environment: `TaskFlow_Staging.postman_environment.json`
- Laporan Newman HTML tersedia di direktori `newman-report/`.

### B. Contoh Respons Gagal (D-01)
```http
DELETE /comments/42 HTTP/1.1
Host: staging-api.taskflow.id
Authorization: Bearer <token_user_B>

HTTP/1.1 204 No Content