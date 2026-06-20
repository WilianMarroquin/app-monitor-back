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
        Server::firstOrCreate(
            ['name' => 'frontesirh.mineducgt.local'],
            [
                'description' => 'frontesirh',
                'internal_ip' => '192.168.100.101',
                'external_ip' => null,
                'entorno'     => 'produccion'
            ]
        );

        Server::firstOrCreate(
            ['name' => 'apps2.mineduc.gob.gt'],
            [
                'description' => 'apps2.mineduc',
                'internal_ip' => '192.168.1.1',
                'external_ip' => null,
                'entorno'     => 'produccion'
            ]
        );

        Server::firstOrCreate(
            ['name' => 'sbm.mineduc.gob.gt'],
            [
                'description' => 'sbm.mineduc',
                'internal_ip' => '192.168.1.1',
                'external_ip' => null,
                'entorno'     => 'produccion'
            ]
        );

        Server::firstOrCreate(
            ['name' => 'sire.mineduc.gob.gt'],
            [
                'description' => 'sire.mineduc.gob.gt',
                'internal_ip' => '192.168.1.1',
                'external_ip' => null,
                'entorno'     => 'produccion'
            ]
        );

        Server::firstOrCreate(
            ['name' => 'Sistemas5'],
            [
                'description' => 'Sistemas5',
                'internal_ip' => '192.168.1.23',
                'external_ip' => null,
                'entorno'     => 'produccion'
            ]
        );

        Server::firstOrCreate(
            ['name' => 'Sistemas'],
            [
                'description' => 'Sistemas',
                'internal_ip' => '192.168.13.3',
                'external_ip' => null,
                'entorno'     => 'produccion'
            ]
        );
    }
}
