@extends('layouts.app')

@section('title', 'Validasi Kehadiran')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('admin.attendances.monitor') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Validasi Kehadiran</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.attendances.monitor') }}">Monitoring</a></div>
    <div class="breadcrumb-item">Validasi</div>
  </div>
</div>

<div class="section-body">
  @php
    $attendanceId = request('id');
    $attendance = \App\Models\Attendance::with(['employee.user', 'employee.branch', 'employee.position'])->findOrFail($attendanceId);
  @endphp

  @if(session('success'))
    <div class="alert alert-success alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ session('success') }}
      </div>
    </div>
  @endif

  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h4>Informasi Karyawan</h4>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Nama:</strong><br>
            {{ $attendance->employee->user->name }}
          </div>
          <div class="mb-3">
            <strong>NIK:</strong><br>
            {{ $attendance->employee->nik }}
          </div>
          <div class="mb-3">
            <strong>Posisi:</strong><br>
            {{ $attendance->employee->position->name }}
          </div>
          <div class="mb-3">
            <strong>Cabang:</strong><br>
            {{ $attendance->employee->branch->name }}
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Foto Kehadiran</h4>
        </div>
        <div class="card-body">
          @if($attendance->check_in_photo)
            <div class="mb-3">
              <strong>Foto Check-in:</strong><br>
              <img src="{{ asset('storage/' . $attendance->check_in_photo) }}" class="img-fluid rounded mt-2" alt="Check-in Photo">
            </div>
          @endif
          
          @if($attendance->check_out_photo)
            <div class="mb-3">
              <strong>Foto Check-out:</strong><br>
              <img src="{{ asset('storage/' . $attendance->check_out_photo) }}" class="img-fluid rounded mt-2" alt="Check-out Photo">
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4>Detail Kehadiran</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>Tanggal:</strong><br>
              {{ $attendance->date->format('d F Y') }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Status:</strong><br>
              @if($attendance->status === 'valid')
                <span class="badge badge-success badge-lg">Valid</span>
              @elseif($attendance->status === 'late')
                <span class="badge badge-warning badge-lg">Terlambat</span>
              @elseif($attendance->status === 'problematic')
                <span class="badge badge-danger badge-lg">Bermasalah</span>
              @else
                <span class="badge badge-info badge-lg">{{ ucfirst($attendance->status) }}</span>
              @endif
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>Check-in:</strong><br>
              {{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Check-out:</strong><br>
              {{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : '-' }}
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>Lokasi Check-in:</strong><br>
              @if($attendance->check_in_lat && $attendance->check_in_lng)
                {{ $attendance->check_in_lat }}, {{ $attendance->check_in_lng }}
                <br><a href="https://www.google.com/maps?q={{ $attendance->check_in_lat }},{{ $attendance->check_in_lng }}" target="_blank">
                  <i class="fas fa-map-marker-alt"></i> Lihat di Maps
                </a>
              @else
                -
              @endif
            </div>
            <div class="col-md-6 mb-3">
              <strong>Lokasi Check-out:</strong><br>
              @if($attendance->check_out_lat && $attendance->check_out_lng)
                {{ $attendance->check_out_lat }}, {{ $attendance->check_out_lng }}
                <br><a href="https://www.google.com/maps?q={{ $attendance->check_out_lat }},{{ $attendance->check_out_lng }}" target="_blank">
                  <i class="fas fa-map-marker-alt"></i> Lihat di Maps
                </a>
              @else
                -
              @endif
            </div>
          </div>

          @if($attendance->notes)
            <div class="mb-3">
              <strong>Catatan:</strong><br>
              <div class="alert alert-info">{{ $attendance->notes }}</div>
            </div>
          @endif

          <div class="mb-3">
            <strong>Verifikasi:</strong><br>
            @if($attendance->is_verified)
              <span class="badge badge-success">Sudah Diverifikasi</span>
              @if($attendance->verifier)
                oleh {{ $attendance->verifier->name }}
              @endif
            @else
              <span class="badge badge-warning">Belum Diverifikasi</span>
            @endif
          </div>
        </div>
      </div>

      @if(!$attendance->is_verified && $attendance->status === 'problematic')
        <div class="card">
          <div class="card-header">
            <h4>Validasi Kehadiran</h4>
          </div>
          <form action="{{ route('admin.attendances.update-validation', $attendance->id) }}" method="POST">
            @csrf
            <div class="card-body">
              <div class="form-group">
                <label>Catatan Validasi</label>
                <textarea name="notes" class="form-control" rows="3">{{ $attendance->notes }}</textarea>
              </div>
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="valid">Valid - Terima kehadiran ini</option>
                  <option value="late">Terlambat - Tandai sebagai terlambat</option>
                  <option value="problematic" selected>Bermasalah - Tetap tandai bermasalah</option>
                </select>
              </div>
            </div>
            <div class="card-footer text-right">
              <a href="{{ route('admin.attendances.validate') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-check"></i> Simpan Validasi
              </button>
            </div>
          </form>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
