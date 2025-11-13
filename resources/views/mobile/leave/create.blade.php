@extends('layouts.mobile')

@section('header-title', 'Ajukan Cuti/Izin')

@section('content')
<form action="{{ route('mobile.leave.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  
  <div class="mobile-card">
    <div class="mobile-card-title">Form Pengajuan</div>
    
    <div class="form-group">
      <label style="font-size: 14px; font-weight: 600; margin-bottom: 8px; display: block;">
        Jenis <span style="color: red;">*</span>
      </label>
      <select name="type" class="form-control @error('type') is-invalid @enderror" required style="border-radius: 10px;">
        <option value="">-- Pilih Jenis --</option>
        <option value="leave" {{ old('type') === 'leave' ? 'selected' : '' }}>Cuti</option>
        <option value="sick" {{ old('type') === 'sick' ? 'selected' : '' }}>Sakit</option>
        <option value="permit" {{ old('type') === 'permit' ? 'selected' : '' }}>Izin</option>
      </select>
      @error('type')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label style="font-size: 14px; font-weight: 600; margin-bottom: 8px; display: block;">
        Tanggal Mulai <span style="color: red;">*</span>
      </label>
      <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
        value="{{ old('start_date') }}" required style="border-radius: 10px;">
      @error('start_date')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label style="font-size: 14px; font-weight: 600; margin-bottom: 8px; display: block;">
        Tanggal Selesai <span style="color: red;">*</span>
      </label>
      <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
        value="{{ old('end_date') }}" required style="border-radius: 10px;">
      @error('end_date')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label style="font-size: 14px; font-weight: 600; margin-bottom: 8px; display: block;">
        Alasan <span style="color: red;">*</span>
      </label>
      <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" 
        rows="4" required style="border-radius: 10px;">{{ old('reason') }}</textarea>
      @error('reason')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <small class="form-text text-muted">Jelaskan alasan pengajuan Anda</small>
    </div>

    <div class="form-group">
      <label style="font-size: 14px; font-weight: 600; margin-bottom: 8px; display: block;">
        Lampiran (Opsional)
      </label>
      <input type="file" name="attachment" class="form-control-file @error('attachment') is-invalid @enderror" 
        accept=".pdf,.jpg,.jpeg,.png">
      @error('attachment')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <small class="form-text text-muted">Upload surat dokter (untuk sakit) atau dokumen pendukung. Max 2MB</small>
    </div>
  </div>

  <button type="submit" class="btn btn-mobile btn-mobile-primary">
    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
  </button>
  
  <a href="{{ route('mobile.leave') }}" class="btn btn-mobile" style="background: #e9ecef; color: #333; margin-top: 10px;">
    <i class="fas fa-times"></i> Batal
  </a>
</form>
@endsection
