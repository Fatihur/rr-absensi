<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan');
        }

        $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('leave-requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(403, 'Data karyawan tidak ditemukan');
        }

        return view('leave-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:leave,sick,permit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $employee = Auth::user()->employee;

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment' => $attachment,
            'status' => 'pending',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'LeaveRequest',
            'model_id' => $leaveRequest->id,
            'description' => 'Mengajukan ' . $validated['type'] . ': ' . $validated['start_date'] . ' - ' . $validated['end_date'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('karyawan.leave-requests.index')
            ->with('success', 'Pengajuan berhasil disubmit. Menunggu approval.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->employee->user_id !== Auth::id()) {
            abort(403);
        }

        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function indexForAdmin()
    {
        $user = Auth::user();
        
        if (!$user->isAdminCabang()) {
            abort(403);
        }

        $leaveRequests = LeaveRequest::whereHas('employee', function($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->with(['employee.user', 'employee.position', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('leave-requests.admin', compact('leaveRequests'));
    }

    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $user = Auth::user();
        if ($user->isAdminCabang() && $leaveRequest->employee->branch_id !== $user->branch_id) {
            abort(403);
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->createAttendanceRecords($leaveRequest);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve-leave',
            'model' => 'LeaveRequest',
            'model_id' => $leaveRequest->id,
            'description' => 'Menyetujui pengajuan ' . $leaveRequest->type . ' dari ' . $leaveRequest->employee->full_name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        $user = Auth::user();
        if ($user->isAdminCabang() && $leaveRequest->employee->branch_id !== $user->branch_id) {
            abort(403);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $request->input('notes'),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'reject-leave',
            'model' => 'LeaveRequest',
            'model_id' => $leaveRequest->id,
            'description' => 'Menolak pengajuan ' . $leaveRequest->type . ' dari ' . $leaveRequest->employee->full_name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil ditolak');
    }

    private function createAttendanceRecords(LeaveRequest $leaveRequest)
    {
        $startDate = Carbon::parse($leaveRequest->start_date);
        $endDate = Carbon::parse($leaveRequest->end_date);

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->isWeekday()) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $leaveRequest->employee_id,
                        'date' => $currentDate->toDateString(),
                    ],
                    [
                        'status' => $leaveRequest->type,
                        'notes' => 'Auto-created from approved leave request: ' . $leaveRequest->type,
                        'is_verified' => true,
                        'verified_by' => Auth::id(),
                    ]
                );
            }
            $currentDate->addDay();
        }
    }
}
