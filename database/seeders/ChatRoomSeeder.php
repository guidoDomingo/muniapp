<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChatRoom;
use App\Models\User;

class ChatRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $admin = $users->where('email', 'admin@admin.com')->first();
        
        if (!$admin) {
            $admin = $users->first();
        }

        // Crear sala de soporte general
        $supportRoom = ChatRoom::create([
            'name' => 'soporte-general',
            'display_name' => 'Soporte General',
            'type' => 'support',
            'description' => 'Sala general de soporte al ciudadano',
            'created_by' => $admin->id,
            'is_active' => true,
            'metadata' => [
                'public' => true,
                'auto_join' => true
            ]
        ]);

        // Agregar todos los usuarios a la sala de soporte
        foreach ($users as $user) {
            $supportRoom->participants()->attach($user->id, [
                'role' => $user->hasRole('admin') ? 'admin' : 'member',
                'joined_at' => now(),
                'is_active' => true,
            ]);
        }

        // Crear sala privada de ejemplo entre admin y primer usuario
        if ($users->count() > 1) {
            $firstUser = $users->where('id', '!=', $admin->id)->first();
            
            $privateRoom = ChatRoom::create([
                'name' => 'private-' . $admin->id . '-' . $firstUser->id,
                'display_name' => 'Chat Privado',
                'type' => 'private',
                'description' => 'Conversación privada',
                'created_by' => $admin->id,
                'is_active' => true,
            ]);

            $privateRoom->participants()->attach([$admin->id, $firstUser->id], [
                'role' => 'member',
                'joined_at' => now(),
                'is_active' => true,
            ]);
        }

        // Crear sala para trámites si hay solicitudes
        $solicitudRoom = ChatRoom::create([
            'name' => 'solicitudes-tramites',
            'display_name' => 'Consultas de Trámites',
            'type' => 'group',
            'description' => 'Sala para consultas sobre trámites y solicitudes',
            'created_by' => $admin->id,
            'is_active' => true,
            'metadata' => [
                'category' => 'tramites',
                'public' => true
            ]
        ]);

        // Agregar usuarios a la sala de trámites
        foreach ($users->take(5) as $user) {
            $solicitudRoom->participants()->attach($user->id, [
                'role' => $user->hasRole('admin') ? 'admin' : 'member',
                'joined_at' => now(),
                'is_active' => true,
            ]);
        }
    }
}
