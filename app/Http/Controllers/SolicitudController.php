<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::where('user_id', Auth::id())->get();

        // Verificar si el usuario es administrador
        if (Auth::user()->hasRole('admin')) {
            
            $solicitudes = Solicitud::all();
        }

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create($id)
    {
        $tramite = Tramite::findOrFail($id);
        return view('solicitudes.create', compact('tramite'));
    }

    public function store(Request $request, $tramiteId)
    {
        
        $campos = [];

        if ($request->hasFile('documento_identidad')) {
            $documento = $request->file('documento_identidad');
        
            if ($documento->isValid()) {
                $disk = Storage::disk('public');
                $filename = uniqid() . '_' . $documento->getClientOriginalName(); // Genera un nombre único
                $path = 'documentos/' . $filename;
        
                // Guardar el archivo en la ubicación especificada
                $disk->put($path, file_get_contents($documento));
        
                $campos[] = [
                    'nombre' => 'documento_identidad',
                    'tipo' => 'file',
                    'valor' => $path
                ];
            } else {
                return redirect()->back()->withErrors(['documento_identidad' => 'El archivo no es válido.']);
            }
        }
        
        

        // Verificar y manejar el archivo imagen_usuario
        if ($request->hasFile('imagen_usuario')) {
            $imagen = $request->file('imagen_usuario');
            
            if ($imagen->isValid()) {
                // Usar el disco público para guardar la imagen
                $disk = Storage::disk('public');
                $filename = uniqid() . '_' . $imagen->getClientOriginalName(); // Generar un nombre único para el archivo
                $path = 'imagenes/' . $filename; // Definir el path donde se guardará el archivo

                // Obtener la ruta completa del directorio de almacenamiento público
                $fullPath = storage_path('app/public/') . $path;

                // Mover el archivo manualmente a la ubicación especificada
                $imagen->move(dirname($fullPath), $filename);

                $campos[] = [
                    'nombre' => 'imagen_usuario',
                    'tipo' => 'image',
                    'valor' => $path
                ];
            } else {
                return redirect()->back()->withErrors(['imagen_usuario' => 'El archivo no es válido.']);
            }
        }
        

        // Verificar y manejar el campo de texto nombre_usuario
        if ($request->has('nombre_usuario')) {
            $campos[] = [
                'nombre' => 'nombre_usuario',
                'tipo' => 'text',
                'valor' => $request->input('nombre_usuario')
            ];
        }

        // Construcción del array final
        $formulario = [
            'campos' => $campos
        ];

        // Guardar en la base de datos
        $solicitud = new Solicitud();
        $solicitud->user_id = auth()->id();
        $solicitud->tramite_id = $tramiteId;
        $solicitud->formulario = json_encode($formulario);
        $solicitud->detalles = $request->input('detalles');
        $solicitud->save();

        return redirect()->route('solicitudes.index');
    }

    public function show($id)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Crear una instancia del Request con la URL
        $request = new Request(['url' => url('solicitudes/' . $solicitud->id)]);
        $base64 = QrCodeController::generateQrCodeBase64($request);
        return view('solicitudes.show', compact('solicitud','base64'));
    }

    private function determinarTipoCampo($nombre, $request)
    {
        // Aquí puedes agregar más lógica para determinar el tipo de campo
        if ($request->hasFile($nombre)) {
            return 'file';
        }

        return 'text';
    }

    public function updateEstado(Request $request, $id)
    {
        
        // Verificar si el usuario es administrador
        if (!Auth::user()->hasRole('admin')) {
            // Código para usuarios admin
            return redirect()->back()->withErrors(['mensaje' => 'No tienes permiso para realizar esta acción.']);
        }

        
        
        // Validar la solicitud
        $request->validate([
            'estado' => 'required|string|in:pendiente,aprobado,rechazado'
        ]);

        

        // Buscar la solicitud por ID
        $solicitud = Solicitud::findOrFail($id);

        // Actualizar el estado
        $solicitud->estado = $request->input('estado');
        $solicitud->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('solicitudes.show', $id)->with('success', 'El estado de la solicitud ha sido actualizado.');
    }
}

