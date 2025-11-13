# Development Guide - Aplikasi Absensi Karyawan

## Status Proyek Saat Ini

### ✅ Yang Sudah Selesai:

1. **Project Setup**
   - ✅ Laravel 12 terinstall
   - ✅ Template Stisla ter-copy ke `public/stisla/`
   - ✅ Environment configuration (.env)
   - ✅ Database configuration (MySQL)

2. **Database Layer**
   - ✅ Semua migrations (9 tabel):
     - users, roles, branches, positions, employees
     - work_schedules, holidays, attendances, leave_requests, audit_logs
   - ✅ Semua Models dengan relationships lengkap
   - ✅ Seeders dengan data demo

3. **Backend Core**
   - ✅ AuthController (login, logout dengan audit log)
   - ✅ DashboardController (3 role: super_admin, admin_cabang, karyawan)
   - ✅ RoleMiddleware untuk authorization
   - ✅ Routes structure

4. **Views Foundation**
   - ✅ Layout structure (layouts/app.blade.php)
   - ⏳ Partials (navbar, sidebar, footer) - Need to create
   - ⏳ Auth views (login) - Need to create
   - ⏳ Dashboard views - Need to create

---

## Langkah-Langkah Setup & Testing

### 1. Persiapan Database

```bash
# Pastikan MySQL running
# Buat database
mysql -u root -p
CREATE DATABASE absensi_karyawan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 2. Jalankan Migrations & Seeders

```bash
cd D:\PROYEK\stisla-absen\absensi-app
php artisan migrate:fresh --seed
```

**Expected Output:**
- Tabel ter-create: users, roles, branches, positions, employees, work_schedules, holidays, attendances, leave_requests, audit_logs
- Data ter-seed:
  - 3 roles (super_admin, admin_cabang, karyawan)
  - 3 branches (Jakarta, Bandung, Surabaya)
  - 5 positions (Manager, Supervisor, Staff, Operator, Admin)
  - 5 users (1 super admin, 2 admin cabang, 2 karyawan)

### 3. Test Login

```bash
php artisan serve
```

Buka: http://localhost:8000

**Default Credentials:**
- Super Admin: `admin@absensi.com` / `password`
- Admin Jakarta: `admin.jakarta@absensi.com` / `password`
- Karyawan: `budi@absensi.com` / `password`

---

## Yang Perlu Dikembangkan Selanjutnya

### Priority 1: Melengkapi View Layer

#### A. Buat Partials
**File**: `resources/views/layouts/partials/navbar.blade.php`
```blade
<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <ul class="navbar-nav mr-3">
      <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
  </form>
  <ul class="navbar-nav navbar-right">
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <img alt="image" src="{{ asset('stisla/assets/img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
        <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-title">{{ Auth::user()->role->display_name ?? 'User' }}</div>
        <a href="#" class="dropdown-item has-icon">
          <i class="far fa-user"></i> Profile
        </a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </button>
        </form>
      </div>
    </li>
  </ul>
</nav>
```

**File**: `resources/views/layouts/partials/sidebar.blade.php`
```blade
<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="{{ route('dashboard') }}">Absensi App</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="{{ route('dashboard') }}">AA</a>
    </div>
    <ul class="sidebar-menu">
      <li class="menu-header">Dashboard</li>
      <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
          <i class="fas fa-fire"></i> <span>Dashboard</span>
        </a>
      </li>

      @if(Auth::user()->isSuperAdmin())
        <li class="menu-header">Master Data</li>
        <li><a class="nav-link" href="#"><i class="fas fa-building"></i> <span>Cabang</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-briefcase"></i> <span>Posisi</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-users"></i> <span>Karyawan</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-user-shield"></i> <span>User</span></a></li>
      @endif

      @if(Auth::user()->isAdminCabang())
        <li class="menu-header">Pengaturan Cabang</li>
        <li><a class="nav-link" href="#"><i class="fas fa-clock"></i> <span>Jam Kerja</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-calendar"></i> <span>Hari Libur</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-map-marker-alt"></i> <span>Lokasi Kantor</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-user-check"></i> <span>Monitoring Absensi</span></a></li>
      @endif

      @if(Auth::user()->isKaryawan())
        <li class="menu-header">Absensi</li>
        <li><a class="nav-link" href="#"><i class="fas fa-fingerprint"></i> <span>Absen Sekarang</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-history"></i> <span>Riwayat Absensi</span></a></li>
        <li><a class="nav-link" href="#"><i class="fas fa-file-alt"></i> <span>Pengajuan Izin</span></a></li>
      @endif

      <li class="menu-header">Laporan</li>
      <li><a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> <span>Laporan Kehadiran</span></a></li>
    </ul>
  </aside>
</div>
```

**File**: `resources/views/layouts/partials/footer.blade.php`
```blade
<footer class="main-footer">
  <div class="footer-left">
    Copyright &copy; {{ date('Y') }} <div class="bullet"></div> Aplikasi Absensi Karyawan
  </div>
  <div class="footer-right">
    
  </div>
</footer>
```

#### B. Buat Auth Views

**File**: `resources/views/auth/login.blade.php`
```blade
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login - {{ config('app.name') }}</title>

  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/bootstrap-social/bootstrap-social.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/css/components.css') }}">
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <h4>Aplikasi Absensi</h4>
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Login</h4></div>

              <div class="card-body">
                @if($errors->any())
                  <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                      <button class="close" data-dismiss="alert">
                        <span>&times;</span>
                      </button>
                      {{ $errors->first() }}
                    </div>
                  </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="needs-validation" novalidate="">
                  @csrf
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" 
                      value="{{ old('email') }}" required autofocus>
                    <div class="invalid-feedback">
                      Masukkan email Anda
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" 
                      tabindex="2" required>
                    <div class="invalid-feedback">
                      Masukkan password Anda
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" 
                        tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me">Ingat Saya</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
              </div>
            </div>
            
            <div class="simple-footer">
              Copyright &copy; {{ date('Y') }}
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="{{ asset('stisla/assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/popper.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/js/stisla.js') }}"></script>
  <script src="{{ asset('stisla/assets/js/scripts.js') }}"></script>
  <script src="{{ asset('stisla/assets/js/custom.js') }}"></script>
</body>
</html>
```

#### C. Buat Dashboard Views (Minimal untuk Testing)

**File**: `resources/views/dashboard/super_admin.blade.php`
**File**: `resources/views/dashboard/admin_cabang.blade.php`
**File**: `resources/views/dashboard/karyawan.blade.php`

---

### Priority 2: Implementasi CRUD (Super Admin)

1. **BranchController** - Kelola cabang dengan GPS picker
2. **PositionController** - Kelola posisi
3. **EmployeeController** - Kelola karyawan
4. **UserController** - Kelola user

### Priority 3: Implementasi Fitur Admin Cabang

1. **WorkScheduleController** - Jam kerja per posisi
2. **HolidayController** - Hari libur
3. **Branch Location** - Set GPS & radius (Leaflet)
4. **Live Monitoring** - Status kehadiran real-time

### Priority 4: Implementasi Absensi (Karyawan)

1. **AttendanceController** - Check in/out
2. GPS Integration (Leaflet + Haversine)
3. Face Recognition (face-api.js)
4. Photo upload & validation

### Priority 5: Laporan & Export

1. Export Excel (Laravel Excel)
2. Export PDF (DomPDF)
3. Audit Log Viewer

---

## Packages Yang Perlu Diinstall

```bash
# Untuk export Excel
composer require maatwebsite/excel

# Untuk export PDF
composer require barryvdh/laravel-dompdf

# Untuk image intervention (resize foto)
composer require intervention/image
```

## Assets Yang Perlu Ditambahkan

### Leaflet.js (Maps)
```html
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

### face-api.js (Face Recognition)
```html
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
```

---

## Testing Checklist

### Phase 1: Authentication
- [ ] Test login super admin
- [ ] Test login admin cabang
- [ ] Test login karyawan
- [ ] Test logout dengan audit log
- [ ] Test redirect berdasarkan role
- [ ] Test akses halaman tanpa login

### Phase 2: Dashboard
- [ ] Super Admin: Lihat statistik global
- [ ] Admin Cabang: Lihat statistik cabang
- [ ] Karyawan: Lihat statistik pribadi

### Phase 3: CRUD Operations
- [ ] Create, Read, Update, Delete untuk semua master data
- [ ] Validasi input
- [ ] Error handling

---

## Command Reference

```bash
# Migration
php artisan migrate:fresh --seed
php artisan migrate:rollback
php artisan migrate:status

# Make Files
php artisan make:controller NamaController
php artisan make:model NamaModel
php artisan make:migration create_nama_table
php artisan make:seeder NamaSeeder

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Testing
php artisan serve
php artisan tinker
```

---

## Folder Structure

```
absensi-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php ✅
│   │   │   ├── DashboardController.php ✅
│   │   │   ├── BranchController.php ⏳
│   │   │   ├── EmployeeController.php ⏳
│   │   │   ├── AttendanceController.php ⏳
│   │   │   └── ...
│   │   └── Middleware/
│   │       └── RoleMiddleware.php ✅
│   └── Models/
│       ├── User.php ✅
│       ├── Role.php ✅
│       ├── Branch.php ✅
│       ├── Employee.php ✅
│       ├── Attendance.php ✅
│       └── ...
├── database/
│   ├── migrations/ ✅ (9 files)
│   └── seeders/ ✅ (4 files)
├── public/
│   └── stisla/ ✅ (Template assets)
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php ✅
│       │   └── partials/ ⏳
│       ├── auth/ ⏳
│       └── dashboard/ ⏳
└── routes/
    └── web.php ✅
```

---

## Next Steps (Recommended Order)

1. Buat partials (navbar, sidebar, footer)
2. Buat view login
3. Test authentication flow
4. Buat dashboard views (minimal)
5. Test dashboard berdasarkan role
6. Implementasi CRUD Branch (dengan GPS picker)
7. Implementasi CRUD Position & Employee
8. Dan seterusnya...

---

## Notes

- **Default Password Semua User**: `password`
- **Foto Face Recognition**: Disimpan di `storage/app/public/faces/`
- **Foto Absensi**: Disimpan di `storage/app/public/attendances/`
- **Attachment Izin**: Disimpan di `storage/app/public/leave_attachments/`

---

**Good Luck! 🚀**
