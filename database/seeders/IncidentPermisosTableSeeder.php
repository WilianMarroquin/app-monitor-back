<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class IncidentPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Incidentes',
            'Crear Incidentes',
            'Editar Incidentes',
            'Eliminar Incidentes',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'Incident',
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::where('name', Rol::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
