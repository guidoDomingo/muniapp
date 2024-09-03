<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crea un usuario utilizando el factory
        $user = User::factory()->create([
            'name' => 'municaacupe',
            'email' => 'municaacupe@municaacupe.com',
            'password' => bcrypt('caacupe123'), // Cambia 'password' por la contraseña que desees
        ]);

        // Asigna el rol de "municipalidad"
        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($role);
    }
}
