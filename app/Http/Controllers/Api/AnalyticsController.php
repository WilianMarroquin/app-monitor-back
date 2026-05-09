<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
                    // Formateamos a 2 decimales para que se vea limpio en el frontend
                    'uptime_percentage' => round($uptimePercentage, 2),
                    'total_downtime_minutes' => round($totalDowntimeMinutes ?? 0, 2),
                    'mttr_minutes' => round($mttrMinutes ?? 0, 2),
                    'total_incidents' => (clone $baseQuery)->count(),
                    'open_incidents' => (clone $baseQuery)->where('status', 'open')->count(),
                ],
                'top_offenders' => $topOffenders
            ]
        ]);
    }
}
