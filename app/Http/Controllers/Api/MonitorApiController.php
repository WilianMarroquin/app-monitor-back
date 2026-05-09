<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class MonitorApiController extends Controller
{
    public function getServicesToMonitor(Request $request)
    {
        // 1. EAGER LOADING: Traemos los servicios junto con su servidor y el área del servidor
        // Solo traemos los que están activos para no hacer pings innecesarios
        $services = Service::with(['server.area'])
            ->where('is_active', true)
            ->get()
            ->map(function (Service $service) {
                // 2. PAYLOAD SHAPING: Moldeamos cada fila al formato exacto del JSON
                return [
                    'id'            => $service->id,
                    'name'          => $service->name,
                    'type'          => $service->type ?? 'HTTP',
                    'testMethod'    => $service->testMethod ?? 'HTTP', // Si en BD es test_method, cámbialo a $service->test_method
                    'checkInterval' => $service->checkInterval ?? 60,  // Igual aquí, si es check_interval...
                    'httpUrl'       => $service->http_url ?? $service->httpUrl,
                    'httpMethod'    => $service->http_method ?? $service->httpMethod ?? 'GET',
                    'port'          => $service->port,

                    // Credenciales DB (si aplica)
                    'dbHost'        => $service->detalleDataBase?->host_ip,
                    'dbPort'        => $service->detalleDataBase?->port,
                    'dbName'        => $service->detalleDataBase?->db_name, //Todo: No estoy mandando el nombre de la base de datos
                    'dbUsername'    => $service->detalleDataBase?->username,
                    'dbPassword'    => $service->detalleDataBase?->password,

                    // Relación anidada: Server -> Area
                    'server'        => $service->server ? [
                        'id'   => $service->server->id,
                        'name' => $service->server->name,
                        'ip'   => $service->server->ip,
                        'area' => $service->server->area ? [
                            'id'   => $service->server->area->id,
                            'name' => $service->server->area->name,
                        ] : null
                    ] : null,

                    // Formato ISO 8601 para la fecha (Ej: "2026-04-23T05:23:56.601Z")
                    // Si tienes un campo last_test_at, úsalo. Si no, usamos updated_at o null.
                    'lastTestAt'    => $service->updated_at ? $service->updated_at->toISOString() : null,
                ];
            });

        // 3. Empaquetamos la respuesta sin el envoltorio 'data' típico de Laravel,
        // para respetar exactamente el formato que pediste.
        return response()->json([
            'services' => $services,
            'keyId'    => 2 // Aquí puedes poner una variable de entorno env('MONITOR_KEY_ID', 2) si lo deseas
        ]);
    }
}
