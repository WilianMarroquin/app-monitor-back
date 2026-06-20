<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\NotificationContact;
use App\Models\Service;
use Illuminate\Database\Seeder;

class AreasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $contacto = NotificationContact::firstOrCreate([
            'nombres' => 'Carlos',
            'apellidos' => 'Amilcar Tezó',
            'telefono' => '55355058'
        ]);

        $area = Area::firstOrCreate([
            'name' => 'Infraestructura y servicios críticos',
            'description' => 'Infraestructuras estratégicas que proporcionan servicios esenciales'
        ]);

        $area->contactosAsignados()->attach($contacto);
    }
}
