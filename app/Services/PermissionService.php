<?php
namespace App\Services;

use App\Models\EventsModel;
use App\Models\UserAuthorPermissionModel;
use App\Models\UserEventRestrictionModel;

class PermissionService
{
    protected $authorPermissionModel;
    protected $eventRestrictionModel;

    public function __construct()
    {
        $this->authorPermissionModel = new UserAuthorPermissionModel();
        $this->eventRestrictionModel = new UserEventRestrictionModel();
    }

    /**
     * Obtener todos los eventos que un usuario puede ver
     */
    public function getUserAllowedEvents($userId)
    {
        // Obtener permisos de autor del usuario
        $authorPermissions = $this->authorPermissionModel->getUserAuthors($userId);

        if (empty($authorPermissions)) {
            return [];
        }

        $allowedEvents = [];

        foreach ($authorPermissions as $permission) {
            if ($permission['permission_level'] === 'full') {
                // Usuario puede ver TODOS los eventos de este autor
                $eventModel = new EventsModel();
                $authorEvents = $eventModel->where('author_id', $permission['id'])
                    ->where('event_status', 'Activo')
                    ->findAll();

                $allowedEvents = array_merge($allowedEvents, $authorEvents);
            } else {
                // Usuario solo puede ver eventos específicos
                $restrictedEvents = $this->eventRestrictionModel->getUserRestrictedEvents($userId);
                $allowedEvents = array_merge($allowedEvents, $restrictedEvents);
            }
        }

        // Eliminar duplicados
        return array_unique($allowedEvents, SORT_REGULAR);
    }



    public function getUserEvents($userId)
    {

        // Usuario solo puede ver eventos específicos
        $restrictedEvents = $this->eventRestrictionModel->getUserRestrictedEvents($userId);

        // Eliminar duplicados
        return array_unique($restrictedEvents, SORT_REGULAR);
    }


    /**
     * Verificar si un usuario puede ver un evento específico
     */
    public function canUserViewEvent($userId, $eventId)
    {
        // Primero obtener el autor del evento
        $eventModel = new EventsModel();
        $event = $eventModel->find($eventId);

        if (!$event) {
            return false;
        }

        // Verificar si tiene permisos sobre el autor
        $authorPermission = $this->authorPermissionModel->hasAuthorPermission($userId, $event['author_id']);

        if (!$authorPermission) {
            return false;
        }

        // Si tiene permiso 'full' sobre el autor, puede ver todos los eventos
        if ($authorPermission['permission_level'] === 'full') {
            return true;
        }

        // Si tiene permiso 'restricted', verificar si tiene acceso al evento específico
        return $this->eventRestrictionModel->hasEventAccess($userId, $eventId) !== null;
    }
}

