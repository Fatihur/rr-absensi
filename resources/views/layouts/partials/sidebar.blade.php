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
        <li><a class="nav-link" href="{{ route('super.branches.index') }}"><i class="fas fa-building"></i> <span>Cabang</span></a></li>
        <li><a class="nav-link" href="{{ route('super.positions.index') }}"><i class="fas fa-briefcase"></i> <span>Posisi</span></a></li>
        <li><a class="nav-link" href="{{ route('super.employees.index') }}"><i class="fas fa-users"></i> <span>Karyawan</span></a></li>
        <li><a class="nav-link" href="{{ route('super.users.index') }}"><i class="fas fa-user-shield"></i> <span>User</span></a></li>
        <li class="menu-header">Laporan</li>
        <li><a class="nav-link" href="{{ route('super.reports.attendance') }}"><i class="fas fa-chart-bar"></i> <span>Laporan Kehadiran</span></a></li>
      @endif

      @if(Auth::user()->isAdminCabang())
        <li class="menu-header">Data Karyawan</li>
        <li><a class="nav-link" href="{{ route('admin.employees.index') }}"><i class="fas fa-users"></i> <span>Daftar Karyawan</span></a></li>
        <li class="menu-header">Pengaturan Cabang</li>
        <li><a class="nav-link" href="{{ route('admin.branch.location') }}"><i class="fas fa-map-marker-alt"></i> <span>Lokasi Kantor</span></a></li>
        <li><a class="nav-link" href="{{ route('admin.work-schedules.index') }}"><i class="fas fa-clock"></i> <span>Jam Kerja</span></a></li>
        <li><a class="nav-link" href="{{ route('admin.holidays.index') }}"><i class="fas fa-calendar"></i> <span>Hari Libur</span></a></li>
        <li class="menu-header">Monitoring</li>
        <li><a class="nav-link" href="{{ route('admin.attendances.monitor') }}"><i class="fas fa-user-check"></i> <span>Monitoring Absensi</span></a></li>
        <li class="menu-header">Approval</li>
        <li><a class="nav-link" href="{{ route('admin.leave-requests.index') }}"><i class="fas fa-file-signature"></i> <span>Cuti/Izin/Sakit</span></a></li>
        <li class="menu-header">Laporan</li>
        <li><a class="nav-link" href="{{ route('admin.reports.attendance') }}"><i class="fas fa-chart-bar"></i> <span>Laporan Kehadiran</span></a></li>
      @endif

      @if(Auth::user()->isKaryawan())
        <li class="menu-header">Absensi</li>
        <li><a class="nav-link" href="{{ route('karyawan.attendance.check-in') }}"><i class="fas fa-fingerprint"></i> <span>Absen Sekarang</span></a></li>
        <li><a class="nav-link" href="{{ route('karyawan.attendance.history') }}"><i class="fas fa-history"></i> <span>Riwayat Absensi</span></a></li>
        <li class="menu-header">Pengajuan</li>
        <li><a class="nav-link" href="{{ route('karyawan.leave-requests.index') }}"><i class="fas fa-file-alt"></i> <span>Cuti/Izin/Sakit</span></a></li>
        <li class="menu-header">Mobile</li>
        <li><a class="nav-link" href="{{ route('mobile.dashboard') }}"><i class="fas fa-mobile-alt"></i> <span>Versi Mobile</span></a></li>
      @endif
    </ul>
  </aside>
</div>
