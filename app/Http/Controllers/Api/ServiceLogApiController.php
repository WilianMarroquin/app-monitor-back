<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateServiceLogApiRequest;
use App\Http\Requests\Api\UpdateServiceLogApiRequest;
use App\Models\ServiceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ServiceLogApiController
 */
class ServiceLogApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Service Loges', only: ['index']),
            new Middleware('permission:Ver Service Loges', only: ['show']),
            new Middleware('permission:Crear Service Loges', only: ['store']),
            new Middleware('permission:Editar Service Loges', only: ['update']),
            new Middleware('permission:Eliminar Service Loges', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Service_logs.
     * GET|HEAD /service_logs
     */
    public function index(Request $request): JsonResponse
    {
        $service_logs = QueryBuilder::for(ServiceLog::class)
            ->allowedFilters([
    'status',
    'response_time',
    'checked_at',
    'service_id'
])
            ->allowedSorts([
    'status',
    'response_time',
    'checked_at',
    'service_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($service_logs, 'service_logs recuperados con éxito.');
    }


    /**
     * Store a newly created ServiceLog in storage.
     * POST /service_logs
     */
    public function store(CreateServiceLogApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $service_logs = ServiceLog::create($input);

        return $this->sendResponse($service_logs->toArray(), 'ServiceLog creado con éxito.');
    }

    /**
     * Display the specified ServiceLog.
     * GET|HEAD /service_logs/{id}
     */
    public function show(ServiceLog $servicelog)
    {
        return $this->sendResponse($servicelog->toArray(), 'ServiceLog recuperado con éxito.');
    }

    /**
    * Update the specified ServiceLog in storage.
    * PUT/PATCH /service_logs/{id}
    */
    public function update(UpdateServiceLogApiRequest $request, $id): JsonResponse
    {
        $servicelog = ServiceLog::findOrFail($id);
        $servicelog->update($request->validated());
        return $this->sendResponse($servicelog, 'ServiceLog actualizado con éxito.');
    }

    /**
    * Remove the specified ServiceLog from storage.
    * DELETE /service_logs/{id}
    */
    public function destroy(ServiceLog $servicelog): JsonResponse
    {
        $servicelog->delete();
        return $this->sendResponse(null, 'ServiceLog eliminado con éxito.');
    }
}
