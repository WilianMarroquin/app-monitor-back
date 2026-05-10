<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service1 = Service::firstOrCreate([
            'name' => '-SDR- SISTEMA DE ASIGNACIÓN Y DOTACIÓN DE RECURSOS',
            'type' => 'web',
            'httpMethod' => 'GET',
            'testMethod' => 'HTTP',
            'is_active' => true,
            'server_id' => 1
        ]);

        $service1->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/sdr/',
        ]);

        $service2 = Service::firstOrCreate([
            'name' => '-SBI- SISTEMA DE BECAS DE INGLÉS',
            'type' => 'web',
            'httpMethod' => 'GET',
            'testMethod' => 'HTTP',
            'is_active' => true,
            'server_id' => 1
        ]);

        $service2->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/SBI/',]);

        $service3 = Service::firstOrCreate([
            'name' => '-SBM- SISTEMA BECA MINISTERIAL',
            'type' => 'web',
            'httpMethod' => 'GET',
            'testMethod' => 'HTTP',
            'is_active' => true,
            'server_id' => 1
        ]);

        $service3->detalleWeb()->firstOrCreate([
            'url' => 'https://sbm.mineduc.gob.gt/',]);

        $service4 = Service::firstOrCreate([
            'name' => '-SIRE- SISTEMA DE REGISTROS EDUCATIVOS ADMINISTRATIVO',
            'type' => 'web',
            'httpMethod' => 'GET',
            'testMethod' => 'HTTP',
            'is_active' => true,
            'server_id' => 2
        ]);

        $service4->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/SIRE/wbFrmLogin.aspx',
        ]);

        $service5 = Service::firstOrCreate([
            'name' => '-ESIRH- SISTEMA INTEGRAL DE RECURSOS HUMANOS',
            'type' => 'web',
            'httpMethod' => 'GET',
            'testMethod' => 'HTTP',
            'is_active' => true,
            'server_id' => 1
        ]);

        $service5->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/MINEDUC.ESIRH.WEB/frmLogin.aspx',
        ]);

        $service6 = Service::firstOrCreate([
            'name' => '-WebSiad- SISTEMA DE EXPEDIENTES ',
            'type' => 'web',
            'httpMethod' => 'GET',
            'testMethod' => 'HTTP',
            'is_active' => true,
            'server_id' => 4
        ]);

        $service6->detalleWeb()->firstOrCreate([
            'url' => 'http://sistemas5/WEBSIAD/Wbfdefault.aspx',
        ]);
    }
}
