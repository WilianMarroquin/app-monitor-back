<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Server::firstOrCreate([
            'name' => 'Apps2',
            'internal_ip' => 'apps2.mineducgt.local'
        ]);
        Server::firstOrCreate([
            'name' => 'AWS',
            'internal_ip' => 'SISTEMA DE REGISTROS EDUCATIVOS ADMINISTRATIVO'
        ]);
        Server::firstOrCreate([
            'name' => 'DEV-01',
            'internal_ip' => '192.168.2.10'
        ]);
        Server::firstOrCreate([
            'name' => 'sistemas5',
            'internal_ip' => 'Sistemas5'
        ]);
    }
}
