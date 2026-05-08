<?php

namespace Database\Seeders\bases;

use App\Models\MenuOpcion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuOpcionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        MenuOpcion::truncate();

        MenuOpcion::create([
            "id" => 1,
            "titulo" => "Inicio",
            "icono" => "ri-home-8-line",
            "ruta" => "index",
            "orden" => 0,
            "action" => "Listar Inicio",
            "subject" => "Inicio",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "id" => 2,
            "titulo" => null,
            "icono" => null,
            "ruta" => null,
            "orden" => 1,
            "titulo_seccion" => "Administración",
            "action" => "Ver Modulo Usuarios",
            "subject" => "User",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "id" => 3,
            "titulo" => "Modulo Usuarios",
            "icono" => "ri-group-line",
            "ruta" => null,
            "orden" => 2,
            "action" => "Ver Modulo Usuarios",
            "subject" => "User",
            "parent_id" => null
        ]);

// Submenús del Modulo Usuarios
        MenuOpcion::create([
            "id" => 4,
            "titulo" => "Usuarios",
            "icono" => "ri-list-ordered-2",
            "ruta" => "admin-modulo-usuarios-usuarios",
            "orden" => 0,
            "action" => "Listar Usuarios",
            "subject" => "User",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "id" => 5,
            "titulo" => "Roles",
            "icono" => "ri-folder-shield-2-line",
            "ruta" => "admin-modulo-usuarios-roles",
            "orden" => 1,
            "action" => "Listar Roles",
            "subject" => "Rol",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "id" => 7,
            "titulo" => "Permisos",
            "icono" => "ri-file-shield-2-fill",
            "ruta" => "admin-modulo-usuarios-permisos",
            "orden" => 2,
            "action" => "Listar Permisos",
            "subject" => "Permission",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "id" => 6,
            "titulo" => "Estados de usuarios",
            "icono" => "ri-folder-user-fill",
            "ruta" => "admin-modulo-usuarios-usuario-estados",
            "orden" => 3,
            "action" => "Listar Usuario Estados",
            "subject" => "UserEstado",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "id" => 8,
            "titulo" => "Configuraciones",
            "icono" => "ri-folder-settings-fill",
            "ruta" => null,
            "orden" => 3,
            "action" => "Ver Modulo Configuracion",
            "subject" => "Configuracion",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "id" => 9,
            "titulo" => "Opciones Menu",
            "icono" => "ri-apps-2-add-line",
            "ruta" => "admin-configuraciones-menu",
            "orden" => 0,
            "action" => "Listar Menu Opciones",
            "subject" => "Menu Opcion",
            "parent_id" => 8
        ]);

        MenuOpcion::create([
            "id" => 10,
            "titulo" => "Generales",
            "icono" => "ri-settings-3-fill",
            "ruta" => "admin-configuraciones-generales",
            "orden" => 1,
            "action" => "Listar Configuraciones Generales",
            "subject" => "Configuracion",
            "parent_id" => 8
        ]);

        $catalogos = MenuOpcion::create([
            "id" => 10,
            "titulo" => "Catálogos",
            "icono" => "ri-settings-3-fill",
            "ruta" => null,
            "orden" => 1,
            "action" => "Listar Modulo Catálogos",
            "subject" => "Configuracion",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "id" => 10,
            "titulo" => "Areas",
            "icono" => "ri-settings-3-fill",
            "ruta" => 'areas',
            "orden" => 1,
            "action" => "Listar Areas",
            "subject" => "Area",
            "parent_id" => $catalogos->id
        ]);

        MenuOpcion::create([
            "id" => 11,
            "titulo" => null,
            "icono" => null,
            "ruta" => null,
            "orden" => 4,
            "titulo_seccion" => "Modulo Programación",
            "action" => "Ver Modulo Desarrollo",
            "subject" => "Desarrollo",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "id" => 12,
            "titulo" => "Developers",
            "icono" => "ri-tools-fill",
            "ruta" => "second-page",
            "orden" => 5,
            "action" => "Ver Modulo Desarrollo",
            "subject" => "Desarrollo",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "id" => 13,
            "titulo" => "Configuraciones",
            "icono" => "ri-settings-5-fill",
            "ruta" => "dev-configuraciones",
            "orden" => 0,
            "action" => "Listar Configuraciones",
            "subject" => "Configuracion",
            "parent_id" => 12
        ]);

        MenuOpcion::create([
            "id" => 14,
            "titulo" => "Componentes",
            "icono" => "ri-code-box-line",
            "ruta" => "dev-componentes",
            "orden" => 1,
            "action" => "Listar Componentes",
            "subject" => "Desarrollo",
            "parent_id" => 12
        ]);



        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
