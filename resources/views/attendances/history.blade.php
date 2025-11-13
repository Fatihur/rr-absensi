@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="section-header">
  <h1>Riwayat Absensi</h1>
</div>

<div class="section-body">
  <div class="card">
    <div class="card-header">
      <h4>Riwayat Absensi Saya</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Check-In</th>
              <th>Check-Out</th>
              <th>Durasi</th>
              <th>Status</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($attendances as $attendance)
              <tr>
                <td>{{ $attendance->date->format('d/m/Y') }}</td>
                <td>
                  @if($attendance->check_in)
                    {{ $attendance->check_in->format('H:i:s') }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($attendance->check_out)
                    {{ $attendance->check_out->format('H:i:s') }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($attendance->check_in && $attendance->check_out)
                    {{ $attendance->check_in->diff($attendance->check_out)->format('%H jam %I menit') }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
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
                  
                  @if($attendance->is_verified)
                    <span class="badge badge-success"><i class="fas fa-check"></i> Verified</span>
                  @endif
                </td>
                <td>
                  <small class="text-muted">{{ $attendance->notes ?? '-' }}</small>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada riwayat absensi</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="mt-3">
        {{ $attendances->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
