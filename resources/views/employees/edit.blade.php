@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('super.employees.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Edit Karyawan</h1>
</div>

<div class="section-body">
  <form action="{{ route('super.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
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
              <label>Nama <span class="text-danger">*</span></label>
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
              <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                value="{{ old('nik', $employee->nik) }}" required>
              @error('nik')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
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
              <label>Cabang <span class="text-danger">*</span></label>
              <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                <option value="">-- Pilih Cabang --</option>
                @foreach($branches as $branch)
                  <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                  </option>
                @endforeach
              </select>
              @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
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
              <label>Tanggal Bergabung <span class="text-danger">*</span></label>
              <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror" 
                value="{{ old('join_date', $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}" required>
              @error('join_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>Foto Wajah</label>
              @if($employee->face_photo)
                <div class="mb-2">
                  <img src="{{ asset('storage/' . $employee->face_photo) }}" class="img-thumbnail" width="150">
                </div>
              @endif
              <input type="file" name="face_photo" class="form-control @error('face_photo') is-invalid @enderror" 
                accept="image/*">
              @error('face_photo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">Upload foto baru untuk mengganti foto lama</small>
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
              <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('super.employees.index') }}" class="btn btn-secondary">
              Batal
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
