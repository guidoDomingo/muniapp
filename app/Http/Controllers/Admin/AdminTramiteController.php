<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tramite;
use App\Models\Department;
use Illuminate\Http\Request;

class AdminTramiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_all_tramites')->only(['index', 'show']);
        $this->middleware('permission:create_tramites')->only(['create', 'store']);
        $this->middleware('permission:edit_tramites')->only(['edit', 'update']);
        $this->middleware('permission:delete_tramites')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Tramite::with(['department', 'solicitudes']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $tramites = $query->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('admin.tramites.index', compact('tramites', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.tramites.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'estimated_days' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'form_fields' => 'nullable|json',
            'include_map' => 'nullable|boolean',
            'workflow_steps' => 'nullable|json',
        ]);

        $tramite = Tramite::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'department_id' => $request->department_id,
            'estimated_days' => $request->estimated_days,
            'cost' => $request->cost ?? 0,
            'form_fields' => $request->form_fields ? json_decode($request->form_fields, true) : null,
            'include_map' => $request->has('include_map'),
            'required_documents' => [], // Ya no se usan documentos requeridos
            'workflow_steps' => $request->workflow_steps ? json_decode($request->workflow_steps, true) : null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.tramites.index')
            ->with('success', 'Trámite creado exitosamente.');
    }

    public function show(Tramite $tramite)
    {
        $tramite->load(['department', 'solicitudes.user']);
        return view('admin.tramites.show', compact('tramite'));
    }

    public function edit(Tramite $tramite)
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.tramites.edit', compact('tramite', 'departments'));
    }

    public function update(Request $request, Tramite $tramite)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'estimated_days' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'form_fields' => 'nullable|json',
            'required_documents' => 'nullable|array',
            'workflow_steps' => 'nullable|json',
        ]);

        $tramite->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'department_id' => $request->department_id,
            'estimated_days' => $request->estimated_days,
            'cost' => $request->cost ?? 0,
            'form_fields' => $request->form_fields ? json_decode($request->form_fields, true) : null,
            'required_documents' => $request->required_documents ?? [],
            'workflow_steps' => $request->workflow_steps ? json_decode($request->workflow_steps, true) : null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.tramites.index')
            ->with('success', 'Trámite actualizado exitosamente.');
    }

    public function destroy(Tramite $tramite)
    {
        if ($tramite->solicitudes()->count() > 0) {
            return redirect()->route('admin.tramites.index')
                ->with('error', 'No se puede eliminar el trámite porque tiene solicitudes asociadas.');
        }

        $tramite->delete();

        return redirect()->route('admin.tramites.index')
            ->with('success', 'Trámite eliminado exitosamente.');
    }
}