@extends('layouts.app')

@section('title', 'Kelola Pengajuan Cuti/Izin')

@section('content')
<div class="section-header">
  <h1>Kelola Pengajuan Cuti/Izin</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item">Pengajuan Cuti/Izin</div>
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
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Jenis</label>
                  <select name="type" class="form-control">
                    <option value="">Semua Jenis</option>
                    <option value="leave" {{ request('type') === 'leave' ? 'selected' : '' }}>Cuti</option>
                    <option value="sick" {{ request('type') === 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="permit" {{ request('type') === 'permit' ? 'selected' : '' }}>Izin</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>&nbsp;</label><br>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                  </button>
                  <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
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
          <h4>Daftar Pengajuan</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th>Karyawan</th>
                  <th>Jenis</th>
                  <th>Periode</th>
                  <th>Durasi</th>
                  <th>Alasan</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $branchId = Auth::user()->branch_id;
                  $query = \App\Models\LeaveRequest::with(['employee.user'])
                    ->whereHas('employee', function($q) use ($branchId) {
                      $q->where('branch_id', $branchId);
                    });
                  
                  if (request('status')) {
                    $query->where('status', request('status'));
                  }
                  
                  if (request('type')) {
                    $query->where('type', request('type'));
                  }
                  
                  $leaveRequests = $query->orderBy('created_at', 'desc')->get();
                @endphp
                
                @forelse($leaveRequests as $request)
                  <tr>
                    <td>{{ $request->employee->user->name }}</td>
                    <td>
                      @if($request->type === 'leave')
                        <span class="badge badge-info">Cuti</span>
                      @elseif($request->type === 'sick')
                        <span class="badge badge-warning">Sakit</span>
                      @else
                        <span class="badge badge-secondary">Izin</span>
                      @endif
                    </td>
                    <td>
                      {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} - 
                      {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }} hari</td>
                    <td>{{ Str::limit($request->reason, 50) }}</td>
                    <td>
                      @if($request->status === 'pending')
                        <span class="badge badge-warning">Menunggu</span>
                      @elseif($request->status === 'approved')
                        <span class="badge badge-success">Disetujui</span>
                      @else
                        <span class="badge badge-danger">Ditolak</span>
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $request->id }}">
                        <i class="fas fa-eye"></i>
                      </button>
                      
                      @if($request->status === 'pending')
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $request->id }}">
                          <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $request->id }}">
                          <i class="fas fa-times"></i>
                        </button>
                      @endif
                    </td>
                  </tr>

                  <!-- Detail Modal -->
                  <div class="modal fade" id="detailModal{{ $request->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Detail Pengajuan</h5>
                          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-6">
                              <strong>Karyawan:</strong><br>
                              {{ $request->employee->user->name }}<br>
                              {{ $request->employee->nik }}
                            </div>
                            <div class="col-md-6">
                              <strong>Jenis:</strong><br>
                              @if($request->type === 'leave') Cuti
                              @elseif($request->type === 'sick') Sakit
                              @else Izin
                              @endif
                            </div>
                          </div>
                          <hr>
                          <div class="row">
                            <div class="col-md-6">
                              <strong>Tanggal Mulai:</strong><br>
                              {{ \Carbon\Carbon::parse($request->start_date)->format('d F Y') }}
                            </div>
                            <div class="col-md-6">
                              <strong>Tanggal Selesai:</strong><br>
                              {{ \Carbon\Carbon::parse($request->end_date)->format('d F Y') }}
                            </div>
                          </div>
                          <hr>
                          <div>
                            <strong>Alasan:</strong><br>
                            {{ $request->reason }}
                          </div>
                          @if($request->attachment)
                            <hr>
                            <div>
                              <strong>Lampiran:</strong><br>
                              <a href="{{ asset('storage/' . $request->attachment) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-file"></i> Lihat Lampiran
                              </a>
                            </div>
                          @endif
                          @if($request->response_note)
                            <hr>
                            <div>
                              <strong>Catatan {{ $request->status === 'approved' ? 'Persetujuan' : 'Penolakan' }}:</strong><br>
                              <div class="alert alert-{{ $request->status === 'approved' ? 'success' : 'danger' }}">
                                {{ $request->response_note }}
                              </div>
                            </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>

                  @if($request->status === 'pending')
                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Setujui Pengajuan</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <form action="{{ route('admin.leave-requests.approve', $request) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <p>Anda yakin ingin menyetujui pengajuan ini?</p>
                              <div class="form-group">
                                <label>Catatan (Opsional)</label>
                                <textarea name="response_note" class="form-control" rows="3"></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Setujui
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Tolak Pengajuan</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <form action="{{ route('admin.leave-requests.reject', $request) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <p>Anda yakin ingin menolak pengajuan ini?</p>
                              <div class="form-group">
                                <label>Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="response_note" class="form-control" rows="3" required></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Tolak
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endif
                @empty
                  <tr>
                    <td colspan="7" class="text-center">Tidak ada pengajuan</td>
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
</script>
@endpush
