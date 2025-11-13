@extends('layouts.mobile')

@section('header-title', 'Beranda')

@section('content')
<!-- Welcome Card -->
<div class="mobile-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
  <h5 style="margin: 0 0 5px 0; font-weight: 600;">Selamat Datang,</h5>
  <h3 style="margin: 0; font-weight: bold;">{{ $employee->full_name }}</h3>
  <p style="margin: 10px 0 0 0; opacity: 0.9;">
    <i class="fas fa-building"></i> {{ $employee->branch->name }}<br>
    <i class="fas fa-briefcase"></i> {{ $employee->position->name }}
  </p>
</div>

<!-- Today Attendance Status -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-calendar-day"></i> Status Hari Ini
  </div>
  
  @if($todayAttendance)
    @if($todayAttendance->check_in && $todayAttendance->check_out)
      <!-- Already Complete -->
      <div class="alert alert-success alert-mobile">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <strong><i class="fas fa-check-circle"></i> Absensi Selesai</strong><br>
            <small>Anda sudah menyelesaikan absensi hari ini</small>
          </div>
        </div>
      </div>
      
      <div style="display: flex; gap: 10px; margin-top: 15px;">
        <div style="flex: 1; background: #f8f9fa; padding: 12px; border-radius: 10px;">
          <div style="font-size: 11px; color: #6c757d; margin-bottom: 3px;">Check-in</div>
          <div style="font-size: 18px; font-weight: bold; color: #28a745;">
            {{ $todayAttendance->check_in->format('H:i') }}
          </div>
        </div>
        <div style="flex: 1; background: #f8f9fa; padding: 12px; border-radius: 10px;">
          <div style="font-size: 11px; color: #6c757d; margin-bottom: 3px;">Check-out</div>
          <div style="font-size: 18px; font-weight: bold; color: #dc3545;">
            {{ $todayAttendance->check_out->format('H:i') }}
          </div>
        </div>
      </div>
      
      <div style="margin-top: 10px; text-align: center;">
        @if($todayAttendance->status === 'valid')
          <span class="badge badge-success badge-mobile"><i class="fas fa-check"></i> Valid</span>
        @elseif($todayAttendance->status === 'late')
          <span class="badge badge-warning badge-mobile"><i class="fas fa-clock"></i> Terlambat</span>
        @else
          <span class="badge badge-danger badge-mobile"><i class="fas fa-exclamation-triangle"></i> Bermasalah</span>
        @endif
      </div>
      
    @elseif($todayAttendance->check_in)
      <!-- Already Check-in, Need Check-out -->
      <div class="alert alert-info alert-mobile">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <strong><i class="fas fa-info-circle"></i> Sudah Check-in</strong><br>
            <small>Masuk: {{ $todayAttendance->check_in->format('H:i') }}</small>
          </div>
          <div>
            @if($todayAttendance->status === 'valid')
              <span class="badge badge-success badge-mobile">Valid</span>
            @elseif($todayAttendance->status === 'late')
              <span class="badge badge-warning badge-mobile">Terlambat</span>
            @else
              <span class="badge badge-danger badge-mobile">Bermasalah</span>
            @endif
          </div>
        </div>
      </div>
      
      <a href="{{ route('mobile.attendance') }}" class="btn btn-mobile btn-mobile-danger">
        <i class="fas fa-sign-out-alt"></i> Check-Out Sekarang
      </a>
    @endif
  @else
    <!-- Not yet Check-in -->
    <div class="alert alert-warning alert-mobile">
      <div style="text-align: center;">
        <i class="fas fa-exclamation-circle" style="font-size: 30px; margin-bottom: 10px;"></i><br>
        <strong>Anda belum absen hari ini</strong><br>
        <small>Silakan lakukan check-in</small>
      </div>
    </div>
    
    <a href="{{ route('mobile.attendance') }}" class="btn btn-mobile btn-mobile-primary">
      <i class="fas fa-fingerprint"></i> Check-In Sekarang
    </a>
  @endif
</div>

<!-- Monthly Statistics -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-chart-bar"></i> Statistik Bulan Ini
  </div>
  
  <div class="stats-grid">
    <div class="stat-item">
      <div class="stat-value">{{ $monthlyStats->total_days ?? 0 }}</div>
      <div class="stat-label">Total Hari</div>
    </div>
    <div class="stat-item">
      <div class="stat-value" style="color: #28a745;">{{ $monthlyStats->on_time ?? 0 }}</div>
      <div class="stat-label">Tepat Waktu</div>
    </div>
    <div class="stat-item">
      <div class="stat-value" style="color: #ffc107;">{{ $monthlyStats->late ?? 0 }}</div>
      <div class="stat-label">Terlambat</div>
    </div>
    <div class="stat-item">
      <div class="stat-value" style="color: #17a2b8;">{{ $monthlyStats->leave ?? 0 }}</div>
      <div class="stat-label">Izin/Cuti</div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-bolt"></i> Menu Cepat
  </div>
  
  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
    <a href="{{ route('mobile.history') }}" style="text-decoration: none;">
      <div style="background: #f8f9fa; padding: 20px 10px; border-radius: 12px; text-align: center;">
        <i class="fas fa-history" style="font-size: 24px; color: #667eea; margin-bottom: 8px;"></i>
        <div style="font-size: 12px; color: #333;">Riwayat</div>
      </div>
    </a>
    
    <a href="{{ route('mobile.leave') }}" style="text-decoration: none;">
      <div style="background: #f8f9fa; padding: 20px 10px; border-radius: 12px; text-align: center;">
        <i class="fas fa-calendar-alt" style="font-size: 24px; color: #667eea; margin-bottom: 8px;"></i>
        <div style="font-size: 12px; color: #333;">Pengajuan</div>
      </div>
    </a>
    
    <a href="{{ route('mobile.profile') }}" style="text-decoration: none;">
      <div style="background: #f8f9fa; padding: 20px 10px; border-radius: 12px; text-align: center;">
        <i class="fas fa-user" style="font-size: 24px; color: #667eea; margin-bottom: 8px;"></i>
        <div style="font-size: 12px; color: #333;">Profil</div>
      </div>
    </a>
  </div>
</div>

<!-- Info -->
<div style="text-align: center; padding: 20px; color: #8e8e93; font-size: 12px;">
  <p>{{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
  <p>Aplikasi Absensi Karyawan v1.0</p>
</div>
@endsection
