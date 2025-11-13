@extends('layouts.app')
@section('title', 'Kelola Jam Kerja')
@section('content')
<div class="section-header">
  <h1>Kelola Jam Kerja</h1>
  <div class="section-header-button">
    <a href="{{ route('admin.work-schedules.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Tambah Jadwal
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
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Cabang</th>
              <th>Posisi</th>
              <th>Jam Masuk</th>
              <th>Jam Pulang</th>
              <th>Toleransi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($schedules as $schedule)
              <tr>
                <td>{{ $schedule->name }}</td>
                <td>{{ $schedule->branch->name }}</td>
                <td>{{ $schedule->position ? $schedule->position->name : 'Semua Posisi' }}</td>
                <td>{{ Carbon\Carbon::parse($schedule->check_in_time)->format('H:i') }}</td>
                <td>{{ Carbon\Carbon::parse($schedule->check_out_time)->format('H:i') }}</td>
                <td>{{ $schedule->late_tolerance }} menit</td>
                <td>
                  <a href="{{ route('admin.work-schedules.edit', $schedule) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.work-schedules.destroy', $schedule) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center">Belum ada data jadwal kerja</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
