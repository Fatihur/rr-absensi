# 📋 Index - Aplikasi Absensi Karyawan

## 🚀 Mulai Dari Sini!

Selamat datang di Aplikasi Absensi Karyawan! Ini adalah aplikasi berbasis web menggunakan Laravel 12 dengan template Stisla.

---

## 📖 Dokumentasi Utama

Pilih dokumentasi sesuai kebutuhan Anda:

### 1. 🏃 **QUICK_START.md** ← **BACA INI DULU!**
**Untuk:** User yang ingin langsung menjalankan aplikasi  
**Isi:**
- Setup 5 menit (create database, migrate, run)
- Login credentials (5 akun demo)
- Testing checklist
- Troubleshooting

**Mulai dari sini jika ini pertama kali Anda setup aplikasi!**

---

### 2. 📚 **README_SETUP.md**
**Untuk:** Developer yang ingin memahami detail setup  
**Isi:**
- Feature list lengkap per role
- Installation step-by-step
- Database structure
- Development roadmap
- Command reference

---

### 3. 🔨 **DEVELOPMENT_GUIDE.md**
**Untuk:** Developer yang akan melanjutkan development  
**Isi:**
- Status proyek detail
- Code snippets untuk partials & views
- Phase-by-phase development plan
- Package yang perlu diinstall
- Next steps recommendation

---

### 4. 📊 **APLIKASI_ABSENSI_SUMMARY.md** (di parent folder)
**Untuk:** Executive summary atau quick overview  
**Isi:**
- Project overview & progress (40% complete)
- Tech stack
- Database schema ringkas
- Testing checklist
- Future enhancements

---

### 5. 📝 **prd.md** (di parent folder)
**Untuk:** Memahami requirement lengkap  
**Isi:**
- Latar belakang & tujuan
- Definisi istilah
- Fitur per role (detail)
- Aturan bisnis
- Flow diagram

---

## 🎯 Quick Navigation

### Setup & Run (5 Menit)
```bash
# 1. Buat database
CREATE DATABASE absensi_karyawan;

# 2. Migrate & seed
cd D:\PROYEK\stisla-absen\absensi-app
php artisan migrate:fresh --seed

# 3. Run
php artisan serve

# 4. Login di http://localhost:8000
Email: admin@absensi.com
Password: password
```

### Login Credentials
| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@absensi.com | password |
| Admin Cabang (Jakarta) | admin.jakarta@absensi.com | password |
| Admin Cabang (Bandung) | admin.bandung@absensi.com | password |
| Karyawan 1 | budi@absensi.com | password |
| Karyawan 2 | siti@absensi.com | password |

---

## 📁 Struktur File Penting

### Backend
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          ← Login/Logout
│   │   └── DashboardController.php     ← Dashboard 3 role
│   └── Middleware/
│       └── RoleMiddleware.php          ← Authorization
└── Models/
    ├── User.php                        ← User model + helpers
    ├── Role.php
    ├── Branch.php
    ├── Position.php
    ├── Employee.php
    ├── WorkSchedule.php
    ├── Holiday.php
    ├── Attendance.php
    ├── LeaveRequest.php
    └── AuditLog.php
```

### Database
```
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2025_11_13_021653_create_roles_table.php
│   ├── 2025_11_13_021707_create_branches_table.php
│   ├── 2025_11_13_021708_create_positions_table.php
│   ├── 2025_11_13_021709_create_employees_table.php
│   ├── 2025_11_13_021710_create_work_schedules_table.php
│   ├── 2025_11_13_021710_create_holidays_table.php
│   ├── 2025_11_13_021711_create_attendances_table.php
│   └── 2025_11_13_021712_create_leave_requests_table.php
└── seeders/
    ├── RoleSeeder.php                  ← 3 roles
    ├── BranchSeeder.php                ← 3 cabang
    ├── PositionSeeder.php              ← 5 posisi
    └── UserSeeder.php                  ← 5 users
```

### Frontend
```
resources/views/
├── layouts/
│   ├── app.blade.php                   ← Master layout
│   └── partials/
│       ├── navbar.blade.php
│       ├── sidebar.blade.php
│       └── footer.blade.php
├── auth/
│   └── login.blade.php                 ← Login page
└── dashboard/
    ├── super_admin.blade.php           ← Dashboard super admin
    ├── admin_cabang.blade.php          ← Dashboard admin cabang
    └── karyawan.blade.php              ← Dashboard karyawan
```

### Routes
```
routes/
└── web.php                             ← All routes defined here
```

### Assets
```
public/
└── stisla/                             ← 4700+ template files
    ├── assets/
    │   ├── css/
    │   ├── js/
    │   ├── img/
    │   └── modules/
    └── *.html (demo pages)
```

---

## ✅ Status Fitur

### ✅ Yang Sudah Jalan
- Authentication (Login/Logout)
- Authorization (Role-based)
- Dashboard 3 role (Super Admin, Admin Cabang, Karyawan)
- Audit log untuk login/logout
- Database structure lengkap
- UI professional (Stisla)

### ⏳ Yang Perlu Dikembangkan
- CRUD Master Data (Cabang, Posisi, Karyawan, User)
- Pengaturan Cabang (Jam Kerja, Libur, Lokasi GPS)
- Absensi dengan GPS & Face Recognition
- Cuti/Izin/Sakit dengan approval
- Laporan & Export (Excel, PDF)
- Live Monitoring
- Audit Log Viewer
- Backup/Restore

---

## 🛠️ Development Commands

```bash
# Server
php artisan serve                       # Start dev server

# Database
php artisan migrate                     # Run migrations
php artisan migrate:fresh --seed        # Reset & seed
php artisan db:seed                     # Run seeders only

# Generate
php artisan make:controller NamaController
php artisan make:model NamaModel
php artisan make:migration create_nama_table
php artisan make:seeder NamaSeeder

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Info
php artisan route:list                  # List all routes
php artisan --version                   # Laravel version
php artisan tinker                      # Interactive console
```

---

## 🐛 Troubleshooting Cepat

### Database connection error
```bash
# Cek .env
DB_CONNECTION=mysql
DB_DATABASE=absensi_karyawan
DB_USERNAME=root
DB_PASSWORD=

# Clear config cache
php artisan config:clear
```

### CSS/JS tidak muncul
```bash
# Cek folder public/stisla ada
# Clear browser cache: Ctrl+Shift+R
```

### Login redirect loop
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 📞 Butuh Bantuan?

1. **Quick issue?** → Lihat Troubleshooting di `QUICK_START.md`
2. **Development stuck?** → Baca `DEVELOPMENT_GUIDE.md`
3. **Belum setup?** → Ikuti `README_SETUP.md`
4. **Butuh overview?** → Baca `APLIKASI_ABSENSI_SUMMARY.md`

---

## 🎓 Learning Path

### Untuk User Baru (Setup & Test)
1. Baca `QUICK_START.md` (5 menit)
2. Setup database & migrate
3. Login dengan 3 role berbeda
4. Explore dashboard

### Untuk Developer (Lanjut Development)
1. Baca `DEVELOPMENT_GUIDE.md`
2. Pilih phase development (1-5)
3. Mulai coding dari CRUD Master Data
4. Test setiap fitur

### Untuk Project Manager (Understanding)
1. Baca `APLIKASI_ABSENSI_SUMMARY.md`
2. Review `prd.md` untuk detail requirement
3. Check progress vs roadmap
4. Plan next sprint

---

## 📈 Progress Tracking

**Current Status: 40% Complete**

```
Foundation (100%) ████████████████████
├─ Database        ████████████████████ 100%
├─ Auth            ████████████████████ 100%
├─ Dashboard       ████████████████████ 100%
└─ Views           ████████████████████ 100%

Features (0%)      ░░░░░░░░░░░░░░░░░░░░ 0%
├─ CRUD            ░░░░░░░░░░░░░░░░░░░░ 0%
├─ Settings        ░░░░░░░░░░░░░░░░░░░░ 0%
├─ Attendance      ░░░░░░░░░░░░░░░░░░░░ 0%
├─ Leave Mgmt      ░░░░░░░░░░░░░░░░░░░░ 0%
└─ Reports         ░░░░░░░░░░░░░░░░░░░░ 0%
```

---

## 🎯 Recommended Next Steps

### Immediate (Hari Ini)
1. ✅ Setup database
2. ✅ Run migrations & seeders
3. ✅ Test login semua role
4. ✅ Verify dashboard

### Short Term (Minggu Ini)
1. ⏳ Implementasi CRUD Cabang
2. ⏳ Implementasi CRUD Posisi
3. ⏳ Implementasi CRUD Karyawan
4. ⏳ Implementasi CRUD User

### Medium Term (Bulan Ini)
1. ⏳ Pengaturan Jam Kerja
2. ⏳ Set Lokasi GPS (Leaflet)
3. ⏳ Absensi dengan GPS validation
4. ⏳ Face Recognition integration

---

**Selamat Coding! 🚀**

**Project:** Aplikasi Absensi Karyawan  
**Framework:** Laravel 12.38.1  
**Database:** MySQL  
**Template:** Stisla  
**Created:** 2025-11-13
