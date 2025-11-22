@extends('layouts.app')

@section('title', 'Pengaturan Lokasi Cabang')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map {
    height: 450px;
    border-radius: 8px;
    border: 2px solid #e3e6f0;
  }
  .location-info {
    background: #f8f9fc;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
  }
  .location-info strong {
    color: #5a5c69;
  }
  .btn-gps {
    position: relative;
  }
  .btn-gps .spinner-border {
    width: 1rem;
    height: 1rem;
    margin-right: 5px;
  }
</style>
@endpush

@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Pengaturan Lokasi Cabang</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('admin.work-schedules.index') }}">Admin Cabang</a></div>
        <div class="breadcrumb-item">Pengaturan Lokasi</div>
      </div>
    </div>

    <div id="alertContainer"></div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-map-marker-alt"></i> Lokasi Cabang: {{ $branch->name }}</h4>
            </div>
            <div class="card-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Petunjuk:</strong> 
                <ul class="mb-0 mt-2">
                  <li>Klik pada peta untuk mengatur lokasi kantor cabang</li>
                  <li>Atau gunakan tombol "Gunakan Lokasi Saya Saat Ini" untuk menggunakan lokasi GPS Anda</li>
                  <li>Atur radius untuk menentukan area absensi yang diperbolehkan</li>
                  <li>Karyawan hanya bisa absen dalam radius yang telah ditentukan</li>
                </ul>
              </div>

              <!-- Current Location Info -->
              <div class="location-info">
                <div class="row">
                  <div class="col-md-4">
                    <strong>Nama Cabang:</strong><br>
                    <span class="text-primary">{{ $branch->name }}</span>
                  </div>
                  <div class="col-md-8">
                    <strong>Alamat:</strong><br>
                    {{ $branch->address }}
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-4">
                    <strong>Latitude:</strong><br>
                    <span id="currentLat">{{ $branch->latitude ?? 'Belum diatur' }}</span>
                  </div>
                  <div class="col-md-4">
                    <strong>Longitude:</strong><br>
                    <span id="currentLng">{{ $branch->longitude ?? 'Belum diatur' }}</span>
                  </div>
                  <div class="col-md-4">
                    <strong>Radius:</strong><br>
                    <span id="currentRadius">{{ $branch->radius ?? 100 }}m</span>
                  </div>
                </div>
              </div>

              <!-- GPS Button -->
              <div class="mb-3">
                <button type="button" class="btn btn-primary btn-gps" id="useMyLocationBtn">
                  <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya Saat Ini
                </button>
                <small class="form-text text-muted">
                  <i class="fas fa-shield-alt"></i> Browser akan meminta izin akses lokasi Anda
                </small>
              </div>

              <!-- Map -->
              <div id="map"></div>

              <!-- Form -->
              <form id="locationForm" class="mt-4">
                @csrf
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Latitude <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="latitude" name="latitude" 
                             value="{{ $branch->latitude }}" readonly required>
                      <small class="form-text text-muted">Klik pada peta untuk mengatur</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Longitude <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="longitude" name="longitude" 
                             value="{{ $branch->longitude }}" readonly required>
                      <small class="form-text text-muted">Klik pada peta untuk mengatur</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Radius (meter) <span class="text-danger">*</span></label>
                      <input type="number" class="form-control" id="radius" name="radius" 
                             value="{{ $branch->radius ?? 100 }}" min="10" max="1000" required>
                      <small class="form-text text-muted">Min: 10m, Max: 1000m</small>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                    <i class="fas fa-save"></i> Simpan Lokasi
                  </button>
                  <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Kembali
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  var map, marker, circle;
  var defaultLat = {{ $branch->latitude ?? -6.2088 }};
  var defaultLng = {{ $branch->longitude ?? 106.8456 }};
  var defaultRadius = {{ $branch->radius ?? 100 }};

  function showAlert(message, type = 'info') {
    const alertHtml = `
      <div class="alert alert-${type} alert-dismissible show fade" style="margin: 0 20px 20px 20px;">
        <div class="alert-body">
          <button class="close" data-dismiss="alert"><span>&times;</span></button>
          ${message}
        </div>
      </div>
    `;
    $('#alertContainer').html(alertHtml);
    setTimeout(() => {
      $('.alert').fadeOut(() => $('.alert').remove());
    }, 5000);
  }

  // Initialize map
  map = L.map('map').setView([defaultLat, defaultLng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  // Add marker
  marker = L.marker([defaultLat, defaultLng], {
    draggable: true,
    icon: L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    })
  }).addTo(map);

  marker.bindPopup('<strong>{{ $branch->name }}</strong><br>{{ $branch->address }}').openPopup();

  // Add radius circle
  circle = L.circle([defaultLat, defaultLng], {
    color: '#667eea',
    fillColor: '#667eea',
    fillOpacity: 0.2,
    radius: defaultRadius
  }).addTo(map);

  // Update form when marker is dragged
  marker.on('dragend', function(e) {
    var position = marker.getLatLng();
    updateLocation(position.lat, position.lng);
  });

  // Click on map to set location
  map.on('click', function(e) {
    updateLocation(e.latlng.lat, e.latlng.lng);
  });

  // Update radius circle when radius input changes
  $('#radius').on('input', function() {
    var newRadius = parseInt($(this).val()) || 100;
    var position = marker.getLatLng();
    circle.setRadius(newRadius);
    $('#currentRadius').text(newRadius + 'm');
  });

  function updateLocation(lat, lng) {
    marker.setLatLng([lat, lng]);
    circle.setLatLng([lat, lng]);
    map.panTo([lat, lng]);
    
    $('#latitude').val(lat.toFixed(8));
    $('#longitude').val(lng.toFixed(8));
    $('#currentLat').text(lat.toFixed(8));
    $('#currentLng').text(lng.toFixed(8));
    
    marker.bindPopup(
      '<strong>{{ $branch->name }}</strong><br>' +
      'Lat: ' + lat.toFixed(6) + '<br>' +
      'Lng: ' + lng.toFixed(6)
    ).openPopup();
  }

  // Use My Location Button
  $('#useMyLocationBtn').on('click', function() {
    var btn = $(this);
    btn.prop('disabled', true);
    btn.html('<span class="spinner-border spinner-border-sm"></span> Mendapatkan lokasi...');

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          var lat = position.coords.latitude;
          var lng = position.coords.longitude;
          
          updateLocation(lat, lng);
          map.setView([lat, lng], 17);
          
          btn.prop('disabled', false);
          btn.html('<i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya Saat Ini');
          
          // Show success notification
          showAlert('Lokasi Anda berhasil digunakan', 'success');
        },
        function(error) {
          btn.prop('disabled', false);
          btn.html('<i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya Saat Ini');
          
          var errorMsg = 'Tidak dapat mendapatkan lokasi Anda. ';
          
          switch(error.code) {
            case error.PERMISSION_DENIED:
              errorMsg += 'Izin akses lokasi ditolak. Silakan aktifkan di pengaturan browser.';
              break;
            case error.POSITION_UNAVAILABLE:
              errorMsg += 'Informasi lokasi tidak tersedia.';
              break;
            case error.TIMEOUT:
              errorMsg += 'Request timeout.';
              break;
            default:
              errorMsg += 'Error tidak diketahui.';
          }
          
          showAlert(errorMsg, 'danger');
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        }
      );
    } else {
      btn.prop('disabled', false);
      btn.html('<i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya Saat Ini');
      
      showAlert('Browser Anda tidak mendukung Geolocation', 'warning');
    }
  });

  // Form Submit
  $('#locationForm').on('submit', function(e) {
    e.preventDefault();
    
    var submitBtn = $('#submitBtn');
    var originalHtml = submitBtn.html();
    
    submitBtn.prop('disabled', true);
    submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
    
    $.ajax({
      url: '{{ route("admin.branch.location.update") }}',
      method: 'POST',
      data: $(this).serialize(),
      success: function(response) {
        showAlert(response.message, 'success');
        
        // Update current location info
        $('#currentLat').text(response.data.latitude);
        $('#currentLng').text(response.data.longitude);
        $('#currentRadius').text(response.data.radius + 'm');
        
        submitBtn.prop('disabled', false);
        submitBtn.html(originalHtml);
      },
      error: function(xhr) {
        var message = 'Terjadi kesalahan';
        
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
          var errors = xhr.responseJSON.errors;
          message = Object.values(errors).flat().join('<br>');
        }
        
        showAlert(message, 'danger');
        
        submitBtn.prop('disabled', false);
        submitBtn.html(originalHtml);
      }
    });
  });
</script>
@endpush
