<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('created_at', 'desc')->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch = Branch::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Branch',
            'model_id' => $branch->id,
            'description' => 'Membuat cabang baru: ' . $branch->name,
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super.branches.index')
            ->with('success', 'Cabang berhasil ditambahkan');
    }

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:1000',
            'is_active' => 'boolean',
        ]);

        $oldValues = $branch->toArray();
        $validated['is_active'] = $request->has('is_active');

        $branch->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'Branch',
            'model_id' => $branch->id,
            'description' => 'Mengupdate cabang: ' . $branch->name,
            'old_values' => $oldValues,
            'new_values' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super.branches.index')
            ->with('success', 'Cabang berhasil diupdate');
    }

    public function destroy(Branch $branch)
    {
        $oldValues = $branch->toArray();
        $name = $branch->name;
        
        $branch->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'Branch',
            'model_id' => $branch->id,
            'description' => 'Menghapus cabang: ' . $name,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('super.branches.index')
            ->with('success', 'Cabang berhasil dihapus');
    }
}
