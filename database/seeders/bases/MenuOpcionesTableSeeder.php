<?php

namespace Database\Seeders\bases;

use App\Models\MenuOpcion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuOpcionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejccutar este seeder, se debe ejecutar el comando: php artisan db:seed --class="Database\Seeders\bases\MenuOpcionesTableSeeder"
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        MenuOpcion::truncate();

        MenuOpcion::create([
            "titulo" => "Inicio",
            "icono" => "ri-home-8-line",
            "ruta" => "index",
            "orden" => 0,
            "action" => "Listar Inicio",
            "subject" => "Inicio",
            "parent_id" => null
        ]);

        MenuOpcion::create([
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
            "titulo" => "Usuarios",
            "icono" => "ri-list-ordered-2",
            "ruta" => "admin-modulo-usuarios-usuarios",
            "orden" => 3,
            "action" => "Listar Usuarios",
            "subject" => "User",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "titulo" => "Roles",
            "icono" => "ri-folder-shield-2-line",
            "ruta" => "admin-modulo-usuarios-roles",
            "orden" => 5,
            "action" => "Listar Roles",
            "subject" => "Rol",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "titulo" => "Permisos",
            "icono" => "ri-file-shield-2-fill",
            "ruta" => "admin-modulo-usuarios-permisos",
            "orden" => 6,
            "action" => "Listar Permisos",
            "subject" => "Permission",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "titulo" => "Estados de usuarios",
            "icono" => "ri-folder-user-fill",
            "ruta" => "admin-modulo-usuarios-usuario-estados",
            "orden" => 7,
            "action" => "Listar Usuario Estados",
            "subject" => "UserEstado",
            "parent_id" => 3
        ]);

        MenuOpcion::create([
            "titulo" => "Configuraciones",
            "icono" => "ri-folder-settings-fill",
            "ruta" => null,
            "orden" => 8,
            "action" => "Ver Modulo Configuracion",
            "subject" => "Configuracion",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Opciones Menu",
            "icono" => "ri-apps-2-add-line",
            "ruta" => "admin-configuraciones-menu",
            "orden" => 9,
            "action" => "Listar Menu Opciones",
            "subject" => "Menu Opcion",
            "parent_id" => 8
        ]);

        MenuOpcion::create([
            "titulo" => "Generales",
            "icono" => "ri-settings-3-fill",
            "ruta" => "admin-configuraciones-generales",
            "orden" => 10,
            "action" => "Listar Configuraciones Generales",
            "subject" => "Configuracion",
            "parent_id" => 8
        ]);

        $catalogos = MenuOpcion::create([
            "titulo" => "Catálogos",
            "icono" => "ri-folder-4-fill",
            "ruta" => null,
            "orden" => 11,
            "action" => "Listar Modulo Catálogos",
            "subject" => "Configuracion",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Areas",
            "icono" => "ri-building-2-fill",
            "ruta" => 'areas',
            "orden" => 12,
            "action" => "Listar Areas",
            "subject" => "Area",
            "parent_id" => $catalogos->id
        ]);

        MenuOpcion::create([
            "titulo" => "Contactos",
            "icono" => "ri-contacts-book-2-fill",
            "ruta" => 'notification-contacts',
            "orden" => 13,
            "action" => "Listar Notification Contactes",
            "subject" => "NotificationContact",
            "parent_id" => $catalogos->id
        ]);

        MenuOpcion::create([
            "titulo" => "Servidores",
            "icono" => "ri-server-fill",
            "ruta" => 'servers',
            "orden" => 14,
            "action" => "Listar Serveres",
            "subject" => "Server",
            "parent_id" => $catalogos->id
        ]);

        MenuOpcion::create([
            "titulo" => null,
            "icono" => null,
            "ruta" => null,
            "orden" => 15,
            "titulo_seccion" => "Core",
            "action" => "Ver Modulo Core",
            "subject" => "Core",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Servicios",
            "icono" => "ri-service-fill",
            "ruta" => 'services',
            "orden" => 16,
            "action" => "Listar Services",
            "subject" => "Service",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => null,
            "icono" => null,
            "ruta" => null,
            "orden" => 17,
            "titulo_seccion" => "Modulo Programación",
            "action" => "Ver Modulo Desarrollo",
            "subject" => "Desarrollo",
            "parent_id" => null
        ]);

        $developers = MenuOpcion::create([
            "titulo" => "Developers",
            "icono" => "ri-tools-fill",
            "ruta" => "second-page",
            "orden" => 18,
            "action" => "Ver Modulo Desarrollo",
            "subject" => "Desarrollo",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Configuraciones",
            "icono" => "ri-settings-5-fill",
            "ruta" => "dev-configuraciones",
            "orden" => 19,
            "action" => "Listar Configuraciones",
            "subject" => "Configuracion",
            "parent_id" => $developers->id
        ]);

        MenuOpcion::create([
            "titulo" => "Componentes",
            "icono" => "ri-code-box-line",
            "ruta" => "dev-componentes",
            "orden" => 20,
            "action" => "Listar Componentes",
            "subject" => "Desarrollo",
            "parent_id" => $developers->id
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
