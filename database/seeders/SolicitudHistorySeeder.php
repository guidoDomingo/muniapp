<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Solicitud;
use App\Models\SolicitudHistory;
use App\Models\User;

class SolicitudHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las solicitudes existentes
        $solicitudes = Solicitud::all();
        $adminUser = User::where('email', 'admin@muniapp.com')->first();
        
        if (!$adminUser) {
            $this->command->error('Usuario admin no encontrado. Ejecuta primero el RoleAndPermissionSeeder.');
            return;
        }

        foreach ($solicitudes as $solicitud) {
            // Crear entrada inicial de creación
            SolicitudHistory::create([
                'solicitud_id' => $solicitud->id,
                'user_id' => $solicitud->user_id,
                'action' => 'created',
                'old_value' => null,
                'new_value' => 'pendiente',
                'description' => 'Solicitud creada por el ciudadano',
                'metadata' => [
                    'tramite' => $solicitud->tramite->nombre ?? 'N/A',
                    'ip' => '127.0.0.1',
                    'user_agent' => 'MuniApp System'
                ],
                'created_at' => $solicitud->created_at,
                'updated_at' => $solicitud->created_at,
            ]);

            // Si la solicitud no está en estado pendiente, crear entrada de cambio de estado
            if ($solicitud->estado !== 'pendiente') {
                SolicitudHistory::create([
                    'solicitud_id' => $solicitud->id,
                    'user_id' => $adminUser->id,
                    'action' => 'status_changed',
                    'old_value' => 'pendiente',
                    'new_value' => $solicitud->estado,
                    'description' => "Estado cambiado a: " . ucfirst($solicitud->estado),
                    'metadata' => [
                        'automated' => true,
                        'reason' => 'Migración de datos históricos'
                    ],
                    'created_at' => $solicitud->updated_at,
                    'updated_at' => $solicitud->updated_at,
                ]);
            }

            // Si la solicitud tiene comentarios, crear entrada
            if (isset($solicitud->comentario) && $solicitud->comentario) {
                SolicitudHistory::create([
                    'solicitud_id' => $solicitud->id,
                    'user_id' => $adminUser->id,
                    'action' => 'commented',
                    'old_value' => null,
                    'new_value' => 'Comentario agregado',
                    'description' => 'Comentario administrativo añadido',
                    'metadata' => [
                        'comment_length' => strlen($solicitud->comentario),
                        'has_html' => strip_tags($solicitud->comentario) !== $solicitud->comentario
                    ],
                    'created_at' => $solicitud->updated_at,
                    'updated_at' => $solicitud->updated_at,
                ]);
            }
        }

        $this->command->info('Historial de solicitudes poblado exitosamente.');
        $this->command->info('Entradas creadas: ' . SolicitudHistory::count());
    }
}
