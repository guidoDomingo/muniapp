<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::where('user_id', Auth::id())->get();
        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create($id)
    {
        $tramite = Tramite::findOrFail($id);
        return view('solicitudes.create', compact('tramite'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'detalles' => 'required|string',
        ]);

        Solicitud::create([
            'user_id' => Auth::id(),
            'tramite_id' => $id,
            'detalles' => $request->detalles,
        ]);

        return redirect()->route('solicitudes.index')->with('message', 'Solicitud enviada con éxito');
    }

    public function show($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        return view('solicitudes.show', compact('solicitud'));
    }
}

