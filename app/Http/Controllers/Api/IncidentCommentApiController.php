<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateIncidentCommentApiRequest;
use App\Http\Requests\Api\UpdateIncidentCommentApiRequest;
use App\Models\IncidentComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class IncidentCommentApiController
 */
class IncidentCommentApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Incident Commentes', only: ['index']),
            new Middleware('permission:Ver Incident Commentes', only: ['show']),
            new Middleware('permission:Crear Incident Commentes', only: ['store']),
            new Middleware('permission:Editar Incident Commentes', only: ['update']),
            new Middleware('permission:Eliminar Incident Commentes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Incident_comments.
     * GET|HEAD /incident_comments
     */
    public function index(Request $request): JsonResponse
    {
        $incident_comments = QueryBuilder::for(IncidentComment::class)
            ->allowedFilters([
    'description',
    'incident_id'
])
            ->allowedSorts([
    'description',
    'incident_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($incident_comments, 'incident_comments recuperados con éxito.');
    }


    /**
     * Store a newly created IncidentComment in storage.
     * POST /incident_comments
     */
    public function store(CreateIncidentCommentApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $incident_comments = IncidentComment::create($input);

        return $this->sendResponse($incident_comments->toArray(), 'IncidentComment creado con éxito.');
    }

    /**
     * Display the specified IncidentComment.
     * GET|HEAD /incident_comments/{id}
     */
    public function show(IncidentComment $incidentcomment)
    {
        return $this->sendResponse($incidentcomment->toArray(), 'IncidentComment recuperado con éxito.');
    }

    /**
    * Update the specified IncidentComment in storage.
    * PUT/PATCH /incident_comments/{id}
    */
    public function update(UpdateIncidentCommentApiRequest $request, $id): JsonResponse
    {
        $incidentcomment = IncidentComment::findOrFail($id);
        $incidentcomment->update($request->validated());
        return $this->sendResponse($incidentcomment, 'IncidentComment actualizado con éxito.');
    }

    /**
    * Remove the specified IncidentComment from storage.
    * DELETE /incident_comments/{id}
    */
    public function destroy(IncidentComment $incidentcomment): JsonResponse
    {
        $incidentcomment->delete();
        return $this->sendResponse(null, 'IncidentComment eliminado con éxito.');
    }
}
