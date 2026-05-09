<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // Ejemplo de la lógica que implementaremos
    public function getUptimeStats(Request $request)
    {
        $serviceId = $request->service_id;
        $days = $request->get('days', 30); // Por defecto últimos 30 días

        // 1. Calculamos el tiempo total del periodo en segundos
        $totalPeriodSeconds = $days * 24 * 60 * 60;

        // 2. Sumamos la duración de todos los incidentes de ese servicio en ese periodo
        // Duración = resolved_at - opened_atap
        $totalDowntimeSeconds = Incident::where('service_id', $serviceId)
            ->where('status', 'resolved')
            ->where('opened_at', '>=', now()->subDays($days))
            ->selectRaw('SUM(TIMESTAMPDIFF(SECOND, opened_at, resolved_at)) as total_down')
            ->value('total_down');

        // 3. Calculamos el porcentaje
        $uptimePercentage = (($totalPeriodSeconds - $totalDowntimeSeconds) / $totalPeriodSeconds) * 100;

        return response()->json([
            'uptime_percentage' => round($uptimePercentage, 2),
            'total_downtime_minutes' => round($totalDowntimeSeconds / 60, 2)
        ]);
    }

    public function getUptimeAndResponse(Request $request)
    {
        // 1. Recibimos los filtros del frontend (Por defecto, los últimos 30 días)
        $startDate = $request->input('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $endDate = $request->input('fecha_fin')
            ? Carbon::parse($request->fecha_fin)->endOfDay()
            : now()->endOfDay();

        $serviceId = $request->input('service_id');

        $baseQuery = Incident::whereBetween('opened_at', [$startDate, $endDate]);

        if ($serviceId) {
            $baseQuery->where('service_id', $serviceId);
        }

        // ==========================================
        // 📊 MÉTRICA 1: MTTR (Tiempo Medio de Resolución)
        // ==========================================
        // Usamos TIMESTAMPDIFF de MySQL para sacar la diferencia en minutos directamente en la BD
        $mttrMinutes = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, resolved_at)) as mttr')
            ->value('mttr');

        $trendData = (clone $baseQuery)
            ->selectRaw('DATE(opened_at) as date, COUNT(*) as total_outages')
            ->groupBy(DB::raw('DATE(opened_at)'))
            ->orderBy('date', 'ASC')
            ->get();

        $alertFatigueCount = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, opened_at, resolved_at) < 3')
            ->count();

        // ==========================================
        // 📊 MÉTRICA 2: TIEMPO TOTAL DE CAÍDA (Downtime)
        // ==========================================
        $totalDowntimeMinutes = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, opened_at, resolved_at)) as total_down')
            ->value('total_down');

        // ==========================================
        // 📊 MÉTRICA 3: CÁLCULO DE UPTIME (SLA %)
        // ==========================================
        $totalPeriodMinutes = $startDate->diffInMinutes($endDate);

        // Si no se filtró por un servicio específico, multiplicamos el tiempo del periodo
        // por la cantidad de servicios activos para sacar el Uptime Global real
        $activeServicesCount = $serviceId ? 1 : Service::where('is_active', true)->count();
        $totalPossibleMinutes = $totalPeriodMinutes * ($activeServicesCount > 0 ? $activeServicesCount : 1);

        $uptimePercentage = 100;
        if ($totalPossibleMinutes > 0) {
            $uptimePercentage = (($totalPossibleMinutes - $totalDowntimeMinutes) / $totalPossibleMinutes) * 100;
        }

        // ==========================================
        // 📊 MÉTRICA 4: TOP 5 SERVICIOS CON MÁS FALLAS
        // ==========================================
        $topOffendersQuery = Incident::selectRaw('
                service_id,
                COUNT(*) as total_outages,
                SUM(TIMESTAMPDIFF(MINUTE, opened_at, resolved_at)) as total_downtime
            ')
            ->whereBetween('opened_at', [$startDate, $endDate])
            ->groupBy('service_id')
            ->orderByDesc('total_outages')
            ->limit(5);

        // Respetamos el filtro de servicio si existe
        if ($serviceId) {
            $topOffendersQuery->where('service_id', $serviceId);
        }

        $topOffenders = $topOffendersQuery->with('service:id,name')->get();

        // 3. Empaquetamos y enviamos el Payload
        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end'   => $endDate->format('Y-m-d'),
                    'days'  => $startDate->diffInDays($endDate)
                ],
                'kpis' => [
                    'uptime_percentage' => round($uptimePercentage, 2),
                    'total_downtime_minutes' => round($totalDowntimeMinutes ?? 0, 2),
                    'mttr_minutes' => round($mttrMinutes ?? 0, 2),
                    'total_incidents' => (clone $baseQuery)->count(),
                    'open_incidents' => (clone $baseQuery)->where('status', 'open')->count(),
                    // 👇 NUEVO KPI INYECTADO 👇
                    'alert_fatigue' => $alertFatigueCount,
                ],
                'top_offenders' => $topOffenders,
                // 👇 NUEVA SERIE DE TIEMPO INYECTADA 👇
                'trend_data' => $trendData
            ]
        ]);
    }

    public function getFailurePatterns(Request $request)
    {
        $startDate = $request->input('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $endDate = $request->input('fecha_fin')
            ? Carbon::parse($request->fecha_fin)->endOfDay()
            : now()->endOfDay();

        $baseQuery = Incident::whereBetween('opened_at', [$startDate, $endDate]);

        // ==========================================
        // 🔍 PATRÓN 1: EL MAPA DE CALOR (Heatmap)
        // ==========================================
        // Extraemos el día de la semana (1=Domingo, 7=Sábado en MySQL) y la hora (0-23)
        $heatmapRaw = (clone $baseQuery)
            ->selectRaw('DAYOFWEEK(opened_at) as day_of_week, HOUR(opened_at) as hour_of_day, COUNT(*) as total')
            ->groupBy(DB::raw('DAYOFWEEK(opened_at)'), DB::raw('HOUR(opened_at)'))
            ->get();

        // ==========================================
        // 🔍 PATRÓN 2: DISTRIBUCIÓN POR TIPO DE SERVICIO (Web vs DB)
        // ==========================================
        // Hacemos un JOIN con la tabla services para saber si fallan más las APIS o las Bases de Datos
        $typeDistribution = (clone $baseQuery)
            ->join('services', 'incidents.service_id', '=', 'services.id')
            ->selectRaw('services.type as service_type, COUNT(incidents.id) as total_outages')
            ->groupBy('services.type')
            ->get();

        // ==========================================
        // 🔍 PATRÓN 3: DURACIÓN DE LAS CAÍDAS (Rangos)
        // ==========================================
        // Clasificamos las caídas en "Micro-caídas", "Medianas" y "Críticas"
        $durationDistribution = (clone $baseQuery)
            ->whereNotNull('resolved_at')
            ->selectRaw('
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, opened_at, resolved_at) < 5 THEN 1 ELSE 0 END) as micro_fails,
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, opened_at, resolved_at) BETWEEN 5 AND 30 THEN 1 ELSE 0 END) as medium_fails,
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, opened_at, resolved_at) > 30 THEN 1 ELSE 0 END) as critical_fails
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'heatmap' => $heatmapRaw,
                'distribution_by_type' => $typeDistribution,
                'duration_ranges' => [
                    'Micro (< 5 min)' => (int) $durationDistribution->micro_fails,
                    'Media (5-30 min)' => (int) $durationDistribution->medium_fails,
                    'Crítica (> 30 min)' => (int) $durationDistribution->critical_fails,
                ]
            ]
        ]);
    }

    public function getLiveStatus()
    {
        // 1. Contadores Rápidos
        $totalServices = Service::where('is_active', true)->count();

        // Obtenemos los incidentes que están "open" AHORA MISMO
        $activeIncidents = Incident::with('service:id,name')
            ->where('status', 'open')
            ->orderBy('opened_at', 'desc')
            ->get();

        $downServicesCount = $activeIncidents->unique('service_id')->count();
        $upServicesCount = $totalServices - $downServicesCount;

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total' => $totalServices,
                    'up' => $upServicesCount,
                    'down' => $downServicesCount,
                    'status_color' => $downServicesCount > 0 ? 'error' : 'success' // Semáforo global
                ],
                // Mandamos los incidentes activos para mostrarlos en tarjetas rojas
                'active_alerts' => $activeIncidents->map(function($incident) {
                    return [
                        'id' => $incident->id,
                        'service_name' => $incident->service ? $incident->service->name : 'Desconocido',
                        'description' => $incident->description,
                        'timestamp' => $incident->opened_at // Unix timestamp para el frontend
                    ];
                })
            ]
        ]);
    }
}
