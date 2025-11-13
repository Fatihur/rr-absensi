@extends('layouts.app')
@section('title', 'Tambah Hari Libur')
@section('content')
<div class="section-header">
  <div class="section-header-back"><a href="{{ route('admin.holidays.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
  <h1>Tambah Hari Libur</h1>
</div>
<div class="section-body">
  <div class="card">
    <form action="{{ route('admin.holidays.store') }}" method="POST">
      @csrf
      <div class="card-body">
        <div class="form-group">
          <label>Cabang <span class="text-danger">*</span></label>
          <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
            <option value="">-- Pilih Cabang --</option>
            @foreach($branches as $branch)
              <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
          </select>
          @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Nama Hari Libur <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
          @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
