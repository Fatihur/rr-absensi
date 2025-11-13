<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard($today);
        } elseif ($user->isAdminCabang()) {
            return $this->adminCabangDashboard($user, $today);
        } else {
            return $this->karyawanDashboard($user, $today);
        }
    }

    private function superAdminDashboard($today)
    {
        $data = [
            'total_branches' => Branch::where('is_active', true)->count(),
            'total_employees' => Employee::where('is_active', true)->count(),
            'total_users' => User::where('is_active', true)->count(),
            'today_attendance' => Attendance::whereDate('date', $today)->count(),
            'today_late' => Attendance::whereDate('date', $today)
                ->where('status', 'late')
                ->count(),
            'today_leave' => Attendance::whereDate('date', $today)
                ->whereIn('status', ['leave', 'sick', 'permit'])
                ->count(),
            'recent_attendances' => Attendance::with(['employee.user', 'employee.branch'])
                ->whereDate('date', $today)
                ->orderBy('check_in', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('dashboard.super_admin', $data);
    }

    private function adminCabangDashboard($user, $today)
    {
        $branchId = $user->branch_id;

        $data = [
            'branch' => Branch::find($branchId),
            'total_employees' => Employee::where('branch_id', $branchId)
                ->where('is_active', true)
                ->count(),
            'today_present' => Attendance::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
                ->whereDate('date', $today)
                ->whereNotNull('check_in')
                ->count(),
            'today_late' => Attendance::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
                ->whereDate('date', $today)
                ->where('status', 'late')
                ->count(),
            'today_absent' => Employee::where('branch_id', $branchId)
                ->where('is_active', true)
                ->whereDoesntHave('attendances', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                })
                ->count(),
            'problematic_attendance' => Attendance::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
                ->where('status', 'problematic')
                ->where('is_verified', false)
                ->count(),
            'recent_attendances' => Attendance::with(['employee.user', 'employee.position'])
                ->whereHas('employee', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->whereDate('date', $today)
                ->orderBy('check_in', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('dashboard.admin_cabang', $data);
    }

    private function karyawanDashboard($user, $today)
    {
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

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

        $data = [
            'employee' => $employee,
            'branch' => $employee->branch,
            'today_attendance' => $todayAttendance,
            'monthly_stats' => $monthlyStats,
            'recent_attendances' => Attendance::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get(),
        ];

        return view('dashboard.karyawan', $data);
    }
}
