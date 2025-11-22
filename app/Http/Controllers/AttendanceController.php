<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkSchedule;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    private function checkAttendanceEligibility($employee, $today)
    {
        $branch = $employee->branch;

        // Check if today is a holiday
        $holiday = \App\Models\Holiday::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->whereDate('date', $today)
            ->first();

        if ($holiday) {
            return ['eligible' => false, 'message' => 'Hari ini adalah hari libur: ' . $holiday->name];
        }

        // Check if employee has approved leave today
        $approvedLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if ($approvedLeave) {
            return ['eligible' => false, 'message' => 'Anda sedang dalam periode izin/cuti'];
        }

        // Get work schedule to check working days
        $workSchedule = WorkSchedule::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->where(function($q) use ($employee) {
                $q->where('position_id', $employee->position_id)
                  ->orWhereNull('position_id');
            })
            ->orderByRaw('position_id IS NULL ASC')
            ->first();

        // Check if today is a working day
        if ($workSchedule && $workSchedule->working_days && is_array($workSchedule->working_days) && count($workSchedule->working_days) > 0) {
            $dayOfWeek = strtolower($today->format('l'));
            if (!in_array($dayOfWeek, $workSchedule->working_days)) {
                return ['eligible' => false, 'message' => 'Hari ini bukan hari kerja Anda'];
            }
        }

        return ['eligible' => true, 'workSchedule' => $workSchedule ?? null];
    }

    public function showCheckIn()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan');
        }

        $today = Carbon::today();
        $branch = $employee->branch;
        
        // Check if today is a holiday
        $holiday = \App\Models\Holiday::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->whereDate('date', $today)
            ->first();
        
        // Check if employee has approved leave today
        $approvedLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();
        
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();
        
        // Get work schedule based on branch and position priority
        // Priority: position_id specific > branch-wide (position_id = null)
        $workSchedule = WorkSchedule::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->where(function($q) use ($employee) {
                $q->where('position_id', $employee->position_id)
                  ->orWhereNull('position_id');
            })
            ->orderByRaw('position_id IS NULL ASC') // position_id specific first
            ->first();

        // Check if today is a working day based on work schedule
        $isWorkingDay = true;
        if ($workSchedule && $workSchedule->working_days && is_array($workSchedule->working_days) && count($workSchedule->working_days) > 0) {
            $dayOfWeek = strtolower($today->format('l')); // monday, tuesday, etc.
            $isWorkingDay = in_array($dayOfWeek, $workSchedule->working_days);
        }

        return view('attendances.check-in', compact('employee', 'branch', 'attendance', 'workSchedule', 'holiday', 'approvedLeave', 'isWorkingDay'));
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'required|image|max:2048',
        ]);

        $employee = Auth::user()->employee;
        $branch = $employee->branch;
        $today = Carbon::today();

        // Check eligibility
        $eligibility = $this->checkAttendanceEligibility($employee, $today);
        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message'],
            ], 422);
        }

        $workSchedule = $eligibility['workSchedule'];

        // Check if already checked in today
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini',
            ], 422);
        }

        // Calculate distance from office
        $distance = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            $branch->latitude,
            $branch->longitude
        );

        // Check if within radius
        $isOutOfRange = $distance > $branch->radius;
        
        // Upload photo
        $photoPath = $request->file('photo')->store('attendances', 'public');

        // Get work schedule (priority: position specific > branch-wide)
        $workSchedule = WorkSchedule::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->where(function($q) use ($employee) {
                $q->where('position_id', $employee->position_id)
                  ->orWhereNull('position_id');
            })
            ->orderByRaw('position_id IS NULL ASC')
            ->first();

        // Determine status
        $checkInTime = now();
        $status = 'valid';
        $notes = [];
        
        if ($workSchedule) {
            $scheduledTime = Carbon::parse($workSchedule->check_in_time);
            $tolerance = $workSchedule->late_tolerance ?? 15;
            
            // Check if late
            if ($checkInTime->gt($scheduledTime->copy()->addMinutes($tolerance))) {
                $status = 'late';
                $minutesLate = $checkInTime->diffInMinutes($scheduledTime);
                $notes[] = "Terlambat {$minutesLate} menit";
            }
        }

        // Check location
        if ($isOutOfRange) {
            $status = 'problematic';
            $notes[] = "Check-in di luar radius kantor (" . round($distance) . "m)";
        }

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'check_in' => $checkInTime,
                'check_in_photo' => $photoPath,
                'check_in_lat' => $validated['latitude'],
                'check_in_lng' => $validated['longitude'],
                'status' => $status,
                'notes' => !empty($notes) ? implode('. ', $notes) : null,
            ]
        );

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'check-in',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Check-in absensi - Status: ' . $status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Status: ' . ucfirst($status),
            'attendance' => $attendance,
            'distance' => round($distance),
            'status' => $status,
        ]);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'required|image|max:2048',
        ]);

        $employee = Auth::user()->employee;
        $today = Carbon::today();

        // Check eligibility
        $eligibility = $this->checkAttendanceEligibility($employee, $today);
        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message'],
            ], 422);
        }

        $branch = $employee->branch;
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in',
            ], 422);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini',
            ], 422);
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            $branch->latitude,
            $branch->longitude
        );

        $isOutOfRange = $distance > $branch->radius;
        
        // Upload photo
        $photoPath = $request->file('photo')->store('attendances', 'public');

        // Update attendance
        $attendance->update([
            'check_out' => now(),
            'check_out_photo' => $photoPath,
            'check_out_lat' => $validated['latitude'],
            'check_out_lng' => $validated['longitude'],
            'notes' => $attendance->notes . ($isOutOfRange ? ' | Check-out di luar radius (' . round($distance) . 'm)' : ''),
        ]);

        if ($isOutOfRange && $attendance->status !== 'problematic') {
            $attendance->update(['status' => 'problematic']);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'check-out',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Check-out absensi',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'attendance' => $attendance,
            'distance' => round($distance),
        ]);
    }

    public function breakStart(Request $request)
    {
        $employee = Auth::user()->employee;
        $today = Carbon::today();

        // Check eligibility
        $eligibility = $this->checkAttendanceEligibility($employee, $today);
        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message'],
            ], 422);
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in',
            ], 422);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check-out, tidak bisa istirahat',
            ], 422);
        }

        if ($attendance->break_start) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memulai istirahat',
            ], 422);
        }

        $attendance->update([
            'break_start' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'break-start',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Mulai istirahat',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Istirahat dimulai!',
            'break_start' => $attendance->break_start->format('H:i:s'),
        ]);
    }

    public function breakEnd(Request $request)
    {
        $employee = Auth::user()->employee;
        $today = Carbon::today();

        // Check eligibility
        $eligibility = $this->checkAttendanceEligibility($employee, $today);
        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message'],
            ], 422);
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in',
            ], 422);
        }

        if (!$attendance->break_start) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memulai istirahat',
            ], 422);
        }

        if ($attendance->break_end) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengakhiri istirahat',
            ], 422);
        }

        $attendance->update([
            'break_end' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'break-end',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Selesai istirahat',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $breakDuration = $attendance->break_start->diffInMinutes($attendance->break_end);

        return response()->json([
            'success' => true,
            'message' => 'Istirahat selesai! Durasi: ' . $breakDuration . ' menit',
            'break_end' => $attendance->break_end->format('H:i:s'),
            'duration' => $breakDuration,
        ]);
    }

    public function history()
    {
        $employee = Auth::user()->employee;
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('attendances.history', compact('attendances'));
    }

    public function monitor(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdminCabang()) {
            abort(403);
        }

        $branchId = $user->branch_id;
        
        // Get date from request or use today
        $selectedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $today = Carbon::today();

        // Get all active employees
        $employees = Employee::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['user', 'position'])
            ->get();

        // If AJAX request for calendar data (check for start/end params from FullCalendar)
        if ($request->has('start') && $request->has('end')) {
            try {
                $start = Carbon::parse($request->input('start'));
                $end = Carbon::parse($request->input('end'));
                
                $events = [];
            
            // Get all attendances in date range
            $attendances = Attendance::whereHas('employee', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->whereBetween('date', [$start, $end])
                ->with('employee.user')
                ->get();
            
            foreach ($attendances as $attendance) {
                $color = '#28a745'; // green for completed
                $statusText = 'Selesai';
                
                if (!$attendance->check_out) {
                    $color = '#007bff'; // blue for present
                    $statusText = 'Hadir';
                }
                if ($attendance->status === 'late') {
                    $color = '#ffc107'; // yellow for late
                    $statusText = 'Terlambat';
                }
                if ($attendance->status === 'problematic') {
                    $color = '#dc3545'; // red for problematic
                    $statusText = 'Bermasalah';
                }
                
                $events[] = [
                    'title' => $attendance->employee->user->name . ' - ' . $statusText,
                    'start' => $attendance->date->format('Y-m-d'),
                    'color' => $color,
                    'extendedProps' => [
                        'employee' => $attendance->employee->user->name,
                        'check_in' => $attendance->check_in ? $attendance->check_in->format('H:i') : '-',
                        'check_out' => $attendance->check_out ? $attendance->check_out->format('H:i') : '-',
                        'status' => $attendance->status,
                        'attendance_id' => $attendance->id,
                    ]
                ];
            }
            
            // Get holidays
            $holidays = \App\Models\Holiday::where('branch_id', $branchId)
                ->where('is_active', true)
                ->whereBetween('date', [$start, $end])
                ->get();
            
            foreach ($holidays as $holiday) {
                $events[] = [
                    'title' => '🏖️ ' . $holiday->name,
                    'start' => $holiday->date->format('Y-m-d'),
                    'color' => '#6c757d',
                    'allDay' => true,
                ];
            }
            
                \Log::info('Calendar events loaded', ['count' => count($events), 'start' => $start, 'end' => $end]);
                
                return response()->json($events);
            } catch (\Exception $e) {
                \Log::error('Calendar error: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        // For normal page load, get today's data
        $employees = $employees->map(function($employee) use ($selectedDate) {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $selectedDate)
                ->first();
            
            $employee->attendance = $attendance;
            
            // Check if employee has approved leave
            $hasLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $selectedDate)
                ->whereDate('end_date', '>=', $selectedDate)
                ->exists();

            // Check if today is a working day
            $workSchedule = WorkSchedule::where('branch_id', $employee->branch_id)
                ->where('is_active', true)
                ->where(function($q) use ($employee) {
                    $q->where('position_id', $employee->position_id)
                      ->orWhereNull('position_id');
                })
                ->orderByRaw('position_id IS NULL ASC')
                ->first();
            
            $isWorkingDay = true;
            if ($workSchedule && $workSchedule->working_days && is_array($workSchedule->working_days) && count($workSchedule->working_days) > 0) {
                $dayOfWeek = strtolower($selectedDate->format('l'));
                $isWorkingDay = in_array($dayOfWeek, $workSchedule->working_days);
            }

            // Check holiday
            $holiday = \App\Models\Holiday::where('branch_id', $employee->branch_id)
                ->where('is_active', true)
                ->whereDate('date', $selectedDate)
                ->first();

            // Determine status
            if ($holiday) {
                $employee->attendance_status = 'holiday';
                $employee->status_label = 'Libur';
                $employee->status_class = 'secondary';
            } elseif ($hasLeave) {
                $employee->attendance_status = 'leave';
                $employee->status_label = 'Izin/Cuti';
                $employee->status_class = 'info';
            } elseif (!$isWorkingDay) {
                $employee->attendance_status = 'off';
                $employee->status_label = 'Hari Libur';
                $employee->status_class = 'secondary';
            } elseif ($attendance) {
                if ($attendance->check_out) {
                    $employee->attendance_status = 'completed';
                    $employee->status_label = 'Selesai';
                    $employee->status_class = 'success';
                } else {
                    $employee->attendance_status = 'present';
                    $employee->status_label = 'Hadir';
                    $employee->status_class = 'primary';
                }
            } else {
                $employee->attendance_status = 'absent';
                $employee->status_label = 'Alfa';
                $employee->status_class = 'danger';
            }

            return $employee;
        });

        return view('attendances.monitor', compact('employees', 'selectedDate', 'today'));
    }

    public function validateAttendance()
    {
        $user = Auth::user();
        
        if (!$user->isAdminCabang()) {
            abort(403);
        }

        $branchId = $user->branch_id;

        $attendances = Attendance::whereHas('employee', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('status', 'problematic')
            ->where('is_verified', false)
            ->with(['employee.user', 'employee.position'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('attendances.validate', compact('attendances'));
    }

    public function approve($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $user = Auth::user();
        if ($user->isAdminCabang() && $attendance->employee->branch_id !== $user->branch_id) {
            abort(403);
        }

        $attendance->update([
            'is_verified' => true,
            'verified_by' => Auth::id(),
            'status' => 'valid',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve-attendance',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Menyetujui absensi: ' . $attendance->employee->full_name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Absensi berhasil disetujui');
    }

    public function reject($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $user = Auth::user();
        if ($user->isAdminCabang() && $attendance->employee->branch_id !== $user->branch_id) {
            abort(403);
        }

        $attendance->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'reject-attendance',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Menolak absensi: ' . $attendance->employee->full_name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Absensi berhasil ditolak');
    }

    public function updateValidation(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $user = Auth::user();
        if ($user->isAdminCabang() && $attendance->employee->branch_id !== $user->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:valid,late,problematic',
            'notes' => 'nullable|string',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'is_verified' => true,
            'verified_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'validate-attendance',
            'model' => 'Attendance',
            'model_id' => $attendance->id,
            'description' => 'Memvalidasi absensi: ' . $attendance->employee->full_name . ' - Status: ' . $validated['status'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.attendances.validate')->with('success', 'Validasi berhasil disimpan');
    }
}
