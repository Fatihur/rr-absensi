@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="section-header">
  <h1>Dashboard</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item">{{ $employee->full_name }}</div>
  </div>
</div>

<div class="section-body">
  <!-- Statistics -->
  <div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-primary">
          <i class="far fa-calendar"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Total Hari Kerja</h4>
          </div>
          <div class="card-body">
            {{ $monthly_stats->total_days ?? 0 }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-success">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Tepat Waktu</h4>
          </div>
          <div class="card-body">
            {{ $monthly_stats->on_time ?? 0 }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-warning">
          <i class="fas fa-clock"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Terlambat</h4>
          </div>
          <div class="card-body">
            {{ $monthly_stats->late ?? 0 }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-info">
          <i class="fas fa-umbrella-beach"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Izin/Cuti</h4>
          </div>
          <div class="card-body">
            {{ $monthly_stats->leave ?? 0 }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Attendance -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Riwayat Absensi Terakhir</h4>
          <div class="card-header-action">
            <a href="{{ route('karyawan.attendance.history') }}" class="btn btn-primary">
              <i class="fas fa-eye"></i> Lihat Semua
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-md">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Check In</th>
                  <th>Check Out</th>
                  <th>Durasi</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recent_attendances as $attendance)
                  <tr>
                    <td>{{ $attendance->date->format('d M Y') }}</td>
                    <td>
                      @if($attendance->check_in)
                        <i class="fas fa-sign-in-alt text-success"></i> 
                        {{ $attendance->check_in->format('H:i') }}
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if($attendance->check_out)
                        <i class="fas fa-sign-out-alt text-danger"></i> 
                        {{ $attendance->check_out->format('H:i') }}
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if($attendance->check_in && $attendance->check_out)
                        @php
                          $duration = $attendance->check_in->diff($attendance->check_out);
                          $hours = $duration->h;
                          $minutes = $duration->i;
                        @endphp
                        {{ $hours }}j {{ $minutes }}m
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if($attendance->status === 'valid')
                        <span class="badge badge-success"><i class="fas fa-check"></i> Valid</span>
                      @elseif($attendance->status === 'late')
                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Terlambat</span>
                      @elseif($attendance->status === 'problematic')
                        <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Bermasalah</span>
                      @else
                        <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4">
                      <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                      <p class="text-muted">Belum ada riwayat absensi</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
