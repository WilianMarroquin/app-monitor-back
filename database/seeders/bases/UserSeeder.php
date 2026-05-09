<?php

namespace Database\Seeders\bases;

use App\Models\User;
use App\Models\UserEstado;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\bases\UserSeeder"
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'primer_nombre' => 'Admin',
                'segundo_nombre' => '',
                'primer_apellido' => 'Admin',
                'segundo_apellido' => '',
                'usuario' => 'Admin',
                'estado_id' => UserEstado::ACTIVO,
                'password' => bcrypt('12345'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'operador@gmail.com'],
            [
                'primer_nombre' => 'Operador',
                'segundo_nombre' => '',
                'primer_apellido' => 'NOC',
                'segundo_apellido' => '',
                'usuario' => 'operador_noc',
                'estado_id' => UserEstado::ACTIVO,
                'password' => bcrypt('12345'),
            ]
        );

        $monitorUser = User::firstOrCreate(
            ['email' => 'monitor@sistema.local'],
            [
                'primer_nombre' => 'Sistema',
                'segundo_nombre' => 'de',
                'primer_apellido' => 'Monitoreo',
                'segundo_apellido' => 'C#',
                'usuario' => 'monitor_api',
                'estado_id' => UserEstado::ACTIVO,
                // A las Service Accounts siempre ponles contraseñas complejas aunque no se usen en el login web
                'password' => bcrypt('M0n1t0r_S3cur3_2026!'),
            ]
        );

        // 🔥 MAGIA: Generar e imprimir el Token automáticamente si no existe
        if ($monitorUser->tokens()->where('name', 'Monitor-CSharp-Key')->doesntExist()) {

            $token = $monitorUser->createToken('Monitor-CSharp-Key', ['monitor:access']);

            // Esto imprimirá un mensaje amarillo y verde en tu consola al correr el seeder
            $this->command->warn('====================================================');
            $this->command->info('🤖 TOKEN DE MONITOREO (SERVICE ACCOUNT) GENERADO:');
            $this->command->info($token->plainTextToken);
            $this->command->warn('Copia este token y pégalo en tu C# (_config.Api.ApiKey)');
            $this->command->warn('====================================================');
        }
    }
}
