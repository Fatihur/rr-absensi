@extends('layouts.app')

@section('title', 'Daftar Karyawan')

@section('content')
<div class="section-header">
  <h1>Daftar Karyawan</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item">Karyawan</div>
  </div>
</div>

<div class="section-body">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ session('success') }}
      </div>
    </div>
  @endif

  <!-- Statistics Cards -->
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
            {{ $statistics['total'] }}
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
            <h4>Aktif</h4>
          </div>
          <div class="card-body">
            {{ $statistics['active'] }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-danger">
          <i class="fas fa-times-circle"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Nonaktif</h4>
          </div>
          <div class="card-body">
            {{ $statistics['inactive'] }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
        <div class="card-icon bg-warning">
          <i class="fas fa-user-clock"></i>
        </div>
        <div class="card-wrap">
          <div class="card-header">
            <h4>Hadir Hari Ini</h4>
          </div>
          <div class="card-body">
            {{ $statistics['present_today'] }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter -->
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
                  <label>Posisi</label>
                  <select name="position_id" class="form-control">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $position)
                      <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Cari</label>
                  <input type="text" name="search" class="form-control" placeholder="Nama atau NIK" value="{{ request('search') }}">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label><br>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-sync"></i>
                  </a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Employee List -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Daftar Karyawan - {{ $branch->name }}</h4>
          <div class="card-header-action">
            <button class="btn btn-success" onclick="exportExcel()">
              <i class="fas fa-file-excel"></i> Export
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Posisi</th>
                  <th>Email</th>
                  <th>No. HP</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($employees as $employee)
                  <tr>
                    <td>
                      @if($employee->face_photo)
                        <img src="{{ asset('storage/' . $employee->face_photo) }}" 
                          class="rounded-circle" width="40" height="40" alt="Photo">
                      @else
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #667eea; color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: bold;">
                          {{ strtoupper(substr($employee->full_name, 0, 1)) }}
                        </div>
                      @endif
                    </td>
                    <td>{{ $employee->nik }}</td>
                    <td>{{ $employee->full_name }}</td>
                    <td>{{ $employee->position->name }}</td>
                    <td>{{ $employee->user->email }}</td>
                    <td>{{ $employee->phone ?? '-' }}</td>
                    <td>
                      @if($employee->is_active)
                        <span class="badge badge-success">Aktif</span>
                      @else
                        <span class="badge badge-danger">Nonaktif</span>
                      @endif
                    </td>
                    <td>
                      <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-info" title="Detail">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center">Tidak ada data karyawan</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        @if($employees->hasPages())
          <div class="card-footer text-right">
            {{ $employees->links() }}
          </div>
        @endif
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
    "columnDefs": [
      { "orderable": false, "targets": [0, 7] }
    ]
  });

  function showAlert(message, type = 'info') {
    const alertHtml = `
      <div class="alert alert-${type} alert-dismissible show fade">
        <div class="alert-body">
          <button class="close" data-dismiss="alert"><span>&times;</span></button>
          ${message}
        </div>
      </div>
    `;
    $('.section-body').prepend(alertHtml);
  }

  function exportExcel() {
    showAlert('Fitur export Excel akan segera tersedia.<br><br>Untuk mengaktifkan, jalankan:<br><code>composer require maatwebsite/excel</code>', 'info');
  }
</script>
@endpush
