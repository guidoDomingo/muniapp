<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Tramite;
use App\Models\TramiteStatus;
use App\Models\Solicitud;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Crear departamentos
        $departments = [
            [
                'name' => 'Departamento de Obras Públicas',
                'description' => 'Encargado de obras de infraestructura y mantenimiento urbano',
                'is_active' => true,
            ],
            [
                'name' => 'Departamento de Habilitaciones',
                'description' => 'Tramites de habilitaciones comerciales y permisos',
                'is_active' => true,
            ],
            [
                'name' => 'Departamento de Catastro',
                'description' => 'Gestión de propiedades y registros catastrales',
                'is_active' => true,
            ],
            [
                'name' => 'Departamento de Medio Ambiente',
                'description' => 'Control y permisos ambientales',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $deptData) {
            Department::create($deptData);
        }

        // Crear estados de trámites
        $statuses = [
            ['name' => 'Recibido', 'description' => 'Solicitud recibida', 'color' => '#6b7280', 'order' => 1],
            ['name' => 'En Revisión', 'description' => 'En proceso de revisión', 'color' => '#f59e0b', 'order' => 2],
            ['name' => 'Documentación Pendiente', 'description' => 'Faltan documentos', 'color' => '#ef4444', 'order' => 3],
            ['name' => 'En Proceso', 'description' => 'En tramitación', 'color' => '#06b6d4', 'order' => 4],
            ['name' => 'Aprobado', 'description' => 'Trámite aprobado', 'color' => '#10b981', 'order' => 5, 'is_final' => true],
            ['name' => 'Rechazado', 'description' => 'Trámite rechazado', 'color' => '#ef4444', 'order' => 6, 'is_final' => true],
        ];

        foreach ($statuses as $statusData) {
            TramiteStatus::create($statusData);
        }

        // Crear trámites ejemplo
        $tramites = [
            [
                'nombre' => 'Permiso de Construcción',
                'descripcion' => 'Solicitud de permiso para construcción de vivienda o edificio',
                'department_id' => 1,
                'estimated_days' => 30,
                'cost' => 150.00,
                'is_active' => true,
                'form_fields' => [
                    ['name' => 'direccion', 'type' => 'text', 'label' => 'Dirección de la obra', 'required' => true],
                    ['name' => 'metros_cuadrados', 'type' => 'number', 'label' => 'Metros cuadrados', 'required' => true],
                    ['name' => 'tipo_construccion', 'type' => 'select', 'label' => 'Tipo de construcción', 'options' => ['Casa', 'Edificio', 'Local comercial'], 'required' => true],
                ],
                'required_documents' => [
                    'Planos arquitectónicos',
                    'Título de propiedad',
                    'Certificado de factibilidad',
                    'Documento de identidad'
                ],
                'workflow_steps' => [
                    ['step' => 1, 'name' => 'Recepción', 'department' => 'Mesa de entradas'],
                    ['step' => 2, 'name' => 'Revisión técnica', 'department' => 'Obras públicas'],
                    ['step' => 3, 'name' => 'Aprobación', 'department' => 'Dirección'],
                ]
            ],
            [
                'nombre' => 'Habilitación Comercial',
                'descripcion' => 'Tramite para habilitar un local comercial',
                'department_id' => 2,
                'estimated_days' => 15,
                'cost' => 80.00,
                'is_active' => true,
                'form_fields' => [
                    ['name' => 'nombre_comercio', 'type' => 'text', 'label' => 'Nombre del comercio', 'required' => true],
                    ['name' => 'direccion', 'type' => 'text', 'label' => 'Dirección', 'required' => true],
                    ['name' => 'rubro', 'type' => 'select', 'label' => 'Rubro', 'options' => ['Alimentación', 'Vestimenta', 'Servicios', 'Otros'], 'required' => true],
                    ['name' => 'superficie', 'type' => 'number', 'label' => 'Superficie en m²', 'required' => true],
                ],
                'required_documents' => [
                    'Contrato de alquiler o escritura',
                    'Plano del local',
                    'Certificado de bomberos',
                    'CUIT/CUIL'
                ]
            ],
            [
                'nombre' => 'Certificado de Libre Deuda',
                'descripcion' => 'Certificado que acredita estar al día con las obligaciones municipales',
                'department_id' => 3,
                'estimated_days' => 5,
                'cost' => 25.00,
                'is_active' => true,
                'form_fields' => [
                    ['name' => 'numero_partida', 'type' => 'text', 'label' => 'Número de partida', 'required' => true],
                    ['name' => 'motivo', 'type' => 'select', 'label' => 'Motivo del certificado', 'options' => ['Venta', 'Hipoteca', 'Otros trámites'], 'required' => true],
                ],
                'required_documents' => [
                    'Documento de identidad',
                    'Última boleta de ABL'
                ]
            ],
            [
                'nombre' => 'Permiso de Poda de Árboles',
                'descripcion' => 'Autorización para poda o extracción de árboles en vía pública',
                'department_id' => 4,
                'estimated_days' => 10,
                'cost' => 0.00,
                'is_active' => true,
                'form_fields' => [
                    ['name' => 'direccion', 'type' => 'text', 'label' => 'Dirección del árbol', 'required' => true],
                    ['name' => 'tipo_arbol', 'type' => 'text', 'label' => 'Tipo de árbol', 'required' => true],
                    ['name' => 'motivo', 'type' => 'textarea', 'label' => 'Motivo de la poda', 'required' => true],
                ],
                'required_documents' => [
                    'Fotos del árbol',
                    'Documento de identidad'
                ]
            ],
            [
                'nombre' => 'Exención de Tasas por Jubilado',
                'descripcion' => 'Solicitud de exención de tasas municipales para jubilados',
                'department_id' => 3,
                'estimated_days' => 20,
                'cost' => 0.00,
                'is_active' => true,
                'form_fields' => [
                    ['name' => 'numero_partida', 'type' => 'text', 'label' => 'Número de partida', 'required' => true],
                    ['name' => 'numero_jubilacion', 'type' => 'text', 'label' => 'Número de jubilación', 'required' => true],
                    ['name' => 'monto_jubilacion', 'type' => 'number', 'label' => 'Monto de jubilación', 'required' => true],
                ],
                'required_documents' => [
                    'Certificado de ANSES',
                    'Documento de identidad',
                    'Título de propiedad',
                    'Última boleta de ABL'
                ]
            ]
        ];

        foreach ($tramites as $tramiteData) {
            Tramite::create($tramiteData);
        }

        // Crear algunas solicitudes de ejemplo
        $users = User::role('user')->take(3)->get();
        $tramitesCreated = Tramite::all();

        foreach ($users as $user) {
            foreach ($tramitesCreated->take(2) as $tramite) {
                $solicitud = Solicitud::create([
                    'user_id' => $user->id,
                    'tramite_id' => $tramite->id,
                    'detalles' => 'Solicitud de ejemplo para ' . $tramite->nombre,
                    'estado' => collect(['recibido', 'en_revision', 'en_proceso'])->random(),
                    'tracking_code' => 'SOL-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'priority' => collect(['low', 'medium', 'high'])->random(),
                    'estimated_completion_date' => now()->addDays($tramite->estimated_days ?? 15),
                    'formulario' => json_encode([
                        'direccion' => 'Calle Ejemplo 123',
                        'telefono' => '123456789',
                        'comentarios' => 'Solicitud de ejemplo'
                    ])
                ]);
            }
        }

        $this->command->info('Datos de demostración creados exitosamente!');
    }
}