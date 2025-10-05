<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_system_settings');
    }

    public function index(Request $request)
    {
        $query = Department::with(['responsibleUser', 'users', 'tramites']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $departments = $query->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $users = User::role(['admin', 'commission', 'functionary'])->get();
        return view('admin.departments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create([
            'name' => $request->name,
            'description' => $request->description,
            'responsible_user_id' => $request->responsible_user_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departamento creado exitosamente.');
    }

    public function show(Department $department)
    {
        $department->load(['responsibleUser', 'users', 'tramites.solicitudes']);
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $users = User::role(['admin', 'commission', 'functionary'])->get();
        return view('admin.departments.edit', compact('department', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        $department->update([
            'name' => $request->name,
            'description' => $request->description,
            'responsible_user_id' => $request->responsible_user_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departamento actualizado exitosamente.');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0 || $department->tramites()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'No se puede eliminar el departamento porque tiene usuarios o trámites asociados.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departamento eliminado exitosamente.');
    }
}