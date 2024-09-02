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

    public function store(Request $request, $tramite_id)
    {
        $request->validate([
            'detalles' => 'required|string',
        ]);

        $solicitud = new Solicitud();
        $solicitud->user_id =  auth()->id(); //auth()->id(); // Asegúrate de que el usuario esté autenticado
        $solicitud->tramite_id = $tramite_id;
        $solicitud->detalles = $request->input('detalles');
        $solicitud->estado = 'pendiente'; // Ajusta el estado según sea necesario
        $solicitud->save();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud enviada con éxito.');
    }

    public function show($id)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Crear una instancia del Request con la URL
        $request = new Request(['url' => url('solicitudes/' . $solicitud->id)]);
        $base64 = QrCodeController::generateQrCodeBase64($request);
        return view('solicitudes.show', compact('solicitud','base64'));
    }
}

