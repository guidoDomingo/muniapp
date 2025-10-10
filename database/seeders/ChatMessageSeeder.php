<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\User;

class ChatMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() === 0) {
            $this->command->warn('No hay usuarios en la base de datos');
            return;
        }

        $admin = $users->first();
        $user = $users->count() > 1 ? $users->skip(1)->first() : $admin;

        // Mensajes para sala general
        Chat::create([
            'user_id' => $admin->id,
            'message' => '¡Bienvenidos al chat municipal! Estamos aquí para ayudarlos.',
            'room' => 'general',
            'chat_type' => 'public',
            'created_at' => now()->subHours(2),
        ]);

        Chat::create([
            'user_id' => $user->id,
            'message' => 'Hola, ¿cómo puedo consultar sobre el estado de mi trámite?',
            'room' => 'general',
            'chat_type' => 'public',
            'created_at' => now()->subHours(1),
        ]);

        Chat::create([
            'user_id' => $admin->id,
            'message' => 'Puedes consultar el estado de tu trámite en la sección "Mis Solicitudes" o aquí mismo proporcionándome tu número de trámite.',
            'room' => 'general',
            'chat_type' => 'public',
            'created_at' => now()->subMinutes(30),
        ]);

        // Mensajes para soporte general
        Chat::create([
            'user_id' => $admin->id,
            'message' => 'Este es el canal de soporte general. Aquí puedes hacer consultas sobre trámites y servicios municipales.',
            'room' => 'soporte-general',
            'chat_type' => 'support',
            'created_at' => now()->subDays(1),
        ]);

        Chat::create([
            'user_id' => $user->id,
            'message' => 'Necesito información sobre los requisitos para obtener una licencia comercial.',
            'room' => 'soporte-general',
            'chat_type' => 'support',
            'created_at' => now()->subMinutes(15),
        ]);

        Chat::create([
            'user_id' => $admin->id,
            'message' => 'Para la licencia comercial necesitas: DNI, certificado de bomberos, plano del local y comprobante de pago de tasas municipales. ¿Necesitas más detalles sobre algún requisito?',
            'room' => 'soporte-general',
            'chat_type' => 'support',
            'created_at' => now()->subMinutes(10),
        ]);

        // Mensajes para consultas de trámites
        Chat::create([
            'user_id' => $admin->id,
            'message' => 'Bienvenido al canal de consultas sobre trámites. Aquí puedes preguntar sobre procesos específicos.',
            'room' => 'solicitudes-tramites',
            'chat_type' => 'group',
            'created_at' => now()->subDays(1),
        ]);

        $this->command->info('Mensajes de chat de prueba creados exitosamente');
    }
}
