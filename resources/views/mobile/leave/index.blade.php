@extends('layouts.mobile')

@section('header-title', 'Pengajuan Cuti/Izin')

@section('content')
<!-- Add Button -->
<a href="{{ route('mobile.leave.create') }}" class="btn btn-mobile btn-mobile-primary" style="margin-bottom: 15px;">
  <i class="fas fa-plus"></i> Ajukan Baru
</a>

<!-- Success Message -->
@if(session('success'))
  <div class="alert alert-success alert-mobile">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
  </div>
@endif

<!-- Leave Requests List -->
@if($leaveRequests->count() > 0)
  @foreach($leaveRequests as $request)
    <div class="mobile-card">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
        <div>
          <h6 style="margin: 0; font-weight: 600; font-size: 15px;">
            @if($request->type === 'leave')
              <i class="fas fa-umbrella-beach"></i> Cuti
            @elseif($request->type === 'sick')
              <i class="fas fa-medkit"></i> Sakit
            @else
              <i class="fas fa-info-circle"></i> Izin
            @endif
          </h6>
          <div style="font-size: 12px; color: #6c757d; margin-top: 3px;">
            {{ $request->created_at->format('d M Y H:i') }}
          </div>
        </div>
        <div>
          @if($request->status === 'pending')
            <span class="badge badge-warning badge-mobile">
              <i class="fas fa-clock"></i> Menunggu
            </span>
          @elseif($request->status === 'approved')
            <span class="badge badge-success badge-mobile">
              <i class="fas fa-check"></i> Disetujui
            </span>
          @else
            <span class="badge badge-danger badge-mobile">
              <i class="fas fa-times"></i> Ditolak
            </span>
          @endif
        </div>
      </div>

      <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
        <div style="font-size: 13px; margin-bottom: 5px;">
          <i class="fas fa-calendar"></i> 
          <strong>{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}</strong>
          s/d
          <strong>{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</strong>
        </div>
        <div style="font-size: 12px; color: #6c757d;">
          {{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }} hari
        </div>
      </div>

      <div style="font-size: 13px; color: #333;">
        <strong>Alasan:</strong><br>
        {{ Str::limit($request->reason, 100) }}
      </div>

      @if($request->response_note && $request->status !== 'pending')
        <div style="margin-top: 10px; padding: 10px; background: {{ $request->status === 'approved' ? '#e8f5e9' : '#ffebee' }}; border-radius: 8px; font-size: 12px;">
          <strong>{{ $request->status === 'approved' ? 'Catatan Persetujuan' : 'Alasan Penolakan' }}:</strong><br>
          {{ $request->response_note }}
        </div>
      @endif
    </div>
  @endforeach

  @if($leaveRequests->hasPages())
    <div style="margin: 20px 0;">
      {{ $leaveRequests->links() }}
    </div>
  @endif
@else
  <div class="mobile-card" style="text-align: center; padding: 40px 20px;">
    <i class="fas fa-inbox" style="font-size: 50px; color: #ddd; margin-bottom: 15px;"></i>
    <h5>Belum Ada Pengajuan</h5>
    <p style="color: #6c757d; font-size: 14px;">Pengajuan cuti/izin Anda akan muncul di sini</p>
  </div>
@endif
@endsection
