# 📙 SmartLib QA Lab Manual: Automation Fundamentals
**Standard**: Antigravity Architect Academic Suite  
**Target**: Mahasiswa SQA (Software Quality Assurance)

---

## 🎯 1. Tujuan Praktikum
Setelah menyelesaikan modul ini, Mahasiswa diharapkan mampu:
1.  Melakukan pengujian atomasi menggunakan **Selenium IDE**.
2.  Membedakan antara **Positive Testing** (jalur sukses) dan **Negative Testing** (jalur gagal).
3.  Mendokumentasikan hasil pengujian secara profesional dan akurat.
4.  Menganalisis mekanisme validasi pada aplikasi web.

---

## 📝 2. Instruksi Pengerjaan (Wajib Dibaca!)
Mahasiswa diwajibkan mengikuti protokol berikut selama sesi praktikum:

1.  **Preparation**: Jalankan aplikasi SmartLib di `http://localhost:8000/`.
2.  **Execution**: Jalankan setiap Test Case (TC) secara berurutan menggunakan Selenium IDE.
3.  **Logging**: Catat hasil setiap TC ke dalam **Tabel Laporan Hasil Pengujian** (lihat poin 3).
4.  **Evidence**: Ambil screenshot (Capture) jika terjadi error atau kegagalan asersi.
5.  **Documentation**: Simpan project Selenium Anda dalam format `.side`.

---

## 📑 3. Template Laporan Hasil Pengujian
Salin tabel ini ke dokumen laporan Anda (Word/Markdown/PDF) untuk setiap sesi pengujian.

| ID Test Case | Nama Pengujian | Status (PASS/FAIL) | Catatan / Error Log | Evidence (Link/File) |
| :--- | :--- | :--- | :--- | :--- |
| TC-AUTH-001 | Login Mahasiswa (Positif) | | | |
| TC-BOOK-008 | ISBN Duplikat (Negatif) | | | |
| ... | ... | ... | ... | ... |

---

## 🔐 4. Modul: Autentikasi (Pintu Masuk Sistem)

### [POSITIVE] TC-AUTH-001 - Login Berhasil (Mahasiswa)
| Parameter | Detail |
| :--- | :--- |
| **Data** | Email: `andi@student.polman.id`, Pass: `smartlib123` |
| **Ekspektasi** | Redirect ke Dashboard Mahasiswa. |

#### Script Selenium
| Command | Target | Value |
| :--- | :--- | :--- |
| `open` | `/login.php` | |
| `execute script` | `document.getElementById('email').value='andi@student.polman.id'` | |
| `execute script` | `document.getElementById('password').value='smartlib123'` | |
| `click` | `id=btnLogin` | |
| `assertUrlContains` | | `student/dashboard.php` |

---

### [NEGATIVE] TC-AUTH-004 - Password Salah
| Parameter | Detail |
| :--- | :--- |
| **Data** | Email: `andi@student.polman.id`, Pass: `passwordsalah` |
| **Ekspektasi** | Pesan error "Email atau password salah" muncul. |

#### Script Selenium
| Command | Target | Value |
| :--- | :--- | :--- |
| `open` | `/login.php` | |
| `execute script` | `document.getElementById('email').value='andi@student.polman.id'` | |
| `execute script` | `document.getElementById('password').value='passwordsalah'` | |
| `click` | `id=btnLogin` | |
| `assertElementPresent` | `css=.alert-danger` | |

---

### [NEGATIVE] TC-AUTH-008 - Login Akun Non-Aktif
> [!WARNING]
> Verifikasi bahwa sistem tidak memberikan akses kepada akun yang telah di-suspend oleh Admin.

| Parameter | Detail |
| :--- | :--- |
| **Data** | Gunakan email yang statusnya `is_active = 0` |
| **Ekspektasi** | Muncul pesan: "Akun Anda tidak aktif". |

#### Script Selenium
| Command | Target | Value |
| :--- | :--- | :--- |
| `open` | `/login.php` | |
| `execute script` | `document.getElementById('email').value='nonaktif@test.com'` | |
| `execute script` | `document.getElementById('password').value='smartlib123'` | |
| `click` | `id=btnLogin` | |
| `assertTextContains` | `css=.alert-danger` | `Akun Anda tidak aktif` |

---

### [NEGATIVE] TC-AUTH-009 - Input Kosong (HTML5 Validation)
| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Form tidak terkirim, browser menunjukkan tooltip "Please fill out this field". |

#### Script Selenium
| Command | Target | Value |
| :--- | :--- | :--- |
| `open` | `/login.php` | |
| `click` | `id=btnLogin` | |
| `assertValue` | `id=email` | |

---

## 📚 5. Modul: Manajemen Buku (Inventaris)

### [NEGATIVE] TC-BOOK-007 - Tambah Buku ISBN Duplikat
| Parameter | Detail |
| :--- | :--- |
| **Skenario** | Memasukkan ISBN yang sudah ada di database. |
| **Ekspektasi** | Muncul pesan error "ISBN sudah ada dalam sistem". |

#### Script Selenium
| Command | Target | Value |
| :--- | :--- | :--- |
| `open` | `/librarian/book_add.php` | |
| `type` | `name=isbn` | `978-0132350884` (ISBN yang sudah ada) |
| `click` | `css=button[type=submit]` | |
| `assertTextContains` | `css=.alert-danger` | `sudah ada` |

---

## 📈 6. Modul: Sirkulasi (Peminjaman)

### [NEGATIVE] TC-LOAN-004 - Mahasiswa Melebihi Kuota (Max: 3)
> [!IMPORTANT]
> Aturan Bisnis: Mahasiswa tidak boleh meminjam buku lebih dari 3 buah secara bersamaan.

| Parameter | Detail |
| :--- | :--- |
| **Ekspektasi** | Tombol submit memberikan error atau info kuota habis tampil. |

#### Script Selenium
| Command | Target | Value |
| :--- | :--- | :--- |
| `open` | `/librarian/loan_add.php` | |
| `select` | `id=user_id` | `label=Andi Pratama (Sudah Pinjam 3)` |
| `assertTextContains` | `id=loanInfo` | `Batas peminjaman (3) tercapai` |

---

## ✅ 7. Checklist Akhir Penyerahan
Sebelum mengumpulkan, pastikan Anda telah:
- [ ] Menjalankan semua 10+ Test Case.
- [ ] Mengisi Tabel Laporan Hasil Pengujian dengan lengkap.
- [ ] Melampirkan bukti Screenshot untuk setiap Test Case yang **FAIL**.
- [ ] Mengekspor Project Selenium dalam format `.side`.

---
*Generated by Antigravity Architect - Supporting the next generation of Indonesian SQA Engineers.*
