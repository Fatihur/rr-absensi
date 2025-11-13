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

<div class="section-body">
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
              <button type="button" class="btn btn-danger btn-lg btn-block" id="checkOutBtn">
                <i class="fas fa-sign-out-alt"></i> Check-Out
              </button>
            @else
              <div class="alert alert-info">
                <strong>Check-out:</strong> {{ $attendance->check_out->format('H:i:s') }}<br>
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
          <div class="mb-3">
            <strong>Informasi:</strong><br>
            <small class="text-muted">
              <i class="fas fa-building"></i> {{ $branch->name }}<br>
              <i class="fas fa-map-marker-alt"></i> {{ $branch->address }}<br>
              <i class="fas fa-circle"></i> Radius: {{ $branch->radius }}m
            </small>
          </div>

          @if($workSchedule)
            <div>
              <strong>Jadwal Kerja:</strong><br>
              <small class="text-muted">
                <i class="fas fa-clock"></i> Masuk: {{ Carbon\Carbon::parse($workSchedule->check_in_time)->format('H:i') }}<br>
                <i class="fas fa-clock"></i> Pulang: {{ Carbon\Carbon::parse($workSchedule->check_out_time)->format('H:i') }}<br>
                <i class="fas fa-hourglass-half"></i> Toleransi keterlambatan: {{ $workSchedule->late_tolerance }} menit
              </small>
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
          <h5 class="modal-title">Ambil Foto</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="camera-container text-center">
            <video id="video" autoplay></video>
            <canvas id="canvas"></canvas>
          </div>
          <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i> Pastikan wajah Anda terlihat jelas
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="captureBtn">
            <i class="fas fa-camera"></i> Ambil Foto
          </button>
          <button type="button" class="btn btn-success" id="submitBtn" style="display:none;">
            <i class="fas fa-check"></i> Submit Absensi
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  // Fix: Remove any orphaned modal backdrop on page load
  $(document).ready(function() {
    // Remove any backdrop that doesn't have an open modal
    if ($('.modal.show').length === 0) {
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css('padding-right', '');
    }
  });

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

  // Get current location
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var lat = position.coords.latitude;
      var lng = position.coords.longitude;

      $('#latitude').val(lat);
      $('#longitude').val(lng);

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
    }, function(error) {
      $('#locationInfo').removeClass('alert-info').addClass('alert-danger');
      $('#locationInfo').html('<i class="fas fa-exclamation-triangle"></i> Gagal mendapatkan lokasi: ' + error.message);
    });
  }

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

  // Check-in button
  $('#checkInBtn').on('click', function() {
    if (!$('#latitude').val()) {
      alert('Tunggu hingga lokasi Anda terdeteksi');
      return;
    }
    currentAction = 'check-in';
    $('#cameraModal').modal({backdrop: false, show: true});
    startCamera();
  });

  // Check-out button
  $('#checkOutBtn').on('click', function() {
    if (!$('#latitude').val()) {
      alert('Tunggu hingga lokasi Anda terdeteksi');
      return;
    }
    currentAction = 'check-out';
    $('#cameraModal').modal({backdrop: false, show: true});
    startCamera();
  });

  // Camera functions
  var video = document.getElementById('video');
  var canvas = document.getElementById('canvas');
  var stream;

  function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(function(s) {
        stream = s;
        video.srcObject = stream;
        video.style.display = 'block';
        canvas.style.display = 'none';
        $('#captureBtn').show();
        $('#submitBtn').hide();
      })
      .catch(function(err) {
        alert('Error accessing camera: ' + err.message);
      });
  }

  function stopCamera() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
  }

  $('#captureBtn').on('click', function() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    video.style.display = 'none';
    canvas.style.display = 'block';
    $('#captureBtn').hide();
    $('#submitBtn').show();

    canvas.toBlob(function(blob) {
      capturedBlob = blob;
    }, 'image/jpeg', 0.8);

    stopCamera();
  });

  $('#submitBtn').on('click', function() {
    if (!capturedBlob) {
      alert('Silakan ambil foto terlebih dahulu');
      return;
    }

    var formData = new FormData();
    formData.append('photo', capturedBlob, 'attendance.jpg');
    formData.append('latitude', $('#latitude').val());
    formData.append('longitude', $('#longitude').val());
    formData.append('_token', '{{ csrf_token() }}');

    var url = currentAction === 'check-in' 
      ? '{{ route("karyawan.attendance.check-in.post") }}'
      : '{{ route("karyawan.attendance.check-out") }}';

    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        alert(response.message);
        location.reload();
      },
      error: function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message 
          ? xhr.responseJSON.message 
          : 'Terjadi kesalahan';
        alert(message);
      }
    });
  });

  $('#cameraModal').on('hidden.bs.modal', function() {
    stopCamera();
    video.style.display = 'block';
    canvas.style.display = 'none';
    $('#captureBtn').show();
    $('#submitBtn').hide();
    capturedBlob = null;
  });

  // Cleanup camera on page unload
  $(window).on('beforeunload', function() {
    stopCamera();
  });
</script>
@endpush
