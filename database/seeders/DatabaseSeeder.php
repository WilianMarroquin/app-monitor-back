<?php

namespace Database\Seeders;

use App\Console\Commands\MigrateIncidents;
use App\Console\Commands\MigrateServiceLogs;
use Database\Seeders\bases\IndexTableSeeder;
use Database\Seeders\bases\RolesPermisosBaseTableSeeder;
use Database\Seeders\bases\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(IndexTableSeeder::class);
//        if(env('APP_ENV') === 'local') {
//            $this->call(ServerTableSeeder::class);
//            $this->call(ServiceTableSeeder::class);
//        }

//        $this->migrarInformacion();
        $this->call(UserSeeder::class);
        $this->call(RolesPermisosBaseTableSeeder::class);

    }

    public function migrarInformacion(): void
    {
        Artisan::call('migrate:incidents');
        Artisan::call('migrate:service-logs');
    }

}
