<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAreaApiRequest;
use App\Http\Requests\Api\UpdateAreaApiRequest;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AreaApiController
 */
class AreaApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('abilities:Listar Areas', only: ['index']),
            new Middleware('abilities:Ver Areas', only: ['show']),
            new Middleware('abilities:Crear Areas', only: ['store']),
            new Middleware('abilities:Editar Areas', only: ['update']),
            new Middleware('abilities:Eliminar Areas', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Areas.
     * GET|HEAD /areas
     */
    public function index(): JsonResponse
    {
        $areas = QueryBuilder::for(Area::class)
            ->allowedFilters([
                'name',
                'description'
            ])
            ->allowedSorts([
                'name',
                'description'
            ])
            ->allowedIncludes([
                'contactosAsignados'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($areas, 'areas recuperados con éxito.');
    }


    /**
     * Store a newly created Area in storage.
     * POST /areas
     */
    public function store(CreateAreaApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $areas = Area::create($input);

        if(isset($input['personas_asignadas_ids'])) {
            $areas->contactosAsignados()->sync($input['personas_asignadas_ids']);
        }

        return $this->sendResponse($areas->toArray(), 'Area creado con éxito.');
    }

    /**
     * Display the specified Area.
     * GET|HEAD /areas/{id}
     */
    public function show(Area $area)
    {
        $area->load('contactosAsignados');

        return $this->sendResponse($area->toArray(), 'Area recuperado con éxito.');
    }

    /**
     * Update the specified Area in storage.
     * PUT/PATCH /areas/{id}
     */
    public function update(UpdateAreaApiRequest $request, $id): JsonResponse
    {
        $area = Area::findOrFail($id);
        $area->update($request->validated());

        if(isset($request->personas_asignadas_ids)) {
            $area->contactosAsignados()->sync($request->personas_asignadas_ids);
        }

        return $this->sendResponse($area, 'Area actualizado con éxito.');
    }

    /**
     * Remove the specified Area from storage.
     * DELETE /areas/{id}
     */
    public function destroy(Area $area): JsonResponse
    {
        $area->delete();
        return $this->sendResponse(null, 'Area eliminado con éxito.');
    }
}
