@extends('layouts.mobile')

@section('header-title', 'Absensi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map {
    height: 250px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 15px;
  }

  #video, #canvas {
    width: 100%;
    max-width: 100%;
    border-radius: 12px;
  }

  #canvas {
    display: none;
  }

  .camera-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 10000;
    display: none;
    flex-direction: column;
  }

  .camera-modal.show {
    display: flex;
  }

  .camera-header {
    padding: 15px 20px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .camera-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .camera-footer {
    padding: 20px;
    display: flex;
    gap: 10px;
    justify-content: center;
  }
</style>
@endpush

@section('content')
<!-- Location Info -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-map-marker-alt"></i> Lokasi Anda
  </div>
  
  <div id="map"></div>
  
  <div id="locationInfo" class="alert alert-info alert-mobile">
    <i class="fas fa-spinner fa-spin"></i> Mendapatkan lokasi...
  </div>
  
  <div style="font-size: 13px; color: #6c757d; margin-top: 10px;">
    <div style="margin-bottom: 5px;">
      <i class="fas fa-building"></i> <strong>{{ $branch->name }}</strong>
    </div>
    <div style="margin-bottom: 5px;">
      <i class="fas fa-map-marker-alt"></i> {{ $branch->address }}
    </div>
    <div>
      <i class="fas fa-circle-notch"></i> Radius: {{ $branch->radius }}m
    </div>
  </div>
</div>

<!-- Attendance Status -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-fingerprint"></i> Status Absensi
  </div>
  
  @if($attendance && $attendance->check_in)
    <div class="alert alert-success alert-mobile">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <div>
          <strong>Check-in:</strong> {{ $attendance->check_in->format('H:i:s') }}
        </div>
        <div>
          @if($attendance->status === 'valid')
            <span class="badge badge-success badge-mobile">Valid</span>
          @elseif($attendance->status === 'late')
            <span class="badge badge-warning badge-mobile">Terlambat</span>
          @else
            <span class="badge badge-danger badge-mobile">Bermasalah</span>
          @endif
        </div>
      </div>
    </div>

    @if(!$attendance->check_out)
      <button type="button" class="btn btn-mobile btn-mobile-danger" id="checkOutBtn">
        <i class="fas fa-sign-out-alt"></i> Check-Out Sekarang
      </button>
    @else
      <div class="alert alert-info alert-mobile">
        <strong>Check-out:</strong> {{ $attendance->check_out->format('H:i:s') }}<br>
        <small>Anda sudah menyelesaikan absensi hari ini.</small>
      </div>
    @endif
  @else
    <div class="alert alert-warning alert-mobile">
      Anda belum melakukan check-in hari ini.
    </div>
    <button type="button" class="btn btn-mobile btn-mobile-primary" id="checkInBtn">
      <i class="fas fa-fingerprint"></i> Check-In Sekarang
    </button>
  @endif
</div>

<!-- Work Schedule -->
@if($workSchedule)
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-clock"></i> Jadwal Kerja
  </div>
  
  <div style="display: flex; gap: 10px;">
    <div style="flex: 1; background: #f8f9fa; padding: 12px; border-radius: 10px; text-align: center;">
      <div style="font-size: 11px; color: #6c757d; margin-bottom: 5px;">Jam Masuk</div>
      <div style="font-size: 20px; font-weight: bold; color: #667eea;">
        {{ Carbon\Carbon::parse($workSchedule->check_in_time)->format('H:i') }}
      </div>
    </div>
    <div style="flex: 1; background: #f8f9fa; padding: 12px; border-radius: 10px; text-align: center;">
      <div style="font-size: 11px; color: #6c757d; margin-bottom: 5px;">Jam Pulang</div>
      <div style="font-size: 20px; font-weight: bold; color: #667eea;">
        {{ Carbon\Carbon::parse($workSchedule->check_out_time)->format('H:i') }}
      </div>
    </div>
  </div>
  
  <div style="margin-top: 10px; text-align: center; font-size: 13px; color: #6c757d;">
    <i class="fas fa-info-circle"></i> Toleransi keterlambatan: {{ $workSchedule->late_tolerance }} menit
  </div>
</div>
@endif

<!-- Camera Modal -->
<div class="camera-modal" id="cameraModal">
  <div class="camera-header">
    <h5 style="margin: 0;">Ambil Foto</h5>
    <button type="button" style="background: none; border: none; color: white; font-size: 24px;" id="closeCamera">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <div class="camera-content">
    <div style="width: 100%; max-width: 400px;">
      <video id="video" autoplay playsinline></video>
      <canvas id="canvas"></canvas>
      <div style="text-align: center; margin-top: 15px; color: white; font-size: 13px;">
        <i class="fas fa-info-circle"></i> Pastikan wajah Anda terlihat jelas
      </div>
    </div>
  </div>
  <div class="camera-footer">
    <button type="button" class="btn btn-mobile btn-mobile-primary" id="captureBtn" style="flex: 1; max-width: 200px;">
      <i class="fas fa-camera"></i> Ambil Foto
    </button>
    <button type="button" class="btn btn-mobile btn-mobile-success" id="submitBtn" style="flex: 1; max-width: 200px; display: none;">
      <i class="fas fa-check"></i> Submit
    </button>
  </div>
</div>

<input type="hidden" id="latitude">
<input type="hidden" id="longitude">
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  var map, marker;
  var branchLat = {{ $branch->latitude ?? -6.2088 }};
  var branchLng = {{ $branch->longitude ?? 106.8456 }};
  var branchRadius = {{ $branch->radius }};
  var currentAction = '';
  var capturedBlob = null;
  var stream;

  // Initialize map
  map = L.map('map').setView([branchLat, branchLng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  // Branch marker
  L.marker([branchLat, branchLng], {
    icon: L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    })
  }).addTo(map).bindPopup('<strong>{{ $branch->name }}</strong>');

  // Radius circle
  L.circle([branchLat, branchLng], {
    color: '#667eea',
    fillColor: '#667eea',
    fillOpacity: 0.2,
    radius: branchRadius
  }).addTo(map);

  // Get current location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var lat = position.coords.latitude;
      var lng = position.coords.longitude;

      $('#latitude').val(lat);
      $('#longitude').val(lng);

      marker = L.marker([lat, lng], {
        icon: L.icon({
          iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
          shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
          iconSize: [25, 41],
          iconAnchor: [12, 41],
          popupAnchor: [1, -34],
          shadowSize: [41, 41]
        })
      }).addTo(map).bindPopup('<strong>Lokasi Anda</strong>');

      map.setView([lat, lng], 16);

      var distance = calculateDistance(lat, lng, branchLat, branchLng);
      
      if (distance <= branchRadius) {
        $('#locationInfo').removeClass('alert-info alert-danger').addClass('alert-success');
        $('#locationInfo').html('<i class="fas fa-check-circle"></i> Anda dalam radius kantor (' + Math.round(distance) + 'm)');
      } else {
        $('#locationInfo').removeClass('alert-info alert-success').addClass('alert-danger');
        $('#locationInfo').html('<i class="fas fa-exclamation-triangle"></i> Anda di luar radius (' + Math.round(distance) + 'm)');
      }
    }, function(error) {
      $('#locationInfo').removeClass('alert-info').addClass('alert-danger');
      $('#locationInfo').html('<i class="fas fa-exclamation-triangle"></i> Gagal mendapatkan lokasi');
    });
  }

  function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  $('#checkInBtn').on('click', function() {
    if (!$('#latitude').val()) {
      alert('Tunggu hingga lokasi terdeteksi');
      return;
    }
    currentAction = 'check-in';
    openCamera();
  });

  $('#checkOutBtn').on('click', function() {
    if (!$('#latitude').val()) {
      alert('Tunggu hingga lokasi terdeteksi');
      return;
    }
    currentAction = 'check-out';
    openCamera();
  });

  function openCamera() {
    $('#cameraModal').addClass('show');
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
      .then(function(s) {
        stream = s;
        document.getElementById('video').srcObject = stream;
        $('#video').show();
        $('#canvas').hide();
        $('#captureBtn').show();
        $('#submitBtn').hide();
      })
      .catch(function(err) {
        alert('Error: ' + err.message);
        $('#cameraModal').removeClass('show');
      });
  }

  function closeCamera() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    $('#cameraModal').removeClass('show');
    $('#video').show();
    $('#canvas').hide();
    $('#captureBtn').show();
    $('#submitBtn').hide();
  }

  $('#closeCamera').on('click', closeCamera);

  $('#captureBtn').on('click', function() {
    var video = document.getElementById('video');
    var canvas = document.getElementById('canvas');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    $('#video').hide();
    $('#canvas').show();
    $('#captureBtn').hide();
    $('#submitBtn').show();

    canvas.toBlob(function(blob) {
      capturedBlob = blob;
    }, 'image/jpeg', 0.8);

    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
  });

  $('#submitBtn').on('click', function() {
    if (!capturedBlob) {
      alert('Silakan ambil foto terlebih dahulu');
      return;
    }

    showLoading();

    var formData = new FormData();
    formData.append('photo', capturedBlob, 'attendance.jpg');
    formData.append('latitude', $('#latitude').val());
    formData.append('longitude', $('#longitude').val());

    var url = currentAction === 'check-in' 
      ? '{{ route("mobile.attendance.check-in") }}'
      : '{{ route("mobile.attendance.check-out") }}';

    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        hideLoading();
        alert(response.message);
        location.reload();
      },
      error: function(xhr) {
        hideLoading();
        var message = xhr.responseJSON && xhr.responseJSON.message 
          ? xhr.responseJSON.message 
          : 'Terjadi kesalahan';
        alert(message);
      }
    });
  });
</script>
@endpush
