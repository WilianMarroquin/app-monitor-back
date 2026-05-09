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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
                'id',
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
        $service = DB::transaction(function () use ($request) {

            $serviceCreated = Service::create($request->except(['service_database', 'service_web']));

            if ($request->filled('service_database')) {
                $dbInput = $request->service_database;

                if (isset($dbInput['password'])) {
                    $dbInput['password'] = Crypt::encryptString($dbInput['password']);
                }

                $serviceCreated->detalleDataBase()->create($dbInput);
            }

            if ($request->filled('service_web')) {
                $serviceCreated->detalleWeb()->create($request->service_web);
            }

            if($request->filled('area_ids')) {
                $serviceCreated->areas()->sync($request->area_ids);
            }

            return $serviceCreated;
        });

        return $this->sendResponse($service->toArray(), 'Servicio creado y configurado con éxito.');
    }

    /**
     * Display the specified Service.
     * GET|HEAD /services/{id}
     */
    public function show(Service $service)
    {
        $service->load([
            'detalleWeb.server',
            'detalleDataBase',
            'areas',
        ]);

        return $this->sendResponse($service->toArray(), 'Service recuperado con éxito.');
    }

    /**
     * Update the specified Service in storage.
     * PUT/PATCH /services/{id}
     */

    public function update(UpdateServiceApiRequest $request, $id): JsonResponse
    {
        $service = DB::transaction(function () use ($request, $id) {

            $serviceTarget = Service::findOrFail($id);

            $serviceTarget->update($request->except(['service_database', 'service_web']));

            if ($serviceTarget->type === 'database' && $request->filled('service_database')) {
                $dbInput = $request->service_database;

                if (!empty($dbInput['password'])) {
                    $dbInput['password'] = Crypt::encryptString($dbInput['password']);
                } else {
                    unset($dbInput['password']);
                }

                $serviceTarget->detalleWeb()->delete();

                $serviceTarget->detalleDataBase()->updateOrCreate(
                    ['service_id' => $serviceTarget->id],
                    $dbInput
                );
            }
            if ($serviceTarget->type === 'web' && $request->filled('service_web')) {
                $serviceTarget->detalleDataBase()->delete();
                $serviceTarget->detalleWeb()->updateOrCreate(
                    ['service_id' => $serviceTarget->id],
                    $request->service_web
                );
            }

            $serviceTarget->areas()->sync($request->area_ids ?? []);

            return $serviceTarget;
        });

        return $this->sendResponse($service->toArray(), 'Servicio actualizado con éxito.');
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
