@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('super.users.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Tambah User Baru</h1>
</div>

<div class="section-body">
  <div class="card">
    <div class="card-header">
      <h4>Form Tambah User</h4>
    </div>
    <form action="{{ route('super.users.store') }}" method="POST">
      @csrf
      <div class="card-body">
        <div class="form-group">
          <label>Nama <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name') }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label>Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
            value="{{ old('email') }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label>Password <span class="text-danger">*</span></label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
            required>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Minimal 8 karakter</small>
        </div>

        <div class="form-group">
          <label>Role <span class="text-danger">*</span></label>
          <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
            <option value="">-- Pilih Role --</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                {{ $role->display_name }}
              </option>
            @endforeach
          </select>
          @error('role_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label>Cabang</label>
          <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
            <option value="">-- Pilih Cabang (Opsional) --</option>
            @foreach($branches as $branch)
              <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
              </option>
            @endforeach
          </select>
          @error('branch_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Wajib diisi untuk Admin Cabang dan Karyawan</small>
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
        <a href="{{ route('super.users.index') }}" class="btn btn-secondary">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
