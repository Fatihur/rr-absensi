@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="section-header">
  <h1>Dashboard Karyawan</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active">{{ $employee->full_name }}</div>
  </div>
</div>

<div class="section-body">
  <div class="row">
    <div class="col-12 col-md-6 col-lg-6">
      <div class="card card-primary">
        <div class="card-header">
          <h4>Status Absensi Hari Ini</h4>
        </div>
        <div class="card-body">
          @if($today_attendance)
            <div class="row">
              <div class="col-6">
                <div class="text-small text-muted">Check In</div>
                <div class="font-weight-bold">{{ $today_attendance->check_in ?? '-' }}</div>
              </div>
              <div class="col-6">
                <div class="text-small text-muted">Check Out</div>
                <div class="font-weight-bold">{{ $today_attendance->check_out ?? '-' }}</div>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-6">
                <div class="text-small text-muted">Status</div>
                @if($today_attendance->status === 'valid')
                  <span class="badge badge-success">Valid</span>
                @elseif($today_attendance->status === 'late')
                  <span class="badge badge-warning">Terlambat</span>
                @else
                  <span class="badge badge-info">{{ ucfirst($today_attendance->status) }}</span>
                @endif
              </div>
              <div class="col-6">
                <div class="text-small text-muted">Verified</div>
                @if($today_attendance->is_verified)
                  <span class="badge badge-success">Ya</span>
                @else
                  <span class="badge badge-secondary">Belum</span>
                @endif
              </div>
            </div>
          @else
            <div class="alert alert-warning mb-0">
              Anda belum melakukan absensi hari ini.
            </div>
          @endif
        </div>
        <div class="card-footer text-center">
          <a href="#" class="btn btn-primary btn-lg">
            <i class="fas fa-fingerprint"></i> Absen Sekarang
          </a>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6 col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4>Statistik Bulan Ini</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-6 col-md-6 col-lg-6">
              <div class="card gradient-bottom">
                <div class="card-body">
                  <h6>Total Hari</h6>
                  <h2>{{ $monthly_stats->total_days ?? 0 }}</h2>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-6 col-lg-6">
              <div class="card gradient-bottom">
                <div class="card-body">
                  <h6>Tepat Waktu</h6>
                  <h2>{{ $monthly_stats->on_time ?? 0 }}</h2>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-6 col-lg-6">
              <div class="card gradient-bottom">
                <div class="card-body">
                  <h6>Terlambat</h6>
                  <h2>{{ $monthly_stats->late ?? 0 }}</h2>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-6 col-lg-6">
              <div class="card gradient-bottom">
                <div class="card-body">
                  <h6>Izin/Sakit</h6>
                  <h2>{{ $monthly_stats->leave ?? 0 }}</h2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Riwayat Absensi Terakhir</h4>
          <div class="card-header-action">
            <a href="#" class="btn btn-primary">Lihat Semua</a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Check In</th>
                  <th>Check Out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recent_attendances as $attendance)
                  <tr>
                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                    <td>{{ $attendance->check_in ?? '-' }}</td>
                    <td>{{ $attendance->check_out ?? '-' }}</td>
                    <td>
                      @if($attendance->status === 'valid')
                        <span class="badge badge-success">Valid</span>
                      @elseif($attendance->status === 'late')
                        <span class="badge badge-warning">Terlambat</span>
                      @else
                        <span class="badge badge-info">{{ ucfirst($attendance->status) }}</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center">Belum ada riwayat absensi</td>
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
