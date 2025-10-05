<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos específicos
        $permissions = [
            // Gestión de usuarios
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Gestión de trámites
            'view_all_tramites',
            'create_tramites',
            'edit_tramites',
            'delete_tramites',
            'approve_tramites',
            
            // Gestión de solicitudes
            'view_all_solicitudes',
            'view_own_solicitudes',
            'create_solicitudes',
            'edit_solicitudes',
            'approve_solicitudes',
            'reject_solicitudes',
            
            // Gestión de chat
            'access_admin_chat',
            'access_commission_chat',
            'access_public_chat',
            'moderate_chat',
            
            // Dashboard y reportes
            'view_admin_dashboard',
            'view_commission_dashboard',
            'view_analytics',
            'export_reports',
            
            // Configuración del sistema
            'manage_system_settings',
            'manage_notifications',
            'manage_workflow',
        ];

        // Crear todos los permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        
        // 1. Rol de Administrador
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // Admin tiene todos los permisos
        
        // 2. Rol de Comisión
        $commissionRole = Role::firstOrCreate(['name' => 'commission']);
        $commissionRole->givePermissionTo([
            'view_all_solicitudes',
            'edit_solicitudes',
            'approve_solicitudes',
            'reject_solicitudes',
            'view_all_tramites',
            'access_commission_chat',
            'access_public_chat',
            'view_commission_dashboard',
            'manage_notifications',
        ]);
        
        // 3. Rol de Usuario/Ciudadano
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'view_own_solicitudes',
            'create_solicitudes',
            'access_public_chat',
        ]);
        
        // 4. Rol de Funcionario Municipal (opcional)
        $functionalRole = Role::firstOrCreate(['name' => 'functionary']);
        $functionalRole->givePermissionTo([
            'view_all_solicitudes',
            'edit_solicitudes',
            'view_all_tramites',
            'access_commission_chat',
            'access_public_chat',
        ]);

        // Crear usuarios por defecto si no existen
        
        // Usuario Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@muniapp.com'],
            [
                'name' => 'Administrador Municipal',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->assignRole('admin');

        // Usuario Comisión
        $commission = User::firstOrCreate(
            ['email' => 'comision@muniapp.com'],
            [
                'name' => 'Comisión Municipal',
                'password' => Hash::make('comision123'),
            ]
        );
        $commission->assignRole('commission');

        // Usuario Ciudadano
        $citizen = User::firstOrCreate(
            ['email' => 'ciudadano@muniapp.com'],
            [
                'name' => 'Juan Pérez',
                'password' => Hash::make('ciudadano123'),
            ]
        );
        $citizen->assignRole('user');
    }
}