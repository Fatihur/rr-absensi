@extends('layouts.app')

@section('title', 'Kelola Cabang')

@section('content')
<div class="section-header">
  <h1>Kelola Cabang</h1>
  <div class="section-header-button">
    <a href="{{ route('super.branches.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Tambah Cabang
    </a>
  </div>
</div>

<div class="section-body">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert">
          <span>&times;</span>
        </button>
        {{ session('success') }}
      </div>
    </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h4>Daftar Cabang</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table-1">
          <thead>
            <tr>
              <th>Nama Cabang</th>
              <th>Alamat</th>
              <th>Koordinat GPS</th>
              <th>Radius</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($branches as $branch)
              <tr>
                <td>{{ $branch->name }}</td>
                <td>{{ Str::limit($branch->address, 50) }}</td>
                <td>
                  @if($branch->latitude && $branch->longitude)
                    <small class="text-muted">
                      {{ number_format($branch->latitude, 6) }}, {{ number_format($branch->longitude, 6) }}
                    </small>
                  @else
                    <span class="badge badge-warning">Belum diset</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-info">{{ $branch->radius }}m</span>
                </td>
                <td>
                  @if($branch->is_active)
                    <span class="badge badge-success">Aktif</span>
                  @else
                    <span class="badge badge-danger">Nonaktif</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('super.branches.show', $branch) }}" class="btn btn-sm btn-info" title="Detail">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="{{ route('super.branches.edit', $branch) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('super.branches.destroy', $branch) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus cabang ini?')" title="Hapus">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada data cabang</td>
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
