<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class DashboardPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Listar Modulo Dashboard',
            'Ver Modulo Analitica',
            'Listar Dashboard Disponibilidad',
            'Listar Dashboard Patrones de Fallo',
            'Listar Dashboard Monitor en Vivo',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'Dashboard',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::where('name', Rol::ADMINISTRADOR)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
