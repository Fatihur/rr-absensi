@extends('layouts.app')
@section('title', 'Edit Hari Libur')
@section('content')
<div class="section-header">
  <div class="section-header-back"><a href="{{ route('admin.holidays.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
  <h1>Edit Hari Libur</h1>
</div>
<div class="section-body">
  <div class="card">
    <form action="{{ route('admin.holidays.update', $holiday) }}" method="POST">
      @csrf @method('PUT')
      <div class="card-body">
        <div class="form-group">
          <label>Cabang <span class="text-danger">*</span></label>
          <select name="branch_id" class="form-control" required>
            @foreach($branches as $branch)
              <option value="{{ $branch->id }}" {{ old('branch_id', $holiday->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Nama Hari Libur <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $holiday->name) }}" required>
        </div>
        <div class="form-group">
          <label>Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="date" class="form-control" value="{{ old('date', $holiday->date->format('Y-m-d')) }}" required>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" class="form-control" rows="3">{{ old('description', $holiday->description) }}</textarea>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
