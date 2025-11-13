@extends('layouts.app')

@section('title', 'Kelola Karyawan')

@section('content')
<div class="section-header">
  <h1>Kelola Karyawan</h1>
  <div class="section-header-button">
    <a href="{{ route('super.employees.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Tambah Karyawan
    </a>
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

  <div class="card">
    <div class="card-header">
      <h4>Daftar Karyawan</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table-1">
          <thead>
            <tr>
              <th>NIK</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Cabang</th>
              <th>Posisi</th>
              <th>Tanggal Bergabung</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($employees as $employee)
              <tr>
                <td>{{ $employee->nik }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    @if($employee->face_photo)
                      <img src="{{ asset('storage/' . $employee->face_photo) }}" 
                        class="rounded-circle mr-2" width="35" height="35" alt="Photo">
                    @else
                      <div class="avatar bg-primary text-white mr-2">
                        {{ substr($employee->full_name, 0, 1) }}
                      </div>
                    @endif
                    <div>{{ $employee->full_name }}</div>
                  </div>
                </td>
                <td>{{ $employee->user->email }}</td>
                <td>{{ $employee->branch->name }}</td>
                <td>{{ $employee->position->name }}</td>
                <td>{{ $employee->join_date ? $employee->join_date->format('d/m/Y') : '-' }}</td>
                <td>
                  @if($employee->is_active)
                    <span class="badge badge-success">Aktif</span>
                  @else
                    <span class="badge badge-danger">Nonaktif</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('super.employees.show', $employee) }}" class="btn btn-sm btn-info" title="Detail">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="{{ route('super.employees.edit', $employee) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('super.employees.destroy', $employee) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus karyawan ini? Akun user akan ikut terhapus!')" title="Hapus">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center">Belum ada data karyawan</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('stisla/assets/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('stisla/assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
  $("#table-1").dataTable();
</script>
@endpush
