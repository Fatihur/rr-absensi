@extends('layouts.mobile')

@section('header-title', 'Profil Saya')

@section('content')
<!-- Profile Card -->
<div class="mobile-card" style="text-align: center;">
  @if($employee->face_photo)
    <img src="{{ asset('storage/' . $employee->face_photo) }}" 
      style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 4px solid #667eea;">
  @else
    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: inline-flex; align-items: center; justify-center; font-size: 40px; font-weight: bold; margin-bottom: 15px;">
      {{ strtoupper(substr($employee->full_name, 0, 1)) }}
    </div>
  @endif
  
  <h4 style="margin: 0 0 5px 0; font-weight: 600;">{{ $employee->full_name }}</h4>
  <p style="color: #6c757d; font-size: 14px; margin: 0;">{{ $employee->nik }}</p>
</div>

<!-- Personal Info -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-user"></i> Informasi Pribadi
  </div>
  
  <div style="margin-bottom: 15px;">
    <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">Email</div>
    <div style="font-size: 14px; font-weight: 500;">{{ $user->email }}</div>
  </div>
  
  <div style="margin-bottom: 15px;">
    <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">No. HP</div>
    <div style="font-size: 14px; font-weight: 500;">{{ $employee->phone ?? '-' }}</div>
  </div>
  
  <div>
    <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">Status</div>
    <div style="font-size: 14px; font-weight: 500;">
      @if($employee->is_active)
        <span class="badge badge-success badge-mobile">Aktif</span>
      @else
        <span class="badge badge-danger badge-mobile">Nonaktif</span>
      @endif
    </div>
  </div>
</div>

<!-- Work Info -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-briefcase"></i> Informasi Pekerjaan
  </div>
  
  <div style="margin-bottom: 15px;">
    <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">Cabang</div>
    <div style="font-size: 14px; font-weight: 500;">
      <i class="fas fa-building"></i> {{ $employee->branch->name }}
    </div>
  </div>
  
  <div style="margin-bottom: 15px;">
    <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">Posisi</div>
    <div style="font-size: 14px; font-weight: 500;">
      <i class="fas fa-id-badge"></i> {{ $employee->position->name }}
    </div>
  </div>
  
  <div style="margin-bottom: 15px;">
    <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">Tanggal Bergabung</div>
    <div style="font-size: 14px; font-weight: 500;">
      <i class="fas fa-calendar-alt"></i> {{ $employee->join_date ? $employee->join_date->format('d F Y') : '-' }}
    </div>
  </div>
  
  @if($employee->join_date)
    <div>
      <div style="font-size: 12px; color: #6c757d; margin-bottom: 3px;">Masa Kerja</div>
      <div style="font-size: 14px; font-weight: 500;">
        <i class="fas fa-clock"></i> {{ $employee->join_date->diffForHumans(null, true) }}
      </div>
    </div>
  @endif
</div>

<!-- Settings -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-cog"></i> Pengaturan
  </div>
  
  <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="width: 40px; height: 40px; border-radius: 10px; background: #e8f5e9; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-bell" style="color: #28a745; font-size: 18px;"></i>
      </div>
      <div>
        <div style="font-size: 14px; font-weight: 500;">Notifikasi</div>
        <div style="font-size: 12px; color: #6c757d;">Kelola notifikasi aplikasi</div>
      </div>
    </div>
    <i class="fas fa-chevron-right" style="color: #ccc;"></i>
  </div>
  
  <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="width: 40px; height: 40px; border-radius: 10px; background: #e3f2fd; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-lock" style="color: #2196f3; font-size: 18px;"></i>
      </div>
      <div>
        <div style="font-size: 14px; font-weight: 500;">Keamanan</div>
        <div style="font-size: 12px; color: #6c757d;">Ubah password</div>
      </div>
    </div>
    <i class="fas fa-chevron-right" style="color: #ccc;"></i>
  </div>
  
  <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0;">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff3e0; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-info-circle" style="color: #ff9800; font-size: 18px;"></i>
      </div>
      <div>
        <div style="font-size: 14px; font-weight: 500;">Tentang Aplikasi</div>
        <div style="font-size: 12px; color: #6c757d;">Versi 1.0.0</div>
      </div>
    </div>
    <i class="fas fa-chevron-right" style="color: #ccc;"></i>
  </div>
</div>

<!-- Logout Button -->
<form action="{{ route('mobile.logout') }}" method="POST">
  @csrf
  <button type="submit" class="btn btn-mobile" style="background: #dc3545; color: white;">
    <i class="fas fa-sign-out-alt"></i> Keluar
  </button>
</form>

<div style="text-align: center; padding: 20px; color: #8e8e93; font-size: 12px;">
  <p>Aplikasi Absensi Karyawan</p>
  <p>&copy; 2025 All Rights Reserved</p>
</div>
@endsection
