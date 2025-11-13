@extends('layouts.mobile')

@section('header-title', 'Riwayat Absensi')

@section('content')
@if($attendances->count() > 0)
  @foreach($attendances as $attendance)
    <div class="mobile-card">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
        <div>
          <h6 style="margin: 0; font-weight: 600; font-size: 15px;">
            {{ $attendance->date->isoFormat('dddd') }}
          </h6>
          <div style="font-size: 13px; color: #6c757d; margin-top: 3px;">
            {{ $attendance->date->format('d F Y') }}
          </div>
        </div>
        <div>
          @if($attendance->status === 'valid')
            <span class="badge badge-success badge-mobile">Valid</span>
          @elseif($attendance->status === 'late')
            <span class="badge badge-warning badge-mobile">Terlambat</span>
          @elseif($attendance->status === 'leave')
            <span class="badge badge-info badge-mobile">Cuti</span>
          @elseif($attendance->status === 'sick')
            <span class="badge badge-info badge-mobile">Sakit</span>
          @elseif($attendance->status === 'permit')
            <span class="badge badge-info badge-mobile">Izin</span>
          @else
            <span class="badge badge-danger badge-mobile">Bermasalah</span>
          @endif
        </div>
      </div>

      <div style="display: flex; gap: 15px;">
        <div style="flex: 1;">
          <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <div style="width: 35px; height: 35px; border-radius: 8px; background: #e8f5e9; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
              <i class="fas fa-sign-in-alt" style="color: #28a745;"></i>
            </div>
            <div>
              <div style="font-size: 11px; color: #6c757d;">Masuk</div>
              <div style="font-size: 16px; font-weight: 600;">
                {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
              </div>
            </div>
          </div>
        </div>
        
        <div style="flex: 1;">
          <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <div style="width: 35px; height: 35px; border-radius: 8px; background: #ffebee; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
              <i class="fas fa-sign-out-alt" style="color: #dc3545;"></i>
            </div>
            <div>
              <div style="font-size: 11px; color: #6c757d;">Pulang</div>
              <div style="font-size: 16px; font-weight: 600;">
                {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
              </div>
            </div>
          </div>
        </div>
      </div>

      @if($attendance->notes)
        <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px; font-size: 12px;">
          <i class="fas fa-sticky-note"></i> {{ $attendance->notes }}
        </div>
      @endif
    </div>
  @endforeach

  <!-- Pagination -->
  @if($attendances->hasPages())
    <div style="margin: 20px 0;">
      {{ $attendances->links() }}
    </div>
  @endif
@else
  <div class="mobile-card" style="text-align: center; padding: 40px 20px;">
    <i class="fas fa-inbox" style="font-size: 50px; color: #ddd; margin-bottom: 15px;"></i>
    <h5>Belum Ada Riwayat</h5>
    <p style="color: #6c757d; font-size: 14px;">Riwayat absensi Anda akan muncul di sini</p>
  </div>
@endif
@endsection
