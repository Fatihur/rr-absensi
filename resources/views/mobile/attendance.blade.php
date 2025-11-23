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

  .permission-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    margin-top: 10px;
  }

  .permission-status.granted {
    background: #d4edda;
    color: #155724;
  }

  .permission-status.denied {
    background: #f8d7da;
    color: #721c24;
  }

  .permission-status.pending {
    background: #fff3cd;
    color: #856404;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }

  .pulse {
    animation: pulse 1.5s ease-in-out infinite;
  }
</style>
@endpush

@section('content')
<!-- Alert Container -->
<div id="alertContainer"></div>

<!-- Location Info -->
<div class="mobile-card">
  <div class="mobile-card-title">
    <i class="fas fa-map-marker-alt"></i> Lokasi Anda
  </div>
  
  <div id="map"></div>
  
  <div id="locationInfo" class="alert alert-info alert-mobile">
    <i class="fas fa-spinner fa-spin"></i> Mendapatkan lokasi...
  </div>

  <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
    <div id="locationPermissionStatus" class="permission-status pending">
      <i class="fas fa-map-marker-alt pulse"></i>
      <span>Meminta izin lokasi...</span>
    </div>
    <div id="cameraPermissionStatus" class="permission-status pending" style="display: none;">
      <i class="fas fa-camera pulse"></i>
      <span>Izin kamera diperlukan</span>
    </div>
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
      <!-- Break Time Buttons -->
      @if($workSchedule && $workSchedule->break_start && $workSchedule->break_end)
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
          @if(!$attendance->break_start)
            <button type="button" class="btn btn-mobile btn-mobile-warning" id="breakStartBtn" style="flex: 1;">
              <i class="fas fa-coffee"></i> Mulai Istirahat
            </button>
          @elseif($attendance->break_start && !$attendance->break_end)
            <div style="flex: 1; padding: 12px; background: #d1ecf1; border-radius: 10px; text-align: center;">
              <small style="color: #0c5460;"><strong>Istirahat Dimulai</strong><br>{{ $attendance->break_start->format('H:i:s') }}</small>
            </div>
            <button type="button" class="btn btn-mobile btn-mobile-success" id="breakEndBtn" style="flex: 1;">
              <i class="fas fa-play"></i> Kembali Bekerja
            </button>
          @else
            <div style="flex: 1; padding: 12px; background: #d4edda; border-radius: 10px; text-align: center;">
              <small style="color: #155724;">
                <strong>Istirahat Selesai</strong><br>
                {{ $attendance->break_start->format('H:i') }} - {{ $attendance->break_end->format('H:i') }}
              </small>
            </div>
          @endif
        </div>
      @endif

      <button type="button" class="btn btn-mobile btn-mobile-danger" id="checkOutBtn">
        <i class="fas fa-sign-out-alt"></i> Check-Out Sekarang
      </button>
    @else
      <div class="alert alert-info alert-mobile">
        <strong>Check-out:</strong> {{ $attendance->check_out->format('H:i:s') }}<br>
        @if($attendance->break_start && $attendance->break_end)
          <strong>Istirahat:</strong> {{ $attendance->break_start->format('H:i') }} - {{ $attendance->break_end->format('H:i') }}<br>
        @endif
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
    @if($workSchedule->position_id)
      <span class="badge badge-mobile" style="background: #667eea; color: white;">{{ $workSchedule->position->name }}</span>
    @endif
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
  
  @if($workSchedule->break_start && $workSchedule->break_end)
  <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 10px; text-align: center;">
    <div style="font-size: 12px; color: #856404; margin-bottom: 3px;">
      <i class="fas fa-coffee"></i> Jam Istirahat
    </div>
    <div style="font-size: 16px; font-weight: bold; color: #856404;">
      {{ Carbon\Carbon::parse($workSchedule->break_start)->format('H:i') }} - {{ Carbon\Carbon::parse($workSchedule->break_end)->format('H:i') }}
    </div>
  </div>
  @endif
  
  <div style="margin-top: 10px; text-align: center; font-size: 13px; color: #6c757d;">
    <i class="fas fa-info-circle"></i> Toleransi: {{ $workSchedule->late_tolerance }} menit
  </div>
</div>
@else
<div class="mobile-card">
  <div class="alert alert-warning alert-mobile">
    <i class="fas fa-exclamation-triangle"></i> Jadwal kerja belum diatur
  </div>
</div>
@endif

<!-- Permission Help Modal -->
<div class="camera-modal" id="permissionModal" style="background: rgba(0, 0, 0, 0.9);">
  <div class="camera-header">
    <h5 style="margin: 0;"><i class="fas fa-info-circle"></i> Izin Diperlukan</h5>
    <button type="button" style="background: none; border: none; color: white; font-size: 24px;" id="closePermissionModal">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <div class="camera-content">
    <div style="width: 100%; max-width: 500px; color: white; padding: 20px;">
      <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
        <h6 style="color: #ffc107; margin-bottom: 15px;">
          <i class="fas fa-map-marker-alt"></i> Izin Lokasi
        </h6>
        <ol style="font-size: 14px; line-height: 1.8; padding-left: 20px;">
          <li>Klik ikon <strong>gembok</strong> atau <strong>info (i)</strong> di address bar browser</li>
          <li>Cari pengaturan <strong>"Lokasi"</strong> atau <strong>"Location"</strong></li>
          <li>Pilih <strong>"Izinkan"</strong> atau <strong>"Allow"</strong></li>
          <li>Refresh halaman ini</li>
        </ol>
      </div>
      
      <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 12px;">
        <h6 style="color: #17a2b8; margin-bottom: 15px;">
          <i class="fas fa-camera"></i> Izin Kamera
        </h6>
        <ol style="font-size: 14px; line-height: 1.8; padding-left: 20px;">
          <li>Klik ikon <strong>gembok</strong> atau <strong>info (i)</strong> di address bar browser</li>
          <li>Cari pengaturan <strong>"Kamera"</strong> atau <strong>"Camera"</strong></li>
          <li>Pilih <strong>"Izinkan"</strong> atau <strong>"Allow"</strong></li>
          <li>Coba lagi untuk mengambil foto</li>
        </ol>
      </div>

      <div style="margin-top: 20px; padding: 15px; background: rgba(220, 53, 69, 0.2); border-radius: 8px; border-left: 4px solid #dc3545;">
        <small style="font-size: 13px;">
          <i class="fas fa-exclamation-triangle"></i> 
          <strong>Penting:</strong> Izin lokasi dan kamera diperlukan untuk melakukan absensi.
        </small>
      </div>
    </div>
  </div>
  <div class="camera-footer">
    <button type="button" class="btn btn-mobile btn-mobile-primary" id="retryPermission" style="flex: 1; max-width: 300px;">
      <i class="fas fa-redo"></i> Coba Lagi
    </button>
  </div>
</div>

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
  var locationPermissionGranted = false;

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

  // Request location permission and get location
  function requestLocationPermission() {
    if (!navigator.geolocation) {
      $('#locationInfo').removeClass('alert-info').addClass('alert-danger');
      $('#locationInfo').html('<i class="fas fa-exclamation-triangle"></i> Browser Anda tidak mendukung geolokasi');
      updatePermissionStatus('location', 'denied', 'Browser tidak support');
      return;
    }

    updatePermissionStatus('location', 'pending', 'Meminta izin lokasi...');

    // Request location with high accuracy
    navigator.geolocation.getCurrentPosition(
      function(position) {
        // Success callback
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        $('#latitude').val(lat);
        $('#longitude').val(lng);
        locationPermissionGranted = true;

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

        updatePermissionStatus('location', 'granted', 'Lokasi diizinkan ✓');
      },
      function(error) {
        // Error callback
        locationPermissionGranted = false;
        $('#locationInfo').removeClass('alert-info').addClass('alert-danger');
        
        var errorMessage = '';
        var statusText = '';
        switch(error.code) {
          case error.PERMISSION_DENIED:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> <strong>Izin lokasi ditolak!</strong><br>' +
                          '<small>Silakan izinkan akses lokasi di pengaturan browser Anda:<br>' +
                          '1. Klik ikon gembok/info di address bar<br>' +
                          '2. Pilih "Izinkan" untuk Lokasi<br>' +
                          '3. Refresh halaman ini</small>';
            statusText = 'Izin ditolak';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> Lokasi tidak tersedia. Pastikan GPS aktif.';
            statusText = 'GPS tidak aktif';
            break;
          case error.TIMEOUT:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> Waktu habis. Coba lagi.';
            statusText = 'Timeout';
            break;
          default:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> Gagal mendapatkan lokasi';
            statusText = 'Gagal';
        }
        
        $('#locationInfo').html(errorMessage);
        updatePermissionStatus('location', 'denied', statusText);
        
        // Show alert to user
        showAlert('Akses lokasi diperlukan untuk absensi. Silakan izinkan akses lokasi.', 'danger');
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  }

  // Update permission status indicator
  function updatePermissionStatus(type, status, text) {
    var elementId = type === 'location' ? '#locationPermissionStatus' : '#cameraPermissionStatus';
    var icon = type === 'location' ? 'fa-map-marker-alt' : 'fa-camera';
    
    $(elementId).removeClass('granted denied pending');
    $(elementId).addClass(status);
    $(elementId).find('i').removeClass('pulse');
    
    if (status === 'pending') {
      $(elementId).find('i').addClass('pulse');
    }
    
    $(elementId).find('span').text(text);
    $(elementId).show();
  }

  // Call location permission on page load
  requestLocationPermission();

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

  function showAlert(message, type = 'info') {
    const alertHtml = `
      <div class="alert alert-${type} alert-dismissible show fade alert-mobile" style="margin: 15px;">
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

  // Show permission help modal
  function showPermissionHelp() {
    $('#permissionModal').addClass('show');
  }

  // Close permission modal
  $('#closePermissionModal').on('click', function() {
    $('#permissionModal').removeClass('show');
  });

  // Retry permission
  $('#retryPermission').on('click', function() {
    $('#permissionModal').removeClass('show');
    requestLocationPermission();
  });

  // Event Handlers - Wrap in document ready
  $(document).ready(function() {
    $('#checkInBtn').on('click', function() {
      // Check location permission first
      if (!locationPermissionGranted || !$('#latitude').val()) {
        showAlert('Akses lokasi diperlukan. Silakan izinkan akses lokasi terlebih dahulu.', 'warning');
        showPermissionHelp();
        return;
      }
      currentAction = 'check-in';
      openCamera();
    });

  $('#breakStartBtn').on('click', function() {
    if (confirm('Yakin ingin mulai istirahat sekarang?')) {
      $.ajax({
        url: '{{ route("mobile.attendance.break-start") }}',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
          showAlert(response.message, 'success');
          setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
          var message = xhr.responseJSON && xhr.responseJSON.message 
            ? xhr.responseJSON.message 
            : 'Terjadi kesalahan';
          showAlert(message, 'danger');
        }
      });
    }
  });

  $('#breakEndBtn').on('click', function() {
    if (confirm('Istirahat selesai dan kembali bekerja?')) {
      $.ajax({
        url: '{{ route("mobile.attendance.break-end") }}',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
          showAlert(response.message, 'success');
          setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
          var message = xhr.responseJSON && xhr.responseJSON.message 
            ? xhr.responseJSON.message 
            : 'Terjadi kesalahan';
          showAlert(message, 'danger');
        }
      });
    }
  });

  $('#checkOutBtn').on('click', function() {
    // Check location permission first
    if (!locationPermissionGranted || !$('#latitude').val()) {
      showAlert('Akses lokasi diperlukan. Silakan izinkan akses lokasi terlebih dahulu.', 'warning');
      showPermissionHelp();
      return;
    }
    currentAction = 'check-out';
    openCamera();
  });

  function openCamera() {
    // Check if camera is supported
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showAlert('Browser Anda tidak mendukung akses kamera', 'danger');
      updatePermissionStatus('camera', 'denied', 'Browser tidak support');
      return;
    }

    $('#cameraModal').addClass('show');
    updatePermissionStatus('camera', 'pending', 'Meminta izin kamera...');
    
    // Request camera permission
    navigator.mediaDevices.getUserMedia({ 
      video: { 
        facingMode: 'user',
        width: { ideal: 1280 },
        height: { ideal: 720 }
      } 
    })
      .then(function(s) {
        stream = s;
        document.getElementById('video').srcObject = stream;
        $('#video').show();
        $('#canvas').hide();
        $('#captureBtn').show();
        $('#submitBtn').hide();
        updatePermissionStatus('camera', 'granted', 'Kamera diizinkan ✓');
      })
      .catch(function(err) {
        $('#cameraModal').removeClass('show');
        
        var errorMessage = '';
        var statusText = '';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
          errorMessage = 'Izin kamera ditolak! Silakan izinkan akses kamera.';
          statusText = 'Izin ditolak';
          showPermissionHelp();
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
          errorMessage = 'Kamera tidak ditemukan pada perangkat Anda';
          statusText = 'Tidak ditemukan';
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
          errorMessage = 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi tersebut dan coba lagi.';
          statusText = 'Sedang digunakan';
        } else {
          errorMessage = 'Gagal mengakses kamera: ' + err.message;
          statusText = 'Gagal';
        }
        
        updatePermissionStatus('camera', 'denied', statusText);
        showAlert(errorMessage, 'danger');
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
      showAlert('Silakan ambil foto terlebih dahulu', 'warning');
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
        showAlert(response.message, 'success');
        setTimeout(() => location.reload(), 1500);
      },
      error: function(xhr) {
        hideLoading();
        var message = xhr.responseJSON && xhr.responseJSON.message 
          ? xhr.responseJSON.message 
          : 'Terjadi kesalahan';
        showAlert(message, 'danger');
      }
    });
  }); // End document.ready
</script>
@endpush
