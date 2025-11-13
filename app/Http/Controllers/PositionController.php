<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('name')->get();
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $position = Position::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Position',
            'model_id' => $position->id,
            'description' => 'Membuat posisi baru: ' . $position->name,
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super.positions.index')
            ->with('success', 'Posisi berhasil ditambahkan');
    }

    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $oldValues = $position->toArray();
        $validated['is_active'] = $request->has('is_active');
        $position->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Position',
            'model_id' => $position->id,
            'description' => 'Mengupdate posisi: ' . $position->name,
            'old_values' => $oldValues,
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super.positions.index')
            ->with('success', 'Posisi berhasil diupdate');
    }

    public function destroy(Position $position)
    {
        $name = $position->name;
        $position->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Position',
            'model_id' => $position->id,
            'description' => 'Menghapus posisi: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('super.positions.index')
            ->with('success', 'Posisi berhasil dihapus');
    }
}
