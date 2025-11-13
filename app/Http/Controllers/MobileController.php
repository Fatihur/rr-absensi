<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MobileController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        $today = Carbon::today();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $thisMonth = Carbon::now();
        $monthlyStats = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', $thisMonth->year)
            ->whereMonth('date', $thisMonth->month)
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(CASE WHEN status = "valid" THEN 1 ELSE 0 END) as on_time,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status IN ("leave", "sick", "permit") THEN 1 ELSE 0 END) as `leave`
            ')
            ->first();

        return view('mobile.dashboard', [
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    public function attendance()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $branch = $employee->branch;

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $workSchedule = $employee->branch->workSchedules()
            ->where(function ($query) use ($employee) {
                $query->whereNull('position_id')
                    ->orWhere('position_id', $employee->position_id);
            })
            ->where('is_active', true)
            ->first();

        return view('mobile.attendance', [
            'employee' => $employee,
            'branch' => $branch,
            'attendance' => $attendance,
            'workSchedule' => $workSchedule,
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $employee = Auth::user()->employee;
        $branch = $employee->branch;
        $today = Carbon::today();

        // Check if already checked in
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return response()->json(['message' => 'Anda sudah melakukan check-in hari ini.'], 400);
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $branch->latitude,
            $branch->longitude
        );

        // Upload photo
        $photoPath = $request->file('photo')->store('attendance/check-in', 'public');

        // Get work schedule
        $workSchedule = $branch->workSchedules()
            ->where(function ($query) use ($employee) {
                $query->whereNull('position_id')
                    ->orWhere('position_id', $employee->position_id);
            })
            ->where('is_active', true)
            ->first();

        // Determine status
        $status = 'valid';
        if ($distance > $branch->radius) {
            $status = 'problematic';
        } elseif ($workSchedule) {
            $checkInTime = Carbon::now();
            $scheduledTime = Carbon::parse($workSchedule->check_in_time);
            $toleranceTime = $scheduledTime->addMinutes($workSchedule->late_tolerance);

            if ($checkInTime->greaterThan($toleranceTime)) {
                $status = 'late';
            }
        }

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'check_in' => Carbon::now(),
                'check_in_photo' => $photoPath,
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'status' => $status,
            ]
        );

        return response()->json([
            'message' => 'Check-in berhasil!',
            'attendance' => $attendance,
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $employee = Auth::user()->employee;
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json(['message' => 'Anda belum melakukan check-in.'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Anda sudah melakukan check-out hari ini.'], 400);
        }

        // Upload photo
        $photoPath = $request->file('photo')->store('attendance/check-out', 'public');

        $attendance->update([
            'check_out' => Carbon::now(),
            'check_out_photo' => $photoPath,
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Check-out berhasil!',
            'attendance' => $attendance,
        ]);
    }

    public function history()
    {
        $employee = Auth::user()->employee;
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(30);

        return view('mobile.history', [
            'attendances' => $attendances,
        ]);
    }

    public function leave()
    {
        $employee = Auth::user()->employee;
        
        $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('mobile.leave.index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    public function leaveCreate()
    {
        return view('mobile.leave.create');
    }

    public function leaveStore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave,sick,permit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $employee = Auth::user()->employee;

        $data = [
            'employee_id' => $employee->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave-requests', 'public');
        }

        LeaveRequest::create($data);

        return redirect()->route('mobile.leave')->with('success', 'Pengajuan berhasil dikirim!');
    }

    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee;

        return view('mobile.profile', [
            'user' => $user,
            'employee' => $employee,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
