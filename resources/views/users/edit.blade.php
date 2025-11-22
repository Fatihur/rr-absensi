@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('super.users.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Edit User</h1>
</div>

<div class="section-body">
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible show fade">
      <div class="alert-title">Error!</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button class="close" data-dismiss="alert">
        <span>&times;</span>
      </button>
    </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h4>Form Edit User</h4>
    </div>
    <form action="{{ route('super.users.update', $user) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="form-group">
          <label>Nama <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name', $user->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label>Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
            value="{{ old('email', $user->email) }}" required>
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
          <label>Role <span class="text-danger">*</span></label>
          <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
            <option value="">-- Pilih Role --</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
              <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
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
            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
              {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_active">Aktif</label>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update
        </button>
        <a href="{{ route('super.users.index') }}" class="btn btn-secondary">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
