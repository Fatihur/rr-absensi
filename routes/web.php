<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\AdminEmployeeController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super.')->group(function () {
        Route::resource('branches', App\Http\Controllers\BranchController::class);
        Route::resource('positions', App\Http\Controllers\PositionController::class);
        Route::resource('employees', App\Http\Controllers\EmployeeController::class);
        Route::resource('users', App\Http\Controllers\UserController::class);
        
        Route::get('reports/attendance', function() {
            return view('reports.attendance');
        })->name('reports.attendance');
    });

    Route::middleware(['role:admin_cabang'])->prefix('admin-cabang')->name('admin.')->group(function () {
        Route::resource('work-schedules', App\Http\Controllers\WorkScheduleController::class);
        Route::resource('holidays', App\Http\Controllers\HolidayController::class);
        
        Route::get('employees', [AdminEmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/{employee}', [AdminEmployeeController::class, 'show'])->name('employees.show');
        
        Route::get('attendances/monitor', [App\Http\Controllers\AttendanceController::class, 'monitor'])->name('attendances.monitor');
        Route::get('attendances/validate', [App\Http\Controllers\AttendanceController::class, 'validateAttendance'])->name('attendances.validate');
        Route::post('attendances/{attendance}/approve', [App\Http\Controllers\AttendanceController::class, 'approve'])->name('attendances.approve');
        Route::post('attendances/{attendance}/reject', [App\Http\Controllers\AttendanceController::class, 'reject'])->name('attendances.reject');
        Route::post('attendances/{attendance}/update-validation', [App\Http\Controllers\AttendanceController::class, 'updateValidation'])->name('attendances.update-validation');
        
        Route::get('leave-requests', [App\Http\Controllers\LeaveRequestController::class, 'indexForAdmin'])->name('leave-requests.index');
        Route::post('leave-requests/{leaveRequest}/approve', [App\Http\Controllers\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('leave-requests/{leaveRequest}/reject', [App\Http\Controllers\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
        
        Route::get('branch/location', [App\Http\Controllers\BranchController::class, 'editLocation'])->name('branch.location');
        Route::post('branch/location', [App\Http\Controllers\BranchController::class, 'updateLocation'])->name('branch.location.update');
        
        Route::get('reports/attendance', function() {
            return view('reports.attendance');
        })->name('reports.attendance');
    });

    Route::middleware(['role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('attendance/check-in', [App\Http\Controllers\AttendanceController::class, 'showCheckIn'])->name('attendance.check-in');
        Route::post('attendance/check-in', [App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in.post');
        Route::post('attendance/check-out', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.check-out.post');
        Route::post('attendance/break-start', [App\Http\Controllers\AttendanceController::class, 'breakStart'])->name('attendance.break-start');
        Route::post('attendance/break-end', [App\Http\Controllers\AttendanceController::class, 'breakEnd'])->name('attendance.break-end');
        Route::get('attendance/history', [App\Http\Controllers\AttendanceController::class, 'history'])->name('attendance.history');
        
        Route::resource('leave-requests', App\Http\Controllers\LeaveRequestController::class)->only(['index', 'create', 'store', 'show']);
    });

    // Mobile Routes for Karyawan
    Route::middleware(['role:karyawan'])->prefix('m')->name('mobile.')->group(function () {
        Route::get('/', [MobileController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance', [MobileController::class, 'attendance'])->name('attendance');
        Route::post('/attendance/check-in', [MobileController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('/attendance/break-start', [MobileController::class, 'breakStart'])->name('attendance.break-start');
        Route::post('/attendance/break-end', [MobileController::class, 'breakEnd'])->name('attendance.break-end');
        Route::post('/attendance/check-out', [MobileController::class, 'checkOut'])->name('attendance.check-out');
        Route::get('/history', [MobileController::class, 'history'])->name('history');
        Route::get('/leave', [MobileController::class, 'leave'])->name('leave');
        Route::get('/leave/create', [MobileController::class, 'leaveCreate'])->name('leave.create');
        Route::post('/leave', [MobileController::class, 'leaveStore'])->name('leave.store');
        Route::get('/profile', [MobileController::class, 'profile'])->name('profile');
        Route::post('/logout', [MobileController::class, 'logout'])->name('logout');
    });
});
