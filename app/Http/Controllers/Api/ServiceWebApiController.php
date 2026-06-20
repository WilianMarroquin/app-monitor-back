<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateServiceWebApiRequest;
use App\Http\Requests\Api\UpdateServiceWebApiRequest;
use App\Models\ServiceWeb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ServiceWebApiController
 */
class ServiceWebApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:Listar Service Webes', only: ['index']),
            new Middleware('abilities:Ver Service Webes', only: ['show']),
            new Middleware('abilities:Crear Service Webes', only: ['store']),
            new Middleware('abilities:Editar Service Webes', only: ['update']),
            new Middleware('abilities:Eliminar Service Webes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Service_webs.
     * GET|HEAD /service_webs
     */
    public function index(Request $request): JsonResponse
    {
        $service_webs = QueryBuilder::for(ServiceWeb::class)
            ->allowedFilters([
    'service_id',
    'url',
    'server_id'
])
            ->allowedSorts([
    'service_id',
    'url',
    'server_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($service_webs, 'service_webs recuperados con éxito.');
    }


    /**
     * Store a newly created ServiceWeb in storage.
     * POST /service_webs
     */
    public function store(CreateServiceWebApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $service_webs = ServiceWeb::create($input);

        return $this->sendResponse($service_webs->toArray(), 'ServiceWeb creado con éxito.');
    }

    /**
     * Display the specified ServiceWeb.
     * GET|HEAD /service_webs/{id}
     */
    public function show(ServiceWeb $serviceweb)
    {
        return $this->sendResponse($serviceweb->toArray(), 'ServiceWeb recuperado con éxito.');
    }

    /**
    * Update the specified ServiceWeb in storage.
    * PUT/PATCH /service_webs/{id}
    */
    public function update(UpdateServiceWebApiRequest $request, $id): JsonResponse
    {
        $serviceweb = ServiceWeb::findOrFail($id);
        $serviceweb->update($request->validated());
        return $this->sendResponse($serviceweb, 'ServiceWeb actualizado con éxito.');
    }

    /**
    * Remove the specified ServiceWeb from storage.
    * DELETE /service_webs/{id}
    */
    public function destroy(ServiceWeb $serviceweb): JsonResponse
    {
        $serviceweb->delete();
        return $this->sendResponse(null, 'ServiceWeb eliminado con éxito.');
    }
}
