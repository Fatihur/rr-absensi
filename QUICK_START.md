# Quick Start Guide - Aplikasi Absensi Karyawan

## ⚡ Instalasi & Setup (5 Menit)

### 1. Persiapan Database

Buka MySQL dan buat database:

```sql
CREATE DATABASE absensi_karyawan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Atau via phpMyAdmin:**
- Buka phpMyAdmin
- Klik "New Database"
- Nama: `absensi_karyawan`
- Collation: `utf8mb4_unicode_ci`

### 2. Konfigurasi Environment

File `.env` sudah dikonfigurasi. Pastikan settingan database sesuai:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_karyawan
DB_USERNAME=root
DB_PASSWORD=
```

Jika password MySQL Anda berbeda, update `DB_PASSWORD`.

### 3. Jalankan Migrations & Seeders

```bash
cd D:\PROYEK\stisla-absen\absensi-app
php artisan migrate:fresh --seed
```

**Output yang diharapkan:**
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table (XX.XXms)
... (dan seterusnya)

Seeding: Database\Seeders\RoleSeeder
Seeded:  Database\Seeders\RoleSeeder (XX.XXms)
... (dan seterusnya)
```

### 4. Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🔐 Login Credentials

### Super Admin (Akses Penuh)
```
Email: admin@absensi.com
Password: password
```
**Fitur:**
- Dashboard statistik global
- Kelola semua cabang
- Kelola semua karyawan
- Lihat laporan semua cabang

### Admin Cabang Jakarta
```
Email: admin.jakarta@absensi.com
Password: password
```
**Fitur:**
- Dashboard statistik cabang Jakarta
- Kelola jam kerja cabang
- Set lokasi & radius kantor
- Monitoring kehadiran karyawan cabang
- Validasi absensi bermasalah

### Admin Cabang Bandung
```
Email: admin.bandung@absensi.com
Password: password
```
**Fitur:**
- Sama seperti Admin Jakarta, tapi untuk cabang Bandung

### Karyawan 1
```
Email: budi@absensi.com
Password: password
```
**Data:**
- NIK: EMP001
- Nama: Budi Santoso
- Cabang: Jakarta
- Posisi: Staff

### Karyawan 2
```
Email: siti@absensi.com
Password: password
```
**Data:**
- NIK: EMP002
- Nama: Siti Rahayu
- Cabang: Jakarta
- Posisi: Staff

---

## 📊 Struktur Database

### Data Yang Sudah Ter-seed:

**3 Roles:**
1. Super Admin
2. Admin Cabang
3. Karyawan

**3 Cabang:**
1. Kantor Pusat Jakarta (-6.208763, 106.845599) - Radius 100m
2. Cabang Bandung (-6.921553, 107.608238) - Radius 150m
3. Cabang Surabaya (-7.257472, 112.752090) - Radius 120m

**5 Posisi:**
1. Manager
2. Supervisor
3. Staff
4. Operator
5. Admin

**5 Users:**
- 1 Super Admin (tanpa cabang)
- 2 Admin Cabang (Jakarta & Bandung)
- 2 Karyawan (keduanya di Jakarta)

---

## ✅ Testing Checklist

### Test 1: Login & Authorization
- [ ] Login sebagai super admin → Lihat dashboard global
- [ ] Login sebagai admin cabang → Lihat dashboard cabang saja
- [ ] Login sebagai karyawan → Lihat dashboard pribadi
- [ ] Test logout dari setiap role
- [ ] Coba akses URL lain (harus redirect ke dashboard sesuai role)

### Test 2: Dashboard Content
- [ ] Super Admin: Lihat total cabang (3), total karyawan (2)
- [ ] Admin Cabang: Lihat total karyawan cabang (Jakarta: 2, Bandung: 0)
- [ ] Karyawan: Lihat status absensi hari ini (belum ada data)

### Test 3: Navigation
- [ ] Sidebar berbeda sesuai role
- [ ] Navbar menampilkan nama user & role
- [ ] Logout button berfungsi

---

## 🚧 Fitur Yang Sudah Berjalan

✅ **Authentication System**
- Login dengan email & password
- Remember me functionality
- Auto logout jika akun non-aktif
- Audit log untuk login/logout

✅ **Authorization (Role-Based Access Control)**
- Middleware `role` untuk proteksi route
- Helper methods: `isSuperAdmin()`, `isAdminCabang()`, `isKaryawan()`
- Sidebar dinamis berdasarkan role

✅ **Dashboard Multi-Role**
- Super Admin: Statistik global + recent attendance
- Admin Cabang: Statistik cabang + monitoring
- Karyawan: Status absensi pribadi + riwayat

✅ **Database Structure**
- 9 tabel dengan relasi lengkap
- Migrations untuk semua tabel
- Seeders dengan data demo realistis

---

## 🔨 Fitur Yang Perlu Dikembangkan

### Priority HIGH (Core Features)

1. **CRUD Master Data (Super Admin)**
   - [ ] Kelola Cabang (dengan GPS picker Leaflet)
   - [ ] Kelola Posisi
   - [ ] Kelola Karyawan (form lengkap + upload foto)
   - [ ] Kelola User

2. **Pengaturan Cabang (Admin Cabang)**
   - [ ] Kelola Jam Kerja per posisi
   - [ ] Kelola Hari Libur
   - [ ] Set Lokasi & Radius (Leaflet map picker)
   - [ ] Live Monitoring Attendance

3. **Absensi (Karyawan)**
   - [ ] Absen Datang/Istirahat/Kembali/Pulang
   - [ ] GPS validation (Haversine formula)
   - [ ] Face Recognition (face-api.js)
   - [ ] Photo capture & upload
   - [ ] Riwayat absensi

### Priority MEDIUM

4. **Cuti/Izin/Sakit**
   - [ ] Form pengajuan (karyawan)
   - [ ] Upload attachment
   - [ ] Approval workflow (admin cabang)

5. **Laporan & Export**
   - [ ] Laporan kehadiran dengan filter
   - [ ] Export ke Excel
   - [ ] Export ke PDF

### Priority LOW

6. **Additional Features**
   - [ ] Audit Log Viewer
   - [ ] Backup & Restore Database
   - [ ] User Profile Management

---

## 📁 File Structure

```
absensi-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php ✅
│   │   │   └── DashboardController.php ✅
│   │   └── Middleware/
│   │       └── RoleMiddleware.php ✅
│   └── Models/ ✅ (9 models)
│
├── database/
│   ├── migrations/ ✅ (9 migrations)
│   └── seeders/ ✅ (4 seeders)
│
├── public/
│   └── stisla/ ✅ (Template assets)
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php ✅
│       │   └── partials/ ✅
│       ├── auth/
│       │   └── login.blade.php ✅
│       └── dashboard/ ✅ (3 views)
│
├── routes/
│   └── web.php ✅
│
├── .env ✅ (Configured)
├── README_SETUP.md ✅ (Full documentation)
├── DEVELOPMENT_GUIDE.md ✅ (Dev guide)
└── QUICK_START.md ✅ (This file)
```

---

## 🐛 Troubleshooting

### Error: "could not find driver"
**Solusi:**
- Pastikan extension `php_pdo_mysql` dan `php_mysqli` enabled di `php.ini`
- Restart web server/terminal setelah edit php.ini

### Error: "Base table or view not found"
**Solusi:**
```bash
php artisan migrate:fresh --seed
```

### Error: "Access denied for user"
**Solusi:**
- Cek username & password MySQL di `.env`
- Pastikan MySQL service running

### Login tidak bisa / redirect terus
**Solusi:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### CSS/JS tidak muncul
**Solusi:**
- Pastikan folder `public/stisla` ada dan berisi assets
- Clear browser cache (Ctrl+Shift+R)

---

## 📞 Development Commands

```bash
# Migration
php artisan migrate               # Run migrations
php artisan migrate:fresh         # Drop all & migrate
php artisan migrate:fresh --seed  # Drop, migrate, seed

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Development
php artisan serve                 # Start server
php artisan tinker                # Interactive console
php artisan route:list            # List all routes
```

---

## 📚 Documentation Files

1. **README_SETUP.md** - Full setup guide & features overview
2. **DEVELOPMENT_GUIDE.md** - Development guide & next steps
3. **QUICK_START.md** - This file (quick setup)
4. **prd.md** - Product Requirements Document (di parent folder)

---

## 🎯 Next Steps

Setelah berhasil login dan test semua role:

1. Baca `DEVELOPMENT_GUIDE.md` untuk detail implementasi selanjutnya
2. Mulai develop dari CRUD Master Data (Branch, Position, Employee)
3. Install packages tambahan (Laravel Excel, DomPDF)
4. Implementasi GPS & Face Recognition

---

**Happy Coding! 🚀**

Jika ada pertanyaan atau error, cek `DEVELOPMENT_GUIDE.md` atau troubleshooting di atas.
