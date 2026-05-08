<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateNotificationContactApiRequest;
use App\Http\Requests\Api\UpdateNotificationContactApiRequest;
use App\Models\NotificationContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class NotificationContactApiController
 */
class NotificationContactApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Notification Contactes', only: ['index']),
            new Middleware('permission:Ver Notification Contactes', only: ['show']),
            new Middleware('permission:Crear Notification Contactes', only: ['store']),
            new Middleware('permission:Editar Notification Contactes', only: ['update']),
            new Middleware('permission:Eliminar Notification Contactes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Notification_contacts.
     * GET|HEAD /notification_contacts
     */
    public function index(Request $request): JsonResponse
    {
        $notification_contacts = QueryBuilder::for(NotificationContact::class)
            ->allowedFilters([
    'nombres',
    'apellidos',
    'telefono'
])
            ->allowedSorts([
    'nombres',
    'apellidos',
    'telefono'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($notification_contacts, 'notification_contacts recuperados con éxito.');
    }


    /**
     * Store a newly created NotificationContact in storage.
     * POST /notification_contacts
     */
    public function store(CreateNotificationContactApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $notification_contacts = NotificationContact::create($input);

        return $this->sendResponse($notification_contacts->toArray(), 'NotificationContact creado con éxito.');
    }

    /**
     * Display the specified NotificationContact.
     * GET|HEAD /notification_contacts/{id}
     */
    public function show(NotificationContact $notificationcontact)
    {
        return $this->sendResponse($notificationcontact->toArray(), 'NotificationContact recuperado con éxito.');
    }

    /**
    * Update the specified NotificationContact in storage.
    * PUT/PATCH /notification_contacts/{id}
    */
    public function update(UpdateNotificationContactApiRequest $request, $id): JsonResponse
    {
        $notificationcontact = NotificationContact::findOrFail($id);
        $notificationcontact->update($request->validated());
        return $this->sendResponse($notificationcontact, 'NotificationContact actualizado con éxito.');
    }

    /**
    * Remove the specified NotificationContact from storage.
    * DELETE /notification_contacts/{id}
    */
    public function destroy(NotificationContact $notificationcontact): JsonResponse
    {
        $notificationcontact->delete();
        return $this->sendResponse(null, 'NotificationContact eliminado con éxito.');
    }
}
