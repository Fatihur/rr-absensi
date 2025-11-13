@extends('layouts.app')

@section('title', 'Edit Cabang')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map { height: 400px; }
</style>
@endpush

@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="{{ route('super.branches.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Edit Cabang</h1>
</div>

<div class="section-body">
  <div class="card">
    <div class="card-header">
      <h4>Form Edit Cabang</h4>
    </div>
    <form action="{{ route('super.branches.update', $branch) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="form-group">
          <label>Nama Cabang <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
            value="{{ old('name', $branch->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label>Alamat <span class="text-danger">*</span></label>
          <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
            rows="3" required>{{ old('address', $branch->address) }}</textarea>
          @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Latitude</label>
              <input type="text" id="latitude" name="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                value="{{ old('latitude', $branch->latitude) }}" readonly>
              @error('latitude')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Longitude</label>
              <input type="text" id="longitude" name="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                value="{{ old('longitude', $branch->longitude) }}" readonly>
              @error('longitude')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Peta Lokasi Kantor</label>
          <p class="text-muted small">Klik pada peta untuk mengubah lokasi kantor</p>
          <div id="map"></div>
        </div>

        <div class="form-group">
          <label>Radius Absensi (meter) <span class="text-danger">*</span></label>
          <input type="number" name="radius" class="form-control @error('radius') is-invalid @enderror" 
            value="{{ old('radius', $branch->radius) }}" min="10" max="1000" required>
          @error('radius')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="form-text text-muted">Jarak maksimal dari lokasi kantor untuk absensi (10-1000 meter)</small>
        </div>

        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
              {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_active">Aktif</label>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update
        </button>
        <a href="{{ route('super.branches.index') }}" class="btn btn-secondary">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  var initialLat = {{ $branch->latitude ?? -6.2088 }};
  var initialLng = {{ $branch->longitude ?? 106.8456 }};

  var map = L.map('map').setView([initialLat, initialLng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  var marker = L.marker([initialLat, initialLng]).addTo(map);

  map.on('click', function(e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    if (marker) {
      map.removeLayer(marker);
    }

    marker = L.marker([lat, lng]).addTo(map);

    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
  });
</script>
@endpush
