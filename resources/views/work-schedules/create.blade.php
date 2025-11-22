@extends('layouts.app')
@section('title', 'Tambah Jadwal Kerja')
@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Tambah Jadwal Kerja</h1>
</div>
<div class="section-body">
  <div class="card">
    <form action="{{ route('admin.work-schedules.store') }}" method="POST">
      @csrf
      <div class="card-body">
        <div class="form-group">
          <label>Nama Jadwal <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
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
          <label>Posisi</label>
          <select name="position_id" class="form-control @error('position_id') is-invalid @enderror">
            <option value="">-- Semua Posisi --</option>
            @foreach($positions as $position)
              <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
            @endforeach
          </select>
          @error('position_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <small class="text-muted">Kosongkan untuk berlaku ke semua posisi</small>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Masuk <span class="text-danger">*</span></label>
              <input type="time" name="check_in_time" class="form-control @error('check_in_time') is-invalid @enderror" value="{{ old('check_in_time') }}" required>
              @error('check_in_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Pulang <span class="text-danger">*</span></label>
              <input type="time" name="check_out_time" class="form-control @error('check_out_time') is-invalid @enderror" value="{{ old('check_out_time') }}" required>
              @error('check_out_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Istirahat Mulai</label>
              <input type="time" name="break_start" class="form-control @error('break_start') is-invalid @enderror" value="{{ old('break_start') }}">
              @error('break_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Istirahat Selesai</label>
              <input type="time" name="break_end" class="form-control @error('break_end') is-invalid @enderror" value="{{ old('break_end') }}">
              @error('break_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Toleransi Keterlambatan (menit) <span class="text-danger">*</span></label>
          <input type="number" name="late_tolerance" class="form-control @error('late_tolerance') is-invalid @enderror" value="{{ old('late_tolerance', 15) }}" min="0" max="60" required>
          @error('late_tolerance')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Hari Kerja</label>
          <div class="row">
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="monday" class="custom-control-input" id="day-monday" {{ is_array(old('working_days')) && in_array('monday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-monday">Senin</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="tuesday" class="custom-control-input" id="day-tuesday" {{ is_array(old('working_days')) && in_array('tuesday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-tuesday">Selasa</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="wednesday" class="custom-control-input" id="day-wednesday" {{ is_array(old('working_days')) && in_array('wednesday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-wednesday">Rabu</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="thursday" class="custom-control-input" id="day-thursday" {{ is_array(old('working_days')) && in_array('thursday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-thursday">Kamis</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="friday" class="custom-control-input" id="day-friday" {{ is_array(old('working_days')) && in_array('friday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-friday">Jumat</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="saturday" class="custom-control-input" id="day-saturday" {{ is_array(old('working_days')) && in_array('saturday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-saturday">Sabtu</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="sunday" class="custom-control-input" id="day-sunday" {{ is_array(old('working_days')) && in_array('sunday', old('working_days')) ? 'checked' : '' }}>
                <label class="custom-control-label" for="day-sunday">Minggu</label>
              </div>
            </div>
          </div>
          <small class="text-muted">Kosongkan jika berlaku untuk semua hari</small>
          @error('working_days')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
