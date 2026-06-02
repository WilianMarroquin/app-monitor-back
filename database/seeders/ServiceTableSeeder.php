<?php

namespace Database\Seeders;

use App\Models\Area;
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
        $area1 = Area::find(1);

        $service1 = Service::firstOrCreate(
            ['name' => 'SISTEMA DE BECAS MINISTERIAL -SBM-'],
            [
                'description'   => 'Sistema de Becas Ministerial',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 443,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 3,
            ]
        );
        $service1->detalleWeb()->firstOrCreate([
            'url' => 'https://sbm.mineduc.gob.gt/',
        ]);
        $service1->areas()->attach($area1);

        $service2 = Service::firstOrCreate(
            ['name' => 'SISTEMA DE REGISTROS EDUCATIVOS ADMINISTRATIVO -SIRE ADMON-'],
            [
                'description'   => 'Sistema que permite al personal administrativo gestionar los registros de estudiantes del SIRE.',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 443,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 2,
            ]
        );
        $service2->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/SIRE/wbFrmLogin.aspx',
        ]);
        $service2->areas()->attach($area1);

        $service3 = Service::firstOrCreate(
            ['name' => 'SISTEMA DE BECAS ESCOLARES -BECAS-'],
            [
                'description'   => 'SISTEMA DE BECAS ESCOLARES',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 80,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 5,
            ]
        );
        $service3->detalleWeb()->firstOrCreate([
            'url' => 'http://sistemas5/BECA/',
        ]);
        $service3->areas()->attach($area1);

        $service4 = Service::firstOrCreate(
            ['name' => 'Sistema Integral de Recursos Humanos (eSIRH INTERNO)'],
            [
                'description'   => 'Sistema Integral de Recursos Humanos (eSIRH INTERNO)',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 80,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 1,
            ]
        );
        $service4->detalleWeb()->firstOrCreate([
            'url' => 'http://frontesirh/MINEDUC.eSIRH.Web/frmLogin.aspx',
        ]);
        $service4->areas()->attach($area1);

        $service5 = Service::firstOrCreate(
            ['name' => 'Sistema Integral de Recursos Humanos (eSIRH EXTERNO)'],
            [
                'description'   => 'Sistema Integral de Recursos Humanos (eSIRH EXTERNO)',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 443,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 2,
            ]
        );
        $service5->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/MINEDUC.ESIRH.WEB/frmLogin.aspx',
        ]);
        $service5->areas()->attach($area1);

        $service6 = Service::firstOrCreate(
            ['name' => 'Sistema de Registros Educativos -SIRE-'],
            [
                'description'   => 'Sistema de Registros Educativos',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 443,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => null, // Vacío en el CSV original
            ]
        );
        $service6->detalleWeb()->firstOrCreate([
            'url' => 'https://sire.mineduc.gob.gt/SREW/',
        ]);
        $service6->areas()->attach($area1);

        $service7 = Service::firstOrCreate(
            ['name' => 'SISTEMA DE ASIGNACIÓN Y DOTACIÓN DE RECURSOS -SDR-'],
            [
                'description'   => 'SISTEMA DE ASIGNACIÓN Y DOTACIÓN DE RECURSOS',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 443,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => null, // Vacío en el CSV original
            ]
        );
        $service7->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/sdr/',
        ]);
        $service7->areas()->attach($area1);

        $service8 = Service::firstOrCreate(
            ['name' => 'SISTEMA -WEBPOA-'],
            [
                'description'   => 'https://apps2.mineduc.gob.gt/WEBPOA/',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 443,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 2,
            ]
        );
        $service8->detalleWeb()->firstOrCreate([
            'url' => 'https://apps2.mineduc.gob.gt/WEBPOA/',
        ]);
        $service8->areas()->attach($area1);

        $service9 = Service::firstOrCreate(
            ['name' => 'Sistema de Acceso a la Información -AIP-'],
            [
                'description'   => 'Sistema de Acceso a la Información -AIP-',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 80,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => null, // Vacío en el CSV original
            ]
        );
        $service9->detalleWeb()->firstOrCreate([
            'url' => 'http://sistemas/accesoinformacion/',
        ]);
        $service9->areas()->attach($area1);

        $service10 = Service::firstOrCreate(
            ['name' => 'Sistema Interno de Administración de Documentos. WEBSIAD'],
            [
                'description'   => 'Sistema Interno de Administración de Documentos.',
                'type'          => 'web',
                'is_active'     => true,
                'testMethod'    => 'HTTP',
                'httpMethod'    => 'GET',
                'port'          => 80,
                'tiempo_espera' => 60,
                'entorno'       => 'Produccion',
                'server_id'     => 5,
            ]
        );
        $service10->detalleWeb()->firstOrCreate([
            'url' => 'http://sistemas5/WEBSIAD/Wbfdefault.aspx',
        ]);
        $service10->areas()->attach($area1);
    }
}
