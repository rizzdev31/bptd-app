<div align="center">

  <img src="public/assets/logo-kemenhub.png" alt="Logo Kementerian Perhubungan" width="110" style="margin-bottom: 12px;" />

  # SISTEM INFORMASI MANAJEMEN PERSEDIAAN ATK
  ### Balai Pengelola Transportasi Darat (BPTD) Kelas II Jawa Timur
  **Direktorat Jenderal Perhubungan Darat — Kementerian Perhubungan Republik Indonesia**

  <p align="center">
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" /></a>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" /></a>
    <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" /></a>
    <a href="https://mysql.com"><img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" /></a>
    <img src="https://img.shields.io/badge/Tests-80%20Passed-10B981?style=for-the-badge&logo=githubactions&logoColor=white" alt="80 Passed Tests" />
    <img src="https://img.shields.io/badge/License-BPTD%20Jatim-0B2341?style=for-the-badge" alt="License" />
  </p>

  <p align="center">
    <i>Solusi digital terintegrasi untuk pengelolaan rantai pasok Alat Tulis Kantor (ATK), transparansi pengadaan, otomatisasi konversi satuan kemasan, pencatatan mutasi SBPB, kontrol stok fisik, serta audit trail komprehensif.</i>
  </p>

</div>

---

## 📌 Gambaran Umum (Overview)

Aplikasi **SIM Persediaan ATK BPTD Kelas II Jawa Timur** dibangun secara khusus untuk menjawab kebutuhan operasional pengelolaan barang habis pakai di lingkungan dinas perhubungan. Sistem ini mengintegrasikan seluruh siklus hidup barang persediaan, mulai dari perencanaan kebutuhan, pembelian rekanan (PO), penerimaan barang (BAPHP), konversi rasio kemasan (Box/Rim ke Pcs/Lembar), pengeluaran berbasis surat dinas (SBPB), rekonsiliasi opname fisik, hingga ekspor laporan manajerial siap cetak A4.

---

## ✨ Fitur Unggulan (Key Features)

### 1. 📦 Multi-Unit Smart Conversion Engine (Mesin Konversi Satuan Pintar)
- **Zero Ambiguity Purchase**: Pengadaan barang dapat dilakukan dalam satuan kemasan besar (*Box, Rim, Pack, Lusin*) maupun eceran (*Pcs, Lembar, Buku*).
- **Kalkulasi Fisik Real-time**: Menampilkan otomatis jumlah unit fisik yang masuk ke gudang (`Kuantitas Beli × Rasio Konversi`) dan harga modal fisik per satuan terkecil (`Harga Beli ÷ Rasio`).
- **One-Click Master Presets**: Tersedia 6 preset standar ATK kedinasan (1 Rim = 500 Lembar, 1 Box = 12 Pcs, 1 Box = 10 Pcs, 1 Pack = 50 Lembar, 1 Pack = 10 Buku, 1 Pcs = 1 Pcs).
- **Kartu Simulasi & Edukasi**: Simulasi interaktif langsung pada form master barang untuk memvisualisasikan bagaimana stok disimpan dan didistribusikan.

### 2. 🛒 Pengadaan Barang & Penerimaan Fisik (Procurement & Direct Stock In)
- **Siklus Lengkap Purchase Order (PO)**: Alur status jelas: `Draft` ➔ `Dipesan (Ordered)` ➔ `Diterima Fisik (Received)` / `Dibatalkan (Cancelled)`.
- **Penerimaan Langsung (Direct Stock In)**: Fasilitas pencatatan cepat barang masuk tanpa melalui alur PO formal untuk kebutuhan mendesak.
- **Pencetakan Dokumen Resmi Dinas**: 
  - Surat Pesanan Pengadaan (PO) standar dinas perhubungan.
  - Berita Acara Penerimaan Hasil Pekerjaan (BAPHP) otomatis berformat A4 lengkap dengan tanda tangan rekanan dan pejabat penerima.

### 3. 📤 Distribusi & Surat Bukti Pengeluaran Barang (SBPB / Stock Out)
- **Validasi Stok Real-Time**: Mencegah pengeluaran barang melebihi kuantitas fisik yang tersedia di gudang.
- **Fleksibilitas Permintaan Pegawai**: Pegawai atau unit kerja dapat mengajukan permintaan dalam satuan eceran maupun boks.
- **Nomor Dokumen Otomatis**: Penomoran resmi dinas otomatis untuk setiap SBPB (`SBPB/YYYYMM/XXXX`).
- **Dokumen SBPB Siap Cetak**: Format cetak standar dinas dengan rincian penerima, unit kerja, dan daftar rincian barang.

### 4. ⚖️ Kontrol Stok & Rekonsiliasi Fisik (Stock Control)
- **Penyesuaian Stok (Stock Adjustment)**: Koreksi saldo stok akibat barang rusak, cacat pabrik, kedaluwarsa, atau selisih administratif dengan keterangan berita acara.
- **Stock Opname Berkala**: Formulir pencatatan opname fisik berkala, mendeteksi selisih buku vs fisik secara akurat sebelum saldo disesuaikan.
- **Buku Besar Kartu Stok (Stock Ledger)**: Pencatatan mutasi stok per barang (*running balance*) dengan narasi transparan per transaksi.

### 5. 📊 Laporan Terpadu & Ekspor Excel Siap Cetak A4 (Reporting)
- **5 Tab Laporan Komprehensif**:
  1. *Laporan Posisi Stok Gudang*
  2. *Laporan Mutasi Barang Keluar (SBPB)*
  3. *Laporan Mutasi Penerimaan Barang*
  4. *Laporan Pengadaan Barang (PO)*
  5. *Laporan Distribusi per Unit Kerja*
- **Ekspor Excel Canggih (PhpSpreadsheet)**:
  - Header instansi resmi & metadata filter tanggal.
  - Border tabel rapi, alignment proporsional, dan format angka Rupiah standar akuntansi.
  - Pengaturan cetak otomatis (*Fit to Page Width A4*) tanpa perlu setting ulang margin di Excel.
- **Ekspor CSV & Mode Cetak Langsung (Print Preview)**.

### 6. 🛡️ Audit Trail & Jejak Digital (Audit Logs)
- Rekam jejak digital terenkripsi untuk setiap tindakan penting pengguna: *Login, Logout, Tambah/Ubah Data Barang, Penerimaan PO, Transaksi SBPB, Adjustment Stok, dan Perubahan Hak Akses*.
- Menyimpan alamat IP, User Agent browser, waktu presisi, serta *payload metadata* (nilai sebelum vs sesudah perubahan).

### 7. 🔐 Role-Based Access Control (RBAC) Matriks Modular
- Menu konfigurasi role dinamis (`/settings/roles`) dengan kontrol perizinan per modul.
- Proteksi bawaan untuk 4 Role Sistem: **Superadmin**, **Petugas ATK**, **Pimpinan**, dan **Pegawai**.
- Dukungan pembuatan *Custom Role* baru sesuai struktur organisasi instansi.

### 8. 🎨 UI/UX Khas Kemenhub & Responsif
- **Identitas Visual Resmi**: Palet warna Navy BPTD (`#0b2341`), Aksen Emas (`#eab308`), dan Biru Navigasi (`#2563eb`).
- **Sticky Navbar & Fixed Viewport Layout**: Header tetap terkunci saat konten digulir, tata letak seimbang dan responsif di berbagai resolusi layar.
- **Interactive Floating Toast**: Notifikasi mengambang dengan *timer shrinking progress bar* yang anti-tertutup header.
- **Glassmorphism Confirmation Modals**: Dialog interaktif dengan backdrop blur modern untuk pencegahan aksi tidak disengaja.

---

## 🛠️ Arsitektur & Teknologi (Tech Stack)

| Komponen | Teknologi yang Digunakan |
| :--- | :--- |
| **Backend Framework** | [Laravel 12.x](https://laravel.com) |
| **Bahasa Pemrograman** | PHP 8.2 / 8.3 / 8.4 |
| **Database Engine** | MySQL 8.0+ / MariaDB 10.4+ |
| **Frontend Template** | Laravel Blade Components + Tailwind CSS v4 |
| **Interaktivitas UI** | Vanilla Reactive ES6+ / Fetch API / SweetAlert-style Glass Modals |
| **Ekspor Spreadsheet** | [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) (`phpoffice/phpspreadsheet`) |
| **Ikon & Desain** | Lucide Icons & Heroicons SVG |
| **Testing Framework** | PHPUnit & Laravel Feature Test Suites (80 Tests / 295 Assertions) |

---

## 🚀 Panduan Instalasi (Getting Started)

### 1. Prasyarat Sistem
- PHP >= 8.2 dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`, `zip`, `gd`.
- Composer >= 2.x
- MySQL Server >= 8.0
- Node.js & NPM (opsional jika ingin build aset frontend)

### 2. Kloning Repositori
```bash
git clone https://github.com/rizzdev31/bptd-app.git
cd bptd-app
```

### 3. Instalasi Dependensi PHP
```bash
composer install
```

### 4. Konfigurasi Environment (`.env`)
Salin file konfigurasi contoh dan buat application key:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan parameter koneksi basis data pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db-bptd
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeding Data Awal
Eksekusi migrasi tabel dan pengisian data master bawaan (Role, Akun Awal, Unit Kerja, Master Barang, dan Kategori):
```bash
php artisan migrate --seed
```

### 6. Jalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui peramban di: **`http://localhost:8000`**

---

## 🔑 Kredensial Pengguna Bawaan (Default Accounts)

| Role | Username / NIP | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `superadmin` / `198501012010121001` | `password123` | Akses Penuh Sistem, Manajemen Role, Audit Log, Pegawai, & Master Data |
| **Petugas ATK** | `petugas` / `199203152018011002` | `password123` | Operasional Gudang, Pengadaan, SBPB, Stock Control, & Laporan |
| **Pimpinan** | `pimpinan` / `197805122003121001` | `password123` | Monitoring Dashboard, Laporan Eksekutif, & Audit Mutasi |
| **Pegawai (ASN)** | `pegawai` / `199508202020121003` | `password123` | Permintaan Barang (SBPB) & Monitoring Penggunaan Pribadi |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Seluruh modul inti dilindungi oleh rangkaian pengujian fitur (*Feature Tests*) untuk menjamin stabilitas fungsionalitas dan mencegah regresi:

```bash
php artisan test
```

### Ringkasan Hasil Pengujian:
```text
   PASS  Tests\Feature\EmployeeTest (6 tests)
   PASS  Tests\Feature\ExampleTest (5 tests)
   PASS  Tests\Feature\ItemTest (7 tests)
   PASS  Tests\Feature\ProcurementTest (12 tests)
   PASS  Tests\Feature\ReportTest (10 tests)
   PASS  Tests\Feature\RolePermissionTest (8 tests)
   PASS  Tests\Feature\StockControlTest (6 tests)
   PASS  Tests\Feature\StockOutTest (9 tests)
   PASS  Tests\Feature\AuditTest (17 tests)

   Tests:    80 passed (295 assertions)
   Duration: ~3.6s
```

---

## 📂 Struktur Modul Aplikasi

```text
bptd-app/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/                     # Autentikasi & Sesi
│   │   ├── DashboardController.php   # Statistik Real-time & Visualisasi
│   │   ├── EmployeeController.php    # Manajemen ASN & Akun
│   │   ├── ItemController.php        # Master ATK & Rasio Konversi
│   │   ├── RolePermissionController.php # Matriks RBAC
│   │   └── Inventory/
│   │       ├── ProcurementController.php  # PO & Direct Stock In
│   │       ├── StockOutController.php     # SBPB & Mutasi Keluar
│   │       ├── StockControlController.php # Adjustment & Opname
│   │       ├── ReportController.php       # Laporan Multi-Tab
│   │       └── AuditController.php        # Jejak Digital & Aktivitas
│   ├── Models/                       # Model Eloquent Terintegrasi
│   └── Services/
│       └── ReportExcelExportService.php # Generator Spreadsheet A4 Ready
├── database/
│   ├── migrations/                   # Skema Basis Data
│   └── seeders/                      # Data Awal Standar Dinas
├── resources/views/
│   ├── layouts/                      # Shell Responsif & Sticky Navbar
│   ├── components/                   # Toast Container, Modal, Badges
│   └── inventory/                    # Antarmuka Seluruh Modul ATK
└── tests/Feature/                    # 80 Pengujian Otomatis
```

---

## 🏛️ Hak Cipta & Lisensi

Hak Cipta © 2026 **Balai Pengelola Transportasi Darat (BPTD) Kelas II Jawa Timur**  
Direktorat Jenderal Perhubungan Darat — Kementerian Perhubungan Republik Indonesia.  
*Seluruh hak cipta dilindungi undang-undang.*
