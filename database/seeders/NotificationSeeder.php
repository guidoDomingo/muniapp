<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Crear notificaciones de prueba directamente en la base de datos
            DatabaseNotification::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\MunicipalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'message' => 'Su solicitud de licencia de construcción ha sido aprobada',
                    'type' => 'tramite_aprobado',
                    'action_url' => '/solicitudes'
                ],
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

            DatabaseNotification::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\MunicipalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'message' => 'Recordatorio: Vence el plazo para presentar documentos adicionales',
                    'type' => 'recordatorio',
                    'action_url' => '/tramites'
                ],
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ]);

            DatabaseNotification::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\MunicipalNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'message' => 'Nueva actualización en el portal ciudadano disponible',
                    'type' => 'sistema',
                    'action_url' => '/'
                ],
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ]);
        }
    }
}
