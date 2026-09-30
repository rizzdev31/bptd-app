# BPTD Kelas II Jawa Timur - Internal Management System 

Aplikasi Internal Terintegrasi Balai Pengelola Transportasi Darat (BPTD) Kelas II Jawa Timur - Kementerian Perhubungan Republik Indonesia.

Aplikasi ini mencakup manajemen inventaris Alat Tulis Kantor (ATK), manajemen pegawai/ASN, role and permission berbasis matriks modul, serta alur approval pengeluaran barang.

---

## 🚀 Fitur Utama (Milestone 1)

1. **Autentikasi & Keamanan (Login & Sesi)**
   - Login multi-identitas: NIP, Username, atau Email.
   - Pengecekan status akun (Active/Inactive).
   - Pengamanan password terenkripsi Bcrypt.
   - Preloader selektif (Welcome login preloader, transisi login ke dashboard, dan logout preloader dengan durasi terarah).
   - Autofocus dan penanganan validasi instan tanpa layout shift.

2. **Manajemen Role & Permission Modul**
   - Menu mandiri **Role & Permission** pada sidebar (`/settings/roles`).
   - Matriks hak akses modular (Pegawai, ATK Master, Stock Out/Permintaan, Stock Control, Procurement, Report, Audit, Role Management).
   - Proteksi Role Sistem BPTD: Superadmin, Petugas ATK, Pimpinan, dan Pegawai.
   - Custom Role creator dengan validasi unik.

3. **Master Data Pegawai & Akun Sistem**
   - Master Data ASN / Pegawai BPTD (`/pegawai`).
   - Relasi ke Unit Kerja dan Role Pengguna.
   - Status aktifasi pegawai dan integrasi akun sistem.

4. **UI/UX & Desain Khas BPTD**
   - Mengacu pada identitas instansi Kemenhub: Navy (`#0b2341`), Gold (`#eab308`), dan Brand Blue (`#2563eb`).
   - Floating Toast feedback auto-dismiss dengan timer shrinking bar.
   - Custom modal konfirmasi terpusat dengan efek glassmorphism backdrop blur.
   - Desain capsule pill dan font modern *Plus Jakarta Sans*.

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: MySQL (`db-bptd`)
- **Styling**: Tailwind CSS (Tailwind v4 syntax support) + Blade Components
- **Icons**: Heroicons & Lucide SVG Icons
- **Testing**: PHPUnit / Laravel Feature Testing (32 passed tests)

---

## 📦 Instalasi & Menjalankan Lokal

1. **Clone repositori**
   ```bash
   git clone https://github.com/rizzdev31/bptd-app.git
   cd bptd-app
   ```

2. **Install dependensi Composer**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi database MySQL pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db-bptd
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi & Seeding Database**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Buka di peramban: `http://localhost:8000`

6. **Akun Bawaan (Default)**:
   - **Superadmin**: `superadmin` / `password123` (atau NIP: `198501012010121001`)
   - **Petugas ATK**: `petugas` / `password123` (atau NIP: `199203152018011002`)

---

## 🧪 Menjalankan Pengujian (Testing)

```bash
php artisan test
```

---

## 📄 Lisensi
Hak Cipta © 2026 BPTD Kelas II Jawa Timur - Kementerian Perhubungan Republik Indonesia.
