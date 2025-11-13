@extends('layouts.app')
@section('title', 'Kelola Hari Libur')
@section('content')
<div class="section-header">
  <h1>Kelola Hari Libur</h1>
  <div class="section-header-button">
    <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Hari Libur</a>
  </div>
</div>
<div class="section-body">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible show fade">
      <div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>{{ session('success') }}</div>
    </div>
  @endif
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr><th>Tanggal</th><th>Nama</th><th>Cabang</th><th>Deskripsi</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            @forelse($holidays as $holiday)
              <tr>
                <td>{{ $holiday->date->format('d/m/Y') }}</td>
                <td>{{ $holiday->name }}</td>
                <td>{{ $holiday->branch->name }}</td>
                <td>{{ $holiday->description ?? '-' }}</td>
                <td>
                  <a href="{{ route('admin.holidays.edit', $holiday) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center">Belum ada data hari libur</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
