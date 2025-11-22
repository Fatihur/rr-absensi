@extends('layouts.app')
@section('title', 'Edit Jadwal Kerja')
@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Edit Jadwal Kerja</h1>
</div>
<div class="section-body">
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert"><span>&times;</span></button>
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif
  
  <div class="card">
    <form action="{{ route('admin.work-schedules.update', $workSchedule) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="form-group">
          <label>Nama Jadwal <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $workSchedule->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Cabang <span class="text-danger">*</span></label>
          <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
            @foreach($branches as $branch)
              <option value="{{ $branch->id }}" {{ old('branch_id', $workSchedule->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Posisi</label>
          <select name="position_id" class="form-control">
            <option value="">-- Semua Posisi --</option>
            @foreach($positions as $position)
              <option value="{{ $position->id }}" {{ old('position_id', $workSchedule->position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Masuk <span class="text-danger">*</span></label>
              <input type="time" name="check_in_time" class="form-control" value="{{ old('check_in_time', $workSchedule->check_in_time) }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Pulang <span class="text-danger">*</span></label>
              <input type="time" name="check_out_time" class="form-control" value="{{ old('check_out_time', $workSchedule->check_out_time) }}" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Istirahat Mulai</label>
              <input type="time" name="break_start" class="form-control" value="{{ old('break_start', $workSchedule->break_start) }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Jam Istirahat Selesai</label>
              <input type="time" name="break_end" class="form-control" value="{{ old('break_end', $workSchedule->break_end) }}">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Toleransi Keterlambatan (menit) <span class="text-danger">*</span></label>
          <input type="number" name="late_tolerance" class="form-control" value="{{ old('late_tolerance', $workSchedule->late_tolerance) }}" min="0" max="60" required>
        </div>
        <div class="form-group">
          <label>Hari Kerja</label>
          <div class="row">
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="monday" class="custom-control-input" id="edit-day-monday" {{ (is_array(old('working_days')) && in_array('monday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('monday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-monday">Senin</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="tuesday" class="custom-control-input" id="edit-day-tuesday" {{ (is_array(old('working_days')) && in_array('tuesday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('tuesday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-tuesday">Selasa</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="wednesday" class="custom-control-input" id="edit-day-wednesday" {{ (is_array(old('working_days')) && in_array('wednesday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('wednesday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-wednesday">Rabu</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="thursday" class="custom-control-input" id="edit-day-thursday" {{ (is_array(old('working_days')) && in_array('thursday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('thursday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-thursday">Kamis</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="friday" class="custom-control-input" id="edit-day-friday" {{ (is_array(old('working_days')) && in_array('friday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('friday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-friday">Jumat</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="saturday" class="custom-control-input" id="edit-day-saturday" {{ (is_array(old('working_days')) && in_array('saturday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('saturday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-saturday">Sabtu</label>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="working_days[]" value="sunday" class="custom-control-input" id="edit-day-sunday" {{ (is_array(old('working_days')) && in_array('sunday', old('working_days'))) || (!old('working_days') && is_array($workSchedule->working_days) && in_array('sunday', $workSchedule->working_days)) ? 'checked' : '' }}>
                <label class="custom-control-label" for="edit-day-sunday">Minggu</label>
              </div>
            </div>
          </div>
          <small class="text-muted">Kosongkan jika berlaku untuk semua hari</small>
          @error('working_days')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
