<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateServiceDatabaseApiRequest;
use App\Http\Requests\Api\UpdateServiceDatabaseApiRequest;
use App\Models\ServiceDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ServiceDatabaseApiController
 */
class ServiceDatabaseApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:Listar Service Databases', only: ['index']),
            new Middleware('abilities:Ver Service Databases', only: ['show']),
            new Middleware('abilities:Crear Service Databases', only: ['store']),
            new Middleware('abilities:Editar Service Databases', only: ['update']),
            new Middleware('abilities:Eliminar Service Databases', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Service_databases.
     * GET|HEAD /service_databases
     */
    public function index(Request $request): JsonResponse
    {
        $service_databases = QueryBuilder::for(ServiceDatabase::class)
            ->allowedFilters([
    'service_id',
    'db_type',
    'host_ip',
    'port',
    'username',
    'password'
])
            ->allowedSorts([
    'service_id',
    'db_type',
    'host_ip',
    'port',
    'username',
    'password'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($service_databases, 'service_databases recuperados con éxito.');
    }


    /**
     * Store a newly created ServiceDatabase in storage.
     * POST /service_databases
     */
    public function store(CreateServiceDatabaseApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $service_databases = ServiceDatabase::create($input);

        return $this->sendResponse($service_databases->toArray(), 'ServiceDatabase creado con éxito.');
    }

    /**
     * Display the specified ServiceDatabase.
     * GET|HEAD /service_databases/{id}
     */
    public function show(ServiceDatabase $servicedatabase)
    {
        return $this->sendResponse($servicedatabase->toArray(), 'ServiceDatabase recuperado con éxito.');
    }

    /**
    * Update the specified ServiceDatabase in storage.
    * PUT/PATCH /service_databases/{id}
    */
    public function update(UpdateServiceDatabaseApiRequest $request, $id): JsonResponse
    {
        $servicedatabase = ServiceDatabase::findOrFail($id);
        $servicedatabase->update($request->validated());
        return $this->sendResponse($servicedatabase, 'ServiceDatabase actualizado con éxito.');
    }

    /**
    * Remove the specified ServiceDatabase from storage.
    * DELETE /service_databases/{id}
    */
    public function destroy(ServiceDatabase $servicedatabase): JsonResponse
    {
        $servicedatabase->delete();
        return $this->sendResponse(null, 'ServiceDatabase eliminado con éxito.');
    }
}
