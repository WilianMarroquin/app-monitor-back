<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Service;
use App\Models\ServiceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitorApiController extends Controller
{
    public function getServicesToMonitor(Request $request)
    {
        $services = Service::with(['server.area'])
            ->soloActivos()
            ->get()
            ->map(function (Service $service) {
                return [
                    'id'            => $service->id,
                    'name'          => $service->name,
                    'type'          => $service->type ?? 'HTTP',
                    'testMethod'    => $service->testMethod ?? 'HTTP', // Si en BD es test_method, cámbialo a $service->test_method
                    'checkInterval' => $service->checkInterval ?? 60,  // Igual aquí, si es check_interval...
                    'httpUrl'       => $service?->detalleWeb?->url ?? null,
                    'httpMethod'    => $service->http_method ?? $service->httpMethod ?? 'GET',
                    'port'          => $service->port,

                    // Credenciales DB (si aplica)
                    'dbHost'        => $service->detalleDataBase?->host_ip,
                    'dbPort'        => $service->detalleDataBase?->port,
                    'dbName'        => $service->detalleDataBase?->db_name, //Todo: No estoy mandando el nombre de la base de datos
                    'dbUsername'    => $service->detalleDataBase?->username,
                    'dbPassword'    => $service->detalleDataBase?->password,

                    // Relación anidada: Server -> Area
                    'server'        => $service?->detalleWeb?->server ? [
                        'id'   => $service->detalleWeb->server->id,
                        'name' => $service->detalleWeb->server->name,
                        'ip'   => $service->detalleWeb->server->ip,
                        'area' => $service->detalleWeb->server->area ? [
                            'id'   => $service->detalleWeb->server->area->id,
                            'name' => $service->detalleWeb->server->area->name,
                        ] : null
                    ] : null,

                    // Formato ISO 8601 para la fecha (Ej: "2026-04-23T05:23:56.601Z")
                    // Si tienes un campo last_test_at, úsalo. Si no, usamos updated_at o null.
                    'lastTestAt'    => $service->updated_at ? $service->updated_at : null,
                ];
            });

        // 3. Empaquetamos la respuesta sin el envoltorio 'data' típico de Laravel,
        // para respetar exactamente el formato que pediste.
        return response()->json([
            'services' => $services,
            'keyId'    => 2 // Aquí puedes poner una variable de entorno env('MONITOR_KEY_ID', 2) si lo deseas
        ]);
    }

    public function storePingResult(Request $request)
    {
        $validated = $request->validate([
            'serviceId'      => 'required|integer|exists:services,id',
            'date'           => 'required|date',
            'result'         => 'required|string|in:SUCCESS,FAILED',
            'responseTimeMs' => 'nullable|integer',
            'observations'   => 'nullable|string'
        ]);

        $pingDate = Carbon::parse($validated['date'], 'UTC')->setTimezone(config('app.timezone'));

        $activeIncident = Incident::where('service_id', $validated['serviceId'])
            ->where('status', 'open')
            ->first();

        if ($validated['result'] === 'FAILED') {
            $ping = ServiceLog::create([
                'service_id'       => $validated['serviceId'],
                'status'           => 'down',
                'response_time' => $validated['responseTimeMs'],
                'checked_at' => now(),
            ]);

            if (!$activeIncident) {
                Incident::create([
                    'service_id'  => $validated['serviceId'],
                    'ping_id'     => $ping->id,
                    'status'      => 'open',
                    'description' => $validated['observations'],
                    'opened_at'   => $pingDate->timestamp,
                ]);
            }

        } elseif ($validated['result'] === 'SUCCESS') {

            ServiceLog::create([
                'service_id'       => $validated['serviceId'],
                'status'           => 'up',
                'response_time' => $validated['responseTimeMs'],
                'checked_at' => now(),
            ]);

            if($activeIncident) {
                $activeIncident->update([
                    'status'      => 'resolved',
                    'resolved_at' => $pingDate->timestamp,
                ]);

                $activeIncident->comentarios()->create([
                    'description' => "RESOLUCIÓN AUTOMÁTICA. Tiempo de respuesta: {$validated['responseTimeMs']}ms.",
                    'created_at'  => $pingDate->timestamp
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Telemetry received successfully'
        ], 200);
    }
}
