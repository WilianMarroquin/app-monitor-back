<?php

namespace App\Console\Commands;

use App\Models\ServiceLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateServiceLogs extends Command
{
    // El comando que escribirás en la terminal
    protected $signature = 'migrate:service-logs';

    // Una breve descripción
    protected $description = 'ETL: Migra los datos de la tabla antigua monitorcheck hacia ServiceLog';

    public function handle()
    {
        $this->info('Iniciando la extracción de la base de datos Legacy...');
        $contador = 0;

        // 1. EXTRACT: Nos conectamos a la BD antigua y traemos los datos en bloques de 1000
        DB::connection('legacy_db')
            ->table('monitorcheck')
            ->orderBy('id') // Chunk requiere un orderBy obligatorio
            ->chunk(1000, function ($registrosAntiguos) use (&$contador) {

                $nuevosLogs = [];

                // 2. TRANSFORM: Mapeamos las columnas viejas a las nuevas
                foreach ($registrosAntiguos as $registro) {
                    $nuevosLogs[] = [
                        'service_id' => $registro->serviceId,
                        'status'     => $registro->status,
                        'response_time' => $registro->responseTime,
                        'checked_at' => $registro->checkedAt,
                    ];
                }

                // 3. LOAD: Inserción masiva. Usamos insert() en lugar de create()
                // porque insert es unas 10 veces más rápido para miles de registros.
                ServiceLog::insert($nuevosLogs);

                $contador += count($nuevosLogs);
                $this->output->write("<info>.</info>"); // Imprime un puntito por cada bloque para ver progreso
            });

        $this->newLine();
        $this->info("¡ETL Completado, brou! Se migraron {$contador} registros con éxito.");
    }
}
