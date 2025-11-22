<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $branch = Auth::user()->branch;

        // Get positions for filter
        $positions = Position::where('is_active', true)->get();

        // Query employees
        $query = Employee::with(['user', 'position'])
            ->where('branch_id', $branchId);

        // Apply filters
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $employees = $query->paginate(20);

        // Calculate statistics
        $today = Carbon::today();
        $statistics = [
            'total' => Employee::where('branch_id', $branchId)->count(),
            'active' => Employee::where('branch_id', $branchId)->where('is_active', true)->count(),
            'inactive' => Employee::where('branch_id', $branchId)->where('is_active', false)->count(),
            'present_today' => Attendance::whereHas('employee', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })
                ->whereDate('date', $today)
                ->whereNotNull('check_in')
                ->count(),
        ];

        return view('admin.employees.index', compact('employees', 'positions', 'branch', 'statistics'));
    }

    public function show(Employee $employee)
    {
        // Verify employee belongs to admin's branch
        if ($employee->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke karyawan ini.');
        }

        $employee->load(['user', 'branch', 'position']);

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Verify employee belongs to admin's branch
        if ($employee->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke karyawan ini.');
        }

        $positions = Position::where('is_active', true)->get();
        $employee->load(['user', 'branch', 'position']);

        return view('admin.employees.edit', compact('employee', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Verify employee belongs to admin's branch
        if ($employee->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke karyawan ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'password' => 'nullable|string|min:8',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position_id' => 'required|exists:positions,id',
            'face_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // Update User
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $employee->user->update($userData);

        // Update Employee
        $employeeData = [
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'position_id' => $request->position_id,
            'is_active' => $request->has('is_active'),
        ];

        // Handle photo upload
        if ($request->hasFile('face_photo')) {
            // Delete old photo
            if ($employee->face_photo) {
                Storage::disk('public')->delete($employee->face_photo);
            }

            $path = $request->file('face_photo')->store('employees/photos', 'public');
            $employeeData['face_photo'] = $path;
        }

        $employee->update($employeeData);

        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'Data karyawan berhasil diupdate!');
    }
}
