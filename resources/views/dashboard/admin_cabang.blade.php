@extends('layouts.app')

@section('title', 'Dashboard Admin Cabang')

@section('content')
<div class="section-header">
  <h1>Dashboard Admin Cabang</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active">{{ $branch->name }}</div>
  </div>
</div>

<div class="section-body">
  <div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-primary">
          <i class="fas fa-users"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Total Karyawan</h4>
          </div>
          <div class="card-body">
            {{ $total_employees }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-success">
          <i class="fas fa-user-check"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Hadir Hari Ini</h4>
          </div>
          <div class="card-body">
            {{ $today_present }}
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
            {{ $today_late }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-danger">
          <i class="fas fa-user-times"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Belum Absen</h4>
          </div>
          <div class="card-body">
            {{ $today_absent }}
          </div>
        </div>
      </div>
    </div>
  </div>

  @if($problematic_attendance > 0)
    <div class="row">
      <div class="col-12">
        <div class="alert alert-warning alert-has-icon">
          <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
          <div class="alert-body">
            <div class="alert-title">Perhatian!</div>
            Terdapat <strong>{{ $problematic_attendance }}</strong> absensi bermasalah yang perlu divalidasi.
          </div>
        </div>
      </div>
    </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Absensi Hari Ini</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Karyawan</th>
                  <th>Posisi</th>
                  <th>Check In</th>
                  <th>Check Out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recent_attendances as $attendance)
                  <tr>
                    <td>{{ $attendance->employee->full_name }}</td>
                    <td>{{ $attendance->employee->position->name }}</td>
                    <td>{{ $attendance->check_in ?? '-' }}</td>
                    <td>{{ $attendance->check_out ?? '-' }}</td>
                    <td>
                      @if($attendance->status === 'valid')
                        <span class="badge badge-success">Valid</span>
                      @elseif($attendance->status === 'late')
                        <span class="badge badge-warning">Terlambat</span>
                      @elseif($attendance->status === 'problematic')
                        <span class="badge badge-danger">Bermasalah</span>
                      @else
                        <span class="badge badge-info">{{ ucfirst($attendance->status) }}</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">Belum ada data absensi hari ini</td>
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
