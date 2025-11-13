@extends('layouts.app')

@section('title', 'Laporan Kehadiran')

@section('content')
<div class="section-header">
  <h1>Laporan Kehadiran</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item">Laporan Kehadiran</div>
  </div>
</div>

<div class="section-body">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Filter Laporan</h4>
        </div>
        <div class="card-body">
          <form method="GET">
            <div class="row">
              @if(Auth::user()->isSuperAdmin())
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Cabang</label>
                    <select name="branch_id" class="form-control">
                      <option value="">Semua Cabang</option>
                      @foreach(\App\Models\Branch::where('is_active', true)->get() as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                          {{ $branch->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              @endif
              
              <div class="col-md-2">
                <div class="form-group">
                  <label>Bulan</label>
                  <select name="month" class="form-control">
                    @for($i = 1; $i <= 12; $i++)
                      <option value="{{ $i }}" {{ request('month', date('m')) == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                      </option>
                    @endfor
                  </select>
                </div>
              </div>
              
              <div class="col-md-2">
                <div class="form-group">
                  <label>Tahun</label>
                  <select name="year" class="form-control">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                      <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>
                        {{ $i }}
                      </option>
                    @endfor
                  </select>
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
                  </select>
                </div>
              </div>
              
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label><br>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                  </button>
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
          <h4>Statistik Kehadiran</h4>
          <div class="card-header-action">
            <button class="btn btn-success" onclick="exportExcel()">
              <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <button class="btn btn-danger" onclick="exportPDF()">
              <i class="fas fa-file-pdf"></i> Export PDF
            </button>
          </div>
        </div>
        <div class="card-body">
          @php
            $month = request('month', date('m'));
            $year = request('year', date('Y'));
            $status = request('status');
            $branchId = request('branch_id');
            
            if (Auth::user()->isAdminCabang()) {
              $branchId = Auth::user()->branch_id;
            }
            
            $query = \App\Models\Attendance::with(['employee.user', 'employee.position', 'employee.branch'])
              ->whereYear('date', $year)
              ->whereMonth('date', $month);
            
            if ($branchId) {
              $query->whereHas('employee', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
              });
            }
            
            if ($status) {
              $query->where('status', $status);
            }
            
            $attendances = $query->orderBy('date', 'desc')->orderBy('check_in', 'desc')->get();
            
            // Statistics
            $totalAttendances = $attendances->count();
            $validCount = $attendances->where('status', 'valid')->count();
            $lateCount = $attendances->where('status', 'late')->count();
            $problematicCount = $attendances->where('status', 'problematic')->count();
          @endphp

          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Kehadiran</h4>
                  </div>
                  <div class="card-body">
                    {{ $totalAttendances }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Valid</h4>
                  </div>
                  <div class="card-body">
                    {{ $validCount }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-clock"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Terlambat</h4>
                  </div>
                  <div class="card-body">
                    {{ $lateCount }}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Bermasalah</h4>
                  </div>
                  <div class="card-body">
                    {{ $problematicCount }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Karyawan</th>
                  <th>Posisi</th>
                  @if(Auth::user()->isSuperAdmin())
                    <th>Cabang</th>
                  @endif
                  <th>Check-in</th>
                  <th>Check-out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($attendances as $attendance)
                  <tr>
                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                    <td>{{ $attendance->employee->user->name }}</td>
                    <td>{{ $attendance->employee->position->name }}</td>
                    @if(Auth::user()->isSuperAdmin())
                      <td>{{ $attendance->employee->branch->name }}</td>
                    @endif
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
                    <td colspan="{{ Auth::user()->isSuperAdmin() ? '7' : '6' }}" class="text-center">Tidak ada data</td>
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
    "order": [[0, "desc"]]
  });

  function exportExcel() {
    alert('Fitur export Excel akan segera tersedia.\n\nUntuk mengaktifkan, jalankan:\ncomposer require maatwebsite/excel');
  }

  function exportPDF() {
    alert('Fitur export PDF akan segera tersedia.\n\nUntuk mengaktifkan, jalankan:\ncomposer require barryvdh/laravel-dompdf');
  }
</script>
@endpush
