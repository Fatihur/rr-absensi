@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Edit Karyawan</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Karyawan</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.employees.show', $employee) }}">Detail</a></div>
    <div class="breadcrumb-item">Edit</div>
  </div>
</div>

<div class="section-body">
  <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
      <div class="col-12 col-md-6">
        <div class="card">
          <div class="card-header">
            <h4>Data Akun</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Nama Pengguna <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                value="{{ old('name', $employee->user->name) }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                value="{{ old('email', $employee->user->email) }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
            </div>

            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                  {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">Aktif</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6">
        <div class="card">
          <div class="card-header">
            <h4>Data Karyawan</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>NIK <span class="text-danger">*</span></label>
              <input type="text" class="form-control" value="{{ $employee->nik }}" readonly>
              <small class="form-text text-muted">NIK tidak dapat diubah</small>
            </div>

            <div class="form-group">
              <label>Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                value="{{ old('full_name', $employee->full_name) }}" required>
              @error('full_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>No. HP</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                value="{{ old('phone', $employee->phone) }}">
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>Cabang</label>
              <input type="text" class="form-control" value="{{ $employee->branch->name }}" readonly>
              <small class="form-text text-muted">Cabang tidak dapat diubah</small>
            </div>

            <div class="form-group">
              <label>Posisi <span class="text-danger">*</span></label>
              <select name="position_id" class="form-control @error('position_id') is-invalid @enderror" required>
                <option value="">-- Pilih Posisi --</option>
                @foreach($positions as $position)
                  <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                    {{ $position->name }}
                  </option>
                @endforeach
              </select>
              @error('position_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>Tanggal Bergabung</label>
              <input type="text" class="form-control" value="{{ $employee->join_date ? $employee->join_date->format('d F Y') : '-' }}" readonly>
              <small class="form-text text-muted">Tanggal bergabung tidak dapat diubah</small>
            </div>

            <div class="form-group">
              <label>Foto Wajah</label>
              @if($employee->face_photo)
                <div class="mb-2">
                  <img src="{{ asset('storage/' . $employee->face_photo) }}" class="img-thumbnail" width="150">
                </div>
              @endif
              <input type="file" name="face_photo" class="form-control-file @error('face_photo') is-invalid @enderror" 
                accept="image/*">
              @error('face_photo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">Upload foto baru untuk mengganti foto lama. Max 2MB</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-secondary">
              <i class="fas fa-times"></i> Batal
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
