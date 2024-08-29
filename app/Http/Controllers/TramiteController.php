<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use Illuminate\Http\Request;

class TramiteController extends Controller
{
    public function index()
    {
        $tramites = Tramite::all();
        return view('tramites.index', compact('tramites'));
    }

    public function show($id)
    {
        $tramite = Tramite::findOrFail($id);
        return view('tramites.show', compact('tramite'));
    }

    // Método para mostrar el formulario de creación
    public function create()
    {
        return view('tramites.create');
    }

    // Método para guardar un nuevo trámite
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        // Crear un nuevo trámite con los datos validados
        Tramite::create($validatedData);

        // Redirigir al listado de trámites con un mensaje de éxito
        return redirect()->route('tramites.index')->with('success', 'Trámite creado exitosamente.');
    }
}
