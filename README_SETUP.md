# Aplikasi Absensi Karyawan - Laravel 12

Aplikasi absensi karyawan berbasis web dengan fitur GPS tracking dan Face Recognition menggunakan Laravel 12 dan template Stisla.

## Fitur Utama

### Berdasarkan Role:

#### Super Admin
- Dashboard global dengan statistik seluruh cabang
- Kelola seluruh user (Super Admin, Admin Cabang, Karyawan)
- Kelola master data: cabang, posisi, karyawan
- Lihat laporan kehadiran semua cabang
- Audit log semua aktivitas
- Backup & restore database

#### Admin Cabang
- Kelola jam kerja per cabang dan posisi
- Kelola hari libur cabang
- Set lokasi kantor & radius absensi (Leaflet Maps)
- Monitoring live attendance karyawan
- Validasi absensi bermasalah (approve/reject/koreksi)
- Kelola pengajuan cuti/izin/sakit
- Laporan absensi cabang

#### Karyawan
- Absensi (Datang, Istirahat, Masuk, Pulang) dengan:
  - GPS validation
  - Face Recognition (face-api.js dengan smile detection)
- Riwayat absensi pribadi
- Pengajuan cuti/izin/sakit
- Lihat status jam kerja hari ini
- Lihat lokasi kantor & radius

## Teknologi

- **Backend**: Laravel 12
- **Database**: MySQL
- **Frontend**: Stisla Admin Template (Bootstrap 4)
- **Maps**: Leaflet.js
- **Face Recognition**: face-api.js
- **Export**: Laravel Excel, DomPDF

## Requirements

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Node.js & NPM (optional, untuk compile assets)
- Web Server (Apache/Nginx) atau Laravel Serve

## Installation

### 1. Clone atau Copy Project

```bash
cd D:\PROYEK\stisla-absen\absensi-app
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

Copy file `.env.example` ke `.env` (sudah dilakukan):

```bash
cp .env.example .env
```

Update konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_karyawan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Buat Database

Buat database MySQL dengan nama `absensi_karyawan`:

```sql
CREATE DATABASE absensi_karyawan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau melalui phpMyAdmin / MySQL Workbench

### 6. Jalankan Migrations & Seeders

```bash
php artisan migrate --seed
```

Command ini akan:
- Membuat semua tabel (users, roles, branches, positions, employees, attendances, dll)
- Mengisi data awal (roles, cabang, posisi, user demo)

### 7. Setup Storage Link

```bash
php artisan storage:link
```

### 8. Set Permission (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

### 9. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

## Default Login Credentials

### Super Admin
- Email: `admin@absensi.com`
- Password: `password`

### Admin Cabang Jakarta
- Email: `admin.jakarta@absensi.com`
- Password: `password`

### Admin Cabang Bandung
- Email: `admin.bandung@absensi.com`
- Password: `password`

### Karyawan (Contoh)
- Email: `budi@absensi.com`
- Password: `password`

- Email: `siti@absensi.com`
- Password: `password`

## Struktur Database

### Tabel Utama:
1. **users** - Data user/akun login
2. **roles** - Role/peran (super_admin, admin_cabang, karyawan)
3. **branches** - Data cabang dengan lokasi GPS
4. **positions** - Posisi/jabatan karyawan
5. **employees** - Data lengkap karyawan
6. **work_schedules** - Jadwal jam kerja per cabang/posisi
7. **holidays** - Data hari libur per cabang
8. **attendances** - Record absensi lengkap dengan GPS & foto
9. **leave_requests** - Pengajuan cuti/izin/sakit
10. **audit_logs** - Log aktivitas sistem

## Fitur yang Akan Dikembangkan

### Phase 1 (In Progress)
- [x] Setup project & database
- [x] Authentication & Authorization
- [ ] Dashboard untuk semua role
- [ ] Modul Kelola User (Super Admin)
- [ ] Modul Kelola Cabang (Super Admin)
- [ ] Modul Kelola Posisi (Super Admin)
- [ ] Modul Kelola Karyawan (Super Admin)

### Phase 2
- [ ] Modul Jam Kerja (Admin Cabang)
- [ ] Modul Hari Libur (Admin Cabang)
- [ ] Set Lokasi & Radius Kantor (Leaflet Maps)
- [ ] Monitoring Live Attendance

### Phase 3
- [ ] Modul Absensi Karyawan
  - [ ] GPS Validation (Haversine Formula)
  - [ ] Face Recognition (face-api.js)
  - [ ] Smile Detection
  - [ ] Photo Upload & Storage
- [ ] Riwayat Absensi

### Phase 4
- [ ] Modul Cuti/Izin/Sakit
  - [ ] Form Pengajuan
  - [ ] Upload Attachment
  - [ ] Approval Workflow
- [ ] Validasi Absensi Bermasalah

### Phase 5
- [ ] Laporan & Rekap
  - [ ] Export Excel
  - [ ] Export PDF
  - [ ] Filter Multi-parameter
- [ ] Audit Log Viewer
- [ ] Backup & Restore Database

## Struktur Folder

```
absensi-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Models/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── stisla/         # Template assets
│   └── storage/        # Storage link
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       └── pages/
└── routes/
    └── web.php
```

## Development Roadmap

1. ✅ Setup Laravel 12
2. ✅ Copy Stisla Template
3. ✅ Database Structure & Migrations
4. ✅ Models & Relationships
5. ✅ Seeders (Initial Data)
6. ⏳ Authentication System
7. ⏳ Authorization Middleware
8. ⏳ Controllers & Views
9. ⏳ GPS & Face Recognition Integration
10. ⏳ Reporting System

## Troubleshooting

### Error: SQLSTATE[HY000] [2002]
- Pastikan MySQL server sudah running
- Cek konfigurasi DB_HOST, DB_PORT di `.env`

### Error: Class 'Role' not found
- Jalankan: `composer dump-autoload`

### Storage Link Error
- Manual create: `mklink /D public\storage ..\storage\app\public`

### Permission Denied (Linux)
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Support & Documentation

Untuk informasi lebih lanjut, lihat:
- **PRD**: `prd.md` di root folder
- **Laravel Docs**: https://laravel.com/docs/12.x
- **Stisla Docs**: https://getstisla.com/docs

## License

This project is private and confidential.
