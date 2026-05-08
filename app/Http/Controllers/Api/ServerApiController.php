<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateServerApiRequest;
use App\Http\Requests\Api\UpdateServerApiRequest;
use App\Models\Server;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ServerApiController
 */
class ServerApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Serveres', only: ['index']),
            new Middleware('permission:Ver Serveres', only: ['show']),
            new Middleware('permission:Crear Serveres', only: ['store']),
            new Middleware('permission:Editar Serveres', only: ['update']),
            new Middleware('permission:Eliminar Serveres', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Servers.
     * GET|HEAD /servers
     */
    public function index(Request $request): JsonResponse
    {
        $servers = QueryBuilder::for(Server::class)
            ->allowedFilters([
    'name',
    'description',
    'internal_ip',
    'external_ip',
    'entorno'
])
            ->allowedSorts([
    'name',
    'description',
    'internal_ip',
    'external_ip',
    'entorno'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($servers, 'servers recuperados con éxito.');
    }


    /**
     * Store a newly created Server in storage.
     * POST /servers
     */
    public function store(CreateServerApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $servers = Server::create($input);

        return $this->sendResponse($servers->toArray(), 'Server creado con éxito.');
    }

    /**
     * Display the specified Server.
     * GET|HEAD /servers/{id}
     */
    public function show(Server $server)
    {
        return $this->sendResponse($server->toArray(), 'Server recuperado con éxito.');
    }

    /**
    * Update the specified Server in storage.
    * PUT/PATCH /servers/{id}
    */
    public function update(UpdateServerApiRequest $request, $id): JsonResponse
    {
        $server = Server::findOrFail($id);
        $server->update($request->validated());
        return $this->sendResponse($server, 'Server actualizado con éxito.');
    }

    /**
    * Remove the specified Server from storage.
    * DELETE /servers/{id}
    */
    public function destroy(Server $server): JsonResponse
    {
        $server->delete();
        return $this->sendResponse(null, 'Server eliminado con éxito.');
    }
}
