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

    public function showCheckIn()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan');
        }

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $branch = $employee->branch;
        $workSchedule = WorkSchedule::where('branch_id', $branch->id)
            ->where(function($q) use ($employee) {
                $q->whereNull('position_id')
                  ->orWhere('position_id', $employee->position_id);
            })
            ->where('is_active', true)
            ->first();

        return view('attendances.check-in', compact('employee', 'branch', 'attendance', 'workSchedule'));
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

        // Get work schedule
        $workSchedule = WorkSchedule::where('branch_id', $branch->id)
            ->where(function($q) use ($employee) {
                $q->whereNull('position_id')
                  ->orWhere('position_id', $employee->position_id);
            })
            ->where('is_active', true)
            ->first();

        // Determine status
        $checkInTime = now();
        $status = 'valid';
        
        if ($workSchedule) {
            $scheduledTime = Carbon::parse($workSchedule->check_in_time);
            $tolerance = $workSchedule->late_tolerance ?? 15;
            
            if ($checkInTime->gt($scheduledTime->addMinutes($tolerance))) {
                $status = 'late';
            }
        }

        if ($isOutOfRange) {
            $status = 'problematic';
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
                'notes' => $isOutOfRange ? 'Check-in di luar radius kantor (' . round($distance) . 'm)' : null,
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
        $branch = $employee->branch;
        $today = Carbon::today();

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

    public function history()
    {
        $employee = Auth::user()->employee;
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('attendances.history', compact('attendances'));
    }

    public function monitor()
    {
        $user = Auth::user();
        
        if (!$user->isAdminCabang()) {
            abort(403);
        }

        $today = Carbon::today();
        $branchId = $user->branch_id;

        $employees = Employee::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['user', 'position', 'attendances' => function($q) use ($today) {
                $q->whereDate('date', $today);
            }])
            ->get();

        return view('attendances.monitor', compact('employees'));
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
}
