@extends('layouts.app')
@section('title', 'Pengajuan Cuti/Izin/Sakit')
@section('content')
<div class="section-header">
  <h1>Pengajuan Cuti/Izin/Sakit</h1>
  <div class="section-header-button">
    <a href="{{ route('karyawan.leave-requests.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Ajukan Baru</a>
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
            <tr><th>Tanggal Pengajuan</th><th>Jenis</th><th>Periode</th><th>Alasan</th><th>Status</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            @forelse($leaveRequests as $request)
              <tr>
                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  @if($request->type === 'leave')<span class="badge badge-info">Cuti</span>
                  @elseif($request->type === 'sick')<span class="badge badge-warning">Sakit</span>
                  @else<span class="badge badge-secondary">Izin</span>@endif
                </td>
                <td>{{ $request->start_date->format('d/m/Y') }} - {{ $request->end_date->format('d/m/Y') }}</td>
                <td>{{ Str::limit($request->reason, 50) }}</td>
                <td>
                  @if($request->status === 'pending')<span class="badge badge-warning">Menunggu</span>
                  @elseif($request->status === 'approved')<span class="badge badge-success">Disetujui</span>
                  @else<span class="badge badge-danger">Ditolak</span>@endif
                </td>
                <td>
                  <a href="{{ route('karyawan.leave-requests.show', $request) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center">Belum ada pengajuan</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $leaveRequests->links() }}</div>
    </div>
  </div>
</div>
@endsection
