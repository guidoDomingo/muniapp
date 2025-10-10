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
        $tramite = Tramite::findOrFail($tramiteId);
        $campos = [];

        // Procesar campos dinámicos del formulario
        if ($request->has('form_data') && $tramite->form_fields) {
            foreach ($tramite->form_fields as $index => $field) {
                $fieldName = "form_data.{$index}";
                $fieldType = $field['type'] ?? 'text';
                $fieldLabel = $field['label'] ?? "Campo {$index}";

                if ($request->has("form_data.{$index}")) {
                    $value = $request->input("form_data.{$index}");

                    // Manejar archivos
                    if ($fieldType === 'file' && $request->hasFile("form_data.{$index}")) {
                        $file = $request->file("form_data.{$index}");
                        if ($file->isValid()) {
                            $disk = Storage::disk('public');
                            
                            // Crear nombre seguro para el archivo
                            $extension = $file->getClientOriginalExtension();
                            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
                            $filename = uniqid() . '_' . $safeName . '.' . $extension;
                            
                            $path = 'documentos/' . $filename;
                            $disk->put($path, file_get_contents($file));
                            $value = $path;
                        }
                    }

                    // Manejar arrays (checkbox)
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    $campos[] = [
                        'nombre' => $fieldLabel,
                        'tipo' => $fieldType,
                        'valor' => $value
                    ];
                }
            }
        } else {
            // Fallback para el formulario estático (retrocompatibilidad)
            if ($request->hasFile('documento_identidad')) {
                $documento = $request->file('documento_identidad');
                if ($documento->isValid()) {
                    $disk = Storage::disk('public');
                    
                    // Crear nombre seguro para el archivo
                    $extension = $documento->getClientOriginalExtension();
                    $originalName = pathinfo($documento->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
                    $filename = uniqid() . '_' . $safeName . '.' . $extension;
                    
                    $path = 'documentos/' . $filename;
                    $disk->put($path, file_get_contents($documento));
                    $campos[] = [
                        'nombre' => 'documento_vigencia',
                        'tipo' => 'file',
                        'valor' => $path
                    ];
                }
            }

            if ($request->hasFile('imagen_usuario')) {
                foreach ($request->file('imagen_usuario') as $imagen) {
                    if ($imagen->isValid()) {
                        $disk = Storage::disk('public');
                        
                        // Crear nombre seguro para la imagen
                        $extension = $imagen->getClientOriginalExtension();
                        $originalName = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
                        $filename = uniqid() . '_' . $safeName . '.' . $extension;
                        
                        $path = 'imagenes/' . $filename;
                        $fullPath = storage_path('app/public/') . $path;
                        $imagen->move(dirname($fullPath), $filename);
                        $campos[] = [
                            'nombre' => 'imagen_zona',
                            'tipo' => 'image',
                            'valor' => $path
                        ];
                    }
                }
            }

            if ($request->has('nombre_usuario')) {
                $campos[] = [
                    'nombre' => 'nombre_comision',
                    'tipo' => 'text',
                    'valor' => $request->input('nombre_usuario')
                ];
            }
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
        $solicitud->latitud = $request->latitud;
        $solicitud->longitud = $request->longitud;
        $solicitud->save();

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud enviada exitosamente.');
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

    public function updateComentario(Request $request, $id)
    {

        // Verificar si el usuario es administrador
        if (!Auth::user()->hasRole('admin')) {
            // Código para usuarios admin
            return redirect()->back()->withErrors(['mensaje' => 'No tienes permiso para realizar esta acción.']);
        }



        // Validar la solicitud
        $request->validate([
            'comentarios' => 'required'
        ]);



        // Buscar la solicitud por ID
        $solicitud = Solicitud::findOrFail($id);

        // Actualizar el estado
        $solicitud->comentario = $request->input('comentarios');
        $solicitud->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('solicitudes.show', $id)->with('success', 'El comentario de la solicitud ha sido actualizado.');
    }
}

