# Aplikasi Rawat Jalan Puskesmas Padang Batung

Sistem informasi manajemen rawat jalan untuk Puskesmas Padang Batung.

## Prasyarat (Prerequisites)

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB

## Instalasi (Installation)

1. **Clone repositori:**
   ```bash
   git clone <repository-url>
   cd <project-folder>
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Instal dependensi JavaScript:**
   ```bash
   npm install
   ```

4. **Konfigurasi Lingkungan:**
   Salin `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.
   ```bash
   cp .env.example .env
   ```

5. **Generate App Key:**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi dan Seeding Database:**
   Perintah ini akan membuat tabel dan mengisi data awal (termasuk 40 data transaksi contoh untuk simulasi dashboard).
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Build Aset:**
   ```bash
   npm run build
   ```

## Pengembangan (Development)

Untuk menjalankan server pengembangan, queue, dan Vite secara bersamaan:
```bash
composer run dev
```

## Akun Demo
Setelah menjalankan seeder, Anda dapat masuk dengan akun berikut (Password: `admin123` untuk admin, atau `password123` untuk lainnya menyesuaikan seeder):
- **Admin**: `admin`
- **Dokter**: `dr.ahmad`
- **Kasir**: `kasir`
- **Apoteker**: `apoteker`
- **Petugas**: `petugas`
- **Kepala**: `kepala`

## Lisensi
Proyek ini bersifat internal untuk Puskesmas Padang Batung.
