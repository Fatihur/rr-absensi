@extends('layouts.app')

@section('title', 'Absensi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map { height: 300px; margin-bottom: 20px; }
  #video, #canvas { width: 100%; max-width: 640px; }
  #canvas { display: none; }
  .camera-container { position: relative; }
  .capture-btn { margin-top: 10px; }
  
  /* Remove modal backdrop completely */
  .modal-backdrop {
    display: none !important;
  }
  
  .modal {
    background-color: rgba(0, 0, 0, 0.5);
  }
</style>
@endpush

@section('content')
<div class="section-header">
  <h1>Absensi Hari Ini</h1>
</div>

<div id="alertContainer"></div>

<div class="section-body">
  @if($holiday)
    <div class="alert alert-info alert-has-icon">
      <div class="alert-icon"><i class="fas fa-calendar-times"></i></div>
      <div class="alert-body">
        <div class="alert-title">Hari Libur</div>
        <strong>{{ $holiday->name }}</strong><br>
        {{ $holiday->description }}<br>
        <small class="text-muted">Anda tidak perlu melakukan absensi hari ini.</small>
      </div>
    </div>
  @elseif($approvedLeave)
    <div class="alert alert-warning alert-has-icon">
      <div class="alert-icon"><i class="fas fa-umbrella-beach"></i></div>
      <div class="alert-body">
        <div class="alert-title">Sedang Izin/Cuti</div>
        <strong>{{ ucfirst($approvedLeave->type) }}</strong><br>
        Periode: {{ $approvedLeave->start_date->format('d M Y') }} - {{ $approvedLeave->end_date->format('d M Y') }}<br>
        Alasan: {{ $approvedLeave->reason }}<br>
        <small class="text-muted">Anda tidak perlu melakukan absensi selama periode izin.</small>
      </div>
    </div>
  @elseif(!$isWorkingDay)
    <div class="alert alert-secondary alert-has-icon">
      <div class="alert-icon"><i class="fas fa-calendar-day"></i></div>
      <div class="alert-body">
        <div class="alert-title">Bukan Hari Kerja</div>
        Hari ini ({{ \Carbon\Carbon::today()->isoFormat('dddd, D MMMM Y') }}) bukan termasuk hari kerja Anda.<br>
        @if($workSchedule && $workSchedule->working_days && count($workSchedule->working_days) > 0)
          @php
            $dayNames = [
              'monday' => 'Senin',
              'tuesday' => 'Selasa',
              'wednesday' => 'Rabu',
              'thursday' => 'Kamis',
              'friday' => 'Jumat',
              'saturday' => 'Sabtu',
              'sunday' => 'Minggu'
            ];
            $workingDaysIndo = array_map(fn($day) => $dayNames[$day] ?? $day, $workSchedule->working_days);
          @endphp
          <strong>Hari kerja Anda:</strong> {{ implode(', ', $workingDaysIndo) }}<br>
        @endif
        <small class="text-muted">Anda tidak perlu melakukan absensi hari ini.</small>
      </div>
    </div>
  @else
  <div class="row">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h4>Status Absensi</h4>
        </div>
        <div class="card-body">
          @if($attendance && $attendance->check_in)
            <div class="alert alert-success">
              <strong>Check-in:</strong> {{ $attendance->check_in->format('H:i:s') }}<br>
              <strong>Status:</strong> 
              @if($attendance->status === 'valid')
                <span class="badge badge-success">Valid</span>
              @elseif($attendance->status === 'late')
                <span class="badge badge-warning">Terlambat</span>
              @else
                <span class="badge badge-danger">Bermasalah</span>
              @endif
            </div>

            @if(!$attendance->check_out)
              <!-- Break Time Buttons -->
              @if($workSchedule && $workSchedule->break_start && $workSchedule->break_end)
                <div class="row mb-3">
                  <div class="col-6">
                    @if(!$attendance->break_start)
                      <button type="button" class="btn btn-warning btn-lg btn-block" id="breakStartBtn">
                        <i class="fas fa-coffee"></i> Mulai Istirahat
                      </button>
                    @elseif(!$attendance->break_end)
                      <div class="alert alert-info mb-0">
                        <small><strong>Istirahat:</strong><br>{{ $attendance->break_start->format('H:i:s') }}</small>
                      </div>
                    @else
                      <div class="alert alert-success mb-0">
                        <small>
                          <strong>Istirahat:</strong><br>
                          {{ $attendance->break_start->format('H:i') }} - {{ $attendance->break_end->format('H:i') }}
                        </small>
                      </div>
                    @endif
                  </div>
                  <div class="col-6">
                    @if($attendance->break_start && !$attendance->break_end)
                      <button type="button" class="btn btn-success btn-lg btn-block" id="breakEndBtn">
                        <i class="fas fa-play"></i> Kembali Bekerja
                      </button>
                    @endif
                  </div>
                </div>
              @endif

              <!-- Check-out Button -->
              <button type="button" class="btn btn-danger btn-lg btn-block" id="checkOutBtn">
                <i class="fas fa-sign-out-alt"></i> Check-Out
              </button>
            @else
              <div class="alert alert-info">
                <strong>Check-out:</strong> {{ $attendance->check_out->format('H:i:s') }}<br>
                @if($attendance->break_start && $attendance->break_end)
                  <strong>Istirahat:</strong> {{ $attendance->break_start->format('H:i') }} - {{ $attendance->break_end->format('H:i') }}<br>
                @endif
                Anda sudah menyelesaikan absensi hari ini.
              </div>
            @endif
          @else
            <div class="alert alert-warning">
              Anda belum melakukan check-in hari ini.
            </div>
            <button type="button" class="btn btn-primary btn-lg btn-block" id="checkInBtn">
              <i class="fas fa-sign-in-alt"></i> Check-In
            </button>
          @endif

          <hr>
          

          @if($workSchedule)
            <div>
              <strong>Jadwal Kerja:</strong>
              @if($workSchedule->position_id)
                <span class="badge badge-info">{{ $workSchedule->position->name }}</span>
              @else
                <span class="badge badge-secondary">Umum</span>
              @endif
              <br>
              <small class="text-muted">
                <i class="fas fa-sign-in-alt"></i> Masuk: <strong>{{ Carbon\Carbon::parse($workSchedule->check_in_time)->format('H:i') }}</strong><br>
                <i class="fas fa-sign-out-alt"></i> Pulang: <strong>{{ Carbon\Carbon::parse($workSchedule->check_out_time)->format('H:i') }}</strong><br>
                @if($workSchedule->break_start && $workSchedule->break_end)
                  <i class="fas fa-coffee"></i> Istirahat: <strong>{{ Carbon\Carbon::parse($workSchedule->break_start)->format('H:i') }} - {{ Carbon\Carbon::parse($workSchedule->break_end)->format('H:i') }}</strong><br>
                @endif
                <i class="fas fa-hourglass-half"></i> Toleransi: <strong>{{ $workSchedule->late_tolerance }} menit</strong>
              </small>
            </div>
          @else
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle"></i> Jadwal kerja belum diatur untuk posisi Anda
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header">
          <h4>Lokasi Anda</h4>
        </div>
        <div class="card-body">
          <div id="map"></div>
          <div class="alert alert-info" id="locationInfo">
            <i class="fas fa-spinner fa-spin"></i> Mendapatkan lokasi Anda...
          </div>
          <input type="hidden" id="latitude">
          <input type="hidden" id="longitude">
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Camera -->
  <div class="modal fade" id="cameraModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ambil Foto Absensi</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="camera-container text-center" style="position: relative;">
            <video id="video" autoplay style="width: 100%; max-width: 640px; transform: scaleX(-1); border-radius: 8px;"></video>
            <canvas id="canvas" style="display: none; width: 100%; max-width: 640px; transform: scaleX(-1); border-radius: 8px;"></canvas>
            <canvas id="overlay" style="position: absolute; top: 0; left: 50%; transform: translateX(-50%) scaleX(-1); pointer-events: none;"></canvas>
          </div>
          
          <!-- Face Detection Status -->
          <div class="mt-3">
            <div class="alert alert-info" id="detectionStatus">
              <i class="fas fa-spinner fa-spin"></i> Memuat model deteksi wajah...
            </div>
            
            <!-- Detection Indicators -->
            <div class="row text-center mt-2">
              <div class="col-6">
                <div class="detection-indicator" id="faceIndicator">
                  <i class="fas fa-user fa-2x text-muted"></i>
                  <p class="mb-0 mt-2">Wajah</p>
                  <small class="text-muted">Belum terdeteksi</small>
                </div>
              </div>
              <div class="col-6">
                <div class="detection-indicator" id="smileIndicator">
                  <i class="fas fa-smile fa-2x text-muted"></i>
                  <p class="mb-0 mt-2">Senyum</p>
                  <small class="text-muted">Belum terdeteksi</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="captureBtn" disabled>
            <i class="fas fa-camera"></i> Ambil Foto
          </button>
          <button type="button" class="btn btn-success" id="submitBtn" style="display:none;">
            <i class="fas fa-check"></i> Submit Absensi
          </button>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
  var map, marker;
  var branchLat = {{ $branch->latitude ?? -6.2088 }};
  var branchLng = {{ $branch->longitude ?? 106.8456 }};
  var branchRadius = {{ $branch->radius }};
  var currentAction = ''; // 'check-in' or 'check-out'
  var capturedBlob = null;

  // Initialize map
  map = L.map('map').setView([branchLat, branchLng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  // Add branch marker
  var branchMarker = L.marker([branchLat, branchLng], {
    icon: L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    })
  }).addTo(map);
  branchMarker.bindPopup('<strong>{{ $branch->name }}</strong>');

  // Add radius circle
  L.circle([branchLat, branchLng], {
    color: 'blue',
    fillColor: '#30f',
    fillOpacity: 0.2,
    radius: branchRadius
  }).addTo(map);

  // Request location permission and get location
  var locationPermissionGranted = false;
  
  function requestLocationPermission() {
    if (!navigator.geolocation) {
      $('#locationInfo').removeClass('alert-info').addClass('alert-danger');
      $('#locationInfo').html('<i class="fas fa-exclamation-triangle"></i> Browser Anda tidak mendukung geolokasi');
      return;
    }

    navigator.geolocation.getCurrentPosition(
      function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        $('#latitude').val(lat);
        $('#longitude').val(lng);
        locationPermissionGranted = true;

        // Add user marker
        marker = L.marker([lat, lng], {
          icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
          })
        }).addTo(map);
        marker.bindPopup('<strong>Lokasi Anda</strong>');

        map.setView([lat, lng], 16);

        // Calculate distance
        var distance = calculateDistance(lat, lng, branchLat, branchLng);
        
        if (distance <= branchRadius) {
          $('#locationInfo').removeClass('alert-info alert-danger').addClass('alert-success');
          $('#locationInfo').html('<i class="fas fa-check-circle"></i> Anda berada dalam radius kantor (' + Math.round(distance) + 'm)');
        } else {
          $('#locationInfo').removeClass('alert-info alert-success').addClass('alert-danger');
          $('#locationInfo').html('<i class="fas fa-exclamation-triangle"></i> Anda berada di luar radius kantor (' + Math.round(distance) + 'm). Absensi akan ditandai bermasalah.');
        }
      },
      function(error) {
        locationPermissionGranted = false;
        $('#locationInfo').removeClass('alert-info').addClass('alert-danger');
        
        var errorMessage = '';
        switch(error.code) {
          case error.PERMISSION_DENIED:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> <strong>Izin lokasi ditolak!</strong><br>' +
                          '<small>Silakan izinkan akses lokasi di pengaturan browser Anda dan refresh halaman.</small>';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> Lokasi tidak tersedia. Pastikan GPS aktif.';
            break;
          case error.TIMEOUT:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> Waktu habis. Coba lagi.';
            break;
          default:
            errorMessage = '<i class="fas fa-exclamation-triangle"></i> Gagal mendapatkan lokasi: ' + error.message;
        }
        
        $('#locationInfo').html(errorMessage);
        showAlert('Akses lokasi diperlukan untuk absensi. Silakan izinkan akses lokasi.', 'danger');
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  }

  // Call location permission on page load
  requestLocationPermission();

  function calculateDistance(lat1, lon1, lat2, lon2) {
    var R = 6371000; // meters
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
      <div class="alert alert-${type} alert-dismissible show fade" style="margin: 0 15px 15px 15px;">
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

  // Event Handlers - All wrapped in document ready
  $(document).ready(function() {
    // Fix: Remove any orphaned modal backdrop on page load
    if ($('.modal.show').length === 0) {
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css('padding-right', '');
    }

    // Check-in button
    $('#checkInBtn').on('click', function() {
      if (!$('#latitude').val()) {
        showAlert('Tunggu hingga lokasi Anda terdeteksi', 'warning');
        return;
      }
      currentAction = 'check-in';
      $('#cameraModal').modal({backdrop: false, show: true});
      startCamera();
    });

  // Break start button
  $('#breakStartBtn').on('click', function() {
    if (confirm('Yakin ingin mulai istirahat sekarang?')) {
      $.ajax({
        url: '{{ route("karyawan.attendance.break-start") }}',
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

  // Break end button
  $('#breakEndBtn').on('click', function() {
    if (confirm('Istirahat selesai dan kembali bekerja?')) {
      $.ajax({
        url: '{{ route("karyawan.attendance.break-end") }}',
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

  // Check-out button
  $('#checkOutBtn').on('click', function() {
    if (!$('#latitude').val()) {
      showAlert('Tunggu hingga lokasi Anda terdeteksi', 'warning');
      return;
    }
    currentAction = 'check-out';
    $('#cameraModal').modal({backdrop: false, show: true});
    startCamera();
  });

  // Camera & Face Detection
  var video = document.getElementById('video');
  var canvas = document.getElementById('canvas');
  var overlay = document.getElementById('overlay');
  var stream;
  var modelsLoaded = false;
  var detectionInterval;
  var faceDetected = false;
  var smileDetected = false;

  // Load face-api.js models
  async function loadModels() {
    try {
      $('#detectionStatus').html('<i class="fas fa-spinner fa-spin"></i> Memuat model deteksi wajah...');
      
      const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/';
      
      await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
      ]);
      
      modelsLoaded = true;
      $('#detectionStatus').html('<i class="fas fa-check-circle text-success"></i> Model siap! Arahkan wajah Anda ke kamera dan tersenyum.');
      console.log('Face detection models loaded successfully');
    } catch (error) {
      console.error('Error loading models:', error);
      $('#detectionStatus').html('<i class="fas fa-exclamation-triangle text-warning"></i> Deteksi wajah tidak tersedia, Anda masih bisa mengambil foto.');
      $('#captureBtn').prop('disabled', false); // Allow capture without detection
    }
  }

  // Detect face and smile
  async function detectFaceAndSmile() {
    if (!modelsLoaded || video.paused || video.ended) return;

    try {
      const detections = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceExpressions();

      // Clear overlay
      const displaySize = { width: video.videoWidth, height: video.videoHeight };
      overlay.width = displaySize.width;
      overlay.height = displaySize.height;
      const ctx = overlay.getContext('2d');
      ctx.clearRect(0, 0, overlay.width, overlay.height);

      if (detections) {
        // Face detected
        faceDetected = true;
        
        // Draw detection box
        const resizedDetections = faceapi.resizeResults(detections, displaySize);
        ctx.strokeStyle = '#28a745';
        ctx.lineWidth = 3;
        ctx.strokeRect(
          resizedDetections.detection.box.x,
          resizedDetections.detection.box.y,
          resizedDetections.detection.box.width,
          resizedDetections.detection.box.height
        );

        // Check smile (happy expression)
        const expressions = detections.expressions;
        const happyScore = expressions.happy;
        smileDetected = happyScore > 0.6; // 60% confidence for smile

        // Update UI indicators
        updateIndicators(true, smileDetected, happyScore);

        // Enable capture button if both conditions met
        if (faceDetected && smileDetected) {
          $('#captureBtn').prop('disabled', false);
        } else {
          $('#captureBtn').prop('disabled', true);
        }
      } else {
        // No face detected
        faceDetected = false;
        smileDetected = false;
        updateIndicators(false, false, 0);
        $('#captureBtn').prop('disabled', true);
      }
    } catch (error) {
      console.error('Detection error:', error);
    }
  }

  function updateIndicators(faceFound, smileFound, happyScore) {
    // Face indicator
    if (faceFound) {
      $('#faceIndicator i').removeClass('text-muted').addClass('text-success');
      $('#faceIndicator small').text('Terdeteksi ✓').removeClass('text-muted').addClass('text-success');
    } else {
      $('#faceIndicator i').removeClass('text-success').addClass('text-muted');
      $('#faceIndicator small').text('Belum terdeteksi').removeClass('text-success').addClass('text-muted');
    }

    // Smile indicator
    if (smileFound) {
      $('#smileIndicator i').removeClass('text-muted').addClass('text-success');
      $('#smileIndicator small').text('Tersenyum ✓ (' + Math.round(happyScore * 100) + '%)').removeClass('text-muted').addClass('text-success');
    } else if (faceFound) {
      $('#smileIndicator i').removeClass('text-success').addClass('text-warning');
      $('#smileIndicator small').text('Silakan senyum (' + Math.round(happyScore * 100) + '%)').removeClass('text-muted text-success').addClass('text-warning');
    } else {
      $('#smileIndicator i').removeClass('text-success text-warning').addClass('text-muted');
      $('#smileIndicator small').text('Belum terdeteksi').removeClass('text-success text-warning').addClass('text-muted');
    }
  }

  function startCamera() {
    // Check if camera is supported
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showAlert('Browser Anda tidak mendukung akses kamera', 'danger');
      return;
    }

    navigator.mediaDevices.getUserMedia({ 
      video: { 
        width: { ideal: 1280 },
        height: { ideal: 720 },
        facingMode: 'user'
      } 
    })
      .then(function(s) {
        stream = s;
        video.srcObject = stream;
        video.style.display = 'block';
        canvas.style.display = 'none';
        $('#captureBtn').show().prop('disabled', true);
        $('#submitBtn').hide();

        // Wait for video to be ready then start detection
        video.addEventListener('loadeddata', () => {
          if (!modelsLoaded) {
            loadModels().then(() => {
              // Start detection loop
              detectionInterval = setInterval(detectFaceAndSmile, 100); // Check every 100ms
            });
          } else {
            detectionInterval = setInterval(detectFaceAndSmile, 100);
          }
        });
      })
      .catch(function(err) {
        var errorMessage = '';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
          errorMessage = '<strong>Izin kamera ditolak!</strong><br>' +
                        'Silakan izinkan akses kamera di pengaturan browser:<br>' +
                        '1. Klik ikon gembok/info di address bar<br>' +
                        '2. Pilih "Izinkan" untuk Kamera<br>' +
                        '3. Refresh halaman dan coba lagi';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
          errorMessage = 'Kamera tidak ditemukan pada perangkat Anda';
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
          errorMessage = 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi tersebut dan coba lagi.';
        } else {
          errorMessage = 'Gagal mengakses kamera: ' + err.message;
        }
        
        showAlert(errorMessage, 'danger');
      });
  }

  function stopCamera() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    if (detectionInterval) {
      clearInterval(detectionInterval);
    }
    // Reset indicators
    faceDetected = false;
    smileDetected = false;
    updateIndicators(false, false, 0);
  }

  $('#captureBtn').on('click', function() {
    // Stop detection
    if (detectionInterval) {
      clearInterval(detectionInterval);
    }
    
    // Capture image (flip back to normal)
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    
    // Flip image back to normal (mirror for display was scaleX(-1))
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0);
    ctx.setTransform(1, 0, 0, 1, 0, 0); // Reset transform
    
    video.style.display = 'none';
    overlay.style.display = 'none';
    canvas.style.display = 'block';
    $('#captureBtn').hide();
    $('#submitBtn').show();
    $('#detectionStatus').hide();
    $('.detection-indicator').hide();

    canvas.toBlob(function(blob) {
      capturedBlob = blob;
    }, 'image/jpeg', 0.8);

    stopCamera();
  });

  $('#submitBtn').on('click', function() {
    if (!capturedBlob) {
      showAlert('Silakan ambil foto terlebih dahulu', 'warning');
      return;
    }

    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');

    var formData = new FormData();
    formData.append('photo', capturedBlob, 'attendance.jpg');
    formData.append('latitude', $('#latitude').val());
    formData.append('longitude', $('#longitude').val());
    formData.append('_token', '{{ csrf_token() }}');

    var url = currentAction === 'check-in' 
      ? '{{ route("karyawan.attendance.check-in.post") }}'
      : '{{ route("karyawan.attendance.check-out.post") }}';

    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#cameraModal').modal('hide');
        showAlert(response.message, 'success');
        setTimeout(() => location.reload(), 1500);
      },
      error: function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message 
          ? xhr.responseJSON.message 
          : 'Terjadi kesalahan';
        showAlert(message, 'danger');
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-check"></i> Submit Absensi');
      }
    });
  });

    $('#cameraModal').on('hidden.bs.modal', function() {
      stopCamera();
      video.style.display = 'block';
      canvas.style.display = 'none';
      overlay.style.display = 'block';
      $('#captureBtn').show().prop('disabled', true);
      $('#submitBtn').hide();
      $('#detectionStatus').show();
      $('.detection-indicator').show();
      updateIndicators(false, false, 0);
      capturedBlob = null;
    });

    // Cleanup camera on page unload
    $(window).on('beforeunload', function() {
      stopCamera();
    });
  }); // End document.ready
</script>
@endpush
