# 🎭 Playwright Automation: Master Guide (Edisi Mahasiswa SQA)
Selamat datang di era baru QA Automation! Dokumen ini dirancang untuk Mahasiswa/Pemula yang ingin bertransisi dari **Selenium IDE** (Low-Code) ke **Playwright** (Professional Code-based Testing).

---

## 🌟 1. Mengapa Playwright?
Dibanding Selenium, Playwright membawa fitur "Dewa" yang membuat hidup tester lebih mudah:
*   **Auto-Waiting**: Tidak perlu lagi `pause` atau `waitForElement`. Playwright menunggu elemen siap secara otomatis.
*   **Zero Flakiness**: Jauh lebih stabil dalam menangani aplikasi modern (Vite, Next.js, PHP Bootstrap).
*   **Trace Viewer**: Rekaman detail kegagalan (video, screenshot, bahkan network logs) tanpa konfigurasi rumit.
*   **Codegen**: Fitur rekam yang menghasilkan kode kualitas tinggi, bukan sekedar "klik-klik".

---

## 🛠️ 2. Step-by-Step Instalasi (Windows)

### Tahap A: Node.js (Jantungnya Playwright)
Playwright berjalan di atas Node.js. Pastikan Anda punya ini:
1.  Download **Node.js (LTS Version)** di [nodejs.org](https://nodejs.org/).
2.  Install seperti biasa (Next-Next-Finish).
3.  Cek di Terminal (CMD/PowerShell):
    ```bash
    node -v
    npm -v
    ```

### Tahap B: Inisialisasi Proyek Playwright
Buka folder proyek Anda (misal di VS Code):
1.  Buka Terminal di VS Code.
2.  Jalankan perintah ajaib ini:
    ```bash
    npm init playwright@latest
    ```
3.  Pilih opsi berikut (TEKAN ENTER):
    *   Where to put tests? `tests`
    *   Add a GitHub Actions workflow? `No` (nanti saja)
    *   Install Playwright browsers? `Yes` (Wajib!)

---

## 📂 3. Mengenal Struktur Folder
Setelah install, Anda akan melihat beberapa folder baru:
*   `tests/`: Tempat Anda menyimpan file `.spec.js`. Ini adalah file skenario Anda.
*   `tests-examples/`: Contoh-contoh test dari Playwright (bisa dihapus nanti).
*   `playwright.config.js`: File konfigurasi (Base URL, Browser apa saja yang dites).
*   `test-results/`: Akan muncul otomatis berisi bukti error (Video/Trace) jika test gagal.

---

## ⚙️ 4. Konfigurasi Awal (Recommended)
Buka file `playwright.config.js`. Cari bagian `use` dan sesuaikan:

```javascript
// playwright.config.js
module.exports = defineConfig({
  // ...
  use: {
    baseURL: 'http://localhost:8000', // Sesuaikan dengan URL SmartLib Anda
    trace: 'on-first-retry',          // Rekam jejak jika gagal
    screenshot: 'only-on-failure',    // Ambil foto jika gagal
    video: 'retain-on-failure',      // Rekam video jika gagal
  },
  // ...
});
```

---

## 📝 5. Menulis Test Case Pertama (Login SmartLib)
Buat file baru di dalam folder `tests/` bernama `auth.spec.js`.

> [!TIP]
> Playwright menggunakan **Locators** yang fokus pada pengalaman pengguna (role, label, text) daripada sekedar CSS selector yang membingungkan.

```javascript
const { test, expect } = require('@playwright/test');

test('TC-AUTH-001: Login Berhasil Mahasiswa', async ({ page }) => {
  // 1. Buka halaman login
  await page.goto('/login.php');

  // 2. Isi form (Menggunakan Locator id sebagai fallback)
  await page.locator('#email').fill('andi@student.polman.id');
  await page.locator('#password').fill('smartlib123');

  // 3. Klik tombol Masuk
  await page.locator('#btnLogin').click();

  // 4. Verifikasi Berhasil (Assetion)
  await expect(page).toHaveURL(/student\/dashboard.php/);
  await expect(page.locator('.stat-label').first()).toContainText('Buku Dipinjam');
});
```

---

## 🚀 6. Fitur "Cheat Sheet" untuk Mahasiswa

### A. Codegen (The Magic Recorder)
Anda belum jago coding? Gunakan **Codegen**. Playwright akan membuka browser, Anda tinggal klik-klik, dan dia akan **menulis kodenya untuk Anda**.
```bash
npx playwright codegen http://localhost:8000
```

### B. UI Mode (Visual Dashboard)
Ingin melihat test berjalan satu per satu dengan antarmuka yang keren?
```bash
npx playwright test --ui
```

### C. Trace Viewer (Forensik Error)
Jika test Anda gagal, jalankan ini untuk melihat "rekaman CCTV" pengetesan Anda:
```bash
npx playwright show-trace path/to/trace.zip
```

---

## 💡 7. Hal Penting untuk Diketahui (SQA Guidelines)

### 1. Locator Strategy
Prioritas memilih element di Playwright:
1.  `page.getByRole()` -> (button, heading, checkbox) - **Paling Stabil**
2.  `page.getByLabel()` -> (form fields)
3.  `page.getByText()` -> (notifikasi, label)
4.  `page.locator('#id')` -> (Gunakan jika tidak ada label/role)

### 2. Auto-Waiting
Anda **TIDAK PERLU** menggunakan `await page.waitForTimeout(3000)`. Playwright secara otomatis menunggu elemen muncul, stabil, dan bisa diklik. Menggunakan timeout manual dianggap *Bad Practice*.

### 3. Assertions
Gunakan `expect(element).toBeVisible()` atau `expect(element).toHaveText()`. Assertions di Playwright bersifat "Asynchronous", artinya dia akan mencoba mengecek berkali-kali sampai berhasil (default timeout 5 detik).

---

## 🏃 8. Cara Menjalankan Test
*   Jalankan semua test: `npx playwright test`
*   Jalankan test di satu file: `npx playwright test tests/auth.spec.js`
*   Jalankan dengan browser terlihat: `npx playwright test --headed`

---

## 🏗️ 9. Skenario Lanjutan: Manajemen Buku (Librarian)
Setelah login sebagai Pustakawan, tugas utamanya adalah mengelola koleksi. Mari kita buat script untuk menambah buku baru.

> [!NOTE]
> Perhatikan bagaimana kita menggunakan `.selectOption()` untuk elemen `<select>` standar.

```javascript
test('TC-BOOK-001: Tambah Buku Baru Berhasil', async ({ page }) => {
  // Login dulu (atau gunakan Session Storage untuk lebih cepat)
  await page.goto('/login.php');
  await page.locator('#email').fill('pustakawan@smartlib.id');
  await page.locator('#password').fill('smartlib123');
  await page.locator('#btnLogin').click();

  // Buka halaman tambah buku
  await page.goto('/librarian/book_add.php');

  // Isi data buku
  await page.locator('name=title').fill('Mastering Playwright for Beginners');
  await page.locator('name=author').fill('Antigravity Architect');
  await page.locator('name=isbn').fill('978-888-777-666');
  
  // Memilih kategori dari dropdown standar
  await page.locator('name=category').selectOption({ label: 'Pengembangan Web' });
  
  await page.locator('name=total_copies').fill('5');
  await page.locator('css=button[type=submit]').click();

  // Verifikasi SweetAlert atau Flash Message muncul
  await expect(page.locator('.alert-success')).toBeVisible();
  await expect(page.locator('.alert-success')).toContainText('berhasil ditambahkan');
});
```

---

## ⚡ 10. Skenario Kompleks: Sirkulasi & Custom UI (Tom Select)
Halaman `loan_add.php` menggunakan **Tom Select** (dropdown dengan pencarian). Playwright tidak bisa menggunakan `.selectOption()` pada elemen ini karena library Tom Select menyembunyikan `<select>` asli.

**Cara Menanganinya:** Kita harus mensimulasikan klik user pada UI Tom Select.

```javascript
test('TC-LOAN-001: Peminjaman dengan Searchable Dropdown', async ({ page }) => {
  await page.goto('/librarian/loan_add.php');

  // 1. Interaksi dengan Tom Select Mahasiswa
  // Klik area pencarian
  await page.locator('.ts-control').first().click(); 
  // Ketik nama mahasiswa
  await page.keyboard.type('Andi');
  // Pilih hasil pertama yang muncul
  await page.locator('.ts-dropdown .option').first().click();

  // 2. Interaksi dengan Tom Select Buku
  await page.locator('.ts-control').last().click();
  await page.keyboard.type('Clean Code');
  await page.locator('.ts-dropdown .option').first().click();

  // 3. Submit
  await page.locator('button[type=submit]').click();
  
  await expect(page).toHaveURL(/librarian\/loans.php/);
});
```

---

## 🧭 11. Skenario Mahasiswa: Katalog & Filter
Mahasiswa biasanya melakukan pencarian buku. Mari kita uji fungsionalitas filternya.

```javascript
test('TC-CAT-002: Pencarian Buku di Katalog Mahasiswa', async ({ page }) => {
  await page.goto('/student/catalog.php');

  // Cari buku "rekayasa"
  await page.locator('name=q').fill('rekayasa');
  await page.keyboard.press('Enter');

  // Pastikan hasil yang muncul mengandung teks "rekayasa"
  const firstBook = page.locator('.book-card').first();
  await expect(firstBook).toContainText(/rekayasa/i);
});
```

---

## 🏛️ 12. Tips Pro: Page Object Model (POM) Sederhana
Jika aplikasi Anda punya 100 fitur, jangan tulis `page.goto` di setiap file. Gunakan konsep **POM**.

**Analoginya**: Kita buat "Kamus Alamat" sehingga jika alamat sebuah tombol berubah, kita cukup update di satu tempat saja.

**Contoh Struktur File:**
1. `models/LoginPage.js` (Berisi fungsi `login()`)
2. `tests/auth.spec.js` (Memanggil fungsi dari model tersebut)

---

## 🏁 Kesimpulan: Menuju SQA Professional
Playwright bukan hanya tool, tapi standard industri. Dengan menguasai fitur-fitur di atas, Anda sudah selangkah lebih maju dibanding tester tradisional.

| Challenge Level | Task |
| :--- | :--- |
| **Pebble** | Berhasil install & run Login Test |
| **Mountain** | Berhasil handle Tom Select Sirkulasi |
| **Galaxy** | Mengimplementasikan POM untuk seluruh modul |

---
*Dibuat oleh Antigravity Architect untuk Masa Depan SQA Indonesia.*
