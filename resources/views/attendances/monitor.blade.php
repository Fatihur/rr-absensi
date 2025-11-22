@extends('layouts.app')

@section('title', 'Monitoring Kehadiran')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
  #calendar {
    max-width: 100%;
    margin: 0 auto;
  }
  .fc-event {
    cursor: pointer;
  }
  .fc-daygrid-day-number {
    font-size: 1.1em;
    font-weight: bold;
  }
</style>
@endpush

@section('content')
<div class="section-header">
  <h1>Monitoring Kehadiran</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
    <div class="breadcrumb-item">Monitoring Kehadiran</div>
  </div>
</div>

<div class="section-body">
  <!-- Calendar View -->
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h4>Kalender Kehadiran</h4>
          <div class="card-header-action">
            <div class="btn-group">
              <button class="btn btn-primary" id="todayBtn">
                <i class="fas fa-calendar-day"></i> Hari Ini
              </button>
              <button class="btn btn-info" id="listViewBtn">
                <i class="fas fa-list"></i> Tampilan List
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div id="calendar"></div>
        </div>
      </div>
    </div>

    <!-- Today's Detail -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h4 id="detailDate">Detail Hari Ini</h4>
        </div>
        <div class="card-body">
          <!-- Summary -->
          <div class="mb-3">
            <div class="row">
              <div class="col-6 mb-2">
                <div class="text-center p-2 bg-success text-white rounded">
                  <h6 class="mb-0">Hadir</h6>
                  <h4 class="mb-0" id="presentCount">{{ $employees->whereIn('attendance_status', ['present', 'completed'])->count() }}</h4>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="text-center p-2 bg-danger text-white rounded">
                  <h6 class="mb-0">Alfa</h6>
                  <h4 class="mb-0" id="absentCount">{{ $employees->where('attendance_status', 'absent')->count() }}</h4>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="text-center p-2 bg-info text-white rounded">
                  <h6 class="mb-0">Izin</h6>
                  <h4 class="mb-0" id="leaveCount">{{ $employees->where('attendance_status', 'leave')->count() }}</h4>
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="text-center p-2 bg-secondary text-white rounded">
                  <h6 class="mb-0">Libur</h6>
                  <h4 class="mb-0" id="holidayCount">{{ $employees->whereIn('attendance_status', ['holiday', 'off'])->count() }}</h4>
                </div>
              </div>
            </div>
          </div>

          <!-- Employee List -->
          <div id="employeeList">
            <h6 class="mb-3">Daftar Karyawan:</h6>
            <div class="list-group" style="max-height: 500px; overflow-y: auto;">
              @foreach($employees as $employee)
                <div class="list-group-item list-group-item-action p-2">
                  <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                      <strong class="mb-0" style="font-size: 0.9em;">{{ $employee->user->name }}</strong>
                      <br>
                      <small class="text-muted">{{ $employee->position->name ?? '-' }}</small>
                      @if($employee->attendance)
                        <br>
                        <small>
                          <i class="fas fa-sign-in-alt text-success"></i> {{ $employee->attendance->check_in ? $employee->attendance->check_in->format('H:i') : '-' }}
                          @if($employee->attendance->check_out)
                            | <i class="fas fa-sign-out-alt text-danger"></i> {{ $employee->attendance->check_out->format('H:i') }}
                          @endif
                        </small>
                      @endif
                    </div>
                    <span class="badge badge-{{ $employee->status_class }}">
                      {{ $employee->status_label }}
                    </span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- List View (Hidden by default) -->
  <div id="listView" style="display: none;">
    <!-- Summary Cards -->
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-users"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Karyawan</h4>
            </div>
            <div class="card-body">
              {{ $employees->count() }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Hadir</h4>
            </div>
            <div class="card-body">
              {{ $employees->whereIn('attendance_status', ['present', 'completed'])->count() }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Alfa</h4>
            </div>
            <div class="card-body">
              {{ $employees->where('attendance_status', 'absent')->count() }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-umbrella-beach"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Izin/Cuti</h4>
            </div>
            <div class="card-body">
              {{ $employees->where('attendance_status', 'leave')->count() }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>Monitoring Kehadiran - {{ $selectedDate->isoFormat('dddd, D MMMM Y') }}</h4>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-md">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Karyawan</th>
                    <th>Posisi</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($employees as $index => $employee)
                    @php
                      $attendance = $employee->attendance;
                    @endphp
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>
                        <strong>{{ $employee->user->name }}</strong><br>
                        <small class="text-muted">{{ $employee->nik }}</small>
                      </td>
                      <td>{{ $employee->position->name ?? '-' }}</td>
                      <td>
                        @if($attendance && $attendance->check_in)
                          <i class="fas fa-sign-in-alt text-success"></i> 
                          {{ $attendance->check_in->format('H:i') }}
                          @if($attendance->status === 'late')
                            <br><small class="text-warning"><i class="fas fa-clock"></i> Terlambat</small>
                          @endif
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @if($attendance && $attendance->check_out)
                          <i class="fas fa-sign-out-alt text-danger"></i> 
                          {{ $attendance->check_out->format('H:i') }}
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge badge-{{ $employee->status_class }}">
                          @if($employee->attendance_status === 'absent')
                            <i class="fas fa-times-circle"></i>
                          @elseif($employee->attendance_status === 'present')
                            <i class="fas fa-user-check"></i>
                          @elseif($employee->attendance_status === 'completed')
                            <i class="fas fa-check-circle"></i>
                          @elseif($employee->attendance_status === 'leave')
                            <i class="fas fa-umbrella-beach"></i>
                          @else
                            <i class="fas fa-calendar"></i>
                          @endif
                          {{ $employee->status_label }}
                        </span>
                      </td>
                      <td>
                        @if($attendance)
                          <a href="{{ route('admin.attendances.validate') }}?id={{ $attendance->id }}" class="btn btn-sm btn-primary" title="Detail">
                            <i class="fas fa-eye"></i>
                          </a>
                          @if($attendance->check_in_photo)
                            <a href="{{ asset('storage/' . $attendance->check_in_photo) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat Foto">
                              <i class="fas fa-image"></i>
                            </a>
                          @endif
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada data karyawan</p>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
  $(document).ready(function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,listWeek'
      },
      locale: 'id',
      buttonText: {
        today: 'Hari Ini',
        month: 'Bulan',
        week: 'Minggu',
        list: 'List'
      },
      events: function(info, successCallback, failureCallback) {
        $.ajax({
          url: '{{ route("admin.attendances.monitor") }}',
          type: 'GET',
          data: {
            start: info.startStr,
            end: info.endStr
          },
          success: function(data) {
            console.log('Calendar data loaded:', data);
            successCallback(data);
          },
          error: function(xhr, status, error) {
            console.error('Calendar error:', xhr.responseText);
            alert('Gagal memuat data kehadiran: ' + error);
            failureCallback(error);
          }
        });
      },
      dateClick: function(info) {
        loadDateDetail(info.dateStr);
      },
      eventClick: function(info) {
        if (info.event.extendedProps.attendance_id) {
          window.location.href = '{{ route("admin.attendances.validate") }}?id=' + info.event.extendedProps.attendance_id;
        }
      },
      eventDidMount: function(info) {
        if (info.event.extendedProps.employee) {
          info.el.title = info.event.extendedProps.employee + ' - ' + 
                          info.event.extendedProps.check_in + ' s/d ' + 
                          info.event.extendedProps.check_out;
        }
      }
    });
    
    calendar.render();

    // Toggle between calendar and list view
    $('#listViewBtn').on('click', function() {
      $('#calendar').parent().parent().hide();
      $('#listView').show();
      $(this).html('<i class="fas fa-calendar"></i> Tampilan Kalender');
      $(this).attr('id', 'calendarViewBtn');
    });

    $(document).on('click', '#calendarViewBtn', function() {
      $('#listView').hide();
      $('#calendar').parent().parent().show();
      $(this).html('<i class="fas fa-list"></i> Tampilan List');
      $(this).attr('id', 'listViewBtn');
    });

    // Today button
    $('#todayBtn').on('click', function() {
      calendar.today();
      loadDateDetail(new Date().toISOString().split('T')[0]);
    });

    // Function to load date detail
    function loadDateDetail(date) {
      $.ajax({
        url: '{{ route("admin.attendances.monitor") }}',
        type: 'GET',
        data: { date: date },
        success: function(response) {
          // Update detail panel
          $('#detailDate').text('Detail ' + formatDate(date));
          
          // This will reload the page with the selected date
          window.location.href = '{{ route("admin.attendances.monitor") }}?date=' + date;
        },
        error: function(xhr) {
          console.error('Error loading date detail:', xhr);
        }
      });
    }

    function formatDate(dateStr) {
      const date = new Date(dateStr);
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      return date.toLocaleDateString('id-ID', options);
    }
  });
</script>
@endpush
