<?php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use App\Models\Branch;
use App\Models\Position;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = WorkSchedule::with(['branch', 'position']);
        
        if ($user->isAdminCabang()) {
            $query->where('branch_id', $user->branch_id);
        }
        
        $schedules = $query->orderBy('created_at', 'desc')->get();
        return view('work-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->isAdminCabang()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $positions = Position::where('is_active', true)->get();
        return view('work-schedules.create', compact('branches', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'position_id' => 'nullable|exists:positions,id',
            'name' => 'required|string|max:255',
            'check_in_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_tolerance' => 'required|integer|min:0|max:60',
        ]);

        $schedule = WorkSchedule::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'WorkSchedule',
            'model_id' => $schedule->id,
            'description' => 'Membuat jadwal kerja: ' . $schedule->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Jadwal kerja berhasil ditambahkan');
    }

    public function edit(WorkSchedule $workSchedule)
    {
        $user = Auth::user();
        
        if ($user->isAdminCabang() && $workSchedule->branch_id !== $user->branch_id) {
            abort(403);
        }
        
        if ($user->isAdminCabang()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::where('is_active', true)->get();
        }
        
        $positions = Position::where('is_active', true)->get();
        return view('work-schedules.edit', compact('workSchedule', 'branches', 'positions'));
    }

    public function update(Request $request, WorkSchedule $workSchedule)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'position_id' => 'nullable|exists:positions,id',
            'name' => 'required|string|max:255',
            'check_in_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_tolerance' => 'required|integer|min:0|max:60',
        ]);

        $workSchedule->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'WorkSchedule',
            'model_id' => $workSchedule->id,
            'description' => 'Mengupdate jadwal kerja: ' . $workSchedule->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Jadwal kerja berhasil diupdate');
    }

    public function destroy(WorkSchedule $workSchedule)
    {
        $workSchedule->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'WorkSchedule',
            'model_id' => $workSchedule->id,
            'description' => 'Menghapus jadwal kerja: ' . $workSchedule->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.work-schedules.index')
            ->with('success', 'Jadwal kerja berhasil dihapus');
    }
}
