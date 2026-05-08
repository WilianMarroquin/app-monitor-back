<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateIncidentApiRequest;
use App\Http\Requests\Api\UpdateIncidentApiRequest;
use App\Models\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class IncidentApiController
 */
class IncidentApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Incidentes', only: ['index']),
            new Middleware('permission:Ver Incidentes', only: ['show']),
            new Middleware('permission:Crear Incidentes', only: ['store']),
            new Middleware('permission:Editar Incidentes', only: ['update']),
            new Middleware('permission:Eliminar Incidentes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Incidents.
     * GET|HEAD /incidents
     */
    public function index(Request $request): JsonResponse
    {
        $incidents = QueryBuilder::for(Incident::class)
            ->allowedFilters([
                'description',
                'status',
                'opened_at',
                'resolved_at',
                'service_id',
                'ping_id'
            ])
            ->allowedSorts([
                'id',
                'description',
                'status',
                'opened_at',
                'resolved_at',
                'service_id',
                'ping_id'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(request('page.size') ?? 10);

        return $this->sendResponse($incidents, 'incidents recuperados con éxito.');
    }


    /**
     * Store a newly created Incident in storage.
     * POST /incidents
     */
    public function store(CreateIncidentApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $incidents = Incident::create($input);

        return $this->sendResponse($incidents->toArray(), 'Incident creado con éxito.');
    }

    /**
     * Display the specified Incident.
     * GET|HEAD /incidents/{id}
     */
    public function show(Incident $incident)
    {
        $incident->load([
            'service',
            'comentarios.user',
        ]);

        return $this->sendResponse($incident->toArray(), 'Incident recuperado con éxito.');
    }

    /**
     * Update the specified Incident in storage.
     * PUT/PATCH /incidents/{id}
     */
    public function update(UpdateIncidentApiRequest $request, $id): JsonResponse
    {
        $incident = Incident::findOrFail($id);
        $incident->update($request->validated());
        return $this->sendResponse($incident, 'Incident actualizado con éxito.');
    }

    /**
     * Remove the specified Incident from storage.
     * DELETE /incidents/{id}
     */
    public function destroy(Incident $incident): JsonResponse
    {
        $incident->delete();
        return $this->sendResponse(null, 'Incident eliminado con éxito.');
    }
}
