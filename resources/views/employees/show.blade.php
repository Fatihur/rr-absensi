@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('super.employees.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Detail Karyawan</h1>
  <div class="section-header-button">
    <a href="{{ route('super.employees.edit', $employee) }}" class="btn btn-primary">
      <i class="fas fa-edit"></i> Edit
    </a>
  </div>
</div>

<div class="section-body">
  <div class="row">
    <div class="col-12 col-md-4">
      <div class="card card-primary">
        <div class="card-body text-center">
          @if($employee->face_photo)
            <img src="{{ asset('storage/' . $employee->face_photo) }}" class="rounded-circle mb-3" width="150" height="150" alt="Photo">
          @else
            <div class="avatar avatar-xl bg-primary text-white mb-3">
              {{ substr($employee->full_name, 0, 1) }}
            </div>
          @endif
          <h4>{{ $employee->full_name }}</h4>
          <p class="text-muted">{{ $employee->position->name }}</p>
          @if($employee->is_active)
            <span class="badge badge-success">Aktif</span>
          @else
            <span class="badge badge-danger">Nonaktif</span>
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
            {{ $employee->user->email }}
          </div>
          <div class="mb-3">
            <strong>No. HP:</strong><br>
            {{ $employee->phone ?? '-' }}
          </div>
          <div class="mb-3">
            <strong>NIK:</strong><br>
            {{ $employee->nik }}
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
            <div class="col-6 mb-3">
              <strong>Cabang:</strong><br>
              {{ $employee->branch->name }}
            </div>
            <div class="col-6 mb-3">
              <strong>Posisi:</strong><br>
              {{ $employee->position->name }}
            </div>
            <div class="col-6 mb-3">
              <strong>Tanggal Bergabung:</strong><br>
              {{ $employee->join_date ? $employee->join_date->format('d/m/Y') : '-' }}
            </div>
            <div class="col-6 mb-3">
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
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Check-In</th>
                  <th>Check-Out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($employee->attendances as $attendance)
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
