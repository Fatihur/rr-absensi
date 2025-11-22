<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Branch;
use App\Models\Position;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'branch', 'position'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        return view('employees.create', compact('branches', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nik' => 'required|string|unique:employees',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
            'position_id' => 'required|exists:positions,id',
            'join_date' => 'required|date',
            'face_photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 3,
            'branch_id' => $validated['branch_id'],
            'is_active' => true,
        ]);

        $facePhoto = null;
        if ($request->hasFile('face_photo')) {
            $facePhoto = $request->file('face_photo')->store('faces', 'public');
        }

        $employee = Employee::create([
            'user_id' => $user->id,
            'nik' => $validated['nik'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'branch_id' => $validated['branch_id'],
            'position_id' => $validated['position_id'],
            'join_date' => $validated['join_date'],
            'face_photo' => $facePhoto,
            'is_active' => true,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Employee',
            'model_id' => $employee->id,
            'description' => 'Membuat karyawan baru: ' . $employee->full_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'branch', 'position', 'attendances' => function($q) {
            $q->orderBy('date', 'desc')->limit(30);
        }]);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        return view('employees.edit', compact('employee', 'branches', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'password' => 'nullable|min:8',
            'nik' => 'required|string|unique:employees,nik,' . $employee->id,
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
            'position_id' => 'required|exists:positions,id',
            'join_date' => 'required|date',
            'face_photo' => 'nullable|image|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'branch_id' => $validated['branch_id'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $employee->user->update($userData);

        $facePhoto = $employee->face_photo;
        if ($request->hasFile('face_photo')) {
            if ($facePhoto) {
                Storage::disk('public')->delete($facePhoto);
            }
            $facePhoto = $request->file('face_photo')->store('faces', 'public');
        }

        $employee->update([
            'nik' => $validated['nik'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'branch_id' => $validated['branch_id'],
            'position_id' => $validated['position_id'],
            'join_date' => $validated['join_date'],
            'face_photo' => $facePhoto,
            'is_active' => $request->has('is_active'),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Employee',
            'model_id' => $employee->id,
            'description' => 'Mengupdate karyawan: ' . $employee->full_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super.employees.index')
            ->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->full_name;
        
        if ($employee->face_photo) {
            Storage::disk('public')->delete($employee->face_photo);
        }

        $employee->user->delete();
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Employee',
            'model_id' => $employee->id,
            'description' => 'Menghapus karyawan: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('super.employees.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }
}
