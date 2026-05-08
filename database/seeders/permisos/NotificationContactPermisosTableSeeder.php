<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Database\Seeders\Role;
use Illuminate\Database\Seeder;

class NotificationContactPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Notification Contactes',
            'Crear Notification Contactes',
            'Editar Notification Contactes',
            'Eliminar Notification Contactes',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'NotificationContact',
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::where('name', Rol::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
