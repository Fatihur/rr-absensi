@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="section-header">
  <h1>Kelola User</h1>
  <div class="section-header-button">
    <a href="{{ route('super.users.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Tambah User
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

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert"><span>&times;</span></button>
        {{ session('error') }}
      </div>
    </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h4>Daftar User</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table-1">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Cabang</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  <span class="badge badge-primary">{{ $user->role->display_name }}</span>
                </td>
                <td>{{ $user->branch ? $user->branch->name : '-' }}</td>
                <td>
                  @if($user->is_active)
                    <span class="badge badge-success">Aktif</span>
                  @else
                    <span class="badge badge-danger">Nonaktif</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('super.users.edit', $user) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  @if($user->id !== Auth::id())
                    <form action="{{ route('super.users.destroy', $user) }}" method="POST" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini?')" title="Hapus">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada data user</td>
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
