@extends('layouts.app')

@section('title', 'Monitoring Kehadiran')

@section('content')
<div class="section-header">
  <h1>Monitoring Kehadiran</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item">Monitoring Kehadiran</div>
  </div>
</div>

<div class="section-body">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Filter</h4>
        </div>
        <div class="card-body">
          <form method="GET">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Tanggal</label>
                  <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="problematic" {{ request('status') === 'problematic' ? 'selected' : '' }}>Bermasalah</option>
                    <option value="leave" {{ request('status') === 'leave' ? 'selected' : '' }}>Cuti</option>
                    <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="permit" {{ request('status') === 'permit' ? 'selected' : '' }}>Izin</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Posisi</label>
                  <select name="position_id" class="form-control">
                    <option value="">Semua Posisi</option>
                    @foreach(\App\Models\Position::where('is_active', true)->get() as $position)
                      <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>&nbsp;</label><br>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="{{ route('admin.attendances.monitor') }}" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Reset
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Daftar Kehadiran</h4>
          <div class="card-header-action">
            <button class="btn btn-success" onclick="exportExcel()">
              <i class="fas fa-file-excel"></i> Export Excel
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th>Karyawan</th>
                  <th>Posisi</th>
                  <th>Tanggal</th>
                  <th>Check-in</th>
                  <th>Check-out</th>
                  <th>Status</th>
                  <th>Foto</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $branchId = Auth::user()->branch_id;
                  $date = request('date', date('Y-m-d'));
                  $status = request('status');
                  $positionId = request('position_id');
                  
                  $query = \App\Models\Attendance::with(['employee.user', 'employee.position'])
                    ->whereHas('employee', function($q) use ($branchId) {
                      $q->where('branch_id', $branchId);
                    })
                    ->whereDate('date', $date);
                  
                  if ($status) {
                    $query->where('status', $status);
                  }
                  
                  if ($positionId) {
                    $query->whereHas('employee', function($q) use ($positionId) {
                      $q->where('position_id', $positionId);
                    });
                  }
                  
                  $attendances = $query->orderBy('check_in', 'desc')->get();
                @endphp
                
                @forelse($attendances as $attendance)
                  <tr>
                    <td>{{ $attendance->employee->user->name }}</td>
                    <td>{{ $attendance->employee->position->name }}</td>
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
                          <i class="fas fa-image"></i> Lihat
                        </a>
                      @else
                        -
                      @endif
                    </td>
                    <td>
                      <a href="{{ route('admin.attendances.validate') }}?id={{ $attendance->id }}" class="btn btn-sm btn-primary" title="Detail">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center">Tidak ada data kehadiran</td>
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

@push('scripts')
<script src="{{ asset('stisla/assets/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('stisla/assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
  $("#table-1").dataTable({
    "order": []
  });

  function exportExcel() {
    alert('Fitur export Excel akan segera tersedia. Silakan install package maatwebsite/excel');
  }
</script>
@endpush
