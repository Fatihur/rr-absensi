@extends('layouts.app')
@section('title', 'Ajukan Cuti/Izin/Sakit')
@section('content')
<div class="section-header">
  <div class="section-header-back"><a href="{{ route('karyawan.leave-requests.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
  <h1>Ajukan Cuti/Izin/Sakit</h1>
</div>
<div class="section-body">
  <div class="card">
    <form action="{{ route('karyawan.leave-requests.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        <div class="form-group">
          <label>Jenis <span class="text-danger">*</span></label>
          <select name="type" class="form-control @error('type') is-invalid @enderror" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="leave" {{ old('type') == 'leave' ? 'selected' : '' }}>Cuti</option>
            <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sakit</option>
            <option value="permit" {{ old('type') == 'permit' ? 'selected' : '' }}>Izin</option>
          </select>
          @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
              @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Tanggal Selesai <span class="text-danger">*</span></label>
              <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
              @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Alasan <span class="text-danger">*</span></label>
          <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason') }}</textarea>
          @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Lampiran</label>
          <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
          @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <small class="text-muted">Upload surat dokter (untuk sakit) atau dokumen pendukung lainnya. Max 2MB (PDF, JPG, PNG)</small>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Pengajuan</button>
        <a href="{{ route('karyawan.leave-requests.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
