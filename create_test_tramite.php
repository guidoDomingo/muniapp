<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tramite;

// Crear trámite de prueba con mapa
$tramite = Tramite::create([
    'nombre' => 'Solicitud de Reparación de Vía Pública',
    'descripcion' => 'Trámite para solicitar reparación de calles, veredas y espacios públicos',
    'form_fields' => json_encode([
        [
            'type' => 'text',
            'label' => 'Nombre completo del solicitante',
            'required' => true,
            'description' => 'Ingrese su nombre completo'
        ],
        [
            'type' => 'email',
            'label' => 'Correo electrónico',
            'required' => true,
            'description' => 'Para recibir notificaciones del estado de su solicitud'
        ],
        [
            'type' => 'image',
            'label' => 'Fotografías del problema',
            'required' => true,
            'description' => 'Suba fotos que muestren claramente el problema a reparar',
            'max_size' => 5
        ],
        [
            'type' => 'file',
            'label' => 'Documento adicional (opcional)',
            'required' => false,
            'description' => 'Puede adjuntar documentos adicionales si los tiene',
            'file_types' => 'pdf,doc,docx',
            'max_size' => 10
        ],
        [
            'type' => 'select',
            'label' => 'Tipo de problema',
            'required' => true,
            'options' => ['Bache en la calle', 'Vereda rota', 'Falta de iluminación', 'Drenaje obstruido', 'Otro'],
            'description' => 'Seleccione el tipo de problema que desea reportar'
        ],
        [
            'type' => 'textarea',
            'label' => 'Descripción detallada',
            'required' => true,
            'description' => 'Describa en detalle el problema y su ubicación exacta'
        ]
    ]),
    'include_map' => true,
    'is_active' => true,
    'cost' => 0
]);

echo "✅ Trámite creado exitosamente!\n";
echo "ID: " . $tramite->id . "\n";
echo "Nombre: " . $tramite->nombre . "\n";
echo "Include Map: " . ($tramite->include_map ? 'SÍ' : 'NO') . "\n";
echo "URL: http://127.0.0.1:8000/solicitudes/create/" . $tramite->id . "\n";

// Mostrar todos los trámites existentes
echo "\n=== TODOS LOS TRÁMITES ===\n";
$tramites = Tramite::all();
foreach($tramites as $t) {
    echo "ID: " . $t->id . " - " . $t->nombre . " - Mapa: " . ($t->include_map ? 'SÍ' : 'NO') . " - Activo: " . ($t->is_active ? 'SÍ' : 'NO') . "\n";
}