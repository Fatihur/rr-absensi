@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible show fade">
    <div class="alert-body">
      <button class="close" data-dismiss="alert"><span>&times;</span></button>
      {{ session('success') }}
    </div>
  </div>
@endif

<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('admin.employees.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Detail Karyawan</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Karyawan</a></div>
    <div class="breadcrumb-item">Detail</div>
  </div>
</div>

<div class="section-body">
  <div class="row">
    <div class="col-12 col-md-4">
      <div class="card card-primary">
        <div class="card-body text-center">
          @if($employee->face_photo)
            <img src="{{ asset('storage/' . $employee->face_photo) }}" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;" alt="Photo">
          @else
            <div class="avatar avatar-xl bg-primary text-white mb-3" style="width: 150px; height: 150px; font-size: 60px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
              {{ substr($employee->full_name, 0, 1) }}
            </div>
          @endif
          <h4>{{ $employee->full_name }}</h4>
          <p class="text-muted">{{ $employee->position->name }}</p>
          @if($employee->is_active)
            <span class="badge badge-success badge-lg">Aktif</span>
          @else
            <span class="badge badge-danger badge-lg">Nonaktif</span>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Informasi Kontak</h4>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Email:</strong><br>
            <a href="mailto:{{ $employee->user->email }}">{{ $employee->user->email }}</a>
          </div>
          <div class="mb-3">
            <strong>No. HP:</strong><br>
            @if($employee->phone)
              <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
            @else
              -
            @endif
          </div>
          <div class="mb-3">
            <strong>NIK:</strong><br>
            {{ $employee->nik }}
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Statistik Bulan Ini</h4>
        </div>
        <div class="card-body">
          @php
            $thisMonth = \Carbon\Carbon::now();
            $monthlyStats = \App\Models\Attendance::where('employee_id', $employee->id)
              ->whereYear('date', $thisMonth->year)
              ->whereMonth('date', $thisMonth->month)
              ->selectRaw('
                  COUNT(*) as total_days,
                  SUM(CASE WHEN status = "valid" THEN 1 ELSE 0 END) as on_time,
                  SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                  SUM(CASE WHEN status IN ("leave", "sick", "permit") THEN 1 ELSE 0 END) as `leave`
              ')
              ->first();
          @endphp

          <div class="row">
            <div class="col-6 mb-3">
              <div class="text-center">
                <div style="font-size: 24px; font-weight: bold; color: #6777ef;">{{ $monthlyStats->total_days ?? 0 }}</div>
                <div style="font-size: 12px; color: #888;">Total Hari</div>
              </div>
            </div>
            <div class="col-6 mb-3">
              <div class="text-center">
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $monthlyStats->on_time ?? 0 }}</div>
                <div style="font-size: 12px; color: #888;">Tepat Waktu</div>
              </div>
            </div>
            <div class="col-6 mb-3">
              <div class="text-center">
                <div style="font-size: 24px; font-weight: bold; color: #ffc107;">{{ $monthlyStats->late ?? 0 }}</div>
                <div style="font-size: 12px; color: #888;">Terlambat</div>
              </div>
            </div>
            <div class="col-6 mb-3">
              <div class="text-center">
                <div style="font-size: 24px; font-weight: bold; color: #17a2b8;">{{ $monthlyStats->leave ?? 0 }}</div>
                <div style="font-size: 12px; color: #888;">Izin/Cuti</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-8">
      <div class="card">
        <div class="card-header">
          <h4>Informasi Kerja</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>Cabang:</strong><br>
              {{ $employee->branch->name }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Posisi:</strong><br>
              {{ $employee->position->name }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Tanggal Bergabung:</strong><br>
              {{ $employee->join_date ? $employee->join_date->format('d F Y') : '-' }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Masa Kerja:</strong><br>
              {{ $employee->join_date ? $employee->join_date->diffForHumans() : '-' }}
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Riwayat Absensi (30 Hari Terakhir)</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-sm">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Check-In</th>
                  <th>Check-Out</th>
                  <th>Status</th>
                  <th>Foto</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $recentAttendances = \App\Models\Attendance::where('employee_id', $employee->id)
                    ->orderBy('date', 'desc')
                    ->limit(30)
                    ->get();
                @endphp

                @forelse($recentAttendances as $attendance)
                  <tr>
                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                    <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}</td>
                    <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : '-' }}</td>
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
                    <td>
                      @if($attendance->check_in_photo)
                        <a href="{{ asset('storage/' . $attendance->check_in_photo) }}" target="_blank">
                          <i class="fas fa-image"></i>
                        </a>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">Belum ada riwayat absensi</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Riwayat Pengajuan Cuti/Izin (Terbaru)</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-sm">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Jenis</th>
                  <th>Periode</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $leaveRequests = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
                @endphp

                @forelse($leaveRequests as $request)
                  <tr>
                    <td>{{ $request->created_at->format('d/m/Y') }}</td>
                    <td>
                      @if($request->type === 'leave')
                        <span class="badge badge-info">Cuti</span>
                      @elseif($request->type === 'sick')
                        <span class="badge badge-warning">Sakit</span>
                      @else
                        <span class="badge badge-secondary">Izin</span>
                      @endif
                    </td>
                    <td>
                      {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} - 
                      {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                    </td>
                    <td>
                      @if($request->status === 'pending')
                        <span class="badge badge-warning">Menunggu</span>
                      @elseif($request->status === 'approved')
                        <span class="badge badge-success">Disetujui</span>
                      @else
                        <span class="badge badge-danger">Ditolak</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center">Belum ada pengajuan</td>
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
