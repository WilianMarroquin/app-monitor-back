<?php

namespace App\Console\Commands;

use App\Models\Incident;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateIncidents extends Command
{
    protected $signature = 'migrate:incidents';
    protected $description = 'ETL: Migra datos de la tabla antigua incident hacia el modelo Incident';

    public function handle()
    {
        $this->info('Iniciando extracción de incidentes (Legacy)...');
        $contador = 0;

        DB::connection('legacy_db')
            ->table('incident')
            ->orderBy('id')
            ->chunk(1000, function ($registrosAntiguos) use (&$contador) {

                $nuevosIncidentes = [];

                foreach ($registrosAntiguos as $registro) {

                    // 1. DATA IMPUTATION: Deducimos el status
                    // Si hay fecha de resolución, está 'resolved', de lo contrario 'open'
                    $statusCalculado = $registro->resolvedAt ? 'resolved' : 'open';

                    // 2. TRANSFORMACIÓN DE FECHAS: Convertimos de datetime(3) a timestamp (integer)
                    $openedAt = $registro->startedAt
                        ? Carbon::parse($registro->startedAt)->format('Y-m-d H:i:s')
                        : null;

                    $resolvedAt = $registro->resolvedAt
                        ? Carbon::parse($registro->resolvedAt)->format('Y-m-d H:i:s')
                        : null;

                    $nuevosIncidentes[] = [
                        'id'          => $registro->id,
                        'service_id'  => $registro->serviceId,
                        'description' => $registro->message,
                        'status'      => $statusCalculado,
                        'opened_at'   => $openedAt,
                        'resolved_at' => $resolvedAt,

                        // Sigo dejando el 1 "quemado" por ahora para que el script corra
                        'ping_id'     => null,

                        // Usamos el mismo formato para los timestamps de Laravel
                        'created_at'  => $openedAt,
                        'updated_at'  => $resolvedAt ?? $openedAt,
                    ];
                }

                // Inserción masiva
                Incident::insert($nuevosIncidentes);

                $contador += count($nuevosIncidentes);
                $this->output->write("<info>.</info>");
            });

        $this->newLine();
        $this->info("¡ETL Completado, brou! Se migraron {$contador} incidentes con éxito.");
    }
}
