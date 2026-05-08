<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateServiceApiRequest;
use App\Http\Requests\Api\UpdateServiceApiRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ServiceApiController
 */
class ServiceApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Services', only: ['index']),
            new Middleware('permission:Ver Services', only: ['show']),
            new Middleware('permission:Crear Services', only: ['store']),
            new Middleware('permission:Editar Services', only: ['update']),
            new Middleware('permission:Eliminar Services', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Services.
     * GET|HEAD /services
     */
    public function index(Request $request): JsonResponse
    {
        $services = QueryBuilder::for(Service::class)
            ->allowedFilters([
    'name',
    'description',
    'type',
    'is_active',
    'testMethod',
    'httpMethod'
])
            ->allowedSorts([
    'name',
    'description',
    'type',
    'is_active',
    'testMethod',
    'httpMethod'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($services, 'services recuperados con éxito.');
    }


    /**
     * Store a newly created Service in storage.
     * POST /services
     */
    public function store(CreateServiceApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $services = Service::create($input);

        return $this->sendResponse($services->toArray(), 'Service creado con éxito.');
    }

    /**
     * Display the specified Service.
     * GET|HEAD /services/{id}
     */
    public function show(Service $service)
    {
        return $this->sendResponse($service->toArray(), 'Service recuperado con éxito.');
    }

    /**
    * Update the specified Service in storage.
    * PUT/PATCH /services/{id}
    */
    public function update(UpdateServiceApiRequest $request, $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->update($request->validated());
        return $this->sendResponse($service, 'Service actualizado con éxito.');
    }

    /**
    * Remove the specified Service from storage.
    * DELETE /services/{id}
    */
    public function destroy(Service $service): JsonResponse
    {
        $service->delete();
        return $this->sendResponse(null, 'Service eliminado con éxito.');
    }
}
