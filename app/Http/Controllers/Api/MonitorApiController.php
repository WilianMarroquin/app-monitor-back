<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\NotificationContact;
use App\Models\Service;
use App\Models\ServiceLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorApiController extends Controller
{
    public function getServicesToMonitor(Request $request)
    {
        $services = Service::soloActivos()
            ->get()
            ->map(function (Service $service) {
                return [
                    'id'            => $service->id,
                    'name'          => $service->name,
                    'type'          => $service->tipo_para_json,
                    'testMethod'    => $service->testMethod ?? 'HTTP', // Si en BD es test_method, cámbialo a $service->test_method
                    'checkInterval' => $service->tiempo_espera ?? 60,  // Igual aquí, si es check_interval...
                    'httpUrl'       => $service?->detalleWeb?->url ?? null,
                    'httpMethod'    => $service->esWeb() ? ($service->httpMethod ?? 'GET') : null,
                    'port'          => $service->port,

                    // Credenciales DB (si aplica)
                    'dbHost'        => $service->detalleDataBase?->host_ip,
                    'dbPort'        => $service->esBaseDatos() ? ($service->detalleDataBase?->port ?? 3306) : null,
                    'dbName'       =>  $service->detalleDataBase?->name,
                    'dbUsername'    => $service->detalleDataBase?->username,
                    'dbPassword'    => $service->detalleDataBase?->password,

                    // Relación anidada: Server -> Area
                    'server'        => $service?->server ? [
                        'id'   => $service->server->id,
                        'name' => $service->server->name,
                        'ip'   => $service->server->internal_ip,
                        'area'   => [
                            'id' => $service->entorno == 'Produccion' ? 1 : 2,
                            'name' => $service->entorno
                        ]
                    ] : null,
                    'userNotification' => $service->contactos,
                    'lastTestAt' => $service->updated_at
                        ? (is_numeric($service->updated_at)
                            ? Carbon::createFromTimestamp($service->updated_at)->utc()->toISOString()
                            : Carbon::parse($service->updated_at)->utc()->toISOString())
                        : null,
                ];
            });

        // 3. Empaquetamos la respuesta sin el envoltorio 'data' típico de Laravel,
        // para respetar exactamente el formato que pediste.
        return response()->json([
            'services' => $services,
        ]);
    }

    public function storePingResult(Request $request)
    {
        $validated = $request->validate([
            'serviceId'      => 'required|integer|exists:services,id',
            'date'           => 'required|date',
            'result'         => 'required|string|in:SUCCESS,FAILED',
            'responseTimeMs' => 'nullable|integer',
            'observations'   => 'nullable|string',
            'userNotification' => 'nullable|array',
        ]);

        $pingDate = Carbon::parse($validated['date'], 'UTC')->setTimezone(config('app.timezone'));

        $activeIncident = Incident::where('service_id', $validated['serviceId'])
            ->where('status', 'open')
            ->first();

        if ($validated['result'] === 'FAILED') {
            $ping = ServiceLog::create([
                'service_id'    => $validated['serviceId'],
                'status'        => 'down',
                'response_time' => $validated['responseTimeMs'],
                'checked_at'    => now(),
            ]);

            if (!$activeIncident) {
                // Reasignamos la variable para asegurar que el objeto exista y pasarlo al helper
                $activeIncident = Incident::create([
                    'service_id'  => $validated['serviceId'],
                    'ping_id'     => $ping->id,
                    'status'      => 'open',
                    'description' => $validated['observations'] ?? 'Sin descripción',
                    'opened_at'   => $pingDate,
                ]);

                $activeIncident->comentarios()->create([
                    'description' => "INCIDENTE DETECTADO AUTOMÁTICAMENTE. Tiempo de respuesta: {$validated['responseTimeMs']}ms.",
                    'created_at'  => $pingDate,
                    'user_id'     => 3,
                ]);
            } else {
                $newError = $validated['observations'] ?? 'Sin descripción';
                $oldError = $activeIncident->description;

                if ($newError !== $oldError) {
                    $activeIncident->update(['description' => $newError]);

                    $activeIncident->comentarios()->create([
                        'description' => "TRANSICIÓN DE ERROR DETECTADA.\nError anterior: {$oldError}\nNuevo error: {$newError}",
                        'created_at'  => $pingDate,
                        'user_id'     => 3,
                    ]);
                }
            }

            // Ejecutamos las notificaciones asegurando que $activeIncident ya existe
            $notifications = $request->input('userNotification', []);
            if (!empty($notifications)) {
                $this->attachNotifications($activeIncident, $notifications);
            }

        } elseif ($validated['result'] === 'SUCCESS') {

            ServiceLog::create([
                'service_id'    => $validated['serviceId'],
                'status'        => 'up',
                'response_time' => $validated['responseTimeMs'],
                'checked_at'    => now(),
            ]);

            // Solo evaluamos y notificamos si realmente había un incidente abierto que cerrar
            if ($activeIncident) {
                $activeIncident->update([
                    'status'      => 'resolved',
                    'resolved_at' => $pingDate,
                ]);

                $activeIncident->comentarios()->create([
                    'description' => "RESOLUCIÓN AUTOMÁTICA. Tiempo de respuesta: {$validated['responseTimeMs']}ms.",
                    'created_at'  => $pingDate,
                    'user_id'     => 3,
                ]);

                $notifications = $request->input('userNotification', []);
                if (!empty($notifications)) {
                    $this->attachNotifications($activeIncident, $notifications);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Telemetry received successfully'
        ], 200);
    }

    /**
     * Helper para procesar las notificaciones de WhatsApp e insertarlas en la tabla pivote
     */
    private function attachNotifications(Incident $incident, array $notifications)
    {
        if (empty($notifications)) return;

        $insertData = [];
        $nombresDeUsers = [];

        foreach ($notifications as $notif) {
            $isUpEvent = filter_var($notif['EventType'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $status = $isUpEvent ? 'resolved' : 'open';

            if (isset($notif['Success']) && $notif['Success'] === true) {

                $contactId = $notif['Id'] ?? null;

                $contactName = $notif['Name'] ?? $notif['Number'] ?? 'Desconocido';

                if (!$contactId && isset($notif['Number'])) {
                    $contact = NotificationContact::firstOrCreate(
                        ['telefono' => $notif['Number']],
                        ['nombres'  => $notif['Name'] ?? 'Desconocido']
                    );
                    $contactId = $contact->id;

                    $contactName = $contact->nombre_completo ?? $contact->nombres;
                } elseif ($contactId) {
                    $contact = NotificationContact::find($contactId);
                    if ($contact) {
                        $contactName = $contact->nombre_completo ?? $contact->nombres;
                    }
                }

                if ($contactId) {
                    $nombresDeUsers[] = $contactName;

                    $insertData[] = [
                        'incident_id'             => $incident->id,
                        'notification_contact_id' => $contactId,
                        'status'                  => $status,
                        'number'                  => $notif['Number'] ?? null,
                    ];
                }
            }
        }

        if (!empty($insertData)) {
            DB::table('incident_has_notificacion')->insertOrIgnore($insertData);

            $status = $insertData[0]['status'] ?? 'open';

            $nombresUnicos = array_unique($nombresDeUsers);

            if($status == 'resolved'){
                $comentario = "RESOLUCIÓN NOTIFICADA A CONTACTOS: " . implode(', ', $nombresUnicos) . ".";
            }else{
                $comentario = "NOTIFICACIÓN DE INCIDENTE A CONTACTOS: " . implode(', ', $nombresUnicos) . ".";
            }

            $incident->comentarios()->create([
                'description' => $comentario,
                'created_at'  => now(),
                'user_id'     => 3,
            ]);
        }
    }
}
