<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\TramiteStatus;
use Illuminate\Http\Request;

class AdminSolicitudController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_all_solicitudes');
    }

    public function index(Request $request)
    {
        $query = Solicitud::with(['user', 'tramite', 'assignedUser']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('estado', $request->status);
        }

        if ($request->filled('tramite')) {
            $query->where('tramite_id', $request->tramite);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('tracking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $tramites = \App\Models\Tramite::all();
        $users = User::role(['commission', 'functionary'])->get();

        return view('admin.solicitudes.index', compact('solicitudes', 'tramites', 'users'));
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['user', 'tramite', 'assignedUser', 'history.user', 'chats.user']);
        return view('admin.solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $statuses = TramiteStatus::orderBy('order')->get();
        $users = User::role(['commission', 'functionary'])->get();
        
        return view('admin.solicitudes.edit', compact('solicitud', 'statuses', 'users'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado' => 'required|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'comments' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $solicitud->estado;
        
        $solicitud->update($request->only(['estado', 'priority', 'assigned_to']));

        // Registrar en historial
        if ($oldStatus !== $request->estado) {
            $solicitud->history()->create([
                'user_id' => auth()->id(),
                'previous_status_id' => null, // You'd need to implement status IDs
                'new_status_id' => null,
                'comments' => $request->comments ?? 'Estado actualizado por administrador',
            ]);
        }

        return redirect()->route('admin.solicitudes.index')
            ->with('success', 'Solicitud actualizada exitosamente.');
    }

    public function updateStatus(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'status' => 'required|string',
            'comments' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $solicitud->estado;
        $solicitud->update(['estado' => $request->status]);

        // Registrar en historial
        $solicitud->history()->create([
            'user_id' => auth()->id(),
            'previous_status_id' => null,
            'new_status_id' => null,
            'comments' => $request->comments ?? 'Estado actualizado',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'new_status' => $request->status
        ]);
    }

    public function assignUser(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'comments' => 'nullable|string|max:1000',
        ]);

        $solicitud->update(['assigned_to' => $request->user_id]);

        // Registrar en historial
        $solicitud->history()->create([
            'user_id' => auth()->id(),
            'comments' => $request->comments ?? 'Solicitud asignada a ' . User::find($request->user_id)->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud asignada exitosamente'
        ]);
    }

    public function destroy(Solicitud $solicitud)
    {
        $this->authorize('delete', $solicitud);
        
        $solicitud->delete();

        return redirect()->route('admin.solicitudes.index')
            ->with('success', 'Solicitud eliminada exitosamente.');
    }
}