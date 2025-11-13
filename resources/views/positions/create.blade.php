@extends('layouts.app')

@section('title', 'Tambah Posisi')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('super.positions.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Tambah Posisi Baru</h1>
</div>

<div class="section-body">
  <div class="card">
    <div class="card-header">
      <h4>Form Tambah Posisi</h4>
    </div>
    <form action="{{ route('super.positions.store') }}" method="POST">
      @csrf
      <div class="card-body">
        <div class="form-group">
          <label>Nama Posisi <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name') }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
            rows="3">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
              {{ old('is_active', true) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_active">Aktif</label>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Simpan
        </button>
        <a href="{{ route('super.positions.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
