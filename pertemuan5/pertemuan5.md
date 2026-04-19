# Pengujian Otomatis (Automation Testing) dalam Pengembangan Perangkat Lunak

## Panduan Lengkap untuk Mahasiswa dan Pengembang Full-Stack Pemula



## Bagian 1: Memahami Konsep Dasar Automation Testing

### Apa Itu Automation Testing?

Bayangkan kamu seorang koki yang setiap hari harus mencicipi masakan untuk memastikan rasanya enak. Setiap kali kamu menambahkan bumbu baru, kamu harus mencicipi lagi dari awal semua masakan yang sudah jadi. Melelahkan, bukan? Nah, dalam dunia pengembangan software, situasi serupa terjadi. Setiap kali programmer menambahkan fitur baru atau memperbaiki bug, mereka harus memastikan bahwa fitur-fitur lama tetap berfungsi dengan baik.

**Automation Testing** adalah solusi untuk masalah ini. Secara sederhana:

> Automation Testing adalah teknik di mana kita menulis "naskah" atau "script" yang akan menjalankan pengujian secara otomatis menggunakan perangkat lunak khusus, tanpa perlu campur tangan manusia setiap saat.

Ibaratnya, kita merekam langkah-langkah mencicipi masakan tadi ke dalam sebuah mesin, dan setiap kali ada perubahan resep, mesin itu akan otomatis mencicipi semua masakan untuk kita dan melaporkan hasilnya.

### Analogi Sederhana untuk Memahami

| **Manual Testing** | **Automation Testing** |
|-------------------|------------------------|
| Seperti mencuci piring satu per satu dengan tangan | Seperti menggunakan mesin pencuci piring otomatis |
| Setiap kali ada piring kotor, kamu harus mencucinya lagi | Mesin akan mencuci semua piring secara otomatis sesuai program |
| Hasil cucian bisa berbeda-beda tergantung mood dan tenaga | Hasil cucian selalu konsisten |
| Cocok untuk jumlah piring sedikit dan variatif | Cocok untuk jumlah piring banyak dan berulang |

### Mengapa Automation Testing Penting dalam Era Modern?

Di era pengembangan software modern seperti **Agile** dan **DevOps**, tim engineering merilis kode baru setiap hari, bahkan setiap jam. Bayangkan jika setiap rilis harus diuji manual oleh tim QA—prosesnya akan sangat lambat dan rawan kesalahan manusia.

**Continuous Integration (CI)** dan **Continuous Delivery (CD)** adalah dua konsep kunci:
- **CI**: Setiap perubahan kode yang di-push ke repository akan otomatis diuji. Jika ada error, tim langsung diberi tahu.
- **CD**: Kode yang sudah lolos uji akan otomatis dikirim ke server produksi.

Tanpa automation testing, CI/CD tidak mungkin berjalan efektif. Automation testing adalah "jantung" dari pipeline CI/CD.

### Pelajaran Berharga dari Kecelakaan Pesawat 1994

Pada tahun 1994, sebuah pesawat yang sedang dalam penerbangan rutin mengalami kecelakaan fatal sesaat sebelum mendarat. Investigasi mengungkapkan bahwa penyebabnya adalah **bug atau cacat pada software** sistem navigasi pesawat. Tim penguji tidak melakukan pengujian akhir dengan teliti karena mengandalkan proses manual yang melelahkan dan rawan kelalaian.

Kasus ini mengajarkan kita bahwa untuk sistem-sistem kritis (seperti pesawat, peralatan medis, sistem perbankan), **pengujian otomatis yang akurat menjadi mutlak diperlukan**. Automation testing membantu memastikan bahwa tidak ada langkah pengujian yang terlewat, bahkan ketika manusia merasa lelah atau bosan.

---

## Bagian 2: Transformasi dari Manual ke Automated Testing

### Perbandingan Mendalam: Manual vs Automated Testing

| **Aspek** | **Manual Testing** | **Automated Testing** |
|-----------|-------------------|----------------------|
| **Keandalan** | Rentan kesalahan manusia (human error). Tester bisa lupa langkah, salah input data, atau melewatkan skenario. | Sangat andal karena dijalankan oleh script. Hasil selalu konsisten setiap kali dijalankan. |
| **Kecepatan** | Lambat. Menguji 100 test case bisa memakan waktu berjam-jam atau berhari-hari. | Sangat cepat. 100 test case bisa selesai dalam hitungan menit. |
| **Investasi Awal** | Rendah. Hanya butuh tenaga manusia. | Tinggi. Butuh tools, infrastruktur, dan skill programming. |
| **Investasi Jangka Panjang** | Tinggi. Harus terus membayar tester untuk pekerjaan berulang. | Rendah. Sekali script dibuat, bisa digunakan ribuan kali. |
| **Cakupan Pengujian** | Terbatas. Tidak mungkin menguji semua kombinasi skenario secara manual. | Luas. Bisa menjalankan ribuan kombinasi data uji. |
| **Pengujian Regresi** | Melelahkan dan rawan terlewat karena perubahan requirement yang cepat. | Ideal. Setiap perubahan kode bisa langsung diuji regresi secara otomatis. |
| **Kebutuhan Skill** | Tidak perlu programming. Fokus pada pemahaman bisnis dan eksplorasi. | Wajib paham programming untuk menulis script. |
| **Feedback Loop** | Lambat. Hasil pengujian baru diketahui setelah tester selesai bekerja. | Cepat. Hasil langsung tersedia setelah script selesai dijalankan. |

### Analogi: Fotografer Profesional

- **Manual Testing** seperti memotret dengan kamera manual: setiap kali harus mengatur fokus, aperture, shutter speed secara manual. Hasilnya bisa artistik dan unik, tapi lambat dan tidak konsisten untuk pemotretan massal.
- **Automated Testing** seperti memotret dengan mode otomatis: pengaturan dilakukan kamera secara konsisten. Cocok untuk memotret ratusan foto produk e-commerce dengan hasil yang seragam.

### Kapan Harus Beralih ke Automation?

Tanda-tanda tim kamu sudah waktunya mengadopsi automation testing:

1. **Rilis semakin sering** (mingguan atau harian).
2. **Test case semakin banyak** dan memakan waktu berjam-jam untuk dijalankan manual.
3. **Banyak bug regresi** muncul setelah penambahan fitur baru.
4. **Tim QA kewalahan** dengan pekerjaan berulang.
5. **Produk sudah stabil** secara fitur inti.

---

## Bagian 3: Jenis-Jenis Pengujian yang Harus Diotomatisasi Terlebih Dahulu

Tidak semua pengujian perlu atau layak diotomatisasi. Berikut prioritasnya:

### 1. End-to-End (E2E) Tests

**Definisi:** Pengujian yang mensimulasikan alur pengguna nyata dari awal hingga akhir aplikasi.

**Contoh Kasus:**
Seorang pengguna membuka website e-commerce, mencari produk "sepatu lari", menambahkannya ke keranjang, melakukan checkout, dan menyelesaikan pembayaran.

**Mengapa Penting Diotomatisasi?**
- Memastikan seluruh sistem (frontend, backend, database, layanan pihak ketiga) terintegrasi dengan baik.
- Menangkap bug yang hanya muncul ketika semua komponen berinteraksi.
- Memberikan keyakinan bahwa fitur kritis bisnis berfungsi sebelum rilis.

### 2. Unit Tests

**Definisi:** Pengujian pada unit terkecil dari kode (fungsi atau metode) secara terisolasi.

**Contoh Kasus:**
Fungsi `calculateDiscount(harga, persentaseDiskon)` pada backend.

**Mengapa Penting Diotomatisasi?**
- Dijalankan setiap kali ada perubahan kode (setiap commit/push).
- Memberikan feedback instan kepada developer jika ada yang rusak.
- Berfungsi sebagai "dokumentasi hidup" tentang bagaimana kode seharusnya bekerja.

### 3. Integration Tests

**Definisi:** Pengujian interaksi antara dua atau lebih modul/komponen.

**Contoh Kasus:**
Menguji apakah endpoint API `/api/users` dapat menyimpan data ke database dengan benar.

**Mengapa Penting Diotomatisasi?**
- Memastikan kontrak antar modul (misalnya API contract) dipenuhi.
- Lebih cepat dari E2E test tapi lebih luas cakupannya dari unit test.

### 4. Performance Tests

**Definisi:** Pengujian untuk mengukur kecepatan, responsivitas, dan stabilitas sistem di bawah beban tertentu.

**Contoh Kasus:**
Mensimulasikan 1000 pengguna mengakses website secara bersamaan.

**Mengapa Penting Diotomatisasi?**
- Manual tidak mungkin mensimulasikan ribuan pengguna.
- Hasil pengukuran harus akurat dan dapat direproduksi.

---

## Bagian 4: Jenis Pengujian yang Sebaiknya Tetap Manual

Beberapa jenis pengujian membutuhkan intuisi, kreativitas, dan penilaian manusia yang tidak bisa digantikan oleh script:

### 1. Exploratory Testing

**Definisi:** Pengujian tanpa skenario baku di mana tester bebas menjelajahi aplikasi menggunakan intuisi dan pengalaman.

**Mengapa Tetap Manual?**
- Manusia bisa menemukan bug yang tidak terduga karena tidak mengikuti "jalur bahagia" (happy path).
- Memerlukan pemahaman konteks dan empati terhadap pengguna akhir.
- Contoh: Seorang tester iseng mengklik tombol "Back" browser saat proses pembayaran, menemukan bahwa data keranjang hilang—skenario yang mungkin tidak terpikirkan saat menulis script otomatis.

### 2. Usability Testing

**Definisi:** Pengujian untuk menilai seberapa mudah dan nyaman aplikasi digunakan oleh manusia.

**Mengapa Tetap Manual?**
- Komputer tidak bisa menilai apakah tombol terlalu kecil untuk diklik, warna teks sulit dibaca, atau alur navigasi membingungkan.
- Membutuhkan observasi langsung terhadap perilaku dan ekspresi pengguna.

### 3. Visual Regression Testing (Aspek Subjektif)

**Definisi:** Memeriksa apakah tampilan UI berubah secara tidak sengaja.

**Catatan:** Bagian perbandingan screenshot bisa diotomatisasi (misalnya dengan Percy atau BackstopJS), tetapi **penilaian apakah perubahan tersebut disengaja atau tidak** tetap membutuhkan keputusan manusia.

---

## Bagian 5: Automation Testing untuk Full-Stack Web Developer

Sebagai seorang **full-stack web developer**, kamu bertanggung jawab atas dua sisi aplikasi:

### Sisi Backend (API)

Fokus pengujian otomatis pada backend:
- **Kontrak API:** Memastikan endpoint mengembalikan struktur JSON yang sesuai spesifikasi.
- **Logika Bisnis:** Memastikan perhitungan, validasi, dan aturan bisnis berjalan benar.
- **Integrasi Database:** Memastikan data tersimpan, ter-update, dan terhapus dengan benar.
- **Keamanan:** Memastikan endpoint terlindungi dari akses tidak sah (autentikasi/otorisasi).
- **Performa:** Memastikan response time dalam batas yang dapat diterima.

**Tools Utama untuk Backend:**
- **Postman / Newman:** Untuk menguji API secara manual dan otomatis.
- **Pytest (Python):** Untuk unit test dan integration test pada FastAPI.
- **Requests Library:** Untuk membuat HTTP client dalam script Python.

### Sisi Frontend (UI)

Fokus pengujian otomatis pada frontend:
- **Fungsionalitas UI:** Memastikan tombol, form, dan link bekerja sesuai harapan.
- **Validasi Form:** Memastikan pesan error muncul saat input tidak valid.
- **Navigasi:** Memastikan routing antar halaman berfungsi.
- **Interaksi JavaScript:** Memastikan event handler dan manipulasi DOM berjalan.
- **Kompatibilitas Browser:** Memastikan aplikasi berjalan di Chrome, Firefox, Edge.

**Tools Utama untuk Frontend:**
- **Selenium WebDriver:** Untuk mengotomatisasi browser nyata.
- **Browser Developer Tools:** Untuk inspeksi elemen dan debugging.

---

## Bagian 6: Studi Kasus Nyata - Aplikasi Manajemen Tugas (Task Manager)

Mari kita bangun sebuah aplikasi sederhana namun realistis untuk mendemonstrasikan automation testing secara menyeluruh.

### Deskripsi Proyek

**Nama Aplikasi:** TaskFlow - Aplikasi Manajemen Tugas Sederhana

**Fitur:**
1. **Backend (FastAPI):**
   - `POST /api/tasks` - Membuat tugas baru
   - `GET /api/tasks` - Mendapatkan daftar semua tugas
   - `GET /api/tasks/{id}` - Mendapatkan detail satu tugas
   - `PUT /api/tasks/{id}` - Memperbarui tugas
   - `DELETE /api/tasks/{id}` - Menghapus tugas

2. **Frontend (PHP Native + JavaScript + Bootstrap):**
   - Halaman daftar tugas (menampilkan semua tugas)
   - Form tambah tugas (judul, deskripsi, status)
   - Tombol edit dan hapus untuk setiap tugas
   - Tampilan responsif dengan Bootstrap 5

### Arsitektur Aplikasi

```
[Browser Pengguna] 
       |
       v
[Frontend: PHP + HTML + JS + Bootstrap]
       |
       | (AJAX/Fetch API)
       v
[Backend: FastAPI (Python)]
       |
       v
[Database: SQLite (file-based)]
```

---

## Bagian 7: Tutorial Langkah demi Langkah - Setup Proyek

### 7.1. Persiapan Lingkungan Development

**Prasyarat:**
- Python 3.8+ terinstall
- PHP 7.4+ terinstall
- Node.js (untuk npm, opsional untuk tools tambahan)
- Browser Chrome (untuk Selenium)
- Text Editor/IDE (VS Code direkomendasikan)

**Struktur Folder Proyek:**

```
taskflow/
├── backend/
│   ├── main.py
│   ├── requirements.txt
│   ├── test_api.py
│   └── tasks.db (SQLite, akan dibuat otomatis)
├── frontend/
│   ├── index.php
│   ├── style.css
│   ├── script.js
│   └── api_config.js
├── tests/
│   ├── postman/
│   │   └── TaskFlow_API_Collection.json
│   ├── selenium/
│   │   ├── test_frontend.py
│   │   └── conftest.py
│   └── performance/
│       └── locustfile.py
└── README.md
```

### 7.2. Membangun Backend dengan FastAPI

**Langkah 1: Buat Virtual Environment dan Install Dependencies**

```bash
# Masuk ke folder backend
cd taskflow/backend

# Buat virtual environment
python -m venv venv

# Aktifkan (Windows)
venv\Scripts\activate
# Aktifkan (Mac/Linux)
source venv/bin/activate

# Install FastAPI dan dependencies
pip install fastapi uvicorn sqlalchemy databases pydantic

# Buat requirements.txt
pip freeze > requirements.txt
```

**Langkah 2: Tulis Kode Backend (`main.py`)**

```python
# backend/main.py
from fastapi import FastAPI, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List
import sqlite3
import json
from datetime import datetime

app = FastAPI(title="TaskFlow API", version="1.0.0")

# CORS Configuration (agar frontend bisa akses API)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Di production, ganti dengan domain frontend
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Pydantic Models untuk validasi data
class TaskBase(BaseModel):
    title: str
    description: Optional[str] = None
    status: str = "pending"  # pending, in-progress, completed

class TaskCreate(TaskBase):
    pass

class Task(TaskBase):
    id: int
    created_at: str
    updated_at: Optional[str] = None

# Database Helper Functions
DB_PATH = "tasks.db"

def get_db_connection():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row  # Agar hasil query bisa diakses seperti dictionary
    return conn

def init_db():
    """Membuat tabel tasks jika belum ada"""
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            status TEXT DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP
        )
    """)
    conn.commit()
    conn.close()

# Inisialisasi database saat aplikasi start
@app.on_event("startup")
async def startup_event():
    init_db()
    print("Database initialized!")

# ========== API Endpoints ==========

@app.get("/")
async def root():
    return {"message": "Selamat datang di TaskFlow API", "docs": "/docs"}

@app.post("/api/tasks", response_model=Task, status_code=status.HTTP_201_CREATED)
async def create_task(task: TaskCreate):
    """Membuat tugas baru"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    cursor.execute("""
        INSERT INTO tasks (title, description, status)
        VALUES (?, ?, ?)
    """, (task.title, task.description, task.status))
    
    task_id = cursor.lastrowid
    conn.commit()
    
    # Ambil data yang baru saja diinsert
    cursor.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
    new_task = dict(cursor.fetchone())
    conn.close()
    
    return new_task

@app.get("/api/tasks", response_model=List[Task])
async def get_all_tasks():
    """Mendapatkan semua tugas"""
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM tasks ORDER BY id DESC")
    tasks = [dict(row) for row in cursor.fetchall()]
    conn.close()
    return tasks

@app.get("/api/tasks/{task_id}", response_model=Task)
async def get_task(task_id: int):
    """Mendapatkan detail satu tugas berdasarkan ID"""
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
    task = cursor.fetchone()
    conn.close()
    
    if not task:
        raise HTTPException(status_code=404, detail="Tugas tidak ditemukan")
    
    return dict(task)

@app.put("/api/tasks/{task_id}", response_model=Task)
async def update_task(task_id: int, task_update: TaskCreate):
    """Memperbarui tugas yang sudah ada"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    # Cek apakah tugas ada
    cursor.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
    existing = cursor.fetchone()
    if not existing:
        conn.close()
        raise HTTPException(status_code=404, detail="Tugas tidak ditemukan")
    
    # Update tugas
    cursor.execute("""
        UPDATE tasks 
        SET title = ?, description = ?, status = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    """, (task_update.title, task_update.description, task_update.status, task_id))
    
    conn.commit()
    
    # Ambil data terbaru
    cursor.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
    updated_task = dict(cursor.fetchone())
    conn.close()
    
    return updated_task

@app.delete("/api/tasks/{task_id}", status_code=status.HTTP_204_NO_CONTENT)
async def delete_task(task_id: int):
    """Menghapus tugas"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    cursor.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
    if not cursor.fetchone():
        conn.close()
        raise HTTPException(status_code=404, detail="Tugas tidak ditemukan")
    
    cursor.execute("DELETE FROM tasks WHERE id = ?", (task_id,))
    conn.commit()
    conn.close()
    
    return None

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=8000, reload=True)
```

**Langkah 3: Jalankan Backend**

```bash
# Pastikan virtual environment aktif
uvicorn main:app --reload --host 127.0.0.1 --port 8000
```

Buka browser dan akses `http://127.0.0.1:8000/docs` - kamu akan melihat dokumentasi API interaktif Swagger UI secara otomatis!

---

### 7.3. Membangun Frontend dengan PHP Native + Bootstrap

**Langkah 1: Buat File `index.php`**

```php
<!-- frontend/index.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow - Manajemen Tugas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-tasks me-2"></i>TaskFlow
            </span>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mt-4">
        <!-- Alert untuk pesan sukses/error -->
        <div id="alertContainer"></div>

        <!-- Form Tambah Tugas -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Tugas Baru</h5>
            </div>
            <div class="card-body">
                <form id="taskForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Masukkan judul tugas" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Deskripsi tugas (opsional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Tugas
                    </button>
                    <button type="button" class="btn btn-secondary" id="resetForm">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Tugas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list text-primary me-2"></i>Daftar Tugas</h5>
                <button class="btn btn-sm btn-outline-primary" id="refreshBtn">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="tasksList">
                    <!-- Data tugas akan dimuat di sini via JavaScript -->
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Memuat data tugas...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Tugas -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <input type="hidden" id="editTaskId">
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Judul Tugas</label>
                            <input type="text" class="form-control" id="editTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="editDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus">
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- API Configuration -->
    <script src="api_config.js"></script>
    <!-- Main JavaScript -->
    <script src="script.js"></script>
</body>
</html>
```

**Langkah 2: Buat File `api_config.js`**

```javascript
// frontend/api_config.js
// Konfigurasi URL API Backend
const API_CONFIG = {
    BASE_URL: 'http://127.0.0.1:8000/api',
    ENDPOINTS: {
        TASKS: '/tasks'
    }
};

// Helper function untuk mendapatkan full URL
function apiUrl(endpoint) {
    return `${API_CONFIG.BASE_URL}${endpoint}`;
}
```

**Langkah 3: Buat File `script.js`**

```javascript
// frontend/script.js
// ========== Global Variables ==========
let tasks = [];
let editingTaskId = null;
let editModal = null;

// ========== Initialization ==========
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Bootstrap Modal
    editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    
    // Load tasks
    loadTasks();
    
    // Event Listeners
    document.getElementById('taskForm').addEventListener('submit', handleCreateTask);
    document.getElementById('resetForm').addEventListener('click', resetForm);
    document.getElementById('refreshBtn').addEventListener('click', loadTasks);
    document.getElementById('saveEditBtn').addEventListener('click', handleUpdateTask);
});

// ========== API Functions ==========
async function fetchTasks() {
    try {
        const response = await fetch(apiUrl(API_CONFIG.ENDPOINTS.TASKS));
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('Error fetching tasks:', error);
        showAlert('Gagal memuat data tugas', 'danger');
        return [];
    }
}

async function createTask(taskData) {
    try {
        const response = await fetch(apiUrl(API_CONFIG.ENDPOINTS.TASKS), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(taskData)
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('Error creating task:', error);
        throw error;
    }
}

async function updateTask(id, taskData) {
    try {
        const response = await fetch(`${apiUrl(API_CONFIG.ENDPOINTS.TASKS)}/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(taskData)
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('Error updating task:', error);
        throw error;
    }
}

async function deleteTask(id) {
    try {
        const response = await fetch(`${apiUrl(API_CONFIG.ENDPOINTS.TASKS)}/${id}`, {
            method: 'DELETE'
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return true;
    } catch (error) {
        console.error('Error deleting task:', error);
        throw error;
    }
}

// ========== UI Functions ==========
async function loadTasks() {
    tasks = await fetchTasks();
    renderTasksList();
}

function renderTasksList() {
    const container = document.getElementById('tasksList');
    
    if (tasks.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                <p>Belum ada tugas. Tambahkan tugas pertama Anda!</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover">';
    html += `
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
    `;
    
    tasks.forEach(task => {
        const statusBadge = getStatusBadge(task.status);
        html += `
            <tr>
                <td>${task.id}</td>
                <td><strong>${escapeHtml(task.title)}</strong></td>
                <td>${escapeHtml(task.description || '-')}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${task.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(${task.id})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning text-dark">Pending</span>',
        'in-progress': '<span class="badge bg-info">In Progress</span>',
        'completed': '<span class="badge bg-success">Completed</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    document.getElementById('alertContainer').innerHTML = alertHtml;
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 3000);
}

// ========== Event Handlers ==========
async function handleCreateTask(e) {
    e.preventDefault();
    
    const taskData = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        status: document.getElementById('status').value
    };
    
    try {
        await createTask(taskData);
        showAlert('Tugas berhasil ditambahkan!', 'success');
        resetForm();
        await loadTasks();
    } catch (error) {
        showAlert('Gagal menambahkan tugas', 'danger');
    }
}

function resetForm() {
    document.getElementById('taskForm').reset();
    document.getElementById('status').value = 'pending';
}

function openEditModal(id) {
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    
    editingTaskId = id;
    document.getElementById('editTaskId').value = id;
    document.getElementById('editTitle').value = task.title;
    document.getElementById('editDescription').value = task.description || '';
    document.getElementById('editStatus').value = task.status;
    
    editModal.show();
}

async function handleUpdateTask() {
    const taskData = {
        title: document.getElementById('editTitle').value,
        description: document.getElementById('editDescription').value,
        status: document.getElementById('editStatus').value
    };
    
    try {
        await updateTask(editingTaskId, taskData);
        showAlert('Tugas berhasil diperbarui!', 'success');
        editModal.hide();
        await loadTasks();
    } catch (error) {
        showAlert('Gagal memperbarui tugas', 'danger');
    }
}

async function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        try {
            await deleteTask(id);
            showAlert('Tugas berhasil dihapus!', 'success');
            await loadTasks();
        } catch (error) {
            showAlert('Gagal menghapus tugas', 'danger');
        }
    }
}
```

**Langkah 4: Buat File `style.css` (Opsional)**

```css
/* frontend/style.css */
body {
    background-color: #f8f9fa;
}

.card {
    border: none;
    border-radius: 10px;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
}

.btn {
    border-radius: 6px;
}

.badge {
    font-size: 0.85rem;
    padding: 0.35em 0.65em;
}

.alert {
    border-radius: 8px;
}
```

**Langkah 5: Jalankan Frontend**

Karena frontend menggunakan PHP, kamu bisa menggunakan PHP built-in server:

```bash
cd taskflow/frontend
php -S 127.0.0.1:8080
```

Buka browser dan akses `http://127.0.0.1:8080`. Pastikan backend FastAPI juga berjalan di port 8000.

---

## Bagian 8: Automation Testing untuk Backend API dengan Postman & Newman

### 8.1. Membuat Collection dan Tests di Postman

**Langkah 1: Buka Postman dan Buat Collection Baru**

1. Buka aplikasi Postman
2. Klik tombol "New" > "Collection"
3. Beri nama: `TaskFlow API Tests`
4. Klik "Create"

**Langkah 2: Tambahkan Environment Variables**

Buat Environment baru:
- Klik "Environments" di sidebar kiri
- Klik "Create Environment"
- Beri nama: `TaskFlow Local`
- Tambahkan variable:
  - `base_url`: `http://127.0.0.1:8000`

**Langkah 3: Buat Request dan Test Scripts**

Berikut adalah test script lengkap untuk setiap endpoint. Kamu bisa menambahkannya di tab "Tests" pada setiap request.

**Request 1: POST /api/tasks (Create Task)**

```
Method: POST
URL: {{base_url}}/api/tasks
Body (raw JSON):
{
    "title": "Belajar Automation Testing",
    "description": "Mempelajari Postman, Newman, dan Selenium",
    "status": "pending"
}

Tests Script:
```

```javascript
// Test script untuk Create Task
pm.test("Status code should be 201 Created", function () {
    pm.response.to.have.status(201);
});

pm.test("Response should have correct structure", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('id');
    pm.expect(jsonData).to.have.property('title');
    pm.expect(jsonData).to.have.property('description');
    pm.expect(jsonData).to.have.property('status');
    pm.expect(jsonData).to.have.property('created_at');
});

pm.test("Title should match sent data", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.title).to.eql("Belajar Automation Testing");
});

pm.test("Status should be 'pending'", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.status).to.eql("pending");
});

// Simpan task ID untuk digunakan di request selanjutnya
const jsonData = pm.response.json();
pm.environment.set("task_id", jsonData.id);
```

**Request 2: GET /api/tasks (Get All Tasks)**

```
Method: GET
URL: {{base_url}}/api/tasks

Tests Script:
```

```javascript
pm.test("Status code should be 200 OK", function () {
    pm.response.to.have.status(200);
});

pm.test("Response should be an array", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.be.an('array');
});

pm.test("Response time should be less than 200ms", function () {
    pm.expect(pm.response.responseTime).to.be.below(200);
});

pm.test("Array should contain at least one task", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.length).to.be.at.least(1);
});

pm.test("Each task should have required properties", function () {
    const jsonData = pm.response.json();
    jsonData.forEach(task => {
        pm.expect(task).to.have.property('id');
        pm.expect(task).to.have.property('title');
        pm.expect(task).to.have.property('status');
    });
});
```

**Request 3: GET /api/tasks/{id} (Get Single Task)**

```
Method: GET
URL: {{base_url}}/api/tasks/{{task_id}}

Tests Script:
```

```javascript
pm.test("Status code should be 200 OK", function () {
    pm.response.to.have.status(200);
});

pm.test("Task ID should match requested ID", function () {
    const jsonData = pm.response.json();
    const requestedId = pm.environment.get("task_id");
    pm.expect(jsonData.id.toString()).to.eql(requestedId);
});

pm.test("Should return correct task properties", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('title');
    pm.expect(jsonData).to.have.property('description');
    pm.expect(jsonData).to.have.property('status');
    pm.expect(jsonData).to.have.property('created_at');
});
```

**Request 4: GET /api/tasks/99999 (Non-existent Task - Negative Test)**

```
Method: GET
URL: {{base_url}}/api/tasks/99999

Tests Script:
```

```javascript
pm.test("Status code should be 404 Not Found", function () {
    pm.response.to.have.status(404);
});

pm.test("Error message should be 'Tugas tidak ditemukan'", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.detail).to.eql("Tugas tidak ditemukan");
});
```

**Request 5: PUT /api/tasks/{id} (Update Task)**

```
Method: PUT
URL: {{base_url}}/api/tasks/{{task_id}}
Body (raw JSON):
{
    "title": "Belajar Automation Testing - Updated",
    "description": "Sudah mempelajari Postman, Newman, dan Selenium",
    "status": "in-progress"
}

Tests Script:
```

```javascript
pm.test("Status code should be 200 OK", function () {
    pm.response.to.have.status(200);
});

pm.test("Title should be updated", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.title).to.include("Updated");
});

pm.test("Status should be updated to 'in-progress'", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.status).to.eql("in-progress");
});

pm.test("Should have updated_at timestamp", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('updated_at');
    pm.expect(jsonData.updated_at).to.not.be.null;
});
```

**Request 6: DELETE /api/tasks/{id} (Delete Task)**

```
Method: DELETE
URL: {{base_url}}/api/tasks/{{task_id}}

Tests Script:
```

```javascript
pm.test("Status code should be 204 No Content", function () {
    pm.response.to.have.status(204);
});

// Verifikasi bahwa task benar-benar terhapus
pm.test("Task should not be retrievable after deletion", function () {
    const taskId = pm.environment.get("task_id");
    pm.sendRequest(`${pm.environment.get("base_url")}/api/tasks/${taskId}`, function (err, res) {
        pm.expect(res.code).to.eql(404);
    });
});
```

**Request 7: POST /api/tasks (Validation Test - Missing Required Field)**

```
Method: POST
URL: {{base_url}}/api/tasks
Body (raw JSON):
{
    "description": "Task tanpa judul - seharusnya error"
}

Tests Script:
```

```javascript
pm.test("Status code should be 422 Unprocessable Entity", function () {
    pm.response.to.have.status(422);
});

pm.test("Error should mention missing 'title' field", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData.detail[0].loc).to.include("title");
});
```

### 8.2. Menjalankan Collection dengan Newman (CLI)

Newman adalah command-line runner untuk Postman collections. Ini memungkinkan kita menjalankan test dalam pipeline CI/CD.

**Langkah 1: Install Newman**

```bash
# Install globally via npm
npm install -g newman

# Atau install sebagai dev dependency
npm install --save-dev newman
```

**Langkah 2: Export Collection dari Postman**

1. Di Postman, klik kanan pada collection "TaskFlow API Tests"
2. Pilih "Export"
3. Pilih format "Collection v2.1"
4. Simpan sebagai `TaskFlow_API_Collection.json` di folder `tests/postman/`

**Langkah 3: Export Environment**

1. Klik ikon mata di kanan atas, pilih environment "TaskFlow Local"
2. Klik "Export"
3. Simpan sebagai `TaskFlow_Local_Environment.json`

**Langkah 4: Jalankan Collection dengan Newman**

```bash
# Basic run
newman run TaskFlow_API_Collection.json -e TaskFlow_Local_Environment.json

# Dengan report HTML (install reporter dulu)
npm install -g newman-reporter-htmlextra
newman run TaskFlow_API_Collection.json \
    -e TaskFlow_Local_Environment.json \
    -r htmlextra \
    --reporter-htmlextra-export ./test-report.html

# Dengan flags tambahan
newman run TaskFlow_API_Collection.json \
    -e TaskFlow_Local_Environment.json \
    --delay-request 100 \
    --timeout-request 5000 \
    --bail
```

**Langkah 5: Buat Script NPM untuk Kemudahan**

Buat `package.json` di root proyek:

```json
{
  "name": "taskflow-tests",
  "version": "1.0.0",
  "scripts": {
    "test:api": "newman run tests/postman/TaskFlow_API_Collection.json -e tests/postman/TaskFlow_Local_Environment.json",
    "test:api:report": "newman run tests/postman/TaskFlow_API_Collection.json -e tests/postman/TaskFlow_Local_Environment.json -r htmlextra --reporter-htmlextra-export ./reports/api-test-report.html"
  },
  "devDependencies": {
    "newman": "^6.0.0",
    "newman-reporter-htmlextra": "^1.23.0"
  }
}
```

Sekarang kamu bisa menjalankan test API dengan:
```bash
npm run test:api
```

---

## Bagian 9: Automation Testing untuk Frontend dengan Selenium WebDriver

### 9.1. Setup Selenium dengan Python

**Langkah 1: Install Dependencies**

```bash
# Di folder tests/selenium/
pip install selenium pytest pytest-html webdriver-manager
```

**Langkah 2: Buat File `conftest.py` (Pytest Configuration)**

```python
# tests/selenium/conftest.py
import pytest
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
import os

# Konfigurasi URL Frontend
FRONTEND_URL = "http://127.0.0.1:8080"

@pytest.fixture(scope="function")
def driver():
    """Fixture untuk WebDriver - akan dibuat baru setiap test function"""
    
    # Chrome Options
    chrome_options = Options()
    
    # Headless mode untuk CI/CD (tidak membuka browser GUI)
    if os.environ.get("HEADLESS", "false").lower() == "true":
        chrome_options.add_argument("--headless")
    
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--window-size=1920,1080")
    
    # Inisialisasi driver dengan WebDriver Manager (auto download chromedriver)
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=chrome_options)
    
    # Implicit wait - menunggu maksimal 10 detik untuk setiap find_element
    driver.implicitly_wait(10)
    
    # Buka halaman utama
    driver.get(FRONTEND_URL)
    
    yield driver
    
    # Cleanup - tutup browser setelah test selesai
    driver.quit()

@pytest.fixture
def frontend_url():
    return FRONTEND_URL

# Hook untuk screenshot saat test gagal
@pytest.hookimpl(tryfirst=True, hookwrapper=True)
def pytest_runtest_makereport(item, call):
    outcome = yield
    rep = outcome.get_result()
    
    if rep.when == "call" and rep.failed:
        if "driver" in item.fixturenames:
            web_driver = item.funcargs["driver"]
            screenshot_dir = "reports/screenshots"
            os.makedirs(screenshot_dir, exist_ok=True)
            
            screenshot_path = f"{screenshot_dir}/{item.name}.png"
            web_driver.save_screenshot(screenshot_path)
            print(f"\nScreenshot saved: {screenshot_path}")
```

### 9.2. Page Object Model (POM) untuk TaskFlow

Page Object Model adalah design pattern yang memisahkan logika interaksi dengan halaman web dari logika test. Ini membuat kode lebih mudah dipelihara.

**Langkah 1: Buat Folder `pages/` dan File `task_page.py`**

```python
# tests/selenium/pages/task_page.py
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys
import time

class TaskPage:
    """Page Object untuk halaman TaskFlow"""
    
    # ========== Locators ==========
    # Form tambah tugas
    FORM_TITLE = (By.ID, "title")
    FORM_DESCRIPTION = (By.ID, "description")
    FORM_STATUS = (By.ID, "status")
    BTN_SUBMIT = (By.CSS_SELECTOR, "button[type='submit']")
    BTN_RESET = (By.ID, "resetForm")
    
    # Tabel tugas
    TASKS_TABLE = (By.CSS_SELECTOR, ".table")
    TASKS_TABLE_BODY = (By.CSS_SELECTOR, ".table tbody")
    TASKS_TABLE_ROWS = (By.CSS_SELECTOR, ".table tbody tr")
    BTN_REFRESH = (By.ID, "refreshBtn")
    
    # Modal edit
    MODAL_EDIT = (By.ID, "editTaskModal")
    MODAL_TITLE = (By.ID, "editTitle")
    MODAL_DESCRIPTION = (By.ID, "editDescription")
    MODAL_STATUS = (By.ID, "editStatus")
    BTN_SAVE_EDIT = (By.ID, "saveEditBtn")
    BTN_CANCEL_EDIT = (By.CSS_SELECTOR, "#editTaskModal .btn-secondary")
    BTN_CLOSE_MODAL = (By.CSS_SELECTOR, "#editTaskModal .btn-close")
    
    # Alert container
    ALERT_CONTAINER = (By.ID, "alertContainer")
    ALERT_SUCCESS = (By.CSS_SELECTOR, ".alert-success")
    ALERT_DANGER = (By.CSS_SELECTOR, ".alert-danger")
    
    # Empty state
    EMPTY_STATE = (By.CSS_SELECTOR, ".text-center.text-muted")
    
    def __init__(self, driver):
        self.driver = driver
        self.wait = WebDriverWait(driver, 10)
    
    # ========== Actions ==========
    
    def create_task(self, title, description="", status="pending"):
        """Membuat tugas baru melalui form"""
        self.wait.until(EC.presence_of_element_located(self.FORM_TITLE))
        
        # Isi form
        self.driver.find_element(*self.FORM_TITLE).send_keys(title)
        if description:
            self.driver.find_element(*self.FORM_DESCRIPTION).send_keys(description)
        
        # Pilih status menggunakan dropdown
        from selenium.webdriver.support.ui import Select
        select = Select(self.driver.find_element(*self.FORM_STATUS))
        select.select_by_value(status)
        
        # Submit form
        self.driver.find_element(*self.BTN_SUBMIT).click()
        
        # Tunggu alert muncul (indikasi sukses)
        self.wait.until(EC.presence_of_element_located(self.ALERT_SUCCESS))
    
    def reset_form(self):
        """Reset form ke keadaan awal"""
        self.driver.find_element(*self.BTN_RESET).click()
    
    def get_form_values(self):
        """Mendapatkan nilai current dari form"""
        return {
            'title': self.driver.find_element(*self.FORM_TITLE).get_attribute('value'),
            'description': self.driver.find_element(*self.FORM_DESCRIPTION).get_attribute('value')
        }
    
    def refresh_tasks(self):
        """Klik tombol refresh"""
        self.driver.find_element(*self.BTN_REFRESH).click()
        time.sleep(0.5)  # Tunggu sebentar untuk loading
    
    def get_all_tasks(self):
        """Mengembalikan list semua tugas yang ditampilkan di tabel"""
        rows = self.driver.find_elements(*self.TASKS_TABLE_ROWS)
        tasks = []
        
        for row in rows:
            cells = row.find_elements(By.TAG_NAME, "td")
            if len(cells) >= 5:
                task = {
                    'id': cells[0].text.strip(),
                    'title': cells[1].text.strip(),
                    'description': cells[2].text.strip(),
                    'status': cells[3].find_element(By.TAG_NAME, "span").text.strip(),
                    'actions': {
                        'edit': cells[4].find_element(By.CSS_SELECTOR, ".btn-warning"),
                        'delete': cells[4].find_element(By.CSS_SELECTOR, ".btn-danger")
                    }
                }
                tasks.append(task)
        
        return tasks
    
    def task_exists(self, title):
        """Memeriksa apakah tugas dengan judul tertentu ada di tabel"""
        tasks = self.get_all_tasks()
        return any(task['title'] == title for task in tasks)
    
    def get_task_by_title(self, title):
        """Mendapatkan data tugas berdasarkan judul"""
        tasks = self.get_all_tasks()
        for task in tasks:
            if task['title'] == title:
                return task
        return None
    
    def click_edit_task(self, title):
        """Klik tombol edit untuk tugas dengan judul tertentu"""
        task = self.get_task_by_title(title)
        if task:
            task['actions']['edit'].click()
            # Tunggu modal muncul
            self.wait.until(EC.visibility_of_element_located(self.MODAL_EDIT))
        else:
            raise Exception(f"Task with title '{title}' not found")
    
    def click_delete_task(self, title):
        """Klik tombol delete untuk tugas dengan judul tertentu"""
        task = self.get_task_by_title(title)
        if task:
            task['actions']['delete'].click()
    
    def update_task_in_modal(self, title=None, description=None, status=None):
        """Update tugas melalui modal edit"""
        if title is not None:
            title_input = self.driver.find_element(*self.MODAL_TITLE)
            title_input.clear()
            title_input.send_keys(title)
        
        if description is not None:
            desc_input = self.driver.find_element(*self.MODAL_DESCRIPTION)
            desc_input.clear()
            desc_input.send_keys(description)
        
        if status is not None:
            from selenium.webdriver.support.ui import Select
            select = Select(self.driver.find_element(*self.MODAL_STATUS))
            select.select_by_value(status)
        
        self.driver.find_element(*self.BTN_SAVE_EDIT).click()
        
        # Tunggu modal tertutup dan alert muncul
        self.wait.until(EC.invisibility_of_element_located(self.MODAL_EDIT))
        self.wait.until(EC.presence_of_element_located(self.ALERT_SUCCESS))
    
    def get_alert_message(self):
        """Mendapatkan teks dari alert yang muncul"""
        alert = self.wait.until(EC.presence_of_element_located(self.ALERT_CONTAINER))
        return alert.text
    
    def is_empty_state_displayed(self):
        """Memeriksa apakah empty state ditampilkan"""
        try:
            self.driver.find_element(*self.EMPTY_STATE)
            return True
        except:
            return False
    
    def wait_for_alert_to_disappear(self):
        """Menunggu alert menghilang"""
        self.wait.until(EC.invisibility_of_element_located(self.ALERT_CONTAINER))
    
    def handle_delete_confirmation(self, accept=True):
        """Menangani dialog konfirmasi delete"""
        alert = self.wait.until(EC.alert_is_present())
        if accept:
            alert.accept()
        else:
            alert.dismiss()
```

### 9.3. Menulis Test Cases dengan Pytest

**Langkah 1: Buat File `test_frontend.py`**

```python
# tests/selenium/test_frontend.py
import pytest
import time
from pages.task_page import TaskPage

class TestTaskFlowFrontend:
    """Test suite untuk frontend TaskFlow"""
    
    @pytest.fixture(autouse=True)
    def setup(self, driver):
        """Setup sebelum setiap test"""
        self.page = TaskPage(driver)
        self.driver = driver
        # Refresh halaman untuk memastikan state bersih
        self.page.refresh_tasks()
    
    # ========== Test Cases ==========
    
    def test_page_loads_correctly(self):
        """TC-FE-001: Memastikan halaman dapat dimuat dengan benar"""
        assert "TaskFlow" in self.driver.title
        assert self.page.FORM_TITLE is not None
        print("✓ Halaman berhasil dimuat")
    
    def test_create_new_task_success(self):
        """TC-FE-002: Membuat tugas baru dengan data valid"""
        # Arrange
        task_title = f"Test Task {time.time()}"
        task_desc = "Ini adalah tugas test"
        task_status = "pending"
        
        # Act
        self.page.create_task(task_title, task_desc, task_status)
        
        # Assert
        assert self.page.task_exists(task_title), "Tugas tidak muncul di tabel"
        alert_msg = self.page.get_alert_message()
        assert "berhasil ditambahkan" in alert_msg
        print(f"✓ Tugas '{task_title}' berhasil dibuat")
    
    def test_create_task_without_title_validation(self):
        """TC-FE-003: Validasi client-side untuk field judul kosong"""
        # Act - Submit form tanpa mengisi judul
        self.driver.find_element(*self.page.BTN_SUBMIT).click()
        
        # Assert - Cek validasi HTML5 required
        title_input = self.driver.find_element(*self.page.FORM_TITLE)
        validation_message = self.driver.execute_script(
            "return arguments[0].validationMessage;", title_input
        )
        assert "isi" in validation_message.lower() or "fill" in validation_message.lower()
        print("✓ Validasi judul kosong berfungsi")
    
    def test_reset_form_functionality(self):
        """TC-FE-004: Memastikan tombol reset mengosongkan form"""
        # Arrange - Isi form dulu
        self.driver.find_element(*self.page.FORM_TITLE).send_keys("Test Reset")
        self.driver.find_element(*self.page.FORM_DESCRIPTION).send_keys("Deskripsi test")
        
        # Act
        self.page.reset_form()
        
        # Assert
        form_values = self.page.get_form_values()
        assert form_values['title'] == "", "Judul tidak tereset"
        assert form_values['description'] == "", "Deskripsi tidak tereset"
        print("✓ Form reset berfungsi")
    
    def test_edit_task_functionality(self):
        """TC-FE-005: Mengedit tugas yang sudah ada"""
        # Arrange - Buat tugas dulu
        original_title = f"Edit Test {time.time()}"
        self.page.create_task(original_title, "Deskripsi awal", "pending")
        
        # Act - Edit tugas
        self.page.click_edit_task(original_title)
        new_title = f"{original_title} (Updated)"
        self.page.update_task_in_modal(
            title=new_title,
            description="Deskripsi setelah update",
            status="completed"
        )
        
        # Assert
        assert self.page.task_exists(new_title), "Tugas dengan judul baru tidak ditemukan"
        assert not self.page.task_exists(original_title), "Tugas lama masih ada"
        
        task = self.page.get_task_by_title(new_title)
        assert task['status'] == "Completed"
        print(f"✓ Tugas berhasil diedit menjadi '{new_title}'")
    
    def test_delete_task_functionality(self):
        """TC-FE-006: Menghapus tugas"""
        # Arrange
        task_title = f"Delete Test {time.time()}"
        self.page.create_task(task_title, "Akan dihapus", "pending")
        assert self.page.task_exists(task_title), "Tugas tidak berhasil dibuat"
        
        # Act
        self.page.click_delete_task(task_title)
        self.page.handle_delete_confirmation(accept=True)
        
        # Assert - Tunggu alert sukses
        alert_msg = self.page.get_alert_message()
        assert "berhasil dihapus" in alert_msg.lower()
        assert not self.page.task_exists(task_title), "Tugas masih ada setelah dihapus"
        print(f"✓ Tugas '{task_title}' berhasil dihapus")
    
    def test_delete_task_cancelled(self):
        """TC-FE-007: Membatalkan penghapusan tugas"""
        # Arrange
        task_title = f"Cancel Delete {time.time()}"
        self.page.create_task(task_title, "Tidak jadi dihapus", "pending")
        
        # Act
        self.page.click_delete_task(task_title)
        self.page.handle_delete_confirmation(accept=False)
        
        # Assert
        time.sleep(0.5)  # Tunggu dialog tertutup
        assert self.page.task_exists(task_title), "Tugas hilang padahal delete dibatalkan"
        print("✓ Pembatalan delete berfungsi")
    
    def test_refresh_button(self):
        """TC-FE-008: Memastikan tombol refresh memuat ulang data"""
        # Arrange - Buat tugas baru via API langsung (simulasi perubahan dari luar)
        import requests
        api_url = "http://127.0.0.1:8000/api/tasks"
        new_task = {"title": "API Created Task", "description": "Dibuat via API", "status": "pending"}
        response = requests.post(api_url, json=new_task)
        assert response.status_code == 201
        
        # Act - Klik refresh
        self.page.refresh_tasks()
        
        # Assert
        assert self.page.task_exists("API Created Task"), "Tugas dari API tidak muncul setelah refresh"
        print("✓ Tombol refresh berfungsi")
    
    def test_status_filter_display(self):
        """TC-FE-009: Memastikan status badge ditampilkan dengan warna yang benar"""
        # Arrange - Buat tugas dengan berbagai status
        tasks_data = [
            ("Task Pending", "pending", "bg-warning"),
            ("Task Progress", "in-progress", "bg-info"),
            ("Task Completed", "completed", "bg-success")
        ]
        
        for title, status, _ in tasks_data:
            self.page.create_task(title, f"Test {status}", status)
            self.page.wait_for_alert_to_disappear()
        
        # Assert - Cek badge classes
        for title, _, expected_class in tasks_data:
            task = self.page.get_task_by_title(title)
            if task:
                # Cari badge di kolom status
                rows = self.driver.find_elements(*self.page.TASKS_TABLE_ROWS)
                for row in rows:
                    if title in row.text:
                        badge = row.find_element(By.CSS_SELECTOR, ".badge")
                        badge_class = badge.get_attribute("class")
                        assert expected_class in badge_class, f"Badge untuk {title} salah"
                        break
        
        print("✓ Status badge ditampilkan dengan benar")
    
    def test_modal_close_buttons(self):
        """TC-FE-010: Memastikan modal edit bisa ditutup dengan berbagai cara"""
        # Arrange
        task_title = f"Modal Test {time.time()}"
        self.page.create_task(task_title, "Test modal", "pending")
        
        # Test 1: Tutup dengan tombol X
        self.page.click_edit_task(task_title)
        self.driver.find_element(*self.page.BTN_CLOSE_MODAL).click()
        assert not self.driver.find_element(*self.page.MODAL_EDIT).is_displayed()
        print("  ✓ Modal bisa ditutup dengan tombol X")
        
        # Test 2: Tutup dengan tombol Cancel
        self.page.click_edit_task(task_title)
        self.driver.find_element(*self.page.BTN_CANCEL_EDIT).click()
        assert not self.driver.find_element(*self.page.MODAL_EDIT).is_displayed()
        print("  ✓ Modal bisa ditutup dengan tombol Cancel")
        
        # Test 3: Tutup dengan klik di luar modal
        self.page.click_edit_task(task_title)
        # Klik backdrop
        self.driver.find_element(By.CSS_SELECTOR, ".modal-backdrop").click()
        time.sleep(0.5)
        assert not self.driver.find_element(*self.page.MODAL_EDIT).is_displayed()
        print("  ✓ Modal bisa ditutup dengan klik di luar")
    
    def test_empty_state_when_no_tasks(self):
        """TC-FE-011: Memastikan empty state muncul saat tidak ada tugas"""
        # Arrange - Hapus semua tugas
        import requests
        api_url = "http://127.0.0.1:8000/api/tasks"
        tasks = requests.get(api_url).json()
        for task in tasks:
            requests.delete(f"{api_url}/{task['id']}")
        
        # Act - Refresh halaman
        self.page.refresh_tasks()
        
        # Assert
        assert self.page.is_empty_state_displayed(), "Empty state tidak muncul"
        empty_text = self.driver.find_element(*self.page.EMPTY_STATE).text
        assert "Belum ada tugas" in empty_text
        print("✓ Empty state ditampilkan dengan benar")
    
    def test_form_validation_after_reset(self):
        """TC-FE-012: Memastikan form valid setelah di-reset"""
        # Arrange - Submit form kosong (trigger validasi)
        self.driver.find_element(*self.page.BTN_SUBMIT).click()
        
        # Act - Reset form
        self.page.reset_form()
        
        # Assert - Coba submit lagi, seharusnya tidak ada validasi error
        self.driver.find_element(*self.page.BTN_SUBMIT).click()
        # Jika tidak ada exception, berarti validasi tidak muncul lagi
        print("✓ Validasi form di-reset dengan benar")
```

### 9.4. Menjalankan Selenium Tests

**Langkah 1: Jalankan Pytest**

```bash
# Masuk ke folder tests/selenium
cd tests/selenium

# Jalankan semua test
pytest test_frontend.py -v

# Jalankan test spesifik
pytest test_frontend.py::TestTaskFlowFrontend::test_create_new_task_success -v

# Jalankan dengan HTML report
pytest test_frontend.py -v --html=../../reports/frontend-test-report.html --self-contained-html

# Jalankan dalam mode headless (untuk CI/CD)
HEADLESS=true pytest test_frontend.py -v
```

**Langkah 2: Buat Script untuk Menjalankan Semua Test**

Buat file `run_all_tests.py` di root proyek:

```python
#!/usr/bin/env python3
"""Script untuk menjalankan semua automation tests"""

import subprocess
import sys
import os
import time
from datetime import datetime

def run_command(command, description):
    """Menjalankan command shell dan print output"""
    print(f"\n{'='*60}")
    print(f"▶ {description}")
    print(f"{'='*60}")
    
    result = subprocess.run(command, shell=True, capture_output=True, text=True)
    
    print(result.stdout)
    if result.stderr:
        print("ERROR:", result.stderr)
    
    return result.returncode == 0

def main():
    print("🚀 TaskFlow Automation Testing Suite")
    print(f"Started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Buat folder reports jika belum ada
    os.makedirs("reports", exist_ok=True)
    
    all_passed = True
    
    # 1. Jalankan API Tests dengan Newman
    print("\n📡 Running Backend API Tests...")
    api_passed = run_command(
        "newman run tests/postman/TaskFlow_API_Collection.json -e tests/postman/TaskFlow_Local_Environment.json --reporters cli,htmlextra --reporter-htmlextra-export reports/api-test-report.html",
        "Backend API Tests"
    )
    all_passed = all_passed and api_passed
    
    # 2. Jalankan Frontend Selenium Tests
    print("\n🖥️ Running Frontend Selenium Tests...")
    frontend_passed = run_command(
        "cd tests/selenium && pytest test_frontend.py -v --html=../../reports/frontend-test-report.html --self-contained-html",
        "Frontend Selenium Tests"
    )
    all_passed = all_passed and frontend_passed
    
    # Summary
    print(f"\n{'='*60}")
    print(f"📊 TEST SUMMARY")
    print(f"{'='*60}")
    print(f"Backend API Tests:  {'✅ PASSED' if api_passed else '❌ FAILED'}")
    print(f"Frontend UI Tests:  {'✅ PASSED' if frontend_passed else '❌ FAILED'}")
    print(f"{'='*60}")
    print(f"Overall Result:     {'✅ ALL PASSED' if all_passed else '❌ SOME TESTS FAILED'}")
    print(f"Finished at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    sys.exit(0 if all_passed else 1)

if __name__ == "__main__":
    main()
```

---

## Bagian 10: Integrasi dengan CI/CD Pipeline

### 10.1. GitHub Actions Workflow

Buat file `.github/workflows/test.yml`:

```yaml
name: TaskFlow Automation Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]
  schedule:
    - cron: '0 2 * * *'  # Run daily at 2 AM

jobs:
  backend-tests:
    runs-on: ubuntu-latest
    
    services:
      # Jalankan backend service
      backend:
        image: python:3.9-slim
        ports:
          - 8000:8000
        options: >-
          --name backend
          --workdir /app
          --volume ${{ github.workspace }}:/app
        env:
          PYTHONPATH: /app
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup Python
      uses: actions/setup-python@v4
      with:
        python-version: '3.9'
    
    - name: Install Backend Dependencies
      run: |
        cd backend
        pip install -r requirements.txt
    
    - name: Start Backend Server
      run: |
        cd backend
        uvicorn main:app --host 0.0.0.0 --port 8000 &
        sleep 5  # Wait for server to start
    
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '18'
    
    - name: Install Newman
      run: npm install -g newman newman-reporter-htmlextra
    
    - name: Run API Tests
      run: |
        newman run tests/postman/TaskFlow_API_Collection.json \
          -e tests/postman/TaskFlow_CI_Environment.json \
          --reporters cli,htmlextra \
          --reporter-htmlextra-export reports/api-test-report.html
    
    - name: Upload API Test Report
      uses: actions/upload-artifact@v3
      if: always()
      with:
        name: api-test-report
        path: reports/api-test-report.html

  frontend-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
    
    - name: Start PHP Server
      run: |
        cd frontend
        php -S 127.0.0.1:8080 &
        sleep 3
    
    - name: Setup Python
      uses: actions/setup-python@v4
      with:
        python-version: '3.9'
    
    - name: Install Selenium Dependencies
      run: |
        pip install selenium pytest pytest-html webdriver-manager
    
    - name: Run Selenium Tests
      env:
        HEADLESS: "true"
      run: |
        cd tests/selenium
        pytest test_frontend.py -v \
          --html=../../reports/frontend-test-report.html \
          --self-contained-html
    
    - name: Upload Selenium Test Report
      uses: actions/upload-artifact@v3
      if: always()
      with:
        name: frontend-test-report
        path: reports/frontend-test-report.html
    
    - name: Upload Screenshots (if failures)
      uses: actions/upload-artifact@v3
      if: failure()
      with:
        name: selenium-screenshots
        path: reports/screenshots/
```

---

## Bagian 11: Performance Testing dengan Locust

Locust adalah tool load testing berbasis Python yang mudah digunakan.

**Langkah 1: Install Locust**

```bash
pip install locust
```

**Langkah 2: Buat File `locustfile.py`**

```python
# tests/performance/locustfile.py
from locust import HttpUser, task, between

class TaskFlowUser(HttpUser):
    """Simulasi pengguna TaskFlow"""
    
    wait_time = between(1, 3)  # Waktu tunggu antar task (1-3 detik)
    
    def on_start(self):
        """Dijalankan saat user mulai"""
        self.task_ids = []
    
    @task(3)  # Weight = 3 (lebih sering dijalankan)
    def view_all_tasks(self):
        """Melihat daftar semua tugas"""
        with self.client.get("/api/tasks", catch_response=True) as response:
            if response.status_code == 200:
                response.success()
            else:
                response.failure(f"Failed: {response.status_code}")
    
    @task(2)
    def create_task(self):
        """Membuat tugas baru"""
        task_data = {
            "title": f"Load Test Task {self.environment.runner.user_count}",
            "description": "Created during load testing",
            "status": "pending"
        }
        
        with self.client.post("/api/tasks", json=task_data, catch_response=True) as response:
            if response.status_code == 201:
                task_id = response.json().get("id")
                if task_id:
                    self.task_ids.append(task_id)
                response.success()
            else:
                response.failure(f"Failed: {response.status_code}")
    
    @task(1)
    def update_task(self):
        """Memperbarui tugas yang ada"""
        if not self.task_ids:
            # Ambil task ID dari API jika belum ada
            response = self.client.get("/api/tasks")
            if response.status_code == 200 and response.json():
                self.task_ids = [task["id"] for task in response.json()]
        
        if self.task_ids:
            task_id = self.task_ids[0]
            update_data = {
                "title": f"Updated Task {task_id}",
                "description": "Updated during load test",
                "status": "in-progress"
            }
            
            with self.client.put(f"/api/tasks/{task_id}", json=update_data, catch_response=True) as response:
                if response.status_code == 200:
                    response.success()
                else:
                    response.failure(f"Failed: {response.status_code}")
    
    @task(1)
    def delete_task(self):
        """Menghapus tugas"""
        if self.task_ids:
            task_id = self.task_ids.pop()
            with self.client.delete(f"/api/tasks/{task_id}", catch_response=True) as response:
                if response.status_code == 204:
                    response.success()
                else:
                    response.failure(f"Failed: {response.status_code}")
    
    def on_stop(self):
        """Dijalankan saat user berhenti"""
        # Cleanup: hapus semua task yang dibuat
        for task_id in self.task_ids:
            self.client.delete(f"/api/tasks/{task_id}")
```

**Langkah 3: Jalankan Locust**

```bash
cd tests/performance

# Jalankan Locust web UI
locust -f locustfile.py --host=http://127.0.0.1:8000

# Atau jalankan headless (untuk CI/CD)
locust -f locustfile.py \
    --host=http://127.0.0.1:8000 \
    --headless \
    --users 100 \
    --spawn-rate 10 \
    --run-time 60s \
    --html=../../reports/performance-report.html
```

---

## Bagian 12: Best Practices dan Tips untuk Automation Testing

### 12.1. Prinsip FIRST untuk Unit Test

| Prinsip | Deskripsi |
|---------|-----------|
| **F**ast | Test harus cepat dijalankan. Jika lambat, developer enggan menjalankannya. |
| **I**ndependent | Test tidak boleh bergantung pada test lain. Bisa dijalankan dalam urutan apa pun. |
| **R**epeatable | Hasil test harus konsisten setiap kali dijalankan. |
| **S**elf-validating | Test harus memiliki asersi yang jelas, tidak perlu interpretasi manual. |
| **T**imely | Tulis test sebelum atau segera setelah kode produksi dibuat. |

### 12.2. Test Pyramid

```
        /\
       /  \      E2E Tests (sedikit, lambat, mahal)
      /____\     
     /      \    Integration Tests (sedang)
    /________\   
   /          \  Unit Tests (banyak, cepat, murah)
  /____________\ 
```

**Rekomendasi Rasio:**
- 70% Unit Tests
- 20% Integration Tests
- 10% E2E Tests

### 12.3. Tips untuk Selenium Tests

1. **Gunakan Page Object Model** - Memisahkan locator dari logika test.
2. **Gunakan Explicit Wait, bukan Thread.sleep()** - Lebih efisien dan reliable.
3. **Jangan test fungsionalitas yang sama di multiple test** - Satu skenario, satu test.
4. **Gunakan data test yang unik** - Hindari konflik antar test.
5. **Capture screenshot saat test gagal** - Memudahkan debugging.

### 12.4. Tips untuk API Tests

1. **Test happy path dan edge cases** - Jangan hanya test skenario sukses.
2. **Validasi schema response** - Pastikan struktur JSON sesuai kontrak.
3. **Test autentikasi dan otorisasi** - Pastikan endpoint terlindungi.
4. **Gunakan environment variables** - Pisahkan konfigurasi untuk dev/staging/prod.
5. **Jalankan test dalam isolated environment** - Gunakan database test terpisah.

---

## Bagian 13: Troubleshooting Masalah Umum

| Masalah | Kemungkinan Penyebab | Solusi |
|---------|---------------------|--------|
| Selenium tidak bisa menemukan elemen | Elemen belum muncul, selector berubah | Gunakan explicit wait, periksa selector |
| API test gagal karena CORS | Backend tidak mengizinkan origin frontend | Tambahkan CORS middleware di FastAPI |
| Chromedriver version mismatch | Versi Chrome berbeda dengan chromedriver | Gunakan webdriver-manager |
| Test gagal secara acak (flaky test) | Race condition, timing issue | Tambahkan wait yang tepat, hindari sleep |
| Newman timeout | Request terlalu lama | Tambahkan `--timeout-request` flag |

---

## Penutup dan Langkah Selanjutnya

Kamu telah menyelesaikan tutorial komprehensif tentang Automation Testing untuk full-stack web application. Mulai dari konsep dasar, setup proyek, testing backend API dengan Postman/Newman, testing frontend UI dengan Selenium, hingga integrasi dengan CI/CD dan performance testing.

### Rekomendasi untuk Eksplorasi Lebih Lanjut:

1. **Pelajari Cypress** - Alternatif modern untuk Selenium dengan developer experience lebih baik.
2. **Pelajari Playwright** - Tool automation dari Microsoft yang support multi-browser.
3. **Pelajari Contract Testing dengan Pact** - Untuk microservices architecture.
4. **Pelajari Visual Regression Testing dengan Percy atau Chromatic**.
5. **Pelajari Security Testing dengan OWASP ZAP**.

---
