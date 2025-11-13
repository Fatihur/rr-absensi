@extends('layouts.app')

@section('title', 'Kelola Posisi')

@section('content')
<div class="section-header">
  <h1>Kelola Posisi</h1>
  <div class="section-header-button">
    <a href="{{ route('super.positions.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Tambah Posisi
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
      <h4>Daftar Posisi</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Nama Posisi</th>
              <th>Deskripsi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($positions as $position)
              <tr>
                <td>{{ $position->name }}</td>
                <td>{{ $position->description ?? '-' }}</td>
                <td>
                  @if($position->is_active)
                    <span class="badge badge-success">Aktif</span>
                  @else
                    <span class="badge badge-danger">Nonaktif</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('super.positions.edit', $position) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('super.positions.destroy', $position) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">Belum ada data posisi</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
