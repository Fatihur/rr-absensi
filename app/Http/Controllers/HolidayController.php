<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = Holiday::with('branch');
        
        if ($user->isAdminCabang()) {
            $query->where('branch_id', $user->branch_id);
        }
        
        $holidays = $query->orderBy('date', 'desc')->get();
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->isAdminCabang()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::where('is_active', true)->get();
        }
        
        return view('holidays.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $holiday = Holiday::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Holiday',
            'model_id' => $holiday->id,
            'description' => 'Membuat hari libur: ' . $holiday->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Hari libur berhasil ditambahkan');
    }

    public function edit(Holiday $holiday)
    {
        $user = Auth::user();
        
        if ($user->isAdminCabang() && $holiday->branch_id !== $user->branch_id) {
            abort(403);
        }
        
        if ($user->isAdminCabang()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::where('is_active', true)->get();
        }
        
        return view('holidays.edit', compact('holiday', 'branches'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $holiday->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Holiday',
            'model_id' => $holiday->id,
            'description' => 'Mengupdate hari libur: ' . $holiday->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Hari libur berhasil diupdate');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Holiday',
            'model_id' => $holiday->id,
            'description' => 'Menghapus hari libur: ' . $holiday->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Hari libur berhasil dihapus');
    }
}
